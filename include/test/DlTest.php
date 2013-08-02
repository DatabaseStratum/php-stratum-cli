<?php
//----------------------------------------------------------------------------------------------------------------------
//require_once( '../etc/test_config.php' );
const TST_SQL_MODE = 'STRICT_ALL_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_AUTO_VALUE_ON_ZERO,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ONLY_FULL_GROUP_BY';

require_once( 'test_dl.php' );

//----------------------------------------------------------------------------------------------------------------------
class DlTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    TST_DL::Connect( 'localhost', 'test', 'test', 'test' );  
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** @name ValidTests
   */
  //@{
  //--------------------------------------------------------------------------------------------------------------------
  /** TestNone
   */
  public function testValid1()
  {
    $ret = TST_DL::TestNone( 1 );
    
    $this->assertEquals( 0, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** TestRow0a 0 valid
   */
  public function testRow0a()
  {
    $ret = TST_DL::TestRow0a( 0 );   
    $this->assertInternalType( 'null', $ret );
    
    $ret = TST_DL::TestRow0a( 1 );      
    $this->assertInternalType( 'array', $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** TestRow0a 1 valid
   */
  public function testValid3()
  {
   
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** TestSingleton0a 0 valid
   *
  public function testValid4()
  {
    $theArg = '0';
    $datalayer = $this->SetupForm1();
    $ret = TST_DL::TestSingleton0a( $theArg );
    
    $datalayer::Disconnect();
    
    $this->assertNull( $ret );
  }
 
  //--------------------------------------------------------------------------------------------------------------------
  /** TestSingleton0a 1 valid
   *
  public function testValid5()
  {
    $theArg = '1';
    $datalayer = $this->SetupForm1();
    $ret = TST_DL::TestSingleton0a( $theArg );
    
    $datalayer::Disconnect();
    
    $this->assertNotNull( $ret );
    
  }
   */
  //--------------------------------------------------------------------------------------------------------------------
  //@}

  /** @name Invalid Tests
   */
  //@{
  //--------------------------------------------------------------------------------------------------------------------
  /** TestRow0a 2 invalid
   */
  public function testInvalid1()
  {

  }
  //--------------------------------------------------------------------------------------------------------------------
  /** TestSingleton0a 2 invalid
   *
  public function testInvalid2()
  {
    $theArg = '2';
    $datalayer = $this->SetupForm1();
    $ret = TST_DL::TestSingleton0a( $theArg );
    
    $datalayer::Disconnect();
    
    $this->assertNull( $ret );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  //@}
  */
  //--------------------------------------------------------------------------------------------------------------------
}