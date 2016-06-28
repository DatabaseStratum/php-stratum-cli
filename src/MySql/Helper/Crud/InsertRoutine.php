<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql\Helper\Crud;

//----------------------------------------------------------------------------------------------------------------------

/**
 * Select routine.
 */
class InsertRoutine extends BaseRoutine
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
    $padding        = $this->getMaxColumnLength($columns);
    $lengthLastLine = 0;
    reset($columns);
    $first   = key($columns);
    $lines[] = sprintf('insert into %s( ', $this->tableName);
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
        $format = sprintf("%%-%ds%%s", $lengthLastLine, $padding);
        $line   = sprintf($format, ',', $column['column_name']);
        if ($column===end($columns))
        {
          $line .= ' ) ';
        }
        $lines[] = $line;
      }
    }

    $lines[] = 'values( ';
    foreach ($columns as $key => $column)
    {
      if ($key===$first)
      {
        $line           = sprintf('p_%s', $column['column_name']);
        $lengthLastLine = strlen($lines[count($lines) - 1]);
        $lines[count($lines) - 1] .= $line;
      }
      else
      {
        $format = sprintf("%%-%ds p_%%-%ds", $lengthLastLine - 1, $padding);
        $line   = sprintf($format, ',', $column['column_name']);
        if ($column===end($columns))
        {
          $line .= ' ) ';
        }
        $lines[] = $line;
      }
    }
    $lines[] = ';';
    if ($this->checkAutoIncrement($columns))
    {
      $lines[] = '';
      $lines[] = 'select last_insert_id();';
    }
  }
  //--------------------------------------------------------------------------------------------------------------------

}
//--------------------------------------------------------------------------------------------------------------------
