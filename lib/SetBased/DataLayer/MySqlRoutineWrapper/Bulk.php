<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer\MySqlRoutineWrapper;

use SetBased\DataLayer\MySqlRoutineWrapper;

//----------------------------------------------------------------------------------------------------------------------
/** @brief Class for generating a wrapper function around a stored procedure with a large result set.
 */
class Bulk extends MySqlRoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  protected function writeResultHandler( $theRoutine )
  {
    $routine_args = $this->getRoutineArgs( $theRoutine );
    $this->writeLine( 'self::ExecuteBulk( $theBulkHandler, \'CALL '.$theRoutine['routine_name'].'('.$routine_args.')\');' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function writeRoutineFunctionLobFetchData( $theRoutine )
  {
    // Nothing to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function writeRoutineFunctionLobReturnData()
  {
    // Nothing to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
}

