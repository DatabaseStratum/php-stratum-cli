<?php
declare(strict_types=1);

namespace SetBased\Stratum\MySql\Helper\Crud;

/**
 * Generates the code for a stored routine that selects a row.
 */
class SelectRoutine extends BaseRoutine
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function generateBody(array $params, array $columns): void
  {
    $this->codeStore->append('select');
    $offset = mb_strlen($this->codeStore->getLastLine());

    $first = true;
    foreach ($columns as $key => $column)
    {
      if ($first)
      {
        $this->codeStore->appendToLastLine(sprintf(' %s', $column['column_name']));
      }
      else
      {
        $format = sprintf("%%-%ds %%s", $offset);
        $this->codeStore->append(sprintf($format, ',', $column['column_name']));
      }

      $first = false;
    }

    $this->codeStore->append(sprintf('from %s', $this->tableName));
    $this->codeStore->append('where');

    $first = true;
    foreach ($params as $key => $column)
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

    $this->codeStore->append(';');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
