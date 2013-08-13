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
          etl_log( $line );
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

  /* AUTO_GENERATED_ROUINE_WRAPPERS */
  // -------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
