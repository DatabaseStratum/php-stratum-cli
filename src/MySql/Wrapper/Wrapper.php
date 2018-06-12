<?php

namespace SetBased\Stratum\MySql\Wrapper;

use SetBased\Exception\FallenException;
use SetBased\Helper\CodeStore\PhpCodeStore;
use SetBased\Stratum\MySql\Helper\DataTypeHelper;
use SetBased\Stratum\NameMangler\NameMangler;

/**
 * Abstract parent class for all wrapper generators.
 */
abstract class Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The code store for the generated PHP code.
   *
   * @var PhpCodeStore
   */
  protected $codeStore;

  /**
   * Array with fully qualified names that must be imported for this wrapper method.
   *
   * @var array
   */
  protected $imports = [];

  /**
   * The name mangler for wrapper and parameter names.
   *
   * @var NameMangler
   */
  protected $nameMangler;

  /**
   * @var bool If true BLOBs and CLOBs must be treated as strings.
   */
  private $lobAsStringFlag;

  /**
   * The metadata of the stored routine.
   *
   * @var array
   */
  protected $routine;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param array        $routine     The metadata of the stored routine.
   * @param PhpCodeStore $codeStore   The code store for the generated code.
   * @param NameMangler  $nameMangler The mangler for wrapper and parameter names.
   * @param bool         $lobAsString If set BLOBs and CLOBs are treated as string. Otherwise, BLOBs and CLOBs will be
   *                                  send as long data.
   */
  public function __construct($routine, $codeStore, $nameMangler, $lobAsString)
  {
    $this->routine         = $routine;
    $this->codeStore       = $codeStore;
    $this->nameMangler     = $nameMangler;
    $this->lobAsStringFlag = $lobAsString;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * A factory for creating the appropriate object for generating a wrapper method for a stored routine.
   *
   * @param array        $routine     The metadata of the stored routine.
   * @param PhpCodeStore $codeStore   The code store for the generated code.
   * @param NameMangler  $nameMangler The mangler for wrapper and parameter names.
   * @param bool         $lobAsString If set BLOBs and CLOBs are treated as string. Otherwise, BLOBs and CLOBs will be
   *                                  send as long data.
   *
   * @return Wrapper
   */
  public static function createRoutineWrapper($routine, $codeStore, $nameMangler, $lobAsString)
  {
    switch ($routine['designation'])
    {
      case 'bulk':
        $wrapper = new BulkWrapper($routine, $codeStore, $nameMangler, $lobAsString);
        break;

      case 'bulk_insert':
        $wrapper = new BulkInsertWrapper($routine, $codeStore, $nameMangler, $lobAsString);
        break;

      case 'log':
        $wrapper = new LogWrapper($routine, $codeStore, $nameMangler, $lobAsString);
        break;

      case 'map':
        $wrapper = new MapWrapper($routine, $codeStore, $nameMangler, $lobAsString);
        break;

      case 'none':
        $wrapper = new NoneWrapper($routine, $codeStore, $nameMangler, $lobAsString);
        break;

      case 'row0':
        $wrapper = new Row0Wrapper($routine, $codeStore, $nameMangler, $lobAsString);
        break;

      case 'row1':
        $wrapper = new Row1Wrapper($routine, $codeStore, $nameMangler, $lobAsString);
        break;

      case 'rows':
        $wrapper = new RowsWrapper($routine, $codeStore, $nameMangler, $lobAsString);
        break;

      case 'rows_with_key':
        $wrapper = new RowsWithKeyWrapper($routine, $codeStore, $nameMangler, $lobAsString);
        break;

      case 'rows_with_index':
        $wrapper = new RowsWithIndexWrapper($routine, $codeStore, $nameMangler, $lobAsString);
        break;

      case 'singleton0':
        $wrapper = new Singleton0Wrapper($routine, $codeStore, $nameMangler, $lobAsString);
        break;

      case 'singleton1':
        $wrapper = new Singleton1Wrapper($routine, $codeStore, $nameMangler, $lobAsString);
        break;

      case 'function':
        $wrapper = new FunctionWrapper($routine, $codeStore, $nameMangler, $lobAsString);
        break;

      case 'table':
        $wrapper = new TableWrapper($routine, $codeStore, $nameMangler, $lobAsString);
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
        $has_blob |= DataTypeHelper::isBlobParameter($parameter_info['data_type']);
      }
    }

    return $has_blob;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates a complete wrapper method.
   */
  public function writeRoutineFunction()
  {
    if (!$this->lobAsStringFlag && $this->isBlobParameter($this->routine['parameters']))
    {
      $this->writeRoutineFunctionWithLob();
    }
    else
    {
      $this->writeRoutineFunctionWithoutLob();
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates a complete wrapper method for a stored routine with a LOB parameter.
   */
  public function writeRoutineFunctionWithLob()
  {
    $wrapper_args = $this->getWrapperArgs();
    $routine_args = $this->getRoutineArgs();
    $method_name  = $this->nameMangler->getMethodName($this->routine['routine_name']);

    $bindings = '';
    $nulls    = '';
    foreach ($this->routine['parameters'] as $parameter_info)
    {
      $binding = DataTypeHelper::getBindVariableType($parameter_info['data_type'], $this->lobAsStringFlag);
      if ($binding=='b')
      {
        $bindings .= 'b';
        if ($nulls) $nulls .= ',';
        $nulls .= '$null';
      }
    }

    $this->codeStore->appendSeparator();
    $this->generatePhpDoc();
    $this->codeStore->append('public static function '.$method_name.'('.$wrapper_args.')');
    $this->codeStore->append('{');
    $this->codeStore->append('$query = \'CALL '.$this->routine['routine_name'].'('.$routine_args.')\';');
    $this->codeStore->append('$stmt  = self::$mysqli->prepare($query);');
    $this->codeStore->append('if (!$stmt) self::mySqlError(\'mysqli::prepare\');');
    $this->codeStore->append('');
    $this->codeStore->append('$null = null;');
    $this->codeStore->append('$b = $stmt->bind_param(\''.$bindings.'\', '.$nulls.');');
    $this->codeStore->append('if (!$b) self::mySqlError(\'mysqli_stmt::bind_param\');');
    $this->codeStore->append('');
    $this->codeStore->append('self::getMaxAllowedPacket();');
    $this->codeStore->append('');

    $blob_argument_index = 0;
    foreach ($this->routine['parameters'] as $parameter_info)
    {
      if (DataTypeHelper::getBindVariableType($parameter_info['data_type'], $this->lobAsStringFlag)=='b')
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
        $this->codeStore->append('');

        $blob_argument_index++;
      }
    }

    $this->codeStore->append('if (self::$logQueries)');
    $this->codeStore->append('{');
    $this->codeStore->append('$time0 = microtime(true);');
    $this->codeStore->append('');
    $this->codeStore->append('$b = $stmt->execute();');
    $this->codeStore->append('if (!$b) self::mySqlError(\'mysqli_stmt::execute\');');
    $this->codeStore->append('');
    $this->codeStore->append('self::$queryLog[] = [\'query\' => $query,');
    $this->codeStore->append('                     \'time\'  => microtime(true) - $time0];');
    $this->codeStore->append('}');
    $this->codeStore->append('else');
    $this->codeStore->append('{');
    $this->codeStore->append('$b = $stmt->execute();');
    $this->codeStore->append('if (!$b) self::mySqlError(\'mysqli_stmt::execute\');');
    $this->codeStore->append('}');
    $this->codeStore->append('');
    $this->writeRoutineFunctionLobFetchData();
    $this->codeStore->append('$stmt->close();');
    $this->codeStore->append('if (self::$mysqli->more_results()) self::$mysqli->next_result();');
    $this->codeStore->append('');
    $this->writeRoutineFunctionLobReturnData();
    $this->codeStore->append('}');
    $this->codeStore->append('');
  }


  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a wrapper method for a stored routine without LOB parameters.
   */
  public function writeRoutineFunctionWithoutLob()
  {
    $wrapper_args = $this->getWrapperArgs();
    $method_name  = $this->nameMangler->getMethodName($this->routine['routine_name']);

    $this->codeStore->appendSeparator();
    $this->generatePhpDoc();
    $this->codeStore->append('public static function '.$method_name.'('.$wrapper_args.')');
    $this->codeStore->append('{');

    $this->writeResultHandler();
    $this->codeStore->append('}');
    $this->codeStore->append('');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Enhances the metadata of the parameters of the store routine wrapper.
   *
   * @param array[] $parameters The metadata of the parameters. For each parameter the
   *                            following keys must be defined:
   *                            <ul>
   *                            <li> php_name             The name of the parader (including $).
   *                            <li> description          The description of the parameter.
   *                            <li> php_type             The type of the parameter.
   *                            <li> data_type_descriptor The data type of the corresponding parameter of the stored
   *                            routine. Null if there is no corresponding parameter.
   *                            </ul>
   */
  protected function enhancePhpDocParameters(&$parameters)
  {
    // Nothing to do.
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
   * @return string
   */
  protected function getRoutineArgs()
  {
    $ret = '';

    foreach ($this->routine['parameters'] as $parameter_info)
    {
      $mangledName = $this->nameMangler->getParameterName($parameter_info['parameter_name']);

      if ($ret) $ret .= ',';
      $ret .= DataTypeHelper::escapePhpExpression($parameter_info, '$'.$mangledName, $this->lobAsStringFlag);
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns code for the parameters of the wrapper method for the stored routine.
   *
   * @return string
   */
  protected function getWrapperArgs()
  {
    if ($this->routine['designation']=='bulk')
    {
      $ret = '$bulkHandler';
    }
    else
    {
      $ret = '';
    }

    foreach ($this->routine['parameters'] as $i => $parameter_info)
    {
      if ($ret) $ret .= ', ';
      $ret .= '$';
      $ret .= $this->nameMangler->getParameterName($parameter_info['parameter_name']);
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates code for calling the stored routine in the wrapper method.
   *
   * @return void
   */
  abstract protected function writeResultHandler();

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates code for fetching data of a stored routine with one or more LOB parameters.
   *
   * @return void
   */
  abstract protected function writeRoutineFunctionLobFetchData();

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
   */
  private function generatePhpDoc()
  {
    $this->codeStore->append('/**', false);

    // Generate phpdoc with short description of routine wrapper.
    $this->generatePhpDocSortDescription();

    // Generate phpdoc with long description of routine wrapper.
    $this->generatePhpDocLongDescription();

    // Generate phpDoc with parameters and descriptions of parameters.
    $this->generatePhpDocParameters();

    // Generate return parameter doc.
    $this->generatePhpDocBlockReturn();

    $this->codeStore->append(' */', false);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the PHP doc block for the return type of the stored routine wrapper.
   */
  private function generatePhpDocBlockReturn()
  {
    $return = $this->getDocBlockReturnType();
    if ($return!=='')
    {
      $this->codeStore->append(' *', false);
      $this->codeStore->append(' * @return '.$return, false);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the long description of stored routine wrapper.
   */
  private function generatePhpDocLongDescription()
  {
    if ($this->routine['phpdoc']['long_description']!=='')
    {
      $this->codeStore->append(' * '.$this->routine['phpdoc']['long_description'], false);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the doc block for parameters of stored routine wrapper.
   */
  private function generatePhpDocParameters()
  {
    $parameters = [];
    foreach ($this->routine['phpdoc']['parameters'] as $parameter)
    {
      $mangledName = $this->nameMangler->getParameterName($parameter['parameter_name']);

      $parameters[] = ['php_name'             => '$'.$mangledName,
                       'description'          => $parameter['description'],
                       'php_type'             => $parameter['php_type'],
                       'data_type_descriptor' => $parameter['data_type_descriptor']];
    }

    $this->enhancePhpDocParameters($parameters);

    if (!empty($parameters))
    {
      // Compute the max lengths of parameter names and the PHP types of the parameters.
      $max_name_length = 0;
      $max_type_length = 0;
      foreach ($parameters as $parameter)
      {
        $max_name_length = max($max_name_length, strlen($parameter['php_name']));
        $max_type_length = max($max_type_length, strlen($parameter['php_type']));
      }

      $this->codeStore->append(' *', false);

      // Generate phpDoc for the parameters of the wrapper method.
      foreach ($parameters as $parameter)
      {
        $format = sprintf(' * %%-%ds %%-%ds %%-%ds %%s', strlen('@param'), $max_type_length, $max_name_length);

        $lines = explode("\n", $parameter['description']);
        if (!empty($lines))
        {
          $line = array_shift($lines);
          $this->codeStore->append(sprintf($format, '@param', $parameter['php_type'], $parameter['php_name'], $line), false);
          foreach ($lines as $line)
          {
            $this->codeStore->append(sprintf($format, ' ', ' ', ' ', $line), false);
          }
        }
        else
        {
          $this->codeStore->append(sprintf($format, '@param', $parameter['php_type'], $parameter['php_name'], ''), false);
        }

        if ($parameter['data_type_descriptor']!==null)
        {
          $this->codeStore->append(sprintf($format, ' ', ' ', ' ', $parameter['data_type_descriptor']), false);
        }
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the sort description of stored routine wrapper.
   */
  private function generatePhpDocSortDescription()
  {
    if ($this->routine['phpdoc']['sort_description']!=='')
    {
      $this->codeStore->append(' * '.$this->routine['phpdoc']['sort_description'], false);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
