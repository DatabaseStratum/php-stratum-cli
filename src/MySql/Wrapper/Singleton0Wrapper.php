<?php
declare(strict_types=1);

namespace SetBased\Stratum\MySql\Wrapper;

use SetBased\Stratum\MySql\Helper\DataTypeHelper;

/**
 * Class for generating a wrapper method for a stored procedure that selects 0 or 1 row having a single column only.
 */
class Singleton0Wrapper extends Wrapper
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
    $routine_args = $this->getRoutineArgs();

    if ($this->routine['return']=='bool')
    {
      $this->codeStore->append('return !empty(self::executeSingleton0(\'call '.$this->routine['routine_name'].'('.$routine_args.')\'));');
    }
    else
    {
      $this->codeStore->append('return self::executeSingleton0(\'call '.$this->routine['routine_name'].'('.$routine_args.')\');');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeRoutineFunctionLobFetchData(): void
  {
    $this->codeStore->append('$row = [];');
    $this->codeStore->append('self::bindAssoc($stmt, $row);');
    $this->codeStore->append('');
    $this->codeStore->append('$tmp = [];');
    $this->codeStore->append('while (($b = $stmt->fetch()))');
    $this->codeStore->append('{');
    $this->codeStore->append('$new = [];');
    $this->codeStore->append('foreach($row as $value)');
    $this->codeStore->append('{');
    $this->codeStore->append('$new[] = $value;');
    $this->codeStore->append('}');
    $this->codeStore->append('$tmp[] = $new;');
    $this->codeStore->append('}');
    $this->codeStore->append('');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeRoutineFunctionLobReturnData(): void
  {
    $this->imports[] = 'SetBased\Stratum\Exception\ResultException';

    $this->codeStore->append('if ($b===false) self::mySqlError(\'mysqli_stmt::fetch\');');
    $this->codeStore->append('if (count($tmp)>1) throw new ResultException(\'0 or 1\', count($tmp), $query);');
    $this->codeStore->append('');

    if ($this->routine['return']=='bool')
    {
      $this->codeStore->append('return !empty($tmp[0][0]);');
    }
    else
    {
      $this->codeStore->append('return $tmp[0][0] ?? null;');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
