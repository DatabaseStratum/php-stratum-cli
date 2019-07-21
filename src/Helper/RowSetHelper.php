<?php
declare(strict_types=1);

namespace SetBased\Stratum\Helper;

use SetBased\Exception\LogicException;

/**
 * Utility class for operations on row sets.
 */
class RowSetHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the key of the first row in a row set for which a column has a specific value. Throws an exception
   * when the value is not found.
   *
   * @param array[] $rowSet     The row set.
   * @param string  $columnName The column name (or in PHP terms the key in an row (i.e. array) in the row set).
   * @param mixed   $value      The value to be found.
   *
   * @return array
   *
   * @since 1.0.0
   * @api
   */
  public static function filter(array $rowSet, string $columnName, $value): array
  {
    $ret = [];

    foreach ($rowSet as $row)
    {
      if ($row[$columnName]===$value)
      {
        $ret[] = $row;
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the key of the first row in a row set for which a column has a specific value. Throws an exception
   * when the value is not found.
   *
   * @param array[] $rowSet     The row set.
   * @param string  $columnName The column name (or in PHP terms the key in an row (i.e. array) in the row set).
   * @param mixed   $value      The value to be found.
   *
   * @return int
   *
   * @since 1.0.0
   * @api
   */
  public static function findInRowSet(array $rowSet, string $columnName, $value): int
  {
    $key = static::searchInRowSet($rowSet, $columnName, $value);
    if ($key===null)
    {
      throw new LogicException("Value '%s' not found", $value);
    }

    return $key;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the key of the first row in a row set for which a column has a specific value. Returns null if no row is
   * found.
   *
   * @param array[] $rowSet     The row set.
   * @param string  $columnName The column name (or in PHP terms the key in an row (i.e. array) in the row set).
   * @param mixed   $value      The value to be found.
   *
   * @return int|null
   *
   * @since 1.0.0
   * @api
   */
  public static function searchInRowSet(array $rowSet, string $columnName, $value): ?int
  {
    foreach ($rowSet as $key => $row)
    {
      if ($row[$columnName]===$value)
      {
        return $key;
      }
    }

    return null;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
