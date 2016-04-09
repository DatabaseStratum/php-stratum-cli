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
  public function __construct($theLobAsStringFlag)
  {
    parent::__construct($theLobAsStringFlag);

    $this->myExceptions[] = 'ResultException';
    $this->myImports[]    = '\SetBased\Stratum\Exception\ResultException';
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
  protected function writeResultHandler($theRoutine)
  {
    $routine_args = $this->getRoutineArgs($theRoutine);
    $this->writeLine('return self::executeSingleton0(\'CALL '.$theRoutine['routine_name'].'('.$routine_args.')\');');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobFetchData($theRoutine)
  {
    $this->writeLine('$row = array();');
    $this->writeLine('self::bindAssoc($stmt, $row);');
    $this->writeLine();
    $this->writeLine('$tmp = array();');
    $this->writeLine('while (($b = $stmt->fetch()))');
    $this->writeLine('{');
    $this->writeLine('$new = array();');
    $this->writeLine('foreach($row as $value)');
    $this->writeLine('{');
    $this->writeLine('$new[] = $value;');
    $this->writeLine('}');
    $this->writeLine('$tmp[] = $new;');
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
    $this->writeLine('if (count($tmp)>1) throw new ResultException(\'0 or 1\', count($tmp), $query);');
    $this->writeLine();
    $this->writeLine('return ($tmp) ? $tmp[0][0] : null;');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
