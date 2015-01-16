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
namespace SetBased\DataLayer\Generator;

use SetBased\DataLayer\StaticDataLayer as DataLayer;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for loading stored routines into a MySQL instance from pseudo SQL files (.psql).
 */
class MySqlRoutineLoader
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The default character set under which the stored routine will be loaded and run.
   *
   * @var string
   */
  private $myCharacterSet;

  /**
   * The default collate under which the stored routine will be loaded and run.
   *
   * @var string
   */
  private $myCollate;

  /**
   * The key or index columns (depending on the designation type) of the stored routine in the current .psql file.
   *
   * @var string
   */
  private $myCurrentColumns;

  /**
   * The column types of columns of the table for bulk insert of the stored routine in the current .psql file.
   *
   * @var string
   */
  private $myCurrentColumnsTypes;

  /**
   * The keys in the PHP array for bulk insert in the current .psql file.
   *
   * @var string
   */
  private $myCurrentFields;

  /**
   * The last modification time of the current .psql file.
   *
   * @var int
   */
  private $myCurrentMTime;

  /**
   * The old metadata of the current .psql file.
   *
   * @var array
   */
  private $myCurrentOldMetadata;

  /**
   * The placeholders in the current .psql file.
   *
   * @var array
   */
  private $myCurrentPlaceholders;

  /**
   * The current .psql filename.
   *
   * @var string
   */
  private $myCurrentPsqlFilename;

  /**
   * The source code as a single string of the current .psql file.
   *
   * @var string
   */
  private $myCurrentPsqlSourceCode;

  /**
   * The source code as an array of lines string of the current .psql file
   *
   * @var string
   */
  private $myCurrentPsqlSourceCodeLines;

  /**
   * The replace pairs (i.e. placeholders and their actual values, see strst) for the current .psql file.
   *
   * @var array
   */
  private $myCurrentReplace = array();

  /**
   * The name of the stored routine in the current .psql file.
   *
   * @var string
   */
  private $myCurrentRoutineName;

  /**
   * The routine type (i.e. procedure or function) of the stored routine in the current .psql file.
   *
   * @var string
   */
  private $myCurrentRoutineType;

  /**
   * The table name for bulk insert of the stored routine in the current .psql file (if designation type is
   * bulk_insert).
   *
   * @var string
   */
  private $myCurrentTableName;

  /**
   * The designation type of the stored routine in the current .psql file.
   *
   * @var string
   */
  private $myCurrentType;

  /**
   * Name used database.
   *
   * @var string
   */
  private $myDatabase;

  /**
   * An array with psql filename that are not loaded into MySQL.
   *
   * @var array
   */
  private $myErrorFileNames = array();

  /**
   * Host name or address.
   *
   * @var string
   */
  private $myHostName;

  /**
   * Path where .psql files can be found.
   *
   * @var string
   */
  private $myIncludePath;

  /**
   * The metadata of all stored routines.
   *
   * @var array
   */
  private $myMetadata;

  /**
   * The filename of the file with the metadata of all stored routines.
   *
   * @var string
   */
  private $myMetadataFilename;

  /**
   * Metadata about old routines.
   *
   * @var array
   */
  private $myOldRoutines;

  /**
   * User password.
   *
   * @var string
   */
  private $myPassword;

  /**
   * All found .psql files.
   *
   * @var array
   */
  private $myPsqlFileNames = array();

  /**
   * A map from placeholders to their actual values.
   *
   * @var array
   */
  private $myReplacePairs = array();

  /**
   * The SQL mode under which the stored routine will be loaded and run.
   *
   * @var string
   */
  private $mySqlMode;

  /**
   * The name of the configuration file of the target project.
   *
   * @var string
   */
  private $myTargetConfigFilename;

  /**
   * User name.
   *
   * @var string
   */
  private $myUserName;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads stored routines into the current schema.
   *
   * @param string   $theConfigFilename The name of the configuration file of the current project
   * @param string[] $theFileNames      The filenames with stored routines that mus be loaded. If empty all stored
   *                                    routines (if required) will loaded.
   *
   * @return int Returns 0 on success, 1 if one or more errors occurred.
   */
  public function main( $theConfigFilename, $theFileNames )
  {
    if (empty($theFileNames))
    {
      $this->loadAll( $theConfigFilename );
    }
    else
    {
      $this->loadList( $theConfigFilename, $theFileNames );
    }

    if ($this->myErrorFileNames)
    {
      foreach ($this->myErrorFileNames as $filename)
      {
        echo sprintf( "Error loading file '%s'.\n", $filename );
      }
    }

    return ($this->myErrorFileNames) ? 1 : 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Drops the current routine if it exists.
   *
   * @see $myCurrentRoutineName The name of the current routine.
   */
  private function dropCurrentRoutine()
  {
    if (isset($this->myOldRoutines[$this->myCurrentRoutineName]))
    {
      $sql = sprintf( "drop %s if exists %s",
                      $this->myOldRoutines[$this->myCurrentRoutineName]['routine_type'],
                      $this->myCurrentRoutineName );

      DataLayer::executeNone( $sql );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Drops obsolete routines (i.e. routines that exits in the current schema but for which we don't have a source file).
   */
  private function dropObsoleteRoutines()
  {
    foreach ($this->myOldRoutines as $old_routine)
    {
      if (!isset($this->myPsqlFileNames[$old_routine['routine_name']]))
      {
        echo sprintf( "Dropping %s %s\n",
                      strtolower( $old_routine['routine_type'] ),
                      $old_routine['routine_name'] );

        $sql = sprintf( "drop %s if exists %s", $old_routine['routine_type'], $old_routine['routine_name'] );
        DataLayer::executeNone( $sql );
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Searches recursively for all .psql files in a directory.
   *
   * @param string $theSourceDir The directory.
   */
  private function findPsqlFiles( $theSourceDir = null )
  {
    if ($theSourceDir===null) $theSourceDir = $this->myIncludePath;

    $psql_filenames = glob( "$theSourceDir/*.psql" );
    foreach ($psql_filenames as $psql_filename)
    {
      $base_name = basename( $psql_filename, '.psql' );
      if (!isset($this->myPsqlFileNames[$base_name]))
      {
        $this->myPsqlFileNames[$base_name] = $psql_filename;
      }
      else
      {
        echo sprintf( "Error: Files '%s' and '%s' have the same basename.\n",
                      $this->myPsqlFileNames[$base_name],
                      $psql_filename );
        $this->myErrorFileNames[] = $psql_filename;
      }
    }

    if (is_dir( $theSourceDir ))
    {
      $filenames = scandir( $theSourceDir );
      $dir_names = array();
      foreach ($filenames as $filename)
      {
        if (is_dir( $theSourceDir.'/'.$filename ))
        {
          if ($filename!='.' && $filename!='..')
          {
            $dir_names[] = $theSourceDir.'/'.$filename;
          }
        }
      }

      foreach ($dir_names as $dir_name)
      {
        $this->findPsqlFiles( $dir_name );
      }
    }
    else
    {
      echo sprintf( "Error: Directory '%s' not exist.\n", $theSourceDir );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Finds all .psql files that actually exists from a list of filenames.
   *
   * @param array $theFilenames The list of filenames.
   */
  private function findPsqlFilesFromList( $theFilenames )
  {
    foreach ($theFilenames as $psql_filename)
    {
      if (file_exists( $psql_filename ))
      {
        $base_name = basename( $psql_filename, '.psql' );
        if (!isset($this->myPsqlFileNames[$base_name]))
        {
          $this->myPsqlFileNames[$base_name] = $psql_filename;
        }
        else
        {
          echo sprintf( "Error: Files '%s' and '%s' have the same basename.\n",
                        $this->myPsqlFileNames[$base_name],
                        $psql_filename );
          $this->myErrorFileNames[] = $psql_filename;
        }
      }
      else
      {
        echo sprintf( "File not exists: '%s'.\n", $psql_filename );
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   *  Gets the column names and column types of the current table for bulk insert.
   */
  private function getBulkInsertTableColumnsInfo()
  {
    // Check if table is a temporary table or a non-temporary table.
    $query                  = sprintf( '
select 1
from   information_schema.TABLES
where table_schema = database()
and   table_name   = %s', DataLayer::quoteString( $this->myCurrentTableName ) );
    $table_is_non_temporary = DataLayer::executeRow0( $query );

    // Create temporary table if table is non-temporary table.
    if (!$table_is_non_temporary)
    {
      $query = 'call '.$this->myCurrentRoutineName.'()';
      DataLayer::executeNone( $query );
    }

    // Get information about the columns of the table.
    $query   = sprintf( "describe `%s`", $this->myCurrentTableName );
    $columns = DataLayer::executeRows( $query );

    // Drop temporary table if table is non-temporary.
    if (!$table_is_non_temporary)
    {
      $query = sprintf( "drop temporary table `%s`", $this->myCurrentTableName );
      DataLayer::executeNone( $query );
    }

    // Check number of columns in the table match the number of fields given in the designation type.
    $n1 = count( explode( ',', $this->myCurrentColumns ) );
    $n2 = count( $columns );
    if ($n1!=$n2) set_assert_failed( "Number of fields %d and number of columns %d don't match.", $n1, $n2 );

    // Fill arrays with column names and column types.
    $tmp_column_types = array();
    $tmp_fields       = array();
    foreach ($columns as $column)
    {
      preg_match( "(\\w+)", $column['Type'], $type );
      $tmp_column_types[] = $type['0'];
      $tmp_fields[]       = $column['Field'];
    }

    $this->myCurrentColumnsTypes = implode( ',', $tmp_column_types );
    $this->myCurrentFields       = implode( ',', $tmp_fields );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Selects schema, table, column names and the column type from MySQL and saves them as replace pairs.
   *
   * @see $myReplacePairs The property were the replace pairs are stored.
   */
  private function getColumnTypes()
  {
    $query = '
select table_name                                    table_name
,      column_name                                   column_name
,      column_type                                   column_type
,      character_set_name                            character_set_name
,      null                                          table_schema
from   information_schema.COLUMNS
where  table_schema = database()
union all
select table_name                                    table_name
,      column_name                                   column_name
,      column_type                                   column_type
,      character_set_name                            character_set_name
,      table_schema                                  table_schema
from   information_schema.COLUMNS
order by table_schema
,        table_name
,        column_name';

    $rows = DataLayer::executeRows( $query );
    foreach ($rows as $row)
    {
      $key = '@';
      if (isset($row['table_schema'])) $key .= $row['table_schema'].'.';
      $key .= $row['table_name'].'.'.$row['column_name'].'%type@';
      $key = strtoupper( $key );

      $value = $row['column_type'];
      if (isset($row['character_set_name'])) $value .= ' character set '.$row['character_set_name'];

      $this->myReplacePairs[$key] = $value;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads constants set the PHP configuration file and  adds them to the replace pairs.
   *
   * @see $myTargetConfigFilename The property with HP configuration filename.
   * @see $myReplacePairs The property were the replace pairs are stored.
   */
  private function getConstants()
  {
    // If myTargetConfigFilename is not set return immediately.
    if (!isset($this->myTargetConfigFilename)) return;

    if (!is_readable( $this->myTargetConfigFilename ))
    {
      set_assert_failed( "Configuration file is not readable '%s'.",
                         $this->myTargetConfigFilename );
    }

    require_once($this->myTargetConfigFilename);
    $constants    = get_defined_constants( true );
    $user_defined = (isset($constants['user'])) ? $constants['user'] : array();

    foreach ($user_defined as $name => $value)
    {
      if (!is_numeric( $value )) $value = "'$value'";

      $this->myReplacePairs['@'.$name.'@'] = $value;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Gets the SQL mode in the order as preferred by MySQL.
   */
  private function getCorrectSqlMode()
  {
    $sql = sprintf( "set sql_mode ='%s'", $this->mySqlMode );
    DataLayer::executeNone( $sql );

    $query           = "select @@sql_mode;";
    $tmp             = DataLayer::executeRows( $query );
    $this->mySqlMode = $tmp[0]['@@sql_mode'];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns true if the current .psql file must be load or reloaded. Otherwise returns false.
   *
   * @see $myCurrentRoutineName The name of the current stored routine name.
   * @return bool
   */
  private function getCurrentMustReload()
  {
    // If this is the first time we see the current .psql file is must be loaded.
    if (!isset($this->myCurrentOldMetadata)) return true;

    // If the .psql has changed the current .psql file is must be loaded.
    if ($this->myCurrentOldMetadata['timestamp']!=$this->myCurrentMTime) return true;

    // If the value of a placeholder has changed the current .psql file is must be loaded.
    foreach ($this->myCurrentOldMetadata['replace'] as $place_holder => $old_value)
    {
      if (!isset($this->myReplacePairs[strtoupper( $place_holder )]) ||
        $this->myReplacePairs[strtoupper( $place_holder )]!==$old_value
      )
      {
        return true;
      }
    }

    // If current routine not exists in database .psql file is must be loaded.
    if (!isset($this->myOldRoutines[$this->myCurrentRoutineName])) return true;

    // If current sql-mode is different the .psql file is must reload.
    if ($this->myOldRoutines[$this->myCurrentRoutineName]['sql_mode']!=$this->mySqlMode) return true;

    // If current character is different the .psql file is must reload.
    if ($this->myOldRoutines[$this->myCurrentRoutineName]['character_set_client']!=$this->myCharacterSet) return true;

    // If current collation is different the .psql file is must reload.
    if ($this->myOldRoutines[$this->myCurrentRoutineName]['collation_connection']!=$this->myCollate) return true;

    return false;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extracts the name of the stored routine and the stored routine type (i.e. procedure or function) source.
   *
   * @see  $myCurrentPsqlSourceCode The source of the current stored routine.
   * @see  $myCurrentRoutineType The property where the type of the routine is stored.
   * @see  $myCurrentRoutineName. The property where the name of the routine is stored.
   * @todo Skip comments and string literals.
   * @return bool Returns true on success, false otherwise.
   */
  private function getCurrentName()
  {
    $ret = true;

    $n = preg_match( "/create\\s+(procedure|function)\\s+([a-zA-Z0-9_]+)/i", $this->myCurrentPsqlSourceCode, $matches );
    if ($n===false) set_assert_failed( "Internal error." );

    if ($n==1)
    {
      $this->myCurrentRoutineType = strtolower( $matches[1] );

      if ($this->myCurrentRoutineName!=$matches[2])
      {
        echo sprintf( "Error: Stored routine name '%s' does not match filename in file '%s'.\n",
                      $matches[2],
                      $this->myCurrentPsqlFilename );
        $ret = false;
      }
    }
    else
    {
      $ret = false;
    }

    if (!isset($this->myCurrentRoutineType))
    {
      echo sprintf( "Error: Unable to find the stored routine name and type in file '%s'.\n",
                    $this->myCurrentPsqlFilename );
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extracts the placeholders from the current stored routine source and stored them.
   *
   * @see $myCurrentPsqlSourceCode The source of the current stored routine.
   * @see $myCurrentReplace The property where the place holders are stored.
   * @return bool Returns true if all placeholders are defined, false otherwise.
   */
  private function getCurrentPlaceholders()
  {
    $err = preg_match_all( '(@[A-Za-z0-9\_\.]+(\%type)?@)', $this->myCurrentPsqlSourceCode, $matches );
    if ($err===false) set_assert_failed( "Internal error." );

    $ret                         = true;
    $this->myCurrentPlaceholders = array();

    if (!empty($matches[0]))
    {
      foreach ($matches[0] as $placeholder)
      {
        if (!isset($this->myReplacePairs[strtoupper( $placeholder )]))
        {
          echo sprintf( "Error: Unknown placeholder '%s' in file '%s'.\n", $placeholder, $this->myCurrentPsqlFilename );
          $ret = false;
        }

        if (!isset($this->myCurrentPlaceholders[$placeholder]))
        {
          $this->myCurrentPlaceholders[$placeholder] = $placeholder;
        }
      }
    }

    if ($ret===true)
    {
      foreach ($this->myCurrentPlaceholders as $placeholder)
      {
        $this->myCurrentReplace[$placeholder] = $this->myReplacePairs[strtoupper( $placeholder )];
      }
      $ok = ksort( $this->myCurrentReplace );
      if ($ok===false) set_assert_failed( "Internal error." );
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extracts the designation type of the current stored routine.
   *
   * @see myCurrentType    The property were the designation type is stored.
   * @see myCurrentColumns The property were the columns (to be used by the wrapper) is stored.
   * @return bool Returns true on success. Otherwise returns false.
   */
  private function getCurrentType()
  {
    $ret = true;
    $key = array_search( 'begin', $this->myCurrentPsqlSourceCodeLines );

    if ($key!==false)
    {
      $n = preg_match( '/^\s*--\s+type:\s*(\w+)\s*(.+)?\s*$/', $this->myCurrentPsqlSourceCodeLines[$key - 1],
                       $matches );

      if ($n===false) set_assert_failed( "Internal error." );

      if ($n==1)
      {
        $this->myCurrentType = $matches[1];
        switch ($this->myCurrentType)
        {
          case 'bulk_insert':
            $m = preg_match( '/^([a-zA-Z0-9_]+)\s+([a-zA-Z0-9_,]+)$/', $matches[2], $info );
            if ($m===false) set_assert_failed( "Internal error." );
            if ($m==0) set_assert_failed( sprintf( "Error: Expected: -- type: bulk_insert <table_name> <columns> in file '%s'.\n",
                                                   $this->myCurrentPsqlFilename ) );
            $this->myCurrentTableName = $info[1];
            $this->myCurrentColumns   = $info[2];
            break;

          case 'rows_with_key':
          case 'rows_with_index':
            $this->myCurrentColumns = $matches[2];
            break;

          default:
            if (isset($matches[2])) $ret = false;
        }
      }
      else
      {
        $ret = false;
      }
    }
    else
    {
      $ret = false;
    }

    if ($ret===false)
    {
      echo sprintf( "Error: Unable to find the designation type of the stored routine in file '%s'.\n",
                    $this->myCurrentPsqlFilename );
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Retrieves information about all stored routines in the current schema.
   *
   * @see $myOldRoutines The property where the information is stored.
   */
  private function getOldRoutines()
  {
    $query = "
select routine_name
,      routine_type
,      sql_mode
,      character_set_client
,      collation_connection
from  information_schema.ROUTINES
where ROUTINE_SCHEMA = database()
order by routine_name";

    $rows = DataLayer::executeRows( $query );

    $this->myOldRoutines = array();
    foreach ($rows as $row)
    {
      $this->myOldRoutines[$row['routine_name']] = $row;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the value of a setting.
   *
   * @param array  $theSettings      The settings as returned by parse_ini_file.
   * @param bool   $theMandatoryFlag If set and setting $theSettingName is not found in section $theSectionName an
   *                                 exception will be thrown.
   * @param string $theSectionName   The name of the section of the requested setting.
   * @param string $theSettingName   The name of the setting of the requested setting.
   *
   * @return array|null The value of the setting.
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
   * Loads all stored routines into MySQL.
   *
   * @param string $theConfigFilename The filename of the configuration file.
   */
  private function loadAll( $theConfigFilename )
  {
    $this->readConfigFile( $theConfigFilename );

    DataLayer::connect( $this->myHostName, $this->myUserName, $this->myPassword, $this->myDatabase );

    $this->findPsqlFiles();
    $this->getColumnTypes();
    $this->readRoutineMetaData();
    $this->getConstants();
    $this->getOldRoutines();
    $this->getCorrectSqlMode();

    foreach ($this->myPsqlFileNames as $this->myCurrentPsqlFilename)
    {
      $err = $this->loadPsqlFile();
      if ($err===false)
      {
        $this->myErrorFileNames[] = $this->myCurrentPsqlFilename;
        unset($this->myMetadata[$this->myCurrentRoutineName]);
      }
    }

    // Drop obsolete stored routines.
    $this->dropObsoleteRoutines();

    // Remove metadata of stored routines that have been removed.
    $this->removeObsoleteMetadata();

    // Write the metadata to file.
    $this->writeRoutineMetadata();

    DataLayer::disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads the current routine into the database.
   */
  private function loadCurrentPsqlFile()
  {
    echo sprintf( "Loading %s %s\n",
                  $this->myCurrentRoutineType,
                  $this->myCurrentRoutineName );

    // Set magic constants specific for this stored routine.
    $this->setMagicConstants();

    // Replace all place holders with their values.
    $lines      = explode( "\n", $this->myCurrentPsqlSourceCode );
    $sql_source = array();
    foreach ($lines as $i => &$line)
    {
      $this->myCurrentReplace['__LINE__'] = $i + 1;
      $sql_source[$i]                     = strtr( $line, $this->myCurrentReplace );
    }
    $sql_source = implode( "\n", $sql_source );

    // Unset magic constants specific for this stored routine.
    $this->unsetMagicConstants();

    // Drop the stored procedure or function if its exists.
    $this->dropCurrentRoutine();

    // Set the SQL-mode under which the stored routine will run.
    $sql = sprintf( "set sql_mode ='%s'", $this->mySqlMode );
    DataLayer::executeNone( $sql );

    // Set the default character set and collate under which the store routine will run.
    $sql = sprintf( "set names '%s' collate '%s'", $this->myCharacterSet, $this->myCollate );
    DataLayer::executeNone( $sql );

    // Load the stored routine into MySQL.
    DataLayer::executeNone( $sql_source );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads all stored routines in a list into MySQL.
   *
   * @param string $theConfigFilename The filename of the configuration file.
   * @param array  $theFileNames      The list of files to be loaded.
   */
  private function loadList( $theConfigFilename, $theFileNames )
  {
    $this->readConfigFile( $theConfigFilename );

    DataLayer::connect( $this->myHostName, $this->myUserName, $this->myPassword, $this->myDatabase );

    $this->findPsqlFilesFromList( $theFileNames );
    $this->getColumnTypes();
    $this->readRoutineMetaData();
    $this->getConstants();
    $this->getOldRoutines();
    $this->getCorrectSqlMode();

    foreach ($this->myPsqlFileNames as $this->myCurrentPsqlFilename)
    {
      $err = $this->loadPsqlFile();
      if ($err===false)
      {
        $this->myErrorFileNames[] = $this->myCurrentPsqlFilename;
        unset($this->myMetadata[$this->myCurrentRoutineName]);
      }
    }

    // Write the metadata to @c $myMetadataFilename.
    $this->writeRoutineMetadata();

    DataLayer::disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads a single stored routine into MySQL.
   *
   * @see $myCurrentPsqlFilename The filename with the stored routine to be loaded.
   * @return bool Returns true on success, false otherwise.
   */
  private function loadPsqlFile()
  {
    $this->myCurrentRoutineName         = null;
    $this->myCurrentPsqlSourceCode      = null;
    $this->myCurrentPsqlSourceCodeLines = null;
    $this->myCurrentPlaceholders        = null;
    $this->myCurrentType                = null;
    $this->myCurrentTableName           = null;
    $this->myCurrentRoutineType         = null;
    $this->myCurrentRoutineName         = null;
    $this->myCurrentColumns             = null;
    $this->myCurrentFields              = null;
    $this->myCurrentColumnsTypes        = null;

    $this->myCurrentMTime   = null;
    $this->myCurrentReplace = array();

    try
    {
      // We assume that the basename of the .psql file and routine name are equal.
      $this->myCurrentRoutineName = basename( $this->myCurrentPsqlFilename, '.psql' );

      // Save old metadata.
      $this->myCurrentOldMetadata = (isset($this->myMetadata[$this->myCurrentRoutineName])) ?
        $this->myMetadata[$this->myCurrentRoutineName] : null;

      // Get modification time of the source file.
      $this->myCurrentMTime = filemtime( $this->myCurrentPsqlFilename );
      if ($this->myCurrentMTime===false) set_assert_failed( "Unable to get mtime of file '%s'.",
                                                            $this->myCurrentPsqlFilename );

      // Load the stored routine into MySQL only if the source has changed or the value of a placeholder.
      $load = $this->getCurrentMustReload();
      if ($load)
      {
        // Read the psql source code.
        $this->myCurrentPsqlSourceCode = file_get_contents( $this->myCurrentPsqlFilename );
        if ($this->myCurrentPsqlSourceCode===false)
        {
          set_assert_failed( "Unable to read file '%s'.", $this->myCurrentPsqlFilename );
        }

        // Split the psql source code into lines.
        $this->myCurrentPsqlSourceCodeLines = explode( "\n", $this->myCurrentPsqlSourceCode );
        if ($this->myCurrentPsqlSourceCodeLines===false) return false;

        // Extract placeholders from the .psql source code.
        $ok = $this->getCurrentPlaceholders( $this->myCurrentPsqlSourceCode, $this->myCurrentPsqlFilename );
        if ($ok===false) return false;

        // Extract the designation type and key or index columns from the .psql source code.
        $ok = $this->getCurrentType();
        if ($ok===false) return false;

        // Extract the routine type (procedure or function) and routine name from the .psql source code.
        $ok = $this->getCurrentName();
        if ($ok===false) return false;

        // Load the routine into MySQL.
        $this->loadCurrentPsqlFile();

        // If the routine is a bulk insert routine, enhance metadata with table columns information.
        if ($this->myCurrentType=='bulk_insert')
        {
          $this->getBulkInsertTableColumnsInfo();
        }

        // Update current Metadata;
        $this->updateCurrentMetadata();
      }

      return true;
    }
    catch (\Exception $e)
    {
      echo $e->getMessage(), "\n";

      $this->myErrorFileNames[] = $this->myCurrentPsqlFilename;

      return false;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads parameters from the configuration file.
   *
   * @param string $theConfigFilename
   */
  private function readConfigFile( $theConfigFilename )
  {
    $settings = parse_ini_file( $theConfigFilename, true );
    if ($settings===false) set_assert_failed( "Unable open configuration file." );

    $this->myHostName = $this->getSetting( $settings, true, 'database', 'host_name' );
    $this->myUserName = $this->getSetting( $settings, true, 'database', 'user_name' );
    $this->myPassword = $this->getSetting( $settings, true, 'database', 'password' );
    $this->myDatabase = $this->getSetting( $settings, true, 'database', 'database_name' );

    $this->myMetadataFilename     = $this->getSetting( $settings, true, 'wrapper', 'metadata' );
    $this->myIncludePath          = $this->getSetting( $settings, true, 'loader', 'psql' );
    $this->myTargetConfigFilename = $this->getSetting( $settings, false, 'loader', 'config' );
    $this->mySqlMode              = $this->getSetting( $settings, true, 'loader', 'sql_mode' );
    $this->myCharacterSet         = $this->getSetting( $settings, true, 'loader', 'character_set' );
    $this->myCollate              = $this->getSetting( $settings, true, 'loader', 'collate' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads the metadata of stored routines from file.
   *
   * @see $myMetadataFilename The filename with the metadata is stored.
   * @see $myMetadata The proeprty were the metadata is stored.
   */
  private function readRoutineMetaData()
  {
    if (file_exists( $this->myMetadataFilename ))
    {
      $data = file_get_contents( $this->myMetadataFilename );
      if ($data===false) set_assert_failed( "Error reading file '%s'.", $this->myMetadataFilename );

      $this->myMetadata = json_decode( $data, true );
      if (json_last_error()!=JSON_ERROR_NONE) set_assert_failed( "Error decoding JSON: '%s'.", json_last_error_msg() );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Removes obsolete entries from the metadata.
   *
   * @see $myMetadata The proeprty were the metadata is stored.
   */
  private function removeObsoleteMetadata()
  {
    $clean = array();
    foreach ($this->myPsqlFileNames as $myPsqlFilename)
    {
      $tmp = basename( $myPsqlFilename, '.psql' );
      if (isset($this->myMetadata[$tmp])) $clean[$tmp] = $this->myMetadata[$tmp];
    }
    $this->myMetadata = $clean;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Add magic constants to current replace list.
   */
  private function setMagicConstants()
  {
    $real_path = realpath( $this->myCurrentPsqlFilename );

    $this->myCurrentReplace['__FILE__']    = "'".DataLayer::realEscapeString( $real_path )."'";
    $this->myCurrentReplace['__ROUTINE__'] = "'".$this->myCurrentRoutineName."'";
    $this->myCurrentReplace['__DIR__']     = "'".DataLayer::realEscapeString( dirname( $real_path ) )."'";
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Removes magic constants from current replace list.
   */
  private function unsetMagicConstants()
  {
    unset($this->myCurrentReplace['__FILE__']);
    unset($this->myCurrentReplace['__ROUTINE__']);
    unset($this->myCurrentReplace['__DIR__']);
    unset($this->myCurrentReplace['__LINE__']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Updates the metadata for current .psql file.
   */
  private function updateCurrentMetadata()
  {
    $query = sprintf( "
select group_concat( t2.parameter_name order by t2.ordinal_position separator ',' ) 'argument_names'
,      group_concat( t2.data_type      order by t2.ordinal_position separator ',' ) 'argument_types'
from            information_schema.ROUTINES   t1
left outer join information_schema.PARAMETERS t2  on  t2.specific_schema = t1.routine_schema and
                                                      t2.specific_name   = t1.routine_name and
                                                      t2.parameter_mode   is not null
where t1.routine_schema = database()
and   t1.routine_name   = '%s'", $this->myCurrentRoutineName );

    $tmp = DataLayer::executeRows( $query );
    /** @todo replace with execute singleton */

    $argument_names = $tmp[0]['argument_names'];
    $argument_types = $tmp[0]['argument_types'];

    $this->myMetadata[$this->myCurrentRoutineName]['routine_name']   = $this->myCurrentRoutineName;
    $this->myMetadata[$this->myCurrentRoutineName]['type']           = $this->myCurrentType;
    $this->myMetadata[$this->myCurrentRoutineName]['table_name']     = $this->myCurrentTableName;
    $this->myMetadata[$this->myCurrentRoutineName]['argument_names'] = ($argument_names) ? explode( ',', $argument_names ) : array();
    $this->myMetadata[$this->myCurrentRoutineName]['argument_types'] = ($argument_types) ? explode( ',', $argument_types ) : array();
    $this->myMetadata[$this->myCurrentRoutineName]['columns']        = ($this->myCurrentColumns) ? explode( ',', $this->myCurrentColumns ) : array();
    $this->myMetadata[$this->myCurrentRoutineName]['fields']         = ($this->myCurrentFields) ? explode( ',', $this->myCurrentFields ) : array();
    $this->myMetadata[$this->myCurrentRoutineName]['column_types']   = ($this->myCurrentColumnsTypes) ? explode( ',', $this->myCurrentColumnsTypes ) : array();
    $this->myMetadata[$this->myCurrentRoutineName]['timestamp']      = $this->myCurrentMTime;
    $this->myMetadata[$this->myCurrentRoutineName]['replace']        = $this->myCurrentReplace;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Writes the stored routines metadata @c myMetadata to file @c myMetadataFilename.
   */
  private function writeRoutineMetadata()
  {
    // Note: Constant JSON_PRETTY_PRINT was introduced in php 5.4 while we want to be compatible with php 5.3.
    $options = 0;
    if (defined( 'JSON_PRETTY_PRINT' )) $options = $options | constant( 'JSON_PRETTY_PRINT' );

    $json_data = json_encode( $this->myMetadata, $options );
    if (json_last_error()!=JSON_ERROR_NONE) set_assert_failed( "Error of encoding to JSON: '%s'.", json_last_error_msg() );

    $bytes = file_put_contents( $this->myMetadataFilename, $json_data );
    if ($bytes===false) set_assert_failed( "Error writing file '%s'.", $this->myMetadataFilename );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
