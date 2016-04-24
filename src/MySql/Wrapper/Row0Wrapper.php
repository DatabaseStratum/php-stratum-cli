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
 * Class for generating a wrapper method for a stored procedure that selects 0 or 1 row.
 */
class Row0Wrapper extends Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function __construct($lobAsString)
  {
    parent::__construct($lobAsString);

    $this->exceptions[] = 'ResultException';
    $this->imports[]    = '\SetBased\Stratum\Exception\ResultException';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @return string
   */
  protected function getDocBlockReturnType()
  {
    return 'array';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeResultHandler($routine)
  {
    $routine_args = $this->getRoutineArgs($routine);
    $this->writeLine('return self::executeRow0(\'CALL '.$routine['routine_name'].'('.$routine_args.')\');');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobFetchData($routine)
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
    $this->writeLine('return ($tmp) ? $tmp[0] : null;');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
