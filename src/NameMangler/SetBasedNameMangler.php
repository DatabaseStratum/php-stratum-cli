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
   * @param string $theRoutineName The name of the stored routine.
   *
   * @return string
   */
  static function getMethodName($theRoutineName)
  {
    return lcfirst(preg_replace_callback('/(_)([a-z])/',
      function ($matches)
      {
        return strtoupper($matches[2]);
      },
                                         stristr($theRoutineName, '_')));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the parameter name unchanged.
   *
   * @param string $theRoutineParameterName The name of the parameter in the stored routine.
   *
   * @return string
   */
  static function getParameterName($theRoutineParameterName)
  {
    return $theRoutineParameterName;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
