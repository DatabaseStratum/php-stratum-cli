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
    return 'array[]';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeResultHandler($theRoutine)
  {
    $routine_args = $this->getRoutineArgs($theRoutine);

    $key = '';
    foreach ($theRoutine['columns'] as $column)
    {
      $key .= '[$row[\''.$column.'\']]';
    }

    $this->writeLine('$result = self::query( \'CALL '.$theRoutine['routine_name'].'('.$routine_args.')\');');
    $this->writeLine('$ret = array();');
    $this->writeLine('while($row = $result->fetch_array( MYSQLI_ASSOC )) $ret'.$key.' = $row;');
    $this->writeLine('$result->free();');
    $this->writeLine('if(self::$ourMySql->more_results()) self::$ourMySql->next_result();');
    $this->writeLine();
    $this->writeLine('return  $ret;');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobFetchData($theRoutine)
  {
    $key = '';
    foreach ($theRoutine['columns'] as $column)
    {
      $key .= '[$new[\''.$column.'\']]';
    }

    $this->writeLine('$row = array();');
    $this->writeLine('self::bindAssoc( $stmt, $row );');
    $this->writeLine();
    $this->writeLine('$ret = array();');
    $this->writeLine('while (($b = $stmt->fetch()))');
    $this->writeLine('{');
    $this->writeLine('$new = array();');
    $this->writeLine('foreach( $row as $key => $value )');
    $this->writeLine('{');
    $this->writeLine('$new[$key] = $value;');
    $this->writeLine('}');
    $this->writeLine('$ret'.$key.' = $new;');
    $this->writeLine('}');
    $this->writeLine();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobReturnData()
  {
    $this->writeLine('if ($b===false) self::mySqlError( \'mysqli_stmt::fetch\' );');
    $this->writeLine();
    $this->writeLine('return $ret;');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
