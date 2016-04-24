<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum;

use SetBased\Stratum\Command\MySql\ConstantsCommand;
use SetBased\Stratum\Command\MySql\RoutineLoaderCommand;
use SetBased\Stratum\Command\MySql\RoutineWrapperGeneratorCommand;
use SetBased\Stratum\Command\StratumCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

//----------------------------------------------------------------------------------------------------------------------
/**
 * The PhpStratum program.
 */
class Stratum extends Application
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Overridden so that the application doesn't expect the command
   * name to be the first argument.
   */
  public function getDefinition()
  {
    $inputDefinition = parent::getDefinition();
    // clear out the normal first argument, which is the command name
    $inputDefinition->setArguments();

    return $inputDefinition;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function getCommandName(InputInterface $input)
  {
    return 'stratum';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Gets the default commands that should always be available.
   *
   * @return array An array of default Command instances
   */
  protected function getDefaultCommands()
  {
    // Keep the core default commands to have the HelpCommand which is used when using the --help option
    $defaultCommands = parent::getDefaultCommands();

    $defaultCommands[] = new ConstantsCommand();
    $defaultCommands[] = new RoutineLoaderCommand();
    $defaultCommands[] = new StratumCommand();
    $defaultCommands[] = new RoutineWrapperGeneratorCommand();

    return $defaultCommands;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
