<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * phpStratum
 *
 * @copyright 2005-2015 Paul Water / Set Based IT Consultancy (https://www.setbased.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link
 */
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql\Wrapper;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class Row1MySqlWrapper
 *
 * @package SetBased\DataLayer\Generator\Wrapper
 */
class Row1Wrapper extends Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function getDocBlockExceptions()
  {
    $exceptions   = parent::getDocBlockExceptions();
    $exceptions[] = 'RowCountException';

    return $exceptions;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @return string
   */
  protected function getDocBlockReturnType()
  {
    return 'array';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeResultHandler( $theRoutine )
  {
    $routine_args = $this->getRoutineArgs( $theRoutine );
    $this->writeLine( 'return self::executeRow1( \'CALL '.$theRoutine['routine_name'].'('.$routine_args.')\');' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobFetchData( $theRoutine )
  {
    $this->writeLine( '$row = array();' );
    $this->writeLine( 'self::bindAssoc( $stmt, $row );' );
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
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobReturnData()
  {
    $this->writeLine( 'if ($b===false) self::sqlError( \'mysqli_stmt::fetch\' );' );
    $this->writeLine( 'if (count($tmp)!=1) throw new RowCountException( \'1\', count($tmp), $query );' );
    $this->writeLine();
    $this->writeLine( 'return $row;' );

    // For this wrapper method the following fully qualified name must be imported.
    $this->myImports[] = '\SetBased\Stratum\Exception\RowCountException';
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
