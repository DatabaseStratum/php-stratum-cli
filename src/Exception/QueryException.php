<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Exception;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Exception for situations where the execution of SQL query has failed.
 */
class QueryException extends MySqlException
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param int    $errno   The error code value of the error ($mysqli->errno).
   * @param string $error   Description of the last error ($mysqli->error).
   * @param string $message The SQL query.
   */
  public function __construct($errno, $error, $message)
  {
    parent::__construct($errno, $error, $message);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'SQL Error';
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
