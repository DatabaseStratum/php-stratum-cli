<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql\Command;

use SetBased\Exception\RuntimeException;
use SetBased\Stratum\MySql\Helper\DataTypeHelper;
use SetBased\Stratum\MySql\MetadataDataLayer as DataLayer;
use SetBased\Stratum\Style\StratumStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Command for creating PHP constants based on column widths, auto increment columns and labels.
 */
class ConstantsCommand extends MySqlCommand
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Name of the class that contains all constants.
   *
   * @var string
   */
  private $className;

  /**
   * All columns in the MySQL schema.
   *
   * @var array
   */
  private $columns = [];

  /**
   * @var array All constants.
   */
  private $constants = [];

  /**
   * Filename with column names, their widths, and constant names.
   *
   * @var string
   */
  private $constantsFilename;

  /**
   * All primary key labels, their widths and constant names.
   *
   * @var array
   */
  private $labels = [];

  /**
   * The previous column names, widths, and constant names (i.e. the content of $constantsFilename upon starting
   * this program).
   *
   * @var array
   */
  private $oldColumns = [];

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this->setName('constants')
         ->setDescription('Generates constants based on database IDs')
         ->addArgument('config file', InputArgument::REQUIRED, 'The stratum configuration file');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->io = new StratumStyle($input, $output);

    $configFileName = $input->getArgument('config file');
    $settings       = $this->readConfigFile($configFileName);

    if ($this->constantsFilename!==null || $this->className!==null)
    {
      $this->io->title('Constants');

      $this->connect($settings);

      $this->executeEnabled();

      $this->disconnect();
    }
    else
    {
      $this->io->logVerbose('Constants not enabled');
    }

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Enhances $oldColumns as follows:
   * If the constant name is *, is is replaced with the column name prefixed by $this->myPrefix in uppercase.
   * Otherwise the constant name is set to uppercase.
   */
  private function enhanceColumns()
  {
    foreach ($this->oldColumns as $table)
    {
      foreach ($table as $column)
      {
        $table_name  = $column['table_name'];
        $column_name = $column['column_name'];

        if ($column['constant_name']=='*')
        {
          $constant_name                                                = strtoupper($column['column_name']);
          $this->oldColumns[$table_name][$column_name]['constant_name'] = $constant_name;
        }
        else
        {
          $constant_name                                                = strtoupper($this->oldColumns[$table_name][$column_name]['constant_name']);
          $this->oldColumns[$table_name][$column_name]['constant_name'] = $constant_name;
        }
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Gathers constants based on column widths.
   */
  private function executeColumnWidths()
  {
    $this->getOldColumns();

    $this->getColumns();

    $this->enhanceColumns();

    $this->mergeColumns();

    $this->writeColumns();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates constants declarations in a class.
   */
  private function executeCreateConstants()
  {
    $this->getLabels();

    $this->fillConstants();

    $this->writeConstantClass();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes the enabled functionalities.
   */
  private function executeEnabled()
  {
    if ($this->constantsFilename!==null)
    {
      $this->executeColumnWidths();
    }

    if ($this->className!==null)
    {
      $this->executeCreateConstants();
    }

    $this->logNumberOfConstants();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Searches for 3 lines in the source code of the class for constants. The lines are:
   * * The first line of the doc block with the annotation '@setbased.stratum.constants'.
   * * The last line of this doc block.
   * * The last line of continuous constant declarations directly after the doc block.
   * If one of these line can not be found the line number will be set to null.
   *
   * @param string $source The source code of the constant class.
   *
   * @return array With the 3 line number as described
   */
  private function extractLines($source)
  {
    $tokens = token_get_all($source);

    $line1 = null;
    $line2 = null;
    $line3 = null;

    // Find annotation @constants
    $step = 1;
    foreach ($tokens as $token)
    {
      switch ($step)
      {
        case 1:
          // Step 1: Find doc comment with annotation.
          if (is_array($token) && $token[0]==T_DOC_COMMENT)
          {
            if (strpos($token[1], '@setbased.stratum.constants')!==false)
            {
              $line1 = $token[2];
              $step  = 2;
            }
          }
          break;

        case 2:
          // Step 2: Find end of doc block.
          if (is_array($token))
          {
            if ($token[0]==T_WHITESPACE)
            {
              $line2 = $token[2];
              if (substr_count($token[1], "\n")>1)
              {
                // Whitespace contains new line: end doc block without constants.
                $step = 4;
              }
            }
            else
            {
              if ($token[0]==T_CONST)
              {
                $line3 = $token[2];
                $step  = 3;
              }
              else
              {
                $step = 4;
              }
            }
          }
          break;

        case 3:
          // Step 4: Find en of constants declarations.
          if (is_array($token))
          {
            if ($token[0]==T_WHITESPACE)
            {
              if (substr_count($token[1], "\n")<=1)
              {
                // Ignore whitespace.
                $line3 = $token[2];
              }
              else
              {
                // Whitespace contains new line: end of const declarations.
                $step = 4;
              }
            }
            elseif ($token[0]==T_CONST || $token[2]==$line3)
            {
              $line3 = $token[2];
            }
            else
            {
              $step = 4;
            }
          }
          break;

        case 4:
          // Leave loop.
          break;
      }
    }

    // @todo get indent based on indent of the doc block.

    return [$line1, $line2, $line3];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Merges $columns and $labels (i.e. all known constants) into $constants.
   */
  private function fillConstants()
  {
    foreach ($this->columns as $table_name => $table)
    {
      foreach ($table as $column_name => $column)
      {
        if (isset($this->columns[$table_name][$column_name]['constant_name']))
        {
          $this->constants[$column['constant_name']] = $column['length'];
        }
      }
    }

    foreach ($this->labels as $label => $id)
    {
      $this->constants[$label] = $id;
    }

    ksort($this->constants);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads the width of all columns in the MySQL schema into $columns.
   */
  private function getColumns()
  {
    $rows = DataLayer::getAllTableColumns();
    foreach ($rows as $row)
    {
      $row['length']                                          = DataTypeHelper::deriveFieldLength($row);
      $this->columns[$row['table_name']][$row['column_name']] = $row;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Gets all primary key labels from the MySQL database.
   */
  private function getLabels()
  {
    $tables = DataLayer::getLabelTables();
    foreach ($tables as $table)
    {
      $rows = DataLayer::getLabelsFromTable($table['table_name'], $table['id'], $table['label']);
      foreach ($rows as $row)
      {
        $this->labels[$row['label']] = $row['id'];
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads from file $constantsFilename the previous table and column names, the width of the column,
   * and the constant name (if assigned) and stores this data in $oldColumns.
   */
  private function getOldColumns()
  {
    if (file_exists($this->constantsFilename))
    {
      $handle = fopen($this->constantsFilename, 'r');

      $line_number = 0;
      while ($line = fgets($handle))
      {
        $line_number++;
        if ($line!="\n")
        {
          $n = preg_match('/^\s*(([a-zA-Z0-9_]+)\.)?([a-zA-Z0-9_]+)\.([a-zA-Z0-9_]+)\s+(\d+)\s*(\*|[a-zA-Z0-9_]+)?\s*$/', $line, $matches);
          if ($n==0)
          {
            throw new RuntimeException("Illegal format at line %d in file '%s'.",
                                       $line_number,
                                       $this->constantsFilename);
          }

          if (isset($matches[6]))
          {
            $schema_name   = $matches[2];
            $table_name    = $matches[3];
            $column_name   = $matches[4];
            $length        = $matches[5];
            $constant_name = $matches[6];

            if ($schema_name)
            {
              $table_name = $schema_name.'.'.$table_name;
            }

            $this->oldColumns[$table_name][$column_name] = ['table_name'    => $table_name,
                                                            'column_name'   => $column_name,
                                                            'length'        => $length,
                                                            'constant_name' => $constant_name];
          }
        }
      }
      if (!feof($handle))
      {
        throw new RuntimeException("Error reading from file '%s'.", $this->constantsFilename);
      }

      $ok = fclose($handle);
      if ($ok===false)
      {
        throw new RuntimeException("Error closing file '%s'.", $this->constantsFilename);
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Logs the number of constants generated.
   */
  private function logNumberOfConstants()
  {
    $n_id  = sizeof($this->labels);
    $n_len = sizeof($this->constants) - $n_id;

    $this->io->writeln('');
    $this->io->text(sprintf('Number of constants based on column widths: %d', $n_len));
    $this->io->text(sprintf('Number of constants based on database IDs : %d', $n_id));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates PHP code with constant declarations.
   *
   * @return array The generated PHP code, lines are stored as rows in the array.
   */
  private function makeConstantStatements()
  {
    $width1    = 0;
    $width2    = 0;
    $constants = [];

    foreach ($this->constants as $constant => $value)
    {
      $width1 = max(strlen($constant), $width1);
      $width2 = max(strlen($value), $width2);
    }

    $line_format = sprintf('  const %%-%ds = %%%dd;', $width1, $width2);
    foreach ($this->constants as $constant => $value)
    {
      $constants[] = sprintf($line_format, $constant, $value);
    }

    return $constants;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Preserves relevant data in $oldColumns into $columns.
   */
  private function mergeColumns()
  {
    foreach ($this->oldColumns as $table_name => $table)
    {
      foreach ($table as $column_name => $column)
      {
        if (isset($this->columns[$table_name][$column_name]))
        {
          $this->columns[$table_name][$column_name]['constant_name'] = $column['constant_name'];
        }
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads configuration parameters from the configuration file.
   *
   * @param string $configFilename
   *
   * @return array
   */
  private function readConfigFile($configFilename)
  {
    $settings = parse_ini_file($configFilename, true);

    $this->constantsFilename = self::getSetting($settings, false, 'constants', 'columns');
    $this->className         = self::getSetting($settings, false, 'constants', 'class');

    return $settings;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Writes table and column names, the width of the column, and the constant name (if assigned) to
   * $constantsFilename.
   */
  private function writeColumns()
  {
    $content = '';
    foreach ($this->columns as $table)
    {
      $width1 = 0;
      $width2 = 0;
      foreach ($table as $column)
      {
        $width1 = max(strlen($column['column_name']), $width1);
        $width2 = max(strlen($column['length']), $width2);
      }

      foreach ($table as $column)
      {
        if (isset($column['length']))
        {
          if (isset($column['constant_name']))
          {
            $line_format = sprintf("%%s.%%-%ds %%%dd %%s\n", $width1, $width2);
            $content .= sprintf($line_format,
                                $column['table_name'],
                                $column['column_name'],
                                $column['length'],
                                $column['constant_name']);
          }
          else
          {
            $line_format = sprintf("%%s.%%-%ds %%%dd\n", $width1, $width2);
            $content .= sprintf($line_format,
                                $column['table_name'],
                                $column['column_name'],
                                $column['length']);
          }
        }
      }

      $content .= "\n";
    }

    // Save the columns, width and constants to the filesystem.
    $this->writeTwoPhases($this->constantsFilename, $content);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Inserts new and replace old (if any) constant declaration statements in a PHP source file.
   */
  private function writeConstantClass()
  {
    // Get the class loader.
    /** @var \Composer\Autoload\ClassLoader $loader */
    $loader = spl_autoload_functions()[0][0];

    // Find the source file of the constant class.
    $file_name = $loader->findFile($this->className);
    if ($file_name===false)
    {
      throw new RuntimeException("ClassLoader can not find class '%s'.", $this->className);
    }

    // Read the source of the class without actually loading the class. Otherwise, we can not (re)load the class in
    // \SetBased\Stratum\MySql\RoutineLoaderCommand::getConstants.
    $source = file_get_contents($file_name);
    if ($source===false)
    {
      throw new RuntimeException("Unable the open source file '%s'.", $file_name);
    }
    $source_lines = explode("\n", $source);

    // Search for the lines where to insert and replace constant declaration statements.
    $line_numbers = $this->extractLines($source);
    if (!isset($line_numbers[0]))
    {
      throw new RuntimeException("Annotation not found in '%s'.", $file_name);
    }

    // Generate the constant declaration statements.
    $constants = $this->makeConstantStatements();

    // Insert new and replace old (if any) constant declaration statements.
    $tmp1         = array_splice($source_lines, 0, $line_numbers[1]);
    $tmp2         = array_splice($source_lines, (isset($line_numbers[2])) ? $line_numbers[2] - $line_numbers[1] : 0);
    $source_lines = array_merge($tmp1, $constants, $tmp2);

    // Save the configuration file.
    $this->writeTwoPhases($file_name, implode("\n", $source_lines));
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
