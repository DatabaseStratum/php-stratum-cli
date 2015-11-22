<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Exception;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for situations where PHP code has fallen through a switch statement or a combination of if-elseif statements.
 */
class FallenException extends RuntimeException
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string $theName  The name or description of the variable of expression.
   * @param string $theValue The actual value that.
   *
   * Example:
   * ```
   *  $size = 'xxl';
   *  switch ($size)
   *  {
   *    case 'S':
   *      echo 'small';
   *      break;
   *
   *    case 'M':
   *      echo 'medium';
   *      break;
   *
   *    case 'L':
   *      echo 'small';
   *      break;
   *
   *    default:
   *      throw new FallenException('size', $size);
   *  }
   * ```
   */
  public function __construct($theName, $theValue)
  {
    parent::__construct("Unknown or unexpected value '%s' for '%s'.", $theValue, $theName);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
