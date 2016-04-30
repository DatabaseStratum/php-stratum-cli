<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Exception;

use SetBased\Exception\RuntimeException;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Exception for situations where the result (set) a query does not meet the expectations. Either a mismatch between
 * the actual and expected numbers of rows selected or an unexpected NULL value was selected.
 */
class ResultException extends RuntimeException
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The actual number of selected rows selected.
   *
   * @var int
   */
  private $actualRowCount;

  /**
   * The expected number selected of rows selected.
   *
   * @var int
   */
  private $expectedRowCount;

  /**
   * The executed SQL query.
   *
   * @var string
   */
  private $query;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param int    $expectedRowCount The expected number of rows selected.
   * @param int    $actualRowCount   The actual number of rows selected.
   * @param string $message          The SQL query
   */
  public function __construct($expectedRowCount, $actualRowCount, $message)
  {
    parent::__construct('%s', self::message($expectedRowCount, $actualRowCount, $message));

    $this->expectedRowCount = $expectedRowCount;
    $this->actualRowCount   = $actualRowCount;
    $this->query            = $message;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the actual number of selected rows.
   *
   * @return int
   */
  public function getActualNumberRows()
  {
    return $this->actualRowCount;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the expected number of selected rows.
   *
   * @return int
   */
  public function getExpectedNumberRows()
  {
    return $this->expectedRowCount;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'Wrong row count';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the executed SQL query.
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
   * @param int    $expectedRowCount The expected number of rows selected.
   * @param int    $actualRowCount   The actual number of rows selected.
   * @param string $query            The SQL query
   *
   * @return string
   */
  private function message($expectedRowCount, $actualRowCount, $query)
  {
    $query = trim($query);

    $message = 'Wrong number of rows selected.';
    $message .= "\n";
    $message .= sprintf("Expected number of rows: %s.\n", $expectedRowCount);
    $message .= sprintf("Actual number of rows: %s.\n", $actualRowCount);
    $message .= 'Query:';
    $message .= (strpos($query, "\n")!==false) ? "\n" : ' ';
    $message .= $query;

    return $message;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
