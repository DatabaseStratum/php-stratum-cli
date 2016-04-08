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
namespace SetBased\Stratum\MySql;

use SetBased\Affirm\Exception\FallenException;
use SetBased\Affirm\Exception\RuntimeException;
use SetBased\Stratum\MySql\StaticDataLayer as DataLayer;
use SetBased\Stratum\Util;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for creating PHP constants based on column widths, and auto increment columns and labels.
 */
class Constants
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Name of the class that contains all constants.
   *
   * @var string
   */
  private $myClassName;

  /**
   * All columns in the MySQL schema.
   *
   * @var array
   */
  private $myColumns = [];

  /**
   * Object for connection to a database instance.
   *
   * @var Connector.
   */
  private $myConnector;

  /**
   * @var array All constants.
   */
  private $myConstants = [];

  /**
   * Filename with column names, their widths, and constant names.
   *
   * @var string
   */
  private $myConstantsFilename;

  /**
   * All primary key labels, their widths and constant names.
   *
   * @var array
   */
  private $myLabels = [];

  /**
   * The previous column names, widths, and constant names (i.e. the content of $myConstantsFilename upon starting
   * this program).
   *
   * @var array
   */
  private $myOldColumns = [];

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param string $theConfigFilename
   *
   * @return int
   */
  public function main($theConfigFilename)
  {
    $this->myConnector = new Connector();

    $this->readConfigFile($theConfigFilename);

    $this->myConnector->connect();

    $this->getOldColumns();

    $this->getColumns();

    $this->enhanceColumns();

    $this->mergeColumns();

    $this->writeColumns();

    $this->getLabels();

    $this->fillConstants();

    $this->writeConstantClass();

    $this->myConnector->disconnect();

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads configuration parameters from the configuration file.
   *
   * @param string $theConfigFilename
   */
  protected function readConfigFile($theConfigFilename)
  {
    $this->myConnector->readConfigFile($theConfigFilename);

    $settings = parse_ini_file($theConfigFilename, true);

    $this->myConstantsFilename = Util::getSetting($settings, true, 'constants', 'columns');
    $this->myClassName         = Util::getSetting($settings, true, 'constants', 'class');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the widths of a field based on a column.
   *
   * @param array $theColumn The column of which the field is based.
   *
   * @return int|null
   */
  private function deriveFieldLength($theColumn)
  {
    $ret = null;
    switch ($theColumn['data_type'])
    {
      case 'tinyint':
      case 'smallint':
      case 'mediumint':
      case 'int':
      case 'bigint':

      case 'decimal':
      case 'float':
      case 'double':
        $ret = $theColumn['numeric_precision'];
        break;

      case 'char':
      case 'varchar':
      case 'binary':
      case 'varbinary':

      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':
      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob':
      case 'bit':
        $ret = $theColumn['character_maximum_length'];
        break;

      case 'timestamp':
        $ret = 16;
        break;

      case 'year':
        $ret = 4;
        break;

      case 'time':
        $ret = 8;
        break;

      case 'date':
        $ret = 10;
        break;

      case 'datetime':
        $ret = 16;
        break;

      case 'enum':
      case 'set':
        // Nothing to do. We don't assign a width to column with type enum and set.
        break;

      default:
        throw new FallenException('column type', $theColumn['data_type']);
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Enhances $myOldColumns as follows:
   * If the constant name is *, is is replaced with the column name prefixed by $this->myPrefix in uppercase.
   * Otherwise the constant name is set to uppercase.
   */
  private function enhanceColumns()
  {
    foreach ($this->myOldColumns as $table)
    {
      foreach ($table as $column)
      {
        $table_name  = $column['table_name'];
        $column_name = $column['column_name'];

        if ($column['constant_name']=='*')
        {
          $constant_name                                                  = strtoupper($column['column_name']);
          $this->myOldColumns[$table_name][$column_name]['constant_name'] = $constant_name;
        }
        else
        {
          $constant_name                                                  = strtoupper($this->myOldColumns[$table_name][$column_name]['constant_name']);
          $this->myOldColumns[$table_name][$column_name]['constant_name'] = $constant_name;
        }
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Searches for 3 lines in the source code of the class for constants. The lines are:
   * * The first line of the doc block with the annotation '@setbased.stratum.constants'.
   * * The last line of this doc block.
   * * The last line of continuous constant declarations directly after the doc block.
   * If one of these line can not be found the line number will be set to null.
   *
   * @param string $theSourceCode The source code of the constant class.
   *
   * @return array With the 3 line number as described
   */
  private function extractLines($theSourceCode)
  {
    $tokens = token_get_all($theSourceCode);

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
   * Merges $myColumns and $myLabels (i.e. all known constants) into $myConstants.
   */
  private function fillConstants()
  {
    foreach ($this->myColumns as $table_name => $table)
    {
      foreach ($table as $column_name => $column)
      {
        if (isset($this->myColumns[$table_name][$column_name]['constant_name']))
        {
          $this->myConstants[$column['constant_name']] = $column['length'];
        }
      }
    }

    foreach ($this->myLabels as $label => $id)
    {
      $this->myConstants[$label] = $id;
    }

    ksort($this->myConstants);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads the width of all columns in the MySQL schema into $myColumns.
   */
  private function getColumns()
  {
    $query = "
(
  select table_name
  ,      column_name
  ,      data_type
  ,      character_maximum_length
  ,      numeric_precision
  from   information_schema.COLUMNS
  where  table_schema = database()
  and    table_name  rlike '^[a-zA-Z0-9_]*$'
  and    column_name rlike '^[a-zA-Z0-9_]*$'
  order by table_name
  ,        ordinal_position
)

union all

(
  select concat(table_schema,'.',table_name) table_name
  ,      column_name
  ,      data_type
  ,      character_maximum_length
  ,      numeric_precision
  from   information_schema.COLUMNS
  where  table_name  rlike '^[a-zA-Z0-9_]*$'
  and    column_name rlike '^[a-zA-Z0-9_]*$'
  order by table_schema
  ,        table_name
  ,        ordinal_position
)
";

    $rows = DataLayer::executeRows($query);
    foreach ($rows as $row)
    {
      $row['length']                                            = $this->deriveFieldLength($row);
      $this->myColumns[$row['table_name']][$row['column_name']] = $row;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Gets all primary key labels from the MySQL database.
   */
  private function getLabels()
  {
    $query_string = "
select t1.table_name  `table_name`
,      t1.column_name `id`
,      t2.column_name `label`
from       information_schema.columns t1
inner join information_schema.columns t2 on t1.table_name = t2.table_name
where t1.table_schema = database()
and   t1.extra        = 'auto_increment'
and   t2.table_schema = database()
and   t2.column_name like '%%\\_label'";

    $tables = DataLayer::executeRows($query_string);
    foreach ($tables as $table)
    {
      $query_string = "
select `%s`  as `id`
,      `%s`  as `label`
from   `%s`
where   nullif(`%s`,'') is not null";

      $query_string = sprintf($query_string, $table['id'], $table['label'], $table['table_name'], $table['label']);
      $rows         = DataLayer::executeRows($query_string);
      foreach ($rows as $row)
      {
        $this->myLabels[$row['label']] = $row['id'];
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads from file $myConstantsFilename the previous table and column names, the width of the column,
   * and the constant name (if assigned) and stores this data in $myOldColumns.
   */
  private function getOldColumns()
  {
    if (file_exists($this->myConstantsFilename))
    {
      $handle = fopen($this->myConstantsFilename, 'r');

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
                                       $this->myConstantsFilename);
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

            $this->myOldColumns[$table_name][$column_name] = ['table_name'    => $table_name,
                                                              'column_name'   => $column_name,
                                                              'length'        => $length,
                                                              'constant_name' => $constant_name];
          }
        }
      }
      if (!feof($handle))
      {
        throw new RuntimeException("Error reading from file '%s'.", $this->myConstantsFilename);
      }

      $ok = fclose($handle);
      if ($ok===false)
      {
        throw new RuntimeException("Error closing file '%s'.", $this->myConstantsFilename);
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  private function makeConstantStatements()
  {
    $width1    = 0;
    $width2    = 0;
    $constants = [];

    foreach ($this->myConstants as $constant => $value)
    {
      $width1 = max(strlen($constant), $width1);
      $width2 = max(strlen($value), $width2);
    }

    $line_format = sprintf('  const %%-%ds = %%%dd;', $width1, $width2);
    foreach ($this->myConstants as $constant => $value)
    {
      $constants[] = sprintf($line_format, $constant, $value);
    }

    return $constants;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Preserves relevant data in $myOldColumns into $myColumns.
   */
  private function mergeColumns()
  {
    foreach ($this->myOldColumns as $table_name => $table)
    {
      foreach ($table as $column_name => $column)
      {
        if (isset($this->myColumns[$table_name][$column_name]))
        {
          $this->myColumns[$table_name][$column_name]['constant_name'] = $column['constant_name'];
        }
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Writes table and column names, the width of the column, and the constant name (if assigned) to
   * $myConstantsFilename.
   */
  private function writeColumns()
  {
    $content = '';
    foreach ($this->myColumns as $table)
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
    Util::writeTwoPhases($this->myConstantsFilename, $content);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Insert new and replace old (if any) constant declaration statements in a PHP source file.
   */
  private function writeConstantClass()
  {
    // Get the class loader.
    /** @var \Composer\Autoload\ClassLoader $loader */
    $loader = spl_autoload_functions()[0][0];

    // Find the source file of the constant class.
    $file_name = $loader->findFile($this->myClassName);
    if ($file_name===false)
    {
      throw new RuntimeException("Composer can not find class '%s'.", $this->myClassName);
    }

    // Read the source of the class without actually loading the class. Otherwise, we can not (re)load the class in
    // \SetBased\Stratum\MySql\RoutineLoader::getConstants.
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
    Util::writeTwoPhases($file_name, implode("\n", $source_lines));
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
