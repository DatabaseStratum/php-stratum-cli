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
class MaxAllowedPacketTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate test for the data Lob type different size.
   */
  public function crc32WithStoredRoutine( $theSize )
  {
    $data  = '';
    $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ1234567890';
    for ($i = 0; $i<=1024; $i++)
    {
      $data .= substr( $chars, rand( 0, strlen( $chars ) ), 1 );
    }
    $data = substr( str_repeat( $data, $theSize / 1024 + 1024 ), 0, $theSize );

    $crc32_php = sprintf( "%u", crc32( $data ) );

    $crc32_db = DataLayer::testMaxAllowedPacket( $data );

    $this->assertEquals( $crc32_php, $crc32_db );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Calling a stored routine with a BLOB less than max_allowed_packet must not be a problem.
   */
  public function test1()
  {
    $this->crc32WithStoredRoutine( 0.5 * DataLayer::getMaxAllowedPacket() );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Calling a stored routine with a BLOB of max_allowed_packet bytes must not be a problem.
   */
  public function test2()
  {
    $this->crc32WithStoredRoutine( DataLayer::getMaxAllowedPacket() );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Calling a stored routine with a BLOB larger than max_allowed_packet bytes is not possible.
   *
   * @expectedException Exception
   */
  public function xtest3()
  {
    $this->crc32WithStoredRoutine( 1.05 * DataLayer::getMaxAllowedPacket()  );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Calling a stored routine with a BLOB of larger than max_allowed_packet bytes is not possible.
   *
   * @expectedException Exception
   */
  public function test4()
  {
    $this->crc32WithStoredRoutine( 2 * DataLayer::getMaxAllowedPacket() );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

