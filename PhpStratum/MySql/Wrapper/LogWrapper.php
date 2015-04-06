<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\PhpStratum\MySql\Wrapper;
  /**
   * phpStratum
   *
   * @copyright 2005-2015 Paul Water / Set Based IT Consultancy (https://www.setbased.nl)
   * @license   http://www.opensource.org/licenses/mit-license.php MIT
   * @link
   */
//----------------------------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class LogMySqlWrapper
 *
 * @package SetBased\DataLayer\Generator\MySqlWrapper
 */
class LogWrapper extends MySqlWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @return string
   */
  protected function getDocBlockReturnType()
  {
    return 'int';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeResultHandler( $theRoutine )
  {
    $routine_args = $this->getRoutineArgs( $theRoutine );
    $this->writeLine( 'return self::executeLog( \'CALL '.$theRoutine['routine_name'].'('.$routine_args.')\' );' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobFetchData( $theRoutine )
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
