<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\DataLayer;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class MySqlRoutineWrapper abstract supper class for generation stored routine wrapper methods based on the type of
 * the stored routine.
 * @package SetBased\DataLayer
 */
/**
 * Class MySqlRoutineWrapper
 * @package SetBased\DataLayer
 */
abstract class MySqlRoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The maximum width of the generated code (in chars).
   */
  const C_PAGE_WIDTH = 120;

  /**
   * @var int The current level of indentation in the generated code.
   */
  private $myIndentLevel = 1;

   /**
   * @var string Buffer for generated code.
   */
  private $myCode = '';

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * A factory for creating the appropriate object for generating a wrapper method for a stored routine.
   *
   * @param array $theRoutine The metadata of the stored routine.
   *
   * @return MySqlRoutineWrapper
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

      case 'function':
        $class = 'Functions';
        break;

      case 'hidden':
        $class = 'Hidden';
        break;

      default:
        $class = ''; // Prevent warnings (possible $class not defined) from IDE.
        set_assert_failed( "Unknown routine type '%s'.", $theRoutine['type']."\n" );
    }

    $class   = '\SetBased\DataLayer\MySqlRoutineWrapper\\'.$class;
    $wrapper = new $class();

    return $wrapper;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates a complete wrapper method.
   *
   * @param $theRoutine array Metadata of the stored routine.
   *
   * @return string
   */
  public function writeRoutineFunction( $theRoutine )
  {
    $has_blob = $this->isBlobArgument( $theRoutine['argument_types'] );

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
  /**
   * Returns @c true if one of the arguments is a BLOB or CLOB.
   *
   * @param $theArgumentsType array|null The argument types.
   *
   * @return bool
   */
  public function isBlobArgument( $theArgumentsType )
  {
    $has_blob = false;

    if ($theArgumentsType)
    {
      foreach ($theArgumentsType as $argument_type)
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

          case 'tinyint':
          case 'smallint':
          case 'mediumint':
          case 'int':
          case 'bigint':
          case 'year':
          case 'decimal':
          case 'float':
          case 'double':
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

            // Nothing to do.
            break;

          default:
            set_assert_failed( "Unknown argument type '%s'.", $argument_type );
        }
      }
    }

    return $has_blob;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates a complete wrapper method for a stored routine with a LOB parameter.
   *
   * @param $theRoutine array The metadata of the stored routine.
   *
   * @return string
   */
  public function writeRoutineFunctionWithLob( $theRoutine )
  {
    $wrapper_function_name = $this->getWrapperRoutineName( $theRoutine['routine_name'] );

    $wrapper_args = $this->getWrapperArgs( $theRoutine );

    $routine_args = $this->getRoutineArgs( $theRoutine );


    $types = '';
    $nulls = '';
    foreach ($theRoutine['argument_types'] as $theType)
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
    foreach ($theRoutine['argument_types'] as $i => $argument)
    {
      if ($this->getBindVariableType( $argument )=='b')
      {
        $this->writeLine( '$n = strlen( $'.$theRoutine['argument_names'][$i].' );' );
        $this->writeLine( '$p = 0;' );
        $this->writeLine( 'while ($p<$n)' );
        $this->writeLine( '{' );
        $this->writeLine( '$b = $stmt->send_long_data( '.$blob_argument_index.', substr( $'.$theRoutine['argument_names'][$i].', $p, self::$ourChunckSize ) );' );
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
  /**
   * Returns a wrapper method for a stored routine without LOB parameters.
   *
   * @param $theRoutine array The metadata of the stored routine.
   *
   * @return string
   */
  public function writeRoutineFunctionWithoutLob( $theRoutine )
  {
    $wrapper_function_name = $this->getWrapperRoutineName( $theRoutine['routine_name'] );

    $wrapper_args = $this->getWrapperArgs( $theRoutine );

    $this->writeSeparator();
    $this->writeLine( '/** @sa Stored Routine '.$theRoutine['routine_name'].'.' );
    $this->writeLine( ' */' );
    $this->writeLine( 'static function '.$wrapper_function_name.'('.$wrapper_args.')' );
    $this->writeLine( '{' );

    $this->writeResultHandler( $theRoutine, $theRoutine['argument_types'] );
    $this->writeLine( '}' );
    $this->writeLine();

    return $this->myCode;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns code for the arguments of the wrapper method for the stored routine.
   *
   * @param $theRoutine array The metadata of the stored routine.
   *
   * @return string
   */
  protected function getWrapperArgs( $theRoutine )
  {
    if ($theRoutine['type']=='bulk')
    {
      $ret = '$theBulkHandler';
    }
    else
    {
      $ret = '';
    }

    if (isset($theRoutine['argument_types']))
    {
      foreach ($theRoutine['argument_types'] as $i => $arg_type)
      {
        if ($ret) $ret .= ', ';
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
            $ret .= '$'.$theRoutine['argument_names'][$i];
            break;

          case 'varbinary':
          case 'binary':

          case 'char':
          case 'varchar':
            $ret .= '$'.$theRoutine['argument_names'][$i];
            break;

          case 'time':
          case 'timestamp':

          case 'date':
          case 'datetime':
            $ret .= '$'.$theRoutine['argument_names'][$i];
            break;

          case 'enum':
          case 'bit':
          case 'set':
            $ret .= '$'.$theRoutine['argument_names'][$i];
            break;

          case 'tinytext':
          case 'text':
          case 'mediumtext':
          case 'longtext':
            $ret .= '$'.$theRoutine['argument_names'][$i];
            break;

          case 'tinyblob':
          case 'blob':
          case 'mediumblob':
          case 'longblob':
            $ret .= '$'.$theRoutine['argument_names'][$i];
            break;

          default:
            set_assert_failed( "Unknown argument type '%s'.", $arg_type );
        }
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns code for the arguments for calling the stored routine in a wrapper method.
   *
   * @param $theRoutine array The metadata of the stored routine.
   *
   * @return string
   */
  protected function getRoutineArgs( $theRoutine )
  {
    $ret = '';

    if ($theRoutine['argument_types'])
    {
      foreach ($theRoutine['argument_types'] as $i => $arg_type)
      {
        if ($ret) $ret .= ',';
        $ret .= $this->writeEscapedArgs( $arg_type, $theRoutine['argument_names'][$i] );
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the type of the corresponding bind variable. @sa http://php.net/manual/en/mysqli-stmt.bind-param.php
   *
   * @param string $theType The argument type of on argument of a stored routine.
   *
   * @return string
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
  /**
   * Appends a comment line to @c $myCode.
   */
  protected function writeSeparator()
  {
    for ($i = 0; $i<2 * $this->myIndentLevel; $i++)
    {
      $this->write( ' ' );
    }

    $this->write( '//' );

    for ($i = 0; $i<(self::C_PAGE_WIDTH - 2 * $this->myIndentLevel - 2 - 1); $i++)
    {
      $this->write( '-' );
    }
    $this->writeLine();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param $theString string Appends @a $theString to @c $myCode
   */
  protected function write( $theString )
  {
    $this->myCode .= $theString;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends @a $theString and a LF to @c $myCode.
   * - @a $theString must not contain a LF.
   * - Indent level is increased or decreased as @a $theString equals to '{' or '}'.
   *
   * @param string $theString
   *
   * @return void
   */
  protected function writeLine( $theString = '' )
  {
    if ($theString)
    {
      if (trim( $theString )=='}') $this->myIndentLevel--;
      for ($i = 0; $i<2 * $this->myIndentLevel; $i++)
      {
        $this->write( ' ' );
      }
      $this->myCode .= $theString;
      $this->myCode .= "\n";
      if (trim( $theString )=='{') $this->myIndentLevel++;
    }
    else
    {
      $this->myCode .= "\n";
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates code for fetching data of a stored routine with one or more LOB arguments.
   *
   * @param $theRoutine array The metadata of the stored routine.
   */
  abstract protected function writeRoutineFunctionLobFetchData( $theRoutine );

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates code for retuning the data returned by a stored routine with one or more LOB arguments.
   */
  abstract protected function writeRoutineFunctionLobReturnData();

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates code for calling the stored routine in the wrapper method.
   *
   * @param $theRoutine array The metadata of the stored routine.
   */
  abstract protected function writeResultHandler( $theRoutine );

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns @a $theStoredRoutineName after the first underscore in camel case.
   * E.g. set_foo_foo => FooFoo.
   *
   * @param $theStoredRoutineName string The name of the stored routine.
   *
   * @return string
   */
  private function getWrapperRoutineName( $theStoredRoutineName )
  {
    return preg_replace( '/(_)([a-z])/e', "strtoupper('\\2')", stristr( $theStoredRoutineName, '_' ) );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Return code for escaping the arguments of a stored routine.
   *
   * @param $theArgType string The type argument of a stored routine.
   * @param $argName    string The name argument of a stored routine.
   *
   * @return string
   */
  private function writeEscapedArgs( $theArgType, $argName )
  {
    $ret = '';
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
        $ret = '\'.self::QuoteNum($'.$argName.').\'';
        break;

      case 'varbinary':
      case 'binary':

      case 'char':
      case 'varchar':
        $ret = '\'.self::QuoteString($'.$argName.').\'';
        break;

      case 'time':
      case 'timestamp':

      case 'date':
      case 'datetime':
        $ret = '\'.self::QuoteString($'.$argName.').\'';
        break;

      case 'enum':
      case 'set':
        $ret = '\'.self::QuoteString($'.$argName.').\'';
        break;

      case 'bit':
        $ret = '\'.self::QuoteBit($'.$argName.').\'';
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
}
