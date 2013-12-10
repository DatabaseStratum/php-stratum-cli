<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer\Generator\MySqlRoutineWrapper;

use SetBased\DataLayer\Generator\MySqlRoutineWrapper;

//----------------------------------------------------------------------------------------------------------------------
/** @brief Class for generating a wrapper function around a stored procedure that selects 0 or 1 row.
 */
class Row0 extends MySqlRoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  protected function writeResultHandler( $theRoutine )
  {
    $routine_args = $this->getRoutineArgs( $theRoutine );
    $this->writeLine( 'return self::executeRow0( \'CALL '.$theRoutine['routine_name'].'('.$routine_args.')\');' );
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
    $this->writeLine( 'foreach( $row as $key => $value )' );
    $this->writeLine( '{' );
    $this->writeLine( '$new[$key] = $value;' );
    $this->writeLine( '}' );
    $this->writeLine( '$tmp[] = $new;' );
    $this->writeLine( '}' );
    $this->writeLine();
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function writeRoutineFunctionLobReturnData()
  {
    $this->writeLine( 'if ($b===false) self::mysqlError( \'mysqli_stmt::fetch failed\' );' );
    $this->writeLine( 'if (sizeof($tmp)>1) self::mysqlError( \'The unexpected number of rows, expected 0 or 1 rows.\' );' );
    $this->writeLine();
    $this->writeLine( 'return ($tmp) ? $tmp[0] : null;' );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

