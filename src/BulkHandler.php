<?php
declare(strict_types=1);

namespace SetBased\Stratum;

/**
 * Interface for defining classes for handling large result sets.
 */
interface BulkHandler
{
  // -------------------------------------------------------------------------------------------------------------------
  /**
   * Will be invoked for each row in the result set.
   *
   * @param array $row A row from the result set.
   *
   * @return void
   *
   * @since 1.0.0
   * @api
   */
  public function row(array $row): void;

  // -------------------------------------------------------------------------------------------------------------------
  /**
   * Will be invoked before the first row will be processed.
   *
   * @return void
   *
   * @since 1.0.0
   * @api
   */
  public function start(): void;

  // -------------------------------------------------------------------------------------------------------------------
  /**
   * Will be invoked after the last row has been processed.
   *
   * @return void
   *
   * @since 1.0.0
   * @api
   */
  public function stop(): void;

  // -------------------------------------------------------------------------------------------------------------------
}

// ---------------------------------------------------------------------------------------------------------------------
