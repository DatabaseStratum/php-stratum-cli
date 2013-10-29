<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer\MySqlRoutineWrapper;
use       SetBased\DataLayer;

//----------------------------------------------------------------------------------------------------------------------
/** @brief Class for generating a wrapper function around a stored procedure that returns a scalar.
 */
class Singleton1 extends \SetBased\DataLayer\MySqlRoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for calling the stored routine in the wrapper method.
      @param $theRoutine       An array with the metadata of the stored routine.
      @param $theArgumentTypes An array with the arguments types of the stored routine.
   */
  protected function writeResultHandler( $theRoutine, $theArgumentTypes )
  {
    $routine_args = $this->getRoutineArgs( $theArgumentTypes );
    $this->writeLine( 'return self::ExecuteSingleton1( \'CALL '.$theRoutine['routine_name'].'('.$routine_args.')\');' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function writeRoutineFunctionLobFetchData( $theRoutine )
  {
    $this->writeLine( '$row = array();' );
    $this->writeLine( 'self::stmt_bind_assoc( $stmt, $row );' );
    $this->writeLine();
    $this->writeLine( '$tmp = array();' );
    $this->writeLine( 'while (($b = $stmt->fetch()))' );
    $this->writeLine( '{' );
    $this->writeLine( '$new = array();' );
    $this->writeLine( 'foreach( $row as $value )' );
    $this->writeLine( '{' );
    $this->writeLine( '$new[] = $value;' );
    $this->writeLine( '}' );
    $this->writeLine( '$tmp[] = $new;' );
    $this->writeLine( '}' );
    $this->writeLine();
    $this->writeLine( '$b = $stmt->fetch();' );
    $this->writeLine();
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function writeRoutineFunctionLobReturnData()
  {
    $this->writeLine( 'if ($b===false) self::ThrowSqlError( \'mysqli_stmt::fetch failed\' );' );
    $this->writeLine( 'if (sizeof($tmp)!=1) self::ThrowSqlError( \'The unexpected number of rows, expected 1 row.\' );' );
    $this->writeLine();
    $this->writeLine( 'return $tmp[0][0];' );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

