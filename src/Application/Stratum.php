<?php
declare(strict_types=1);

namespace SetBased\Stratum\Frontend\Application;

use SetBased\Stratum\Frontend\Command\ConstantsCommand;
use SetBased\Stratum\Frontend\Command\CrudCommand;
use SetBased\Stratum\Frontend\Command\RoutineLoaderCommand;
use SetBased\Stratum\Frontend\Command\RoutineWrapperGeneratorCommand;
use SetBased\Stratum\Frontend\Command\StratumCommand;
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
    parent::__construct('stratum', '6.3.0');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Gets the default commands that should always be available.
   *
   * @return Command[]
   */
  protected function getDefaultCommands(): array
  {
    // Keep the core default commands to have the HelpCommand which is used when using the --help option
    $defaultCommands = parent::getDefaultCommands();

    $defaultCommands[] = new ConstantsCommand();
    $defaultCommands[] = new CrudCommand();
    $defaultCommands[] = new RoutineLoaderCommand();
    $defaultCommands[] = new RoutineWrapperGeneratorCommand();
    $defaultCommands[] = new StratumCommand();

    return $defaultCommands;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
