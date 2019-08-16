<?php
declare(strict_types=1);

namespace SetBased\Stratum\Command;

use SetBased\Stratum\Backend;
use SetBased\Stratum\Helper\StratumConfig;
use SetBased\Stratum\StratumStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base command for other commands of PhpStratum.
 */
class BaseCommand extends Command
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The configuration object.
   *
   * @var StratumConfig
   */
  protected $config;

  /**
   * The Output decorator.
   *
   * @var StratumStyle
   */
  protected $io;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the Backend Factory as specified in the PhpStratum configuration file.
   *
   * @return Backend
   */
  protected function createBackendFactory(): Backend
  {
    $class = $this->config->manString('stratum.backend');

    return new $class();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates the PhpStratum Styl object.
   *
   * @param InputInterface  $input  The input object.
   * @param OutputInterface $output The output object.
   */
  protected function createStyle(InputInterface $input, OutputInterface $output): void
  {
    $this->io = new StratumStyle($input, $output);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads the PhpStratum configuration file.
   *
   * @param InputInterface $input The input object.
   */
  protected function readConfigFile(InputInterface $input): void
  {
    $this->config = new StratumConfig($input->getArgument('config file'));
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
