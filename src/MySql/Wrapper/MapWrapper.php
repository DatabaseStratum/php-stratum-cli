<?php

namespace SetBased\Stratum\MySql\Wrapper;

/**
 * Class for generating a wrapper method for a stored procedure that selects 0 or more rows with 2 columns. The rows are
 * returned as an array the first column are the keys and the second column are the values.
 */
class MapWrapper extends Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function getDocBlockReturnType()
  {
    return 'array';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeResultHandler()
  {
    $routine_args = $this->getRoutineArgs();

    $this->codeStore->append('$result = self::query(\'CALL '.$this->routine['routine_name'].'('.$routine_args.')\');');
    $this->codeStore->append('$ret = [];');
    $this->codeStore->append('while($row = $result->fetch_array(MYSQLI_NUM)) $ret[$row[0]] = $row[1];');
    $this->codeStore->append('$result->free();');
    $this->codeStore->append('if (self::$mysqli->more_results()) self::$mysqli->next_result();');
    $this->codeStore->append('');
    $this->codeStore->append('return $ret;');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeRoutineFunctionLobFetchData()
  {
    $this->codeStore->append('$result = $stmt->get_result();');
    $this->codeStore->append('$ret = [];');
    $this->codeStore->append('while($row = $result->fetch_array(MYSQLI_NUM)) $ret[$row[0]] = $row[1];');
    $this->codeStore->append('$result->free();');
    $this->codeStore->append('');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeRoutineFunctionLobReturnData()
  {
    $this->codeStore->append('return $ret;');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
