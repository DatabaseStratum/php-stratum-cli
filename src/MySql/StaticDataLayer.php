<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * PhpStratum
 *
 * @copyright 2005-2015 Paul Water / Set Based IT Consultancy (https://www.setbased.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link
 */
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql;

use SetBased\Exception\FallenException;
use SetBased\Exception\RuntimeException;
use SetBased\Stratum\BulkHandler;
use SetBased\Stratum\Exception\ResultException;
use SetBased\Stratum\MySql\Exception\DataLayerException;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Supper class for a static stored routine wrapper class.
 */
class StaticDataLayer
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The default character set to be used when sending data from and to the MySQL instance.
   *
   * @var string
   *
   * @since 1.0.0
   * @api
   */
  public static $charSet = 'utf8';

  /**
   * If set queries must be logged.
   *
   * @var bool
   *
   * @since 1.0.0
   * @api
   */
  public static $logQueries = false;

  /**
   * The SQL mode of the MySQL instance.
   *
   * @var string
   *
   * @since 1.0.0
   * @api
   */
  public static $sqlMode = 'STRICT_ALL_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_AUTO_VALUE_ON_ZERO,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ONLY_FULL_GROUP_BY';

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
   *
   * @since 1.0.0
   * @api
   */
  public static $transactionIsolationLevel = 'READ-COMMITTED';

  /**
   * Chunk size when transmitting LOB to the MySQL instance. Must be less than max_allowed_packet.
   *
   * @var int
   */
  protected static $chunkSize;

  /**
   * True if method mysqli_result::fetch_all exists (i.e. we are using MySQL native driver).
   *
   * @var bool
   */
  protected static $haveFetchAll;

  /**
   * Value of variable max_allowed_packet
   *
   * @var int
   */
  protected static $maxAllowedPacket;

  /**
   * The connection between PHP and the MySQL instance.
   *
   * @var \mysqli
   */
  protected static $mysqli;

  /**
   * The query log.
   *
   * @var \array[]
   */
  protected static $queryLog;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Starts a transaction.
   *
   * Wrapper around [mysqli::autocommit](http://php.net/manual/mysqli.autocommit.php), however on failure an exception
   * is thrown.
   *
   * @since 1.0.0
   * @api
   */
  public static function begin()
  {
    $ret = self::$mysqli->autocommit(false);
    if (!$ret) self::mySqlError('mysqli::autocommit');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param \mysqli_stmt $stmt
   * @param array        $out
   */
  public static function bindAssoc($stmt, &$out)
  {
    $data = $stmt->result_metadata();
    if (!$data) self::mySqlError('mysqli_stmt::result_metadata');

    $fields = [];
    $out    = [];

    while ($field = $data->fetch_field())
    {
      $fields[] = &$out[$field->name];
    }

    $b = call_user_func_array([$stmt, 'bind_result'], $fields);
    if ($b===false) self::mySqlError('mysqli_stmt::bind_result');

    $data->free();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Commits the current transaction (and starts a new transaction).
   *
   * Wrapper around [mysqli::commit](http://php.net/manual/mysqli.commit.php), however on failure an exception is
   * thrown.
   *
   * @since 1.0.0
   * @api
   */
  public static function commit()
  {
    $ret = self::$mysqli->commit();
    if (!$ret) self::mySqlError('mysqli::commit');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Connects to a MySQL instance.
   *
   * Wrapper around [mysqli::__construct](http://php.net/manual/mysqli.construct.php), however on failure an exception
   * is thrown.
   *
   * @param string $host     The hostname.
   * @param string $user     The MySQL user name.
   * @param string $password The password.
   * @param string $database The default database.
   * @param int    $port     The port number.
   *
   * @since 1.0.0
   * @api
   */
  public static function connect($host, $user, $password, $database, $port = 3306)
  {
    self::$mysqli = new \mysqli($host, $user, $password, $database, $port);
    if (self::$mysqli->connect_errno)
    {
      $message = 'MySQL Error no: '.self::$mysqli->connect_errno."\n";
      $message .= str_replace('%', '%%', self::$mysqli->connect_error);
      $message .= "\n";

      throw new RuntimeException($message);
    }

    // Set the default character set.
    if (self::$charSet)
    {
      $ret = self::$mysqli->set_charset(self::$charSet);
      if (!$ret) self::mySqlError('mysqli::set_charset');
    }

    // Set the SQL mode.
    if (self::$sqlMode)
    {
      self::executeNone("SET sql_mode = '".self::$sqlMode."'");
    }

    // Set transaction isolation level.
    if (self::$transactionIsolationLevel)
    {
      self::executeNone("SET SESSION tx_isolation = '".self::$transactionIsolationLevel."'");
    }

    // Set flag to use method mysqli_result::fetch_all if we are using MySQL native driver.
    self::$haveFetchAll = method_exists('mysqli_result', 'fetch_all');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Closes the connection to the MySQL instance, if connected.
   *
   * @since 1.0.0
   * @api
   */
  public static function disconnect()
  {
    if (self::$mysqli)
    {
      self::$mysqli->close();
      self::$mysqli = null;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query using a bulk handler.
   *
   * @param BulkHandler $bulkHandler The bulk handler.
   * @param string      $query       The SQL statement.
   *
   * @since 1.0.0
   * @api
   */
  public static function executeBulk($bulkHandler, $query)
  {
    self::realQuery($query);

    $bulkHandler->start();

    $result = self::$mysqli->use_result();
    while ($row = $result->fetch_assoc())
    {
      $bulkHandler->row($row);
    }
    $result->free();

    $bulkHandler->stop();

    if (self::$mysqli->more_results()) self::$mysqli->next_result();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query and logs the result set.
   *
   * @param string $queries The query or multi query.
   *
   * @return int The total number of rows selected/logged.
   *
   * @since 1.0.0
   * @api
   */
  public static function executeLog($queries)
  {
    // Counter for the number of rows written/logged.
    $n = 0;

    self::multiQuery($queries);
    do
    {
      $result = self::$mysqli->store_result();
      if (self::$mysqli->errno) self::mySqlError('mysqli::store_result');
      if ($result)
      {
        $fields = $result->fetch_fields();
        while ($row = $result->fetch_row())
        {
          $line = '';
          foreach ($row as $i => $field)
          {
            if ($i>0) $line .= ' ';
            $line .= str_pad($field, $fields[$i]->max_length);
          }
          echo date('Y-m-d H:i:s'), ' ', $line, "\n";
          $n++;
        }
        $result->free();
      }

      $continue = self::$mysqli->more_results();
      if ($continue)
      {
        $tmp = self::$mysqli->next_result();
        if ($tmp===false) self::mySqlError('mysqli::next_result');
      }
    } while ($continue);

    return $n;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query that does not select any rows.
   *
   * @param string $query The SQL statement.
   *
   * @return int The number of affected rows (if any).
   *
   * @since 1.0.0
   * @api
   */
  public static function executeNone($query)
  {
    self::query($query);

    $n = self::$mysqli->affected_rows;

    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    return $n;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query that returns 0 or 1 row.
   * Throws an exception if the query selects 2 or more rows.
   *
   * @param string $query The SQL statement.
   *
   * @return array|null The selected row.
   * @throws ResultException
   *
   * @since 1.0.0
   * @api
   */
  public static function executeRow0($query)
  {
    $result = self::query($query);
    $row    = $result->fetch_assoc();
    $n      = $result->num_rows;
    $result->free();

    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    if (!($n==0 || $n==1))
    {
      throw new ResultException('0 or 1', $n, $query);
    }

    return $row;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query that returns 1 and only 1 row.
   * Throws an exception if the query selects none, 2 or more rows.
   *
   * @param string $query The SQL statement.
   *
   * @return array The selected row.
   * @throws ResultException
   *
   * @since 1.0.0
   * @api
   */
  public static function executeRow1($query)
  {
    $result = self::query($query);
    $row    = $result->fetch_assoc();
    $n      = $result->num_rows;
    $result->free();

    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    if ($n!=1)
    {
      throw new ResultException('1', $n, $query);
    }

    return $row;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query that returns 0 or more rows.
   *
   * @param string $query The SQL statement.
   *
   * @return \array[] The selected rows.
   *
   * @since 1.0.0
   * @api
   */
  public static function executeRows($query)
  {
    $result = self::query($query);
    if (self::$haveFetchAll)
    {
      $ret = $result->fetch_all(MYSQLI_ASSOC);
    }
    else
    {
      $ret = [];
      while ($row = $result->fetch_assoc())
      {
        $ret[] = $row;
      }
    }
    $result->free();

    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query that returns 0 or 1 row with one column.
   * Throws an exception if the query selects 2 or more rows.
   *
   * @param string $query The SQL statement.
   *
   * @return int|string|null The selected value.
   * @throws ResultException
   *
   * @since 1.0.0
   * @api
   */
  public static function executeSingleton0($query)
  {
    $result = self::query($query);
    $row    = $result->fetch_array(MYSQLI_NUM);
    $n      = $result->num_rows;
    $result->free();

    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    if (!($n==0 || $n==1))
    {
      throw new ResultException('0 or 1', $n, $query);
    }

    return $row[0];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query that returns 1 and only 1 row with 1 column.
   * Throws an exception if the query selects none, 2 or more rows.
   *
   * @param string $query The SQL statement.
   *
   * @return int|string The selected value.
   * @throws ResultException
   *
   * @since 1.0.0
   * @api
   */
  public static function executeSingleton1($query)
  {
    $result = self::query($query);
    $row    = $result->fetch_array(MYSQLI_NUM);
    $n      = $result->num_rows;
    $result->free();

    if (self::$mysqli->more_results()) self::$mysqli->next_result();

    if ($n!=1)
    {
      throw new ResultException('1', $n, $query);
    }

    return $row[0];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query and shows the data in a formatted in a table (like mysql's default pager) of in multiple tables
   * (in case of a multi query).
   *
   * @param string $query The query.
   *
   * @return int The total number of rows in the tables.
   *
   * @since 1.0.0
   * @api
   */
  public static function executeTable($query)
  {
    $row_count = 0;

    self::multiQuery($query);
    do
    {
      $result = self::$mysqli->store_result();
      if (self::$mysqli->errno) self::mySqlError('mysqli::store_result');
      if ($result)
      {
        $columns = [];

        // Get metadata to array.
        foreach ($result->fetch_fields() as $str_num => $column)
        {
          $columns[$str_num]['header'] = $column->name;
          $columns[$str_num]['type']   = $column->type;
          $columns[$str_num]['length'] = max(4, $column->max_length, strlen($column->name));
        }

        // Show the table header.
        self::executeTableShowHeader($columns);

        // Show for all rows all columns.
        while ($row = $result->fetch_row())
        {
          $row_count++;

          // First row separator.
          echo '|';

          foreach ($row as $i => $value)
          {
            self::executeTableShowTableColumn($columns[$i], $value);
            echo '|';
          }

          echo "\n";
        }

        // Show the table footer.
        self::executeTableShowFooter($columns);
      }

      $continue = self::$mysqli->more_results();
      if ($continue)
      {
        $tmp = self::$mysqli->next_result();
        if ($tmp===false) self::mySqlError('mysqli::next_result');
      }
    } while ($continue);

    return $row_count;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the value of the MySQL variable max_allowed_packet.
   *
   * @return int
   */
  public static function getMaxAllowedPacket()
  {
    if (!isset(self::$maxAllowedPacket))
    {
      $query              = "show variables like 'max_allowed_packet'";
      $max_allowed_packet = self::executeRow1($query);

      self::$maxAllowedPacket = $max_allowed_packet['Value'];

      // Note: When setting $chunkSize equal to $maxAllowedPacket it is not possible to transmit a LOB
      // with size $maxAllowedPacket bytes (but only $maxAllowedPacket - 8 bytes). But when setting the size of
      // $chunkSize less than $maxAllowedPacket than it is possible to transmit a LOB with size
      // $maxAllowedPacket bytes.
      self::$chunkSize = (int)min(self::$maxAllowedPacket - 8, 1024 * 1024);
    }

    return self::$maxAllowedPacket;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the query log.
   *
   * To enable the query log set {@link $queryLog} to true.
   *
   * @return \array[]
   *
   * @since 1.0.0
   * @api
   */
  public static function getQueryLog()
  {
    return self::$queryLog;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the first row in a row set for which a column has a specific value.
   *
   * Throws an exception if now row is found.
   *
   * @param string  $columnName The column name (or in PHP terms the key in an row (i.e. array) in the row set).
   * @param mixed   $value      The value to be found.
   * @param array[] $rowSet     The row set.
   *
   * @return mixed
   *
   * @since 1.0.0
   * @api
   */
  public static function getRowInRowSet($columnName, $value, $rowSet)
  {
    if (is_array($rowSet))
    {
      foreach ($rowSet as $row)
      {
        if ((string)$row[$columnName]==(string)$value)
        {
          return $row;
        }
      }
    }

    throw new RuntimeException("Value '%s' for column '%s' not found in row set.", $value, $columnName);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes multiple SQL statements.
   *
   * Wrapper around [multi_mysqli::query](http://php.net/manual/mysqli.multi-query.php), however on failure an exception
   * is thrown.
   *
   * @param string $queries The SQL statements.
   *
   * @return bool
   */
  public static function multiQuery($queries)
  {
    if (self::$logQueries)
    {
      $time0 = microtime(true);

      $ret = self::$mysqli->multi_query($queries);
      if ($ret===false)
      {
        throw new DataLayerException(self::$mysqli->errno, self::$mysqli->error, $queries);
      }

      self::$queryLog[] = ['query' => $queries, 'time' => microtime(true) - $time0];
    }
    else
    {
      $ret = self::$mysqli->multi_query($queries);
      if ($ret===false)
      {
        throw new DataLayerException(self::$mysqli->errno, self::$mysqli->error, $queries);
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a SQL statement.
   *
   * Wrapper around [mysqli::query](http://php.net/manual/mysqli.query.php), however on failure an exception is thrown.
   *
   * @param string $query The SQL statement.
   *
   * @return \mysqli_result
   * @throws DataLayerException
   */
  public static function query($query)
  {
    if (self::$logQueries)
    {
      $time0 = microtime(true);

      $ret = self::$mysqli->query($query);
      if ($ret===false)
      {
        throw new DataLayerException(self::$mysqli->errno, self::$mysqli->error, $query);
      }

      self::$queryLog[] = ['query' => $query, 'time' => microtime(true) - $time0];
    }
    else
    {
      $ret = self::$mysqli->query($query);
      if ($ret===false)
      {
        throw new DataLayerException(self::$mysqli->errno, self::$mysqli->error, $query);
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a literal for a bit field that can be safely used in SQL statements.
   *
   * @param string $bits The bit field.
   *
   * @return string
   */
  public static function quoteBit($bits)
  {
    if ($bits===null || $bits===false || $bits==='')
    {
      return 'NULL';
    }
    else
    {
      return "b'".self::$mysqli->real_escape_string($bits)."'";
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  public static function quoteListOfInt($list, $delimiter, $enclosure, $escape)
  {
    if ($list===null || $list===false || $list==='' || $list===[])
    {
      return 'NULL';
    }
    else
    {
      $ret = '';
      if (is_scalar($list))
      {
        $list = str_getcsv($list, $delimiter, $enclosure, $escape);
      }
      elseif (is_array($list))
      {
        // Nothing to do.
        ;
      }
      else
      {
        throw new RuntimeException("Unexpected parameter type '%s'. Array or scalar expected.", gettype($list));
      }

      foreach ($list as $number)
      {
        if ($list===null || $list===false || $list==='')
        {
          throw new RuntimeException('Empty values are not allowed.');
        }
        if (!is_numeric($number))
        {
          throw new RuntimeException("Value '%s' is not a number.", (is_scalar($number)) ? $number : gettype($number));
        }

        if ($ret) $ret .= ',';
        $ret .= $number;
      }

      return self::quoteString($ret);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a literal for a numerical field that can be safely used in SQL statements. Throws an exception if the value
   * is not numeric.
   *
   * To be more specific:
   * <ul>
   * <li>Returns the string representation of an int, float, and a string that is a number.
   * <li>Returns 'NULL' if the value is null or ''.
   * <li>Returns '0' if the value if false.
   * <li>Returns '1' if the value is true.
   * <li>Throws an exception in all other cases.
   * </ul>
   *
   * @param int|float|bool|null $value The numerical value.
   *
   * @return string
   */
  public static function quoteNum($value)
  {
    if (is_numeric($value)) return (string)$value;
    if ($value===null || $value==='') return 'NULL';
    if ($value===false) return '0';
    if ($value===true) return '1';

    throw new RuntimeException("Value '%s' is not a number.", (is_scalar($value) ? $value : gettype($value)));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a literal for a string field that can be safely used in SQL statements.
   *
   * To be more specific:
   * <ul>
   * <li>Returns 'NULL' if the value is null, '', or false.
   * <li>Otherwise returns the escaped value.
   * </ul>
   *
   * @param string|null $value The value.
   *
   * @return string
   */
  public static function quoteString($value)
  {
    if ($value===null || $value===false || $value==='') return 'NULL';
    if (is_scalar($value)) return "'".self::$mysqli->real_escape_string($value)."'";

    throw new RuntimeException("'%s' is not a string.", gettype($value));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Escapes special characters in a string such that it can be safely used in SQL statements.
   *
   * Wrapper around [mysqli::real_escape_string](http://php.net/manual/mysqli.real-escape-string.php).
   *
   * @param string $string The string.
   *
   * @return string
   */
  public static function realEscapeString($string)
  {
    return self::$mysqli->real_escape_string($string);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Rollbacks the current transaction (and starts a new transaction).
   *
   * Wrapper around [mysqli::rollback](http://php.net/manual/en/mysqli.rollback.php), however on failure an exception
   * is thrown.
   *
   * @since 1.0.0
   * @api
   */
  public static function rollback()
  {
    $ret = self::$mysqli->rollback();
    if (!$ret) self::mySqlError('mysqli::rollback');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the key of the first row in a row set for which a column has a specific value. Returns null if no row is
   * found.
   *
   * @param string  $columnName The column name (or in PHP terms the key in an row (i.e. array) in the row set).
   * @param string  $value      The value to be found.
   * @param array[] $rowSet     The row set.
   *
   * @return int|null|string
   *
   * @since 1.0.0
   * @api
   */
  public static function searchInRowSet($columnName, $value, $rowSet)
  {
    if (is_array($rowSet))
    {
      foreach ($rowSet as $key => $row)
      {
        if ((string)$row[$columnName]==(string)$value)
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
   *
   * Wrapper around the SQL statement [show warnings](https://dev.mysql.com/doc/refman/5.6/en/show-warnings.html).
   *
   * @since 1.0.0
   * @api
   */
  public static function showWarnings()
  {
    self::executeLog('show warnings');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Throws an exception with error information provided by MySQL/[mysqli](http://php.net/manual/en/class.mysqli.php).
   *
   * This method must called after a method of [mysqli](http://php.net/manual/en/class.mysqli.php) returns an
   * error only.
   *
   * @param string $method The name of the method that has failed.
   *
   * @throws RuntimeException
   */
  protected static function mySqlError($method)
  {
    $message = 'MySQL Error no: '.self::$mysqli->errno."\n";
    $message .= self::$mysqli->error;
    $message .= "\n";
    $message .= $method;
    $message .= "\n";

    throw new RuntimeException('%s', $message);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Execute an SQL query.
   *
   * Wrapper around [mysqli::real_query](http://php.net/manual/en/mysqli.real-query.php), however on failure an
   * exception is thrown.
   *
   * @param string $query The SQL statement.
   */
  protected static function realQuery($query)
  {
    if (self::$logQueries)
    {
      $time0 = microtime(true);

      $ret = self::$mysqli->real_query($query);
      if ($ret===false)
      {
        throw new DataLayerException(self::$mysqli->errno, self::$mysqli->error, $query);
      }

      self::$queryLog[] = ['query' => $query,
                           'time'  => microtime(true) - $time0];
    }
    else
    {
      $ret = self::$mysqli->real_query($query);
      if ($ret===false)
      {
        throw new DataLayerException(self::$mysqli->errno, self::$mysqli->error, $query);
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Helper method for method executeTable. Shows table footer.
   *
   * @param array $columns
   */
  private static function executeTableShowFooter($columns)
  {
    $separator = '+';

    foreach ($columns as $column)
    {
      $separator .= str_repeat('-', $column['length'] + 2).'+';
    }
    echo $separator, "\n";
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Helper method for method executeTable. Shows table header.
   *
   * @param array $columns
   */
  private static function executeTableShowHeader($columns)
  {
    $separator = '+';
    $header    = '|';

    foreach ($columns as $column)
    {
      $separator .= str_repeat('-', $column['length'] + 2).'+';
      $spaces = ($column['length'] + 2) - strlen($column['header']);

      $l_spaces = $spaces / 2;
      $r_spaces = ($spaces / 2) + ($spaces % 2);

      $l_spaces = ($l_spaces>0) ? str_repeat(' ', $l_spaces) : '';
      $r_spaces = ($r_spaces>0) ? str_repeat(' ', $r_spaces) : '';

      $header .= $l_spaces.$column['header'].$r_spaces.'|';
    }

    echo "\n", $separator, "\n";
    echo $header, "\n";
    echo $separator, "\n";
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Helper method for method executeTable. Shows table cell with data.
   *
   * @param array  $column
   * @param string $value
   */
  private static function executeTableShowTableColumn($column, $value)
  {
    $spaces = str_repeat(' ', $column['length'] - strlen($value));

    switch ($column['type'])
    {
      case 1: // tinyint
      case 2: // smallint
      case 3: // int
      case 4: // float
      case 5: // double
      case 8: // bigint
      case 9: // mediumint
        echo ' ', $spaces.$value, ' ';
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
        echo ' ', $value.$spaces, ' ';
        break;

      case 246: // decimal
        echo ' ', $value.$spaces, ' ';
        break;

      default:
        throw new FallenException('data type id', $column['type']);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
