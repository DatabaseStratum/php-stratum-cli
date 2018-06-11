<?php

namespace SetBased\Stratum\MySql\Helper\Crud;

use SetBased\Exception\FallenException;
use SetBased\Helper\CodeStore\MySqlCompoundSyntaxCodeStore;
use SetBased\Stratum\MySql\MetadataDataLayer;
use SetBased\Stratum\MySql\StaticDataLayer;
use SetBased\Stratum\Style\StratumStyle;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Abstract parent class for classes for generating CRUD stored routines.
 */
abstract class BaseRoutine
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The code of the generated stored routine.
   *
   * @var MySqlCompoundSyntaxCodeStore
   */
  protected $codeStore;

  /**
   * The data schema.
   *
   * @var string
   */
  protected $dataSchema;

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
  protected $parameters;

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

  /**
   * Helper for questions.
   *
   * @var SymfonyQuestionHelper
   */
  private $helper;

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

    $this->codeStore = new MySqlCompoundSyntaxCodeStore();

    $tableColumns = MetadataDataLayer::getTableColumns($this->dataSchema, $this->tableName);
    $params       = [];
    if ($spType!=='INSERT')
    {
      $params = $this->checkUniqueKeys($tableColumns, $this->spType);
    }

    if (!isset($params))
    {
      $params = $tableColumns;
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

    $this->modifiesPart($this->checkAutoIncrement($tableColumns));
    $this->codeStore->append('begin');

    $this->generateBody($params, $tableColumns);

    $this->codeStore->append('end');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the generated code of the stored routine.
   *
   * @return string
   */
  public function getCode()
  {
    return $this->codeStore->getCode();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Checks if the table has a auto_increment column.
   *
   * @param array[] $columns Columns from table.
   *
   * @return bool
   */
  protected function checkAutoIncrement($columns)
  {
    foreach ($columns as $column)
    {
      if ($column['extra']=='auto_increment')
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
   * @return array|null
   */
  protected function checkUniqueKeys($columns, $spType = null)
  {
    $primaryKeys = MetadataDataLayer::getTablePrimaryKeys($this->dataSchema, $this->tableName);
    $uniqueKeys  = MetadataDataLayer::getTableUniqueKeys($this->dataSchema, $this->tableName);

    $resultColumns = [];

    if (!isset($spType))
    {
      if (empty($uniqueKeys) && empty($primaryKeys))
      {
        return null;
      }
      else
      {
        return $columns;
      }
    }

    if (!empty($primaryKeys))
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
      if (!empty($uniqueKeys))
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

          $question   = new Question(sprintf('What unique keys use in statement?(%s): ',
                                             $uniqueKeys[$first]['Key_name']),
                                     $uniqueKeys[$first]['Key_name']);
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
   * Generates the body of the stored routine.
   *
   * @param array[] $columns Columns from table.
   * @param array[] $params  Params for where block.
   *
   * @return void
   */
  abstract protected function generateBody($params, $columns);

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the doc block for the stored routine.
   *
   * @param array[] $columns Columns from table.
   */
  protected function generateDocBlock($columns)
  {
    $this->codeStore->append('/**');
    $this->codeStore->append(' * @todo describe routine', false);
    $this->codeStore->append(' * ', false);

    $padding = $this->getMaxColumnLength($columns);
    $format  = sprintf(' * @param p_%%-%ds @todo describe parameter', $padding);
    foreach ($columns as $column)
    {
      $this->codeStore->append(sprintf($format, $column['column_name']), false);
    }

    $this->codeStore->append(' */', false);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the function name and parameters of the stored routine.
   *
   * @param array[] $columns Columns from table.
   */
  protected function generateMainPart($columns)
  {
    $this->codeStore->append(sprintf('create procedure %s(', $this->spName));

    $padding = $this->getMaxColumnLength($columns);
    $offset  = mb_strlen($this->codeStore->getLastLine());

    $first = true;
    foreach ($columns as $column)
    {
      if ($first)
      {
        $format = sprintf(' in p_%%-%ds @%%s.%%s%%s@', $padding);
        $this->codeStore->appendToLastLine(strtolower(sprintf($format,
                                                              $column['column_name'],
                                                              $this->tableName,
                                                              $column['column_name'],
                                                              '%type')));
      }
      else
      {
        $format = sprintf('%%%ds p_%%-%ds @%%s.%%s%%s@', $offset + 3, $padding);
        $this->codeStore->append(strtolower(sprintf($format,
                                                    'in',
                                                    $column['column_name'],
                                                    $this->tableName,
                                                    $column['column_name'],
                                                    '%type')), false);
      }

      if ($column!=end($columns))
      {
        $this->codeStore->appendToLastLine(',');
      }
      else
      {
        $this->codeStore->appendToLastLine(' )');
      }

      $first = false;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the length the longest column name of a table.
   *
   * @param array[] $columns The metadata of the columns of the table.
   *
   * @return int
   */
  protected function getMaxColumnLength($columns)
  {
    $length = 0;
    foreach ($columns as $column)
    {
      $length = max(mb_strlen($column['column_name']), $length);
    }

    return $length;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates the modifies/reads sql data and designation type comment of the stored routine.
   *
   * @param bool $flag Set or no type.
   */
  protected function modifiesPart($flag)
  {
    if ($this->spType!=='SELECT')
    {
      $this->codeStore->append('modifies sql data');
    }
    else
    {
      $this->codeStore->append('reads sql data');
    }

    switch ($this->spType)
    {
      case 'UPDATE':
      case 'DELETE':
        $this->codeStore->append('-- type: none');
        break;

      case 'SELECT':
        $this->codeStore->append('-- type: row1');
        break;

      case 'INSERT':
        if ($flag)
        {
          $this->codeStore->append('-- type: singleton1');
        }
        else
        {
          $this->codeStore->append('-- type: none');
        }
        break;

      default:
        throw new FallenException("Unknown stored routine type '%s'", $this->spType);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
