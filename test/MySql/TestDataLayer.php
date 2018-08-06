<?php
declare(strict_types=1);

namespace SetBased\Stratum\Test\MySql;

use SetBased\Stratum\Exception\ResultException;
use SetBased\Stratum\MySql\DataLayer;

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
   * @param int|null              $pTstInt       Parameter of type int.
   *                                             int(11)
   * @param int|null              $pTstSmallint  Parameter of type smallint.
   *                                             smallint(6)
   * @param int|null              $pTstTinyint   Parameter of type tinyint.
   *                                             tinyint(4)
   * @param int|null              $pTstMediumint Parameter of type mediumint.
   *                                             mediumint(9)
   * @param int|null              $pTstBigint    Parameter of type bigint.
   *                                             bigint(20)
   * @param int|float|string|null $pTstDecimal   Parameter of type decimal.
   *                                             decimal(10,2)
   * @param int|float|string|null $pTstDecimal0  Parameter of type decimal with 0 scale.
   *                                             decimal(65,0)
   * @param float|null            $pTstFloat     Parameter of type float.
   *                                             float
   * @param float|null            $pTstDouble    Parameter of type double.
   *                                             double
   * @param string|null           $pTstBit       Parameter of type bit.
   *                                             bit(8)
   * @param string|null           $pTstDate      Parameter of type date.
   *                                             date
   * @param string|null           $pTstDatetime  Parameter of type datetime.
   *                                             datetime
   * @param string|null           $pTstTimestamp Parameter of type timestamp.
   *                                             timestamp
   * @param string|null           $pTstTime      Parameter of type time.
   *                                             time
   * @param int|null              $pTstYear      Parameter of type year.
   *                                             year(4)
   * @param string|null           $pTstChar      Parameter of type char.
   *                                             char(10) character set utf8 collation utf8_general_ci
   * @param string|null           $pTstVarchar   Parameter of type varchar.
   *                                             varchar(10) character set utf8 collation utf8_general_ci
   * @param string|null           $pTstBinary    Parameter of type binary.
   *                                             binary(10)
   * @param string|null           $pTstVarbinary Parameter of type varbinary.
   *                                             varbinary(10)
   * @param string|null           $pTstEnum      Parameter of type enum.
   *                                             enum('a','b') character set utf8 collation utf8_general_ci
   * @param string|null           $pTstSet       Parameter of type set.
   *                                             set('a','b') character set utf8 collation utf8_general_ci
   *
   * @return int
   */
  public function tstTest01(?int $pTstInt, ?int $pTstSmallint, ?int $pTstTinyint, ?int $pTstMediumint, ?int $pTstBigint, $pTstDecimal, $pTstDecimal0, ?float $pTstFloat, ?float $pTstDouble, ?string $pTstBit, ?string $pTstDate, ?string $pTstDatetime, ?string $pTstTimestamp, ?string $pTstTime, ?int $pTstYear, ?string $pTstChar, ?string $pTstVarchar, ?string $pTstBinary, ?string $pTstVarbinary, ?string $pTstEnum, ?string $pTstSet): int
  {
    return $this->executeNone('call tst_test01('.$this->quoteInt($pTstInt).','.$this->quoteInt($pTstSmallint).','.$this->quoteInt($pTstTinyint).','.$this->quoteInt($pTstMediumint).','.$this->quoteInt($pTstBigint).','.$this->quoteDecimal($pTstDecimal).','.$this->quoteDecimal($pTstDecimal0).','.$this->quoteFloat($pTstFloat).','.$this->quoteFloat($pTstDouble).','.$this->quoteBit($pTstBit).','.$this->quoteString($pTstDate).','.$this->quoteString($pTstDatetime).','.$this->quoteString($pTstTimestamp).','.$this->quoteString($pTstTime).','.$this->quoteInt($pTstYear).','.$this->quoteString($pTstChar).','.$this->quoteString($pTstVarchar).','.$this->quoteBinary($pTstBinary).','.$this->quoteBinary($pTstVarbinary).','.$this->quoteString($pTstEnum).','.$this->quoteString($pTstSet).')');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for all possible types of parameters including LOB's.
   *
   * @param int|null              $pTstInt        Parameter of type int.
   *                                              int(11)
   * @param int|null              $pTstSmallint   Parameter of type smallint.
   *                                              smallint(6)
   * @param int|null              $pTstTinyint    Parameter of type tinyint.
   *                                              tinyint(4)
   * @param int|null              $pTstMediumint  Parameter of type mediumint.
   *                                              mediumint(9)
   * @param int|null              $pTstBigint     Parameter of type bigint.
   *                                              bigint(20)
   * @param int|float|string|null $pTstDecimal    Parameter of type decimal.
   *                                              decimal(10,2)
   * @param int|float|string|null $pTstDecimal0   Parameter of type decimal with 0 scale.
   *                                              decimal(65,0)
   * @param float|null            $pTstFloat      Parameter of type float.
   *                                              float
   * @param float|null            $pTstDouble     Parameter of type double.
   *                                              double
   * @param string|null           $pTstBit        Parameter of type bit.
   *                                              bit(8)
   * @param string|null           $pTstDate       Parameter of type date.
   *                                              date
   * @param string|null           $pTstDatetime   Parameter of type datetime.
   *                                              datetime
   * @param string|null           $pTstTimestamp  Parameter of type timestamp.
   *                                              timestamp
   * @param string|null           $pTstTime       Parameter of type time.
   *                                              time
   * @param int|null              $pTstYear       Parameter of type year.
   *                                              year(4)
   * @param string|null           $pTstChar       Parameter of type char.
   *                                              char(10) character set utf8 collation utf8_general_ci
   * @param string|null           $pTstVarchar    Parameter of type varchar.
   *                                              varchar(10) character set utf8 collation utf8_general_ci
   * @param string|null           $pTstBinary     Parameter of type binary.
   *                                              binary(10)
   * @param string|null           $pTstVarbinary  Parameter of type varbinary.
   *                                              varbinary(10)
   * @param string|null           $pTstTinyblob   Parameter of type tinyblob.
   *                                              tinyblob
   * @param string|null           $pTstBlob       Parameter of type blob.
   *                                              blob
   * @param string|null           $pTstMediumblob Parameter of type mediumblob.
   *                                              mediumblob
   * @param string|null           $pTstLongblob   Parameter of type longblob.
   *                                              longblob
   * @param string|null           $pTstTinytext   Parameter of type tinytext.
   *                                              tinytext character set utf8 collation utf8_general_ci
   * @param string|null           $pTstText       Parameter of type text.
   *                                              text character set utf8 collation utf8_general_ci
   * @param string|null           $pTstMediumtext Parameter of type mediumtext.
   *                                              mediumtext character set utf8 collation utf8_general_ci
   * @param string|null           $pTstLongtext   Parameter of type longtext.
   *                                              longtext character set utf8 collation utf8_general_ci
   * @param string|null           $pTstEnum       Parameter of type enum.
   *                                              enum('a','b') character set utf8 collation utf8_general_ci
   * @param string|null           $pTstSet        Parameter of type set.
   *                                              set('a','b') character set utf8 collation utf8_general_ci
   *
   * @return int
   */
  public function tstTest02(?int $pTstInt, ?int $pTstSmallint, ?int $pTstTinyint, ?int $pTstMediumint, ?int $pTstBigint, $pTstDecimal, $pTstDecimal0, ?float $pTstFloat, ?float $pTstDouble, ?string $pTstBit, ?string $pTstDate, ?string $pTstDatetime, ?string $pTstTimestamp, ?string $pTstTime, ?int $pTstYear, ?string $pTstChar, ?string $pTstVarchar, ?string $pTstBinary, ?string $pTstVarbinary, ?string $pTstTinyblob, ?string $pTstBlob, ?string $pTstMediumblob, ?string $pTstLongblob, ?string $pTstTinytext, ?string $pTstText, ?string $pTstMediumtext, ?string $pTstLongtext, ?string $pTstEnum, ?string $pTstSet)
  {
    $query = 'call tst_test02('.$this->quoteInt($pTstInt).','.$this->quoteInt($pTstSmallint).','.$this->quoteInt($pTstTinyint).','.$this->quoteInt($pTstMediumint).','.$this->quoteInt($pTstBigint).','.$this->quoteDecimal($pTstDecimal).','.$this->quoteDecimal($pTstDecimal0).','.$this->quoteFloat($pTstFloat).','.$this->quoteFloat($pTstDouble).','.$this->quoteBit($pTstBit).','.$this->quoteString($pTstDate).','.$this->quoteString($pTstDatetime).','.$this->quoteString($pTstTimestamp).','.$this->quoteString($pTstTime).','.$this->quoteInt($pTstYear).','.$this->quoteString($pTstChar).','.$this->quoteString($pTstVarchar).','.$this->quoteBinary($pTstBinary).','.$this->quoteBinary($pTstVarbinary).',?,?,?,?,?,?,?,?,'.$this->quoteString($pTstEnum).','.$this->quoteString($pTstSet).')';
    $stmt  = $this->mysqli->prepare($query);
    if (!$stmt) $this->mySqlError('mysqli::prepare');

    $null = null;
    $b = $stmt->bind_param('bbbbbbbb', $null, $null, $null, $null, $null, $null, $null, $null);
    if (!$b) $this->mySqlError('mysqli_stmt::bind_param');

    $this->getMaxAllowedPacket();

    $this->sendLongData($stmt, 0, $pTstTinyblob);
    $this->sendLongData($stmt, 1, $pTstBlob);
    $this->sendLongData($stmt, 2, $pTstMediumblob);
    $this->sendLongData($stmt, 3, $pTstLongblob);
    $this->sendLongData($stmt, 4, $pTstTinytext);
    $this->sendLongData($stmt, 5, $pTstText);
    $this->sendLongData($stmt, 6, $pTstMediumtext);
    $this->sendLongData($stmt, 7, $pTstLongtext);

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
      $sql = "INSERT INTO `TST_TEMPO`(`tst_int`,`tst_smallint`,`tst_mediumint`,`tst_tinyint`,`tst_bigint`,`tst_year`,`tst_decimal`,`tst_decimal0`,`tst_float`,`tst_double`,`tst_binary`,`tst_varbinary`,`tst_char`,`tst_varchar`,`tst_time`,`tst_timestamp`,`tst_date`,`tst_datetime`,`tst_enum`,`tst_set`,`tst_bit`)";
      $first = true;
      foreach($rows as $row)
      {
        if ($first) $sql .=' values('.$this->quoteInt($row['field_int']).','.$this->quoteInt($row['field_smallint']).','.$this->quoteInt($row['field_mediumint']).','.$this->quoteInt($row['field_tinyint']).','.$this->quoteInt($row['field_bigint']).','.$this->quoteInt($row['field_year']).','.$this->quoteDecimal($row['field_decimal']).','.$this->quoteDecimal($row['field_decimal0']).','.$this->quoteFloat($row['field_float']).','.$this->quoteFloat($row['field_double']).','.$this->quoteBinary($row['field_binary']).','.$this->quoteBinary($row['field_varbinary']).','.$this->quoteString($row['field_char']).','.$this->quoteString($row['field_varchar']).','.$this->quoteString($row['field_time']).','.$this->quoteString($row['field_timestamp']).','.$this->quoteString($row['field_date']).','.$this->quoteString($row['field_datetime']).','.$this->quoteString($row['field_enum']).','.$this->quoteString($row['field_set']).','.$this->quoteBit($row['field_bit']).')';
        else        $sql .=',      ('.$this->quoteInt($row['field_int']).','.$this->quoteInt($row['field_smallint']).','.$this->quoteInt($row['field_mediumint']).','.$this->quoteInt($row['field_tinyint']).','.$this->quoteInt($row['field_bigint']).','.$this->quoteInt($row['field_year']).','.$this->quoteDecimal($row['field_decimal']).','.$this->quoteDecimal($row['field_decimal0']).','.$this->quoteFloat($row['field_float']).','.$this->quoteFloat($row['field_double']).','.$this->quoteBinary($row['field_binary']).','.$this->quoteBinary($row['field_varbinary']).','.$this->quoteString($row['field_char']).','.$this->quoteString($row['field_varchar']).','.$this->quoteString($row['field_time']).','.$this->quoteString($row['field_timestamp']).','.$this->quoteString($row['field_date']).','.$this->quoteString($row['field_datetime']).','.$this->quoteString($row['field_enum']).','.$this->quoteString($row['field_set']).','.$this->quoteBit($row['field_bit']).')';
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
   * @param int|null $pA Parameter A.
   *                     int(11)
   * @param int|null $pB Parameter B.
   *                     int(11)
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
   * @param int|null $pRet The return value.
   *                       int(11)
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
   * @param string|null $pRet The return value.
   *                          varchar(8) character set utf8 collation utf8_general_ci
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
   * @param string|int[]|null $pIds The id's in CSV format.
   *                                varchar(255) character set utf8 collation utf8_general_ci
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
   * @param int|null $pCount Number of rows selected.
   *                         int(11)
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
   * @param int|null    $pCount Number of rows selected.
   *                            int(11)
   * @param string|null $pBlob  The BLOB.
   *                            blob
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

    $this->sendLongData($stmt, 0, $pBlob);

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
   * @param string|null $pTmpBlob The BLOB larger than max_allowed_packet.
   *                              longblob
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

    $this->sendLongData($stmt, 0, $pTmpBlob);

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
   * @param int|null $pCount The number of iterations.
   *                         bigint(20)
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
   * @param int|null    $pCount The number of iterations.
   *                            bigint(20)
   * @param string|null $pBlob  The BLOB.
   *                            blob
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

    $this->sendLongData($stmt, 0, $pBlob);

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
   * @param int|float|string|null $pPhpType1 Must be converted to PHP type string in the TestDataLayer.
   *                                         decimal(10,2)
   * @param int|float|string|null $pPhpType2 Must be converted to PHP type string in the TestDataLayer.
   *                                         decimal(65,0)
   *
   * @return int
   */
  public function tstTestParameterType($pPhpType1, $pPhpType2): int
  {
    return $this->executeNone('call tst_test_parameter_type('.$this->quoteDecimal($pPhpType1).','.$this->quoteDecimal($pPhpType2).')');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type row0.
   *
   * @param int|null $pCount The number of rows selected. * 0 For a valid test. * 1 For a valid test. * 2 For a invalid test.
   *                         int(11)
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
   * @param int|null    $pCount The number of rows selected. * 0 For a valid test. * 1 For a valid test. * 2 For a invalid test.
   *                            int(11)
   * @param string|null $pBlob  The BLOB.
   *                            blob
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

    $this->sendLongData($stmt, 0, $pBlob);

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
   * @param int|null $pCount The number of rows selected. * 0 For a invalid test. * 1 For a valid test. * 2 For a invalid test.
   *                         int(11)
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
   * @param int|null    $pCount The number of rows selected. * 0 For a invalid test. * 1 For a valid test. * 2 For a invalid test.
   *                            int(11)
   * @param string|null $pBlob  The BLOB.
   *                            blob
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

    $this->sendLongData($stmt, 0, $pBlob);

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
   * @param int|null $pCount The number of rows selected. * 0 For a invalid test. * 1 For a valid test. * 2 For a invalid test.
   *                         int(11)
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
   * @param int|null    $pCount The number of rows selected.
   *                            int(11)
   * @param string|null $pBlob  The BLOB.
   *                            blob
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

    $this->sendLongData($stmt, 0, $pBlob);

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
   * @param int|null $pCount The number of rows selected.
   *                         int(11)
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
   * @param int|null    $pCount The number of rows selected.
   *                            int(11)
   * @param string|null $pBlob  The BLOB.
   *                            blob
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

    $this->sendLongData($stmt, 0, $pBlob);

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
   * @param int|null $pCount Number of rows selected.
   *                         int(11)
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
   * @param int|null    $pCount The number of rows selected.
   *                            int(11)
   * @param string|null $pBlob  The BLOB.
   *                            blob
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

    $this->sendLongData($stmt, 0, $pBlob);

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
   * @param int|null $pCount The number of rows selected. * 0 For a valid test. * 1 For a valid test. * 2 For a invalid test.
   *                         int(11)
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
   * @param int|null    $pCount The number of rows selected. * 0 For a valid test. * 1 For a valid test. * 2 For a invalid test.
   *                            int(11)
   * @param string|null $pBlob  The BLOB.
   *                            blob
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

    $this->sendLongData($stmt, 0, $pBlob);

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
   * @param int|null $pCount The number of rows selected. * 0 For a valid test. * 1 For a valid test. * 2 For a invalid test.
   *                         int(11)
   * @param int|null $pValue The selected value.
   *                         int(11)
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
   * @param int|null    $pCount The number of rows selected. * 0 For a valid test. * 1 For a valid test. * 2 For a invalid test.
   *                            int(11)
   * @param int|null    $pValue The selected value.
   *                            int(11)
   * @param string|null $pBlob  The BLOB.
   *                            blob
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

    $this->sendLongData($stmt, 0, $pBlob);

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
   * @param int|null $pCount The number of rows selected. * 0 For a invalid test. * 1 For a valid test. * 2 For a invalid test.
   *                         int(11)
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
   * @param int|null    $pCount The number of rows selected. * 0 For a invalid test. * 1 For a valid test. * 2 For a invalid test.
   *                            int(11)
   * @param string|null $pBlob  The BLOB.
   *                            blob
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

    $this->sendLongData($stmt, 0, $pBlob);

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
   * @param int|null $pCount The number of rows selected. * 0 For a invalid test. * 1 For a valid test. * 2 For a invalid test.
   *                         int(11)
   * @param int|null $pValue The selected value.
   *                         int(11)
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
   * @param int|null    $pCount The number of rows selected. * 0 For a invalid test. * 1 For a valid test. * 2 For a invalid test.
   *                            int(11)
   * @param int|null    $pValue The selected value.
   *                            int(11)
   * @param string|null $pBlob  The BLOB.
   *                            blob
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

    $this->sendLongData($stmt, 0, $pBlob);

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
