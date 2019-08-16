<?php
declare(strict_types=1);

namespace SetBased\Stratum\Command;

use SetBased\Stratum\StratumStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for loading stored routines into a MySQL instance from pseudo SQL files.
 */
class RoutineLoaderCommand extends BaseCommand
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function configure()
  {
    $this->setName('loader')
         ->setDescription('Generates the routine wrapper class')
         ->addArgument('config file', InputArgument::REQUIRED, 'The stratum configuration file')
         ->addArgument('sources', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Sources with stored routines');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->createStyle($input, $output);
    $this->readConfigFile($input);

    $this->io->title('Loader');

    $factory = $this->createBackendFactory();
    $worker  = $factory->createRoutineLoaderWorker($this->config, $this->io);

    if ($worker===null)
    {
      $this->io->error('This command is not implemented by the backend');

      return -1;
    }

    return $worker->execute();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
