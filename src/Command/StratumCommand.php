<?php
declare(strict_types=1);

namespace SetBased\Stratum\Command;

use SetBased\Stratum\StratumStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The stratum command: combination of constants, loader, and wrapper commands.
 */
class StratumCommand extends BaseCommand
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The output decorator
   *
   * @var StratumStyle
   */
  protected $io;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function configure()
  {
    $this->setName('stratum')
         ->setDescription('Runs the constants, loader, and wrapper commands')
         ->addArgument('config file', InputArgument::REQUIRED, 'The stratum configuration file')
         ->addArgument('sources', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Sources with stored routines');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes the actual PhpStratum program. Returns 0 is everything went fine. Otherwise, returns non-zero.
   *
   * @param InputInterface  $input  An InputInterface instance
   * @param OutputInterface $output An OutputInterface instance
   *
   * @return int
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $ret = -1;

    $this->createStyle($input, $output);
    $this->readConfigFile($input);

    $factory = $this->createBackendFactory();

    $worker = $factory->createConstantWorker($this->config, $this->io);
    if ($worker!==null)
    {
      $ret = $worker->execute();

      if ($ret!==0) return $ret;
    }

    $worker = $factory->createRoutineLoaderWorker($this->config, $this->io);
    if ($worker!==null)
    {
      $ret = $worker->execute();

      if ($ret!==0) return $ret;
    }

    $worker = $factory->createRoutineWrapperGeneratorWorker($this->config, $this->io);
    if ($worker!==null)
    {
      $ret = $worker->execute();

      if ($ret!==0) return $ret;
    }

    $this->io->writeln('');

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
