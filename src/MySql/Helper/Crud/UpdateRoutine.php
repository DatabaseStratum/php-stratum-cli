<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql\Helper\Crud;

//----------------------------------------------------------------------------------------------------------------------
use SetBased\Stratum\MySql\DataLayer;
use SetBased\Stratum\MySql\StaticDataLayer;

/**
 * Select routine.
 */
class UpdateRoutine extends BaseRoutine
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate body of stored procedure.
   *
   * @param array[]  $columns Columns from table.
   * @param array[]  $params  Params for where block.
   * @param string[] $lines   Stored procedure code lines.
   *
   * @return \string[]
   */
  protected function bodyPart($params, $columns, &$lines)
  {
    $set         = [];
    $primaryKeys = DataLayer::getTablePrimaryKeys($this->dataSchema, $this->tableName);

    $lines[] = sprintf('update %s', $this->tableName);
    foreach ($columns as $column)
    {
      $check = StaticDataLayer::searchInRowSet('Column_name', $column['column_name'], $primaryKeys);
      if (!isset($check))
      {
        $set[] = $column;
      }
    }

    reset($set);
    $first   = key($set);
    $lines[] = 'set';
    foreach ($set as $key => $column)
    {
      if ($key===$first)
      {
        $format = sprintf("%%%ds %%s = p_%%s", 3);
        $line   = sprintf($format, '', $column['column_name'], $column['column_name']);
        if ($column!=end($set))
        {
          $line .= ',';
        }
        $lines[count($lines) - 1] .= $line;
      }
      else
      {
        $format = sprintf("%%%ds %%s = p_%%s", 6);
        $line   = sprintf($format, '', $column['column_name'], $column['column_name']);
        if ($column!=end($set))
        {
          $line .= ',';
        }
        $lines[] = $line;
      }
    }

    $lines[] = 'where';
    reset($params);
    $first = key($params);
    foreach ($params as $key => $column)
    {
      if ($key===$first)
      {
        $format = sprintf("%%%ds %%s = p_%%s", 1);
        $line   = sprintf($format, '', $column['column_name'], $column['column_name']);
        $lines[count($lines) - 1] .= $line;
      }
      else
      {
        $format  = sprintf("and%%%ds %%s = p_%%s", 3);
        $line    = sprintf($format, '', $column['column_name'], $column['column_name']);
        $lines[] = $line;
      }
    }
    $lines[] = ';';
  }
  //--------------------------------------------------------------------------------------------------------------------

}

//----------------------------------------------------------------------------------------------------------------------
