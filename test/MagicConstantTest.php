<?php
//----------------------------------------------------------------------------------------------------------------------
class MagicConstantTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    TST_DL::Connect( 'localhost', 'test', 'test', 'test' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Test constant __ROUTINE__. Must return name of routine.
   */
  public function test1()
  {
    $ret = TST_DL::MagicConstant01();
    $this->assertEquals( 'tst_magic_constant01', $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Test constant __LINE__. Must return line number in the source code.
   */
  public function test2()
  {
    $ret = TST_DL::MagicConstant02();
    $this->assertEquals( 7, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Test constant __FILE__. Must return the filename of the source of the routine.
   */
  public function test3()
  {
    $filename = realpath( __DIR__.'/../include/psql/test/tst_magic_constant03.psql' );

    $ret = TST_DL::MagicConstant03();
    $this->assertEquals( $filename, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Test constant __DIR__. Must return name of the folder where the source file of routine the is located.
   */
  public function test4()
  {
    $dir_name = realpath( __DIR__.'/../include/psql/test' );

    $ret = TST_DL::MagicConstant04();
    $this->assertEquals( $dir_name, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Test constant __DIR__ with several characters that need escaping.
   */
  public function test5()
  {
    $dir_name = realpath( __DIR__.'/../include/psql/test/ test_escape \' " @ $ ! .' );

    $ret = TST_DL::MagicConstant05();
    $this->assertEquals( $dir_name, $ret );
  }
}

//----------------------------------------------------------------------------------------------------------------------
