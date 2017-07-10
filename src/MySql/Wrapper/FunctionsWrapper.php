<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql\Wrapper;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating a wrapper method for a stored function.
 */
class FunctionsWrapper extends Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function getDocBlockReturnType()
  {
    return 'string';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeResultHandler($routine)
  {
    $routine_args = $this->getRoutineArgs($routine);
    $this->codeStore->append('return self::executeSingleton0(\'SELECT '.$routine['routine_name'].'('.$routine_args.')\');');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobFetchData($routine)
  {
    $this->codeStore->append('$ret = self::$mysqli->affected_rows;');
    $this->codeStore->append('');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobReturnData()
  {
    $this->codeStore->append('return $ret;');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
