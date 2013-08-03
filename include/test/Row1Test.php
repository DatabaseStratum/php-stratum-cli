<?php
//----------------------------------------------------------------------------------------------------------------------
class Row1Test extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    TST_DL::Connect( 'localhost', 'test', 'test', 'test' );  
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type row1 must return 1 row and 1 row only.
   */
  public function testSelect1Rows()
  {
    $ret = TST_DL::TestRow1a( 1 );      
    $this->assertInternalType( 'array', $ret );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** An exception must be thrown when a stored routine with designation type row1 returns 0 rows.
   *  @expectedException Exception
   */ 
  public function testSelect0Rows()
  {
    TST_DL::TestRow1a( 0 ); 
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** An exception must be thrown when a stored routine with designation type row1 returns more than 1 rows.
   *  @expectedException Exception
   */
  public function testSelect2Rows()
  {
    TST_DL::TestRow1a( 2 ); 
  }
  
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
