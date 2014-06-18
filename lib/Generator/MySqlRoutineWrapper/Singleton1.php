<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer\Generator\MySqlRoutineWrapper;

use SetBased\DataLayer\Generator\MySqlRoutineWrapper;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class Singleton1
 *
 * @package SetBased\DataLayer\Generator\MySqlRoutineWrapper
 *
 * Class for generating a wrapper function around a stored procedure selects 1 and only 1 row with only one
 * column.
 */
class Singleton1 extends MySqlRoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  protected function writeResultHandler( $theRoutine )
  {
    $routine_args = $this->getRoutineArgs( $theRoutine );
    $this->writeLine( 'return self::executeSingleton1( \'CALL '.$theRoutine['routine_name'].'('.$routine_args.')\');' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function writeRoutineFunctionLobFetchData( $theRoutine )
  {
    $this->writeLine( '$row = array();' );
    $this->writeLine( 'self::bindAssoc( $stmt, $row );' );
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
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function writeRoutineFunctionLobReturnData()
  {
    $this->writeLine( 'if ($b===false) self::sqlError( \'mysqli_stmt::fetch\' );' );
    $this->writeLine( 'if (sizeof($tmp)!=1) self::assertFailed( \'Expected 1 row found %d rows.\', sizeof($tmp) );' );
    $this->writeLine();
    $this->writeLine( 'return $tmp[0][0];' );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

