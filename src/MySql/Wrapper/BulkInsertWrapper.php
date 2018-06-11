<?php

namespace SetBased\Stratum\MySql\Wrapper;

use SetBased\Exception\LogicException;
use SetBased\Stratum\MySql\Helper\DataTypeHelper;

/**
 * Class for generating a wrapper method for a stored procedure that prepares a table to be used with a bulk SQL
 * statement.
 */
class BulkInsertWrapper extends Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function enhancePhpDocParameters(&$parameters)
  {
    $parameter = ['php_name'             => '$rows',
                  'description'          => 'The rows that must inserted.',
                  'php_type'             => 'array[]',
                  'data_type_descriptor' => null];

    $parameters = array_merge([$parameter], $parameters);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function getDocBlockReturnType()
  {
    return '';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function getWrapperArgs()
  {
    return '$rows';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeResultHandler()
  {
    // Validate number of column names and number of column types are equal.
    $n1 = count($this->routine['columns']);
    $n2 = count($this->routine['column_types']);
    if ($n1!=$n2)
    {
      throw new LogicException("Number of fields %d and number of columns %d don't match.", $n1, $n2);
    }

    $routine_args = $this->getRoutineArgs();
    $this->codeStore->append('self::query(\'CALL '.$this->routine['routine_name'].'('.$routine_args.')\');');

    $columns = '';
    $fields  = '';
    foreach ($this->routine['columns'] as $i => $field)
    {
      if ($field!='_')
      {
        if ($columns) $columns .= ',';
        $columns .= '`'.$this->routine['fields'][$i].'`';

        if ($fields) $fields .= ',';
        $fields .= DataTypeHelper::escapePhpExpression(['data_type' => $this->routine['column_types'][$i]],
                                                       '$row[\''.$field.'\']',
                                                       true);
      }
    }

    $this->codeStore->append('if (is_array($rows) && !empty($rows))');
    $this->codeStore->append('{');
    $this->codeStore->append('$sql = "INSERT INTO `'.$this->routine['table_name'].'`('.$columns.')";');
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
   * @inheritdoc
   */
  protected function writeRoutineFunctionLobFetchData()
  {
    // Nothing to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeRoutineFunctionLobReturnData()
  {
    // Nothing to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
