<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * Created by PhpStorm.
 * User: Dima
 * Date: 21.12.2015
 * Time: 11:27
 */

namespace SetBased\Stratum\NameMangler;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Interface NameMangler
 *
 * @package SetBased\Stratum\NameMangler
 */
interface NameMangler
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the name of the method in the wrapper class.
   *
   * @param $theRoutineName string The name of the stored routine.
   *
   * @return string
   */
  static function getMethodName($theRoutineName);

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the parameter name in the wrapper method.
   *
   * @param $theRoutineParameterName
   *
   * @return string
   */
  static function getParameterName($theRoutineParameterName);

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
