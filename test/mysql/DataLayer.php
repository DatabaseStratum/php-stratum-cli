<?php
//----------------------------------------------------------------------------------------------------------------------
use \SetBased\Stratum\Exception\RunTimeException;
use \SetBased\Stratum\Exception\ResultException;
use \SetBased\Stratum\MySql\StaticDataLayer;

//----------------------------------------------------------------------------------------------------------------------
class DataLayer extends StaticDataLayer
{
  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function magicConstant01(  )
  {
    return self::executeSingleton1( 'CALL tst_magic_constant01()');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function magicConstant02(  )
  {
    return self::executeSingleton1( 'CALL tst_magic_constant02()');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function magicConstant03(  )
  {
    return self::executeSingleton1( 'CALL tst_magic_constant03()');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function magicConstant04(  )
  {
    return self::executeSingleton1( 'CALL tst_magic_constant04()');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function magicConstant05(  )
  {
    return self::executeSingleton1( 'CALL tst_magic_constant05()');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for all possible types of parameters excluding LOB's.
   *
   * @param int    $p_param00 Test parameter 00.
   *                          int(11)
   * @param int    $p_param01 Test parameter 01.
   *                          smallint(6)
   * @param int    $p_param02 Test parameter 02.
   *                          tinyint(4)
   * @param int    $p_param03 Test parameter 03.
   *                          mediumint(9)
   * @param int    $p_param04 Test parameter 04.
   *                          bigint(20)
   * @param float  $p_param05 Test parameter 05.
   *                          decimal(10,2)
   * @param float  $p_param06 Test parameter 06.
   *                          float
   * @param float  $p_param07 Test parameter 07.
   *                          double
   * @param int    $p_param08 Test parameter 08.
   *                          bit(8)
   * @param string $p_param09 Test parameter 09.
   *                          date
   * @param string $p_param10 Test parameter 10.
   *                          datetime
   * @param string $p_param11 Test parameter 11.
   *                          timestamp
   * @param string $p_param12 Test parameter 12.
   *                          time
   * @param int    $p_param13 Test parameter 13.
   *                          year(4)
   * @param string $p_param14 Test parameter 14.
   *                          char(10) character set utf8 collation utf8_general_ci
   * @param string $p_param15 Test parameter 15.
   *                          varchar(10) character set utf8 collation utf8_general_ci
   * @param string $p_param16 Test parameter 16.
   *                          binary(10)
   * @param string $p_param17 Test parameter 17.
   *                          varbinary(10)
   * @param string $p_param26 Test parameter 26.
   *                          enum('a','b') character set utf8 collation utf8_general_ci
   * @param string $p_param27 Test parameter 27.
   *                          set('a','b') character set utf8 collation utf8_general_ci
   *
   * @return int
   * @throws RunTimeException
   */
  public static function test01( $p_param00, $p_param01, $p_param02, $p_param03, $p_param04, $p_param05, $p_param06, $p_param07, $p_param08, $p_param09, $p_param10, $p_param11, $p_param12, $p_param13, $p_param14, $p_param15, $p_param16, $p_param17, $p_param26, $p_param27 )
  {
    return self::executeNone( 'CALL tst_test01('.self::quoteNum( $p_param00 ).','.self::quoteNum( $p_param01 ).','.self::quoteNum( $p_param02 ).','.self::quoteNum( $p_param03 ).','.self::quoteNum( $p_param04 ).','.self::quoteNum( $p_param05 ).','.self::quoteNum( $p_param06 ).','.self::quoteNum( $p_param07 ).','.self::quoteBit( $p_param08 ).','.self::quoteString( $p_param09 ).','.self::quoteString( $p_param10 ).','.self::quoteString( $p_param11 ).','.self::quoteString( $p_param12 ).','.self::quoteNum( $p_param13 ).','.self::quoteString( $p_param14 ).','.self::quoteString( $p_param15 ).','.self::quoteString( $p_param16 ).','.self::quoteString( $p_param17 ).','.self::quoteString( $p_param26 ).','.self::quoteString( $p_param27 ).')' );
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for all possible types of parameters including LOB's.
   *
   * @param int    $p_param00 Test parameter 00.
   *                          int(11)
   * @param int    $p_param01 Test parameter 01.
   *                          smallint(6)
   * @param int    $p_param02 Test parameter 02.
   *                          tinyint(4)
   * @param int    $p_param03 Test parameter 03.
   *                          mediumint(9)
   * @param int    $p_param04 Test parameter 04.
   *                          bigint(20)
   * @param float  $p_param05 Test parameter 05.
   *                          decimal(10,2)
   * @param float  $p_param06 Test parameter 06.
   *                          float
   * @param float  $p_param07 Test parameter 07.
   *                          double
   * @param int    $p_param08 Test parameter 08.
   *                          bit(8)
   * @param string $p_param09 Test parameter 09.
   *                          date
   * @param string $p_param10 Test parameter 10.
   *                          datetime
   * @param string $p_param11 Test parameter 11.
   *                          timestamp
   * @param string $p_param12 Test parameter 12.
   *                          time
   * @param int    $p_param13 Test parameter 13.
   *                          year(4)
   * @param string $p_param14 Test parameter 14.
   *                          char(10) character set utf8 collation utf8_general_ci
   * @param string $p_param15 Test parameter 15.
   *                          varchar(10) character set utf8 collation utf8_general_ci
   * @param string $p_param16 Test parameter 16.
   *                          binary(10)
   * @param string $p_param17 Test parameter 17.
   *                          varbinary(10)
   * @param string $p_param18 Test parameter 18.
   *                          tinyblob
   * @param string $p_param19 Test parameter 19.
   *                          blob
   * @param string $p_param20 Test parameter 20.
   *                          mediumblob
   * @param string $p_param21 Test parameter 21.
   *                          longblob
   * @param string $p_param22 Test parameter 22.
   *                          tinytext character set utf8 collation utf8_general_ci
   * @param string $p_param23 Test parameter 23.
   *                          text character set utf8 collation utf8_general_ci
   * @param string $p_param24 Test parameter 24.
   *                          mediumtext character set utf8 collation utf8_general_ci
   * @param string $p_param25 Test parameter 25.
   *                          longtext character set utf8 collation utf8_general_ci
   * @param string $p_param26 Test parameter 26.
   *                          enum('a','b') character set utf8 collation utf8_general_ci
   * @param string $p_param27 Test parameter 27.
   *                          set('a','b') character set utf8 collation utf8_general_ci
   *
   * @return int
   * @throws RunTimeException
   */
  public static function test02( $p_param00, $p_param01, $p_param02, $p_param03, $p_param04, $p_param05, $p_param06, $p_param07, $p_param08, $p_param09, $p_param10, $p_param11, $p_param12, $p_param13, $p_param14, $p_param15, $p_param16, $p_param17, $p_param18, $p_param19, $p_param20, $p_param21, $p_param22, $p_param23, $p_param24, $p_param25, $p_param26, $p_param27 )
  {
    $query = 'CALL tst_test02( '.self::quoteNum( $p_param00 ).','.self::quoteNum( $p_param01 ).','.self::quoteNum( $p_param02 ).','.self::quoteNum( $p_param03 ).','.self::quoteNum( $p_param04 ).','.self::quoteNum( $p_param05 ).','.self::quoteNum( $p_param06 ).','.self::quoteNum( $p_param07 ).','.self::quoteBit( $p_param08 ).','.self::quoteString( $p_param09 ).','.self::quoteString( $p_param10 ).','.self::quoteString( $p_param11 ).','.self::quoteString( $p_param12 ).','.self::quoteNum( $p_param13 ).','.self::quoteString( $p_param14 ).','.self::quoteString( $p_param15 ).','.self::quoteString( $p_param16 ).','.self::quoteString( $p_param17 ).',?,?,?,?,?,?,?,?,'.self::quoteString( $p_param26 ).','.self::quoteString( $p_param27 ).' )';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::mySqlError( 'mysqli::prepare' );

    $null = null;
    $b = $stmt->bind_param( 'bbbbbbbb', $null,$null,$null,$null,$null,$null,$null,$null );
    if (!$b) self::mySqlError( 'mysqli_stmt::bind_param' );

    self::getMaxAllowedPacket();

    $n = strlen( $p_param18 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $p_param18, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    $n = strlen( $p_param19 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 1, substr( $p_param19, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    $n = strlen( $p_param20 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 2, substr( $p_param20, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    $n = strlen( $p_param21 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 3, substr( $p_param21, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    $n = strlen( $p_param22 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 4, substr( $p_param22, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    $n = strlen( $p_param23 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 5, substr( $p_param23, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    $n = strlen( $p_param24 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 6, substr( $p_param24, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    $n = strlen( $p_param25 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 7, substr( $p_param25, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    if (self::$ourQueryLogFlag)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );

      self::$ourQueryLog[] = ['query' => $query,
                              'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );
    }

    $ret = self::$ourMySql->affected_rows;

    $stmt->close();
    if(self::$ourMySql->more_results()) self::$ourMySql->next_result();

    return $ret;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation bulk_insert.
   *
   * @param array $theData
   * @throws RunTimeException
   */
  public static function testBulkInsert01( $theData )
  {
    self::query( 'CALL tst_test_bulk_insert01()');
    if (is_array($theData) &&!empty($theData))
    {
      $sql = "INSERT INTO `TST_TEMPO`(`tst_col1`,`tst_col2`,`tst_col3`,`tst_col4`,`tst_col5`,`tst_col6`,`tst_col7`,`tst_col8`,`tst_col9`,`tst_col10`,`tst_col11`,`tst_col12`,`tst_col13`,`tst_col14`,`tst_col15`,`tst_col16`,`tst_col17`,`tst_col18`,`tst_col19`,`tst_col20`)";
      $first = true;
      foreach( $theData as $row )
      {
        if ($first) $sql .=' values('.self::quoteNum($row['field1']).','.self::quoteNum($row['field2']).','.self::quoteNum($row['field3']).','.self::quoteNum($row['field4']).','.self::quoteNum($row['field5']).','.self::quoteNum($row['field6']).','.self::quoteNum($row['field7']).','.self::quoteNum($row['field8']).','.self::quoteNum($row['field9']).','.self::quoteString($row['field10']).','.self::quoteString($row['field11']).','.self::quoteString($row['field12']).','.self::quoteString($row['field13']).','.self::quoteString($row['field14']).','.self::quoteString($row['field15']).','.self::quoteString($row['field16']).','.self::quoteString($row['field17']).','.self::quoteString($row['field18']).','.self::quoteString($row['field19']).','.self::quoteBit($row['field20']).')';
        else        $sql .=',      ('.self::quoteNum($row['field1']).','.self::quoteNum($row['field2']).','.self::quoteNum($row['field3']).','.self::quoteNum($row['field4']).','.self::quoteNum($row['field5']).','.self::quoteNum($row['field6']).','.self::quoteNum($row['field7']).','.self::quoteNum($row['field8']).','.self::quoteNum($row['field9']).','.self::quoteString($row['field10']).','.self::quoteString($row['field11']).','.self::quoteString($row['field12']).','.self::quoteString($row['field13']).','.self::quoteString($row['field14']).','.self::quoteString($row['field15']).','.self::quoteString($row['field16']).','.self::quoteString($row['field17']).','.self::quoteString($row['field18']).','.self::quoteString($row['field19']).','.self::quoteBit($row['field20']).')';
        $first = false;
      }
      self::query( $sql );
    }
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation bulk_insert.
   *
   * @param array $theData
   * @throws RunTimeException
   */
  public static function testBulkInsert02( $theData )
  {
    self::query( 'CALL tst_test_bulk_insert02()');
    if (is_array($theData) &&!empty($theData))
    {
      $sql = "INSERT INTO `TST_TEMPO`(`tst_col1`,`tst_col4`,`tst_col5`)";
      $first = true;
      foreach( $theData as $row )
      {
        if ($first) $sql .=' values('.self::quoteNum($row['field1']).','.self::quoteNum($row['field4']).','.self::quoteNum($row['field5']).')';
        else        $sql .=',      ('.self::quoteNum($row['field1']).','.self::quoteNum($row['field4']).','.self::quoteNum($row['field5']).')';
        $first = false;
      }
      self::query( $sql );
    }
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for stored function wrapper.
   *
   * @param int $p_a Parameter A.
   *                 int(11)
   * @param int $p_b Parameter B.
   *                 int(11)
   *
   * @return string
   * @throws RunTimeException
   */
  public static function testFunction( $p_a, $p_b )
  {
    return self::executeSingleton0( 'SELECT tst_test_function('.self::quoteNum( $p_a ).','.self::quoteNum( $p_b ).') ' );
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   *
   * @param string|int[] $p_ids The id's in CSV format.
   *                            varchar(255) character set utf8 collation utf8_general_ci
   *
   * @return array[]
   * @throws RunTimeException
   */
  public static function testListOfInt( $p_ids )
  {
    $result = self::query( 'CALL tst_test_list_of_int('.self::quoteListOfInt( $p_ids, ',', '\"', '\\' ).')');
    $ret = array();
    while($row = $result->fetch_array( MYSQLI_ASSOC )) $ret[$row['tst_id']] = $row;
    $result->free();
    if(self::$ourMySql->more_results()) self::$ourMySql->next_result();

    return  $ret;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type log.
   *
   * @return int
   * @throws RunTimeException
   */
  public static function testLog(  )
  {
    return self::executeLog( 'CALL tst_test_log()' );
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for sending data larger than max_allowed_packet.
   *
   * @param string $p_tmp_blob The BLOB larger than max_allowed_packet.
   *                           longblob
   *
   * @return string
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function testMaxAllowedPacket( $p_tmp_blob )
  {
    $query = 'CALL tst_test_max_allowed_packet( ? )';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::mySqlError( 'mysqli::prepare' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::mySqlError( 'mysqli_stmt::bind_param' );

    self::getMaxAllowedPacket();

    $n = strlen( $p_tmp_blob );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $p_tmp_blob, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    if (self::$ourQueryLogFlag)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );

      self::$ourQueryLog[] = ['query' => $query,
                              'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );
    }

    $row = array();
    self::bindAssoc( $stmt, $row );

    $tmp = array();
    while (($b = $stmt->fetch()))
    {
      $new = array();
      foreach( $row as $value )
      {
        $new[] = $value;
      }
      $tmp[] = $new;
    }

    $stmt->close();
    if(self::$ourMySql->more_results()) self::$ourMySql->next_result();

    if ($b===false) self::mySqlError( 'mysqli_stmt::fetch' );
    if (count($tmp)!=1) throw new ResultException( '1', count($tmp), $query );

    return $tmp[0][0];
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type none.
   *
   * @param int $p_count The number of iterations.
   *                     bigint(20)
   *
   * @return int
   * @throws RunTimeException
   */
  public static function testNone( $p_count )
  {
    return self::executeNone( 'CALL tst_test_none('.self::quoteNum( $p_count ).')' );
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type none.
   *
   * @param int $p_count The number of iterations.
   *                     bigint(20)
   *
   * @return int
   * @throws RunTimeException
   */
  public static function testNone2( $p_count )
  {
    return self::executeNone( 'CALL foo_test_none2('.self::quoteNum( $p_count ).')' );
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type none with BLOB.
   *
   * @param int    $p_count The number of iterations.
   *                        bigint(20)
   * @param string $p_blob  The BLOB.
   *                        blob
   *
   * @return int
   * @throws RunTimeException
   */
  public static function testNoneWithLob( $p_count, $p_blob )
  {
    $query = 'CALL tst_test_none_with_lob( '.self::quoteNum( $p_count ).',? )';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::mySqlError( 'mysqli::prepare' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::mySqlError( 'mysqli_stmt::bind_param' );

    self::getMaxAllowedPacket();

    $n = strlen( $p_blob );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $p_blob, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    if (self::$ourQueryLogFlag)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );

      self::$ourQueryLog[] = ['query' => $query,
                              'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );
    }

    $ret = self::$ourMySql->affected_rows;

    $stmt->close();
    if(self::$ourMySql->more_results()) self::$ourMySql->next_result();

    return $ret;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for conversion of MySQL types to PHP types.
   *
   * @param float $p_php_type1 Must be converted to PHP type float in the DataLayer.
   *                           decimal(10,2)
   * @param int   $p_php_type2 Must be converted to PHP type int in the DataLayer.
   *                           decimal(10,0)
   *
   * @return int
   * @throws RunTimeException
   */
  public static function testParameterType( $p_php_type1, $p_php_type2 )
  {
    return self::executeNone( 'CALL tst_test_parameter_type('.self::quoteNum( $p_php_type1 ).','.self::quoteNum( $p_php_type2 ).')' );
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type row0.
   *
   * @param int $p_count The number of rows selected.
   *                     * 0 For a valid test.
   *                     * 1 For a valid test.
   *                     * 2 For a invalid test.
   *                     int(11)
   *
   * @return array
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function testRow0a( $p_count )
  {
    return self::executeRow0( 'CALL tst_test_row0a('.self::quoteNum( $p_count ).')');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type row0 with BLOB.
   *
   * @param int    $p_count The number of rows selected.
   *                        * 0 For a valid test.
   *                        * 1 For a valid test.
   *                        * 2 For a invalid test.
   *                        int(11)
   * @param string $p_blob  The BLOB.
   *                        blob
   *
   * @return array
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function testRow0aWithLob( $p_count, $p_blob )
  {
    $query = 'CALL tst_test_row0a_with_lob( '.self::quoteNum( $p_count ).',? )';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::mySqlError( 'mysqli::prepare' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::mySqlError( 'mysqli_stmt::bind_param' );

    self::getMaxAllowedPacket();

    $n = strlen( $p_blob );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $p_blob, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    if (self::$ourQueryLogFlag)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );

      self::$ourQueryLog[] = ['query' => $query,
                              'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );
    }

    $row = array();
    self::bindAssoc( $stmt, $row );

    $tmp = array();
    while (($b = $stmt->fetch()))
    {
      $new = array();
      foreach( $row as $key => $value )
      {
        $new[$key] = $value;
      }
      $tmp[] = $new;
    }

    $stmt->close();
    if(self::$ourMySql->more_results()) self::$ourMySql->next_result();

    if ($b===false) self::mySqlError( 'mysqli_stmt::fetch' );
    if (count($tmp)>1) throw new ResultException( '0 or 1', count($tmp), $query );

    return ($tmp) ? $tmp[0] : null;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type row1.
   *
   * @param int $p_count The number of rows selected.
   *                     * 0 For a invalid test.
   *                     * 1 For a valid test.
   *                     * 2 For a invalid test.
   *                     int(11)
   *
   * @return array
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function testRow1a( $p_count )
  {
    return self::executeRow1( 'CALL tst_test_row1a('.self::quoteNum( $p_count ).')');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type row1 with BLOB.
   *
   * @param int    $p_count The number of rows selected.
   *                        * 0 For a invalid test.
   *                        * 1 For a valid test.
   *                        * 2 For a invalid test.
   *                        int(11)
   * @param string $p_blob  The BLOB.
   *                        blob
   *
   * @return array
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function testRow1aWithLob( $p_count, $p_blob )
  {
    $query = 'CALL tst_test_row1a_with_lob( '.self::quoteNum( $p_count ).',? )';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::mySqlError( 'mysqli::prepare' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::mySqlError( 'mysqli_stmt::bind_param' );

    self::getMaxAllowedPacket();

    $n = strlen( $p_blob );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $p_blob, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    if (self::$ourQueryLogFlag)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );

      self::$ourQueryLog[] = ['query' => $query,
                              'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );
    }

    $row = array();
    self::bindAssoc( $stmt, $row );

    $tmp = array();
    while (($b = $stmt->fetch()))
    {
      $new = array();
      foreach( $row as $key => $value )
      {
        $new[$key] = $value;
      }
      $tmp[] = $new;
    }

    $stmt->close();
    if(self::$ourMySql->more_results()) self::$ourMySql->next_result();

    if ($b===false) self::mySqlError( 'mysqli_stmt::fetch' );
    if (count($tmp)!=1) throw new ResultException( '1', count($tmp), $query );

    return $row;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type row1.
   *
   * @param int $p_count The number of rows selected.
   *                     * 0 For a invalid test.
   *                     * 1 For a valid test.
   *                     * 2 For a invalid test.
   *                     int(11)
   *
   * @return array[]
   * @throws RunTimeException
   */
  public static function testRows1( $p_count )
  {
    return self::executeRows( 'CALL tst_test_rows1('.self::quoteNum( $p_count ).')' );
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type rows.
   *
   * @param int    $p_count The number of rows selected.
   *                        int(11)
   * @param string $p_blob  The BLOB.
   *                        blob
   *
   * @return array[]
   * @throws RunTimeException
   */
  public static function testRows1WithLob( $p_count, $p_blob )
  {
    $query = 'CALL tst_test_rows1_with_lob( '.self::quoteNum( $p_count ).',? )';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::mySqlError( 'mysqli::prepare' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::mySqlError( 'mysqli_stmt::bind_param' );

    self::getMaxAllowedPacket();

    $n = strlen( $p_blob );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $p_blob, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    if (self::$ourQueryLogFlag)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );

      self::$ourQueryLog[] = ['query' => $query,
                              'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );
    }

    $row = array();
    self::bindAssoc( $stmt, $row );

    $tmp = array();
    while (($b = $stmt->fetch()))
    {
      $new = array();
      foreach( $row as $key => $value )
      {
        $new[$key] = $value;
      }
       $tmp[] = $new;
    }

    $stmt->close();
    if(self::$ourMySql->more_results()) self::$ourMySql->next_result();

    if ($b===false) self::mySqlError( 'mysqli_stmt::fetch' );

    return $tmp;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type rows_with_index.
   *
   * @param int $p_count The number of rows selected.
   *                     int(11)
   *
   * @return array[]
   * @throws RunTimeException
   */
  public static function testRowsWithIndex1( $p_count )
  {
    $result = self::query( 'CALL tst_test_rows_with_index1('.self::quoteNum( $p_count ).')');
    $ret = array();
    while($row = $result->fetch_array( MYSQLI_ASSOC )) $ret[$row['tst_c01']][$row['tst_c02']][] = $row;
    $result->free();
    if(self::$ourMySql->more_results()) self::$ourMySql->next_result();

    return $ret;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type rows_with_index with BLOB.
   * .
   *
   * @param int    $p_count The number of rows selected.
   *                        int(11)
   * @param string $p_blob  The BLOB.
   *                        blob
   *
   * @return array[]
   * @throws RunTimeException
   */
  public static function testRowsWithIndex1WithLob( $p_count, $p_blob )
  {
    $query = 'CALL tst_test_rows_with_index1_with_lob( '.self::quoteNum( $p_count ).',? )';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::mySqlError( 'mysqli::prepare' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::mySqlError( 'mysqli_stmt::bind_param' );

    self::getMaxAllowedPacket();

    $n = strlen( $p_blob );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $p_blob, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    if (self::$ourQueryLogFlag)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );

      self::$ourQueryLog[] = ['query' => $query,
                              'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );
    }

    $row = array();
    self::bindAssoc( $stmt, $row );

    $ret = array();
    while (($b = $stmt->fetch()))
    {
      $new = array();
      foreach( $row as $key => $value )
      {
        $new[$key] = $value;
      }
      $ret[$new['tst_c01']][$new['tst_c02']][] = $new;
    }

    $stmt->close();
    if(self::$ourMySql->more_results()) self::$ourMySql->next_result();

    if ($b===false) self::mySqlError( 'mysqli_stmt::fetch' );

    return $ret;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type rows_with_key.
   *
   * @param int $p_count Number of rows selected.
   *                     int(11)
   *
   * @return array[]
   * @throws RunTimeException
   */
  public static function testRowsWithKey1( $p_count )
  {
    $result = self::query( 'CALL tst_test_rows_with_key1('.self::quoteNum( $p_count ).')');
    $ret = array();
    while($row = $result->fetch_array( MYSQLI_ASSOC )) $ret[$row['tst_c01']][$row['tst_c02']][$row['tst_c03']] = $row;
    $result->free();
    if(self::$ourMySql->more_results()) self::$ourMySql->next_result();

    return  $ret;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type rows_with_key with BLOB.
   *
   * @param int    $p_count The number of rows selected.
   *                        int(11)
   * @param string $p_blob  The BLOB.
   *                        blob
   *
   * @return array[]
   * @throws RunTimeException
   */
  public static function testRowsWithKey1WithLob( $p_count, $p_blob )
  {
    $query = 'CALL tst_test_rows_with_key1_with_lob( '.self::quoteNum( $p_count ).',? )';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::mySqlError( 'mysqli::prepare' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::mySqlError( 'mysqli_stmt::bind_param' );

    self::getMaxAllowedPacket();

    $n = strlen( $p_blob );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $p_blob, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    if (self::$ourQueryLogFlag)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );

      self::$ourQueryLog[] = ['query' => $query,
                              'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );
    }

    $row = array();
    self::bindAssoc( $stmt, $row );

    $ret = array();
    while (($b = $stmt->fetch()))
    {
      $new = array();
      foreach( $row as $key => $value )
      {
        $new[$key] = $value;
      }
      $ret[$new['tst_c01']][$new['tst_c02']][$new['tst_c03']] = $new;
    }

    $stmt->close();
    if(self::$ourMySql->more_results()) self::$ourMySql->next_result();

    if ($b===false) self::mySqlError( 'mysqli_stmt::fetch' );

    return $ret;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type singleton0.
   *
   * @param int $p_count The number of rows selected.
   *                     * 0 For a valid test.
   *                     * 1 For a valid test.
   *                     * 2 For a invalid test.
   *                     int(11)
   *
   * @return string
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function testSingleton0a( $p_count )
  {
    return self::executeSingleton0( 'CALL tst_test_singleton0a('.self::quoteNum( $p_count ).')');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type singleton0 with BLOB.
   * .
   *
   * @param int    $p_count The number of rows selected.
   *                        * 0 For a valid test.
   *                        * 1 For a valid test.
   *                        * 2 For a invalid test.
   *                        int(11)
   * @param string $p_blob  The BLOB.
   *                        blob
   *
   * @return string
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function testSingleton0aWithLob( $p_count, $p_blob )
  {
    $query = 'CALL tst_test_singleton0a_with_lob( '.self::quoteNum( $p_count ).',? )';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::mySqlError( 'mysqli::prepare' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::mySqlError( 'mysqli_stmt::bind_param' );

    self::getMaxAllowedPacket();

    $n = strlen( $p_blob );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $p_blob, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    if (self::$ourQueryLogFlag)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );

      self::$ourQueryLog[] = ['query' => $query,
                              'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );
    }

    $row = array();
    self::bindAssoc( $stmt, $row );

    $tmp = array();
    while (($b = $stmt->fetch()))
    {
      $new = array();
      foreach( $row as $value )
      {
        $new[] = $value;
      }
      $tmp[] = $new;
    }

    $stmt->close();
    if(self::$ourMySql->more_results()) self::$ourMySql->next_result();

    if ($b===false) self::mySqlError( 'mysqli_stmt::fetch' );
    if (count($tmp)>1) throw new ResultException( '0 or 1', count($tmp), $query );

    return ($tmp) ? $tmp[0][0] : null;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type singleton1.
   *
   * @param int $p_count The number of rows selected.
   *                     * 0 For a invalid test.
   *                     * 1 For a valid test.
   *                     * 2 For a invalid test.
   *                     int(11)
   *
   * @return string
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function testSingleton1a( $p_count )
  {
    return self::executeSingleton1( 'CALL tst_test_singleton1a('.self::quoteNum( $p_count ).')');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type singleton1 with BLOB.
   *
   * @param int    $p_count The number of rows selected.
   *                        * 0 For a invalid test.
   *                        * 1 For a valid test.
   *                        * 2 For a invalid test.
   *                        int(11)
   * @param string $p_blob  The BLOB.
   *                        blob
   *
   * @return string
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function testSingleton1aWithLob( $p_count, $p_blob )
  {
    $query = 'CALL tst_test_singleton1a_with_lob( '.self::quoteNum( $p_count ).',? )';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::mySqlError( 'mysqli::prepare' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::mySqlError( 'mysqli_stmt::bind_param' );

    self::getMaxAllowedPacket();

    $n = strlen( $p_blob );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $p_blob, $p, self::$ourChunkSize ) );
      if (!$b) self::mySqlError( 'mysqli_stmt::send_long_data' );
      $p += self::$ourChunkSize;
    }

    if (self::$ourQueryLogFlag)
    {
      $time0 = microtime(true);

      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );

      self::$ourQueryLog[] = ['query' => $query,
                              'time'  => microtime(true) - $time0];
    }
    else
    {
      $b = $stmt->execute();
      if (!$b) self::mySqlError( 'mysqli_stmt::execute' );
    }

    $row = array();
    self::bindAssoc( $stmt, $row );

    $tmp = array();
    while (($b = $stmt->fetch()))
    {
      $new = array();
      foreach( $row as $value )
      {
        $new[] = $value;
      }
      $tmp[] = $new;
    }

    $stmt->close();
    if(self::$ourMySql->more_results()) self::$ourMySql->next_result();

    if ($b===false) self::mySqlError( 'mysqli_stmt::fetch' );
    if (count($tmp)!=1) throw new ResultException( '1', count($tmp), $query );

    return $tmp[0][0];
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for designation type table.
   *
   * @return int
   * @throws RunTimeException
   */
  public static function testTable(  )
  {
    return self::executeTable( 'CALL tst_test_table()' );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
