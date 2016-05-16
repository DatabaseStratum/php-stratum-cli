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
use SetBased\Exception\LogicException;
use SetBased\Exception\RuntimeException;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating a wrapper method for a stored procedure that prepares a table to be used with a bulk SQL
 * statement.
 */
class BulkInsertWrapper extends Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @return string
   */
  protected function getDocBlockReturnType()
  {
    return '';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function getWrapperArgs($routine)
  {
    return '$rows';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeResultHandler($routine)
  {
    // Validate number of column names and number of column types are equal.
    $n1 = count($routine['columns']);
    $n2 = count($routine['column_types']);
    if ($n1!=$n2)
    {
      throw new LogicException("Number of fields %d and number of columns %d don't match.", $n1, $n2);
    }

    $routine_args = $this->getRoutineArgs($routine);
    $this->codeStore->append('self::query(\'CALL '.$routine['routine_name'].'('.$routine_args.')\');');

    $columns = '';
    $fields  = '';
    foreach ($routine['columns'] as $i => $field)
    {
      if ($field!='_')
      {
        if ($columns) $columns .= ',';
        $columns .= '`'.$routine['fields'][$i].'`';

        if ($fields) $fields .= ',';
        $fields .= $this->writeEscapesValue($routine['column_types'][$i], '$row[\''.$field.'\']');
      }
    }

    $this->codeStore->append('if (is_array($rows) && !empty($rows))');
    $this->codeStore->append('{');
    $this->codeStore->append('$sql = "INSERT INTO `'.$routine['table_name'].'`('.$columns.')";');
    $this->codeStore->append('$first = true;');
    $this->codeStore->append('foreach($rows as $row)');
    $this->codeStore->append('{');

    $this->codeStore->append('if ($first) $sql .=\' values('.$fields.')\';');
    $this->codeStore->append('else        $sql .=\',      ('.$fields.')\';');

    $this->codeStore->append('$first = false;');
    $this->codeStore->append('}');
    $this->codeStore->append('self::query($sql);');
    $this->codeStore->append('}');
  }


  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobFetchData($routine)
  {
    // Nothing to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function writeRoutineFunctionLobReturnData()
  {
    // Nothing to do.
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
  private function writeEscapesValue($valueType, $fieldExpression)
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
}

//----------------------------------------------------------------------------------------------------------------------
