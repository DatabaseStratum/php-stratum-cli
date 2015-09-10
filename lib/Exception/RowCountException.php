<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Exception;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Exception for situations where the row count of a select query does not match with the expected number of rows.
 */
class RowCountException extends \Exception
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
   * @var string
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
   * @param string $theExpectedRowCount The expected number of rows selected.
   * @param int    $theActualRowCount   The actual number of rows selected.
   * @param string $theQuery            The SQL query
   */
  public function __construct( $theExpectedRowCount, $theActualRowCount, $theQuery )
  {
    parent::__construct( "Wrong number of rows selected." );

    $this->myExpectedRowCount = $theExpectedRowCount;
    $this->myActualRowCount   = $theActualRowCount;
    $this->myQuery            = $theQuery;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the string representation of the exception. This includes the expected and actual of number rows and the
   * SQL query.
   *
   * @return string
   */
  public function __toString()
  {
    $string = parent::__toString();
    $string .= "\n";
    $string .= sprintf( "Expected number of rows: %s.\n", $this->myExpectedRowCount );
    $string .= sprintf( "Actual number of rows: %s.\n", $this->myActualRowCount );
    $string .= "Query:\n";
    $string .= $this->myQuery;

    return $string;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the executed SQL query.
   *
   * @return string
   */
  public function geQuery()
  {
    return $this->myQuery;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the actual number of selected rows.
   *
   * @return string
   */
  public function getActualNumberRows()
  {
    return $this->myActualRowCount;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the expected number of selected rows.
   *
   * @return string
   */
  public function getExpectedNumberRows()
  {
    return $this->myExpectedRowCount;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
