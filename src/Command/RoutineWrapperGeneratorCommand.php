<?php
declare(strict_types=1);

namespace SetBased\Stratum\Command;

use SetBased\Stratum\StratumStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for generating a class with wrapper methods for calling stored routines in the database.
 */
class RoutineWrapperGeneratorCommand extends BaseCommand
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function configure()
  {
    $this->setName('wrapper')
         ->setDescription('Generates a class with wrapper methods for calling stored routines')
         ->addArgument('config file', InputArgument::REQUIRED, 'The stratum configuration file');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->createStyle($input, $output);
    $this->readConfigFile($input);

    $factory = $this->createBackendFactory();
    $worker  = $factory->createRoutineWrapperGeneratorWorker($this->config, $this->io);

    $this->io->title('Wrapper');

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
