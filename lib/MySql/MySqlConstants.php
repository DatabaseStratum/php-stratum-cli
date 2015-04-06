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
namespace SetBased\Stratum\MySql;

use SetBased\Affirm\Affirm;
use SetBased\Stratum\MySql\Wrapper\StaticDataLayer as DataLayer;
use SetBased\Stratum\Util;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for creating PHP constants based on column widths, and auto increment columns and labels.
 */
class MySqlConstants
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The placeholder in the template file to be replaced with the generated constants.
   */
  const C_PLACEHOLDER = '/* AUTO_GENERATED_CONSTS */';

  /**
   * All columns in the MySQL schema.
   *
   * @var array
   */
  private $myColumns = array();

  /**
   * Name of file that contains all constants.
   *
   * @var string
   */
  private $myConfigFilename;

  /**
   * @var array All constants.
   */
  private $myConstants = array();

  /**
   * Filename with column names, their widths, and constant names.
   *
   * @var string
   */
  private $myConstantsFilename;

  /**
   * Name used database.
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
   * All primary key labels, their widths and constant names.
   *
   * @var array
   */
  private $myLabels = array();

  /**
   * The previous column names, widths, and constant names (i.e. the content of $myConstantsFilename upon starting
   * this program).
   *
   * @var array
   */
  private $myOldColumns = array();

  /**
   * User password.
   *
   * @var string
   */
  private $myPassword;

  /**
   * The prefix used for designations a unknown constants.
   *
   * @var string
   */
  private $myPrefix;

  /**
   * Template filename under which the file is generated with the constants.
   *
   * @var string
   */
  private $myTemplateConfigFilename;

  /**
   * @var string User name.
   */
  private $myUserName;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param string $theConfigFilename
   *
   * @return int
   */
  public function main( $theConfigFilename )
  {
    $this->readConfigFile( $theConfigFilename );

    DataLayer::connect( $this->myHostName, $this->myUserName, $this->myPassword, $this->myDatabase );

    $this->getOldColumns();

    $this->getColumns();

    $this->enhanceColumns();

    $this->mergeColumns();

    $this->writeColumns();

    $this->getLabels();

    $this->fillConstants();

    $this->writeTargetConfigFile();

    DataLayer::disconnect();

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the widths of a field based on column.
   *
   * @param array $theColumn The column of which the field is based.
   *
   * @returns int|null The width of the column.
   */
  private function deriveFieldLength( $theColumn )
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
        Affirm::assertFailed( "Unknown type '%s'.", $theColumn['data_type'] );
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
          $constant_name                                                  = strtoupper( $this->myPrefix.$column['column_name'] );
          $this->myOldColumns[$table_name][$column_name]['constant_name'] = $constant_name;
        }
        else
        {
          $constant_name                                                  = strtoupper( $this->myOldColumns[$table_name][$column_name]['constant_name'] );
          $this->myOldColumns[$table_name][$column_name]['constant_name'] = $constant_name;
        }
      }
    }
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

    $ok = ksort( $this->myConstants );
    if ($ok===false) Affirm::assertFailed( "Internal error." );
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

    $rows = DataLayer::executeRows( $query );
    foreach ($rows as $row)
    {
      $row['length']                                            = $this->deriveFieldLength( $row );
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

    $tables = DataLayer::executeRows( $query_string );
    foreach ($tables as $table)
    {
      $query_string = "
select `%s`  as `id`
,      `%s`  as `label`
from   `%s`
where   nullif(`%s`,'') is not null";

      $query_string = sprintf( $query_string, $table['id'], $table['label'], $table['table_name'], $table['label'] );
      $rows         = DataLayer::executeRows( $query_string );
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
    if (file_exists( $this->myConstantsFilename ))
    {
      $handle = fopen( $this->myConstantsFilename, 'r' );

      $line_number = 0;
      while ($line = fgets( $handle ))
      {
        $line_number++;
        if ($line!="\n")
        {
          $n = preg_match( '/^\s*(([a-zA-Z0-9_]+)\.)?([a-zA-Z0-9_]+)\.([a-zA-Z0-9_]+)\s+(\d+)\s*(\*|[a-zA-Z0-9_]+)?\s*$/', $line, $matches );
          if ($n==0)
          {
            Affirm::assertFailed( "Illegal format at line %d in file '%s'.", $line_number, $this->myConstantsFilename );
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

            $this->myOldColumns[$table_name][$column_name] = array('table_name'    => $table_name,
                                                                   'column_name'   => $column_name,
                                                                   'length'        => $length,
                                                                   'constant_name' => $constant_name);
          }
        }
      }
      if (!feof( $handle )) Affirm::assertFailed( "Error reading from file '%s'.", $this->myConstantsFilename );

      $ok = fclose( $handle );
      if ($ok===false) Affirm::assertFailed( "Error closing file '%s'.", $this->myConstantsFilename );
    }
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
   * Reads configuration parameters from the configuration file.
   *
   * @param string $theConfigFilename
   */
  private function readConfigFile( $theConfigFilename )
  {
    $settings = parse_ini_file( $theConfigFilename, true );

    $this->myHostName = Util::getSetting( $settings, true, 'database', 'host_name' );
    $this->myUserName = Util::getSetting( $settings, true, 'database', 'user_name' );
    $this->myPassword = Util::getSetting( $settings, true, 'database', 'password' );
    $this->myDatabase = Util::getSetting( $settings, true, 'database', 'database_name' );

    $this->myConstantsFilename      = Util::getSetting( $settings, true, 'constants', 'columns' );
    $this->myPrefix                 = Util::getSetting( $settings, true, 'constants', 'prefix' );
    $this->myTemplateConfigFilename = Util::getSetting( $settings, true, 'constants', 'config_template' );
    $this->myConfigFilename         = Util::getSetting( $settings, true, 'constants', 'config' );
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
        $width1 = max( strlen( $column['column_name'] ), $width1 );
        $width2 = max( strlen( $column['length'] ), $width2 );
      }


      foreach ($table as $column)
      {
        if (isset($column['length']))
        {
          if (isset($column['constant_name']))
          {
            $line_format = sprintf( "%%s.%%-%ds %%%dd %%s\n", $width1, $width2 );
            $content .= sprintf( $line_format,
                                 $column['table_name'],
                                 $column['column_name'],
                                 $column['length'],
                                 $column['constant_name'] );
          }
          else
          {
            $line_format = sprintf( "%%s.%%-%ds %%%dd\n", $width1, $width2 );
            $content .= sprintf( $line_format,
                                 $column['table_name'],
                                 $column['column_name'],
                                 $column['length'] );
          }
        }
      }

      $content .= "\n";
    }

    // Save the columns, width and constants to the filesystem.
    Util::writeTwoPhases( $this->myConstantsFilename, $content );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates a PHP configuration file from the configuration template file. In the configuration template file the
   * place holder for constants is replaced with the constants definition.
   *
   */
  private function writeTargetConfigFile()
  {
    $content   = file_get_contents( $this->myTemplateConfigFilename );
    $width1    = 0;
    $width2    = 0;
    $constants = '';

    foreach ($this->myConstants as $constant => $value)
    {
      $width1 = max( strlen( $constant ), $width1 );
      $width2 = max( strlen( $value ), $width2 );
    }

    $line_format = sprintf( "const %%-%ds = %%%dd; \n", $width1, $width2 );
    foreach ($this->myConstants as $constant => $value)
    {
      $constants .= sprintf( $line_format, $constant, $value );
    }

    $count_match = substr_count( $content, self::C_PLACEHOLDER );
    if ($count_match!=1)
    {
      Affirm::assertFailed( "Error expected 1 placeholder in file '%s', found %d.", $this->myTemplateConfigFilename, $count_match );
    }

    $content = str_replace( self::C_PLACEHOLDER, $constants, $content );

    // Save the configuration file.
    Util::writeTwoPhases( $this->myConfigFilename, $content );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
