<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql;

use SetBased\Stratum\MySql\Wrapper\StaticDataLayer as DataLayer;
use SetBased\Stratum\Util;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for connecting to SQL Server instances and reading SQl Server specific connection parameters from
 * configuration files.
 */
class MySqlConnector
{

  /**
   * Host name or address.
   *
   * @var string
   */
  protected $myHostName;

  /**
   * Name used database.
   *
   * @var string
   */
  protected $myDatabase;

  /**
   * @var string User name.
   */
  protected $myUserName;

  /**
   * User password.
   *
   * @var string
   */
  protected $myPassword;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   *  Connects to the database.
   */
  protected function connect()
  {
    DataLayer::connect( $this->myHostName,
                        $this->myUserName,
                        $this->myPassword,
                        $this->myDatabase );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   *  Disconnects from the database.
   */
  protected function disconnect()
  {
    DataLayer::disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads configuration parameters from the configuration file.
   *
   * @param string $theConfigFilename
   */
  protected function readConfigFile( $theConfigFilename )
  {
    $settings = parse_ini_file( $theConfigFilename, true );

    $this->myHostName = Util::getSetting( $settings, true, 'database', 'host_name' );
    $this->myUserName = Util::getSetting( $settings, true, 'database', 'user_name' );
    $this->myPassword = Util::getSetting( $settings, true, 'database', 'password' );
    $this->myDatabase = Util::getSetting( $settings, true, 'database', 'database_name' );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
