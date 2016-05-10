<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\NameMangler;

//----------------------------------------------------------------------------------------------------------------------
/**
 * A name mangler for stored routines that confirm to Set Based's coding standards.
 */
class SetBasedNameMangler implements NameMangler
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the routine name after the first underscore in camelCase. I.e. abc_foo_bar => fooBar.
   *
   * @param string $routineName The name of the stored routine.
   *
   * @return string
   */
  public static function getMethodName($routineName)
  {
    return lcfirst(preg_replace_callback('/(_)([a-z0-9])/',
      function ($matches)
      {
        return strtoupper($matches[2]);
      },
                                         stristr($routineName, '_')));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the parameter name into camelCase.
   *
   * @param string $routineParameterName The name of the parameter in the stored routine.
   *
   * @return string
   */
  public static function getParameterName($routineParameterName)
  {
    return lcfirst(str_replace(' ', '', ucwords(strtr($routineParameterName, '_', ' '))));
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
