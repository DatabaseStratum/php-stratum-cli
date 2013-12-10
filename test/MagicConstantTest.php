<?php
//----------------------------------------------------------------------------------------------------------------------
class MagicConstantTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    DataLayer::connect( 'localhost', 'test', 'test', 'test' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Test constant __ROUTINE__. Must return name of routine.
   */
  public function test1()
  {
    $ret = DataLayer::magicConstant01();
    $this->assertEquals( 'tst_magic_constant01', $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Test constant __LINE__. Must return line number in the source code.
   */
  public function test2()
  {
    $ret = DataLayer::magicConstant02();
    $this->assertEquals( 7, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Test constant __FILE__. Must return the filename of the source of the routine.
   */
  public function test3()
  {
    $filename = realpath( __DIR__.'/../include/psql/test/tst_magic_constant03.psql' );

    $ret = DataLayer::magicConstant03();
    $this->assertEquals( $filename, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Test constant __DIR__. Must return name of the folder where the source file of routine the is located.
   */
  public function test4()
  {
    $dir_name = realpath( __DIR__.'/../include/psql/test' );

    $ret = DataLayer::magicConstant04();
    $this->assertEquals( $dir_name, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Test constant __DIR__ with several characters that need escaping.
   */
  public function test5()
  {
    $dir_name = realpath( __DIR__.'/../include/psql/test/ test_escape \' " @ $ ! .' );

    $ret = DataLayer::magicConstant05();
    $this->assertEquals( $dir_name, $ret );
  }
}

//----------------------------------------------------------------------------------------------------------------------
