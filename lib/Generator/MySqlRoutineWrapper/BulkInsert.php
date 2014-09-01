<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer\Generator\MySqlRoutineWrapper;

use SetBased\DataLayer\Generator\MySqlRoutineWrapper;

/**
 * Class BulkInsert
 *
 * @package SetBased\DataLayer\Generator\MySqlRoutineWrapper
 *
 * Class for generating a wrapper function around a stored procedure that ...
 */
class BulkInsert extends MySqlRoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates code for the arguments of the wrapper method for a stored routine.
   *
   * @param $theRoutine array The metadata of the stored routine.
   *
   * @return string
   */
  protected function getWrapperArgs( $theRoutine )
  {
    return '$theData';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates code for calling the stored routine in the wrapper method.
   *
   * @param $theRoutine array The metadata of the stored routine.
   */
  protected function writeResultHandler( $theRoutine )
  {
    // Validate count of columns and column types for equal.
    $n1 = count( $theRoutine['columns'] );
    $n2 = count( $theRoutine['column_types'] );
    if ($n1!=$n2) set_assert_failed( "Number of fields %d and number of columns %d don't match.", $n1, $n2 );

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
  protected function writeRoutineFunctionLobFetchData( $theRoutine )
  {
    // Nothing to do.
  }


  //--------------------------------------------------------------------------------------------------------------------
  protected function writeRoutineFunctionLobReturnData()
  {
    // Nothing to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets shielding special characters for the field @a $fieldName, depends on the type value @a $theValueType
   * in this field.
   *
   * @param string $theValueType
   * @param string $fieldName
   *
   * @return string
   */
  private function writeEscapesValue( $theValueType, $fieldName )
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
        $ret = '\'.self::quoteNum('.$fieldName.').\'';
        break;

      case 'varbinary':
      case 'binary':

      case 'char':
      case 'varchar':
        $ret = '\'.self::quoteString('.$fieldName.').\'';
        break;

      case 'time':
      case 'timestamp':

      case 'date':
      case 'datetime':
        $ret = '\'.self::quoteString('.$fieldName.').\'';
        break;

      case 'enum':
      case 'set':
        $ret = '\'.self::quoteString('.$fieldName.').\'';
        break;

      case 'bit':
        $ret = '\'.self::quoteBit('.$fieldName.').\'';
        break;

      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':

      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob':
        set_assert_failed( "LOBs are not possible in temporary tables" );
        break;

      default:
        set_assert_failed( "Unknown type '%s'.", $theValueType );
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
