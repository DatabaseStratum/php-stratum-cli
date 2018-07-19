<?php
declare(strict_types=1);

namespace SetBased\Stratum\NameMangler;

/**
 * A name mangler for stored routines and parameters names creating method and parameter names that confirm to
 * the PSR-1 Basic Coding Standard.
 */
class PsrNameMangler implements NameMangler
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the routine name after the first underscore in camelCase. I.e. abc_foo_bar => fooBar.
   *
   * @param string $routineName The name of the stored routine.
   *
   * @return string
   */
  public static function getMethodName(string $routineName): string
  {
    return lcfirst(str_replace(' ', '', ucwords(strtr($routineName, '_', ' '))));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the parameter name into camelCase.
   *
   * @param string $parameterName The name of the parameter in the stored routine.
   *
   * @return string
   */
  public static function getParameterName(string $parameterName): string
  {
    return lcfirst(str_replace(' ', '', ucwords(strtr($parameterName, '_', ' '))));
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
