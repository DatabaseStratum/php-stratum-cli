<?php
declare(strict_types=1);

namespace SetBased\Stratum\Frontend\Command;

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
  protected function configure(): void
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
  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $this->createStyle($input, $output);
    $this->readConfigFile($input);

    $factory = $this->createBackendFactory();
    $worker  = $factory->createRoutineLoaderWorker($this->config, $this->io);
    $sources = $input->getArgument('sources');

    if ($worker===null)
    {
      $this->io->title('Loader');
      $this->io->error('Loader command is not implemented by the backend');

      return -1;
    }

    return $worker->execute($sources);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
