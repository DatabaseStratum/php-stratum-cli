<?php
declare(strict_types=1);

namespace SetBased\Stratum\Test\MySql;

/**
 * Test case for bulk inserts.
 */
class BulkInsertTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test number of rows inserted is corrected. All columns type supported.
   */
  public function test1()
  {
    $data = [];

    $data[] = ['field1'  => 1, //  int,
               'field2'  => 1, //  smallint,
               'field3'  => 1, //  mediumint,
               'field4'  => 1, //  tinyint,
               'field5'  => 1, //  bigint,
               'field6'  => 2000, //  year,
               'field7'  => '0.1', //  decimal,
               'field8'  => 0.1, //  float,
               'field9'  => 0.1, //  double,
               'field10' => '1010', //  binary,
               'field11' => '1010', //  varbinary,
               'field12' => 'abc', //  char,
               'field13' => 'abc', //  varchar(80),
               'field14' => date('H:i:s'), //  time,
               'field15' => date('Y-m-d H:i:s'), //  timestamp,
               'field16' => date('Y-m-d'), //  date,
               'field17' => date('Y-m-d H:i:s'), //  datetime,
               'field18' => 'a', //  enum('a','b','c'),
               'field19' => 'a', //  set('a','b','c'),
               'field20' => '1010']; //  bit(4),

    $data[] = ['field1'  => 2, //  int,
               'field2'  => 2, //  smallint,
               'field3'  => 2, //  mediumint,
               'field4'  => 2, //  tinyint,
               'field5'  => 2, //  bigint,
               'field6'  => 2001, //  year,
               'field7'  => '0.2', //  decimal,
               'field8'  => 0.2, //  float,
               'field9'  => 0.2, //  double,
               'field10' => '1010', //  binary,
               'field11' => '1010', //  varbinary,
               'field12' => 'abc', //  char,
               'field13' => 'abc', //  varchar(80),
               'field14' => date('H:i:s'), //  time,
               'field15' => date('Y-m-d H:i:s'), //  timestamp,
               'field16' => date('Y-m-d'), //  date,
               'field17' => date('Y-m-d H:i:s'), //  datetime,
               'field18' => 'b', //  enum('a','b','c'),
               'field19' => 'b', //  set('a','b','c'),
               'field20' => '1010']; //  bit(4),

    $data[] = ['field1'  => 3, //  int,
               'field2'  => 3, //  smallint,
               'field3'  => 3, //  mediumint,
               'field4'  => 3, //  tinyint,
               'field5'  => 3, //  bigint,
               'field6'  => 2002, //  year,
               'field7'  => '0.3', //  decimal,
               'field8'  => 0.3, //  float,
               'field9'  => 0.3, //  double,
               'field10' => '1010', //  binary,
               'field11' => '1010', //  varbinary,
               'field12' => 'abc', //  char,
               'field13' => 'abc', //  varchar(80),
               'field14' => date('H:i:s'), //  time,
               'field15' => date('Y-m-d H:i:s'), //  timestamp,
               'field16' => date('Y-m-d'), //  date,
               'field17' => date('Y-m-d H:i:s'), //  datetime,
               'field18' => 'c', //  enum('a','b','c'),
               'field19' => 'c', //  set('a','b','c'),
               'field20' => '1010']; //  bit(4),

    $data[] = ['field1'  => 4, //  int,
               'field2'  => 1, //  smallint,
               'field3'  => 1, //  mediumint,
               'field4'  => 1, //  tinyint,
               'field5'  => 1, //  bigint,
               'field6'  => 2000, //  year,
               'field7'  => '0.1', //  decimal,
               'field8'  => 0.1, //  float,
               'field9'  => 0.1, //  double,
               'field10' => '1010', //  binary,
               'field11' => '1010', //  varbinary,
               'field12' => 'abc', //  char,
               'field13' => 'abc', //  varchar(80),
               'field14' => date('H:i:s'), //  time,
               'field15' => date('Y-m-d H:i:s'), //  timestamp,
               'field16' => date('Y-m-d'), //  date,
               'field17' => date('Y-m-d H:i:s'), //  datetime,
               'field18' => 'c', //  enum('a','b','c'),
               'field19' => 'a,b', //  set('a','b','c'),
               'field20' => '1010']; //  bit(4),

    $this->dataLayer->tstTestBulkInsert01($data);

    $query = 'SELECT count(*) FROM `TST_TEMPO`';
    $ret   = $this->dataLayer->executeSingleton1($query);

    self::assertEquals(4, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for empty fields.
   */
  public function test2()
  {
    $data   = [];
    $data[] = ['field1' => 1, //  int,
               'field2' => 1, //  int,
               'field3' => 1, //  int,
               'field4' => 1, //  int,
               'field5' => 1]; //  int,

    $this->dataLayer->tstTestBulkInsert02($data);

    $query = 'SELECT count(*) FROM `TST_TEMPO`';
    $ret   = $this->dataLayer->executeSingleton1($query);

    self::assertEquals(1, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
