<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Application;

use SetBased\Stratum\Command\CrudCommand;
use SetBased\Stratum\Command\NonStaticCommand;
use SetBased\Stratum\Command\StratumCommand;
use SetBased\Stratum\MySql\Command\ConstantsCommand;
use SetBased\Stratum\MySql\Command\RoutineLoaderCommand;
use SetBased\Stratum\MySql\Command\RoutineWrapperGeneratorCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

/**
 * The PhpStratum program.
 */
class Stratum extends Application
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   */
  public function __construct()
  {
    parent::__construct('stratum', '0.9.57');
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

    $defaultCommands[] = new ConstantsCommand();
    $defaultCommands[] = new CrudCommand();
    $defaultCommands[] = new NonStaticCommand();
    $defaultCommands[] = new RoutineLoaderCommand();
    $defaultCommands[] = new RoutineWrapperGeneratorCommand();
    $defaultCommands[] = new StratumCommand();

    return $defaultCommands;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
