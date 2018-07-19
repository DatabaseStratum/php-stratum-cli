<?php
declare(strict_types=1);

namespace SetBased\Stratum\Command;

use SetBased\Stratum\Style\StratumStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The PhpStratum command.
 */
class StratumCommand extends Command
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
    $this->io = new StratumStyle($input, $output);

    $command = $this->getApplication()->find('constants');
    $ret     = $command->execute($input, $output);
    if ($ret!=0) return $ret;

    $command = $this->getApplication()->find('loader');
    $ret     = $command->execute($input, $output);
    if ($ret!=0) return $ret;

    $command = $this->getApplication()->find('wrapper');
    $ret     = $command->execute($input, $output);

    $this->io->writeln('');

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
