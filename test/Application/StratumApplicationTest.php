<?php
declare(strict_types=1);

namespace SetBased\Stratum\Test\Application;

use PHPUnit\Framework\TestCase;
use SetBased\Stratum\Application\Stratum;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Test cases for the stratum application.
 */
class StratumApplicationTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  public function testExecute()
  {
    $application = new Stratum();
    $application->setAutoExit(false);

    $tester = new ApplicationTester($application);
    $tester->run(['command'     => 'stratum',
                  'config file' => 'test/MySql/etc/stratum.cfg']);

    self::assertSame(0, $tester->getStatusCode(), $tester->getDisplay());
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
