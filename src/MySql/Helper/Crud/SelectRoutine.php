<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql\Helper\Crud;

//----------------------------------------------------------------------------------------------------------------------

/**
 * Select routine.
 */
class SelectRoutine extends BaseRoutine
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
    $lengthLastLine = 0;
    reset($columns);
    $first   = key($columns);
    $lines[] = 'select ';
    foreach ($columns as $key => $column)
    {
      if ($key===$first)
      {
        $line           = sprintf('%s', $column['column_name']);
        $lengthLastLine = strlen($lines[count($lines) - 1]);
        $lines[count($lines) - 1] .= $line;
      }
      else
      {
        $format  = sprintf("%%-%ds %%s", $lengthLastLine - 1);
        $line    = sprintf($format, ',', $column['column_name']);
        $lines[] = $line;
      }
    }
    $lines[] = sprintf('from %s', $this->tableName);
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
  }
  //--------------------------------------------------------------------------------------------------------------------

}

//----------------------------------------------------------------------------------------------------------------------
