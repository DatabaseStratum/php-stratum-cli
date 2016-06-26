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
class RowsWithKeyWrapper extends Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @return string
   */
  protected function getDocBlockReturnType()
  {
    return '\array[]';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeResultHandler($routine)
  {
    $routine_args = $this->getRoutineArgs($routine);

    $key = '';
    foreach ($routine['columns'] as $column)
    {
      $key .= '[$row[\''.$column.'\']]';
    }

    $this->codeStore->append('$result = self::query(\'CALL '.$routine['routine_name'].'('.$routine_args.')\');');
    $this->codeStore->append('$ret = [];');
    $this->codeStore->append('while($row = $result->fetch_array(MYSQLI_ASSOC)) $ret'.$key.' = $row;');
    $this->codeStore->append('$result->free();');
    $this->codeStore->append('if (self::$mysqli->more_results()) self::$mysqli->next_result();');
    $this->codeStore->append('');
    $this->codeStore->append('return  $ret;');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobFetchData($routine)
  {
    $key = '';
    foreach ($routine['columns'] as $column)
    {
      $key .= '[$new[\''.$column.'\']]';
    }

    $this->codeStore->append('$row = [];');
    $this->codeStore->append('self::bindAssoc($stmt, $row);');
    $this->codeStore->append('');
    $this->codeStore->append('$ret = [];');
    $this->codeStore->append('while (($b = $stmt->fetch()))');
    $this->codeStore->append('{');
    $this->codeStore->append('$new = [];');
    $this->codeStore->append('foreach($row as $key => $value)');
    $this->codeStore->append('{');
    $this->codeStore->append('$new[$key] = $value;');
    $this->codeStore->append('}');
    $this->codeStore->append('$ret'.$key.' = $new;');
    $this->codeStore->append('}');
    $this->codeStore->append('');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobReturnData()
  {
    $this->codeStore->append('if ($b===false) self::mySqlError(\'mysqli_stmt::fetch\');');
    $this->codeStore->append('');
    $this->codeStore->append('return $ret;');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
