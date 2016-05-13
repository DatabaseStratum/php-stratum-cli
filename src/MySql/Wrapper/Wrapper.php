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

use SetBased\Exception\FallenException;
use SetBased\Stratum\Helper\PhpCodeStore;
use SetBased\Stratum\NameMangler\NameMangler;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Abstract parent class for all wrapper generators.
 */
abstract class Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Variable for generated code with indention.
   *
   * @var PhpCodeStore
   */
  protected $codeStore;

  /**
   * The exceptions that the wrapper method can throw.
   *
   * @var array
   */
  protected $exceptions = [];

  /**
   * Array with fully qualified names that must be imported for this wrapper method.
   *
   * @var array
   */
  protected $imports = [];

  /**
   * @var bool If true BLOBs and CLOBs must be treated as strings.
   */
  private $lobAsStringFlag;

  /**
   * The name mangler for wrapper and parameter names.
   *
   * @var NameMangler
   */
  protected $nameMangler;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param NameMangler $nameMangler The mangler for wrapper and parameter names.
   * @param bool        $lobAsString If set BLOBs and CLOBs are treated as string. Otherwise, BLOBs and CLOBs will be
   *                                 send as long data.
   */
  public function __construct($nameMangler, $lobAsString)
  {
    $this->codeStore       = new PhpCodeStore(1);
    $this->nameMangler     = $nameMangler;
    $this->lobAsStringFlag = $lobAsString;
    $this->exceptions[]    = 'RuntimeException';
    $this->imports[]       = '\SetBased\Exception\RuntimeException';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * A factory for creating the appropriate object for generating a wrapper method for a stored routine.
   *
   * @param array       $routine      The metadata of the stored routine.
   * @param NameMangler $nameMangler  The mangler for wrapper and parameter names.
   * @param bool        $lobAsString  If set BLOBs and CLOBs are treated as string. Otherwise, BLOBs and CLOBs will be
   *                                  send as long data.
   *
   * @return Wrapper
   */
  public static function createRoutineWrapper($routine, $nameMangler, $lobAsString)
  {
    switch ($routine['designation'])
    {
      case 'bulk':
        $wrapper = new BulkWrapper($nameMangler, $lobAsString);
        break;

      case 'bulk_insert':
        $wrapper = new BulkInsertWrapper($nameMangler, $lobAsString);
        break;

      case 'log':
        $wrapper = new LogWrapper($nameMangler, $lobAsString);
        break;

      case 'none':
        $wrapper = new NoneWrapper($nameMangler, $lobAsString);
        break;

      case 'row0':
        $wrapper = new Row0Wrapper($nameMangler, $lobAsString);
        break;

      case 'row1':
        $wrapper = new Row1Wrapper($nameMangler, $lobAsString);
        break;

      case 'rows':
        $wrapper = new RowsWrapper($nameMangler, $lobAsString);
        break;

      case 'rows_with_key':
        $wrapper = new RowsWithKeyWrapper($nameMangler, $lobAsString);
        break;

      case 'rows_with_index':
        $wrapper = new RowsWithIndexWrapper($nameMangler, $lobAsString);
        break;

      case 'singleton0':
        $wrapper = new Singleton0Wrapper($nameMangler, $lobAsString);
        break;

      case 'singleton1':
        $wrapper = new Singleton1Wrapper($nameMangler, $lobAsString);
        break;

      case 'function':
        $wrapper = new FunctionsWrapper($nameMangler, $lobAsString);
        break;

      case 'table':
        $wrapper = new TableWrapper($nameMangler, $lobAsString);
        break;

      default:
        throw new FallenException('routine type', $routine['designation']);
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
    return $this->imports;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns true if one of the parameters is a BLOB or CLOB.
   *
   * @param array|null $parameters The parameters info (name, type, description).
   *
   * @return bool
   */
  public function isBlobParameter($parameters)
  {
    $has_blob = false;

    if ($parameters)
    {
      foreach ($parameters as $parameter_info)
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
   * @param array $routine Metadata of the stored routine.
   *
   * @return string PHP code with a routine wrapper.
   */
  public function writeRoutineFunction($routine)
  {
    if (!$this->lobAsStringFlag && $this->isBlobParameter($routine['parameters']))
    {
      return $this->writeRoutineFunctionWithLob($routine);
    }
    else
    {
      return $this->writeRoutineFunctionWithoutLob($routine);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates a complete wrapper method for a stored routine with a LOB parameter.
   *
   * @param array $routine The metadata of the stored routine.
   *
   * @return string PHP code with a routine wrapper.
   */
  public function writeRoutineFunctionWithLob($routine)
  {
    $wrapper_args = $this->getWrapperArgs($routine);
    $routine_args = $this->getRoutineArgs($routine);
    $method_name  = $this->nameMangler->getMethodName($routine['routine_name']);

    $bindings = '';
    $nulls    = '';
    foreach ($routine['parameters'] as $parameter_info)
    {
      $binding = $this->getBindVariableType($parameter_info['data_type']);
      if ($binding=='b')
      {
        $bindings .= 'b';
        if ($nulls) $nulls .= ',';
        $nulls .= '$null';
      }
    }

    $this->codeStore->appendSeparator();
    $this->generatePhpDoc($routine);
    $this->codeStore->append('public static function '.$method_name.'('.$wrapper_args.')');
    $this->codeStore->append('{');
    $this->codeStore->append('$query = \'CALL '.$routine['routine_name'].'('.$routine_args.')\';');
    $this->codeStore->append('$stmt  = self::$mysqli->prepare($query);');
    $this->codeStore->append('if (!$stmt) self::mySqlError(\'mysqli::prepare\');');
    $this->codeStore->append();
    $this->codeStore->append('$null = null;');
    $this->codeStore->append('$b = $stmt->bind_param(\''.$bindings.'\', '.$nulls.');');
    $this->codeStore->append('if (!$b) self::mySqlError(\'mysqli_stmt::bind_param\');');
    $this->codeStore->append();
    $this->codeStore->append('self::getMaxAllowedPacket();');
    $this->codeStore->append();

    $blob_argument_index = 0;
    foreach ($routine['parameters'] as $parameter_info)
    {
      if ($this->getBindVariableType($parameter_info['data_type'])=='b')
      {
        $mangledName = $this->nameMangler->getParameterName($parameter_info['parameter_name']);

        $this->codeStore->append('$n = strlen($'.$mangledName.');');
        $this->codeStore->append('$p = 0;');
        $this->codeStore->append('while ($p<$n)');
        $this->codeStore->append('{');
        $this->codeStore->append('$b = $stmt->send_long_data('.$blob_argument_index.', substr($'.$mangledName.', $p, self::$chunkSize));');
        $this->codeStore->append('if (!$b) self::mySqlError(\'mysqli_stmt::send_long_data\');');
        $this->codeStore->append('$p += self::$chunkSize;');
        $this->codeStore->append('}');
        $this->codeStore->append();

        $blob_argument_index++;
      }
    }

    $this->codeStore->append('if (self::$logQueries)');
    $this->codeStore->append('{');
    $this->codeStore->append('$time0 = microtime(true);');
    $this->codeStore->append();
    $this->codeStore->append('$b = $stmt->execute();');
    $this->codeStore->append('if (!$b) self::mySqlError(\'mysqli_stmt::execute\');');
    $this->codeStore->append();
    $this->codeStore->append('self::$queryLog[] = [\'query\' => $query,');
    $this->codeStore->append('                     \'time\'  => microtime(true) - $time0];');
    $this->codeStore->append('}');
    $this->codeStore->append('else');
    $this->codeStore->append('{');
    $this->codeStore->append('$b = $stmt->execute();');
    $this->codeStore->append('if (!$b) self::mySqlError(\'mysqli_stmt::execute\');');
    $this->codeStore->append('}');
    $this->codeStore->append();
    $this->writeRoutineFunctionLobFetchData($routine);
    $this->codeStore->append('$stmt->close();');
    $this->codeStore->append('if (self::$mysqli->more_results()) self::$mysqli->next_result();');
    $this->codeStore->append();
    $this->writeRoutineFunctionLobReturnData();
    $this->codeStore->append('}');
    $this->codeStore->append();

    return $this->codeStore->getCode();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a wrapper method for a stored routine without LOB parameters.
   *
   * @param             $routine     array The metadata of the stored routine.
   *
   * @return string PHP code with a routine wrapper.
   */
  public function writeRoutineFunctionWithoutLob($routine)
  {
    $wrapper_args = $this->getWrapperArgs($routine);
    $method_name  = $this->nameMangler->getMethodName($routine['routine_name']);

    $this->codeStore->appendSeparator();
    $this->generatePhpDoc($routine);
    $this->codeStore->append('public static function '.$method_name.'('.$wrapper_args.')');
    $this->codeStore->append('{');

    $this->writeResultHandler($routine);
    $this->codeStore->append('}');
    $this->codeStore->append();

    return $this->codeStore->getCode();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the type of the corresponding bind variable.
   *
   * @see http://php.net/manual/en/mysqli-stmt.bind-param.php
   *
   * @param string $type The parameter type of a parameter of a stored routine.
   *
   * @return string
   */
  protected function getBindVariableType($type)
  {
    $ret = '';
    switch ($type)
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
        $ret .= ($this->lobAsStringFlag) ? 's' : 'b';
        break;

      case 'list_of_int':
        $ret = 's';
        break;

      default:
        throw new FallenException('parameter type', $type);
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the exception that can be thrown by this method.
   *
   * @return array
   */
  protected function getDocBlockExceptions()
  {
    sort($this->exceptions);

    return $this->exceptions;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the return type the be used in the DocBlock.
   *
   * @return string
   */
  abstract protected function getDocBlockReturnType();

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns code for the arguments for calling the stored routine in a wrapper method.
   *
   * @param array $routine The metadata of the stored routine.
   *
   * @return string
   */
  protected function getRoutineArgs($routine)
  {
    $ret = '';

    foreach ($routine['parameters'] as $parameter_info)
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
   * @param array $routine The metadata of the stored routine.
   *
   * @return string
   */
  protected function getWrapperArgs($routine)
  {
    if ($routine['designation']=='bulk')
    {
      $ret = '$theBulkHandler';
    }
    else
    {
      $ret = '';
    }

    foreach ($routine['parameters'] as $i => $parameter_info)
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
          $ret .= $this->nameMangler->getParameterName('$'.$parameter_info['parameter_name']);
          break;

        case 'varbinary':
        case 'binary':
        case 'char':
        case 'varchar':
          $ret .= $this->nameMangler->getParameterName('$'.$parameter_info['parameter_name']);
          break;

        case 'time':
        case 'timestamp':
        case 'date':
        case 'datetime':
          $ret .= $this->nameMangler->getParameterName('$'.$parameter_info['parameter_name']);
          break;

        case 'enum':
        case 'bit':
        case 'set':
          $ret .= $this->nameMangler->getParameterName('$'.$parameter_info['parameter_name']);
          break;

        case 'tinytext':
        case 'text':
        case 'mediumtext':
        case 'longtext':
          $ret .= $this->nameMangler->getParameterName('$'.$parameter_info['parameter_name']);
          break;

        case 'tinyblob':
        case 'blob':
        case 'mediumblob':
        case 'longblob':
          $ret .= $this->nameMangler->getParameterName('$'.$parameter_info['parameter_name']);
          break;

        case 'list_of_int':
          $ret .= $this->nameMangler->getParameterName('$'.$parameter_info['parameter_name']);
          break;

        default:
          throw new FallenException('parameter type', $parameter_info['data_type']);
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates code for calling the stored routine in the wrapper method.
   *
   * @param array $routine The metadata of the stored routine.
   *
   * @return void
   */
  abstract protected function writeResultHandler($routine);

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates code for fetching data of a stored routine with one or more LOB parameters.
   *
   * @param array $routine The metadata of the stored routine.
   *
   * @return void
   */
  abstract protected function writeRoutineFunctionLobFetchData($routine);

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates code for retuning the data returned by a stored routine with one or more LOB parameters.
   *
   * @return void
   */
  abstract protected function writeRoutineFunctionLobReturnData();

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate php doc block in the data layer for stored routine.
   *
   * @param array $routine Metadata of the stored routine.
   */
  private function generatePhpDoc($routine)
  {
    $this->codeStore->append('/**', false);

    // Generate phpdoc with short description of routine wrapper.
    if ($routine['phpdoc']['sort_description'])
    {
      $this->codeStore->append(' * '.$routine['phpdoc']['sort_description'], false);
    }

    // Generate phpdoc with long description of routine wrapper.
    if ($routine['phpdoc']['long_description'])
    {
      $this->codeStore->append(' * '.$routine['phpdoc']['long_description'], false);
    }

    // Generate phpDoc with parameters and descriptions of parameters.
    if (!empty($routine['phpdoc']['parameters']))
    {
      $this->codeStore->append(' *', false);

      // Compute the max lengths of parameter names and the PHP types of the parameters.
      $max_name_length = 0;
      $max_type_length = 0;
      foreach ($routine['phpdoc']['parameters'] as $parameter)
      {
        $mangledName = $this->nameMangler->getParameterName($parameter['parameter_name']);

        $max_name_length = max($max_name_length, strlen($mangledName));
        $max_type_length = max($max_type_length, strlen($parameter['php_type']));
      }
      // Add 1 character for $.
      $max_name_length++;

      // Generate phpDoc for the parameters of the wrapper method.
      foreach ($routine['phpdoc']['parameters'] as $parameter)
      {
        $mangledName = $this->nameMangler->getParameterName($parameter['parameter_name']);

        $format = sprintf(' * %%-%ds %%-%ds %%-%ds %%s', strlen('@param'), $max_type_length, $max_name_length);

        $lines = explode("\n", $parameter['description']);
        if (!empty($lines))
        {
          $line = array_shift($lines);
          $this->codeStore->append(sprintf($format, '@param', $parameter['php_type'], '$'.$mangledName, $line), false);
          foreach ($lines as $line)
          {
            $this->codeStore->append(sprintf($format, ' ', ' ', ' ', $line), false);
          }
        }
        else
        {
          $this->codeStore->append(sprintf($format, '@param', $parameter['php_type'], '$'.$mangledName, ''), false);
        }

        $this->codeStore->append(sprintf($format, ' ', ' ', ' ', $parameter['data_type_descriptor']), false);
      }
    }
    elseif ($routine['designation']==='bulk_insert')
    {
      // Generate parameter for bulk_insert routine type.
      $this->codeStore->append(' *', false);
      $this->codeStore->append(' * @param array $rows', false);
    }

    // Generate return parameter doc.
    $return = $this->getDocBlockReturnType();
    if ($return)
    {
      $this->codeStore->append(' *', false);
      $this->codeStore->append(' * @return '.$return, false);
    }

    // Generate exceptions doc.
    $exceptions = $this->getDocBlockExceptions();
    if (!empty($exceptions))
    {
      $exceptions = array_unique($exceptions);
      foreach ($exceptions as $exception)
      {
        $this->codeStore->append(' * @throws '.$exception, false);
      }
    }

    $this->codeStore->append(' */', false);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Return code for escaping the arguments of a stored routine.
   *
   * @param string[] $parameters Information about the parameters of the stored routine.
   *
   * @return string
   */
  private function writeEscapedArgs($parameters)
  {
    $mangledName = $this->nameMangler->getParameterName($parameters['parameter_name']);

    switch ($parameters['data_type'])
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
        $ret = '\'.self::quoteNum($'.$mangledName.').\'';
        break;

      case 'varbinary':
      case 'binary':
      case 'char':
      case 'varchar':
        $ret = '\'.self::quoteString($'.$mangledName.').\'';
        break;

      case 'time':
      case 'timestamp':
      case 'date':
      case 'datetime':
        $ret = '\'.self::quoteString($'.$mangledName.').\'';
        break;

      case 'enum':
      case 'set':
        $ret = '\'.self::quoteString($'.$mangledName.').\'';
        break;

      case 'bit':
        $ret = '\'.self::quoteBit($'.$mangledName.').\'';
        break;

      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':
      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob':
        $ret = ($this->lobAsStringFlag) ? $ret = '\'.self::quoteString($'.$mangledName.').\'' : '?';
        break;

      case 'list_of_int':
        $ret = '\'.self::quoteListOfInt($'.$mangledName.", '".
          addslashes($parameters['delimiter'])."', '".
          addslashes($parameters['enclosure'])."', '".
          addslashes($parameters['escape'])."').'";
        break;

      default:
        throw new FallenException('parameter type', $parameters['data_type']);
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
