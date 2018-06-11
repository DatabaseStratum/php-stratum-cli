<?php

namespace SetBased\Stratum\Test\MySql;

use SetBased\Exception\RuntimeException;

/**
 * Test cases with max-allowed-packet.
 */
class MaxAllowedPacketTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate test for the data Lob type different size.
   */
  public function crc32WithStoredRoutine($size)
  {
    $data  = '';
    $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ1234567890';
    for ($i = 0; $i<=1024; $i++)
    {
      $data .= substr($chars, rand(0, strlen($chars)), 1);
    }
    $data = substr(str_repeat($data, $size / 1024 + 1024), 0, $size);

    $crc32_php = sprintf("%u", crc32($data));

    $crc32_db = $this->dataLayer->tstTestMaxAllowedPacket($data);

    self::assertEquals($crc32_php, $crc32_db);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Calling a stored routine with a BLOB less than max_allowed_packet must not be a problem.
   */
  public function test1()
  {
    $this->crc32WithStoredRoutine(0.5 * $this->dataLayer->getMaxAllowedPacket());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Calling a stored routine with a BLOB of max_allowed_packet bytes must not be a problem.
   */
  public function test2()
  {
    $this->crc32WithStoredRoutine($this->dataLayer->getMaxAllowedPacket());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Calling a stored routine with a BLOB of larger than max_allowed_packet bytes is not possible.
   *
   * @expectedException RuntimeException
   */
  public function test4()
  {
    $this->crc32WithStoredRoutine(2 * $this->dataLayer->getMaxAllowedPacket());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Calling a stored routine with a BLOB larger than max_allowed_packet bytes is not possible.
   *
   * @expectedException RuntimeException
   */
  public function xtest3()
  {
    $this->crc32WithStoredRoutine(1.05 * $this->dataLayer->getMaxAllowedPacket());
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

