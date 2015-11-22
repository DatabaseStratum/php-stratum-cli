<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * phpStratum
 *
 * @copyright 2005-2015 Paul Water / Set Based IT Consultancy (https://www.setbased.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link
 */
//----------------------------------------------------------------------------------------------------------------------
class LogTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type none must return the number of rows affected.
   */
  public function test1()
  {
    $n = DataLayer::testLog();

    $this->expectOutputRegex('/^(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}\sHello, world\n){2}$/');
    $this->assertEquals(2, $n);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
