<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer;

//----------------------------------------------------------------------------------------------------------------------
/** @brief Klasse voor een programma voor het aanmaken aan een klasse met wrappers functies voor Stored Routines in het
    SET schema.
 */
class  MySqlRoutineWrapperGenerator
{
  /** Processed code function.
   */
  private $myCode = '' ;

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

  //--------------------------------------------------------------------------------------------------------------------
  /** Generates a complete wrapper method for a Stored Routine.
      @param $theRoutine The row from table DEV_ROUTINE.
   */
  private function writeRoutineFunction( $theRoutine )
  {
    $wrapper = MySqlRoutineWrapper::createRoutineWrapper( $theRoutine );

    $this->myCode .= $wrapper->writeRoutineFunction( $theRoutine );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Returns the metadata about all stored routines stored in @c myMetadataFilename.
   */
  private function readRoutineMetaData()
  {
    $theFilename = $this->myMetadataFilename;

    $handle = fopen( $theFilename, 'r' );
    if ($handle===null) set_assert_failed( "Unable to open file '%s'.", $theFilename );

    // Skip header row.
    fgetcsv( $handle, 0, ',' );
    $line_number = 1;

    while (($row = fgetcsv( $handle, 0, ',' ))!==false)
    {
      $line_number++;

      // Test the number of fields in the row.
      $n = sizeof( $row );
      if ($n!=6)
      {
        set_assert_failed( "Error at line %d in file '%s'. Expecting %d fields but found %d fields.",
                           $line_number,
                           $theFilename,
                           6,
                           $n );
      }

      $routines[] = array( 'routine_name'   => $row[0],
                           'type'           => $row[1],
                           'argument_types' => $row[2],
                           'columns'        => explode( ',', $row[3] ) );
    }
    if (!feof($handle)) etl_assert_failed('Did not reach eof of %s', $theFilename );

    $err = fclose( $handle );
    if ($err===false) set_assert_failed( "Error closing file '%s'.", $theFilename );

    return $routines;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Returns the value of a setting.
      @param $theSettings    The settings (as returned by parse_ini_file).
      @param $theSectionName The section name.
      @param $theSettingName The setting name.
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
  /** Read parameters from configuration file @c $myConfigurationFilename.
   */
  private function readConfigurationFile()
  {
    $settings = parse_ini_file( $this->myConfigurationFilename, true );
    if ($settings===false) set_assert_failed( "Unable open configuration file '%s'", $this->myConfigurationFilename );

    $this->myHostName = $this->getSetting( $settings, 'database', 'host_name');
    $this->myUserName = $this->getSetting( $settings, 'database', 'user_name');
    $this->myPassword = $this->getSetting( $settings, 'database', 'password');
    $this->myDatabase = $this->getSetting( $settings, 'database', 'database_name');

    $this->myTemplateFilename = $this->getSetting( $settings, 'wrapper', 'template' );
    $this->myWrapperFilename  = $this->getSetting( $settings, 'wrapper', 'wrapper'  );
    $this->myMetadataFilename = $this->getSetting( $settings, 'wrapper', 'metadata' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Creates the actual stored routine wrapper class.
      @param $ConfigurationFilename The name of configuration file.
   */
  public function run( $theConfigurationFilename )
  {
    $this->myConfigurationFilename = $theConfigurationFilename;

    $this->readConfigurationFile();

    \SET_DL::connect( $this->myHostName, $this->myUserName, $this->myPassword, $this->myDatabase );

    $routines = $this->readRoutineMetaData();

    foreach( $routines as $routine )
    {
      // If routine type is hidden don't create routine wrapper.
      if ($routine['type']!='hidden')
      {
        $this->writeRoutineFunction( $routine );
      }
    }

    $replace['  /* AUTO_GENERATED_ROUINE_WRAPPERS */'] =  $this->myCode;

    $code = file_get_contents( $this->myTemplateFilename );
    if ($code===false) set_assert_failed( "Error reading file %s", $this->myTemplateFilename );

    $code = strtr( $code, $replace );

    $bytes = file_put_contents( $this->myWrapperFilename, $code );
    if ($bytes===false) set_assert_failed( "Error writing file %s", $this->myWrapperFilename );

    \SET_DL::disconnect();

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

