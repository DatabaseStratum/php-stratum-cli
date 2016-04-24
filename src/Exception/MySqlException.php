<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Exception;

use SetBased\Exception\RuntimeException;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Exception for situations where the execution of s SQL query has failed.
 */
class MySqlException extends RuntimeException
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The error code value of the error ($mysqli->errno).
   *
   * @var int
   */
  private $errno;

  /**
   * Description of the last error ($mysqli->error).
   *
   * @var string
   */
  private $error;

  /**
   * The executed SQL query or description of function.
   *
   * @var string
   */
  private $query;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param int    $errno   The error code value of the error ($mysqli->errno).
   * @param string $error   Description of the last error ($mysqli->error).
   * @param string $message The SQL query or function name.
   */
  public function __construct($errno, $error, $message)
  {
    parent::__construct('%s', self::message($errno, $error, $message));

    $this->errno = $errno;
    $this->error = $error;
    $this->query = $message;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the error code value of the error
   *
   * @return int
   */
  public function getErrno()
  {
    return $this->query;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the description of the error.
   *
   * @return string
   */
  public function getError()
  {
    return $this->error;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'MySQL Error';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns SQL query or function name.
   *
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Composes the exception message.
   *
   * @param int    $errno The error code value of the error ($mysqli->errno).
   * @param string $error Description of the error ($mysqli->error).
   * @param string $query The SQL query.
   *
   * @return string
   */
  private function message($errno, $error, $query)
  {
    $message = 'MySQL Error no: '.$errno."\n";
    $message .= $error;
    $message .= "\n";

    $query = trim($query);
    if (strpos($query, "\n")!==false)
    {
      // Query is a multi line query.
      $message .= "\n";

      // Prepend each line with line number.
      $lines  = explode("\n", $query);
      $digits = ceil(log(sizeof($lines), 10));
      $format = sprintf("%%%dd %%s\n", $digits);
      foreach ($lines as $i => $line)
      {
        $message .= sprintf($format, $i + 1, $line);
      }
    }
    else
    {
      // Query is a single line query.
      $message .= $query;
    }

    return $message;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
