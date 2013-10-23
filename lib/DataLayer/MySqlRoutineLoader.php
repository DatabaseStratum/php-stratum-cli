<?php
//----------------------------------------------------------------------------------------------------------------------
namespace DataLayer;

//----------------------------------------------------------------------------------------------------------------------
/** @brief Class for loading stored routine into a MySQL instance from pseudo SQL files (.psql).
 */
class  MySqlRoutineLoader
{
  /** @name Settings
     @{
     Properties for settings.
  */

  /** Path where .psql files can be found.
  */
  private $myIncludePath;

  /** The name of the file with SQL statement to retreive table and column names, and column types.
   */
  private $mySqlColumnTypeFilename;

  /** The SQL mode under which the stored routine will be loaded and run.
   */
  private $mySqlMode;

  /** The default character set under which the stored routine will be loaded and run.
   */
  private $myCharacterSet;

  /** The default collate under which the stored routine will be loaded and run.
   */
  private $myCollate;

  /** The name of the configuration file of the target porject
   */
  private $myTargetConfigFilename;
  /** @} */


  /** @name Overall
     @{
     Properties with data about all stored routines and .psql files.
   */

  /** An array with all found .psql files.
   */
  private $myPsqlFilenames = array();

  /** The filename of the file with the metadata of all stored routines.
   */
  private $myMetadataFilename;

  /** Array with the metadata of all stored routines.
   */
  private $myMetadata = array();

  /** A map from placeholders to their actual values.
   */
  private $myReplacePairs = array();

  /** An array with psql filenames that are not loaded into MySQL.
   */
  private $myErrorFilenames = array();

  /** Information about old routines.
   */
  private $myOldRoutines;
  /** @} */


  /** @name Current
     @{
     Properties with data about the current stored routine and/or .psql file.
  */
  /** The current .psql filename.
   */
  private $myCurrentPsqlFilename;

  /** The source code as a single string of the current .psql file.
   */
  private $myCurrentPsqlSourceCode;

  /** The source code as an array of lines string of the current .psql file.
   */
  private $myCurrentPsqlSourceCodeLines;

  /** The placeholders in the current .psql file.
   */
  private $myCurrentPlaceholders;

  /** The designation type of the stored routine in the current .psql file.
   */
  private $myCurrentType;

  /** The routine type (i.e. procedure or function) of the stored routine in the current .psql file.
   */
  private $myCurrentRoutineType;

  /** The name of the stored routine in the current .psql file.
   */
  private $myCurrentRoutineName;

  /** The key or index columns (depending on the designation type) of the stored routine in the current .psql file.
   */
  private $myCurrentColumns;

  /** The last modification time of the current .psql file.
   */
  private $myCurrentMtime;

  /** The replace pairs (i.e. placeholders and their actual values, see strst) for the current .psql file.
   */
  private $myCurrentReplace = array();

  /** The old metadata of the current .psql file.
   */
  private $myCurrentOldMetadata;
  /** @} */

  /** @name MySQL
     @{
     MySQL database settings.
  */

  /** Host name or addres.
   */
  private $myHostName;

  /** User name.
   */
  private $myUserName;

  /** Uesr password.
   */
  private $myPassword;

  /** Name used databae.
   */
  private $myDatabase;
  /** @} */

  //--------------------------------------------------------------------------------------------------------------------
  /** Returns the value of a setting.
      @param $theSettings      The settings as returned by @c parse_ini_file.
      @param $theMandatoryFlag If set and setting @a $theSettingName is not found in section @a $theSectionName an
                               exception will be thrown.
      @param $theSectionName   The name of the section of the requested setting.
      @param $theSettingName   The name of the setting of the requested setting.
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
  /** Reads parameters from configuration @a $theConfigFilename
   */
  private function readConfigFile( $theConfigFilename )
  {
    $settings = parse_ini_file( $theConfigFilename, true );
    if ($settings===false) set_assert_failed( "Unable open configuration file" );

    $this->myHostName = $this->getSetting( $settings, true,  'database', 'host_name');
    $this->myUserName = $this->getSetting( $settings, true,  'database', 'user_name');
    $this->myPassword = $this->getSetting( $settings, true,  'database', 'password');
    $this->myDatabase = $this->getSetting( $settings, true,  'database', 'database_name');

    $this->myMetadataFilename      = $this->getSetting( $settings, true,  'wrapper', 'metadata');
    $this->myIncludePath           = $this->getSetting( $settings, true,  'loader',  'psql' );
    $this->mySqlColumnTypeFilename = $this->getSetting( $settings, true,  'loader',  'column_types_sql' );
    $this->myTargetConfigFilename  = $this->getSetting( $settings, false, 'loader',  'config' );
    $this->mySqlMode               = $this->getSetting( $settings, true,  'loader',  'sql_mode');
    $this->myCharacterSet          = $this->getSetting( $settings, true,  'loader',  'character_set' );
    $this->myCollate               = $this->getSetting( $settings, true,  'loader',  'collate' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Reads constants set in @c myTargetConfigFilename and adds them to @c myReplacePairs.
   */
  private function getConstants()
  {
    // If myTargetConfigFilename is not set return immediatly.
    if (!isset($this->myTargetConfigFilename)) return;

    if (!is_readable( $this->myTargetConfigFilename )) set_assert_failed( "Configuration file is not readable '%s'.",
                                                                           $this->myTargetConfigFilename );

    require_once( $this->myTargetConfigFilename );
    $constants    = get_defined_constants(true);
    $user_defined = (isset($constants['user'])) ? $constants['user'] : array();

    foreach( $user_defined as $name => $value )
    {
      if (!is_numeric( $value )) $value = "'$value'";

      $this->myReplacePairs['@'.$name.'@'] = $value;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Searches recursively for all .psql files under directory @p $theSourceDir.
   */
  private function findPsqlFiles( $theSourceDir=null )
  {
    if($theSourceDir===null) $theSourceDir = $this->myIncludePath;

    $psql_filenames = glob( "$theSourceDir/*.psql" );
    foreach( $psql_filenames as $psql_filename )
    {
      $base_name = basename( $psql_filename, '.psql' );
      if (!isset($this->myPsqlFilenames[$base_name]))
      {
        $this->myPsqlFilenames[$base_name] = $psql_filename;
      }
      else
      {
        echo sprintf( "Error: Files '%s' and '%s' have the same basename.\n",
                      $this->myPsqlFilenames[$base_name],
                      $psql_filename );
        $this->myErrorFilenames[] = $psql_filename;
      }
    }

    $filenames = scandir( $theSourceDir );
    $dir_names = array();
    foreach( $filenames as $filename )
    {
      if (is_dir( $theSourceDir.'/'.$filename ))
      {
        if ($filename!='.' && $filename!='..')
        {
          $dir_names[] = $theSourceDir.'/'.$filename;
        }
      }
    }

    foreach( $dir_names as $dir_name )
    {
      $this->findPsqlFiles( $dir_name );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Returns the metadata @a myMetadata of stored routines stored in file.
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

      while (($row = fgetcsv( $handle, 0, ','  ))!==false)
      {
        $this->myMetadata[$row[0]] = array( 'routine_name'   => $row[0],
                                            'type'           => $row[1],
                                            'argument_types' => $row[2],
                                            'columns'        => $row[3],
                                            'timestamp'      => $row[4],
                                            'replace'        => $row[5] );
      }

      $err = fclose( $handle );
      if ($err===false) set_assert_failed( "Error closing file '%s'.", $this->myMetadataFilename );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Writes the stored routines metadata @c myMetadatae to file @c myMetadataFilename.
   */
  private function writeRoutineMetadata()
  {
    $handle = fopen( $this->myMetadataFilename, 'w' );
    if ($handle===false) set_assert_failed( "Unable to open file '%s'.", $this->myMetadataFilename );

    $header = array( 'routine_name', 'type', 'argument_types', 'columns', 'timestamp', 'replace' );
    $n = fputcsv( $handle, $header  );
    if ($n===false) set_assert_failed( "Error writing file '%s'.", $this->myMetadataFilename );

    $ok = ksort( $this->myMetadata );
    if ($ok===false) set_assert_failed( 'Internal error.' );

    foreach( $this->myMetadata as $routine_properties )
    {
      $n = fputcsv( $handle, $routine_properties );
      if ($n===false) set_assert_failed( "Error writing file '%s'.", $this->myMetadataFilename );
    }

    $err = fclose( $handle );
    if ($err===false) set_assert_failed( "Error closing file '%s'.", $this->myMetadataFilename );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Exracts the placeholders from the current psql file and stored them in @c myCurrentPlaceholders.
      Returns @c true if all placeholders are defined, @c false otherwise.
  */
  private function getCurrentPlaceholders()
  {
    $err = preg_match_all( "(@[A-Za-z0-9\_\.]+(\%type)?@)", $this->myCurrentPsqlSourceCode, $matches );
    if ($err===false) set_assert_failed( 'Internal error.' );

    $ret = true;
    $this->myCurrentPlaceholders = array();

    if (!empty($matches[0]))
    {
      foreach( $matches[0] as $placeholder )
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
      foreach( $this->myCurrentPlaceholders as $placeholder )
      {
        $this->myCurrentReplace[$placeholder] = $this->myReplacePairs[strtoupper( $placeholder )];
      }
      $ok = ksort( $this->myCurrentReplace );
      if ($ok===false) set_assert_failed( 'Internal error.' );
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Extracts the designation type of the current stored routine and sets @c myCurrentType and @c myCurrentColumns.
      Returns @c true on success. Otherwise returns @c false.
   */
  private function getCurrentType()
  {
    $ret = true;
    $key = array_search( 'begin', $this->myCurrentPsqlSourceCodeLines );

    if ($key!==false)
    {
      $n = preg_match( "/^\s*--\s+type:\s*(\w+)\s*([a-zA-Z0-9_,]+)?\s*/", $this->myCurrentPsqlSourceCodeLines[$key-1],
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
      echo sprintf( "Error: Unable to find the desgination type of the stored routine in file '%s'.",
                    $this->myCurrentPsqlFilename );
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Extracts the name of the stored routine and the stored routine type (i.e. procedure or function) and sets
      @c myCurrentRoutineType and @c myCurrentRoutineName. Returns @c true on success. Otherwise returns @c false.

      @todo Skip comments and string literals.
   */
  private function getCurrentName()
  {
    $ret = true;

    $n = preg_match( "/create\s+(procedure|function)\s+([a-zA-Z0-9_]+)/i", $this->myCurrentPsqlSourceCode, $matches );
    if ($n===false) set_assert_failed( 'Internal error.' );

    if ($n==1)
    {
      $this->myCurrentRoutineType = strtolower( $matches[1] );

      if ($this->myCurrentRoutineName!=$matches[2])
      {
        echo sprintf( "Error: Stored routine name '%s' does not match filename in file '%s'.\n",
                      $this->myCurrentRoutineName,
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
  /** Drops the current routine if it exists.
   */
  private function dropCurrentRoutine()
  {
    if (isset($this->myOldRoutines[$this->myCurrentRoutineName]))
    {
      $sql = sprintf( "drop %s if exists %s",
                      $this->myOldRoutines[$this->myCurrentRoutineName]['routine_type'],
                      $this->myCurrentRoutineName );

      \SET_DL::executeNone( $sql );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Add magic constants to constant list.
   */
  private function setMagicConsatnt()
  {
    $real_path  = realpath( $this->myCurrentPsqlFilename );

    $this->myCurrentReplace['__FILE__'] = "'".\SET_DL::realEscapeString( $real_path  )."'";

    $this->myCurrentReplace['__ROUTINE__'] =   "'".$this->myCurrentRoutineName."'";

    $this->myCurrentReplace['__DIR__'] = "'".\SET_DL::realEscapeString( dirname( $real_path  ) )."'";

  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Remove magic constant forom list.
   */
  private function unsetMagicConsatnt()
  {
    unset( $this->myCurrentReplace['__FILE__'] );
    unset( $this->myCurrentReplace['__ROUTINE__'] );
    unset( $this->myCurrentReplace['__DIR__'] );
    unset( $this->myCurrentReplace['__LINE__'] );
  }

  //--------------------------------------------------------------------------------------------------------------------
  private function loadCurrentPsqlFile()
  {
    echo sprintf( "Loading %s %s\n",
                  $this->myCurrentRoutineType,
                  $this->myCurrentRoutineName );

    $this->setMagicConsatnt();

    $lines = explode( "\n", $this->myCurrentPsqlSourceCode );
    foreach( $lines as $i => &$line )
    {
      $this->myCurrentReplace['__LINE__']  = $i+1;
      $sql_source[$i] = strtr( $line, $this->myCurrentReplace );
    }

    $sql_source = implode( "\n", $sql_source );

    $this->unsetMagicConsatnt();

    // Drop the stored procedure or function if its exists.
    $this->dropCurrentRoutine();

    // Set the SQL-mode under which the stored routine will run.
    $sql = sprintf( "set sql_mode ='%s'", $this->mySqlMode );
    \SET_DL::executeNone( $sql );

    // Set the default charaacter set and collate under which the store routine will run.
    $sql = sprintf( "set names '%s' COLLATE '%s'", $this->myCharacterSet, $this->myCollate );
    \SET_DL::executeNone( $sql );

    // Load the stored routine into MySQL.
    \SET_DL::executeNone( $sql_source );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Returns @c true if the current .psql file must be load or reloaded. Otherwise returns @c false.
   */
  private function getCurrentMustReload()
  {
    // If this is the first time we see the current .psql file is must be loaded.
    if (!isset($this->myCurrentOldMetadata)) return true;

    // If the .psql has changed the current .psql file is must be loaded.
    if ($this->myCurrentOldMetadata['timestamp']!=$this->myCurrentMtime) return true;

    // Get the old replace pairs
    $old_replace_pairs = unserialize( $this->myCurrentOldMetadata['replace'] );
    if ($old_replace_pairs===false)
    {
      set_assert_failed( "Unable to unserialize replace pairs for stored routine '%s'.", $this->myCurrentRoutineName );
    }

    // If the value of placeholder has changed the current .psql file is must be loaded.
    foreach( $old_replace_pairs as $place_holder => $old_value )
    {
      if (!isset($this->myReplacePairs[strtoupper( $place_holder )]) ||
           $this->myReplacePairs[strtoupper( $place_holder )]!==$old_value) return true;
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
  /** Loads the stored routine in file @c myCurrentPsqlFilename into MySQL.
      Returns @c true on success, @c false otherwise.
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
    $this->myCurrentMtime               = null;
    $this->myCurrentReplace             = array();

    try
    {
      // We assume that the basename of the .psql file and routine name are equal.
      $this->myCurrentRoutineName = basename( $this->myCurrentPsqlFilename, '.psql' );

      // Save old metadata.
      $this->myCurrentOldMetadata = (isset($this->myMetadata[$this->myCurrentRoutineName])) ?
                                    $this->myMetadata[$this->myCurrentRoutineName] : null;

      // Get mtime of the source file.
      $this->myCurrentMtime = filemtime( $this->myCurrentPsqlFilename );
      if ($this->myCurrentMtime===false) set_assert_failed( "Unable to get mtime of file '%s'.",
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

        // Update curent Metadata;
        $this->updateCurentMetadata();
      }

      return true;
    }
    catch (Exception $e)
    {
      echo $e->getMessage();

      $this->myErrorFilenames[] = $this->myCurrentPsqlFilename;

      return false;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Selects schema, table, and colum names and the column type from the MySQL and the column type placeholders
      to @c myReplacePairs.
   */
  private function getColumnTypes()
  {
    $query = file_get_contents( $this->mySqlColumnTypeFilename );
    if ($query===false) set_assert_failed( "Unable to read file '%s'.", $this->mySqlColumnTypeFilename );

    $rows = \SET_DL::executeRows( $query );

    foreach( $rows as $row )
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
  /** Update metadata for current .psql if it exist, otherwise drop current metadata.
   */
  private function updateCurentMetadata()
  {
    $query  = sprintf( "
select group_concat( t2.data_type order by t2.ordinal_position separator ',' ) 'arguments'
from            information_schema.ROUTINES   t1
left outer join information_schema.PARAMETERS t2  on  t2.specific_schema = t1.routine_schema and
                                                      t2.specific_name   = t1.routine_name
where t1.routine_schema = database()
and   t1.routine_name   = '%s'", $this->myCurrentRoutineName );

    $tmp = \SET_DL::executeRows( $query );  /** @todo replace with execute singleton */
    $argument_types = $tmp[0]['arguments'];

    $this->myMetadata[$this->myCurrentRoutineName]['routine_name']   = $this->myCurrentRoutineName;
    $this->myMetadata[$this->myCurrentRoutineName]['type']           = $this->myCurrentType;
    $this->myMetadata[$this->myCurrentRoutineName]['argument_types'] = $argument_types;
    $this->myMetadata[$this->myCurrentRoutineName]['columns']        = $this->myCurrentColumns;
    $this->myMetadata[$this->myCurrentRoutineName]['timestamp']      = $this->myCurrentMtime;
    $this->myMetadata[$this->myCurrentRoutineName]['replace']        = serialize( $this->myCurrentReplace );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Remove obsolete entries from metadata.
   */
  private function removeObsoleteMetadata()
  {
    foreach ( $this->myPsqlFilenames as $myPsqlFilename )
    {
      $tmp = basename( $myPsqlFilename, '.psql' );
      if (isset($this->myMetadata[$tmp])) $clen[$tmp] = $this->myMetadata[$tmp];
    }
    $this->myMetadata = $clen;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Get information about all stored routinesin MySQL.
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

    $rows = \SET_DL::executeRows( $query );

    $this->myOldRoutines = array();
    foreach( $rows as $row )
    {
      $this->myOldRoutines[$row['routine_name']] = $row;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Gets real sql mode.
   */
  private function getCorrectSqlMode()
  {
    $sql = sprintf( "set sql_mode ='%s'", $this->mySqlMode );
    \SET_DL::executeNone( $sql );

    $query = "select @@sql_mode;";
    $tmp = \SET_DL::executeRows( $query );
    $this->mySqlMode = $tmp[0]['@@sql_mode'];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Drop obsolete routines (i.e. routines that exits in MySQL but for which we don't have a source file).
   */
  private function dropObsoleteRoutines()
  {
    foreach( $this->myOldRoutines as $old_routine )
    {
      if (!isset($this->myPsqlFilenames[$old_routine['routine_name']]))
      {
        echo sprintf( "Dropping %s %s\n",
                      strtolower( $old_routine['routine_type'] ),
                      $old_routine['routine_name'] );

        $sql = sprintf( "drop %s if exists %s", $old_routine['routine_type'], $old_routine['routine_name'] );
        \SET_DL::executeNone( $sql );
      }
    }
  }


  //--------------------------------------------------------------------------------------------------------------------
  /** Get all .psql into $this->myPsqlFilenames file from list.
   */
  private function getPsqlFilename( $theFilenames )
  {
    foreach( $theFilenames as $psql_filename )
    {
      if (file_exists( $psql_filename ))
      {
        $base_name = basename( $psql_filename, '.psql' );
        if (!isset($this->myPsqlFilenames[$base_name]))
        {
          $this->myPsqlFilenames[$base_name] = $psql_filename;
        }
        else
        {
          echo sprintf( "Error: Files '%s' and '%s' have the same basename.\n",
                        $this->myPsqlFilenames[$base_name],
                        $psql_filename );
          $this->myErrorFilenames[] = $psql_filename;
        }
      }
      else
      {
        echo sprintf( " Not correct set name file or file not exists: '%s'.\n", $psql_filename);
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Get all .psql file from source directory and processed their.
   */
  private function loadAll( $theConfigFilename )
  {
    $this->readConfigFile( $theConfigFilename );

    \SET_DL::connect( $this->myHostName, $this->myUserName, $this->myPassword, $this->myDatabase );

    $this->findPsqlFiles();
    $this->getColumnTypes();
    $this->readRoutineMetaData();
    $this->getConstants();
    $this->getOldRoutines();
    $this->getCorrectSqlMode();

    foreach( $this->myPsqlFilenames as $this->myCurrentPsqlFilename )
    {
      $err = $this->loadPsqlFile();
      if ($err===false)
      {
        $this->myErrorFilenames = $this->myCurrentPsqlFilename;
        unset($this->myMetadata[$this->myCurrentRoutineName]);
      }
    }

    // Drop obsolete routines.
    $this->dropObsoleteRoutines();

    // Remove metadata of store routines that have been removed.
    $this->removeObsoleteMetadata();

    // Write the metadata to @c $myMetadataFilename.
    $this->writeRoutineMetadata();

    \SET_DL::disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Get all .psql file from list set in command line and processed their.
   */
  private function loadList( $theConfigFilename, $theFilenames )
  {
    $this->readConfigFile( $theConfigFilename );

    \SET_DL::connect( $this->myHostName, $this->myUserName, $this->myPassword, $this->myDatabase );

    $this->getPsqlFilename( $theFilenames );
    $this->getColumnTypes();
    $this->readRoutineMetaData();
    $this->getConstants();
    $this->getOldRoutines();
    $this->getCorrectSqlMode();

    foreach( $this->myPsqlFilenames as $this->myCurrentPsqlFilename )
    {
      $err = $this->loadPsqlFile();
      if ($err===false)
      {
        $this->myErrorFilenames = $this->myCurrentPsqlFilename;
        unset($this->myMetadata[$this->myCurrentRoutineName]);
      }
    }

    // Write the metadata to @c $myMetadataFilename.
    $this->writeRoutineMetadata();

    \SET_DL::disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function run( $theConfigFilename, $theFilenames )
  {
    if (empty($theFilenames))
    {
      $this->loadAll( $theConfigFilename );
    }
    else
    {
      $this->loadList( $theConfigFilename, $theFilenames );
    }

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

