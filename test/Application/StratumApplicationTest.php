<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\Application;

use SetBased\Stratum\Application\Stratum;
use Symfony\Component\Console\Tester\ApplicationTester;

//----------------------------------------------------------------------------------------------------------------------
class StratumApplicationTest extends \PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  public function testExecute()
  {
    $application = new Stratum();
    $application->setAutoExit(false);

    $tester = new ApplicationTester($application);
    $tester->run(['command'     => 'stratum',
                  'config file' => 'test/MySql/etc/stratum.cfg']);

    $this->assertSame(0, $tester->getStatusCode(), $tester->getDisplay());
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
