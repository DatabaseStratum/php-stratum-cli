<?php

namespace SetBased\Stratum\NameMangler;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Interface for mangling routine names to method names and stored routine parameter names to parameters names in
 * the data layer.
 */
interface NameMangler
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the name of the wrapper method in the data layer for a stored routine.
   *
   * @param string $routineName The name of the stored routine.
   *
   * @return string
   */
  static function getMethodName($routineName);

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the parameter name in the wrapper method.
   *
   * @param string $parameterName The name of the parameter in the stored routine.
   *
   * @return string
   */
  static function getParameterName($parameterName);

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
