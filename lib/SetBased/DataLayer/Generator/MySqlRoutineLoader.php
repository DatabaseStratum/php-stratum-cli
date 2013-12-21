<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer\Generator;

use SetBased\DataLayer\StaticDataLayer as DataLayer;

/**
 * Class MySqlRoutineLoader Class for loading stored routine into a MySQL instance from pseudo SQL files (.psql).
 * @package SetBased\DataLayer
 */
class MySqlRoutineLoader
{
  /**
   * @var string Path where .psql files can be found.
   */
  private $myIncludePath;

  /**
   * @var string The SQL mode under which the stored routine will be loaded and run.
   */
  private $mySqlMode;

  /**
   * @var string The default character set under which the stored routine will be loaded and run.
   */
  private $myCharacterSet;

  /**
   * @var string The default collate under which the stored routine will be loaded and run.
   */
  private $myCollate;

  /**
   * @var string The name of the configuration file of the target project
   */
  private $myTargetConfigFilename;



  /**
   * @var array An array with all found .psql files.
   */
  private $myPsqlFileNames = array();

  /**
   * @var string The filename of the file with the metadata of all stored routines.
   */
  private $myMetadataFilename;

  /**
   * @var array Array with the metadata of all stored routines.
   */
  private $myMetadata = array();

  /**
   * @var array A map from placeholders to their actual values.
   */
  private $myReplacePairs = array();

  /**
   * @var array An array with psql filename that are not loaded into MySQL.
   */
  private $myErrorFileNames = array();

  /**
   * @var array Information about old routines.
   */
  private $myOldRoutines;
  
  
  /**
   * @var string The current .psql filename.
   */
  private $myCurrentPsqlFilename;

  /**
   * @var string The source code as a single string of the current .psql file.
   */
  private $myCurrentPsqlSourceCode;

  /**
   * @var string The source code as an array of lines string of the current .psql file
   */
  private $myCurrentPsqlSourceCodeLines;

  /**
   * @var array The placeholders in the current .psql file.
   */
  private $myCurrentPlaceholders;

  /**
   * @var string The designation type of the stored routine in the current .psql file.
   */
  private $myCurrentType;

  /**
   * @var string The routine type (i.e. procedure or function) of the stored routine in the current .psql file.
   */
  private $myCurrentRoutineType;

  /**
   * @var string The name of the stored routine in the current .psql file.
   */
  private $myCurrentRoutineName;

  /**
   * @var string The key or index columns (depending on the designation type) of the stored routine in the current .psql file.
   */
  private $myCurrentColumns;

  /**
   * @var int The last modification time of the current .psql file.
   */
  private $myCurrentMTime;

  /**
   * @var array The replace pairs (i.e. placeholders and their actual values, see strst) for the current .psql file.
   */
  private $myCurrentReplace = array();

  /**
   * @var array The old metadata of the current .psql file.
   */
  private $myCurrentOldMetadata;

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

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param string $theConfigFilename The name of the configuration file of the current project
   * @param        $theFileNames
   *
   * @return int
   */
  public function run( $theConfigFilename, $theFileNames )
  {
    if (empty($theFileNames))
    {
      $this->loadAll( $theConfigFilename );
    }
    else
    {
      $this->loadList( $theConfigFilename, $theFileNames );
    }

    return 0;
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
        $this->myErrorFileNames = $this->myCurrentPsqlFilename;
        unset($this->myMetadata[$this->myCurrentRoutineName]);
      }
    }

    // Drop obsolete routines.
    $this->dropObsoleteRoutines();

    // Remove metadata of store routines that have been removed.
    $this->removeObsoleteMetadata();

    // Write the metadata to @c $myMetadataFilename.
    $this->writeRoutineMetadata();

    DataLayer::disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads parameters from configuration @a $theConfigFilename
   *
   * @param string $theConfigFilename
   */
  private function readConfigFile( $theConfigFilename )
  {
    $settings = parse_ini_file( $theConfigFilename, true );
    if ($settings===false) set_assert_failed( "Unable open configuration file" );

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
  /** Returns the value of a setting.
   *
   * @param $theSettings      array The settings as returned by @c parse_ini_file.
   * @param $theMandatoryFlag bool  If set and setting @a $theSettingName is not found in section @a $theSectionName an
   *                          exception will be thrown.
   * @param $theSectionName   string  The name of the section of the requested setting.
   * @param $theSettingName   string  The name of the setting of the requested setting.
   *
   * @return array|null
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
   * Searches recursively for all .psql files under directory @p $theSourceDir.
   *
   * @param $theSourceDir string
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

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Selects schema, table, and column names and the column type from the MySQL and the column type placeholders
   * to @c myReplacePairs.
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
   * Reads the metadata of stored routines from file @c myMetadataFilename in to @a myMetadata.
   */
  private function readRoutineMetaData()
  {
    $this->myMetadata = array();

    if (file_exists( $this->myMetadataFilename ))
    {
      $handle = fopen( $this->myMetadataFilename, 'r' );
      if ($handle===null) set_assert_failed( "Unable to open file '%s'.", $this->myMetadataFilename );

      // Skip header row.
      fgetcsv( $handle, 0, ',' );

      while (($row = fgetcsv( $handle, 0, ',' ))!==false)
      {
        $this->myMetadata[$row[0]] = array('routine_name'   => $row[0],
                                           'type'           => $row[1],
                                           'argument_names' => $row[2],
                                           'argument_types' => $row[3],
                                           'columns'        => $row[4],
                                           'timestamp'      => $row[5],
                                           'replace'        => $row[6]);
      }
      if (!feof( $handle )) set_assert_failed( "Did not reach eof of '%s'", $this->myMetadataFilename );

      $err = fclose( $handle );
      if ($err===false) set_assert_failed( "Error closing file '%s'.", $this->myMetadataFilename );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads constants set in @c myTargetConfigFilename and adds them to @c myReplacePairs.
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
   * Get information about all stored routines in MySQL.
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
   * Gets the SQL mode as in the order as preferred by MySQL .
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
   * Loads the stored routine in file @c myCurrentPsqlFilename into MySQL.
   * Returns @c true on success, @c false otherwise.
   * @return bool
   */
  private function loadPsqlFile()
  {
    $this->myCurrentRoutineName         = null;
    $this->myCurrentPsqlSourceCode      = null;
    $this->myCurrentPsqlSourceCodeLines = null;
    $this->myCurrentPlaceholders        = null;
    $this->myCurrentType                = null;
    $this->myCurrentRoutineType         = null;
    $this->myCurrentRoutineName         = null;
    $this->myCurrentColumns             = null;
    $this->myCurrentMTime               = null;
    $this->myCurrentReplace             = array();

    try
    {
      // We assume that the basename of the .psql file and routine name are equal.
      $this->myCurrentRoutineName = basename( $this->myCurrentPsqlFilename, '.psql' );

      // Save old metadata.
      $this->myCurrentOldMetadata = (isset($this->myMetadata[$this->myCurrentRoutineName])) ?
        $this->myMetadata[$this->myCurrentRoutineName] : null;

      // Get modification time of the source file.
      $this->myCurrentMTime = filemtime( $this->myCurrentPsqlFilename );
      if ($this->myCurrentMTime===false)
      {
        set_assert_failed( "Unable to get mtime of file '%s'.",
                           $this->myCurrentPsqlFilename );
      }

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

        // Update current Metadata;
        $this->updateCurrentMetadata();
      }

      return true;
    }
    catch (\Exception $e)
    {
      echo $e->getMessage();

      $this->myErrorFileNames[] = $this->myCurrentPsqlFilename;

      return false;
    }
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
    $this->myMetadata[$this->myCurrentRoutineName]['argument_names'] = $argument_names;
    $this->myMetadata[$this->myCurrentRoutineName]['argument_types'] = $argument_types;
    $this->myMetadata[$this->myCurrentRoutineName]['columns']        = $this->myCurrentColumns;
    $this->myMetadata[$this->myCurrentRoutineName]['timestamp']      = $this->myCurrentMTime;
    $this->myMetadata[$this->myCurrentRoutineName]['replace']        = serialize( $this->myCurrentReplace );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns @c true if the current .psql file must be load or reloaded. Otherwise returns @c false.
   * @return bool
   */
  private function getCurrentMustReload()
  {
    // If this is the first time we see the current .psql file is must be loaded.
    if (!isset($this->myCurrentOldMetadata)) return true;

    // If the .psql has changed the current .psql file is must be loaded.
    if ($this->myCurrentOldMetadata['timestamp']!=$this->myCurrentMTime) return true;

    // Get the old replace pairs
    $old_replace_pairs = unserialize( $this->myCurrentOldMetadata['replace'] );
    if ($old_replace_pairs===false)
    {
      set_assert_failed( "Unable to unserialize replace pairs for stored routine '%s'.", $this->myCurrentRoutineName );
    }

    // If the value of placeholder has changed the current .psql file is must be loaded.
    foreach ($old_replace_pairs as $place_holder => $old_value)
    {
      if (!isset($this->myReplacePairs[strtoupper( $place_holder )]) ||
        $this->myReplacePairs[strtoupper( $place_holder )]!==$old_value
      )
      {
        return true;
      }
    }

    // If current routine is not exist in database .psql file is must be loaded.
    if (!isset($this->myOldRoutines[$this->myCurrentRoutineName])) return true;

    // If current sql-mode different to set in current routine, .psql file is must reload.
    if ($this->myOldRoutines[$this->myCurrentRoutineName]['sql_mode']!=$this->mySqlMode) return true;

    // If current character different to set in current routine, .psql file is must reload.
    if ($this->myOldRoutines[$this->myCurrentRoutineName]['character_set_client']!=$this->myCharacterSet) return true;

    // If current collation different to set in current routine, .psql file is must reload.
    if ($this->myOldRoutines[$this->myCurrentRoutineName]['collation_connection']!=$this->myCollate) return true;

    return false;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extracts the placeholders from the current psql file and stored them in @c myCurrentPlaceholders.
   * Returns @c true if all placeholders are defined, @c false otherwise.
   * @return bool
   */
  private function getCurrentPlaceholders()
  {
    $err = preg_match_all( '(@[A-Za-z0-9\_\.]+(\%type)?@)', $this->myCurrentPsqlSourceCode, $matches );
    if ($err===false) set_assert_failed( 'Internal error.' );

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
      if ($ok===false) set_assert_failed( 'Internal error.' );
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extracts the designation type of the current stored routine and sets @c myCurrentType and @c myCurrentColumns.
   * Returns @c true on success. Otherwise returns @c false.
   * @return bool
   */
  private function getCurrentType()
  {
    $ret = true;
    $key = array_search( 'begin', $this->myCurrentPsqlSourceCodeLines );

    if ($key!==false)
    {
      $n = preg_match( '/^\s*--\s+type:\s*(\w+)\s*([a-zA-Z0-9_,]+)?\s*/', $this->myCurrentPsqlSourceCodeLines[$key - 1],
                       $matches );
      if ($n===false) set_assert_failed( "Internal error." );

      if ($n==1)
      {
        $this->myCurrentType = $matches[1];
        if (isset($matches[2]))
        {
          $this->myCurrentColumns = $matches[2];
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
      echo sprintf( "Error: Unable to find the designation type of the stored routine in file '%s'.",
                    $this->myCurrentPsqlFilename );
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Extracts the name of the stored routine and the stored routine type (i.e. procedure or function) and sets
   * @c    myCurrentRoutineType and @c myCurrentRoutineName.
   * Returns @c true on success. Otherwise returns @c false.
   * @todo Skip comments and string literals.
   * @return bool
   */
  private function getCurrentName()
  {
    $ret = true;

    $n = preg_match( "/create\\s+(procedure|function)\\s+([a-zA-Z0-9_]+)/i", $this->myCurrentPsqlSourceCode, $matches );
    if ($n===false) set_assert_failed( 'Internal error.' );

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
   * Load current routine to database.
   */
  private function loadCurrentPsqlFile()
  {
    echo sprintf( "Loading %s %s\n",
                  $this->myCurrentRoutineType,
                  $this->myCurrentRoutineName );

    $this->setMagicConstant();

    $lines      = explode( "\n", $this->myCurrentPsqlSourceCode );
    $sql_source = '';
    foreach ($lines as $i => &$line)
    {
      $this->myCurrentReplace['__LINE__'] = $i + 1;
      $sql_source[$i]                     = strtr( $line, $this->myCurrentReplace );
    }

    $sql_source = implode( "\n", $sql_source );

    $this->unsetMagicConstant();

    // Drop the stored procedure or function if its exists.
    $this->dropCurrentRoutine();

    // Set the SQL-mode under which the stored routine will run.
    $sql = sprintf( "set sql_mode ='%s'", $this->mySqlMode );
    DataLayer::executeNone( $sql );

    // Set the default character set and collate under which the store routine will run.
    $sql = sprintf( "set names '%s' COLLATE '%s'", $this->myCharacterSet, $this->myCollate );
    DataLayer::executeNone( $sql );

    // Load the stored routine into MySQL.
    DataLayer::executeNone( $sql_source );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Add magic constants to current replace list.
   */
  private function setMagicConstant()
  {
    $real_path = realpath( $this->myCurrentPsqlFilename );

    $this->myCurrentReplace['__FILE__'] = "'".DataLayer::realEscapeString( $real_path )."'";

    $this->myCurrentReplace['__ROUTINE__'] = "'".$this->myCurrentRoutineName."'";

    $this->myCurrentReplace['__DIR__'] = "'".DataLayer::realEscapeString( dirname( $real_path ) )."'";

  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Remove magic constants from current replace list.
   */
  private function unsetMagicConstant()
  {
    unset($this->myCurrentReplace['__FILE__']);
    unset($this->myCurrentReplace['__ROUTINE__']);
    unset($this->myCurrentReplace['__DIR__']);
    unset($this->myCurrentReplace['__LINE__']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Drops the current routine if it exists.
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
   * Drops obsolete routines (i.e. routines that exits in MySQL but for which we don't have a source file).
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
   * Removes obsolete entries from @c myMetadata.
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
   * Writes the stored routines metadata @c myMetadata to file @c myMetadataFilename.
   */
  private function writeRoutineMetadata()
  {
    $handle = fopen( $this->myMetadataFilename, 'w' );
    if ($handle===false) set_assert_failed( "Unable to open file '%s'.", $this->myMetadataFilename );

    $header = array('routine_name', 'type', 'argument_names', 'argument_types', 'columns', 'timestamp', 'replace');

    $n = fputcsv( $handle, $header );
    if ($n===false) set_assert_failed( "Error writing file '%s'.", $this->myMetadataFilename );

    $ok = ksort( $this->myMetadata );
    if ($ok===false) set_assert_failed( 'Internal error.' );

    foreach ($this->myMetadata as $routine_properties)
    {
      $n = fputcsv( $handle, $routine_properties );
      if ($n===false) set_assert_failed( "Error writing file '%s'.", $this->myMetadataFilename );
    }

    $err = fclose( $handle );
    if ($err===false) set_assert_failed( "Error closing file '%s'.", $this->myMetadataFilename );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads all stored routines in a list into MySQL.
   *
   * @param string $theConfigFilename The filename of the configuration file.
   * @param array  $theFilenames      The list of files to be loaded.
   */
  private function loadList( $theConfigFilename, $theFilenames )
  {
    $this->readConfigFile( $theConfigFilename );

    DataLayer::connect( $this->myHostName, $this->myUserName, $this->myPassword, $this->myDatabase );

    $this->findPsqlFilesFromList( $theFilenames );
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
        $this->myErrorFileNames = $this->myCurrentPsqlFilename;
        unset($this->myMetadata[$this->myCurrentRoutineName]);
      }
    }

    // Write the metadata to @c $myMetadataFilename.
    $this->writeRoutineMetadata();

    DataLayer::disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   *  Find all .psql that actually exists from a list of filenames.
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
}

//----------------------------------------------------------------------------------------------------------------------
