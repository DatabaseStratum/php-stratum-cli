<?php

namespace SetBased\Stratum\Test\MySql;

use SetBased\Stratum\Exception\ResultException;
use SetBased\Stratum\MySql\DataLayer;

//----------------------------------------------------------------------------------------------------------------------
/**
 * The data layer.
 */
class TestDataLayer extends DataLayer
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string
   */
  public function tstMagicConstant01(): string
  {
    return $this->executeSingleton1('call tst_magic_constant01()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return int
   */
  public function tstMagicConstant02(): int
  {
    return $this->executeSingleton1('call tst_magic_constant02()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string
   */
  public function tstMagicConstant03(): string
  {
    return $this->executeSingleton1('call tst_magic_constant03()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string
   */
  public function tstMagicConstant04(): string
  {
    return $this->executeSingleton1('call tst_magic_constant04()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string
   */
  public function tstMagicConstant05(): string
  {
    return $this->executeSingleton1('call tst_magic_constant05()');
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
   * @param string $pParam05 Test parameter 05.
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
   *                         char(10) character set utf8 collation utf8_general_ci
   * @param string $pParam15 Test parameter 15.
   *                         varchar(10) character set utf8 collation utf8_general_ci
   * @param string $pParam16 Test parameter 16.
   *                         binary(10)
   * @param string $pParam17 Test parameter 17.
   *                         varbinary(10)
   * @param string $pParam26 Test parameter 26.
   *                         enum('a','b') character set utf8 collation utf8_general_ci
   * @param string $pParam27 Test parameter 27.
   *                         set('a','b') character set utf8 collation utf8_general_ci
   *
   * @return int
   */
  public function tstTest01(?int $pParam00, ?int $pParam01, ?int $pParam02, ?int $pParam03, ?int $pParam04, ?string $pParam05, ?float $pParam06, ?float $pParam07, ?int $pParam08, ?string $pParam09, ?string $pParam10, ?string $pParam11, ?string $pParam12, ?int $pParam13, ?string $pParam14, ?string $pParam15, ?string $pParam16, ?string $pParam17, ?string $pParam26, ?string $pParam27): int
  {
    return $this->executeNone('call tst_test01('.$this->quoteInt($pParam00).','.$this->quoteInt($pParam01).','.$this->quoteInt($pParam02).','.$this->quoteInt($pParam03).','.$this->quoteInt($pParam04).','.$this->quoteString($pParam05).','.$this->quoteFloat($pParam06).','.$this->quoteFloat($pParam07).','.$this->quoteBit($pParam08).','.$this->quoteString($pParam09).','.$this->quoteString($pParam10).','.$this->quoteString($pParam11).','.$this->quoteString($pParam12).','.$this->quoteInt($pParam13).','.$this->quoteString($pParam14).','.$this->quoteString($pParam15).','.$this->quoteString($pParam16).','.$this->quoteString($pParam17).','.$this->quoteString($pParam26).','.$this->quoteString($pParam27).')');
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
   * @param string $pParam05 Test parameter 05.
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
   *                         char(10) character set utf8 collation utf8_general_ci
   * @param string $pParam15 Test parameter 15.
   *                         varchar(10) character set utf8 collation utf8_general_ci
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
   *                         tinytext character set utf8 collation utf8_general_ci
   * @param string $pParam23 Test parameter 23.
   *                         text character set utf8 collation utf8_general_ci
   * @param string $pParam24 Test parameter 24.
   *                         mediumtext character set utf8 collation utf8_general_ci
   * @param string $pParam25 Test parameter 25.
   *                         longtext character set utf8 collation utf8_general_ci
   * @param string $pParam26 Test parameter 26.
   *                         enum('a','b') character set utf8 collation utf8_general_ci
   * @param string $pParam27 Test parameter 27.
   *                         set('a','b') character set utf8 collation utf8_general_ci
   *
   * @return int
   */
  public function tstTest02(?int $pParam00, ?int $pParam01, ?int $pParam02, ?int $pParam03, ?int $pParam04, ?string $pParam05, ?float $pParam06, ?float $pParam07, ?int $pParam08, ?string $pParam09, ?string $pParam10, ?string $pParam11, ?string $pParam12, ?int $pParam13, ?string $pParam14, ?string $pParam15, ?string $pParam16, ?string $pParam17, ?string $pParam18, ?string $pParam19, ?string $pParam20, ?string $pParam21, ?string $pParam22, ?string $pParam23, ?string $pParam24, ?string $pParam25, ?string $pParam26, ?string $pParam27)
  {
    $query = 'call tst_test02('.$this->quoteInt($pParam00).','.$this->quoteInt($pParam01).','.$this->quoteInt($pParam02).','.$this->quoteInt($pParam03).','.$this->quoteInt($pParam04).','.$this->quoteString($pParam05).','.$this->quoteFloat($pParam06).','.$this->quoteFloat($pParam07).','.$this->quoteBit($pParam08).','.$this->quoteString($pParam09).','.$this->quoteString($pParam10).','.$this->quoteString($pParam11).','.$this->quoteString($pParam12).','.$this->quoteInt($pParam13).','.$this->quoteString($pParam14).','.$this->quoteString($pParam15).','.$this->quoteString($pParam16).','.$this->quoteString($pParam17).',?,?,?,?,?,?,?,?,'.$this->quoteString($pParam26).','.$this->quoteString($pParam27).')';
    $stmt  = $this->mysqli->prepare($query);
    if (!$stmt) $this->mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('bbbbbbbb', $null,$null,$null,$null,$null,$null,$null,$null);
    if (!$b) $this->mySqlError('mysqli_stmt::bind_param');

    $this->getMaxAllowedPacket();

    $n = strlen($pParam18);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pParam18, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    $n = strlen($pParam19);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(1, substr($pParam19, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    $n = strlen($pParam20);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(2, substr($pParam20, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    $n = strlen($pParam21);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(3, substr($pParam21, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    $n = strlen($pParam22);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(4, substr($pParam22, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    $n = strlen($pParam23);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(5, substr($pParam23, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    $n = strlen($pParam24);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(6, substr($pParam24, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    $n = strlen($pParam25);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(7, substr($pParam25, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    if ($this->logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');

      $this->queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');
    }

    $ret = $this->mysqli->affected_rows;

    $stmt->close();
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation bulk_insert.
   *
   * @param array[] $rows The rows that must inserted.
   *
   * @return void
   */
  public function tstTestBulkInsert01(?array $rows): void
  {
    $this->realQuery('call tst_test_bulk_insert01()');
    if (is_array($rows) && !empty($rows))
    {
      $sql = "INSERT INTO `TST_TEMPO`(`tst_col1`,`tst_col2`,`tst_col3`,`tst_col4`,`tst_col5`,`tst_col6`,`tst_col7`,`tst_col8`,`tst_col9`,`tst_col10`,`tst_col11`,`tst_col12`,`tst_col13`,`tst_col14`,`tst_col15`,`tst_col16`,`tst_col17`,`tst_col18`,`tst_col19`,`tst_col20`)";
      $first = true;
      foreach($rows as $row)
      {
        if ($first) $sql .=' values('.$this->quoteInt($row['field1']).','.$this->quoteInt($row['field2']).','.$this->quoteInt($row['field3']).','.$this->quoteInt($row['field4']).','.$this->quoteInt($row['field5']).','.$this->quoteInt($row['field6']).','.$this->quoteString($row['field7']).','.$this->quoteFloat($row['field8']).','.$this->quoteFloat($row['field9']).','.$this->quoteString($row['field10']).','.$this->quoteString($row['field11']).','.$this->quoteString($row['field12']).','.$this->quoteString($row['field13']).','.$this->quoteString($row['field14']).','.$this->quoteString($row['field15']).','.$this->quoteString($row['field16']).','.$this->quoteString($row['field17']).','.$this->quoteString($row['field18']).','.$this->quoteString($row['field19']).','.$this->quoteBit($row['field20']).')';
        else        $sql .=',      ('.$this->quoteInt($row['field1']).','.$this->quoteInt($row['field2']).','.$this->quoteInt($row['field3']).','.$this->quoteInt($row['field4']).','.$this->quoteInt($row['field5']).','.$this->quoteInt($row['field6']).','.$this->quoteString($row['field7']).','.$this->quoteFloat($row['field8']).','.$this->quoteFloat($row['field9']).','.$this->quoteString($row['field10']).','.$this->quoteString($row['field11']).','.$this->quoteString($row['field12']).','.$this->quoteString($row['field13']).','.$this->quoteString($row['field14']).','.$this->quoteString($row['field15']).','.$this->quoteString($row['field16']).','.$this->quoteString($row['field17']).','.$this->quoteString($row['field18']).','.$this->quoteString($row['field19']).','.$this->quoteBit($row['field20']).')';
        $first = false;
      }
      $this->realQuery($sql);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation bulk_insert.
   *
   * @param array[] $rows The rows that must inserted.
   *
   * @return void
   */
  public function tstTestBulkInsert02(?array $rows): void
  {
    $this->realQuery('call tst_test_bulk_insert02()');
    if (is_array($rows) && !empty($rows))
    {
      $sql = "INSERT INTO `TST_TEMPO`(`tst_col1`,`tst_col4`,`tst_col5`)";
      $first = true;
      foreach($rows as $row)
      {
        if ($first) $sql .=' values('.$this->quoteInt($row['field1']).','.$this->quoteInt($row['field4']).','.$this->quoteInt($row['field5']).')';
        else        $sql .=',      ('.$this->quoteInt($row['field1']).','.$this->quoteInt($row['field4']).','.$this->quoteInt($row['field5']).')';
        $first = false;
      }
      $this->realQuery($sql);
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
   * @return int|null
   */
  public function tstTestFunction(?int $pA, ?int $pB): ?int
  {
    return $this->executeSingleton0('select tst_test_function('.$this->quoteInt($pA).','.$this->quoteInt($pB).')');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for stored function with return type bool wrapper.
   *
   * @param int $pRet The return value.
   *                  int(11)
   *
   * @return bool
   */
  public function tstTestFunctionBool1(?int $pRet): bool
  {
    return !empty($this->executeSingleton0('select tst_test_function_bool1('.$this->quoteInt($pRet).')'));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for stored function with return type bool wrapper.
   *
   * @param string $pRet The return value.
   *                     varchar(8) character set utf8 collation utf8_general_ci
   *
   * @return bool
   */
  public function tstTestFunctionBool2(?string $pRet): bool
  {
    return !empty($this->executeSingleton0('select tst_test_function_bool2('.$this->quoteString($pRet).')'));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for illegal query.
   *
   * @return array[]
   */
  public function tstTestIllegalQuery(): array
  {
    return $this->executeRows('call tst_test_illegal_query()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   *
   * @param string|int[] $pIds The id's in CSV format.
   *                           varchar(255) character set utf8 collation utf8_general_ci
   *
   * @return array[]
   */
  public function tstTestListOfInt($pIds): array
  {
    $result = $this->query('call tst_test_list_of_int('.$this->quoteListOfInt($pIds, ',', '\"', '\\').')');
    $ret = [];
    while (($row = $result->fetch_array(MYSQLI_ASSOC))) $ret[$row['tst_id']] = $row;
    $result->free();
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type log.
   *
   * @return int
   */
  public function tstTestLog(): int
  {
    return $this->executeLog('call tst_test_log()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type map.
   *
   * @param int $pCount Number of rows selected.
   *                    int(11)
   *
   * @return array
   */
  public function tstTestMap1(?int $pCount): array
  {
    $result = $this->query('call tst_test_map1('.$this->quoteInt($pCount).')');
    $ret = [];
    while (($row = $result->fetch_array(MYSQLI_NUM))) $ret[$row[0]] = $row[1];
    $result->free();
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type rows_with_key with BLOB.
   *
   * @param int    $pCount Number of rows selected.
   *                       int(11)
   * @param string $pBlob  The BLOB.
   *                       blob
   *
   * @return array
   */
  public function tstTestMap1WithLob(?int $pCount, ?string $pBlob)
  {
    $query = 'call tst_test_map1_with_lob('.$this->quoteInt($pCount).',?)';
    $stmt  = $this->mysqli->prepare($query);
    if (!$stmt) $this->mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) $this->mySqlError('mysqli_stmt::bind_param');

    $this->getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    if ($this->logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');

      $this->queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');
    }

    $result = $stmt->get_result();
    $ret = [];
    while (($row = $result->fetch_array(MYSQLI_NUM))) $ret[$row[0]] = $row[1];
    $result->free();

    $stmt->close();
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for sending data larger than max_allowed_packet.
   *
   * @param string $pTmpBlob The BLOB larger than max_allowed_packet.
   *                         longblob
   *
   * @return int
   */
  public function tstTestMaxAllowedPacket(?string $pTmpBlob)
  {
    $query = 'call tst_test_max_allowed_packet(?)';
    $stmt  = $this->mysqli->prepare($query);
    if (!$stmt) $this->mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) $this->mySqlError('mysqli_stmt::bind_param');

    $this->getMaxAllowedPacket();

    $n = strlen($pTmpBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pTmpBlob, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    if ($this->logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');

      $this->queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    $this->bindAssoc($stmt, $row);

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
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    if ($b===false) $this->mySqlError('mysqli_stmt::fetch');
    if (count($tmp)!=1) throw new ResultException('1', count($tmp), $query);

    return $tmp[0][0];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   *
   * @return array
   */
  public function tstTestNoDocBlock(): array
  {
    return $this->executeRow1('call tst_test_no_doc_block()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type none.
   *
   * @param int $pCount The number of iterations.
   *                    bigint(20)
   *
   * @return int
   */
  public function tstTestNone(?int $pCount): int
  {
    return $this->executeNone('call tst_test_none('.$this->quoteInt($pCount).')');
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
   */
  public function tstTestNoneWithLob(?int $pCount, ?string $pBlob)
  {
    $query = 'call tst_test_none_with_lob('.$this->quoteInt($pCount).',?)';
    $stmt  = $this->mysqli->prepare($query);
    if (!$stmt) $this->mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) $this->mySqlError('mysqli_stmt::bind_param');

    $this->getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    if ($this->logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');

      $this->queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');
    }

    $ret = $this->mysqli->affected_rows;

    $stmt->close();
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for conversion of MySQL types to PHP types.
   *
   * @param string $pPhpType1 Must be converted to PHP type float in the TestDataLayer.
   *                          decimal(10,2)
   * @param int    $pPhpType2 Must be converted to PHP type int in the TestDataLayer.
   *                          decimal(10,0)
   *
   * @return int
   */
  public function tstTestParameterType(?string $pPhpType1, ?int $pPhpType2): int
  {
    return $this->executeNone('call tst_test_parameter_type('.$this->quoteString($pPhpType1).','.$this->quoteString($pPhpType2).')');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type row0.
   *
   * @param int $pCount The number of rows selected. * 0 For a valid test. * 1 For a valid test. * 2 For a invalid test.
   *                    int(11)
   *
   * @return array|null
   */
  public function tstTestRow0a(?int $pCount): ?array
  {
    return $this->executeRow0('call tst_test_row0a('.$this->quoteInt($pCount).')');
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
   */
  public function tstTestRow0aWithLob(?int $pCount, ?string $pBlob)
  {
    $query = 'call tst_test_row0a_with_lob('.$this->quoteInt($pCount).',?)';
    $stmt  = $this->mysqli->prepare($query);
    if (!$stmt) $this->mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) $this->mySqlError('mysqli_stmt::bind_param');

    $this->getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    if ($this->logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');

      $this->queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    $this->bindAssoc($stmt, $row);

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
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    if ($b===false) $this->mySqlError('mysqli_stmt::fetch');
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
   */
  public function tstTestRow1a(?int $pCount): array
  {
    return $this->executeRow1('call tst_test_row1a('.$this->quoteInt($pCount).')');
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
   */
  public function tstTestRow1aWithLob(?int $pCount, ?string $pBlob)
  {
    $query = 'call tst_test_row1a_with_lob('.$this->quoteInt($pCount).',?)';
    $stmt  = $this->mysqli->prepare($query);
    if (!$stmt) $this->mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) $this->mySqlError('mysqli_stmt::bind_param');

    $this->getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    if ($this->logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');

      $this->queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    $this->bindAssoc($stmt, $row);

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
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    if ($b===false) $this->mySqlError('mysqli_stmt::fetch');
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
   * @return array[]
   */
  public function tstTestRows1(?int $pCount): array
  {
    return $this->executeRows('call tst_test_rows1('.$this->quoteInt($pCount).')');
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
   * @return array[]
   */
  public function tstTestRows1WithLob(?int $pCount, ?string $pBlob)
  {
    $query = 'call tst_test_rows1_with_lob('.$this->quoteInt($pCount).',?)';
    $stmt  = $this->mysqli->prepare($query);
    if (!$stmt) $this->mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) $this->mySqlError('mysqli_stmt::bind_param');

    $this->getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    if ($this->logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');

      $this->queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    $this->bindAssoc($stmt, $row);

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
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    if ($b===false) $this->mySqlError('mysqli_stmt::fetch');

    return $tmp;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type rows_with_index.
   *
   * @param int $pCount The number of rows selected.
   *                    int(11)
   *
   * @return array[]
   */
  public function tstTestRowsWithIndex1(?int $pCount): array
  {
    $result = $this->query('call tst_test_rows_with_index1('.$this->quoteInt($pCount).')');
    $ret = [];
    while (($row = $result->fetch_array(MYSQLI_ASSOC))) $ret[$row['tst_c01']][$row['tst_c02']][] = $row;
    $result->free();
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

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
   * @return array[]
   */
  public function tstTestRowsWithIndex1WithLob(?int $pCount, ?string $pBlob)
  {
    $query = 'call tst_test_rows_with_index1_with_lob('.$this->quoteInt($pCount).',?)';
    $stmt  = $this->mysqli->prepare($query);
    if (!$stmt) $this->mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) $this->mySqlError('mysqli_stmt::bind_param');

    $this->getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    if ($this->logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');

      $this->queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    $this->bindAssoc($stmt, $row);

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
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    if ($b===false) $this->mySqlError('mysqli_stmt::fetch');

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type rows_with_key.
   *
   * @param int $pCount Number of rows selected.
   *                    int(11)
   *
   * @return array[]
   */
  public function tstTestRowsWithKey1(?int $pCount): array
  {
    $result = $this->query('call tst_test_rows_with_key1('.$this->quoteInt($pCount).')');
    $ret = [];
    while (($row = $result->fetch_array(MYSQLI_ASSOC))) $ret[$row['tst_c01']][$row['tst_c02']][$row['tst_c03']] = $row;
    $result->free();
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    return $ret;
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
   * @return array[]
   */
  public function tstTestRowsWithKey1WithLob(?int $pCount, ?string $pBlob)
  {
    $query = 'call tst_test_rows_with_key1_with_lob('.$this->quoteInt($pCount).',?)';
    $stmt  = $this->mysqli->prepare($query);
    if (!$stmt) $this->mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) $this->mySqlError('mysqli_stmt::bind_param');

    $this->getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    if ($this->logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');

      $this->queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    $this->bindAssoc($stmt, $row);

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
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    if ($b===false) $this->mySqlError('mysqli_stmt::fetch');

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type singleton0.
   *
   * @param int $pCount The number of rows selected. * 0 For a valid test. * 1 For a valid test. * 2 For a invalid test.
   *                    int(11)
   *
   * @return int|null
   */
  public function tstTestSingleton0a(?int $pCount): ?int
  {
    return $this->executeSingleton0('call tst_test_singleton0a('.$this->quoteInt($pCount).')');
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
   * @return int|null
   */
  public function tstTestSingleton0aWithLob(?int $pCount, ?string $pBlob)
  {
    $query = 'call tst_test_singleton0a_with_lob('.$this->quoteInt($pCount).',?)';
    $stmt  = $this->mysqli->prepare($query);
    if (!$stmt) $this->mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) $this->mySqlError('mysqli_stmt::bind_param');

    $this->getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    if ($this->logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');

      $this->queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    $this->bindAssoc($stmt, $row);

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
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    if ($b===false) $this->mySqlError('mysqli_stmt::fetch');
    if (count($tmp)>1) throw new ResultException('0 or 1', count($tmp), $query);

    return $tmp[0][0] ?? null;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type singleton0 with return type bool.
   *
   * @param int $pCount The number of rows selected. * 0 For a valid test. * 1 For a valid test. * 2 For a invalid test.
   *                    int(11)
   * @param int $pValue The selected value.
   *                    int(11)
   *
   * @return bool
   */
  public function tstTestSingleton0b(?int $pCount, ?int $pValue): bool
  {
    return !empty($this->executeSingleton0('call tst_test_singleton0b('.$this->quoteInt($pCount).','.$this->quoteInt($pValue).')'));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type singleton0 with BLOB..
   *
   * @param int    $pCount The number of rows selected. * 0 For a valid test. * 1 For a valid test. * 2 For a invalid test.
   *                       int(11)
   * @param int    $pValue The selected value.
   *                       int(11)
   * @param string $pBlob  The BLOB.
   *                       blob
   *
   * @return bool
   */
  public function tstTestSingleton0bWithLob(?int $pCount, ?int $pValue, ?string $pBlob)
  {
    $query = 'call tst_test_singleton0b_with_lob('.$this->quoteInt($pCount).','.$this->quoteInt($pValue).',?)';
    $stmt  = $this->mysqli->prepare($query);
    if (!$stmt) $this->mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) $this->mySqlError('mysqli_stmt::bind_param');

    $this->getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    if ($this->logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');

      $this->queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    $this->bindAssoc($stmt, $row);

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
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    if ($b===false) $this->mySqlError('mysqli_stmt::fetch');
    if (count($tmp)>1) throw new ResultException('0 or 1', count($tmp), $query);

    return !empty($tmp[0][0]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type singleton1.
   *
   * @param int $pCount The number of rows selected. * 0 For a invalid test. * 1 For a valid test. * 2 For a invalid test.
   *                    int(11)
   *
   * @return int
   */
  public function tstTestSingleton1a(?int $pCount): int
  {
    return $this->executeSingleton1('call tst_test_singleton1a('.$this->quoteInt($pCount).')');
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
   * @return int
   */
  public function tstTestSingleton1aWithLob(?int $pCount, ?string $pBlob)
  {
    $query = 'call tst_test_singleton1a_with_lob('.$this->quoteInt($pCount).',?)';
    $stmt  = $this->mysqli->prepare($query);
    if (!$stmt) $this->mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) $this->mySqlError('mysqli_stmt::bind_param');

    $this->getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    if ($this->logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');

      $this->queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    $this->bindAssoc($stmt, $row);

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
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    if ($b===false) $this->mySqlError('mysqli_stmt::fetch');
    if (count($tmp)!=1) throw new ResultException('1', count($tmp), $query);

    return $tmp[0][0];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type singleton1 with return type bool.
   *
   * @param int $pCount The number of rows selected. * 0 For a invalid test. * 1 For a valid test. * 2 For a invalid test.
   *                    int(11)
   * @param int $pValue The selected value.
   *                    int(11)
   *
   * @return bool
   */
  public function tstTestSingleton1b(?int $pCount, ?int $pValue): bool
  {
    return !empty($this->executeSingleton1('call tst_test_singleton1b('.$this->quoteInt($pCount).','.$this->quoteInt($pValue).')'));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type singleton1 with BLOB.
   *
   * @param int    $pCount The number of rows selected. * 0 For a invalid test. * 1 For a valid test. * 2 For a invalid test.
   *                       int(11)
   * @param int    $pValue The selected value.
   *                       int(11)
   * @param string $pBlob  The BLOB.
   *                       blob
   *
   * @return bool
   */
  public function tstTestSingleton1bWithLob(?int $pCount, ?int $pValue, ?string $pBlob)
  {
    $query = 'call tst_test_singleton1b_with_lob('.$this->quoteInt($pCount).','.$this->quoteInt($pValue).',?)';
    $stmt  = $this->mysqli->prepare($query);
    if (!$stmt) $this->mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('b', $null);
    if (!$b) $this->mySqlError('mysqli_stmt::bind_param');

    $this->getMaxAllowedPacket();

    $n = strlen($pBlob);
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data(0, substr($pBlob, $p, $this->chunkSize));
      if (!$b) $this->mySqlError('mysqli_stmt::send_long_data');
      $p += $this->chunkSize;
    }

    if ($this->logQueries)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');

      $this->queryLog[] = ['query' => $query,
      'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) $this->mySqlError('mysqli_stmt::execute');
    }

    $row = [];
    $this->bindAssoc($stmt, $row);

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
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    if ($b===false) $this->mySqlError('mysqli_stmt::fetch');
    if (count($tmp)!=1) throw new ResultException('1', count($tmp), $query);

    return !empty($tmp[0][0]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type table.
   *
   * @return int
   */
  public function tstTestTable(): int
  {
    return $this->executeTable('call tst_test_table()');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
