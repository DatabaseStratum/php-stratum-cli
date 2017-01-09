<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

use SetBased\Exception\RuntimeException;
use SetBased\Stratum\Exception\ResultException;
use SetBased\Stratum\MySql\StaticDataLayer;

//----------------------------------------------------------------------------------------------------------------------
/**
 * The data layer.
 */
class DataLayer extends StaticDataLayer
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string|null
   * @throws ResultException
   * @throws RuntimeException
   */
  public static function tstMagicConstant01()
  {
    return self::executeSingleton1('CALL tst_magic_constant01()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string|null
   * @throws ResultException
   * @throws RuntimeException
   */
  public static function tstMagicConstant02()
  {
    return self::executeSingleton1('CALL tst_magic_constant02()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string|null
   * @throws ResultException
   * @throws RuntimeException
   */
  public static function tstMagicConstant03()
  {
    return self::executeSingleton1('CALL tst_magic_constant03()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string|null
   * @throws ResultException
   * @throws RuntimeException
   */
  public static function tstMagicConstant04()
  {
    return self::executeSingleton1('CALL tst_magic_constant04()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string|null
   * @throws ResultException
   * @throws RuntimeException
   */
  public static function tstMagicConstant05()
  {
    return self::executeSingleton1('CALL tst_magic_constant05()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for all possible types of parameters excluding LOB's.
   *
   * @param int    $pParam00 Test parameter 00.
   *                         int(11)
   * @param int    $pParam01 Test parameter 01.
   *                         smallint(6)
   * @param int    $pParam02 Test parameter 02.
   *                         tinyint(4)
   * @param int    $pParam03 Test parameter 03.
   *                         mediumint(9)
   * @param int    $pParam04 Test parameter 04.
   *                         bigint(20)
   * @param float  $pParam05 Test parameter 05.
   *                         decimal(10,2)
   * @param float  $pParam06 Test parameter 06.
   *                         float
   * @param float  $pParam07 Test parameter 07.
   *                         double
   * @param int    $pParam08 Test parameter 08.
   *                         bit(8)
   * @param string $pParam09 Test parameter 09.
   *                         date
   * @param string $pParam10 Test parameter 10.
   *                         datetime
   * @param string $pParam11 Test parameter 11.
   *                         timestamp
   * @param string $pParam12 Test parameter 12.
   *                         time
   * @param int    $pParam13 Test parameter 13.
   *                         year(4)
   * @param string $pParam14 Test parameter 14.
   *                         char(10) character set latin1 collation latin1_swedish_ci
   * @param string $pParam15 Test parameter 15.
   *                         varchar(10) character set latin1 collation latin1_swedish_ci
   * @param string $pParam16 Test parameter 16.
   *                         binary(10)
   * @param string $pParam17 Test parameter 17.
   *                         varbinary(10)
   * @param string $pParam26 Test parameter 26.
   *                         enum('a','b') character set latin1 collation latin1_swedish_ci
   * @param string $pParam27 Test parameter 27.
   *                         set('a','b') character set latin1 collation latin1_swedish_ci
   *
   * @return int
   * @throws RuntimeException
   */
  public static function tstTest01($pParam00, $pParam01, $pParam02, $pParam03, $pParam04, $pParam05, $pParam06, $pParam07, $pParam08, $pParam09, $pParam10, $pParam11, $pParam12, $pParam13, $pParam14, $pParam15, $pParam16, $pParam17, $pParam26, $pParam27)
  {
    return self::executeNone('CALL tst_test01('.self::quoteNum($pParam00).','.self::quoteNum($pParam01).','.self::quoteNum($pParam02).','.self::quoteNum($pParam03).','.self::quoteNum($pParam04).','.self::quoteNum($pParam05).','.self::quoteNum($pParam06).','.self::quoteNum($pParam07).','.self::quoteBit($pParam08).','.self::quoteString($pParam09).','.self::quoteString($pParam10).','.self::quoteString($pParam11).','.self::quoteString($pParam12).','.self::quoteNum($pParam13).','.self::quoteString($pParam14).','.self::quoteString($pParam15).','.self::quoteString($pParam16).','.self::quoteString($pParam17).','.self::quoteString($pParam26).','.self::quoteString($pParam27).')');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for all possible types of parameters including LOB's.
   *
   * @param int    $pParam00 Test parameter 00.
   *                         int(11)
   * @param int    $pParam01 Test parameter 01.
   *                         smallint(6)
   * @param int    $pParam02 Test parameter 02.
   *                         tinyint(4)
   * @param int    $pParam03 Test parameter 03.
   *                         mediumint(9)
   * @param int    $pParam04 Test parameter 04.
   *                         bigint(20)
   * @param float  $pParam05 Test parameter 05.
   *                         decimal(10,2)
   * @param float  $pParam06 Test parameter 06.
   *                         float
   * @param float  $pParam07 Test parameter 07.
   *                         double
   * @param int    $pParam08 Test parameter 08.
   *                         bit(8)
   * @param string $pParam09 Test parameter 09.
   *                         date
   * @param string $pParam10 Test parameter 10.
   *                         datetime
   * @param string $pParam11 Test parameter 11.
   *                         timestamp
   * @param string $pParam12 Test parameter 12.
   *                         time
   * @param int    $pParam13 Test parameter 13.
   *                         year(4)
   * @param string $pParam14 Test parameter 14.
   *                         char(10) character set latin1 collation latin1_swedish_ci
   * @param string $pParam15 Test parameter 15.
   *                         varchar(10) character set latin1 collation latin1_swedish_ci
   * @param string $pParam16 Test parameter 16.
   *                         binary(10)
   * @param string $pParam17 Test parameter 17.
   *                         varbinary(10)
   * @param string $pParam18 Test parameter 18.
   *                         tinyblob
   * @param string $pParam19 Test parameter 19.
   *                         blob
   * @param string $pParam20 Test parameter 20.
   *                         mediumblob
   * @param string $pParam21 Test parameter 21.
   *                         longblob
   * @param string $pParam22 Test parameter 22.
   *                         tinytext character set latin1 collation latin1_swedish_ci
   * @param string $pParam23 Test parameter 23.
   *                         text character set latin1 collation latin1_swedish_ci
   * @param string $pParam24 Test parameter 24.
   *                         mediumtext character set latin1 collation latin1_swedish_ci
   * @param string $pParam25 Test parameter 25.
   *                         longtext character set latin1 collation latin1_swedish_ci
   * @param string $pParam26 Test parameter 26.
   *                         enum('a','b') character set latin1 collation latin1_swedish_ci
   * @param string $pParam27 Test parameter 27.
   *                         set('a','b') character set latin1 collation latin1_swedish_ci
   *
   * @return int
   * @throws RuntimeException
   */
  public static function tstTest02($pParam00, $pParam01, $pParam02, $pParam03, $pParam04, $pParam05, $pParam06, $pParam07, $pParam08, $pParam09, $pParam10, $pParam11, $pParam12, $pParam13, $pParam14, $pParam15, $pParam16, $pParam17, $pParam18, $pParam19, $pParam20, $pParam21, $pParam22, $pParam23, $pParam24, $pParam25, $pParam26, $pParam27)
  {
    $query = 'CALL tst_test02('.self::quoteNum($pParam00).','.self::quoteNum($pParam01).','.self::quoteNum($pParam02).','.self::quoteNum($pParam03).','.self::quoteNum($pParam04).','.self::quoteNum($pParam05).','.self::quoteNum($pParam06).','.self::quoteNum($pParam07).','.self::quoteBit($pParam08).','.self::quoteString($pParam09).','.self::quoteString($pParam10).','.self::quoteString($pParam11).','.self::quoteString($pParam12).','.self::quoteNum($pParam13).','.self::quoteString($pParam14).','.self::quoteString($pParam15).','.self::quoteString($pParam16).','.self::quoteString($pParam17).',?,?,?,?,?,?,?,?,'.self::quoteString($pParam26).','.self::quoteString($pParam27).')';
    $stmt  = self::$mysqli->prepare($query);
    if (!$stmt) self::mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('bbbbbbbb', $null,$null,$null,$null,$null,$null,$null,$null);
    if (!$b) self::mySqlError('mysqli_stmt::bind_param');

    self::getMaxAllowedPacket();

    $n = strlen($pParam18);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pParam18, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    $n = strlen($pParam19);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(1, substr($pParam19, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    $n = strlen($pParam20);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(2, substr($pParam20, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    $n = strlen($pParam21);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(3, substr($pParam21, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    $n = strlen($pParam22);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(4, substr($pParam22, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    $n = strlen($pParam23);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(5, substr($pParam23, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    $n = strlen($pParam24);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(6, substr($pParam24, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    $n = strlen($pParam25);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(7, substr($pParam25, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    if (self::$logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');

      self::$queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');
    }

    $ret = self::$mysqli->affected_rows;

    $stmt->close();
    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation bulk_insert.
   *
   * @param array $rows
   * @throws RuntimeException
   */
  public static function tstTestBulkInsert01($rows)
  {
    self::query('CALL tst_test_bulk_insert01()');
    if (is_array($rows) && !empty($rows))
    {
      $sql = "INSERT INTO `TST_TEMPO`(`tst_col1`,`tst_col2`,`tst_col3`,`tst_col4`,`tst_col5`,`tst_col6`,`tst_col7`,`tst_col8`,`tst_col9`,`tst_col10`,`tst_col11`,`tst_col12`,`tst_col13`,`tst_col14`,`tst_col15`,`tst_col16`,`tst_col17`,`tst_col18`,`tst_col19`,`tst_col20`)";
      $first = true;
      foreach($rows as $row)
      {
        if ($first) $sql .=' values('.self::quoteNum($row['field1']).','.self::quoteNum($row['field2']).','.self::quoteNum($row['field3']).','.self::quoteNum($row['field4']).','.self::quoteNum($row['field5']).','.self::quoteNum($row['field6']).','.self::quoteNum($row['field7']).','.self::quoteNum($row['field8']).','.self::quoteNum($row['field9']).','.self::quoteString($row['field10']).','.self::quoteString($row['field11']).','.self::quoteString($row['field12']).','.self::quoteString($row['field13']).','.self::quoteString($row['field14']).','.self::quoteString($row['field15']).','.self::quoteString($row['field16']).','.self::quoteString($row['field17']).','.self::quoteString($row['field18']).','.self::quoteString($row['field19']).','.self::quoteBit($row['field20']).')';
        else        $sql .=',      ('.self::quoteNum($row['field1']).','.self::quoteNum($row['field2']).','.self::quoteNum($row['field3']).','.self::quoteNum($row['field4']).','.self::quoteNum($row['field5']).','.self::quoteNum($row['field6']).','.self::quoteNum($row['field7']).','.self::quoteNum($row['field8']).','.self::quoteNum($row['field9']).','.self::quoteString($row['field10']).','.self::quoteString($row['field11']).','.self::quoteString($row['field12']).','.self::quoteString($row['field13']).','.self::quoteString($row['field14']).','.self::quoteString($row['field15']).','.self::quoteString($row['field16']).','.self::quoteString($row['field17']).','.self::quoteString($row['field18']).','.self::quoteString($row['field19']).','.self::quoteBit($row['field20']).')';
        $first = false;
      }
      self::query($sql);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation bulk_insert.
   *
   * @param array $rows
   * @throws RuntimeException
   */
  public static function tstTestBulkInsert02($rows)
  {
    self::query('CALL tst_test_bulk_insert02()');
    if (is_array($rows) && !empty($rows))
    {
      $sql = "INSERT INTO `TST_TEMPO`(`tst_col1`,`tst_col4`,`tst_col5`)";
      $first = true;
      foreach($rows as $row)
      {
        if ($first) $sql .=' values('.self::quoteNum($row['field1']).','.self::quoteNum($row['field4']).','.self::quoteNum($row['field5']).')';
        else        $sql .=',      ('.self::quoteNum($row['field1']).','.self::quoteNum($row['field4']).','.self::quoteNum($row['field5']).')';
        $first = false;
      }
      self::query($sql);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for stored function wrapper.
   *
   * @param int $pA Parameter A.
   *                int(11)
   * @param int $pB Parameter B.
   *                int(11)
   *
   * @return string
   * @throws RuntimeException
   */
  public static function tstTestFunction($pA, $pB)
  {
    return self::executeSingleton0('SELECT tst_test_function('.self::quoteNum($pA).','.self::quoteNum($pB).')');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for illegal query.
   *
   * @return \array[]
   * @throws RuntimeException
   */
  public static function tstTestIllegalQuery()
  {
    return self::executeRows('CALL tst_test_illegal_query()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   *
   * @param string|int[] $pIds The id's in CSV format.
   *                           varchar(255) character set latin1 collation latin1_swedish_ci
   *
   * @return \array[]
   * @throws RuntimeException
   */
  public static function tstTestListOfInt($pIds)
  {
    $result = self::query('CALL tst_test_list_of_int('.self::quoteListOfInt($pIds, ',', '\"', '\\').')');
    $ret = [];
    while($row = $result->fetch_array(MYSQLI_ASSOC)) $ret[$row['tst_id']] = $row;
    $result->free();
    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    return  $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type log.
   *
   * @return int
   * @throws RuntimeException
   */
  public static function tstTestLog()
  {
    return self::executeLog('CALL tst_test_log()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for sending data larger than max_allowed_packet.
   *
   * @param string $pTmpBlob The BLOB larger than max_allowed_packet.
   *                         longblob
   *
   * @return string|null
   * @throws ResultException
   * @throws RuntimeException
   */
  public static function tstTestMaxAllowedPacket($pTmpBlob)
  {
    $query = 'CALL tst_test_max_allowed_packet(?)';
    $stmt  = self::$mysqli->prepare($query);
    if (!$stmt) self::mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) self::mySqlError('mysqli_stmt::bind_param');

    self::getMaxAllowedPacket();

    $n = strlen($pTmpBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pTmpBlob, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    if (self::$logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');

      self::$queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    self::bindAssoc($stmt, $row);

    $tmp = [];
    while (($b = $stmt->fetch()))
    {
      $new = [];
      foreach($row as $value)
      {
        $new[] = $value;
      }
      $tmp[] = $new;
    }

    $stmt->close();
    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    if ($b===false) self::mySqlError('mysqli_stmt::fetch');
    if (count($tmp)!=1) throw new ResultException('1', count($tmp), $query);

    return $tmp[0][0];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   *
   * @return array
   * @throws ResultException
   * @throws RuntimeException
   */
  public static function tstTestNoDocBlock()
  {
    return self::executeRow1('CALL tst_test_no_doc_block()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type none.
   *
   * @param int $pCount The number of iterations.
   *                    bigint(20)
   *
   * @return int
   * @throws RuntimeException
   */
  public static function tstTestNone($pCount)
  {
    return self::executeNone('CALL tst_test_none('.self::quoteNum($pCount).')');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type none with BLOB.
   *
   * @param int    $pCount The number of iterations.
   *                       bigint(20)
   * @param string $pBlob  The BLOB.
   *                       blob
   *
   * @return int
   * @throws RuntimeException
   */
  public static function tstTestNoneWithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_none_with_lob('.self::quoteNum($pCount).',?)';
    $stmt  = self::$mysqli->prepare($query);
    if (!$stmt) self::mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) self::mySqlError('mysqli_stmt::bind_param');

    self::getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    if (self::$logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');

      self::$queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');
    }

    $ret = self::$mysqli->affected_rows;

    $stmt->close();
    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for conversion of MySQL types to PHP types.
   *
   * @param float $pPhpType1 Must be converted to PHP type float in the DataLayer.
   *                         decimal(10,2)
   * @param int   $pPhpType2 Must be converted to PHP type int in the DataLayer.
   *                         decimal(10,0)
   *
   * @return int
   * @throws RuntimeException
   */
  public static function tstTestParameterType($pPhpType1, $pPhpType2)
  {
    return self::executeNone('CALL tst_test_parameter_type('.self::quoteNum($pPhpType1).','.self::quoteNum($pPhpType2).')');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type row0.
   *
   * @param int $pCount The number of rows selected. * 0 For a valid test. * 1 For a valid test. * 2 For a invalid test.
   *                    int(11)
   *
   * @return array|null
   * @throws ResultException
   * @throws RuntimeException
   */
  public static function tstTestRow0a($pCount)
  {
    return self::executeRow0('CALL tst_test_row0a('.self::quoteNum($pCount).')');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type row0 with BLOB.
   *
   * @param int    $pCount The number of rows selected. * 0 For a valid test. * 1 For a valid test. * 2 For a invalid test.
   *                       int(11)
   * @param string $pBlob  The BLOB.
   *                       blob
   *
   * @return array|null
   * @throws ResultException
   * @throws RuntimeException
   */
  public static function tstTestRow0aWithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_row0a_with_lob('.self::quoteNum($pCount).',?)';
    $stmt  = self::$mysqli->prepare($query);
    if (!$stmt) self::mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) self::mySqlError('mysqli_stmt::bind_param');

    self::getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    if (self::$logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');

      self::$queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    self::bindAssoc($stmt, $row);

    $tmp = [];
    while (($b = $stmt->fetch()))
    {
      $new = [];
      foreach($row as $key => $value)
      {
        $new[$key] = $value;
      }
      $tmp[] = $new;
    }

    $stmt->close();
    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    if ($b===false) self::mySqlError('mysqli_stmt::fetch');
    if (count($tmp)>1) throw new ResultException('0 or 1', count($tmp), $query);

    return ($tmp) ? $tmp[0] : null;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type row1.
   *
   * @param int $pCount The number of rows selected. * 0 For a invalid test. * 1 For a valid test. * 2 For a invalid test.
   *                    int(11)
   *
   * @return array
   * @throws ResultException
   * @throws RuntimeException
   */
  public static function tstTestRow1a($pCount)
  {
    return self::executeRow1('CALL tst_test_row1a('.self::quoteNum($pCount).')');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type row1 with BLOB.
   *
   * @param int    $pCount The number of rows selected. * 0 For a invalid test. * 1 For a valid test. * 2 For a invalid test.
   *                       int(11)
   * @param string $pBlob  The BLOB.
   *                       blob
   *
   * @return array
   * @throws ResultException
   * @throws RuntimeException
   */
  public static function tstTestRow1aWithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_row1a_with_lob('.self::quoteNum($pCount).',?)';
    $stmt  = self::$mysqli->prepare($query);
    if (!$stmt) self::mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) self::mySqlError('mysqli_stmt::bind_param');

    self::getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    if (self::$logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');

      self::$queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    self::bindAssoc($stmt, $row);

    $tmp = [];
    while (($b = $stmt->fetch()))
    {
      $new = [];
      foreach($row as $key => $value)
      {
        $new[$key] = $value;
      }
      $tmp[] = $new;
    }

    $stmt->close();
    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    if ($b===false) self::mySqlError('mysqli_stmt::fetch');
    if (count($tmp)!=1) throw new ResultException('1', count($tmp), $query);

    return $row;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type row1.
   *
   * @param int $pCount The number of rows selected. * 0 For a invalid test. * 1 For a valid test. * 2 For a invalid test.
   *                    int(11)
   *
   * @return \array[]
   * @throws RuntimeException
   */
  public static function tstTestRows1($pCount)
  {
    return self::executeRows('CALL tst_test_rows1('.self::quoteNum($pCount).')');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type rows.
   *
   * @param int    $pCount The number of rows selected.
   *                       int(11)
   * @param string $pBlob  The BLOB.
   *                       blob
   *
   * @return \array[]
   * @throws RuntimeException
   */
  public static function tstTestRows1WithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_rows1_with_lob('.self::quoteNum($pCount).',?)';
    $stmt  = self::$mysqli->prepare($query);
    if (!$stmt) self::mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) self::mySqlError('mysqli_stmt::bind_param');

    self::getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    if (self::$logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');

      self::$queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    self::bindAssoc($stmt, $row);

    $tmp = [];
    while (($b = $stmt->fetch()))
    {
      $new = [];
      foreach($row as $key => $value)
      {
        $new[$key] = $value;
      }
      $tmp[] = $new;
    }

    $stmt->close();
    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    if ($b===false) self::mySqlError('mysqli_stmt::fetch');

    return $tmp;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type rows_with_index.
   *
   * @param int $pCount The number of rows selected.
   *                    int(11)
   *
   * @return \array[]
   * @throws RuntimeException
   */
  public static function tstTestRowsWithIndex1($pCount)
  {
    $result = self::query('CALL tst_test_rows_with_index1('.self::quoteNum($pCount).')');
    $ret = [];
    while($row = $result->fetch_array(MYSQLI_ASSOC)) $ret[$row['tst_c01']][$row['tst_c02']][] = $row;
    $result->free();
    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type rows_with_index with BLOB..
   *
   * @param int    $pCount The number of rows selected.
   *                       int(11)
   * @param string $pBlob  The BLOB.
   *                       blob
   *
   * @return \array[]
   * @throws RuntimeException
   */
  public static function tstTestRowsWithIndex1WithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_rows_with_index1_with_lob('.self::quoteNum($pCount).',?)';
    $stmt  = self::$mysqli->prepare($query);
    if (!$stmt) self::mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) self::mySqlError('mysqli_stmt::bind_param');

    self::getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    if (self::$logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');

      self::$queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    self::bindAssoc($stmt, $row);

    $ret = [];
    while (($b = $stmt->fetch()))
    {
      $new = [];
      foreach($row as $key => $value)
      {
        $new[$key] = $value;
      }
      $ret[$new['tst_c01']][$new['tst_c02']][] = $new;
    }

    $stmt->close();
    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    if ($b===false) self::mySqlError('mysqli_stmt::fetch');

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type rows_with_key.
   *
   * @param int $pCount Number of rows selected.
   *                    int(11)
   *
   * @return \array[]
   * @throws RuntimeException
   */
  public static function tstTestRowsWithKey1($pCount)
  {
    $result = self::query('CALL tst_test_rows_with_key1('.self::quoteNum($pCount).')');
    $ret = [];
    while($row = $result->fetch_array(MYSQLI_ASSOC)) $ret[$row['tst_c01']][$row['tst_c02']][$row['tst_c03']] = $row;
    $result->free();
    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    return  $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type rows_with_key with BLOB.
   *
   * @param int    $pCount The number of rows selected.
   *                       int(11)
   * @param string $pBlob  The BLOB.
   *                       blob
   *
   * @return \array[]
   * @throws RuntimeException
   */
  public static function tstTestRowsWithKey1WithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_rows_with_key1_with_lob('.self::quoteNum($pCount).',?)';
    $stmt  = self::$mysqli->prepare($query);
    if (!$stmt) self::mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) self::mySqlError('mysqli_stmt::bind_param');

    self::getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    if (self::$logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');

      self::$queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    self::bindAssoc($stmt, $row);

    $ret = [];
    while (($b = $stmt->fetch()))
    {
      $new = [];
      foreach($row as $key => $value)
      {
        $new[$key] = $value;
      }
      $ret[$new['tst_c01']][$new['tst_c02']][$new['tst_c03']] = $new;
    }

    $stmt->close();
    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    if ($b===false) self::mySqlError('mysqli_stmt::fetch');

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type singleton0.
   *
   * @param int $pCount The number of rows selected. * 0 For a valid test. * 1 For a valid test. * 2 For a invalid test.
   *                    int(11)
   *
   * @return string|null
   * @throws ResultException
   * @throws RuntimeException
   */
  public static function tstTestSingleton0a($pCount)
  {
    return self::executeSingleton0('CALL tst_test_singleton0a('.self::quoteNum($pCount).')');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type singleton0 with BLOB..
   *
   * @param int    $pCount The number of rows selected. * 0 For a valid test. * 1 For a valid test. * 2 For a invalid test.
   *                       int(11)
   * @param string $pBlob  The BLOB.
   *                       blob
   *
   * @return string|null
   * @throws ResultException
   * @throws RuntimeException
   */
  public static function tstTestSingleton0aWithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_singleton0a_with_lob('.self::quoteNum($pCount).',?)';
    $stmt  = self::$mysqli->prepare($query);
    if (!$stmt) self::mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) self::mySqlError('mysqli_stmt::bind_param');

    self::getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    if (self::$logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');

      self::$queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    self::bindAssoc($stmt, $row);

    $tmp = [];
    while (($b = $stmt->fetch()))
    {
      $new = [];
      foreach($row as $value)
      {
        $new[] = $value;
      }
      $tmp[] = $new;
    }

    $stmt->close();
    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    if ($b===false) self::mySqlError('mysqli_stmt::fetch');
    if (count($tmp)>1) throw new ResultException('0 or 1', count($tmp), $query);

    return ($tmp) ? $tmp[0][0] : null;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type singleton1.
   *
   * @param int $pCount The number of rows selected. * 0 For a invalid test. * 1 For a valid test. * 2 For a invalid test.
   *                    int(11)
   *
   * @return string|null
   * @throws ResultException
   * @throws RuntimeException
   */
  public static function tstTestSingleton1a($pCount)
  {
    return self::executeSingleton1('CALL tst_test_singleton1a('.self::quoteNum($pCount).')');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type singleton1 with BLOB.
   *
   * @param int    $pCount The number of rows selected. * 0 For a invalid test. * 1 For a valid test. * 2 For a invalid test.
   *                       int(11)
   * @param string $pBlob  The BLOB.
   *                       blob
   *
   * @return string|null
   * @throws ResultException
   * @throws RuntimeException
   */
  public static function tstTestSingleton1aWithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_singleton1a_with_lob('.self::quoteNum($pCount).',?)';
    $stmt  = self::$mysqli->prepare($query);
    if (!$stmt) self::mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) self::mySqlError('mysqli_stmt::bind_param');

    self::getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, self::$chunkSize));
      if (!$b) self::mySqlError('mysqli_stmt::send_long_data');
      $p += self::$chunkSize;
    }

    if (self::$logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');

      self::$queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    self::bindAssoc($stmt, $row);

    $tmp = [];
    while (($b = $stmt->fetch()))
    {
      $new = [];
      foreach($row as $value)
      {
        $new[] = $value;
      }
      $tmp[] = $new;
    }

    $stmt->close();
    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    if ($b===false) self::mySqlError('mysqli_stmt::fetch');
    if (count($tmp)!=1) throw new ResultException('1', count($tmp), $query);

    return $tmp[0][0];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type table.
   *
   * @return int
   * @throws RuntimeException
   */
  public static function tstTestTable()
  {
    return self::executeTable('CALL tst_test_table()');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
