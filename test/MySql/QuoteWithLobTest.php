<?php

namespace SetBased\Stratum\Test\MySql;

use SetBased\Exception\RuntimeException;

/**
 * Test cases for quoting variables with LOBs.
 */
class QuoteWithLobTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  public function genericInvalid($column, $value)
  {
    try
    {
      $this->dataLayer->tstTest02(($column=='int') ? $value : null,
                                  ($column=='smallint') ? $value : null,
                                  ($column=='tinyint') ? $value : null,
                                  ($column=='mediumint') ? $value : null,
                                  ($column=='bigint') ? $value : null,
                                  ($column=='decimal') ? $value : null,
                                  ($column=='float') ? $value : null,
                                  ($column=='double') ? $value : null,
                                  ($column=='bit') ? $value : null,
                                  ($column=='date') ? $value : null,
                                  ($column=='datetime') ? $value : null,
                                  ($column=='timestamp') ? $value : null,
                                  ($column=='time') ? $value : null,
                                  ($column=='year') ? $value : null,
                                  ($column=='char') ? $value : null,
                                  ($column=='varchar') ? $value : null,
                                  ($column=='binary') ? $value : null,
                                  ($column=='varbinary') ? $value : null,
                                  ($column=='tinyblob') ? $value : null,
                                  ($column=='blob') ? $value : null,
                                  ($column=='mediumblob') ? $value : null,
                                  ($column=='longblob') ? $value : null,
                                  ($column=='tinytext') ? $value : null,
                                  ($column=='text') ? $value : null,
                                  ($column=='mediumtext') ? $value : null,
                                  ($column=='longtext') ? $value : null,
                                  ($column=='enum') ? $value : null,
                                  ($column=='set') ? $value : null);
      $this->assertTrue(false, "column: $column, value: $value");
    }
    catch (RuntimeException $e)
    {
      $this->assertTrue(true);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function genericValid($column, $value)
  {
    $n = $this->dataLayer->tstTest02(($column=='int') ? $value : null,
                                     ($column=='smallint') ? $value : null,
                                     ($column=='tinyint') ? $value : null,
                                     ($column=='mediumint') ? $value : null,
                                     ($column=='bigint') ? $value : null,
                                     ($column=='decimal') ? $value : null,
                                     ($column=='float') ? $value : null,
                                     ($column=='double') ? $value : null,
                                     ($column=='bit') ? $value : null,
                                     ($column=='date') ? $value : null,
                                     ($column=='datetime') ? $value : null,
                                     ($column=='timestamp') ? $value : null,
                                     ($column=='time') ? $value : null,
                                     ($column=='year') ? $value : null,
                                     ($column=='char') ? $value : null,
                                     ($column=='varchar') ? $value : null,
                                     ($column=='binary') ? $value : null,
                                     ($column=='varbinary') ? $value : null,
                                     ($column=='tinyblob') ? $value : null,
                                     ($column=='blob') ? $value : null,
                                     ($column=='mediumblob') ? $value : null,
                                     ($column=='longblob') ? $value : null,
                                     ($column=='tinytext') ? $value : null,
                                     ($column=='text') ? $value : null,
                                     ($column=='mediumtext') ? $value : null,
                                     ($column=='longtext') ? $value : null,
                                     ($column=='enum') ? $value : null,
                                     ($column=='set') ? $value : null);
    self::assertEquals(1, $n);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test illegal values will raise an exception.
   */
  public function testInvalid()
  {
    $tests = [];

    $tests[] = ['column' => 'int', 'value' => 'abc'];
    $tests[] = ['column' => 'smallint', 'value' => 'abc'];
    $tests[] = ['column' => 'tinyint', 'value' => 'abc'];
    $tests[] = ['column' => 'mediumint', 'value' => 'abc'];
    $tests[] = ['column' => 'bigint', 'value' => 'abc'];

    $tests[] = ['column' => 'decimal', 'value' => 'abc'];
    $tests[] = ['column' => 'float', 'value' => 'abc'];
    $tests[] = ['column' => 'double', 'value' => 'abc'];

    $tests[] = ['column' => 'bit', 'value' => 'abc'];

    $tests[] = ['column' => 'date', 'value' => 'qwerty'];
    $tests[] = ['column' => 'datetime', 'value' => 'qwerty'];
    $tests[] = ['column' => 'timestamp', 'value' => 'qwerty'];
    $tests[] = ['column' => 'time', 'value' => 'qwerty'];

    $tests[] = ['column' => 'year', 'value' => 'abc'];

    $tests[] = ['column' => 'enum', 'value' => 'c'];
    $tests[] = ['column' => 'set', 'value' => 'c'];

    foreach ($tests as $test)
    {
      $this->genericInvalid($test['column'], $test['value']);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test all column types are quoted properly.
   */
  public function testValid()
  {
    $tests = [];

    $tests[] = ['column' => 'int', 'value' => 1];
    $tests[] = ['column' => 'int', 'value' => '1'];
    $tests[] = ['column' => 'smallint', 'value' => 1];
    $tests[] = ['column' => 'smallint', 'value' => '1'];
    $tests[] = ['column' => 'tinyint', 'value' => 1];
    $tests[] = ['column' => 'tinyint', 'value' => '1'];
    $tests[] = ['column' => 'mediumint', 'value' => 1];
    $tests[] = ['column' => 'mediumint', 'value' => '1'];
    $tests[] = ['column' => 'bigint', 'value' => 1];
    $tests[] = ['column' => 'bigint', 'value' => '1'];

    $tests[] = ['column' => 'decimal', 'value' => 0.1];
    $tests[] = ['column' => 'decimal', 'value' => '0.1'];
    $tests[] = ['column' => 'float', 'value' => 0.1];
    $tests[] = ['column' => 'float', 'value' => '0.1'];
    $tests[] = ['column' => 'double', 'value' => 0.1];
    $tests[] = ['column' => 'double', 'value' => '0.1'];

    $tests[] = ['column' => 'bit', 'value' => 1010];
    $tests[] = ['column' => 'bit', 'value' => '1010'];

    $tests[] = ['column' => 'date', 'value' => '2000-01-01'];
    $tests[] = ['column' => 'datetime', 'value' => '2000-01-01 10:00:00'];
    $tests[] = ['column' => 'timestamp', 'value' => 2102101];
    $tests[] = ['column' => 'time', 'value' => '10:00:00'];
    $tests[] = ['column' => 'year', 'value' => 2000];
    $tests[] = ['column' => 'year', 'value' => '2000'];

    $tests[] = ['column' => 'char', 'value' => 1234];
    $tests[] = ['column' => 'char', 'value' => 'abc'];
    $tests[] = ['column' => 'char', 'value' => "0xC8 ' --"];

    $tests[] = ['column' => 'varchar', 'value' => 1234];
    $tests[] = ['column' => 'varchar', 'value' => 'abc'];
    $tests[] = ['column' => 'varchar', 'value' => "0xC8 ' --"];

    $tests[] = ['column' => 'binary', 'value' => 1010];
    $tests[] = ['column' => 'binary', 'value' => '1010'];
    $tests[] = ['column' => 'binary', 'value' => "\xFF\x7F\x80\x5c\x00\x10"];
    $tests[] = ['column' => 'varbinary', 'value' => 1010];
    $tests[] = ['column' => 'varbinary', 'value' => '1010'];
    $tests[] = ['column' => 'varbinary', 'value' => "\xFF\x7F\x80\x5c\x00\x10"];

    $tests[] = ['column' => 'tinyblob', 'value' => 100];
    $tests[] = ['column' => 'tinyblob', 'value' => 'abc'];
    $tests[] = ['column' => 'blob', 'value' => 200];
    $tests[] = ['column' => 'blob', 'value' => 'abc'];
    $tests[] = ['column' => 'mediumblob', 'value' => 300];
    $tests[] = ['column' => 'mediumblob', 'value' => 'abc'];
    $tests[] = ['column' => 'longblob', 'value' => 400];
    $tests[] = ['column' => 'longblob', 'value' => 'abc'];

    $tests[] = ['column' => 'tinytext', 'value' => 100];
    $tests[] = ['column' => 'tinytext', 'value' => 'abc'];
    $tests[] = ['column' => 'text', 'value' => 200];
    $tests[] = ['column' => 'text', 'value' => 'abc'];
    $tests[] = ['column' => 'mediumtext', 'value' => 300];
    $tests[] = ['column' => 'mediumtext', 'value' => 'abc'];
    $tests[] = ['column' => 'longtext', 'value' => 400];
    $tests[] = ['column' => 'longtext', 'value' => 'abc'];

    $tests[] = ['column' => 'enum', 'value' => 'a'];
    $tests[] = ['column' => 'enum', 'value' => 'b'];

    $tests[] = ['column' => 'set', 'value' => 'a'];
    $tests[] = ['column' => 'set', 'value' => 'b'];

    foreach ($tests as $test)
    {
      $this->genericValid($test['column'], $test['value']);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------

}
//----------------------------------------------------------------------------------------------------------------------
