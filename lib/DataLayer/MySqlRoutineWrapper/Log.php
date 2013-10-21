<?php
//----------------------------------------------------------------------------------------------------------------------
namespace DataLayer\MySqlRoutineWrapper;
use       DataLayer;

//----------------------------------------------------------------------------------------------------------------------
/** @brief Class for generating a wrapper function around a stored procedure that returns nothing but writes the rows of
 *  the results sets to the standard out.
 */
class Log extends \DataLayer\MySqlRoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for calling the stored routine in the wrapper method.
     @param $theRoutine       An array with the metadata of the stored routine.
     @param $theArgumentTypes An array with the arguments types of the stored routine.
   */
  protected function writeResultHandler( $theRoutine, $theArgumentTypes )
  {
    $routine_args = $this->getRoutineArgs( $theArgumentTypes );
    $this->writeLine( 'self::ExecuteEcho( \'CALL '.$theRoutine['routine_name'].'('.$routine_args.')\' );' );
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

