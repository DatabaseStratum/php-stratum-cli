<?php
declare(strict_types=1);

namespace SetBased\Stratum\MySql\Helper\Crud;

/**
 * Generates the code for a stored routine that inserts a row.
 */
class InsertRoutine extends BaseRoutine
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function generateBody(array $params, array $columns): void
  {
    $padding = $this->getMaxColumnLength($columns);

    $this->codeStore->append(sprintf('insert into %s(', $this->tableName));
    $offset = mb_strlen($this->codeStore->getLastLine());

    $first = true;
    foreach ($columns as $column)
    {
      if ($first)
      {
        $this->codeStore->appendToLastLine(sprintf(' %s', $column['column_name']));
      }
      else
      {
        $format = sprintf('%%-%ds %%s', $offset, $padding);
        $this->codeStore->append(sprintf($format, ',', $column['column_name']));
        if ($column===end($columns))
        {
          $this->codeStore->appendToLastLine(' )');
        }
      }

      $first = false;
    }

    $this->codeStore->append('values(');
    $offset = mb_strlen($this->codeStore->getLastLine());

    $first = true;
    foreach ($columns as $column)
    {
      if ($first)
      {
        $this->codeStore->appendToLastLine(sprintf(' p_%s', $column['column_name']));
      }
      else
      {
        $format = sprintf('%%-%ds p_%%-%ds', $offset, $padding);
        $this->codeStore->append(sprintf($format, ',', $column['column_name']));
        if ($column===end($columns))
        {
          $this->codeStore->appendToLastLine(' )');
        }
      }

      $first = false;
    }
    $this->codeStore->append(';');

    if ($this->checkAutoIncrement($columns))
    {
      $this->codeStore->append('');
      $this->codeStore->append('select last_insert_id();');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//--------------------------------------------------------------------------------------------------------------------
