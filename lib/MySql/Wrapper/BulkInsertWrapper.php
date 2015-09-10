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

use SetBased\Affirm\Affirm;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating a wrapper method for a stored procedure that prepares a table to be used with a bulk SQL
 * statement.
 *
 * @package SetBased\DataLayer\Generator\Wrapper
 */
class BulkInsertWrapper extends Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @return string
   */
  protected function getDocBlockReturnType()
  {
    return '';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function getWrapperArgs( $theRoutine )
  {
    return '$theData';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeResultHandler( $theRoutine )
  {
    // Validate number of column names and number of column types are equal.
    $n1 = count( $theRoutine['columns'] );
    $n2 = count( $theRoutine['column_types'] );
    if ($n1!=$n2) Affirm::assertFailed( "Number of fields %d and number of columns %d don't match.", $n1, $n2 );

    $routine_args = $this->getRoutineArgs( $theRoutine );
    $this->writeLine( 'self::query( \'CALL '.$theRoutine['routine_name'].'('.$routine_args.')\');' );

    $columns = '';
    $fields  = '';
    foreach ($theRoutine['columns'] as $i => $field)
    {
      if ($field!='_')
      {
        if ($columns) $columns .= ',';
        $columns .= '`'.$theRoutine['fields'][$i].'`';

        if ($fields) $fields .= ',';
        $fields .= $this->writeEscapesValue( $theRoutine['column_types'][$i], '$row[\''.$field.'\']' );
      }
    }

    $this->writeLine( 'if (is_array($theData) &&!empty($theData))' );
    $this->writeLine( '{' );
    $this->writeLine( '$sql = "INSERT INTO `'.$theRoutine['table_name'].'`('.$columns.')";' );
    $this->writeLine( '$first = true;' );
    $this->writeLine( 'foreach( $theData as $row )' );
    $this->writeLine( '{' );

    $this->writeLine( 'if ($first) $sql .=\' values('.$fields.')\';' );
    $this->writeLine( 'else        $sql .=\',      ('.$fields.')\';' );

    $this->writeLine( '$first = false;' );
    $this->writeLine( '}' );
    $this->writeLine( 'self::query( $sql );' );
    $this->writeLine( '}' );
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
  /**
   * Generates code for escaping data.
   *
   * @param string $theValueType       The column type.
   * @param string $theFieldExpression The expression of the field in the PHP array, e.g. $row['first_name'].
   *
   * @return string The generated PHP code.
   */
  private function writeEscapesValue( $theValueType, $theFieldExpression )
  {
    $ret = '';
    switch ($theValueType)
    {
      case 'tinyint':
      case 'smallint':
      case 'mediumint':
      case 'int':
      case 'bigint':

      case 'year':

      case 'decimal':
      case 'float':
      case 'double':
        $ret = '\'.self::quoteNum('.$theFieldExpression.').\'';
        break;

      case 'varbinary':
      case 'binary':

      case 'char':
      case 'varchar':
        $ret = '\'.self::quoteString('.$theFieldExpression.').\'';
        break;

      case 'time':
      case 'timestamp':

      case 'date':
      case 'datetime':
        $ret = '\'.self::quoteString('.$theFieldExpression.').\'';
        break;

      case 'enum':
      case 'set':
        $ret = '\'.self::quoteString('.$theFieldExpression.').\'';
        break;

      case 'bit':
        $ret = '\'.self::quoteBit('.$theFieldExpression.').\'';
        break;

      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':

      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob':
        Affirm::assertFailed( "LOBs are not possible in temporary tables" );
        break;

      default:
        Affirm::assertFailed( "Unknown type '%s'.", $theValueType );
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
