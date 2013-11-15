<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating a  class with wrappers for stored routines.
 * @package SetBased\DataLayer
 */
class  MySqlRoutineWrapperGenerator
{
  /**
   * Place holder in the template file that will be replaced with the generated routine wrappers.
   */
  const C_PLACEHOLDER = '  /* AUTO_GENERATED_ROUTINE_WRAPPERS */';

  /**
   * @var string .The generated PHP code.
   */
  private $myCode = '';

  /**
   * @var string The filename of the template.
   */
  private $myTemplateFilename;

  /**
   * @var string The filename where the generated wrapper class must be stored
   */
  private $myWrapperFilename;

  /**
   * @var string The filename of the file with the metadata of all stored procedures.
   */
  private $myMetadataFilename;

  /**
   * @var string The filename of the configuration file.
   */
  private $myConfigurationFilename;

  /**
   * @var string Host name or address.
   */
  private $myHostName;

  /**
   * @var string user name.
   */
  private $myUserName;

  /**
   * @var string User password.
   */
  private $myPassword;

  /**
   * @var string The schema name.
   */
  private $myDatabase;

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

    \SET_DL::connect( $this->myHostName, $this->myUserName, $this->myPassword, $this->myDatabase );

    $routines = $this->readRoutineMetaData();

    foreach ($routines as $routine)
    {
      // If routine type is hidden don't create routine wrapper.
      if ($routine['type']!='hidden')
      {
        $this->writeRoutineFunction( $routine );
      }
    }

    $replace[self::C_PLACEHOLDER] = $this->myCode;


    $code = file_get_contents( $this->myTemplateFilename );
    if ($code===false) set_assert_failed( "Error reading file %s", $this->myTemplateFilename );

    $count_match = substr_count( $code, self::C_PLACEHOLDER );
    if ($count_match!=1)
    {
      set_assert_failed( "Error expected 1 placeholder in file '%s', found %d.", $this->myTemplateFilename, $count_match );
    }

    $code = strtr( $code, $replace );

    $write_wrapper_file_flag = true;
    if (file_exists( $this->myWrapperFilename ))
    {
      $old_code = file_get_contents( $this->myWrapperFilename );
      if ($code==$old_code) $write_wrapper_file_flag = false;
    }

    if ($write_wrapper_file_flag)
    {
      $bytes = file_put_contents( $this->myWrapperFilename, $code );
      if ($bytes===false) set_assert_failed( "Error writing file %s", $this->myWrapperFilename );
      echo "Created : '", $this->myWrapperFilename, "'.\n";
    }

    \SET_DL::disconnect();

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads parameters from the configuration file @c $myConfigurationFilename.
   */
  private function readConfigurationFile()
  {
    $settings = parse_ini_file( $this->myConfigurationFilename, true );
    if ($settings===false) set_assert_failed( "Unable open configuration file '%s'", $this->myConfigurationFilename );

    $this->myHostName = $this->getSetting( $settings, 'database', 'host_name' );
    $this->myUserName = $this->getSetting( $settings, 'database', 'user_name' );
    $this->myPassword = $this->getSetting( $settings, 'database', 'password' );
    $this->myDatabase = $this->getSetting( $settings, 'database', 'database_name' );

    $this->myTemplateFilename = $this->getSetting( $settings, 'wrapper', 'template' );
    $this->myWrapperFilename  = $this->getSetting( $settings, 'wrapper', 'wrapper' );
    $this->myMetadataFilename = $this->getSetting( $settings, 'wrapper', 'metadata' );
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
   * Returns the metadata of stored routines stored in the metadata file $myMetadataFilename.
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
    if (!feof( $handle )) set_assert_failed( 'Did not reach eof of %s', $theFilename );

    $err = fclose( $handle );
    if ($err===false) set_assert_failed( "Error closing file '%s'.", $theFilename );

    return $routines;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates a complete wrapper method for a stored routine.
   *
   * @param $theRoutine array The metadata of the stored routine.
   */
  private function writeRoutineFunction( $theRoutine )
  {
    $wrapper = MySqlRoutineWrapper::createRoutineWrapper( $theRoutine );
    $this->myCode .= $wrapper->writeRoutineFunction( $theRoutine );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
