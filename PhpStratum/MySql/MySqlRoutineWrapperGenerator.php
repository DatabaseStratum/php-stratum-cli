<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * phpStratum
 *
 * @copyright 2005-2015 Paul Water / Set Based IT Consultancy (https://www.setbased.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link
 */
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\PhpStratum\MySql;

use SetBased\Affirm\Affirm;
use SetBased\PhpStratum\MySql\Wrapper\MySqlWrapper;
use SetBased\PhpStratum\MySql\Wrapper\StaticDataLayer as DataLayer;
use SetBased\PhpStratum\Util;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating a class with wrapper methods for calling stored routines in a MySQL database.
 */
class MySqlRoutineWrapperGenerator
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The generated PHP code.
   *
   * @var string
   */
  private $myCode = '';

  /**
   * The filename of the configuration file.
   *
   * @var string
   */
  private $myConfigurationFilename;

  /**
   * The schema name.
   *
   * @var string
   */
  private $myDatabase;

  /**
   * Host name or address.
   *
   * @var string
   */
  private $myHostName;

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
   * The class name (including namespace) of the parent class of the routine wrapper.
   *
   * @var string
   */
  private $myParentClassName;

  /**
   * The password.
   *
   * @var string
   */
  private $myPassword;

  /**
   * The user name.
   *
   * @var string
   */
  private $myUserName;

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
   * The "main" of the wrapper generator.
   *
   * @param $theConfigurationFilename string The name of the configuration file.
   *
   * @return int Returns 0 on success, 1 if one or more errors occured.
   */
  public function run( $theConfigurationFilename )
  {
    $this->myConfigurationFilename = $theConfigurationFilename;

    $this->readConfigurationFile();

    DataLayer::connect( $this->myHostName, $this->myUserName, $this->myPassword, $this->myDatabase );

    $routines = $this->readRoutineMetadata();

    // Write the header of the wrapper class.
    $this->writeClassHeader();

    if (is_array( $routines ))
    {
      // Write methods for each stored routine.
      foreach ($routines as $routine)
      {
        // If routine type is hidden don't create routine wrapper.
        if ($routine['designation']!='hidden')
        {
          $this->writeRoutineFunction( $routine );
        }
      }
    }
    else
    {
      echo "No files with stored routines found.\n";
    }

    // Write the trailer of the wrapper class.
    $this->writeClassTrailer();

    // Write the wrapper class tot the filesystem.
    Util::writeTwoPhases( $this->myWrapperFilename, $this->myCode );

    DataLayer::disconnect();

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads parameters from the configuration file.
   */
  private function readConfigurationFile()
  {
    // Read the configuration file.
    $settings = parse_ini_file( $this->myConfigurationFilename, true );

    // Set default values.
    if (!isset($settings['wrapper']['lob_as_string']))
    {
      $settings['wrapper']['lob_as_string'] = false;
    }

    $this->myHostName = Util::getSetting( $settings, true, 'database', 'host_name' );
    $this->myUserName = Util::getSetting( $settings, true, 'database', 'user_name' );
    $this->myPassword = Util::getSetting( $settings, true, 'database', 'password' );
    $this->myDatabase = Util::getSetting( $settings, true, 'database', 'database_name' );

    $this->myParentClassName  = Util::getSetting( $settings, true, 'wrapper', 'parent_class' );
    $this->myWrapperClassName = Util::getSetting( $settings, true, 'wrapper', 'wrapper_class' );
    $this->myWrapperFilename  = Util::getSetting( $settings, true, 'wrapper', 'wrapper_file' );
    $this->myMetadataFilename = Util::getSetting( $settings, true, 'wrapper', 'metadata' );
    $this->myLobAsStringFlag  = (Util::getSetting( $settings, true, 'wrapper', 'lob_as_string' )) ? true : false;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the metadata of stored routines.
   *
   * @return array
   */
  private function readRoutineMetadata()
  {
    $data = file_get_contents( $this->myMetadataFilename );

    $routines = json_decode( $data, true );
    if (json_last_error()!=JSON_ERROR_NONE) Affirm::assertFailed( "Error decoding JSON: '%s'.", json_last_error_msg() );

    return $routines;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate a class header for stored routine wrapper.
   */
  private function writeClassHeader()
  {
    $p = strrpos( $this->myWrapperClassName, '\\' );
    if ($p!==false)
    {
      $namespace  = ltrim( substr( $this->myWrapperClassName, 0, $p ), '\\' );
      $class_name = substr( $this->myWrapperClassName, $p + 1 );
    }
    else
    {
      $namespace  = null;
      $class_name = $this->myWrapperClassName;
    }

    $this->myCode .= "<?php\n";
    $this->myCode .= '//'.str_repeat( '-', MySqlWrapper::C_PAGE_WIDTH - 2 )."\n";
    if ($namespace)
    {
      $this->myCode .= "namespace ${namespace};\n";
      $this->myCode .= "\n";
      $this->myCode .= '//'.str_repeat( '-', MySqlWrapper::C_PAGE_WIDTH - 2 )."\n";
    }
    $this->myCode .= 'class '.$class_name.' extends '.$this->myParentClassName."\n";
    $this->myCode .= "{\n";
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate a class trailer for stored routine wrapper.
   */
  private function writeClassTrailer()
  {
    $this->myCode .= '  //'.str_repeat( '-', MySqlWrapper::C_PAGE_WIDTH - 4 )."\n";
    $this->myCode .= "}\n";
    $this->myCode .= "\n";
    $this->myCode .= '//'.str_repeat( '-', MySqlWrapper::C_PAGE_WIDTH - 2 )."\n";
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates a complete wrapper method for a stored routine.
   *
   * @param array $theRoutine The metadata of the stored routine.
   */
  private function writeRoutineFunction( $theRoutine )
  {
    $wrapper = MySqlWrapper::createRoutineWrapper( $theRoutine, $this->myLobAsStringFlag );
    $this->myCode .= $wrapper->writeRoutineFunction( $theRoutine );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
