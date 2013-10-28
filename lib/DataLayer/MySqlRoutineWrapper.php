<?php
//----------------------------------------------------------------------------------------------------------------------
namespace DataLayer;
use DataLayer\MySqlRoutineWrapper;

//----------------------------------------------------------------------------------------------------------------------
/** @brief abstract supper class for generation stored routine wrapper methods based on the type of the stored routine.
 */
abstract class MySqlRoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /** The constant contain width page (in chars).
   */
  const C_PAGE_WIDTH = 120;

  /** The current level of indentation in the generated code.
   */
  private $myIndentLevel = 1;

  /** Buffer for generated code.
   */
  private $myCode = '';

  //--------------------------------------------------------------------------------------------------------------------
  /** Appends @a $theString to @c $myCode.
   */
  protected function write( $theString )
  {
    $this->myCode .= $theString;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Appends @a $theString and a LF to @c $myCode.
      - @a $theString must not contain a LF.
      - Indent level is increased or decreased as @a $theString equals to '{' or '}'.
   */
  protected function writeLine( $theString=false )
  {
    if ($theString)
    {
      if (trim($theString)=='}') $this->myIndentLevel--;
      for( $i=0; $i<2*$this->myIndentLevel; $i++ )
      {
        $this->write( ' ' );
      }
      $this->myCode .= $theString;
      $this->myCode .= "\n";
      if (trim($theString)=='{') $this->myIndentLevel++;
    }
    else
    {
      $this->myCode .= "\n";
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Appends a comment line to @c $myCode.
   */
  protected function writeSeparator()
  {
    for( $i=0; $i<2*$this->myIndentLevel; $i++ )
    {
      $this->write( ' ' );
    }

    $this->write( '//' );

    for( $i=0; $i<(self::C_PAGE_WIDTH-2*$this->myIndentLevel-2-1); $i++ )
    {
      $this->write( '-' );
    }
    $this->writeLine();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Returns @a $theSqlFunctionName after the first underscore in camel case.
      E.g. set_foo_foo => FooFoo.
   */
  private function getWrapperRoutineName( $theSqlFunctionName )
  {
    $name = preg_replace( '/(_)([a-z])/e', "strtoupper('\\2')", stristr( $theSqlFunctionName, '_' ) );

    return $name;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for escaping the arguments of a stored routine.
      @param $theArgsTypes An array with the arguments of a strored routine.
   */
  private function writeEscapedArgs( $theArgType, $numberArgType )
  {
    switch ($theArgType)
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
      $ret = '\'.self::QuoteNum($theArg'.$numberArgType.').\'';
      break;

    case 'varbinary':
    case 'binary':

    case 'char':
    case 'varchar':
      $ret = '\'.self::QuoteString($theArg'.$numberArgType.').\'';
      break;

    case 'time':
    case 'timestamp':

    case 'date':
    case 'datetime':
      $ret = '\'.self::QuoteString($theArg'.$numberArgType.').\'';
      break;

    case 'enum':
    case 'set':
      $ret = '\'.self::QuoteString($theArg'.$numberArgType.').\'';
      break;

    case 'bit':
      $ret = '\'.self::QuoteBit($theArg'.$numberArgType.').\'';
      break;

    case 'tinytext':
    case 'text':
    case 'mediumtext':
    case 'longtext':

    case 'tinyblob':
    case 'blob':
    case 'mediumblob':
    case 'longblob':
      $ret = '?';
      break;

    default:
      set_assert_failed( "Unknown arg type '%s'.", $theArgType );
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for the arguments of the wrapper method for @a $theRoutine.
      @param $theRoutine An array with the argument types of the stored routine.
   */
  protected function getWrapperArgs( $theRoutine )
  {
    if ($theRoutine['argument_types']) $argument_types = explode( ',', $theRoutine['argument_types'] );
    else                               $argument_types = array();

    if ($theRoutine['type']=='bulk') $ret = '$theBulkHandler';
    else                             $ret = '';

    foreach( $argument_types as $i => $arg_type )
    {
      if ($ret) $ret .= ',';
      switch ($arg_type)
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
        $ret .= '$theArg'.$i;
        break;

      case 'varbinary':
      case 'binary':

      case 'char':
      case 'varchar':
        $ret .= '$theArg'.$i;
        break;

      case 'time':
      case 'timestamp':

      case 'date':
      case 'datetime':
        $ret .= '$theArg'.$i;
        break;

      case 'enum':
      case 'bit':
      case 'set':
        $ret .= '$theArg'.$i;
        break;

      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':
        $ret .= '$theArg'.$i;
        break;

      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob':
        $ret .= '$theArg'.$i;
        break;

      default:
        set_assert_failed( "Unknown argument type '%s'.", $arg_type );
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for the arguments for calling the stored routine in a wrapper method.
      @param $theArgsTypes array with the argument type of a Stored Routine.
   */
  protected function getRoutineArgs( $theArgsTypes )
  {
    $ret = '';
    foreach( $theArgsTypes as $i => $arg_type )
    {
      if ($ret) $ret .= ',';
      $ret .= $this->writeEscapedArgs( $arg_type, $i );
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Returns the type of the corresponding bind variable. @sa http://php.net/manual/en/mysqli-stmt.bind-param.php
      @param $theType The argument type of on argument of a stored routine.
   */
  protected function getBindVariableType( $theType )
  {
    $ret = '';
    switch ($theType)
    {
    case 'tinyint':
    case 'smallint':
    case 'mediumint':
    case 'int':
    case 'bigint':
    case 'year':
      $ret = 'i';
      break;

    case 'decimal':
    case 'float':
    case 'double':
      $ret = 'd';
      break;


    case 'time':
    case 'timestamp':
    case 'binary':
    case 'enum':
    case 'bit':
    case 'set':
    case 'char':
    case 'varchar':
    case 'date':
    case 'datetime':
    case 'varbinary':
      $ret = 's';
      break;

    case 'tinytext':
    case 'text':
    case 'mediumtext':
    case 'longtext':
    case 'tinyblob':
    case 'blob':
    case 'mediumblob':
    case 'longblob':
      $ret .= 'b';
      break;

    default:
      set_assert_failed( "Unknown argument type '%s'.", $theType );
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Genereert een volledige wrapper methode voor een Stored Routine.
      @param $theRoutine Een rij uit tabel DEV_ROUTINE.
   */
  public function writeRoutineFunctionWithoutLob( $theRoutine )
  {
    if ($theRoutine['argument_types']) $argument_types = explode( ',', $theRoutine['argument_types'] );
    else                               $argument_types = array();

    $wrapper_function_name = $this->getWrapperRoutineName( $theRoutine['routine_name'] );
    $wrapper_args          = $this->getWrapperArgs( $theRoutine );

    $this->writeSeparator();
    $this->writeLine( '/** @sa Stored Routine '.$theRoutine['routine_name'].'.' );
    $this->writeLine( ' */' );
    $this->writeLine( 'static function '.$wrapper_function_name.'('.$wrapper_args.')' );
    $this->writeLine( '{' );

    $this->writeResultHandler( $theRoutine, $argument_types );
    $this->writeLine( '}' );
    $this->writeLine();

    return $this->myCode;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Generates a complete wrapper method.
      @param $theRoutine Metadata of the stored routine.
   */
  public function writeRoutineFunctionWithLob( $theRoutine )
  {
    if ($theRoutine['argument_types']) $argument_types = explode( ',', $theRoutine['argument_types'] );
    else                               $argument_types = array();

    $wrapper_function_name = $this->getWrapperRoutineName( $theRoutine['routine_name'] );
    $wrapper_args          = $this->getWrapperArgs( $theRoutine );
    $routine_args          = $this->getRoutineArgs( $argument_types );

    $types = '';
    $nulls = '';
    foreach( $argument_types as $theType )
    {
      $type = $this->getBindVariableType( $theType );
      if ($type=='b')
      {
        $types .= 'b';
        if ($nulls) $nulls .= ',';
        $nulls .= '$null';
      }
    }

    $this->writeSeparator();
    $this->writeLine( '/** @sa Stored Routine '.$theRoutine['routine_name'].'.' );
    $this->writeLine( ' */' );
    $this->writeLine( 'static function '.$wrapper_function_name.'('.$wrapper_args.')' );
    $this->writeLine( '{' );
    $this->writeLine( '$query = \'CALL '.$theRoutine['routine_name'].'('.$routine_args.')\';' );
    $this->writeLine( '$stmt  = self::$ourMySql->prepare( $query );' );
    $this->writeLine( 'if (!$stmt) self::ThrowSqlError( \'prepare failed\' );' );
    $this->writeLine();
    $this->writeLine( '$null = null;' );
    $this->writeLine( '$b = $stmt->bind_param( \''.$types.'\', '.$nulls.' );' );
    $this->writeLine( 'if (!$b) self::ThrowSqlError( \'bind_param failed\' );' );
    $this->writeLine();

    $blob_argument_index = 0;
    foreach( $argument_types as $i => $argument )
    {
      if ($this->getBindVariableType( $argument )=='b')
      {
        $this->writeLine( '$n = strlen( $theArg'.$i.' );' );
        $this->writeLine( '$p = 0;' );
        $this->writeLine( 'while ($p<$n)' );
        $this->writeLine( '{' );
        $this->writeLine( '$b = $stmt->send_long_data( '.$blob_argument_index.', substr( $theArg'.$i.', $p, self::$ourChunckSize ) );' );
        $this->writeLine( 'if (!$b) self::ThrowSqlError( \'send_long_data failed\' );' );
        $this->writeLine( '$p += self::$ourChunckSize;' );
        $this->writeLine( '}' );
        $this->writeLine();

        $blob_argument_index++;
      }
    }

    $this->writeLine( '$b = $stmt->execute();' );
    $this->writeLine( 'if (!$b) self::ThrowSqlError( \'execute failed\' );' );
    $this->writeLine();
    $this->writeRoutineFunctionLobFetchData( $theRoutine );
    $this->writeLine( '$stmt->close();' );
    $this->writeLine( 'self::$ourMySql->next_result();' );
    $this->writeLine();
    $this->writeRoutineFunctionLobReturnData();
    $this->writeLine( '}' );
    $this->writeLine();

    return $this->myCode;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Returns @c true if one of the arguments is a BLOB or CLOB.
      @param $theArgumentsType An aaray with the argument types.
   */
  public function isBlobArgument( $theArgumentsType )
  {
    $has_blob = false;
    foreach( $theArgumentsType as $argument_type )
    {
      switch ($argument_type)
      {
      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':

      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob':

        $has_blob = true;
        break;
      }
    }

    return $has_blob;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Generates a complete wrapper method.
      @param $theRoutine Metadata of the stored routine.
   */
  public function writeRoutineFunction( $theRoutine )
  {
    $theArgumentsType = explode( ',', $theRoutine['argument_types'] );

    $has_blob = $this->isBlobArgument( $theArgumentsType );

    if ($has_blob)
    {
      return $this->writeRoutineFunctionWithLob( $theRoutine );
    }
    else
    {
      return $this->writeRoutineFunctionWithoutLob( $theRoutine );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for calling the stored routine in the wrapper method.
      @param $theRoutine       An array with the metadata of the stored routine.
      @param $theArgumentTypes An array with the arguments types of the stored routine.
   */
  abstract protected function writeResultHandler( $theRoutine, $theArgumentTypes );

  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for fetching data of a stored routine with one or more LOB arguments.
      @todo Make abstract and implement for all child classes
   */
  abstract protected function writeRoutineFunctionLobFetchData( $theRoutine );

  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for returing the data returend by a stored routine with one or more LOB arguments.
      @todo Make abstract and implement for all child classes
   */
  abstract protected function writeRoutineFunctionLobReturnData();

  //--------------------------------------------------------------------------------------------------------------------
  /** A factory for creating the appropiate object for generating code for the stored routine @a $theRoutine.
   */
  static public function createRoutineWrapper( $theRoutine )
  {
    switch ($theRoutine['type'])
    {
    case 'bulk':
      $class = 'Bulk';
      break;

    case 'bulk_insert':
      $class = 'BulkInsert';
      break;

    case 'log':
      $class = 'Log';
      break;

    case 'none':
      $class = 'None';
      break;

    case 'row0':
      $class = 'Row0';
      break;

    case 'row1':
      $class = 'Row1';
      break;

    case 'rows':
      $class = 'Rows';
      break;

    case 'rows_with_key':
      $class = 'RowsWithKey';
      break;

    case 'rows_with_index':
      $class = 'RowsWithIndex';
      break;

    case 'singleton0':
      $class = 'Singleton0';
      break;

    case 'singleton1':
      $class = 'Singleton1';
      break;

    default:
      set_assert_failed( "Unknown routine type '%s'.", $theRoutine['type'] );
    }

    $class = 'DataLayer\\MySqlRoutineWrapper\\'.$class;
    $wrapper = new $class();

    return $wrapper;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

