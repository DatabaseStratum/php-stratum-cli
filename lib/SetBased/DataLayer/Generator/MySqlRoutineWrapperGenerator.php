<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer\Generator;

use SetBased\DataLayer\Generator\MySqlRoutineWrapper;
use SetBased\DataLayer\StaticDataLayer as DataLayer;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating a  class with wrappers for stored routines.
 *
 * @package SetBased\DataLayer
 */
class MySqlRoutineWrapperGenerator
{
  /**
   * @var string The generated PHP code.
   */
  private $myCode = '';

  /**
   * @var string The filename of the configuration file.
   */
  private $myConfigurationFilename;

  /**
   * @var string The schema name.
   */
  private $myDatabase;

  /**
   * @var string Host name or address.
   */
  private $myHostName;

  /**
   * @var bool If true BLOBs and CLOBs must be treated as strings.
   */
  private $myLobAsStringFlag;

  /**
   * @var string The filename of the file with the metadata of all stored procedures.
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
   * @return int
   */
  public function run( $theConfigurationFilename )
  {
    $this->myConfigurationFilename = $theConfigurationFilename;

    $this->readConfigurationFile();

    DataLayer::connect( $this->myHostName, $this->myUserName, $this->myPassword, $this->myDatabase );

    $routines = $this->readRoutineMetaData();

    // Write the header of the wrapper class.
    $this->writeClassHeader();

    if (is_array( $routines ))
    {
      // Write methods for each stored routine.
      foreach ($routines as $routine)
      {
        // If routine type is hidden don't create routine wrapper.
        if ($routine['type']!='hidden')
        {
          $this->writeRoutineFunction( $routine );
        }
      }
    }
    else
    {
      echo "Stored routine files not found.\n";
    }


    // Write the trailer of the wrapper class.
    $this->writeClassTrailer();


    $write_wrapper_file_flag = true;
    if (file_exists( $this->myWrapperFilename ))
    {
      $old_code = file_get_contents( $this->myWrapperFilename );
      if ($old_code===false) set_assert_failed( "Unable to read file '%s'.", $this->myWrapperFilename );
      if ($this->myCode==$old_code) $write_wrapper_file_flag = false;
    }

    if ($write_wrapper_file_flag)
    {
      $bytes = file_put_contents( $this->myWrapperFilename, $this->myCode );
      if ($bytes===false) set_assert_failed( "Error writing file '%s'.", $this->myWrapperFilename );
      echo "Created: '", $this->myWrapperFilename, "'.\n";
    }

    DataLayer::disconnect();

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the value of a setting.
   *
   * @param $theSettings    array  The settings
   * @param $theSectionName string The section name of the requested setting.
   * @param $theSettingName string The name of the requested setting.
   *
   * @return string
   */
  private function getSetting( $theSettings, $theSectionName, $theSettingName )
  {
    // Test if the section exists.
    if (!array_key_exists( $theSectionName, $theSettings ))
    {
      set_assert_failed( "Section '%s' not found in configuration file '%s'.",
                         $theSectionName,
                         $this->myConfigurationFilename );
    }

    // Test if the setting in the section exists.
    if (!array_key_exists( $theSettingName, $theSettings[$theSectionName] ))
    {
      set_assert_failed( "Setting '%s' not found in section '%s' configuration file '%s'.",
                         $theSettingName,
                         $theSectionName,
                         $this->myConfigurationFilename );
    }

    return $theSettings[$theSectionName][$theSettingName];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads parameters from the configuration file @c $myConfigurationFilename.
   */
  private function readConfigurationFile()
  {
    // Read the configuration file.
    $settings = parse_ini_file( $this->myConfigurationFilename, true );
    if ($settings===false) set_assert_failed( "Unable open configuration file '%s'.", $this->myConfigurationFilename );

    // Set default values.
    if (!isset($theSettings['wrapper']['lob_as_string']))
    {
      $theSettings['wrapper']['lob_as_string'] = false;
    }

    $this->myHostName = $this->getSetting( $settings, 'database', 'host_name' );
    $this->myUserName = $this->getSetting( $settings, 'database', 'user_name' );
    $this->myPassword = $this->getSetting( $settings, 'database', 'password' );
    $this->myDatabase = $this->getSetting( $settings, 'database', 'database_name' );

    $this->myParentClassName  = $this->getSetting( $settings, 'wrapper', 'parent_class' );
    $this->myWrapperClassName = $this->getSetting( $settings, 'wrapper', 'wrapper_class' );
    $this->myWrapperFilename  = $this->getSetting( $settings, 'wrapper', 'wrapper_file' );
    $this->myMetadataFilename = $this->getSetting( $settings, 'wrapper', 'metadata' );
    $this->myLobAsStringFlag  = ($this->getSetting( $settings, 'wrapper', 'lob_as_string' )) ? true : false;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the metadata of stored routines stored in the metadata file $myMetadataFilename.
   *
   * @return array
   */
  private function readRoutineMetaData()
  {
    $theFilename = $this->myMetadataFilename;

    $handle = fopen( $theFilename, 'r' );
    if ($handle===null) set_assert_failed( "Unable to open file '%s'.", $theFilename );

    $routines = '';

    // Skip header row.
    fgetcsv( $handle, 0, ',' );
    $line_number = 1;

    while (($row = fgetcsv( $handle, 0, ',' ))!==false)
    {
      $line_number++;

      // Test the number of fields in the row.
      $n = sizeof( $row );
      if ($n!=7)
      {
        set_assert_failed( "Error at line %d in file '%s'. Expecting %d fields but found %d fields.",
                           $line_number,
                           $theFilename,
                           7,
                           $n );
      }

      $routines[$line_number]['routine_name']   = $row[0];
      $routines[$line_number]['type']           = $row[1];
      $routines[$line_number]['argument_names'] = ($row[2]) ? explode( ',', $row[2] ) : array();
      $routines[$line_number]['argument_types'] = ($row[3]) ? explode( ',', $row[3] ) : array();
      $routines[$line_number]['columns']        = ($row[4]) ? explode( ',', $row[4] ) : array();
    }
    if (!feof( $handle )) set_assert_failed( "Did not reach eof of '%s'.", $theFilename );

    $err = fclose( $handle );
    if ($err===false) set_assert_failed( "Error closing file '%s'.", $theFilename );

    return $routines;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate a class header for stored routines.
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
    $this->myCode .= '//'.str_repeat( '-', MySqlRoutineWrapper::C_PAGE_WIDTH - 2 )."\n";
    if ($namespace)
    {
      $this->myCode .= "namespace ${namespace};\n";
      $this->myCode .= "\n";
      $this->myCode .= '//'.str_repeat( '-', MySqlRoutineWrapper::C_PAGE_WIDTH - 2 )."\n";
    }
    $this->myCode .= 'class '.$class_name.' extends '.$this->myParentClassName."\n";
    $this->myCode .= "{\n";
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   *
   */
  private function writeClassTrailer()
  {
    $this->myCode .= '  //'.str_repeat( '-', MySqlRoutineWrapper::C_PAGE_WIDTH - 4 )."\n";
    $this->myCode .= "}\n";
    $this->myCode .= "\n";
    $this->myCode .= '//'.str_repeat( '-', MySqlRoutineWrapper::C_PAGE_WIDTH - 2 )."\n";
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates a complete wrapper method for a stored routine.
   *
   * @param $theRoutine array The metadata of the stored routine.
   */
  private function writeRoutineFunction( $theRoutine )
  {
    $wrapper = MySqlRoutineWrapper::createRoutineWrapper( $theRoutine, $this->myLobAsStringFlag );
    $this->myCode .= $wrapper->writeRoutineFunction( $theRoutine );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
