<?php
declare(strict_types=1);

namespace SetBased\Stratum\MySql\Helper\Crud;

/**
 * Generates the code for a stored routine that deletes a row.
 */
class DeleteRoutine extends BaseRoutine
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate body part.
   *
   * @param array[] $columns Columns from table.
   * @param array[] $params  Params for where block.
   */
  protected function generateBody(array $params, array $columns): void
  {
    $uniqueColumns = $this->checkUniqueKeys($columns);
    $limit         = ($uniqueColumns==null);

    $this->codeStore->append(sprintf('delete from %s', $this->tableName));
    $this->codeStore->append('where');

    $first = true;
    foreach ($params as $column)
    {
      if ($first)
      {
        $format = sprintf("%%%ds %%s = p_%%s", 1);
        $this->codeStore->appendToLastLine(sprintf($format, '', $column['column_name'], $column['column_name']));
      }
      else
      {
        $format = sprintf("and%%%ds %%s = p_%%s", 3);
        $this->codeStore->append(sprintf($format, '', $column['column_name'], $column['column_name']));
      }

      $first = false;
    }

    if ($limit)
    {
      $this->codeStore->append('limit 0,1');
    }

    $this->codeStore->append(';');
  }

  //--------------------------------------------------------------------------------------------------------------------

}
//----------------------------------------------------------------------------------------------------------------------
