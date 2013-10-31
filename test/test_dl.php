<?php
//----------------------------------------------------------------------------------------------------------------------
// require_once( "$gids_home/include/etl/system.php" );

// ---------------------------------------------------------------------------------------------------------------------
/** @brief Interface voor klasses om de rijen van queries die een groot aantal rij teruggeeft te verwerken.
 */
interface TST_DataLayerBulkHandler
{
  // -------------------------------------------------------------------------------------------------------------------
  /** Wordt aangeroepen voor het verwerken van de eerste rij in de result set (en na het afvuren van de query).
   */
  public function Start();

  // -------------------------------------------------------------------------------------------------------------------
  /** Wordt aangeroepn voor iedere rij @a $theRow in de result set.
   */
  public function Row( $theRow );

  // -------------------------------------------------------------------------------------------------------------------
  /** Wordt aangeroepen voor het verwerken van de laatste rij in de result set.
   */
  public function Stop();

  // -------------------------------------------------------------------------------------------------------------------
}

// ---------------------------------------------------------------------------------------------------------------------
/** @brief Klasse met wrapper functies voor alle stored procedures die aangeroepen mogen worden door PHP-code.
 */
class TST_DL
{
  /** Referentie naar een mysqli object (zie Connect) dat gebruikt wordt voor het afvuren van alle SQL satements in
   *  deze klasse.
   */
  private static $ourMySql;

  /** Value of variable max_allowed_packet
   */
  private static $ourMaxAllowedPacket;

  /** Number of bytes send with mysqli_stmt::send_long_data
   */
  private static $ourChunckSize = 1000000;

  // -------------------------------------------------------------------------------------------------------------------
  /** Werpt een excptie met de huidige error code en beschrijving van $ourMySql.
   */
  private static function ThrowSqlError( $theText )
  {
    $message  = "MySQL Error no: ".self::$ourMySql->errno."\n";
    $message .= self::$ourMySql->error;
    $message .= "\n";
    $message .= $theText."\n";

    throw new Exception( $message );
  }

  // -------------------------------------------------------------------------------------------------------------------
  /** Get constant MAX_ALLOWED_PACKET.
   */
  public function getMaxAllowedPacket()
  {
    if (!isset(self::$ourMaxAllowedPacket))
    {
      $query = "show variables like 'max_allowed_packet'";
      $max_allowed_packet = TST_DL::ExecuteRow1( $query );

      self::$ourMaxAllowedPacket = $max_allowed_packet['Value'];
    }

    return self::$ourMaxAllowedPacket;
  }

  // -------------------------------------------------------------------------------------------------------------------
  /** Werpt een excptie met de huidige error code en beschrijving van $ourMySql.
   */
  private static function AssertFailed()
  {
    $args    = func_get_args();
    $format  = array_shift( $args );
    $message = vsprintf( $format,  $args );

    throw new Exception( $message );
  }

  //------------------------------------------------------------------------------------------------------------------
  public static function stmt_bind_assoc( $stmt, &$out )
  {
    $data = $stmt->result_metadata();
    if (!$data) self::ThrowSqlError( 'mysqli_stmt::result_metadata failed' );

    $fields = array();
    $out    = array();

    $fields[0] = $stmt;
    $count = 1;

    while($field = $data->fetch_field())
    {
      $fields[$count] = &$out[$field->name];
      $count++;
    }

    $b = call_user_func_array( 'mysqli_stmt_bind_result', $fields );
    if ($b===false) self::ThrowSqlError( 'mysqli_stmt_bind_result failed' );

    $data->free();
  }

  // -------------------------------------------------------------------------------------------------------------------
  /** Wrapper om mysqli::query. Indien de call naar mysqli::query mislukt wordt een een exceptie geworpen.
   */
  private static function Query( $theQuery )
  {
    $ret = self::$ourMySql->query( $theQuery );
    if ($ret===false) self::ThrowSqlError( $theQuery );

    return $ret;
  }

  // -------------------------------------------------------------------------------------------------------------------
  /** Wrapper om mysqli::real_query. Indien de call naar mysqli::real_query mislukt wordt een een exceptie geworpen.
   */
  private static function RealQuery( $theQuery )
  {
    $tmp = self::$ourMySql->real_query( $theQuery );
    if ($tmp===false) self::ThrowSqlError( $theQuery );
  }

  // -------------------------------------------------------------------------------------------------------------------
  /** Start een transactie in MySQL.
   */
  public static function Begin()
  {
    $ret = self::$ourMySql->autocommit(false);
    if (!$ret) self::ThrowSqlError( 'autocommit' );
  }

  // -------------------------------------------------------------------------------------------------------------------
  /** Commit de huidige transactie in MySQL.
   */
  public static function Commit()
  {
    $ret = self::$ourMySql->commit();
    if (!$ret) self::ThrowSqlError( 'commit' );
  }

  // -------------------------------------------------------------------------------------------------------------------
  /** Logt de waarschuwingen van het laatst uitgevoerde SQL statement.
   */
  public static function ShowWarnings()
  {
    self::ExecuteEcho( 'show warnings' );
  }

  // -------------------------------------------------------------------------------------------------------------------
  /** Connecteert naar de MySQL server (parameters @a $theHostName, @a $theUserName, @a $thePassWord en @a $theDatabase)
   *  en voert een aantal initialisaties uit.
   */
  public static function Connect( $theHostName, $theUserName, $thePassWord, $theDatabase )
  {
    self::$ourMySql = new mysqli( $theHostName, $theUserName, $thePassWord, $theDatabase );
    if (!self::$ourMySql) self::ThrowSqlError( 'init' );

    $ret = self::$ourMySql->options(MYSQLI_OPT_CONNECT_TIMEOUT, 600);
    if (!$ret) self::ThrowSqlError( 'options' );

    $ret = self::$ourMySql->set_charset("utf8");
    if (!$ret) self::ThrowSqlError( 'set_charset' );

    $ret = self::ExecuteNone( "set sql_mode = '".TST_SQL_MODE."'");

    // The default transaction level is REPEATABLE-READ. Set transaction level to READ-COMMITED.
    self::ExecuteNone( "SET tx_isolation = 'READ-COMMITTED'" );

    // Disable query caching.
    // self::ExecuteNone( "set query_cache_type = 0" );
  }

  // -------------------------------------------------------------------------------------------------------------------
  /** Indien er een verbinding is met een MySQL server, verbreekt de verbinding.
   */
  public static function Disconnect()
  {
    if (self::$ourMySql)
    {
      self::$ourMySql->close();
      self::$ourMySql = null;
    }
  }

  // -------------------------------------------------------------------------------------------------------------------
  /** Voert query @a $theQuery uit. De query mag een multi query zijn (b.v. een store procedure) en de output van de
   *  query wordt gelogt.
   */
  public static function ExecuteEcho( $theQuery )
  {
    $ret = self::$ourMySql->multi_query( $theQuery );
    if (!$ret) self::ThrowSqlError( $theQuery );
    do
    {
      $result = self::$ourMySql->store_result();
      if (self::$ourMySql->errno) self::ThrowSqlError( '$mysqli->store_result failed for \''.$theQuery.'\'' );
      if ($result)
      {
        $fields = $result->fetch_fields();
        while ($row = $result->fetch_row())
        {
          $line = '';
          foreach( $row as $i => $field )
          {
            if ($i>0) $line .= ' ';
            $line .= str_pad( $field, $fields[$i]->max_length );
          }
          echo date( 'Y-m-d H:i:s' ),' ',$line,"\n";
        }
        $result->free();
      }
    }
    while (self::$ourMySql->next_result());
    if (self::$ourMySql->errno) self::ThrowSqlError( '$mysqli->next_result failed for \''.$theQuery.'\'' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Voert query @a $theQuery uit een geeft het aantal "affected rows" terug. @a $theQuery mag geen rijen teruggeven.
   */
  public function ExecuteNone( $theQuery )
  {
    self::Query( $theQuery );

    $n = self::$ourMySql->affected_rows;

    self::$ourMySql->next_result();

    return $n;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Voert query @a $theQuery uit. @a $theQuery moet 0 of 1 rij teruggeven, anders wordt een exceptie geworpen.
   */
  public function ExecuteRow0( $theQuery )
  {
    $result = self::Query( $theQuery );
    $row    = $result->fetch_array( MYSQLI_ASSOC );
    $n      = $result->num_rows;
    $result->free();

    self::$ourMySql->next_result();

    if (!($n==0 || $n==1))
    {
      TST_DL::AssertFailed( "Number of rows selected by query below is %d expected 0 or 1.\n%s",
                            $result->num_rows,
                            $theQuery );
    } // @codeCoverageIgnore

    return $row;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Voert query @a $theQuery uit. @a $theQuery moet 1 en slechts 1 rij teruggeven, anders wordt een exceptie geworpen.
   */
  public function ExecuteRow1( $theQuery )
  {
    $result = self::Query( $theQuery );
    $row = $result->fetch_array( MYSQLI_ASSOC );
    $n = $result->num_rows;
    $result->free();

    self::$ourMySql->next_result();

    if($n!=1)
    {
      TST_DL::AssertFailed( "Number of rows selected by query below is %d expected 1.\n%s",
                            $result->num_rows,
                            $theQuery );
    } // @codeCoverageIgnore

    return $row;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Voert query @a $theQuery uit. @a $theQuery mag 0 of meer rijen teruggeven.
   */
  public function ExecuteRows( $theQuery )
  {
    $result = self::Query( $theQuery );
    $ret = array();
    while($row = $result->fetch_array( MYSQLI_ASSOC )) $ret[] = $row;
    $result->free();

    self::$ourMySql->next_result();

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Voert query @a $theQuery uit. $theQuery moet 0 of 1 rij met 1 column teruggeven, anders wordt een exceptie
   *  geworpen.
   */
  public function ExecuteSingleton0( $theQuery )
  {
    $result = self::Query( $theQuery );
    $row    = $result->fetch_array( MYSQL_NUM );
    $n = $result->num_rows;
    $result->free();

    self::$ourMySql->next_result();

    if (!($n==0 || $n==1))
    {
      TST_DL::AssertFailed( "Number of rows selected by query below is %d expected 0 or 1.\n%s",
                            $result->num_rows,
                            $theQuery );
    } // @codeCoverageIgnore

    return $row[0];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Voert query @a $theQuery uit. @a $theQuery moet 1 rij met 1 column teruggeven, anders wordt een exceptie
   *  geworpen.
   */
  public function ExecuteSingleton1( $theQuery )
  {
    $result = self::Query( $theQuery );
    $row = $result->fetch_array( MYSQL_NUM );
    $n = $result->num_rows;
    $result->free();

    self::$ourMySql->next_result();

    if ($n!=1)
    {
      TST_DL::AssertFailed( "Number of rows selected by query below is %d expected 1.\n%s",
                            $result->num_rows,
                            $theQuery );
    } // @codeCoverageIgnore

    return $row[0];
  }

  // -------------------------------------------------------------------------------------------------------------------
  /** Voert query @a $theQuery uit en itereert over de resultaat set m.b.v. @a $theBulkHandler.
   */
  public function ExecuteBulk( $theBulkHandler, $theQuery )
  {
    self::RealQuery( $theQuery );

    $theBulkHandler->Start();

    $result = self::$ourMySql->use_result();
    while ($row=$result->fetch_array( MYSQL_ASSOC ))
    {
      $theBulkHandler->Row( $row );
    }
    $result->free();

    $theBulkHandler->Stop();

    self::$ourMySql->next_result();
  }

  // -------------------------------------------------------------------------------------------------------------------
  /** Geeft een literal voor @a $theValue terug die veilig gebruikt kan worden in SQL statements. Lege waarden altijd
   *  naar NUUL vertaalt. Indien @a niet numeriek is wordt een exceptie geworpen.
   */
  public static function QuoteNum( $theValue )
  {
    if (is_numeric( $theValue )) return $theValue;
    if ($theValue==='')          return 'NULL';
    if ($theValue===null)        return 'NULL';
    if ($theValue===false)       return 'NULL';
    if ($theValue===true)        return 1;

    self::ThrowSqlError( "Value '$theValue' is not a number." );
  } // @codeCoverageIgnore

  // -------------------------------------------------------------------------------------------------------------------
  /** Geeft een literal voor @a $theString terug die veilig gebruikt kan worden als string in SQL statements.
   */
  public static function QuoteString( $theString )
  {
    if ($theString===null || $theString===false || $theString==='')
    {
      return 'NULL';
    }
    else
    {
      return "'".self::$ourMySql->real_escape_string( $theString )."'";
    }
  }

  // -------------------------------------------------------------------------------------------------------------------
  /** Geeft een literal voor @a $theString terug die veilig gebruikt kan worden als string in SQL statements.
   */
  public static function QuoteBit( $theString )
  {
    if ($theString===null || $theString===false || $theString==='')
    {
      return 'NULL';
    }
    else
    {
      return "b'".self::$ourMySql->real_escape_string( $theString )."'";
    }
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_magic_constant01.
   */
  static function MagicConstant01()
  {
    return self::ExecuteSingleton1( 'CALL tst_magic_constant01()');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_magic_constant02.
   */
  static function MagicConstant02()
  {
    return self::ExecuteSingleton1( 'CALL tst_magic_constant02()');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_magic_constant03.
   */
  static function MagicConstant03()
  {
    return self::ExecuteSingleton1( 'CALL tst_magic_constant03()');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_magic_constant04.
   */
  static function MagicConstant04()
  {
    return self::ExecuteSingleton1( 'CALL tst_magic_constant04()');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_magic_constant05.
   */
  static function MagicConstant05()
  {
    return self::ExecuteSingleton1( 'CALL tst_magic_constant05()');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test01.
   */
  static function Test01($theArg0,$theArg1,$theArg2,$theArg3,$theArg4,$theArg5,$theArg6,$theArg7,$theArg8,$theArg9,$theArg10,$theArg11,$theArg12,$theArg13,$theArg14,$theArg15,$theArg16,$theArg17,$theArg18,$theArg19)
  {
    return self::ExecuteNone( 'CALL tst_test01('.self::QuoteNum($theArg0).','.self::QuoteNum($theArg1).','.self::QuoteNum($theArg2).','.self::QuoteNum($theArg3).','.self::QuoteNum($theArg4).','.self::QuoteNum($theArg5).','.self::QuoteNum($theArg6).','.self::QuoteNum($theArg7).','.self::QuoteBit($theArg8).','.self::QuoteString($theArg9).','.self::QuoteString($theArg10).','.self::QuoteString($theArg11).','.self::QuoteString($theArg12).','.self::QuoteNum($theArg13).','.self::QuoteString($theArg14).','.self::QuoteString($theArg15).','.self::QuoteString($theArg16).','.self::QuoteString($theArg17).','.self::QuoteString($theArg18).','.self::QuoteString($theArg19).')' );
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test02.
   */
  static function Test02($theArg0,$theArg1,$theArg2,$theArg3,$theArg4,$theArg5,$theArg6,$theArg7,$theArg8,$theArg9,$theArg10,$theArg11,$theArg12,$theArg13,$theArg14,$theArg15,$theArg16,$theArg17,$theArg18,$theArg19,$theArg20,$theArg21,$theArg22,$theArg23,$theArg24,$theArg25,$theArg26,$theArg27)
  {
    $query = 'CALL tst_test02('.self::QuoteNum($theArg0).','.self::QuoteNum($theArg1).','.self::QuoteNum($theArg2).','.self::QuoteNum($theArg3).','.self::QuoteNum($theArg4).','.self::QuoteNum($theArg5).','.self::QuoteNum($theArg6).','.self::QuoteNum($theArg7).','.self::QuoteBit($theArg8).','.self::QuoteString($theArg9).','.self::QuoteString($theArg10).','.self::QuoteString($theArg11).','.self::QuoteString($theArg12).','.self::QuoteNum($theArg13).','.self::QuoteString($theArg14).','.self::QuoteString($theArg15).','.self::QuoteString($theArg16).','.self::QuoteString($theArg17).',?,?,?,?,?,?,?,?,'.self::QuoteString($theArg26).','.self::QuoteString($theArg27).')';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::ThrowSqlError( 'prepare failed' );

    $null = null;
    $b = $stmt->bind_param( 'bbbbbbbb', $null,$null,$null,$null,$null,$null,$null,$null );
    if (!$b) self::ThrowSqlError( 'bind_param failed' );

    $n = strlen( $theArg18 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $theArg18, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $n = strlen( $theArg19 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 1, substr( $theArg19, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $n = strlen( $theArg20 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 2, substr( $theArg20, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $n = strlen( $theArg21 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 3, substr( $theArg21, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $n = strlen( $theArg22 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 4, substr( $theArg22, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $n = strlen( $theArg23 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 5, substr( $theArg23, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $n = strlen( $theArg24 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 6, substr( $theArg24, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $n = strlen( $theArg25 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 7, substr( $theArg25, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $b = $stmt->execute();
    if (!$b) self::ThrowSqlError( 'execute failed' );

    $ret = self::$ourMySql->affected_rows;

    $stmt->close();
    self::$ourMySql->next_result();

    return $ret;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_bulk_insert01.
   */
  static function TestBulkInsert01($theData)
  {
    self::Query(  'CALL tst_test_bulk_insert01()');
    $sql = "INSERT INTO `TST_TEMPO`(`tst_col1`,`tst_col2`,`tst_col3`,`tst_col4`,`tst_col5`,`tst_col6`,`tst_col7`,`tst_col8`,`tst_col9`,`tst_col10`,`tst_col11`,`tst_col12`,`tst_col13`,`tst_col14`,`tst_col15`,`tst_col16`,`tst_col17`,`tst_col18`,`tst_col19`,`tst_col20`)";
    $first = true;
    foreach( $theData as $row )
    {
        if ($first) $sql .=' values('.self::QuoteNum($row['field1']).','.self::QuoteNum($row['field2']).','.self::QuoteNum($row['field3']).','.self::QuoteNum($row['field4']).','.self::QuoteNum($row['field5']).','.self::QuoteNum($row['field6']).','.self::QuoteNum($row['field7']).','.self::QuoteNum($row['field8']).','.self::QuoteNum($row['field9']).','.self::QuoteString($row['field10']).','.self::QuoteString($row['field11']).','.self::QuoteString($row['field12']).','.self::QuoteString($row['field13']).','.self::QuoteString($row['field14']).','.self::QuoteString($row['field15']).','.self::QuoteString($row['field16']).','.self::QuoteString($row['field17']).','.self::QuoteString($row['field18']).','.self::QuoteString($row['field19']).','.self::QuoteBit($row['field20']).')';
        else        $sql .=',      ('.self::QuoteNum($row['field1']).','.self::QuoteNum($row['field2']).','.self::QuoteNum($row['field3']).','.self::QuoteNum($row['field4']).','.self::QuoteNum($row['field5']).','.self::QuoteNum($row['field6']).','.self::QuoteNum($row['field7']).','.self::QuoteNum($row['field8']).','.self::QuoteNum($row['field9']).','.self::QuoteString($row['field10']).','.self::QuoteString($row['field11']).','.self::QuoteString($row['field12']).','.self::QuoteString($row['field13']).','.self::QuoteString($row['field14']).','.self::QuoteString($row['field15']).','.self::QuoteString($row['field16']).','.self::QuoteString($row['field17']).','.self::QuoteString($row['field18']).','.self::QuoteString($row['field19']).','.self::QuoteBit($row['field20']).')';
        $first = false;
    }
    self::Query( $sql );
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_bulk_insert02.
   */
  static function TestBulkInsert02($theData)
  {
    self::Query(  'CALL tst_test_bulk_insert02()');
    $sql = "INSERT INTO `TST_TEMPO`(`tst_col1`,`tst_col4`,`tst_col5`)";
    $first = true;
    foreach( $theData as $row )
    {
        if ($first) $sql .=' values('.self::QuoteNum($row['field1']).','.self::QuoteNum($row['field4']).','.self::QuoteNum($row['field5']).')';
        else        $sql .=',      ('.self::QuoteNum($row['field1']).','.self::QuoteNum($row['field4']).','.self::QuoteNum($row['field5']).')';
        $first = false;
    }
    self::Query( $sql );
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_log.
   */
  static function TestLog()
  {
    self::ExecuteEcho( 'CALL tst_test_log()' );
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_max_alllowed_packet.
   */
  static function TestMaxAlllowedPacket($theArg0)
  {
    $query = 'CALL tst_test_max_alllowed_packet(?)';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::ThrowSqlError( 'prepare failed' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::ThrowSqlError( 'bind_param failed' );

    $n = strlen( $theArg0 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $theArg0, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $b = $stmt->execute();
    if (!$b) self::ThrowSqlError( 'execute failed' );

    $row = array();
    self::stmt_bind_assoc( $stmt, $row );

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

    $b = $stmt->fetch();

    $stmt->close();
    self::$ourMySql->next_result();

    if ($b===false) self::ThrowSqlError( 'mysqli_stmt::fetch failed' );
    if (sizeof($tmp)!=1) self::ThrowSqlError( 'The unexpected number of rows, expected 1 row.' );

    return $tmp[0][0];
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_none.
   */
  static function TestNone($theArg0)
  {
    return self::ExecuteNone( 'CALL tst_test_none('.self::QuoteNum($theArg0).')' );
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_none_with_lob.
   */
  static function TestNoneWithLob($theArg0,$theArg1)
  {
    $query = 'CALL tst_test_none_with_lob('.self::QuoteNum($theArg0).',?)';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::ThrowSqlError( 'prepare failed' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::ThrowSqlError( 'bind_param failed' );

    $n = strlen( $theArg1 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $theArg1, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $b = $stmt->execute();
    if (!$b) self::ThrowSqlError( 'execute failed' );

    $ret = self::$ourMySql->affected_rows;

    $stmt->close();
    self::$ourMySql->next_result();

    return $ret;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_row0a.
   */
  static function TestRow0a($theArg0)
  {
    return self::ExecuteRow0( 'CALL tst_test_row0a('.self::QuoteNum($theArg0).')');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_row0a_with_lob.
   */
  static function TestRow0aWithLob($theArg0,$theArg1)
  {
    $query = 'CALL tst_test_row0a_with_lob('.self::QuoteNum($theArg0).',?)';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::ThrowSqlError( 'prepare failed' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::ThrowSqlError( 'bind_param failed' );

    $n = strlen( $theArg1 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $theArg1, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $b = $stmt->execute();
    if (!$b) self::ThrowSqlError( 'execute failed' );

    $row = array();
    self::stmt_bind_assoc( $stmt, $row );

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
    self::$ourMySql->next_result();

    if ($b===false) self::ThrowSqlError( 'mysqli_stmt::fetch failed' );
    if (sizeof($tmp)>1) self::ThrowSqlError( 'The unexpected number of rows, expected 0 or 1 rows.' );

    return ($tmp) ? $tmp[0] : null;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_row1a.
   */
  static function TestRow1a($theArg0)
  {
    return self::ExecuteRow1( 'CALL tst_test_row1a('.self::QuoteNum($theArg0).')');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_row1a_with_lob.
   */
  static function TestRow1aWithLob($theArg0,$theArg1)
  {
    $query = 'CALL tst_test_row1a_with_lob('.self::QuoteNum($theArg0).',?)';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::ThrowSqlError( 'prepare failed' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::ThrowSqlError( 'bind_param failed' );

    $n = strlen( $theArg1 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $theArg1, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $b = $stmt->execute();
    if (!$b) self::ThrowSqlError( 'execute failed' );

    $row = array();
    self::stmt_bind_assoc( $stmt, $row );

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
    self::$ourMySql->next_result();

    if ($b===false) self::ThrowSqlError( 'mysqli_stmt::fetch failed' );
    if (sizeof($tmp)!=1) self::ThrowSqlError( 'The unexpected  number of rows,  expected 1 row.' );

    return $row;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_rows1.
   */
  static function TestRows1($theArg0)
  {
    return self::ExecuteRows( 'CALL tst_test_rows1('.self::QuoteNum($theArg0).')');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_rows1_with_lob.
   */
  static function TestRows1WithLob($theArg0,$theArg1)
  {
    $query = 'CALL tst_test_rows1_with_lob('.self::QuoteNum($theArg0).',?)';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::ThrowSqlError( 'prepare failed' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::ThrowSqlError( 'bind_param failed' );

    $n = strlen( $theArg1 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $theArg1, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $b = $stmt->execute();
    if (!$b) self::ThrowSqlError( 'execute failed' );

    $row = array();
    self::stmt_bind_assoc( $stmt, $row );

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
    self::$ourMySql->next_result();

    if ($b===false) self::ThrowSqlError( 'mysqli_stmt::fetch failed' );

    return $tmp;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_rows_with_index1.
   */
  static function TestRowsWithIndex1($theArg0)
  {
    $result = self::Query( 'CALL tst_test_rows_with_index1('.self::QuoteNum($theArg0).')');
    $ret = array();
    while($row = $result->fetch_array( MYSQLI_ASSOC )) $ret[$row['tst_c01']][$row['tst_c02']][] = $row;
    $result->close();
    self::$ourMySql->next_result();
    return $ret;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_rows_with_index1_with_lob.
   */
  static function TestRowsWithIndex1WithLob($theArg0,$theArg1)
  {
    $query = 'CALL tst_test_rows_with_index1_with_lob('.self::QuoteNum($theArg0).',?)';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::ThrowSqlError( 'prepare failed' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::ThrowSqlError( 'bind_param failed' );

    $n = strlen( $theArg1 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $theArg1, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $b = $stmt->execute();
    if (!$b) self::ThrowSqlError( 'execute failed' );

    $row = array();
    self::stmt_bind_assoc( $stmt, $row );

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

    $b = $stmt->fetch();

    $stmt->close();
    self::$ourMySql->next_result();

    if ($b===false) self::ThrowSqlError( 'mysqli_stmt::fetch failed' );

    return $ret;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_rows_with_key1.
   */
  static function TestRowsWithKey1($theArg0)
  {
    $result = self::Query( 'CALL tst_test_rows_with_key1('.self::QuoteNum($theArg0).')');
    $ret = array();
    while($row = $result->fetch_array( MYSQLI_ASSOC )) $ret[$row['tst_c01']][$row['tst_c02']][$row['tst_c03']] = $row;
    $result->close();
    self::$ourMySql->next_result();
    return  $ret;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_rows_with_key1_with_lob.
   */
  static function TestRowsWithKey1WithLob($theArg0,$theArg1)
  {
    $query = 'CALL tst_test_rows_with_key1_with_lob('.self::QuoteNum($theArg0).',?)';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::ThrowSqlError( 'prepare failed' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::ThrowSqlError( 'bind_param failed' );

    $n = strlen( $theArg1 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $theArg1, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $b = $stmt->execute();
    if (!$b) self::ThrowSqlError( 'execute failed' );

    $row = array();
    self::stmt_bind_assoc( $stmt, $row );

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

    $b = $stmt->fetch();

    $stmt->close();
    self::$ourMySql->next_result();

    if ($b===false) self::ThrowSqlError( 'mysqli_stmt::fetch failed' );

    return $ret;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_singleton0a.
   */
  static function TestSingleton0a($theArg0)
  {
    return self::ExecuteSingleton0( 'CALL tst_test_singleton0a('.self::QuoteNum($theArg0).')');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_singleton0a_with_lob.
   */
  static function TestSingleton0aWithLob($theArg0,$theArg1)
  {
    $query = 'CALL tst_test_singleton0a_with_lob('.self::QuoteNum($theArg0).',?)';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::ThrowSqlError( 'prepare failed' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::ThrowSqlError( 'bind_param failed' );

    $n = strlen( $theArg1 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $theArg1, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $b = $stmt->execute();
    if (!$b) self::ThrowSqlError( 'execute failed' );

    $row = array();
    self::stmt_bind_assoc( $stmt, $row );

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

    $b = $stmt->fetch();

    $stmt->close();
    self::$ourMySql->next_result();

    if ($b===false) self::ThrowSqlError( 'mysqli_stmt::fetch failed' );
    if (sizeof($tmp)>1) self::ThrowSqlError( 'The unexpected number of rows, expected 0 or 1 rows.' );

    return ($tmp) ? $tmp[0][0] : null;
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_singleton1a.
   */
  static function TestSingleton1a($theArg0)
  {
    return self::ExecuteSingleton1( 'CALL tst_test_singleton1a('.self::QuoteNum($theArg0).')');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /** @sa Stored Routine tst_test_singleton1a_with_lob.
   */
  static function TestSingleton1aWithLob($theArg0,$theArg1)
  {
    $query = 'CALL tst_test_singleton1a_with_lob('.self::QuoteNum($theArg0).',?)';
    $stmt  = self::$ourMySql->prepare( $query );
    if (!$stmt) self::ThrowSqlError( 'prepare failed' );

    $null = null;
    $b = $stmt->bind_param( 'b', $null );
    if (!$b) self::ThrowSqlError( 'bind_param failed' );

    $n = strlen( $theArg1 );
    $p = 0;
    while ($p<$n)
    {
      $b = $stmt->send_long_data( 0, substr( $theArg1, $p, self::$ourChunckSize ) );
      if (!$b) self::ThrowSqlError( 'send_long_data failed' );
      $p += self::$ourChunckSize;
    }

    $b = $stmt->execute();
    if (!$b) self::ThrowSqlError( 'execute failed' );

    $row = array();
    self::stmt_bind_assoc( $stmt, $row );

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

    $b = $stmt->fetch();

    $stmt->close();
    self::$ourMySql->next_result();

    if ($b===false) self::ThrowSqlError( 'mysqli_stmt::fetch failed' );
    if (sizeof($tmp)!=1) self::ThrowSqlError( 'The unexpected number of rows, expected 1 row.' );

    return $tmp[0][0];
  }


  // -------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
