<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer\Generator\MySqlRoutineWrapper;

use SetBased\DataLayer\Generator\MySqlRoutineWrapper;

//----------------------------------------------------------------------------------------------------------------------
/** @brief Class for generating a wrapper function around a stored procedure that selects 0 or more rows. The rows are
 * returned as nested arrays.
 */
class RowsWithKey extends MySqlRoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  protected function writeResultHandler( $theRoutine )
  {
    $routine_args = $this->getRoutineArgs( $theRoutine );

    $key = '';
    foreach ($theRoutine['columns'] as $column)
    {
      $key .= '[$row[\''.$column.'\']]';
    }

    $this->writeLine( '$result = self::Query( \'CALL '.$theRoutine['routine_name'].'('.$routine_args.')\');' );
    $this->writeLine( '$ret = array();' );
    $this->writeLine( 'while($row = $result->fetch_array( MYSQLI_ASSOC )) $ret'.$key.' = $row;' );
    $this->writeLine( '$result->close();' );
    $this->writeLine( 'self::$ourMySql->next_result();' );
    $this->writeLine( 'return  $ret;' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function writeRoutineFunctionLobFetchData( $theRoutine )
  {
    $key = '';
    foreach ($theRoutine['columns'] as $column)
    {
      $key .= '[$new[\''.$column.'\']]';
    }

    $this->writeLine( '$row = array();' );
    $this->writeLine( 'self::stmt_bind_assoc( $stmt, $row );' );
    $this->writeLine();
    $this->writeLine( '$ret = array();' );
    $this->writeLine( 'while (($b = $stmt->fetch()))' );
    $this->writeLine( '{' );
    $this->writeLine( '$new = array();' );
    $this->writeLine( 'foreach( $row as $key => $value )' );
    $this->writeLine( '{' );
    $this->writeLine( '$new[$key] = $value;' );
    $this->writeLine( '}' );
    $this->writeLine( '$ret'.$key.' = $new;' );
    $this->writeLine( '}' );
    $this->writeLine();
    $this->writeLine( '$b = $stmt->fetch();' );
    $this->writeLine();
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function writeRoutineFunctionLobReturnData()
  {
    $this->writeLine( 'if ($b===false) self::mysqlError( \'mysqli_stmt::fetch failed\' );' );
    $this->writeLine();
    $this->writeLine( 'return $ret;' );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

