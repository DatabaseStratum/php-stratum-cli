<?php

namespace SetBased\Stratum\Exception;

use SetBased\Exception\RuntimeException;

/**
 * Exception for situations where the result (set) of a query does not meet the expectations. Either a mismatch between
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
   * @var string
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
   * @param string $expectedRowCount The expected number of rows selected.
   * @param int    $actualRowCount   The actual number of rows selected.
   * @param string $message          The SQL query
   */
  public function __construct(string $expectedRowCount, int $actualRowCount, string $message)
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
  public function getActualNumberRows(): int
  {
    return $this->actualRowCount;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the expected number of selected rows.
   *
   * @return string
   */
  public function getExpectedNumberRows(): string
  {
    return $this->expectedRowCount;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
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
  public function getQuery(): string
  {
    return $this->query;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Composes the exception message.
   *
   * @param string $expectedRowCount The expected number of rows selected.
   * @param int    $actualRowCount   The actual number of rows selected.
   * @param string $query            The SQL query.
   *
   * @return string
   */
  private function message(string $expectedRowCount, int $actualRowCount, string $query): string
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
