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
use SetBased\Stratum\NameMangler\NameMangler;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Abstract parent class for all wrapper generators.
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
  protected $exceptions = [];

  /**
   * Array with fully qualified names that must be imported for this wrapper method.
   *
   * @var array
   */
  protected $imports = [];

  /**
   * @var string Buffer for generated code.
   */
  private $code = '';

  /**
   * @var int The current level of indentation in the generated code.
   */
  private $indentLevel = 1;

  /**
   * @var bool If true BLOBs and CLOBs must be treated as strings.
   */
  private $lobAsStringFlag;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param bool $lobAsString        If set BLOBs and CLOBs are treated as string. Otherwise, BLOBs and CLOBs will be
   *                                 send as long data.
   */
  public function __construct($lobAsString)
  {
    $this->lobAsStringFlag = $lobAsString;
    $this->exceptions[]    = 'RuntimeException';
    $this->imports[]       = '\SetBased\Exception\RuntimeException';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * A factory for creating the appropriate object for generating a wrapper method for a stored routine.
   *
   * @param array $routine            The metadata of the stored routine.
   * @param bool  $lobAsString        If set BLOBs and CLOBs are treated as string. Otherwise, BLOBs and CLOBs will be
   *                                  send as long data.
   *
   * @return Wrapper
   */
  public static function createRoutineWrapper($routine, $lobAsString)
  {
    switch ($routine['designation'])
    {
      case 'bulk':
        $wrapper = new BulkWrapper($lobAsString);
        break;

      case 'bulk_insert':
        $wrapper = new BulkInsertWrapper($lobAsString);
        break;

      case 'log':
        $wrapper = new LogWrapper($lobAsString);
        break;

      case 'none':
        $wrapper = new NoneWrapper($lobAsString);
        break;

      case 'row0':
        $wrapper = new Row0Wrapper($lobAsString);
        break;

      case 'row1':
        $wrapper = new Row1Wrapper($lobAsString);
        break;

      case 'rows':
        $wrapper = new RowsWrapper($lobAsString);
        break;

      case 'rows_with_key':
        $wrapper = new RowsWithKeyWrapper($lobAsString);
        break;

      case 'rows_with_index':
        $wrapper = new RowsWithIndexWrapper($lobAsString);
        break;

      case 'singleton0':
        $wrapper = new Singleton0Wrapper($lobAsString);
        break;

      case 'singleton1':
        $wrapper = new Singleton1Wrapper($lobAsString);
        break;

      case 'function':
        $wrapper = new FunctionsWrapper($lobAsString);
        break;

      case 'table':
        $wrapper = new TableWrapper($lobAsString);
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
   * @param array       $routine     Metadata of the stored routine.
   * @param NameMangler $nameMangler The mangler for wrapper and parameter names.
   *
   * @return string PHP code with a routine wrapper.
   */
  public function writeRoutineFunction($routine, $nameMangler)
  {
    if (!$this->lobAsStringFlag && $this->isBlobParameter($routine['parameters']))
    {
      return $this->writeRoutineFunctionWithLob($routine, $nameMangler);
    }
    else
    {
      return $this->writeRoutineFunctionWithoutLob($routine, $nameMangler);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates a complete wrapper method for a stored routine with a LOB parameter.
   *
   * @param array       $routine     The metadata of the stored routine.
   * @param NameMangler $nameMangler The mangler for wrapper and parameter names.
   *
   * @return string PHP code with a routine wrapper.
   */
  public function writeRoutineFunctionWithLob($routine, $nameMangler)
  {
    $wrapper_args = $this->getWrapperArgs($routine, $nameMangler);
    $routine_args = $this->getRoutineArgs($routine);
    $method_name  = $nameMangler->getMethodName($routine['routine_name']);

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

    $this->writeSeparator();
    $this->generatePhpDoc($routine);
    $this->writeLine('public static function '.$method_name.'('.$wrapper_args.')');
    $this->writeLine('{');
    $this->writeLine('$query = \'CALL '.$routine['routine_name'].'('.$routine_args.')\';');
    $this->writeLine('$stmt  = self::$mysqli->prepare($query);');
    $this->writeLine('if (!$stmt) self::mySqlError(\'mysqli::prepare\');');
    $this->writeLine();
    $this->writeLine('$null = null;');
    $this->writeLine('$b = $stmt->bind_param(\''.$bindings.'\', '.$nulls.');');
    $this->writeLine('if (!$b) self::mySqlError(\'mysqli_stmt::bind_param\');');
    $this->writeLine();
    $this->writeLine('self::getMaxAllowedPacket();');
    $this->writeLine();

    $blob_argument_index = 0;
    foreach ($routine['parameters'] as $parameter_info)
    {
      if ($this->getBindVariableType($parameter_info['data_type'])=='b')
      {
        $this->writeLine('$n = strlen($'.$parameter_info['name'].');');
        $this->writeLine('$p = 0;');
        $this->writeLine('while ($p<$n)');
        $this->writeLine('{');
        $this->writeLine('$b = $stmt->send_long_data('.$blob_argument_index.', substr($'.$parameter_info['name'].', $p, self::$chunkSize));');
        $this->writeLine('if (!$b) self::mySqlError(\'mysqli_stmt::send_long_data\');');
        $this->writeLine('$p += self::$chunkSize;');
        $this->writeLine('}');
        $this->writeLine();

        $blob_argument_index++;
      }
    }

    $this->writeLine('if (self::$logQueries)');
    $this->writeLine('{');
    $this->writeLine('$time0 = microtime(true);');
    $this->writeLine('');
    $this->writeLine('$b = $stmt->execute();');
    $this->writeLine('if (!$b) self::mySqlError(\'mysqli_stmt::execute\');');
    $this->writeLine('');
    $this->writeLine('self::$queryLog[] = [\'query\' => $query,');
    $this->writeLine('                     \'time\'  => microtime(true) - $time0];');
    $this->writeLine('}');
    $this->writeLine('else');
    $this->writeLine('{');
    $this->writeLine('$b = $stmt->execute();');
    $this->writeLine('if (!$b) self::mySqlError(\'mysqli_stmt::execute\');');
    $this->writeLine('}');
    $this->writeLine();
    $this->writeRoutineFunctionLobFetchData($routine);
    $this->writeLine('$stmt->close();');
    $this->writeLine('if (self::$mysqli->more_results()) self::$mysqli->next_result();');
    $this->writeLine();
    $this->writeRoutineFunctionLobReturnData();
    $this->writeLine('}');
    $this->writeLine();

    return $this->code;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a wrapper method for a stored routine without LOB parameters.
   *
   * @param             $routine     array The metadata of the stored routine.
   * @param NameMangler $nameMangler The mangler for wrapper and parameter names.
   *
   * @return string PHP code with a routine wrapper.
   */
  public function writeRoutineFunctionWithoutLob($routine, $nameMangler)
  {
    $wrapper_args = $this->getWrapperArgs($routine, $nameMangler);
    $method_name  = $nameMangler->getMethodName($routine['routine_name']);

    $this->writeSeparator();
    $this->generatePhpDoc($routine);
    $this->writeLine('public static function '.$method_name.'('.$wrapper_args.')');
    $this->writeLine('{');

    $this->writeResultHandler($routine);
    $this->writeLine('}');
    $this->writeLine();

    return $this->code;
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
   * @param array       $routine     The metadata of the stored routine.
   * @param NameMangler $nameMangler The mangler for wrapper and parameter names.
   *
   * @return string
   */
  protected function getWrapperArgs($routine, $nameMangler)
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
          $ret .= $nameMangler->getParameterName('$'.$parameter_info['name']);
          break;

        case 'varbinary':
        case 'binary':

        case 'char':
        case 'varchar':
          $ret .= $nameMangler->getParameterName('$'.$parameter_info['name']);
          break;

        case 'time':
        case 'timestamp':

        case 'date':
        case 'datetime':
          $ret .= $nameMangler->getParameterName('$'.$parameter_info['name']);
          break;

        case 'enum':
        case 'bit':
        case 'set':
          $ret .= $nameMangler->getParameterName('$'.$parameter_info['name']);
          break;

        case 'tinytext':
        case 'text':
        case 'mediumtext':
        case 'longtext':
          $ret .= $nameMangler->getParameterName('$'.$parameter_info['name']);
          break;

        case 'tinyblob':
        case 'blob':
        case 'mediumblob':
        case 'longblob':
          $ret .= $nameMangler->getParameterName('$'.$parameter_info['name']);
          break;

        case 'list_of_int':
          $ret .= $nameMangler->getParameterName('$'.$parameter_info['name']);
          break;

        default:
          throw new FallenException('parameter type', $parameter_info['data_type']);
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends a string to the generated code.
   *
   * @param string $string The string.
   */
  protected function write($string)
  {
    $this->code .= $string;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   *  Appends a string and automatically a LF to the generated code.
   * Note:
   * - The string must not contain a LF.
   * - Indent level is increased or decreased as the string equals to '{' or '}'.
   *
   * @param string $string The string.
   */
  protected function writeLine($string = '')
  {
    if ($string)
    {
      if (trim($string)=='}') $this->indentLevel--;
      for ($i = 0; $i<2 * $this->indentLevel; $i++)
      {
        $this->write(' ');
      }
      $this->code .= $string;
      $this->code .= "\n";
      if (trim($string)=='{') $this->indentLevel++;
    }
    else
    {
      $this->code .= "\n";
    }
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
   * Appends a comment line to the generated code.
   */
  protected function writeSeparator()
  {
    for ($i = 0; $i<2 * $this->indentLevel; $i++)
    {
      $this->write(' ');
    }

    $this->write('//');

    for ($i = 0; $i<(self::C_PAGE_WIDTH - 2 * $this->indentLevel - 2 - 1); $i++)
    {
      $this->write('-');
    }
    $this->writeLine();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate php doc block in the data layer for stored routine.
   *
   * @param array $routine Metadata of the stored routine.
   */
  private function generatePhpDoc($routine)
  {
    $this->writeLine('/**');

    // Generate phpdoc with short description of routine wrapper.
    if ($routine['phpdoc']['sort_description'])
    {
      $this->writeLine(' * '.$routine['phpdoc']['sort_description']);
    }

    // Generate phpdoc with long description of routine wrapper.
    if ($routine['phpdoc']['long_description'])
    {
      $this->writeLine(' * '.$routine['phpdoc']['long_description']);
    }

    // Generate phpDoc with parameters and descriptions of parameters.
    if (!empty($routine['phpdoc']['parameters']))
    {
      $this->writeLine(' *');

      // Compute the max lengths of parameter names and the PHP types of the parameters.
      $max_name_length = 0;
      $max_type_length = 0;
      foreach ($routine['phpdoc']['parameters'] as $parameter)
      {
        $max_name_length = max($max_name_length, strlen($parameter['name']));
        $max_type_length = max($max_type_length, strlen($parameter['php_type']));
      }
      // Add 1 character for $.
      $max_name_length++;

      // Generate phpDoc for the parameters of the wrapper method.
      foreach ($routine['phpdoc']['parameters'] as $parameter)
      {
        $format = sprintf(' * %%-%ds %%-%ds %%-%ds %%s', strlen('@param'), $max_type_length, $max_name_length);

        $lines = explode("\n", $parameter['description']);
        if (!empty($lines))
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
    elseif ($routine['designation']==='bulk_insert')
    {
      // Generate parameter for bulk_insert routine type.
      $this->writeLine(' *');
      $this->writeLine(' * @param array $rows');
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
    if (!empty($exceptions))
    {
      $exceptions = array_unique($exceptions);
      foreach ($exceptions as $exception)
      {
        $this->writeLine(' * @throws '.$exception);
      }
    }

    $this->writeLine(' */');
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
        $ret = '\'.self::quoteNum($'.$parameters['name'].').\'';
        break;

      case 'varbinary':
      case 'binary':

      case 'char':
      case 'varchar':
        $ret = '\'.self::quoteString($'.$parameters['name'].').\'';
        break;

      case 'time':
      case 'timestamp':

      case 'date':
      case 'datetime':
        $ret = '\'.self::quoteString($'.$parameters['name'].').\'';
        break;

      case 'enum':
      case 'set':
        $ret = '\'.self::quoteString($'.$parameters['name'].').\'';
        break;

      case 'bit':
        $ret = '\'.self::quoteBit($'.$parameters['name'].').\'';
        break;

      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':

      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob':
        $ret = ($this->lobAsStringFlag) ? $ret = '\'.self::quoteString($'.$parameters['name'].').\'' : '?';
        break;

      case 'list_of_int':
        $ret = '\'.self::quoteListOfInt($'.$parameters['name'].", '".
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
