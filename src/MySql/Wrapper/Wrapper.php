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

use SetBased\Stratum\Exception\FallenException;
use SetBased\Stratum\NameMangler\SetBasedNameMangler;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class Wrapper
 * routine.
 */
abstract class Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The maximum width of the generated code (in chars).
   */
  const C_PAGE_WIDTH = 120;

  /**
   * The exceptions that the wrapper method can throw.
   *
   * @var array
   */
  protected $myExceptions;

  /**
   * Array with fully qualified names that must be imported for this wrapper method.
   *
   * @var array
   */
  protected $myImports = [];

  /**
   * @var string Buffer for generated code.
   */
  private $myCode = '';

  /**
   * @var int The current level of indentation in the generated code.
   */
  private $myIndentLevel = 1;

  /**
   * @var bool If true BLOBs and CLOBs must be treated as strings.
   */
  private $myLobAsStringFlag;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param bool $theLobAsStringFlag If set BLOBs and CLOBs are treated as string. Otherwise, BLOBs and CLOBs will be
   *                                 send as long data.
   */
  public function __construct($theLobAsStringFlag)
  {
    $this->myLobAsStringFlag = $theLobAsStringFlag;
    $this->myExceptions      = ['\RunTimeException'];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * A factory for creating the appropriate object for generating a wrapper method for a stored routine.
   *
   * @param array $theRoutine         The metadata of the stored routine.
   * @param bool  $theLobAsStringFlag If set BLOBs and CLOBs are treated as string. Otherwise, BLOBs and CLOBs will be
   *                                  send as long data.
   *
   * @return Wrapper
   */
  public static function createRoutineWrapper($theRoutine, $theLobAsStringFlag)
  {
    switch ($theRoutine['designation'])
    {
      case 'bulk':
        $wrapper = new BulkWrapper($theLobAsStringFlag);
        break;

      case 'bulk_insert':
        $wrapper = new BulkInsertWrapper($theLobAsStringFlag);
        break;

      case 'log':
        $wrapper = new LogWrapper($theLobAsStringFlag);
        break;

      case 'none':
        $wrapper = new NoneWrapper($theLobAsStringFlag);
        break;

      case 'row0':
        $wrapper = new Row0Wrapper($theLobAsStringFlag);
        break;

      case 'row1':
        $wrapper = new Row1Wrapper($theLobAsStringFlag);
        break;

      case 'rows':
        $wrapper = new RowsWrapper($theLobAsStringFlag);
        break;

      case 'rows_with_key':
        $wrapper = new RowsWithKeyWrapper($theLobAsStringFlag);
        break;

      case 'rows_with_index':
        $wrapper = new RowsWithIndexWrapper($theLobAsStringFlag);
        break;

      case 'singleton0':
        $wrapper = new Singleton0Wrapper($theLobAsStringFlag);
        break;

      case 'singleton1':
        $wrapper = new Singleton1Wrapper($theLobAsStringFlag);
        break;

      case 'function':
        $wrapper = new FunctionsWrapper($theLobAsStringFlag);
        break;

      case 'table':
        $wrapper = new TableWrapper($theLobAsStringFlag);
        break;

      default:
        throw new FallenException('routine type', $theRoutine['designation']);
    }

    return $wrapper;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns an array with fully qualified names that must be imported in the stored routine wrapper class.
   *
   * @return array
   */
  public function getImports()
  {
    return $this->myImports;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns true if one of the parameters is a BLOB or CLOB.
   *
   * @param array|null $theParameters The parameters info (name, type, description).
   *
   * @return bool
   */
  public function isBlobParameter($theParameters)
  {
    $has_blob = false;

    if ($theParameters)
    {
      foreach ($theParameters as $parameter_info)
      {
        switch ($parameter_info['data_type'])
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

          case 'list_of_int':

            // Nothing to do.
            break;

          default:
            throw new FallenException('parameter type', $parameter_info['data_type']);
        }
      }
    }

    return $has_blob;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates a complete wrapper method.
   *
   * @param array $theRoutine Metadata of the stored routine.
   *
   * @return string PHP code with a routine wrapper.
   */
  public function writeRoutineFunction($theRoutine)
  {
    if (!$this->myLobAsStringFlag && $this->isBlobParameter($theRoutine['parameters']))
    {
      return $this->writeRoutineFunctionWithLob($theRoutine);
    }
    else
    {
      return $this->writeRoutineFunctionWithoutLob($theRoutine);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates a complete wrapper method for a stored routine with a LOB parameter.
   *
   * @param array $theRoutine The metadata of the stored routine.
   *
   * @return string PHP code with a routine wrapper.
   */
  public function writeRoutineFunctionWithLob($theRoutine)
  {
//    $wrapper_function_name = $this->getWrapperRoutineName($theRoutine['routine_name']);
    $wrapper_function_name = SetBasedNameMangler::getMethodName($theRoutine['routine_name']);

    $wrapper_args = $this->getWrapperArgs($theRoutine);

    $routine_args = $this->getRoutineArgs($theRoutine);


    $bindings = '';
    $nulls    = '';
    foreach ($theRoutine['parameters'] as $parameter_info)
    {
      $binding = $this->getBindVariableType($parameter_info['data_type']);
      if ($binding=='b')
      {
        $bindings .= 'b';
        if ($nulls) $nulls .= ',';
        $nulls .= '$null';
      }
    }

    $this->writeSeparator();
    $this->generatePhpDoc($theRoutine);
    $this->writeLine('public static function '.$wrapper_function_name.'( '.$wrapper_args.' )');
    $this->writeLine('{');
    $this->writeLine('$query = \'CALL '.$theRoutine['routine_name'].'( '.$routine_args.' )\';');
    $this->writeLine('$stmt  = self::$ourMySql->prepare( $query );');
    $this->writeLine('if (!$stmt) self::mySqlError( \'mysqli::prepare\' );');
    $this->writeLine();
    $this->writeLine('$null = null;');
    $this->writeLine('$b = $stmt->bind_param( \''.$bindings.'\', '.$nulls.' );');
    $this->writeLine('if (!$b) self::mySqlError( \'mysqli_stmt::bind_param\' );');
    $this->writeLine();
    $this->writeLine('self::getMaxAllowedPacket();');
    $this->writeLine();

    $blob_argument_index = 0;
    foreach ($theRoutine['parameters'] as $i => $parameter_info)
    {
      if ($this->getBindVariableType($parameter_info['data_type'])=='b')
      {
        $this->writeLine('$n = strlen( $'.$parameter_info['name'].' );');
        $this->writeLine('$p = 0;');
        $this->writeLine('while ($p<$n)');
        $this->writeLine('{');
        $this->writeLine('$b = $stmt->send_long_data( '.$blob_argument_index.', substr( $'.$parameter_info['name'].', $p, self::$ourChunkSize ) );');
        $this->writeLine('if (!$b) self::mySqlError( \'mysqli_stmt::send_long_data\' );');
        $this->writeLine('$p += self::$ourChunkSize;');
        $this->writeLine('}');
        $this->writeLine();

        $blob_argument_index++;
      }
    }

    $this->writeLine('$b = $stmt->execute();');
    $this->writeLine('if (!$b) self::mySqlError( \'mysqli_stmt::execute\' );');
    $this->writeLine();
    $this->writeRoutineFunctionLobFetchData($theRoutine);
    $this->writeLine('$stmt->close();');
    $this->writeLine('if(self::$ourMySql->more_results()) self::$ourMySql->next_result();');
    $this->writeLine();
    $this->writeRoutineFunctionLobReturnData();
    $this->writeLine('}');
    $this->writeLine();

    return $this->myCode;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a wrapper method for a stored routine without LOB parameters.
   *
   * @param $theRoutine array The metadata of the stored routine.
   *
   * @return string PHP code with a routine wrapper.
   */
  public function writeRoutineFunctionWithoutLob($theRoutine)
  {
//    $wrapper_function_name = $this->getWrapperRoutineName($theRoutine['routine_name']);
    $wrapper_function_name = SetBasedNameMangler::getMethodName($theRoutine['routine_name']);

    $wrapper_args = $this->getWrapperArgs($theRoutine);

    $this->writeSeparator();
    $this->generatePhpDoc($theRoutine);
    $this->writeLine('public static function '.$wrapper_function_name.'( '.$wrapper_args.' )');
    $this->writeLine('{');

    $this->writeResultHandler($theRoutine);
    $this->writeLine('}');
    $this->writeLine();

    return $this->myCode;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the type of the corresponding bind variable.
   *
   * @see http://php.net/manual/en/mysqli-stmt.bind-param.php
   *
   * @param string $theType The parameter type of a parameter of a stored routine.
   *
   * @return string
   */
  protected function getBindVariableType($theType)
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
        $ret .= ($this->myLobAsStringFlag) ? 's' : 'b';
        break;

      case 'list_of_int':
        $ret = 's';
        break;

      default:
        throw new FallenException('parameter type', $theType);
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the exception that can be thrown by this method.
   *
   * @return array;
   */
  protected function getDocBlockExceptions()
  {
    return $this->myExceptions;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the return type the be used in the DocBlock.
   */
  abstract protected function getDocBlockReturnType();

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns code for the arguments for calling the stored routine in a wrapper method.
   *
   * @param $theRoutine array The metadata of the stored routine.
   *
   * @return string
   */
  protected function getRoutineArgs($theRoutine)
  {
    $ret = '';

    foreach ($theRoutine['parameters'] as $i => $parameter_info)
    {
      if ($ret) $ret .= ',';
      $ret .= $this->writeEscapedArgs($parameter_info);
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns code for the parameters of the wrapper method for the stored routine.
   *
   * @param $theRoutine array The metadata of the stored routine.
   *
   * @return string
   */
  protected function getWrapperArgs($theRoutine)
  {
    if ($theRoutine['designation']=='bulk')
    {
      $ret = '$theBulkHandler';
    }
    else
    {
      $ret = '';
    }

    foreach ($theRoutine['parameters'] as $i => $parameter_info)
    {
      if ($ret) $ret .= ', ';
      switch ($parameter_info['data_type'])
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
//          $ret .= '$'.$parameter_info['name'];
          $ret .= SetBasedNameMangler::getParameterName('$'.$parameter_info['name']);
          break;

        case 'varbinary':
        case 'binary':

        case 'char':
        case 'varchar':
          $ret .= SetBasedNameMangler::getParameterName('$'.$parameter_info['name']);
          break;

        case 'time':
        case 'timestamp':

        case 'date':
        case 'datetime':
          $ret .= SetBasedNameMangler::getParameterName('$'.$parameter_info['name']);
          break;

        case 'enum':
        case 'bit':
        case 'set':
          $ret .= SetBasedNameMangler::getParameterName('$'.$parameter_info['name']);
          break;

        case 'tinytext':
        case 'text':
        case 'mediumtext':
        case 'longtext':
          $ret .= SetBasedNameMangler::getParameterName('$'.$parameter_info['name']);
          break;

        case 'tinyblob':
        case 'blob':
        case 'mediumblob':
        case 'longblob':
          $ret .= SetBasedNameMangler::getParameterName('$'.$parameter_info['name']);
          break;

        case 'list_of_int':
          $ret .= SetBasedNameMangler::getParameterName('$'.$parameter_info['name']);
          break;

        default:
          throw new FallenException('parameter type', $parameter_info['data_type']);
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param $theString string Appends @a $theString to @c $myCode
   */
  protected function write($theString)
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
  protected function writeLine($theString = '')
  {
    if ($theString)
    {
      if (trim($theString)=='}') $this->myIndentLevel--;
      for ($i = 0; $i<2 * $this->myIndentLevel; $i++)
      {
        $this->write(' ');
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
  /**
   * Generates code for calling the stored routine in the wrapper method.
   *
   * @param $theRoutine array The metadata of the stored routine.
   */
  abstract protected function writeResultHandler($theRoutine);

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates code for fetching data of a stored routine with one or more LOB parameters.
   *
   * @param $theRoutine array The metadata of the stored routine.
   */
  abstract protected function writeRoutineFunctionLobFetchData($theRoutine);

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates code for retuning the data returned by a stored routine with one or more LOB parameters.
   */
  abstract protected function writeRoutineFunctionLobReturnData();

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends a comment line to @c $myCode.
   */
  protected function writeSeparator()
  {
    for ($i = 0; $i<2 * $this->myIndentLevel; $i++)
    {
      $this->write(' ');
    }

    $this->write('//');

    for ($i = 0; $i<(self::C_PAGE_WIDTH - 2 * $this->myIndentLevel - 2 - 1); $i++)
    {
      $this->write('-');
    }
    $this->writeLine();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate php doc block in the data layer for stored routine.
   *
   * @param array $theRoutine Metadata of the stored routine.
   */
  private function generatePhpDoc($theRoutine)
  {
    $this->writeLine('/**');

    // Generate phpdoc with short description of routine wrapper.
    if ($theRoutine['phpdoc']['sort_description'])
    {
      $this->writeLine(' * '.$theRoutine['phpdoc']['sort_description']);
    }

    // Generate phpdoc with long description of routine wrapper.
    if ($theRoutine['phpdoc']['long_description'])
    {
      $this->writeLine(' * '.$theRoutine['phpdoc']['long_description']);
    }

    // Generate phpDoc with parameters and descriptions of parameters.
    if ($theRoutine['phpdoc']['parameters'])
    {
      $this->writeLine(' *');

      // Compute the max lengths of parameter names and the PHP types of the parameters.
      $max_name_length = 0;
      $max_type_length = 0;
      foreach ($theRoutine['phpdoc']['parameters'] as $parameter)
      {
        $max_name_length = max($max_name_length, strlen($parameter['name']));
        $max_type_length = max($max_type_length, strlen($parameter['php_type']));
      }
      # Add 1 character for $.
      $max_name_length++;

      // Generate phpDoc for the parameters of the wrapper method.
      foreach ($theRoutine['phpdoc']['parameters'] as $parameter)
      {
        $format = sprintf(" * %%-%ds %%-%ds %%-%ds %%s", strlen('@param'), $max_type_length, $max_name_length);

        $lines = explode("\n", $parameter['description']);
        if ($lines)
        {
          $line = array_shift($lines);
          $this->writeLine(sprintf($format, '@param', $parameter['php_type'], '$'.$parameter['name'], $line));
          foreach ($lines as $line)
          {
            $this->writeLine(sprintf($format, ' ', ' ', ' ', $line));
          }
        }
        else
        {
          $this->writeLine(sprintf($format, '@param', $parameter['php_type'], '$'.$parameter['name'], ''));
        }

        $this->writeLine(sprintf($format, ' ', ' ', ' ', $parameter['data_type_descriptor']));
      }
    }
    elseif ($theRoutine['designation']==='bulk_insert')
    {
      // Generate parameter for bulk_insert routine type.
      $this->writeLine(' *');
      $this->writeLine(' * @param array $theData');
    }

    // Generate return parameter doc.
    $return = $this->getDocBlockReturnType();
    if ($return)
    {
      $this->writeLine(' *');
      $this->writeLine(' * @return '.$return);
    }

    // Generate exceptions doc.
    $exceptions = $this->getDocBlockExceptions();
    if ($exceptions)
    {
      $exceptions = array_unique($exceptions);
      foreach ($exceptions as $exception)
      {
        $this->writeLine(' * @throws  '.$exception);
      }
    }

    $this->writeLine(' */');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns @a $theStoredRoutineName after the first underscore in camel case.
   * E.g. set_foo_foo => fooFoo.
   *
   * @param $theStoredRoutineName string The name of the stored routine.
   *
   * @return string
   */
  private function getWrapperRoutineName($theStoredRoutineName)
  {
    return lcfirst(preg_replace_callback('/(_)([a-z])/',
      function ($matches)
      {
        return strtoupper($matches[2]);
      },
                                         stristr($theStoredRoutineName, '_')));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Return code for escaping the arguments of a stored routine.
   *
   * @param array[] $theParameters Information about the parameters of the stored routine.
   *
   * @return string
   */
  private function writeEscapedArgs($theParameters)
  {
    switch ($theParameters['data_type'])
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
        $ret = '\'.self::quoteNum( $'.$theParameters['name'].' ).\'';
        break;

      case 'varbinary':
      case 'binary':

      case 'char':
      case 'varchar':
        $ret = '\'.self::quoteString( $'.$theParameters['name'].' ).\'';
        break;

      case 'time':
      case 'timestamp':

      case 'date':
      case 'datetime':
        $ret = '\'.self::quoteString( $'.$theParameters['name'].' ).\'';
        break;

      case 'enum':
      case 'set':
        $ret = '\'.self::quoteString( $'.$theParameters['name'].' ).\'';
        break;

      case 'bit':
        $ret = '\'.self::quoteBit( $'.$theParameters['name'].' ).\'';
        break;

      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':

      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob':
        $ret = ($this->myLobAsStringFlag) ? $ret = '\'.self::quoteString( $'.$theParameters['name'].' ).\'' : '?';
        break;

      case 'list_of_int':
        $ret = '\'.self::quoteListOfInt( $'.$theParameters['name'].", '".
          addslashes($theParameters['delimiter'])."', '".
          addslashes($theParameters['enclosure'])."', '".
          addslashes($theParameters['escape'])."' ).'";
        break;

      default:
        throw new FallenException('parameter type', $theParameters['data_type']);
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
