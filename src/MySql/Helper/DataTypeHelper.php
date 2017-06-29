<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql\Helper;

use SetBased\Exception\FallenException;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Utility class for deriving information based on a MySQL data type.
 */
class DataTypeHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the corresponding PHP data type of a MySQL column type.
   *
   * @param string[] $dataTypeInfo Metadata of the MySQL data type.
   *
   * @return string
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
   * Returns the widths of a field based on a MySQL data type.
   *
   * @param array $dataTypeInfo Metadata of the column on which the field is based.
   *
   * @return int|null
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
   * Returns PHP code escaping the value of a PHP expression that can be safely used when concatenating a SQL statement.
   *
   * @param array  $dataTypeInfo Metadata of the column on which the field is based.
   * @param string $expression   The PHP expression.
   * @param bool   $lobAsString  A flag indication LOBs must be treated as strings.
   *
   * @return string The generated PHP code.
   */
  public static function escapePhpExpression($dataTypeInfo, $expression, $lobAsString)
  {
    switch ($dataTypeInfo['data_type'])
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
        $ret = "'.self::quoteNum(".$expression.").'";
        break;

      case 'varbinary':
      case 'binary':
      case 'char':
      case 'varchar':
        $ret = "'.self::quoteString(".$expression.").'";
        break;

      case 'time':
      case 'timestamp':
      case 'date':
      case 'datetime':
        $ret = "'.self::quoteString(".$expression.").'";
        break;

      case 'enum':
      case 'set':
        $ret = "'.self::quoteString(".$expression.").'";
        break;

      case 'bit':
        $ret = "'.self::quoteBit(".$expression.").'";
        break;

      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':
      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob':
        $ret = ($lobAsString) ? $ret = "'.self::quoteString(".$expression.").'" : '?';
        break;

      case 'list_of_int':
        $ret = "'.self::quoteListOfInt(".$expression.", '".
          addslashes($dataTypeInfo['delimiter'])."', '".
          addslashes($dataTypeInfo['enclosure'])."', '".
          addslashes($dataTypeInfo['escape'])."').'";
        break;

      default:
        throw new FallenException('data type', $dataTypeInfo['data_type']);
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the type of a bind variable.
   *
   * @see http://php.net/manual/en/mysqli-stmt.bind-param.php
   *
   * @param string $dataType    The MySQL data type.
   * @param bool   $lobAsString A flag indication LOBs must be treated as strings.
   *
   * @return string
   */
  public static function getBindVariableType($dataType, $lobAsString)
  {
    $ret = '';
    switch ($dataType)
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
        $ret .= ($lobAsString) ? 's' : 'b';
        break;

      case 'list_of_int':
        $ret = 's';
        break;

      default:
        throw new FallenException('parameter type', $dataType);
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns true if one if a MySQL column type is a BLOB or a CLOB.
   *
   * @param string $dataType Metadata of the MySQL data type.
   *
   * @return bool
   */
  public static function isBlobParameter($dataType)
  {
    switch ($dataType)
    {
      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':
      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob':
        $isBlob = true;
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
        $isBlob = false;
        break;

      default:
        throw new FallenException('data type', $dataType);
    }

    return $isBlob;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
