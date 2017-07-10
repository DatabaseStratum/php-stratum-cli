<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql\Wrapper;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating a wrapper method for a stored procedure of which the selected rows must be echoed in a table
 * layout.
 */
class TableWrapper extends Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function getDocBlockReturnType()
  {
    return 'int';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeResultHandler($routine)
  {
    $routine_args = $this->getRoutineArgs($routine);
    $this->codeStore->append('return self::executeTable(\'CALL '.$routine['routine_name'].'('.$routine_args.')\');');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobFetchData($routine)
  {
    // Nothing to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobReturnData()
  {
    // Nothing to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
