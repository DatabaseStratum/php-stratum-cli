<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

use SetBased\Exception\RuntimeException;
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
   * @return string|null
   */
  public function tstMagicConstant01()
  {
    return $this->executeSingleton1('CALL tst_magic_constant01()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string|null
   */
  public function tstMagicConstant02()
  {
    return $this->executeSingleton1('CALL tst_magic_constant02()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string|null
   */
  public function tstMagicConstant03()
  {
    return $this->executeSingleton1('CALL tst_magic_constant03()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string|null
   */
  public function tstMagicConstant04()
  {
    return $this->executeSingleton1('CALL tst_magic_constant04()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string|null
   */
  public function tstMagicConstant05()
  {
    return $this->executeSingleton1('CALL tst_magic_constant05()');
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
   */
  public function tstTest01($pParam00, $pParam01, $pParam02, $pParam03, $pParam04, $pParam05, $pParam06, $pParam07, $pParam08, $pParam09, $pParam10, $pParam11, $pParam12, $pParam13, $pParam14, $pParam15, $pParam16, $pParam17, $pParam26, $pParam27)
  {
    return $this->executeNone('CALL tst_test01('.$this->quoteNum($pParam00).','.$this->quoteNum($pParam01).','.$this->quoteNum($pParam02).','.$this->quoteNum($pParam03).','.$this->quoteNum($pParam04).','.$this->quoteNum($pParam05).','.$this->quoteNum($pParam06).','.$this->quoteNum($pParam07).','.$this->quoteBit($pParam08).','.$this->quoteString($pParam09).','.$this->quoteString($pParam10).','.$this->quoteString($pParam11).','.$this->quoteString($pParam12).','.$this->quoteNum($pParam13).','.$this->quoteString($pParam14).','.$this->quoteString($pParam15).','.$this->quoteString($pParam16).','.$this->quoteString($pParam17).','.$this->quoteString($pParam26).','.$this->quoteString($pParam27).')');
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
   */
  public function tstTest02($pParam00, $pParam01, $pParam02, $pParam03, $pParam04, $pParam05, $pParam06, $pParam07, $pParam08, $pParam09, $pParam10, $pParam11, $pParam12, $pParam13, $pParam14, $pParam15, $pParam16, $pParam17, $pParam18, $pParam19, $pParam20, $pParam21, $pParam22, $pParam23, $pParam24, $pParam25, $pParam26, $pParam27)
  {
    $query = 'CALL tst_test02('.$this->quoteNum($pParam00).','.$this->quoteNum($pParam01).','.$this->quoteNum($pParam02).','.$this->quoteNum($pParam03).','.$this->quoteNum($pParam04).','.$this->quoteNum($pParam05).','.$this->quoteNum($pParam06).','.$this->quoteNum($pParam07).','.$this->quoteBit($pParam08).','.$this->quoteString($pParam09).','.$this->quoteString($pParam10).','.$this->quoteString($pParam11).','.$this->quoteString($pParam12).','.$this->quoteNum($pParam13).','.$this->quoteString($pParam14).','.$this->quoteString($pParam15).','.$this->quoteString($pParam16).','.$this->quoteString($pParam17).',?,?,?,?,?,?,?,?,'.$this->quoteString($pParam26).','.$this->quoteString($pParam27).')';
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
   * @param array $rows
   */
  public function tstTestBulkInsert01($rows)
  {
    $this->query('CALL tst_test_bulk_insert01()');
    if (is_array($rows) && !empty($rows))
    {
      $sql = "INSERT INTO `TST_TEMPO`(`tst_col1`,`tst_col2`,`tst_col3`,`tst_col4`,`tst_col5`,`tst_col6`,`tst_col7`,`tst_col8`,`tst_col9`,`tst_col10`,`tst_col11`,`tst_col12`,`tst_col13`,`tst_col14`,`tst_col15`,`tst_col16`,`tst_col17`,`tst_col18`,`tst_col19`,`tst_col20`)";
      $first = true;
      foreach($rows as $row)
      {
        if ($first) $sql .=' values('.$this->quoteNum($row['field1']).','.$this->quoteNum($row['field2']).','.$this->quoteNum($row['field3']).','.$this->quoteNum($row['field4']).','.$this->quoteNum($row['field5']).','.$this->quoteNum($row['field6']).','.$this->quoteNum($row['field7']).','.$this->quoteNum($row['field8']).','.$this->quoteNum($row['field9']).','.$this->quoteString($row['field10']).','.$this->quoteString($row['field11']).','.$this->quoteString($row['field12']).','.$this->quoteString($row['field13']).','.$this->quoteString($row['field14']).','.$this->quoteString($row['field15']).','.$this->quoteString($row['field16']).','.$this->quoteString($row['field17']).','.$this->quoteString($row['field18']).','.$this->quoteString($row['field19']).','.$this->quoteBit($row['field20']).')';
        else        $sql .=',      ('.$this->quoteNum($row['field1']).','.$this->quoteNum($row['field2']).','.$this->quoteNum($row['field3']).','.$this->quoteNum($row['field4']).','.$this->quoteNum($row['field5']).','.$this->quoteNum($row['field6']).','.$this->quoteNum($row['field7']).','.$this->quoteNum($row['field8']).','.$this->quoteNum($row['field9']).','.$this->quoteString($row['field10']).','.$this->quoteString($row['field11']).','.$this->quoteString($row['field12']).','.$this->quoteString($row['field13']).','.$this->quoteString($row['field14']).','.$this->quoteString($row['field15']).','.$this->quoteString($row['field16']).','.$this->quoteString($row['field17']).','.$this->quoteString($row['field18']).','.$this->quoteString($row['field19']).','.$this->quoteBit($row['field20']).')';
        $first = false;
      }
      $this->query($sql);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation bulk_insert.
   *
   * @param array $rows
   */
  public function tstTestBulkInsert02($rows)
  {
    $this->query('CALL tst_test_bulk_insert02()');
    if (is_array($rows) && !empty($rows))
    {
      $sql = "INSERT INTO `TST_TEMPO`(`tst_col1`,`tst_col4`,`tst_col5`)";
      $first = true;
      foreach($rows as $row)
      {
        if ($first) $sql .=' values('.$this->quoteNum($row['field1']).','.$this->quoteNum($row['field4']).','.$this->quoteNum($row['field5']).')';
        else        $sql .=',      ('.$this->quoteNum($row['field1']).','.$this->quoteNum($row['field4']).','.$this->quoteNum($row['field5']).')';
        $first = false;
      }
      $this->query($sql);
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
   */
  public function tstTestFunction($pA, $pB)
  {
    return $this->executeSingleton0('SELECT tst_test_function('.$this->quoteNum($pA).','.$this->quoteNum($pB).')');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for illegal query.
   *
   * @return \array[]
   */
  public function tstTestIllegalQuery()
  {
    return $this->executeRows('CALL tst_test_illegal_query()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   *
   * @param string|int[] $pIds The id's in CSV format.
   *                           varchar(255) character set latin1 collation latin1_swedish_ci
   *
   * @return \array[]
   */
  public function tstTestListOfInt($pIds)
  {
    $result = $this->query('CALL tst_test_list_of_int('.$this->quoteListOfInt($pIds, ',', '\"', '\\').')');
    $ret = [];
    while($row = $result->fetch_array(MYSQLI_ASSOC)) $ret[$row['tst_id']] = $row;
    $result->free();
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

    return  $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type log.
   *
   * @return int
   */
  public function tstTestLog()
  {
    return $this->executeLog('CALL tst_test_log()');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for sending data larger than max_allowed_packet.
   *
   * @param string $pTmpBlob The BLOB larger than max_allowed_packet.
   *                         longblob
   *
   * @return string|null
   */
  public function tstTestMaxAllowedPacket($pTmpBlob)
  {
    $query = 'CALL tst_test_max_allowed_packet(?)';
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
  public function tstTestNoDocBlock()
  {
    return $this->executeRow1('CALL tst_test_no_doc_block()');
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
  public function tstTestNone($pCount)
  {
    return $this->executeNone('CALL tst_test_none('.$this->quoteNum($pCount).')');
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
  public function tstTestNoneWithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_none_with_lob('.$this->quoteNum($pCount).',?)';
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
   * @param float $pPhpType1 Must be converted to PHP type float in the TestDataLayer.
   *                         decimal(10,2)
   * @param int   $pPhpType2 Must be converted to PHP type int in the TestDataLayer.
   *                         decimal(10,0)
   *
   * @return int
   */
  public function tstTestParameterType($pPhpType1, $pPhpType2)
  {
    return $this->executeNone('CALL tst_test_parameter_type('.$this->quoteNum($pPhpType1).','.$this->quoteNum($pPhpType2).')');
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
  public function tstTestRow0a($pCount)
  {
    return $this->executeRow0('CALL tst_test_row0a('.$this->quoteNum($pCount).')');
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
  public function tstTestRow0aWithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_row0a_with_lob('.$this->quoteNum($pCount).',?)';
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
  public function tstTestRow1a($pCount)
  {
    return $this->executeRow1('CALL tst_test_row1a('.$this->quoteNum($pCount).')');
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
  public function tstTestRow1aWithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_row1a_with_lob('.$this->quoteNum($pCount).',?)';
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
   * @return \array[]
   */
  public function tstTestRows1($pCount)
  {
    return $this->executeRows('CALL tst_test_rows1('.$this->quoteNum($pCount).')');
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
   */
  public function tstTestRows1WithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_rows1_with_lob('.$this->quoteNum($pCount).',?)';
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
   * @return \array[]
   */
  public function tstTestRowsWithIndex1($pCount)
  {
    $result = $this->query('CALL tst_test_rows_with_index1('.$this->quoteNum($pCount).')');
    $ret = [];
    while($row = $result->fetch_array(MYSQLI_ASSOC)) $ret[$row['tst_c01']][$row['tst_c02']][] = $row;
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
   * @return \array[]
   */
  public function tstTestRowsWithIndex1WithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_rows_with_index1_with_lob('.$this->quoteNum($pCount).',?)';
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
   * @return \array[]
   */
  public function tstTestRowsWithKey1($pCount)
  {
    $result = $this->query('CALL tst_test_rows_with_key1('.$this->quoteNum($pCount).')');
    $ret = [];
    while($row = $result->fetch_array(MYSQLI_ASSOC)) $ret[$row['tst_c01']][$row['tst_c02']][$row['tst_c03']] = $row;
    $result->free();
    if ($this->mysqli->more_results()) $this->mysqli->next_result();

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
   */
  public function tstTestRowsWithKey1WithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_rows_with_key1_with_lob('.$this->quoteNum($pCount).',?)';
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
   * @return string|null
   */
  public function tstTestSingleton0a($pCount)
  {
    return $this->executeSingleton0('CALL tst_test_singleton0a('.$this->quoteNum($pCount).')');
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
   */
  public function tstTestSingleton0aWithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_singleton0a_with_lob('.$this->quoteNum($pCount).',?)';
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
   */
  public function tstTestSingleton1a($pCount)
  {
    return $this->executeSingleton1('CALL tst_test_singleton1a('.$this->quoteNum($pCount).')');
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
   */
  public function tstTestSingleton1aWithLob($pCount, $pBlob)
  {
    $query = 'CALL tst_test_singleton1a_with_lob('.$this->quoteNum($pCount).',?)';
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
   * Test for designation type table.
   *
   * @return int
   */
  public function tstTestTable()
  {
    return $this->executeTable('CALL tst_test_table()');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
