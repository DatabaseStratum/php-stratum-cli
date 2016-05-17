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
namespace SetBased\Stratum\MySql\Helper;


//----------------------------------------------------------------------------------------------------------------------
use SetBased\Exception\FallenException;
use SetBased\Exception\RuntimeException;
use SetBased\Stratum\Helper\PhpCodeStore;
use SetBased\Stratum\MySql\Wrapper\BulkInsertWrapper;
use SetBased\Stratum\MySql\Wrapper\BulkWrapper;
use SetBased\Stratum\MySql\Wrapper\FunctionsWrapper;
use SetBased\Stratum\MySql\Wrapper\LogWrapper;
use SetBased\Stratum\MySql\Wrapper\NoneWrapper;
use SetBased\Stratum\MySql\Wrapper\Row0Wrapper;
use SetBased\Stratum\MySql\Wrapper\Row1Wrapper;
use SetBased\Stratum\MySql\Wrapper\RowsWithIndexWrapper;
use SetBased\Stratum\MySql\Wrapper\RowsWithKeyWrapper;
use SetBased\Stratum\MySql\Wrapper\RowsWrapper;
use SetBased\Stratum\MySql\Wrapper\Singleton0Wrapper;
use SetBased\Stratum\MySql\Wrapper\Singleton1Wrapper;
use SetBased\Stratum\MySql\Wrapper\TableWrapper;
use SetBased\Stratum\MySql\Wrapper\Wrapper;
use SetBased\Stratum\NameMangler\NameMangler;

/**
 * Helper class for columns.
 */
class ColumnInfo
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the widths of a field based on a column.
   *
   * @param array $column The column of which the field is based.
   *
   * @return int|null
   */
  public static function deriveFieldLength($column)
  {
    $ret = null;
    switch ($column['data_type'])
    {
      case 'tinyint':
      case 'smallint':
      case 'mediumint':
      case 'int':
      case 'bigint':

      case 'decimal':
      case 'float':
      case 'double':
        $ret = $column['numeric_precision'];
        break;

      case 'char':
      case 'varchar':
      case 'binary':
      case 'varbinary':

      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':
      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob':
      case 'bit':
        $ret = $column['character_maximum_length'];
        break;

      case 'timestamp':
        $ret = 16;
        break;

      case 'year':
        $ret = 4;
        break;

      case 'time':
        $ret = 8;
        break;

      case 'date':
        $ret = 10;
        break;

      case 'datetime':
        $ret = 16;
        break;

      case 'enum':
      case 'set':
        // Nothing to do. We don't assign a width to column with type enum and set.
        break;

      default:
        throw new FallenException('column type', $column['data_type']);
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Converts MySQL data type to the PHP data type.
   *
   * @param string[] $parameterInfo
   *
   * @return string
   * @throws \Exception
   */
  public static function columnTypeToPhpType($parameterInfo) //todo move to MySql/Helper/ColumnInfo
  {
    switch ($parameterInfo['data_type'])
    {
      case 'tinyint':
      case 'smallint':
      case 'mediumint':
      case 'int':
      case 'bigint':

      case 'year':

      case 'bit':
        $php_type = 'int';
        break;

      case 'decimal':
        $php_type = ($parameterInfo['numeric_scale']=='0') ? 'int' : 'float';
        break;

      case 'float':
      case 'double':
        $php_type = 'float';
        break;

      case 'varbinary':
      case 'binary':

      case 'char':
      case 'varchar':

      case 'time':
      case 'timestamp':

      case 'date':
      case 'datetime':

      case 'enum':
      case 'set':

      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':

      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob':
        $php_type = 'string';
        break;

      case 'list_of_int':
        $php_type = 'string|int[]';
        break;

      default:
        throw new FallenException('column type', $parameterInfo['data_type']);
    }

    return $php_type;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates code for escaping data.
   *
   * @param string $valueType       The column type.
   * @param string $fieldExpression The expression of the field in the PHP array, e.g. $row['first_name'].
   *
   * @return string The generated PHP code.
   */
  public static function writeEscapesValue($valueType, $fieldExpression) //todo move to MySql/Helper/ColumnInfo
  {
    switch ($valueType)
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
        $ret = '\'.self::quoteNum('.$fieldExpression.').\'';
        break;

      case 'varbinary':
      case 'binary':

      case 'char':
      case 'varchar':
        $ret = '\'.self::quoteString('.$fieldExpression.').\'';
        break;

      case 'time':
      case 'timestamp':

      case 'date':
      case 'datetime':
        $ret = '\'.self::quoteString('.$fieldExpression.').\'';
        break;

      case 'enum':
      case 'set':
        $ret = '\'.self::quoteString('.$fieldExpression.').\'';
        break;

      case 'bit':
        $ret = '\'.self::quoteBit('.$fieldExpression.').\'';
        break;

      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':

      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob':
        throw new RuntimeException('LOBs are not possible in temporary tables');

      default:
        throw new FallenException('column type', $valueType);
    }

    return $ret;
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
        $wrapper = new BulkWrapper($codeStore, $nameMangler, $lobAsString);
        break;

      case 'bulk_insert':
        $wrapper = new BulkInsertWrapper($codeStore, $nameMangler, $lobAsString);
        break;

      case 'log':
        $wrapper = new LogWrapper($codeStore, $nameMangler, $lobAsString);
        break;

      case 'none':
        $wrapper = new NoneWrapper($codeStore, $nameMangler, $lobAsString);
        break;

      case 'row0':
        $wrapper = new Row0Wrapper($codeStore, $nameMangler, $lobAsString);
        break;

      case 'row1':
        $wrapper = new Row1Wrapper($codeStore, $nameMangler, $lobAsString);
        break;

      case 'rows':
        $wrapper = new RowsWrapper($codeStore, $nameMangler, $lobAsString);
        break;

      case 'rows_with_key':
        $wrapper = new RowsWithKeyWrapper($codeStore, $nameMangler, $lobAsString);
        break;

      case 'rows_with_index':
        $wrapper = new RowsWithIndexWrapper($codeStore, $nameMangler, $lobAsString);
        break;

      case 'singleton0':
        $wrapper = new Singleton0Wrapper($codeStore, $nameMangler, $lobAsString);
        break;

      case 'singleton1':
        $wrapper = new Singleton1Wrapper($codeStore, $nameMangler, $lobAsString);
        break;

      case 'function':
        $wrapper = new FunctionsWrapper($codeStore, $nameMangler, $lobAsString);
        break;

      case 'table':
        $wrapper = new TableWrapper($codeStore, $nameMangler, $lobAsString);
        break;

      default:
        throw new FallenException('routine type', $routine['designation']);
    }

    return $wrapper;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
