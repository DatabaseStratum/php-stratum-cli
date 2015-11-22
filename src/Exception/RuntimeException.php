<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Exception;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for runtime exceptions.
 */
class RuntimeException extends \RuntimeException
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string $theFormat The format string of the error message, see
   *                          [sprintf](http://php.net/manual/function.sprintf.php).
   * @param mixed  ...$param  The arguments for the format string. However, if the first argument is an exception it
   *                          will be used as the [previous](http://php.net/manual/exception.construct.php) exception
   *                          for the exception chaining.
   */
  public function __construct($theFormat)
  {
    $args = func_get_args();
    array_shift($args);

    if (isset($args[0]) && is_a($args[0], '\Exception'))
    {
      $previous = array_shift($args);
    }
    else
    {
      $previous = null;
    }

    parent::__construct(vsprintf($theFormat, $args), 0, $previous);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
