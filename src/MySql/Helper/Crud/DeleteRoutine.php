<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql\Helper\Crud;

//----------------------------------------------------------------------------------------------------------------------

/**
 * Select routine.
 */
class DeleteRoutine extends BaseRoutine
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate body part.
   *
   * @param array[]  $columns Columns from table.
   * @param array[]  $params  Params for where block.
   * @param string[] $lines   Stored procedure code lines.
   */
  protected function bodyPart($params, $columns, &$lines)
  {
    $lines[]       = sprintf('delete from %s', $this->tableName);
    $uniqueColumns = $this->checkUniqueKeys($columns);
    $limit         = false;
    if (!isset($uniqueColumns))
    {
      $limit = true;
    }

    reset($params);
    $first   = key($params);
    $lines[] = 'where';
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
    if ($limit)
    {
      $lines[] = 'limit 0,1';
    }
  }
  //--------------------------------------------------------------------------------------------------------------------

}

//----------------------------------------------------------------------------------------------------------------------