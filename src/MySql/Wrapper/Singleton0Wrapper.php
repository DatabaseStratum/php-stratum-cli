<?php
//----------------------------------------------------------------------------------------------------------------------
/*
 * PhpStratum
 *
 * @copyright 2005-2015 Paul Water / Set Based IT Consultancy (https://www.setbased.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link
 */
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql\Wrapper;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class Singleton0MySqlWrapper
 *
 * @package SetBased\DataLayer\Generator\Wrapper
 */
class Singleton0Wrapper extends Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function __construct($nameMangler, $lobAsString)
  {
    parent::__construct($nameMangler, $lobAsString);

    $this->exceptions[] = 'ResultException';
    $this->imports[]    = '\SetBased\Stratum\Exception\ResultException';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @return string
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
    $this->codeStore->append('return self::executeSingleton0(\'CALL '.$routine['routine_name'].'('.$routine_args.')\');');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobFetchData($routine)
  {
    $this->codeStore->append('$row = [];');
    $this->codeStore->append('self::bindAssoc($stmt, $row);');
    $this->codeStore->append();
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
    $this->codeStore->append();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobReturnData()
  {
    $this->codeStore->append('if ($b===false) self::mySqlError(\'mysqli_stmt::fetch\');');
    $this->codeStore->append('if (count($tmp)>1) throw new ResultException(\'0 or 1\', count($tmp), $query);');
    $this->codeStore->append();
    $this->codeStore->append('return ($tmp) ? $tmp[0][0] : null;');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
