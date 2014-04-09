<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer\Generator;

use SetBased\DataLayer\StaticDataLayer as DataLayer;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class MySqlConfigConstants
 *
 * @package SetBased\DataLayer
 *          Class for creating PHP constants based on column widths, and auto increment columns and labels.
 */
class MySqlConfigConstants
{
  /**
   * @var string Filename with column names, their widths, and constant names.
   */
  private $myConstantsFilename;

  /**
   * @var string Template filename under which the file is generated with the constants.
   */
  private $myTemplateConfigFilename;

  /**
   * @var string Name of file that contains all constants.
   */
  private $myConfigFilename;

  /**
   * @var array All columns in the MySQL schema.
   */
  private $myColumns = array();

  /**
   * @var array The previous column names, widths, and constant names (i.e. the content of @c $myConstantsFilename upon
   * starting this program).
   */
  private $myOldColumns = array();

  /**
   * @var array All constants.
   */
  private $myConstants = array();

  /**
   * @var string The prefix used for designations a unknown constants.
   */
  private $myPrefix;

  /**
   * @var array All primary key labels, their widths and constant names.
   */
  private $myLabels = array();


  /** @name MySQL
  @{
   * MySQL database settings.
   */

  /**
   * @var string Host name or address.
   */
  private $myHostName;

  /**
   * @var string User name.
   */
  private $myUserName;

  /**
   * @var string User password.
   */
  private $myPassword;

  /**
   * @var string Name used database.
   */
  private $myDatabase;
  /** @} */

  /**
   * The placeholder in the template file to be replaced with the generated constants.
   */
  const C_PLACEHOLDER = '/* AUTO_GENERATED_CONSTS */';

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param string $theConfigFilename
   *
   * @return int
   */
  public function run( $theConfigFilename )
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
   * Returns the value of a setting.
   *
   * @param $theSettings      array  The settings as returned by @c parse_ini_file.
   * @param $theMandatoryFlag bool   If set and setting @a $theSettingName is not found in section @a $theSectionName
   *                          an exception will be thrown.
   * @param $theSectionName   string The name of the section of the requested setting.
   * @param $theSettingName   string The name of the setting of the requested setting.
   *
   * @return array | null
   */
  private function getSetting( $theSettings, $theMandatoryFlag, $theSectionName, $theSettingName )
  {
    // Test if the section exists.
    if (!array_key_exists( $theSectionName, $theSettings ))
    {
      if ($theMandatoryFlag)
      {
        set_assert_failed( "Section '%s' not found in configuration file.", $theSectionName );
      }
      else
      {
        return null;
      }
    }

    // Test if the setting in the section exists.
    if (!array_key_exists( $theSettingName, $theSettings[$theSectionName] ))
    {
      if ($theMandatoryFlag)
      {
        set_assert_failed( "Setting '%s' not found in section '%s' configuration file.", $theSettingName,
                           $theSectionName );
      }
      else
      {
        return null;
      }
    }

    return $theSettings[$theSectionName][$theSettingName];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads configuration parameters from the configuration file.
   *
   * @param $theConfigFilename string.
   */
  private function readConfigFile( $theConfigFilename )
  {
    $settings = parse_ini_file( $theConfigFilename, true );
    if ($settings===false) set_assert_failed( "Unable open configuration file." );

    $this->myHostName = $this->getSetting( $settings, true, 'database', 'host_name' );
    $this->myUserName = $this->getSetting( $settings, true, 'database', 'user_name' );
    $this->myPassword = $this->getSetting( $settings, true, 'database', 'password' );
    $this->myDatabase = $this->getSetting( $settings, true, 'database', 'database_name' );

    $this->myConstantsFilename      = $this->getSetting( $settings, true, 'constants', 'columns' );
    $this->myPrefix                 = $this->getSetting( $settings, true, 'constants', 'prefix' );
    $this->myTemplateConfigFilename = $this->getSetting( $settings, true, 'constants', 'config_template' );
    $this->myConfigFilename         = $this->getSetting( $settings, true, 'constants', 'config' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads the width of all columns in the MySQL schema into @c myColumns.
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
  and    table_name  rlike '^[a-zA-Z0-9_]*$'
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
   * Returns the width of a field based on column.
   *
   * @param $theColumn array The column of which the field is based.
   *
   * @returns int|null
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
        set_assert_failed( "Unknown type '%s'.", $theColumn['data_type'] );
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Writes table and column names, the width of the column, and the constant name (if assigned) to @c
   * myConstantsFilename.
   */
  private function writeColumns()
  {
    $temp_filename = $this->myConstantsFilename.'.tmp';
    $handle        = fopen( $temp_filename, 'w' );
    if ($handle===null) set_assert_failed( "Unable to open file '%s'.", $this->myConstantsFilename );

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
            $n           = fprintf( $handle, $line_format, $column['table_name'],
                                    $column['column_name'],
                                    $column['length'],
                                    $column['constant_name'] );
            if ($n===false) set_assert_failed( "Error writing file '%s'.", $this->myConstantsFilename );
          }
          else
          {
            $line_format = sprintf( "%%s.%%-%ds %%%dd\n", $width1, $width2 );
            $n           = fprintf( $handle, $line_format, $column['table_name'], $column['column_name'], $column['length'] );
            if ($n===false) set_assert_failed( "Error writing file '%s'.", $this->myConstantsFilename );
          }
        }
      }

      $n = fprintf( $handle, "\n" );
      if ($n===false) set_assert_failed( "Error writing file '%s'.", $this->myConstantsFilename );
    }

    $err = fclose( $handle );
    if ($err===false) set_assert_failed( "Error closing file '%s'.", $this->myConstantsFilename );

    $err = rename( $this->myConstantsFilename.'.tmp', $this->myConstantsFilename );
    if ($err===false)
    {
      set_assert_failed( "Error: can't rename file '%s' to '%s'.", $temp_filename,
                         $this->myConstantsFilename );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads from file @c myConstantsFilename the previous table and column names, the width of the column,
   * and the constant name (if assigned) and stores this data in @c myOldColumns.
   */
  private function getOldColumns()
  {
    if (file_exists( $this->myConstantsFilename ))
    {
      $handle = fopen( $this->myConstantsFilename, 'r' );
      if ($handle===null) set_assert_failed( "Unable to open file '%s'.", $this->myConstantsFilename );

      $line_number = 0;
      while ($line = fgets( $handle ))
      {
        $line_number++;
        if ($line!="\n")
        {
          $n = preg_match( '/^\s*(([a-zA-Z0-9_]+)\.)?([a-zA-Z0-9_]+)\.([a-zA-Z0-9_]+)\s+(\d+)\s*(\*|[a-zA-Z0-9_]+)?\s*$/', $line, $matches );
          if ($n===false) set_assert_failed( "Internal error." );

          if ($n==0)
          {
            set_assert_failed( "Illegal format at line %d in file '%s'.", $line_number, $this->myConstantsFilename );
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
      if (!feof( $handle )) set_assert_failed( "Error reading from file '%s'.", $this->myConstantsFilename );

      $ok = fclose( $handle );
      if ($ok===false) set_assert_failed( "Error closing file '%s'.", $this->myConstantsFilename );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Enhances @c myOldColumns as follows:
   * If the constant name is *, is is replaced with the column name prefixed by @c $this->myPrefix in uppercase.
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
   * Preserves relevant data in @c myOldColumns into @c myColumns.
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
   * Gets all primary key labels from the MySQL database and stores them into @c myLabels.
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
and   t2.column_name like '%%\_label'";

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
   * Merges @c myColumns and @c myLabels (i.e. all known constants) into @c myConstants.
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
    if ($ok===false) set_assert_failed( "Internal error." );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates a PHP configuration file (@c myConfigFilename) from the configuration template file
   * (@c myTemplateConfigFilename). In the configuration template file the place holder for constants is replaced
   * with the constants definition.
   */
  private function writeTargetConfigFile()
  {
    $source = file_get_contents( $this->myTemplateConfigFilename );
    if ($source===false) set_assert_failed( "Unable to read file '%s'.", $this->myTemplateConfigFilename );

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

    $count_match = substr_count( $source, self::C_PLACEHOLDER );
    if ($count_match!=1)
    {
      set_assert_failed( "Error expected 1 placeholder in file '%s', found %d.", $this->myTemplateConfigFilename, $count_match );
    }

    $source = str_replace( self::C_PLACEHOLDER, $constants, $source );

    $write_config_file_flag = true;
    if (file_exists( $this->myConfigFilename ))
    {
      $old_source = file_get_contents( $this->myConfigFilename );
      if ($old_source===false) set_assert_failed( "Unable to read file '%s'.", $this->myConfigFilename );
      if ($source==$old_source) $write_config_file_flag = false;
    }

    if ($write_config_file_flag)
    {
      $ok = file_put_contents( $this->myConfigFilename, $source );
      if ($ok===false) set_assert_failed( "Unable to write to file '%s'.", $this->myConfigFilename );
      echo "Created: '", $this->myConfigFilename, "'.\n";
    }

  }

  //--------------------------------------------------------------------------------------------------------------------
}
