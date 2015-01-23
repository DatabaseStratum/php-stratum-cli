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
 * Class for loading stored routines into a MySQL instance from pseudo SQL files.
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
   * Name used database.
   *
   * @var string
   */
  private $myDatabase;

  /**
   * An array with source filenames that are not loaded into MySQL.
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
   * Old metadata about all stored routines.
   *
   * @var array
   */
  private $myOldStoredRoutinesInfo;

  /**
   * User password.
   *
   * @var string
   */
  private $myPassword;

  /**
   * A map from placeholders to their actual values.
   *
   * @var array
   */
  private $myReplacePairs = array();

  /**
   * Path where source files can be found.
   *
   * @var string
   */
  private $mySourceDirectory;

  /**
   * The extension of the source files.
   * @var string
   */
  private $mySourceFileExtension;

  /**
   * All found source files.
   *
   * @var array
   */
  private $mySourceFileNames = array();

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
   * @param string[] $theFileNames      The source filenames that must be loaded. If empty all sources (if required)
   *                                    will loaded.
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
   * Drops obsolete stored routines (i.e. stored routines that exits in the current schema but for which we don't have
   * a source file).
   */
  private function dropObsoleteRoutines()
  {
    foreach ($this->myOldStoredRoutinesInfo as $old_routine)
    {
      if (!isset($this->mySourceFileNames[$old_routine['routine_name']]))
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
   * Searches recursively for all source files in a directory.
   *
   * @param string $theSourceDir The directory.
   */
  private function findSourceFiles( $theSourceDir = null )
  {
    if ($theSourceDir===null) $theSourceDir = $this->mySourceDirectory;

    $psql_filenames = glob( $theSourceDir.'/*'.$this->mySourceFileExtension );
    foreach ($psql_filenames as $psql_filename)
    {
      $base_name = basename( $psql_filename, $this->mySourceFileExtension );
      if (!isset($this->mySourceFileNames[$base_name]))
      {
        $this->mySourceFileNames[$base_name] = $psql_filename;
      }
      else
      {
        echo sprintf( "Error: Files '%s' and '%s' have the same basename.\n",
                      $this->mySourceFileNames[$base_name],
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
        $this->findSourceFiles( $dir_name );
      }
    }
    else
    {
      echo sprintf( "Error: Directory '%s' not exist.\n", $theSourceDir );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Finds all source files that actually exists from a list of filenames.
   *
   * @param array $theFileNames The list of filenames.
   */
  private function findSourceFilesFromList( $theFileNames )
  {
    foreach ($theFileNames as $psql_filename)
    {
      if (file_exists( $psql_filename ))
      {
        $base_name = basename( $psql_filename, $this->mySourceFileExtension );
        if (!isset($this->mySourceFileNames[$base_name]))
        {
          $this->mySourceFileNames[$base_name] = $psql_filename;
        }
        else
        {
          echo sprintf( "Error: Files '%s' and '%s' have the same basename.\n",
                        $this->mySourceFileNames[$base_name],
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
   * Selects schema, table, column names and the column type from MySQL and saves them as replace pairs.
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
   * Retrieves information about all stored routines in the current schema.
   */
  private function getOldStoredRoutinesInfo()
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

    $this->myOldStoredRoutinesInfo = array();
    foreach ($rows as $row)
    {
      $this->myOldStoredRoutinesInfo[$row['routine_name']] = $row;
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

    $this->findSourceFiles();
    $this->getColumnTypes();
    $this->readStoredRoutineMetadata();
    $this->getConstants();
    $this->getOldStoredRoutinesInfo();
    $this->getCorrectSqlMode();

    $this->loadStoredRoutines();

    // Drop obsolete stored routines.
    $this->dropObsoleteRoutines();

    // Remove metadata of stored routines that have been removed.
    $this->removeObsoleteMetadata();

    // Write the metadata to file.
    $this->writeStoredRoutineMetadata();

    DataLayer::disconnect();
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

    $this->findSourceFilesFromList( $theFileNames );
    $this->getColumnTypes();
    $this->readStoredRoutineMetadata();
    $this->getConstants();
    $this->getOldStoredRoutinesInfo();
    $this->getCorrectSqlMode();

    $this->loadStoredRoutines();

    // Write the metadata to @c $myMetadataFilename.
    $this->writeStoredRoutineMetadata();

    DataLayer::disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads all stored routines.
   */
  private function loadStoredRoutines()
  {
    foreach ($this->mySourceFileNames as $filename)
    {
      $routine_name = basename( $filename, $this->mySourceFileExtension );

      $helper = new MySqlRoutineLoaderHelper( $filename,
                                              $this->mySourceFileExtension,
                                              isset($this->myMetadata[$routine_name]) ? $this->myMetadata[$routine_name] : null,
                                              $this->myReplacePairs,
                                              isset($this->myOldStoredRoutinesInfo[$routine_name]) ? $this->myOldStoredRoutinesInfo[$routine_name] : null,
                                              $this->mySqlMode,
                                              $this->myCharacterSet,
                                              $this->myCollate );

      $meta_data = $helper->loadStoredRoutine();
      if ($meta_data===false)
      {
        # An error occurred during the loading og the stored routine.
        $this->myErrorFileNames[] = $filename;
        unset($this->myMetadata[$routine_name]);
      }
      else
      {
        # Stored routine is successfully loaded.
        $this->myMetadata[$routine_name] = $meta_data;
      }
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
    $this->mySourceDirectory      = $this->getSetting( $settings, true, 'loader', 'source_directory' );
    $this->mySourceFileExtension  = $this->getSetting( $settings, true, 'loader', 'extension' );
    $this->myTargetConfigFilename = $this->getSetting( $settings, false, 'loader', 'config' );
    $this->mySqlMode              = $this->getSetting( $settings, true, 'loader', 'sql_mode' );
    $this->myCharacterSet         = $this->getSetting( $settings, true, 'loader', 'character_set' );
    $this->myCollate              = $this->getSetting( $settings, true, 'loader', 'collate' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads the metadata of stored routines from the metadata file.
   */
  private function readStoredRoutineMetadata()
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
   * Removes obsolete entries from the metadata of all stored routines.
   */
  private function removeObsoleteMetadata()
  {
    $clean = array();
    foreach ($this->mySourceFileNames as $myPsqlFilename)
    {
      $tmp = basename( $myPsqlFilename, $this->mySourceFileExtension );
      if (isset($this->myMetadata[$tmp])) $clean[$tmp] = $this->myMetadata[$tmp];
    }
    $this->myMetadata = $clean;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Writes the metadata of all stored routines to the metadata file.
   */
  private function writeStoredRoutineMetadata()
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
