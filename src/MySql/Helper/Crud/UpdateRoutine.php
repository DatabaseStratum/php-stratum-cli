<?php
declare(strict_types=1);

namespace SetBased\Stratum\MySql\Helper\Crud;

use SetBased\Stratum\MySql\MetadataDataLayer;
use SetBased\Stratum\MySql\StaticDataLayer;

/**
 * Generates the code for a stored routine that updates a row.
 */
class UpdateRoutine extends BaseRoutine
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function generateBody(array $params, array $columns): void
  {
    $set         = [];
    $primaryKeys = MetadataDataLayer::getTablePrimaryKeys($this->dataSchema, $this->tableName);

    foreach ($columns as $column)
    {
      $check = StaticDataLayer::searchInRowSet('Column_name', $column['column_name'], $primaryKeys);
      if (!isset($check))
      {
        $set[] = $column;
      }
    }

    $this->codeStore->append(sprintf('update %s', $this->tableName));
    $this->codeStore->append('set');
    $offset = mb_strlen($this->codeStore->getLastLine());

    $first = true;
    foreach ($set as $column)
    {
      if ($first)
      {
        $format = sprintf("%%%ds %%s = p_%%s", $offset);
        $this->codeStore->appendToLastLine(sprintf($format, '', $column['column_name'], $column['column_name']));
      }
      else
      {
        $format = sprintf("%%-%ds %%s = p_%%s", $offset + 3);
        $this->codeStore->append(sprintf($format, ',', $column['column_name'], $column['column_name']));
      }

      $first = false;
    }

    $this->codeStore->append('where');

    $first = true;
    foreach ($params as $column)
    {
      if ($first)
      {
        $format = sprintf("%%%ds %%s = p_%%s", 1);
        $line   = sprintf($format, '', $column['column_name'], $column['column_name']);
        $this->codeStore->appendToLastLine($line);
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
