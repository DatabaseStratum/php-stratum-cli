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
class QuoteTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  public function genericInvalid($theColumn, $theValue)
  {
    try
    {
      DataLayer::test01(($theColumn=='int') ? $theValue : null,
                        ($theColumn=='smallint') ? $theValue : null,
                        ($theColumn=='tinyint') ? $theValue : null,
                        ($theColumn=='mediumint') ? $theValue : null,
                        ($theColumn=='bigint') ? $theValue : null,
                        ($theColumn=='decimal)') ? $theValue : null,
                        ($theColumn=='float') ? $theValue : null,
                        ($theColumn=='double') ? $theValue : null,
                        ($theColumn=='bit') ? $theValue : null,
                        ($theColumn=='date') ? $theValue : null,
                        ($theColumn=='datetime') ? $theValue : null,
                        ($theColumn=='timestamp') ? $theValue : null,
                        ($theColumn=='time') ? $theValue : null,
                        ($theColumn=='year') ? $theValue : null,
                        ($theColumn=='char') ? $theValue : null,
                        ($theColumn=='varchar') ? $theValue : null,
                        ($theColumn=='binary') ? $theValue : null,
                        ($theColumn=='varbinary') ? $theValue : null,
                        ($theColumn=='enum') ? $theValue : null,
                        ($theColumn=='set') ? $theValue : null);
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
    $n = DataLayer::test01(($theColumn=='int') ? $theValue : null,
                           ($theColumn=='smallint') ? $theValue : null,
                           ($theColumn=='tinyint') ? $theValue : null,
                           ($theColumn=='mediumint') ? $theValue : null,
                           ($theColumn=='bigint') ? $theValue : null,
                           ($theColumn=='decimal)') ? $theValue : null,
                           ($theColumn=='float') ? $theValue : null,
                           ($theColumn=='double') ? $theValue : null,
                           ($theColumn=='bit') ? $theValue : null,
                           ($theColumn=='date') ? $theValue : null,
                           ($theColumn=='datetime') ? $theValue : null,
                           ($theColumn=='timestamp') ? $theValue : null,
                           ($theColumn=='time') ? $theValue : null,
                           ($theColumn=='year') ? $theValue : null,
                           ($theColumn=='char') ? $theValue : null,
                           ($theColumn=='varchar') ? $theValue : null,
                           ($theColumn=='binary') ? $theValue : null,
                           ($theColumn=='varbinary') ? $theValue : null,
                           ($theColumn=='enum') ? $theValue : null,
                           ($theColumn=='set') ? $theValue : null);
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
