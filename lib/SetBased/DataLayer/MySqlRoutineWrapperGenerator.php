<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer;

//----------------------------------------------------------------------------------------------------------------------
/** @brief Klasse voor een programma voor het aanmaken aan een klasse met wrappers functies voor Stored Routines in het
 * SET schema.
 */
class  MySqlRoutineWrapperGenerator
{
  /** Processed code function.
   */
  private $myCode = '';

  /** The filename of the template.
   */
  private $myTemplateFilename;

  /** The filename where the generated wrapper class must be stored
   */
  private $myWrapperFilename;

  /** The filename of the file with the metadata of all stored procedures.
   */
  private $myMetadataFilename;

  /** The filename of the configuration file.
   */
  private $myConfigurationFilename;

  /** Host name or address.
   */
  private $myHostName;

  /** User name.
   */
  private $myUserName;

  /** User password.
   */
  private $myPassword;

  /** Name used database.
   */
  private $myDatabase;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Construction class for stored routine wrapper.
   *
   * @param $theConfigurationFilename string The path file configuration.
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

    $replace['  /* AUTO_GENERATED_ROUINE_WRAPPERS */'] = $this->myCode;

    $code = file_get_contents( $this->myTemplateFilename );
    if ($code===false) set_assert_failed( "Error reading file %s", $this->myTemplateFilename );

    $code = strtr( $code, $replace );

    $bytes = file_put_contents( $this->myWrapperFilename, $code );
    if ($bytes===false) set_assert_failed( "Error writing file %s", $this->myWrapperFilename );

    \SET_DL::disconnect();

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Read parameters from the configuration file @c $myConfigurationFilename.
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
  /*
   * Getting parameters from a array @a $theSettings whit key @c $theSectionName and @c $theSettingName.
   *
   * @param $theSettings array
   * @param $theSectionName string
   * @param $theSettingName string
   *
   * @return mixed
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
   * Returns the metadata @a $routines of stored routines stored in file.
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

      $routines[$line_number]['routine_name'] = $row[0];
      $routines[$line_number]['type']         = $row[1];
      $routines[$line_number]['argument_names'] = ($row[2])? explode( ',', $row[2] ) : array();
      $routines[$line_number]['argument_types'] = ($row[3])? explode( ',', $row[3] ) : array();
      $routines[$line_number]['columns']        = ($row[4])? explode( ',', $row[4] ) : array();
    }

    $err = fclose( $handle );
    if ($err===false) set_assert_failed( "Error closing file '%s'.", $theFilename );

    return $routines;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates a complete wrapper method for a Stored Routine.
   *
   * @param $theRoutine array The row from table DEV_ROUTINE.
   */
  private function writeRoutineFunction( $theRoutine )
  {
    $wrapper = MySqlRoutineWrapper::createRoutineWrapper( $theRoutine );
    $this->myCode .= $wrapper->writeRoutineFunction( $theRoutine );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

