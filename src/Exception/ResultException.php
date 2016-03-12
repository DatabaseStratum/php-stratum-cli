<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Exception;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Exception for situations where the result (set) a query does not meet the expectations. Either a mismatch between
 * the actual and expected numbers of rows selected or a unexpected NULL value was selected.
 */
class ResultException extends \RuntimeException
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The actual number of selected rows selected.
   *
   * @var int
   */
  private $myActualRowCount;

  /**
   * The expected number selected of rows selected.
   *
   * @var int
   */
  private $myExpectedRowCount;

  /**
   * The executed SQL query.
   *
   * @var string
   */
  private $myQuery;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param int    $theExpectedRowCount The expected number of rows selected.
   * @param int    $theActualRowCount   The actual number of rows selected.
   * @param string $theQuery            The SQL query
   */
  public function __construct($theExpectedRowCount, $theActualRowCount, $theQuery)
  {
    parent::__construct(self::message($theExpectedRowCount, $theActualRowCount, $theQuery));

    $this->myExpectedRowCount = $theExpectedRowCount;
    $this->myActualRowCount   = $theActualRowCount;
    $this->myQuery            = $theQuery;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the actual number of selected rows.
   *
   * @return int
   */
  public function getActualNumberRows()
  {
    return $this->myActualRowCount;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the expected number of selected rows.
   *
   * @return int
   */
  public function getExpectedNumberRows()
  {
    return $this->myExpectedRowCount;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the executed SQL query.
   *
   * @return string
   */
  public function getQuery()
  {
    return $this->myQuery;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Composes the exception message.
   *
   * @param int    $theExpectedRowCount The expected number of rows selected.
   * @param int    $theActualRowCount   The actual number of rows selected.
   * @param string $theQuery            The SQL query
   *
   * @return string
   */
  private function message($theExpectedRowCount, $theActualRowCount, $theQuery)
  {
    $query = trim($theQuery);

    $message = 'Wrong number of rows selected.';
    $message .= "\n";
    $message .= sprintf("Expected number of rows: %s.\n", $theExpectedRowCount);
    $message .= sprintf("Actual number of rows: %s.\n", $theActualRowCount);
    $message .= 'Query:';
    $message .= (strpos($query, "\n")!==false) ? "\n" : ' ';
    $message .= $query;

    return $message;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
