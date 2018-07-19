<?php
declare(strict_types=1);

namespace SetBased\Stratum\MySql\Wrapper;

use SetBased\Stratum\MySql\Helper\DataTypeHelper;

/**
 * Class for generating a wrapper method for a stored function.
 */
class FunctionWrapper extends Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function getDocBlockReturnType(): string
  {
    return $this->routine['return'];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function getReturnTypeDeclaration(): string
  {
    $type = DataTypeHelper::phpTypeHintingToPhpTypeDeclaration($this->getDocBlockReturnType());

    if ($type==='') return '';

    return ': '.$type;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeResultHandler(): void
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
  protected function writeRoutineFunctionLobFetchData(): void
  {
    $this->codeStore->append('$ret = self::$mysqli->affected_rows;');
    $this->codeStore->append('');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeRoutineFunctionLobReturnData(): void
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
