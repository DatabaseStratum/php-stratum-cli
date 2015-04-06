<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * phpStratum
 *
 * @copyright 2005-2015 Paul Water / Set Based IT Consultancy (https://www.setbased.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link
 */
// ---------------------------------------------------------------------------------------------------------------------
namespace SetBased\PhpStratum;

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
   * @api
   *
   * @param string[] $theRow A row from the result set.
   */
  public function row( $theRow );

  // -------------------------------------------------------------------------------------------------------------------
  /**
   * Will be invoked before the first row will be processed.
   *
   * @api
   */
  public function start();

  // -------------------------------------------------------------------------------------------------------------------
  /**
   * Will be invoked after the last row has been processed.
   *
   * @api
   */
  public function stop();

  // -------------------------------------------------------------------------------------------------------------------
}

// ---------------------------------------------------------------------------------------------------------------------
