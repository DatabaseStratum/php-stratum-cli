<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql\Helper\Crud;

use SetBased\Stratum\Helper\CompoundSyntaxStore;
use SetBased\Stratum\MySql\DataLayer;
use SetBased\Stratum\MySql\StaticDataLayer;
use SetBased\Stratum\Style\StratumStyle;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Base class for routine.
 */
class BaseRoutine
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The data schema.
   *
   * @var string
   */
  protected $dataSchema;

  /**
   * Helper for questions.
   */
  protected $helper;

  /**
   * InputInterface.
   *
   * @var InputInterface
   */
  protected $input;

  /**
   * The output decorator
   *
   * @var StratumStyle
   */
  protected $io;

  /**
   * OutputInterface.
   *
   * @var OutputInterface
   */
  protected $output;

  /**
   * Metadata about the stored routine parameters.
   *
   * @var array[]
   */
  protected $paramaters;

  /**
   * The stored procedure name
   *
   * @var string
   */
  protected $spName;

  /**
   * The stored procedure type
   *
   * @var string
   */
  protected $spType;

  /**
   * Stored procedure code.
   *
   * @var CompoundSyntaxStore
   */
  protected $storedProcedureCode;

  /**
   * Metadata about the columns of the table.
   *
   * @var array[]
   */
  protected $tableColumns;

  /**
   * The table name.
   *
   * @var string
   */
  protected $tableName;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param InputInterface  $input
   * @param OutputInterface $output
   * @param                 $helper     Helper for questions.
   * @param string          $spType     Stored procedure type {insert|update|delete|select}.
   * @param string          $spName     Stored procedure name.
   * @param string          $tableName  The table name.
   * @param string          $dataSchema Data schema.
   */
  public function __construct($input, $output, $helper, $spType, $spName, $tableName, $dataSchema)
  {
    $this->io = new StratumStyle($input, $output);

    $this->input      = $input;
    $this->output     = $output;
    $this->helper     = $helper;
    $this->dataSchema = $dataSchema;
    $this->spName     = $spName;
    $this->spType     = $spType;
    $this->tableName  = $tableName;

    $this->storedProcedureCode = new CompoundSyntaxStore();

    $tableColumns = DataLayer::getTableColumns($this->dataSchema, $this->tableName);
    $params       = [];
    if ($spType!=='INSERT')
    {
      $params = $this->checkUniqueKeys($tableColumns, $this->spType);
    }

    switch ($spType)
    {
      case 'INSERT':
      case 'UPDATE':
        $this->generateDocBlock($tableColumns);
        $this->generateMainPart($tableColumns);
        break;
      default:
        $this->generateDocBlock($params);
        $this->generateMainPart($params);
    }

    $this->generateBodyPart($params, $tableColumns);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Get code.
   *
   * @return string
   */
  public function getCode()
  {
    return $this->storedProcedureCode->getCode();
  }

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
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Check if table have auto_increment column.
   *
   * @param array[] $columns Columns from table.
   *
   * @return bool
   */
  protected function checkAutoIncrement($columns)
  {
    foreach ($columns as $column)
    {
      if (isset($column['extra']))
      {
        return true;
      }
    }

    return false;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate main part with name and params.
   *
   * @param array[]     $columns Columns from table.
   * @param string|null $spType  Stored procedure type {insert|update|delete|select}.
   *
   * @return array[]|null
   */
  protected function checkUniqueKeys($columns, $spType = null)
  {
    $primaryKeys = DataLayer::getTablePrimaryKeys($this->dataSchema, $this->tableName);
    $uniqueKeys  = DataLayer::getTableUniqueKeys($this->dataSchema, $this->tableName);

    $resultColumns = [];

    if (!isset($spType))
    {
      if (count($uniqueKeys)<=0 && count($primaryKeys)<=0)
      {
        return null;
      }
      else
      {
        return $columns;
      }
    }

    if (count($primaryKeys)>0)
    {
      foreach ($columns as $column)
      {
        $check = StaticDataLayer::searchInRowSet('Column_name', $column['column_name'], $primaryKeys);
        if (isset($check))
        {
          $resultColumns[] = $column;
        }
      }

      return $resultColumns;
    }
    else
    {
      if (count($uniqueKeys)>0)
      {
        reset($uniqueKeys);
        $first = key($uniqueKeys);
        if (count($uniqueKeys)>1)
        {
          $this->io->writeln(sprintf('Table <dbo>%s</dbo> has more than one unique key.', $this->tableName));

          $array = [];
          foreach ($uniqueKeys as $column)
          {
            if (isset($array[$column['Key_name']]))
            {
              $array[$column['Key_name']] .= ',';
              $array[$column['Key_name']] .= $column['Column_name'];
            }
            else
            {
              $array[$column['Key_name']] = $column['Column_name'];
            }
          }

          $tableArray = [];
          foreach ($array as $key => $column)
          {
            $tableArray[] = [$key, $column];
          }

          $table = new Table($this->output);
          $table->setHeaders(['Name', 'Keys']);
          $table->setRows($tableArray);
          $table->render();

          $question   = new Question(sprintf('What unique keys use in statement?(%s): ', $uniqueKeys[$first]['Key_name']), $uniqueKeys[$first]['Key_name']);
          $uniqueKeys = $this->helper->ask($this->input, $this->output, $question);
          $uniqueKeys = explode(',', $array[$uniqueKeys]);
          foreach ($uniqueKeys as $column)
          {
            $resultColumns[] = ['column_name' => $column];
          }

          return $resultColumns;
        }
        else
        {
          foreach ($uniqueKeys as $column)
          {
            $resultColumns[] = ['column_name' => $column['Column_name']];
          }

          return $resultColumns;
        }
      }
      else
      {
        return null;
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate comments for stored procedure.
   *
   * @param array[] $columns Columns from table.
   */
  protected function generateDocBlock($columns)
  {
    $lines   = [];
    $lines[] = '/**';
    $lines[] = ' * @todo describe routine';
    $lines[] = ' * ';
    $padding = $this->getMaxColumnLength($columns);
    $format  = sprintf(" * @param p_%%-%ds @todo describe parameter", $padding);
    foreach ($columns as $column)
    {
      $lines[] = sprintf($format, $column['column_name']);
    }
    $lines[] = ' */';

    $this->storedProcedureCode->append($lines, false);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate main part with name and params.
   *
   * @param array[] $columns Columns from table.
   */
  protected function generateMainPart($columns)
  {
    $lines          = [];
    $lines[]        = sprintf('create procedure %s (', $this->spName);
    $lengthLastLine = 0;

    $padding = $this->getMaxColumnLength($columns);
    reset($columns);
    $first = key($columns);
    foreach ($columns as $key => $column)
    {
      if ($key===$first)
      {
        $format         = sprintf(" in p_%%-%ds @%%s.%%s%%s@", $padding);
        $line           = strtolower(sprintf($format, $column['column_name'], $this->tableName, $column['column_name'], '%type'));
        $lengthLastLine = strlen($lines[count($lines) - 1]);
        if ($column!=end($columns))
        {
          $line .= ',';
        }
        else
        {
          $line .= ' )';
        }
        $lines[count($lines) - 1] .= $line;
      }
      else
      {
        $format = sprintf("%%%ds p_%%-%ds @%%s.%%s%%s@", $lengthLastLine + 3, $padding);
        $line   = strtolower(sprintf($format, 'in', $column['column_name'], $this->tableName, $column['column_name'], '%type'));
        if ($column!=end($columns))
        {
          $line .= ',';
        }
        else
        {
          $line .= ' )';
        }
        $lines[] = $line;
      }
    }

    $this->storedProcedureCode->append($lines, false);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Return max column length.
   *
   * @param array[] $columns Columns from table.
   *
   * @return int
   */
  protected function getMaxColumnLength($columns)
  {
    $length = 0;
    foreach ($columns as $column)
    {
      $length = max(strlen($column['column_name']), $length);
    }

    return $length;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Add modifies sql data part.
   *
   * @param bool     $flag  Set or no type.
   * @param string[] $lines Stored procedure code lines.
   */
  protected function modifiesPart($flag, &$lines)
  {
    if ($this->spType!=='SELECT')
    {
      $lines[] = 'modifies sql data';
    }
    else
    {
      $lines[] = 'reads sql data';
    }
    switch ($this->spType)
    {
      case 'UPDATE':
      case 'DELETE':
        $lines[] = '-- type: none';
        break;
      case 'SELECT':
        $lines[] = '-- type: row1';
        break;
      case 'INSERT':
        if ($flag)
        {
          $lines[] = '-- type: singleton1';
        }
        else
        {
          $lines[] = '-- type: none';
        }
        break;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate body of stored procedure.
   *
   * @param array[] $columns Columns from table.
   * @param array[] $params  Params for where block.
   */
  private function generateBodyPart($params, $columns)
  {
    $lines = [];
    $this->modifiesPart($this->checkAutoIncrement($columns), $lines);
    $lines[] = 'begin';

    $this->bodyPart($params, $columns, $lines);

    $lines[] = 'end';

    $this->storedProcedureCode->append($lines, false);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
