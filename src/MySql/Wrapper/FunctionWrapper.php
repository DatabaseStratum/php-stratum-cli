<?php

namespace SetBased\Stratum\MySql\Wrapper;

/**
 * Class for generating a wrapper method for a stored function.
 */
class FunctionWrapper extends Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function getDocBlockReturnType()
  {
    return $this->routine['return'];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeResultHandler()
  {
    if ($this->routine['return']=='bool')
    {
      $this->codeStore->append(sprintf("return !empty(self::executeSingleton0('select %s(%s)'));",
                                       $this->routine['routine_name'],
                                       $this->getRoutineArgs()));
    }
    else
    {
      $this->codeStore->append(sprintf("return self::executeSingleton0('select %s(%s)');",
                                       $this->routine['routine_name'],
                                       $this->getRoutineArgs()));
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeRoutineFunctionLobFetchData()
  {
    $this->codeStore->append('$ret = self::$mysqli->affected_rows;');
    $this->codeStore->append('');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeRoutineFunctionLobReturnData()
  {
    if ($this->routine['return']=='bool')
    {
      $this->codeStore->append('return !empty($ret);');
    }
    else
    {
      $this->codeStore->append('return $ret;');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
