<?php
declare(strict_types=1);

namespace SetBased\Stratum\Application;

use SetBased\Stratum\Command\ConstantsCommand;
use SetBased\Stratum\Command\CrudCommand;
use SetBased\Stratum\Command\NonStaticCommand;
use SetBased\Stratum\Command\RoutineLoaderCommand;
use SetBased\Stratum\Command\RoutineWrapperGeneratorCommand;
use SetBased\Stratum\Command\StratumCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

/**
 * The PhpStratum application.
 */
class Stratum extends Application
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   */
  public function __construct()
  {
    parent::__construct('stratum', '4.0.0');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Gets the default commands that should always be available.
   *
   * @return Command[]
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
