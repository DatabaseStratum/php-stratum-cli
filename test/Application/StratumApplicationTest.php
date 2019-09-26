<?php
declare(strict_types=1);

namespace SetBased\Stratum\Frontend\Test\Application;

use PHPUnit\Framework\TestCase;
use SetBased\Stratum\Frontend\Application\Stratum;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Test cases for the stratum application.
 */
class StratumApplicationTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  public function testExecute(): void
  {
    $application = new Stratum();
    $application->setAutoExit(false);

    $tester = new ApplicationTester($application);
    $tester->run(['command'     => 'stratum',
                  'config file' => 'test/etc/null.ini']);

    self::assertSame(-1, $tester->getStatusCode(), $tester->getDisplay());
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
