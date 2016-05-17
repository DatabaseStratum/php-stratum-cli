<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql\Helper;

use SetBased\Exception\FallenException;
use SetBased\Exception\RuntimeException;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Helper class for deriving information based on a MySQL data type.
 */
class DataTypeHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the widths of a field based on a MySQL data type.
   *
   * @param array $dataTypeInfo Metadata of the column on which the field is based.
   *
   * @return int|null
   * @throws FallenException
   */
  public static function deriveFieldLength($dataTypeInfo)
  {
    switch ($dataTypeInfo['data_type'])
    {
      case 'tinyint':
      case 'smallint':
      case 'mediumint':
      case 'int':
      case 'bigint':
      case 'decimal':
      case 'float':
      case 'double':
        $ret = $dataTypeInfo['numeric_precision'];
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
        $ret = $dataTypeInfo['character_maximum_length'];
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
        // We don't assign a width to column with type enum and set.
        $ret = null;
        break;

      default:
        throw new FallenException('data type', $dataTypeInfo['data_type']);
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Converts MySQL data type to the PHP data type.
   *
   * @param string[] $dataTypeInfo Metadata of the MySQL data type.
   *
   * @return string
   * @throws FallenException
   */
  public static function columnTypeToPhpType($dataTypeInfo)
  {
    switch ($dataTypeInfo['data_type'])
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
        $php_type = ($dataTypeInfo['numeric_scale']=='0') ? 'int' : 'float';
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
        throw new FallenException('data type', $dataTypeInfo['data_type']);
    }

    return $php_type;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates code for escaping expressions in SQL (calling stored routines).
   *
   * @param string $dataType        The column type.
   * @param string $fieldExpression The expression of the field in the PHP array, e.g. $row['first_name'].
   *
   * @return string The generated PHP code.
   */
  public static function writeEscapesValue($dataType, $fieldExpression)
  {
    switch ($dataType)
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
        $ret = "'.self::quoteNum(".$fieldExpression.").'";
        break;

      case 'varbinary':
      case 'binary':
      case 'char':
      case 'varchar':
        $ret = "'.self::quoteString(".$fieldExpression.").'";
        break;

      case 'time':
      case 'timestamp':
      case 'date':
      case 'datetime':
        $ret = "'.self::quoteString(".$fieldExpression.").'";
        break;

      case 'enum':
      case 'set':
        $ret = "'.self::quoteString(".$fieldExpression.").'";
        break;

      case 'bit':
        $ret = "'.self::quoteBit(".$fieldExpression.").'";
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
        throw new FallenException('data type', $dataType);
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
