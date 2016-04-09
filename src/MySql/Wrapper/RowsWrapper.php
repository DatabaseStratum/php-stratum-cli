<?php
//----------------------------------------------------------------------------------------------------------------------
/**
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
 * Class RowsMySqlWrapper
 *
 * @package SetBased\DataLayer\Generator\Wrapper
 */
class RowsWrapper extends Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @return string
   */
  protected function getDocBlockReturnType()
  {
    return 'array[]';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeResultHandler($theRoutine)
  {
    $routine_args = $this->getRoutineArgs($theRoutine);
    $this->writeLine('return self::executeRows(\'CALL '.$theRoutine['routine_name'].'('.$routine_args.')\');');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobFetchData($theRoutine)
  {
    $this->writeLine('$row = [];');
    $this->writeLine('self::bindAssoc($stmt, $row);');
    $this->writeLine();
    $this->writeLine('$tmp = [];');
    $this->writeLine('while (($b = $stmt->fetch()))');
    $this->writeLine('{');
    $this->writeLine('$new = [];');
    $this->writeLine('foreach($row as $key => $value)');
    $this->writeLine('{');
    $this->writeLine('$new[$key] = $value;');
    $this->writeLine('}');
    $this->writeLine(' $tmp[] = $new;');
    $this->writeLine('}');
    $this->writeLine();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobReturnData()
  {
    $this->writeLine('if ($b===false) self::mySqlError(\'mysqli_stmt::fetch\');');
    $this->writeLine();
    $this->writeLine('return $tmp;');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
