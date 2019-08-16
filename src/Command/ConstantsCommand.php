<?php
declare(strict_types=1);

namespace SetBased\Stratum\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for creating PHP constants based on column widths, auto increment columns and labels.
 */
class ConstantsCommand extends BaseCommand
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function configure()
  {
    $this->setName('constants')
         ->setDescription('Generates constants based on database IDs')
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
    $worker  = $factory->createConstantWorker($this->config, $this->io);

    if ($worker===null)
    {
      $this->io->title('Constants');
      $this->io->error('This command is not implemented by the backend');

      return -1;
    }

    return $worker->execute();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
