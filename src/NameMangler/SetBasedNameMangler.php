<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * Created by PhpStorm.
 * User: Dima
 * Date: 21.12.2015
 * Time: 11:31
 */

namespace SetBased\Stratum\NameMangler;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class SetBasedNameMangler
 *
 * @package SetBased\Stratum\NameMangler
 */
class SetBasedNameMangler implements NameMangler
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the name of the method in the wrapper class.
   *
   * @param $theRoutineName string The name of the stored routine.
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
   * Returns the parameter name in the wrapper method.
   *
   * @param $theRoutineParameterName
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
