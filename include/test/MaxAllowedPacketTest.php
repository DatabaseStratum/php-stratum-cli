<?php
//----------------------------------------------------------------------------------------------------------------------
class MaxAllowedPacketTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    TST_DL::Connect( 'localhost', 'test', 'test', 'test' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Generete test for the data Lob type diferend size.
   */
  public function generic( $theSize )
  {
    $data  = '';
    $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ1234567890';
    for($i=0; $i<=1024; $i++)
    {
      $data .= substr( $chars, rand( 0, strlen($chars) ), 1 );
    }
    $data = substr( str_repeat( $data, $theSize/1024 + 1024 ), 0, $theSize );

    $crc32_php = sprintf( "%u", crc32( $data ) );

    $crc32_db = TST_DL::TestMaxAlllowedPacket( $data );

    $this->assertEquals( $crc32_php, $crc32_db );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Calling a stored routine with a BLOB less than max_allowed_packet must not be a poblem.
   */
  public function test1()
  {
    $this->generic( 0.5 * TST_DL::getMaxAllowedPacket() );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Calling a stored routine with a BLOB of max_allowed_packet bytes must not be a poblem.
   */
  public function test2()
  {
    $this->generic( TST_DL::getMaxAllowedPacket() );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Calling a stored routine with a BLOB larger than max_allowed_packet bytes is not possible.
   *  @expectedException Exception
   */
  public function test3()
  {
    $this->generic( TST_DL::getMaxAllowedPacket() + 1 );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Calling a stored routine with a BLOB of larger than max_allowed_packet bytes is not possible.
   *  @expectedException Exception
   */
  public function test4()
  {
    $this->generic( 2 * TST_DL::getMaxAllowedPacket() );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

