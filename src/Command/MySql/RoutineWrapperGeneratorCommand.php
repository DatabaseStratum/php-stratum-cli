<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * PhpStratum
 *
 * @copyright 2005-2015 Paul Water / Set Based IT Consultancy (https://www.setbased.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link
 */
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Command\MySql;

use SetBased\Exception\RuntimeException;
use SetBased\Stratum\Command\BaseCommand;
use SetBased\Stratum\MySql\Wrapper\Wrapper;
use SetBased\Stratum\NameMangler\NameMangler;
use SetBased\Stratum\Style\StratumStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Command for generating a class with wrapper methods for calling stored routines in a MySQL database.
 */
class RoutineWrapperGeneratorCommand extends BaseCommand
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The generated PHP code.
   *
   * @var string
   */
  private $phpCode = '';

  /**
   * Array with fully qualified names that must be imported.
   *
   * @var array
   */
  private $imports = [];

  /**
   * If true BLOBs and CLOBs must be treated as strings.
   *
   * @var bool
   */
  private $myLobAsStringFlag;

  /**
   * The filename of the file with the metadata of all stored procedures.
   *
   * @var string
   */
  private $myMetadataFilename;

  /**
   * Class name for mangling routine and parameter names.
   *
   * @var string
   */
  private $myNameMangler;

  /**
   * The class name (including namespace) of the parent class of the routine wrapper.
   *
   * @var string
   */
  private $myParentClassName;

  /**
   * The class name (including namespace) of the routine wrapper.
   *
   * @var string
   */
  private $myWrapperClassName;

  /**
   * The filename where the generated wrapper class must be stored
   *
   * @var string
   */
  private $myWrapperFilename;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this->setName('wrapper')
         ->setDescription('Generates constants based on database IDs');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->io = new StratumStyle($input, $output);

    $configFileName = $input->getArgument('config file');

    $this->readConfigurationFile($configFileName);

    if ($this->myWrapperClassName!==null)
    {
      $this->generateWrapperClass();
    }

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the wrapper class.
   */
  private function generateWrapperClass()
  {
    $this->io->title('Wrapper');

    /** @var NameMangler $mangler */
    $mangler  = new $this->myNameMangler();
    $routines = $this->readRoutineMetadata();

    if (!empty($routines))
    {
      // Sort routines by their wrapper method name.
      $sorted_routines = [];
      foreach ($routines as $routine)
      {
        $method_name                   = $mangler->getMethodName($routine['routine_name']);
        $sorted_routines[$method_name] = $routine;
      }
      ksort($sorted_routines);

      // Write methods for each stored routine.
      foreach ($sorted_routines as $method_name => $routine)
      {
        // If routine type is hidden don't create routine wrapper.
        if ($routine['designation']!='hidden')
        {
          $this->writeRoutineFunction($routine, $mangler);
        }
      }
    }
    else
    {
      echo "No files with stored routines found.\n";
    }

    $methods       = $this->phpCode;
    $this->phpCode = '';

    // Write the header of the wrapper class.
    $this->writeClassHeader();

    // Write methods of the wrapper calls.
    $this->phpCode .= $methods;

    // Write the trailer of the wrapper class.
    $this->writeClassTrailer();

    // Write the wrapper class to the filesystem.
    $this->writeTwoPhases($this->myWrapperFilename, $this->phpCode);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads parameters from the configuration file.
   *
   * @param string $configFilename The filename of the configuration file.
   */
  private function readConfigurationFile($configFilename)
  {
    // Read the configuration file.
    $settings = parse_ini_file($configFilename, true);

    // Set default values.
    if (!isset($settings['wrapper']['lob_as_string']))
    {
      $settings['wrapper']['lob_as_string'] = false;
    }

    $this->myWrapperClassName = self::getSetting($settings, false, 'wrapper', 'wrapper_class');
    if ($this->myWrapperClassName!==null)
    {
      $this->myParentClassName  = self::getSetting($settings, true, 'wrapper', 'parent_class');
      $this->myNameMangler      = self::getSetting($settings, true, 'wrapper', 'mangler_class');
      $this->myWrapperFilename  = self::getSetting($settings, true, 'wrapper', 'wrapper_file');
      $this->myLobAsStringFlag  = (self::getSetting($settings, true, 'wrapper', 'lob_as_string')) ? true : false;
      $this->myMetadataFilename = self::getSetting($settings, true, 'loader', 'metadata');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the metadata of stored routines.
   *
   * @return array
   */
  private function readRoutineMetadata()
  {
    $data = file_get_contents($this->myMetadataFilename);

    $routines = (array)json_decode($data, true);
    if (json_last_error()!=JSON_ERROR_NONE)
    {
      throw new RuntimeException("Error decoding JSON: '%s'.", json_last_error_msg());
    }

    return $routines;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate a class header for stored routine wrapper.
   */
  private function writeClassHeader()
  {
    $p = strrpos($this->myWrapperClassName, '\\');
    if ($p!==false)
    {
      $namespace  = ltrim(substr($this->myWrapperClassName, 0, $p), '\\');
      $class_name = substr($this->myWrapperClassName, $p + 1);
    }
    else
    {
      $namespace  = null;
      $class_name = $this->myWrapperClassName;
    }

    // Write PHP tag.
    $this->phpCode .= "<?php\n";
    if ($namespace!==null)
    {
      $this->phpCode .= '//'.str_repeat('-', Wrapper::C_PAGE_WIDTH - 2)."\n";
      $this->phpCode .= "namespace ${namespace};\n";
      $this->phpCode .= "\n";
    }

    // If the child class and parent class have different names import the parent class. Otherwise use the fully
    // qualified parent class name.
    $parent_class_name = substr($this->myParentClassName, strrpos($this->myParentClassName, '\\') + 1);
    if ($class_name!=$parent_class_name)
    {
      $this->imports[]         = $this->myParentClassName;
      $this->myParentClassName = $parent_class_name;
    }

    // Write use statements.
    if (!empty($this->imports))
    {
      $this->imports = array_unique($this->imports, SORT_REGULAR);
      $this->phpCode .= '//'.str_repeat('-', Wrapper::C_PAGE_WIDTH - 2)."\n";
      foreach ($this->imports as $import)
      {
        $this->phpCode .= 'use '.$import.";\n";
      }
      $this->phpCode .= "\n";
    }

    // Write class name.
    $this->phpCode .= '//'.str_repeat('-', Wrapper::C_PAGE_WIDTH - 2)."\n";
    $this->phpCode .= 'class '.$class_name.' extends '.$this->myParentClassName."\n";
    $this->phpCode .= "{\n";
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate a class trailer for stored routine wrapper.
   */
  private function writeClassTrailer()
  {
    $this->phpCode .= '  //'.str_repeat('-', Wrapper::C_PAGE_WIDTH - 4)."\n";
    $this->phpCode .= "}\n";
    $this->phpCode .= "\n";
    $this->phpCode .= '//'.str_repeat('-', Wrapper::C_PAGE_WIDTH - 2)."\n";
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates a complete wrapper method for a stored routine.
   *
   * @param array       $routine     The metadata of the stored routine.
   * @param NameMangler $nameMangler The mangler for wrapper and parameter names.
   */
  private function writeRoutineFunction($routine, $nameMangler)
  {
    $wrapper = Wrapper::createRoutineWrapper($routine, $this->myLobAsStringFlag);
    $this->phpCode .= $wrapper->writeRoutineFunction($routine, $nameMangler);

    $this->imports = array_merge($this->imports, $wrapper->getImports());
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
