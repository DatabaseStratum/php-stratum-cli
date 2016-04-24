<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Command;

use SetBased\Stratum\Style\StratumStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//----------------------------------------------------------------------------------------------------------------------
class StratumCommand extends Command
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The output decorator
   *
   * @var StratumStyle
   */
  private $io;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this->setName('stratum')
         ->setDescription('Loads stored routines and generates a wrapper class')
         ->addArgument('config file', InputArgument::OPTIONAL, 'The audit configuration file')
         ->addArgument('sources', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Sources with stored routines');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
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