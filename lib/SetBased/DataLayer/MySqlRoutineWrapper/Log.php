<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer\MySqlRoutineWrapper;
use       SetBased\DataLayer;

//----------------------------------------------------------------------------------------------------------------------
/** @brief Class for generating a wrapper function around a stored procedure that logs the result sets of the wrapped
           stored procedure.
 */
class Log extends \SetBased\DataLayer\MySqlRoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for calling the stored procedure in the wrapper method.
      @param $theRoutine       An array with the metadata about the stored routine.
      @param $theArgumentTypes An array with the arguments types of the stored routine.
   */
  protected function writeResultHandler( $theRoutine, $theArgumentTypes )
  {
    $routine_args = $this->getRoutineArgs( $theArgumentTypes );
    $this->writeLine( 'self::ExecuteLog( \'CALL '.$theRoutine['routine_name'].'('.$routine_args.')\' );' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function writeRoutineFunctionLobFetchData( $theRoutine )
  {
    // Nothing todo.
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function writeRoutineFunctionLobReturnData()
  {
    // Nothing todo.
  }

  //--------------------------------------------------------------------------------------------------------------------
}

