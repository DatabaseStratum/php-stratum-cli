<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * PhpStratum
 *
 * @copyright 2005-2015 Paul Water / Set Based IT Consultancy (https://www.setbased.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link
 */
// ---------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum;

// ---------------------------------------------------------------------------------------------------------------------
/**
 * Interface for defining classes for handling large result sets.
 */
interface BulkHandler
{
  // -------------------------------------------------------------------------------------------------------------------
  /**
   * Will be invoked for each row in the result set.
   *
   * @param string[] $row A row from the result set.
   *
   * @return void
   *
   * @since 1.0.0
   * @api
   */
  public function row($row);

  // -------------------------------------------------------------------------------------------------------------------
  /**
   * Will be invoked before the first row will be processed.
   *
   * @return void
   *
   * @since 1.0.0
   * @api
   */
  public function start();

  // -------------------------------------------------------------------------------------------------------------------
  /**
   * Will be invoked after the last row has been processed.
   *
   * @return void
   *
   * @since 1.0.0
   * @api
   */
  public function stop();

  // -------------------------------------------------------------------------------------------------------------------
}

// ---------------------------------------------------------------------------------------------------------------------
