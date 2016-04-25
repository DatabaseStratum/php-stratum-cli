<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql\Command;

use SetBased\Stratum\Command\BaseCommand;
use SetBased\Stratum\MySql\MetadataDataLayer;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Base class for commands which needs to connect to a MySQL instance.
 */
class MySqlCommand extends BaseCommand
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Disconnects from MySQL instance.
   */
  public function disconnect()
  {
    MetadataDataLayer::disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Connects to a MySQL instance.
   *
   * @param array $settings The settings from the configuration file.
   */
  protected function connect($settings)
  {
    $host     = $this->getSetting($settings, true, 'database', 'host');
    $user     = $this->getSetting($settings, true, 'database', 'user');
    $password = $this->getSetting($settings, true, 'database', 'password');
    $database = $this->getSetting($settings, true, 'database', 'database');

    MetadataDataLayer::setIo($this->io);
    MetadataDataLayer::connect($host, $user, $password, $database);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
