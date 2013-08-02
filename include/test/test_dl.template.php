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
    $result->close();
    /** @todo Test the actual number of rows and number  use result->num_rows*/

    self::$ourMySql->next_result();

    return $row;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Voert query @a $theQuery uit. @a $theQuery moet 1 en slechts 1 rij teruggeven, anders wordt een exceptie geworpen.
   */
  public function ExecuteRow1( $theQuery )
  {
    $result = self::Query( $theQuery );
    $row    = $result->fetch_array( MYSQLI_ASSOC );
    $result->close();

    self::$ourMySql->next_result();

    if (empty($row)) GIDS_DL::ThrowMissingDataException( $theQuery );
    /** @todo Test uitvoeren op meer dan 1 rijen */

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
    $result->close();

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
    $result->close();
    /** @todo Test the actual number of rows and number of columns */

    self::$ourMySql->next_result();

    return $row[0];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Voert query @a $theQuery uit. @a $theQuery moet 1 rij met 1 column teruggeven, anders wordt een exceptie
   *  geworpen.
   */
  public function ExecuteSingleton1( $theQuery )
  {
    $result = self::Query( $theQuery );
    $row    = $result->fetch_array( MYSQL_NUM );
    $result->close();
    /** @todo Test uitvoeren op het daawerkelijke aantal rijen en kolomen */

    self::$ourMySql->next_result();

    if (empty($row)) GIDS_DL::ThrowMissingDataException( $theQuery );

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
    $result->close();

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
  }

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
  /** Slaat alle data uit tabel @$theTableName op in bestand @a $theFileName in CSV formaat.
   */
  public static function DumpTableCsv( $theTableName, $theFileName )
  {
   // $handle = ETL_System::FileOpen( $theFileName, 'w' );
   $handle = fopen( $theFileName, 'w' );

    $query = sprintf( "select * from %s", $theTableName );
    $result = self::Query( $query );
    if ($result)
    {
      while ($row = $result->fetch_row())
      {
        foreach( $row as $i => $field )
        {
          if ($field===null)
          {
           // Vervang NULL door \N (string literal voor NULL, zie mysqlimport).
            $row[$i] = '\N';
          }
          else
          {
            $row[$i] = self::$ourMySql->real_escape_string( $row[$i] );
          }
        }
        $err = fputcsv( $handle, $row, ",", '"' );
        if ($err===false) throw new exception( sprintf( "Unable to write record to file '%s'", $theFileName ) );
      }
    }
    $result->close();

    self::$ourMySql->next_result();

  //  ETL_System::FileClose( $handle );
  fclose( $handle );
  }

  /* AUTO_GENERATED_ROUINE_WRAPPERS */
  // -------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
