<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer\MySqlRoutineWrapper;

use SetBased\DataLayer\MySqlRoutineWrapper;

/** @brief Class for generating a wrapper function around a stored procedure that ...
 */
class BulkInsert extends MySqlRoutineWrapper
{
  /** Name of the temporary table.
   */
  private $myTableName;

  /** Properties columns in the temporary table.
   */
  private $myColumns;

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
    $this->getTableProperties( $theRoutine );

    $n1 = count( $theRoutine['columns'] );
    $n2 = count( $this->myColumns );
    if ($n1!=$n2) set_assert_failed( "Number of fields %d and number of columns %d don't match.", $n1, $n2 );

    $routine_args = $this->getRoutineArgs( $theRoutine );
    $this->writeLine( 'self::Query(  \'CALL '.$theRoutine['routine_name'].'('.$routine_args.')\');' );

    $columns = '';
    $fields  = '';
    foreach ($theRoutine['columns'] as $i => $field)
    {
      if ($field!='_')
      {
        if ($columns) $columns .= ',';
        $columns .= '`'.$this->myColumns[$i]['field'].'`';

        if ($fields) $fields .= ',';
        $fields .= $this->writeEscapesValue( $this->myColumns[$i]['type'], '$row[\''.$field.'\']' );
      }
    }

    $this->writeLine( '$sql = "INSERT INTO `'.$this->myTableName.'`('.$columns.')";' );
    $this->writeLine( '$first = true;' );
    $this->writeLine( 'foreach( $theData as $row )' );
    $this->writeLine( '{' );

    $this->writeLine( '  if ($first) $sql .=\' values('.$fields.')\';' );
    $this->writeLine( '  else        $sql .=\',      ('.$fields.')\';' );

    $this->writeLine( '  $first = false;' );
    $this->writeLine( '}' );
    $this->writeLine( 'self::Query( $sql );' );
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
   * Get name and properties of the temporary table from database.
   *
   * @param array $theRoutine The metadata of the stored routine that creates a temporary table.
   */
  private function getTableProperties( $theRoutine )
  {
    $query = 'call '.$theRoutine['routine_name'].'()';

    \SET_DL::executeNone( $query );

    $query = 'select table_name from information_schema.TEMPORARY_TABLES';
    $rows  = \SET_DL::executeRows( $query );

    if (count( $rows )!=1) set_assert_failed( "Error can't find temporary table." );

    $this->myTableName = $rows['0']['table_name'];

    $query   = sprintf( "describe `%s`", $this->myTableName );
    $columns = \SET_DL::executeRows( $query );
    foreach ($columns as $key => $column)
    {
      preg_match( "(\w+)", $column['Type'], $type );
      $this->myColumns[$key]['type']  = $type['0'];
      $this->myColumns[$key]['field'] = $column['Field'];
    }

    $query = sprintf( "drop temporary table`%s`", $this->myTableName );
    \SET_DL::executeNone( $query );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Sets shielding special characters for the field @a $fieldName, depends on the type value @a $theValueType
   * in this field.
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
        $ret = '\'.self::QuoteNum('.$fieldName.').\'';
        break;

      case 'varbinary':
      case 'binary':

      case 'char':
      case 'varchar':
        $ret = '\'.self::QuoteString('.$fieldName.').\'';
        break;

      case 'time':
      case 'timestamp':

      case 'date':
      case 'datetime':
        $ret = '\'.self::QuoteString('.$fieldName.').\'';
        break;

      case 'enum':
      case 'set':
        $ret = '\'.self::QuoteString('.$fieldName.').\'';
        break;

      case 'bit':
        $ret = '\'.self::QuoteBit('.$fieldName.').\'';
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



