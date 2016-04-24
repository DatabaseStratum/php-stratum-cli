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
 * Class for generating a wrapper method for a stored procedure that selects 0 or more rows. The rows are
 *
 * @package SetBased\DataLayer\Generator\Wrapper
 *          returned as nested arrays.
 */
class RowsWithIndexWrapper extends Wrapper
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
  protected function writeResultHandler($routine)
  {
    $routine_args = $this->getRoutineArgs($routine);

    $index = '';
    foreach ($routine['columns'] as $column)
    {
      $index .= '[$row[\''.$column.'\']]';
    }

    $this->writeLine('$result = self::query(\'CALL '.$routine['routine_name'].'('.$routine_args.')\');');
    $this->writeLine('$ret = [];');
    $this->writeLine('while($row = $result->fetch_array(MYSQLI_ASSOC)) $ret'.$index.'[] = $row;');
    $this->writeLine('$result->free();');
    $this->writeLine('if (self::$mysqli->more_results()) self::$mysqli->next_result();');
    $this->writeLine();
    $this->writeLine('return $ret;');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobFetchData($routine)
  {
    $index = '';
    foreach ($routine['columns'] as $column)
    {
      $index .= '[$new[\''.$column.'\']]';
    }

    $this->writeLine('$row = [];');
    $this->writeLine('self::bindAssoc($stmt, $row);');
    $this->writeLine();
    $this->writeLine('$ret = [];');
    $this->writeLine('while (($b = $stmt->fetch()))');
    $this->writeLine('{');
    $this->writeLine('$new = [];');
    $this->writeLine('foreach($row as $key => $value)');
    $this->writeLine('{');
    $this->writeLine('$new[$key] = $value;');
    $this->writeLine('}');
    $this->writeLine('$ret'.$index.'[] = $new;');
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
    $this->writeLine('return $ret;');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
