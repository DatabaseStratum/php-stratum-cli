<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

//----------------------------------------------------------------------------------------------------------------------
class MagicConstantTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test constant __ROUTINE__. Must return name of routine.
   */
  public function test1()
  {
    $ret = TestDataLayer::tstMagicConstant01();
    $this->assertEquals('tst_magic_constant01', $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test constant __LINE__. Must return line number in the source code.
   */
  public function test2()
  {
    $ret = TestDataLayer::tstMagicConstant02();
    $this->assertEquals(8, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test constant __FILE__. Must return the filename of the source of the routine.
   */
  public function test3()
  {
    $filename = realpath(__DIR__.'/../../test/MySql/psql/tst_magic_constant03.psql');

    $ret = TestDataLayer::tstMagicConstant03();
    $this->assertEquals($filename, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test constant __DIR__. Must return name of the folder where the source file of routine the is located.
   */
  public function test4()
  {
    $dir_name = realpath(__DIR__.'/../../test/MySql/psql');

    $ret = TestDataLayer::tstMagicConstant04();
    $this->assertEquals($dir_name, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test constant __DIR__ with several characters that need escaping.
   */
  public function test5()
  {
    $dir_name = realpath(__DIR__.'/../../test/MySql/psql/ test_escape \' " @ $ ! .');

    if ($dir_name)
    {
      $ret = TestDataLayer::tstMagicConstant05();
      $this->assertEquals($dir_name, $ret);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
