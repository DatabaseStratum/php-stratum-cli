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
class QuoteWithLobTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  public function genericInvalid($theColumn, $theValue)
  {
    try
    {
      $n = DataLayer::test02(($theColumn=='int') ? $theValue : null,        // tst_c00 int
                             ($theColumn=='smallint') ? $theValue : null,        // tst_c01 smallint
                             ($theColumn=='tinyint') ? $theValue : null,        // tst_c02 tinyint
                             ($theColumn=='mediumint') ? $theValue : null,        // tst_c03 mediumint
                             ($theColumn=='bigint') ? $theValue : null,        // tst_c04 bigint
                             ($theColumn=='decimal)') ? $theValue : null,        // tst_c05 decimal(10,2)
                             ($theColumn=='float') ? $theValue : null,        // tst_c06 float
                             ($theColumn=='double') ? $theValue : null,        // tst_c07 double
                             ($theColumn=='bit') ? $theValue : null,        // tst_c08 bit
                             ($theColumn=='date') ? $theValue : null,        // tst_c09 date
                             ($theColumn=='datetime') ? $theValue : null,        // tst_c10 datetime
                             ($theColumn=='timestamp') ? $theValue : null,        // tst_c11 timestamp
                             ($theColumn=='time') ? $theValue : null,        // tst_c12 time
                             ($theColumn=='year') ? $theValue : null,        // tst_c13 year
                             ($theColumn=='char') ? $theValue : null,        // tst_c14 char(10)
                             ($theColumn=='varchar') ? $theValue : null,        // tst_c15 varchar(10)
                             ($theColumn=='binary') ? $theValue : null,        // tst_c16 binary(10)
                             ($theColumn=='varbinary') ? $theValue : null,        // tst_c17 varbinary(10)
                             ($theColumn=='tinyblob') ? $theValue : null,        // tst_c18 tinyblob
                             ($theColumn=='blob') ? $theValue : null,        // tst_c19 blob
                             ($theColumn=='mediumblob') ? $theValue : null,        // tst_c20 mediumblob
                             ($theColumn=='longblob') ? $theValue : null,        // tst_c21 longblob
                             ($theColumn=='tinytext') ? $theValue : null,        // tst_c22 tinytext
                             ($theColumn=='text') ? $theValue : null,        // tst_c23 text
                             ($theColumn=='mediumtext') ? $theValue : null,        // tst_c24 mediumtext
                             ($theColumn=='longtext') ? $theValue : null,        // tst_c25 longtext
                             ($theColumn=='enum') ? $theValue : null,        // tst_c26 enum('a','b')
                             ($theColumn=='set') ? $theValue : null);      // tst_c27 set('a','b')
      $this->assertTrue(false);
    }
    catch (Exception $e)
    {
      $this->assertTrue(true);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function genericValid($theColumn, $theValue)
  {
    $n = DataLayer::test02(($theColumn=='int') ? $theValue : null,        // tst_c00 int
                           ($theColumn=='smallint') ? $theValue : null,        // tst_c01 smallint
                           ($theColumn=='tinyint') ? $theValue : null,        // tst_c02 tinyint
                           ($theColumn=='mediumint') ? $theValue : null,        // tst_c03 mediumint
                           ($theColumn=='bigint') ? $theValue : null,        // tst_c04 bigint
                           ($theColumn=='decimal)') ? $theValue : null,        // tst_c05 decimal(10,2)
                           ($theColumn=='float') ? $theValue : null,        // tst_c06 float
                           ($theColumn=='double') ? $theValue : null,        // tst_c07 double
                           ($theColumn=='bit') ? $theValue : null,        // tst_c08 bit
                           ($theColumn=='date') ? $theValue : null,        // tst_c09 date
                           ($theColumn=='datetime') ? $theValue : null,        // tst_c10 datetime
                           ($theColumn=='timestamp') ? $theValue : null,        // tst_c11 timestamp
                           ($theColumn=='time') ? $theValue : null,        // tst_c12 time
                           ($theColumn=='year') ? $theValue : null,        // tst_c13 year
                           ($theColumn=='char') ? $theValue : null,        // tst_c14 char(10)
                           ($theColumn=='varchar') ? $theValue : null,        // tst_c15 varchar(10)
                           ($theColumn=='binary') ? $theValue : null,        // tst_c16 binary(10)
                           ($theColumn=='varbinary') ? $theValue : null,        // tst_c17 varbinary(10)
                           ($theColumn=='tinyblob') ? $theValue : null,        // tst_c18 tinyblob
                           ($theColumn=='blob') ? $theValue : null,        // tst_c19 blob
                           ($theColumn=='mediumblob') ? $theValue : null,        // tst_c20 mediumblob
                           ($theColumn=='longblob') ? $theValue : null,        // tst_c21 longblob
                           ($theColumn=='tinytext') ? $theValue : null,        // tst_c22 tinytext
                           ($theColumn=='text') ? $theValue : null,        // tst_c23 text
                           ($theColumn=='mediumtext') ? $theValue : null,        // tst_c24 mediumtext
                           ($theColumn=='longtext') ? $theValue : null,        // tst_c25 longtext
                           ($theColumn=='enum') ? $theValue : null,        // tst_c26 enum('a','b')
                           ($theColumn=='set') ? $theValue : null);      // tst_c27 set('a','b')
    $this->assertEquals(1, $n);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test illegal values will raise an exception.
   */
  public function testInvalid()
  {
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
