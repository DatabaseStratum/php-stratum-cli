<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Application;

use SetBased\Stratum\Command\CrudCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

//----------------------------------------------------------------------------------------------------------------------
/**
 * The PhpStratum program.
 */
class Crud extends Application
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
    return 'crud';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Gets the default commands that should always be available.
   *
   * @return Command[] An array of default Command instances
   */
  protected function getDefaultCommands()
  {
    // Keep the core default commands to have the HelpCommand which is used when using the --help option
    $defaultCommands = parent::getDefaultCommands();

    $defaultCommands[] = new CrudCommand();

    return $defaultCommands;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
