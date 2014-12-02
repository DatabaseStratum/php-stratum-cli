<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class StaticDataLayer
 *
 * @package SetBased\DataLayer
 */
class StaticDataLayer
{

  /**
   * The default character set et to be used when sending data from and to the MySQL instance.
   *
   * @var string
   */
  public static $ourCharSet = 'utf8';

  /**
   * The SQL mode of the MySQL instance.
   *
   * @var string
   */
  public static $ourSqlMode = 'STRICT_ALL_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_AUTO_VALUE_ON_ZERO,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ONLY_FULL_GROUP_BY';

  /**
   * The transaction isolation level. Possible values are:
   * <ul>
   * <li> REPEATABLE-READ
   * <li> READ-COMMITTED
   * <li> READ-UNCOMMITTED
   * <li> SERIALIZABLE
   * </ul>
   *
   * @var string
   */
  public static $ourTransactionIsolationLevel = 'READ-COMMITTED';

  /**
   * Chunk size when transmitting LOB to the MySQL instance. Must be less than max_allowed_packet.
   *
   * @var int
   */
  protected static $ourChunkSize;

  /**
   * True if method mysqli_result::fetch_all exists (i.e. we are using MySQL native driver).
   *
   * @var bool
   */
  protected static $ourHaveFetchAll;

  /**
   * Value of variable max_allowed_packet
   *
   * @var int
   */
  protected static $ourMaxAllowedPacket;

  /**
   * The connection between PHP and the MySQL instance.
   *
   * @var \mysqli
   */
  protected static $ourMySql;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Starts a transaction.
   * Wrapper around mysqli::autocommit, however on failure an exception is thrown.
   */
  public static function begin()
  {
    $ret = self::$ourMySql->autocommit( false );
    if (!$ret) self::sqlError( 'mysqli::autocommit' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param \mysqli_stmt $stmt
   * @param array        $out
   */
  public static function bindAssoc( $stmt, &$out )
  {
    $data = $stmt->result_metadata();
    if (!$data) self::sqlError( 'mysqli_stmt::result_metadata' );

    $fields = array();
    $out    = array();

    $i = 0;
    while ($field = $data->fetch_field())
    {
      $fields[$i] = &$out[$field->name];
      $i++;
    }

    $b = call_user_func_array( array($stmt, 'bind_result'), $fields );
    if ($b===false) self::sqlError( 'mysqli_stmt::bind_result' );

    $data->free();

    if (self::$ourMySql->more_results()) self::$ourMySql->next_result();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Commits the current transaction (and starts a new transaction).
   * Wrapper around mysqli::commit, however on failure an exception is thrown.
   */
  public static function commit()
  {
    $ret = self::$ourMySql->commit();
    if (!$ret) self::sqlError( 'mysqli::commit' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Connects to a MySQL instance.
   * Wrapper around mysqli::__construct, however on failure an exception is thrown.
   *
   * @param string $theHostName The hostname.
   * @param string $theUserName The MySQL user name.
   * @param string $thePassWord The password.
   * @param string $theDatabase The default database.
   * @param int    $thePort     The port number.
   */
  public static function connect( $theHostName, $theUserName, $thePassWord, $theDatabase, $thePort = 3306 )
  {
    self::$ourMySql = new \mysqli( $theHostName, $theUserName, $thePassWord, $theDatabase, $thePort );
    if (!self::$ourMySql) self::sqlError( 'mysqli::__construct' );

    // Set the default character set.
    if (self::$ourCharSet)
    {
      $ret = self::$ourMySql->set_charset( self::$ourCharSet );
      if (!$ret) self::sqlError( 'mysqli::set_charset' );
    }

    // Set the SQL mode.
    if (self::$ourSqlMode)
    {
      self::executeNone( "SET sql_mode = '".self::$ourSqlMode."'" );
    }

    // Set transaction isolation level.
    if (self::$ourTransactionIsolationLevel)
    {
      self::executeNone( "SET SESSION tx_isolation = '".self::$ourTransactionIsolationLevel."'" );
    }

    // Set flag to use method mysqli_result::fetch_all if we are using MySQL native driver.
    self::$ourHaveFetchAll = method_exists( 'mysqli_result', 'fetch_all' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Closes the connection to the MySQL instance, if connected.
   */
  public static function disconnect()
  {
    if (self::$ourMySql)
    {
      self::$ourMySql->close();
      self::$ourMySql = null;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Runs a query using a bulk handler.
   *
   * @param BulkHandler $theBulkHandler The bulk handler.
   * @param string      $theQuery       The SQL statement.
   */
  public static function executeBulk( $theBulkHandler, $theQuery )
  {
    self::realQuery( $theQuery );

    $theBulkHandler->start();

    $result = self::$ourMySql->use_result();
    while ($row = $result->fetch_array( MYSQL_ASSOC ))
    {
      $theBulkHandler->row( $row );
    }
    $result->free();

    $theBulkHandler->stop();

    if (self::$ourMySql->more_results()) self::$ourMySql->next_result();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query and logs the result set and returns the total number of rows selected.
   *
   * @param string $theQuery The query or multi query.
   *
   * @return int
   */
  public static function executeLog( $theQuery )
  {
    // Counter for the number of rows written/logged.
    $n = 0;

    $ret = self::$ourMySql->multi_query( $theQuery );
    if (!$ret) self::sqlError( $theQuery );
    do
    {
      $result = self::$ourMySql->store_result();
      if (self::$ourMySql->errno) self::sqlError( 'mysqli::store_result' );
      if ($result)
      {
        $fields = $result->fetch_fields();
        while ($row = $result->fetch_row())
        {
          $line = '';
          foreach ($row as $i => $field)
          {
            if ($i>0) $line .= ' ';
            $line .= str_pad( $field, $fields[$i]->max_length );
          }
          echo date( 'Y-m-d H:i:s' ), ' ', $line, "\n";
          $n++;
        }
        $result->free();
      }

      $continue = self::$ourMySql->more_results();
      if ($continue)
      {
        $tmp = self::$ourMySql->next_result();
        if ($tmp===false) self::sqlError( 'mysqli::next_result' );
      }
    } while ($continue);

    return $n;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Runs a query that does not select any rows and returns the number of affected rows (if any).
   *
   * @param string $theQuery The SQL statement.
   *
   * @return int
   */
  public static function executeNone( $theQuery )
  {
    self::query( $theQuery );

    $n = self::$ourMySql->affected_rows;

    if (self::$ourMySql->more_results()) self::$ourMySql->next_result();

    return $n;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Runs a query that returns 0 or 1 row and returns that row.
   * Throws an exception if the query selects 2 or more rows.
   *
   * @param string $theQuery The SQL statement.
   *
   * @return array|null
   */
  public static function executeRow0( $theQuery )
  {
    $result = self::query( $theQuery );
    $row    = $result->fetch_array( MYSQLI_ASSOC );
    $n      = $result->num_rows;
    $result->free();

    if (self::$ourMySql->more_results()) self::$ourMySql->next_result();

    if (!($n==0 || $n==1))
    {
      self::assertFailed( "Number of rows selected by query below is %d expected 0 or 1.\n%s",
                          $n,
                          $theQuery );
    }

    return $row;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Runs a query that returns 1 and only 1 row and returns that row.
   * Throws an exception if the query selects none, 2 or more rows.
   *
   * @param string $theQuery The SQL statement.
   *
   * @return array
   */
  public static function executeRow1( $theQuery )
  {
    $result = self::query( $theQuery );
    $row    = $result->fetch_array( MYSQLI_ASSOC );
    $n      = $result->num_rows;
    $result->free();

    if (self::$ourMySql->more_results()) self::$ourMySql->next_result();

    if ($n!=1)
    {
      self::assertFailed( "Number of rows selected by query below is %d expected 1.\n%s",
                          $n,
                          $theQuery );
    }

    return $row;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Runs a query that returns 0 or more rows and returns those rows.
   *
   * @param string $theQuery The SQL statement.
   *
   * @return array
   */
  public static function executeRows( $theQuery )
  {
    $result = self::query( $theQuery );
    if (self::$ourHaveFetchAll)
    {
      $ret = $result->fetch_all( MYSQLI_ASSOC );
    }
    else
    {
      $ret = array();
      while ($row = $result->fetch_array( MYSQLI_ASSOC ))
      {
        $ret[] = $row;
      }
    }
    $result->free();

    if (self::$ourMySql->more_results()) self::$ourMySql->next_result();

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Runs a query that returns 0 or 1 row with one column and returns the value of that column.
   * Throws an exception if the query selects 2 or more rows.
   *
   * @param string $theQuery The SQL statement.
   *
   * @return string|int
   */
  public static function executeSingleton0( $theQuery )
  {
    $result = self::query( $theQuery );
    $row    = $result->fetch_array( MYSQL_NUM );
    $n      = $result->num_rows;
    $result->free();

    if (self::$ourMySql->more_results()) self::$ourMySql->next_result();

    if (!($n==0 || $n==1))
    {
      self::assertFailed( "Number of rows selected by query below is %d expected 0 or 1.\n%s",
                          $n,
                          $theQuery );
    }

    return $row[0];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Runs a query that returns 1 and only 1 row with 1 column and returns the value of that column.
   * Throws an exception if the query selects none, 2 or more rows.
   *
   * @param string $theQuery The SQL statement.
   *
   * @return string|int
   */
  public static function executeSingleton1( $theQuery )
  {
    $result = self::query( $theQuery );
    $row    = $result->fetch_array( MYSQL_NUM );
    $n      = $result->num_rows;
    $result->free();

    if (self::$ourMySql->more_results()) self::$ourMySql->next_result();

    if ($n!=1)
    {
      self::assertFailed( "Number of rows selected by query below is %d expected 1.\n%s",
                          $n,
                          $theQuery );
    }

    return $row[0];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query and shows the data in a formatted table (like mysql's default pager)
   *
   * @param string $theQuery The query.
   */
  public static function executeTable( $theQuery )
  {
    $ret = self::$ourMySql->multi_query( $theQuery );
    if (!$ret) self::sqlError( $theQuery );
    do
    {
      $result = self::$ourMySql->store_result();

      if (self::$ourMySql->errno) self::sqlError( 'mysqli::store_result' );
      if ($result)
      {
        $columns = array();

        // Get metadata to array.
        foreach ($result->fetch_fields() as $str_num => $column)
        {
          $columns[$str_num]['header'] = $column->name;
          $columns[$str_num]['type']   = $column->type;
          $columns[$str_num]['length'] = max( 4, $column->max_length, strlen( $column->name ) );
        }

        // Show the table header.
        self::executeTableShowHeader( $columns );

        // Show for all rows all columns.
        while ($row = $result->fetch_row())
        {
          // First row separator.
          echo "|";

          foreach ($row as $i => $value)
          {
            self::executeTableShowTableColumn( $columns[$i], $value );
            echo "|";
          }

          echo "\n";
        }

        // Show the table footer.
        self::executeTableShowFooter( $columns );
      }

      $continue = self::$ourMySql->more_results();
      if ($continue)
      {
        $tmp = self::$ourMySql->next_result();
        if ($tmp===false) self::sqlError( 'mysqli::next_result' );
      }
    } while ($continue);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the value of the MySQL variable max_allowed_packet.
   *
   * @return int
   */
  public static function getMaxAllowedPacket()
  {
    if (!isset(self::$ourMaxAllowedPacket))
    {
      $query              = "show variables like 'max_allowed_packet'";
      $max_allowed_packet = self::executeRow1( $query );

      self::$ourMaxAllowedPacket = $max_allowed_packet['Value'];

      // Note: When setting $ourChunkSize equal to $ourMaxAllowedPacket it is not possible to transmit a LOB
      // with size $ourMaxAllowedPacket bytes (but only $ourMaxAllowedPacket - 8 bytes). But when setting the size of
      // $ourChunkSize less than $ourMaxAllowedPacket than it is possible to transmit a LOB with size
      // $ourMaxAllowedPacket bytes.
      self::$ourChunkSize = min( self::$ourMaxAllowedPacket - 8, 1024 * 1024 );
    }

    return self::$ourMaxAllowedPacket;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Wrapper around mysqli::query, however on failure an exception is thrown.
   *
   * @param string $theQuery The SQL statement.
   *
   * @return \mysqli_result
   */
  public static function query( $theQuery )
  {
    $ret = self::$ourMySql->query( $theQuery );
    if ($ret===false) self::sqlError( $theQuery );

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a literal for a bit field that can be safely used in SQL statements.
   *
   * @param string $theBits The bit field.
   *
   * @return string
   */
  public static function quoteBit( $theBits )
  {
    if ($theBits===null || $theBits===false || $theBits==='')
    {
      return 'NULL';
    }
    else
    {
      return "b'".self::$ourMySql->real_escape_string( $theBits )."'";
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a literal for a numerical field that can be safely used in SQL statements.
   * Throws an exception if the value is not numeric.
   *
   * @param string $theValue The number.
   *
   * @return string
   */
  public static function quoteNum( $theValue )
  {
    if (is_numeric( $theValue )) return $theValue;
    if ($theValue===null || $theValue==='' || $theValue===false) return 'NULL';
    if ($theValue===true) return 1;

    self::assertFailed( "Value '%s' is not a number.", $theValue );
    // Not reached.

    // Keep our IDE happy.
    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a literal for a string field that can be safely used in SQL statements.
   *
   * @param string $theString The string.
   *
   * @return string
   */
  public static function quoteString( $theString )
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

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Escapes special characters in a string such that it can be safely used in SQL statements.
   *
   * @param string $theString The string.
   *
   * @return string
   */
  public static function realEscapeString( $theString )
  {
    return self::$ourMySql->real_escape_string( $theString );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Wrapper around mysqli::query, however on failure an exception is thrown.
   *
   * @param string $theQuery The SQL statement.
   */
  public static function realQuery( $theQuery )
  {
    $tmp = self::$ourMySql->real_query( $theQuery );
    if ($tmp===false) self::sqlError( $theQuery );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Rollbacks the current transaction (and starts a new transaction).
   * Wrapper around mysqli::rollback, however on failure an exception is thrown.
   */
  public static function rollback()
  {
    $ret = self::$ourMySql->rollback();
    if (!$ret) self::sqlError( 'mysqli::rollback' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the key of the first row in a row set for which a column has a specific value. Returns null if no row is
   * found.
   *
   * @param string  $theColumnName The column name (or in PHP terms the key in an row (i.e. array) in the row set).
   * @param string  $theValue      The value to be found.
   * @param array[] $theRowSet     The row set.
   *
   * @return int|null|string
   */
  public static function searchInRowSet( $theColumnName, $theValue, $theRowSet )
  {
    if (is_array( $theRowSet ))
    {
      foreach ($theRowSet as $key => $row)
      {
        if ($row[$theColumnName]===$theValue)
        {
          return $key;
        }
      }
    }

    return null;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Logs the warnings of the last executed SQL statement.
   * Wrapper around the SQL statement 'show warnings'.
   */
  public static function showWarnings()
  {
    self::executeLog( 'show warnings' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Throws an exception.
   */
  protected static function assertFailed()
  {
    $args    = func_get_args();
    $format  = array_shift( $args );
    $message = vsprintf( $format, $args );

    throw new \Exception( $message );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   *
   */
  protected static function sqlError( $theText )
  {
    $message = "MySQL Error no: ".self::$ourMySql->errno."\n";
    $message .= self::$ourMySql->error;
    $message .= "\n";
    $message .= $theText."\n";

    throw new \Exception( $message );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Show table footer.
   *
   * @param array $theColumns
   */
  private static function executeTableShowFooter( $theColumns )
  {
    $separator = '+';

    foreach ($theColumns as $column)
    {
      $separator .= str_repeat( '-', $column['length'] + 2 )."+";
    }
    echo $separator, "\n";
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Show table header.
   *
   * @param array $theColumns
   */
  private static function executeTableShowHeader( $theColumns )
  {
    $separator = '+';
    $header    = '|';

    foreach ($theColumns as $column)
    {
      $separator .= str_repeat( '-', $column['length'] + 2 )."+";
      $spaces = ($column['length'] + 2) - strlen( $column['header'] );

      $l_spaces = $spaces / 2;
      $r_spaces = ($spaces / 2) + ($spaces % 2);

      $l_spaces = ($l_spaces>0) ? str_repeat( " ", $l_spaces ) : '';
      $r_spaces = ($r_spaces>0) ? str_repeat( " ", $r_spaces ) : '';

      $header .= $l_spaces.$column['header'].$r_spaces.'|';
    }

    echo "\n", $separator, "\n";
    echo $header, "\n";
    echo $separator, "\n";
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Show table cell with data.
   *
   * @param array  $theColumn
   * @param string $theValue
   */
  private static function executeTableShowTableColumn( $theColumn, $theValue )
  {
    $spaces = str_repeat( " ", $theColumn['length'] - strlen( $theValue ) );

    switch ($theColumn['type'])
    {
      case 1: // tinyint
      case 2: // smallint
      case 3: // int
      case 4: // float
      case 5: // double
      case 8: // bigint
      case 9: // mediumint

        echo ' ', $spaces.$theValue, ' ';
        break;

      case 7: // timestamp
      case 10: // date
      case 11: // time
      case 12: // datetime
      case 13: // year
      case 16: // bit
      case 252: // is currently mapped to all text and blob types (MySQL 5.0.51a)
      case 253: // varchar
      case 254: // char

        echo ' ', $theValue.$spaces, ' ';
        break;

      case 246: // decimal

        echo ' ', $theValue.$spaces, ' ';
        break;

      default:
        self::assertFailed( "Unknown data type id '%s'.", $theColumn['type'] );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
