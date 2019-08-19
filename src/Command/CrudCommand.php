<?php
declare(strict_types=1);

namespace SetBased\Stratum\Command;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Base class for commands which needs to connect to a MySQL instance.
 */
class CrudCommand extends BaseCommand
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Helper object for questions.
   *
   * @var SymfonyQuestionHelper
   */
  private $helper;

  /**
   * The input object..
   *
   * @var InputInterface
   */
  private $input;

  /**
   * The output object.
   *
   * @var OutputInterface
   */
  private $output;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @var \SetBased\Stratum\CrudWorker|null
   */
  private $worker;

  /**
   * @inheritdoc
   */
  protected function configure()
  {
    $this->setName('crud')
         ->setDescription('This is an interactive command for generating stored routines for CRUD operations')
         ->addArgument('config file', InputArgument::REQUIRED, 'The audit configuration file')
         ->addOption('tables', 't', InputOption::VALUE_NONE, 'Show all tables');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->input  = $input;
    $this->output = $output;
    $this->helper = new QuestionHelper();

    $this->createStyle($input, $output);
    $this->readConfigFile($input);

    $factory      = $this->createBackendFactory();
    $this->worker = $factory->createCrudWorker($this->config, $this->io);

    if ($this->worker===null)
    {
      $this->io->error('CRUD command is not implemented by the backend');

      return -1;
    }

    $tables     = $this->worker->tables();
    $operations = $this->worker->operations();

    $table = $this->askTableName($tables);
    $dir   = $this->askDirectory($this->config->manString('crud.source_directory', 'lib/psql'));
    $this->generateRoutines($table, $operations, $dir);

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Asks the user where the stored routines must stored.
   *
   * @param string $default The default directory.
   *
   * @return string
   */
  private function askDirectory(string $default): string
  {
    while (true)
    {
      $text     = sprintf('Enter source directory for stored routines [<fso>%s</fso>]: ',
                          OutputFormatter::escape($default));
      $question = new Question($text, $default);
      $dir      = $this->helper->ask($this->input, $this->output, $question);

      if (is_dir($dir))
      {
        return $dir;
      }

      $this->io->logNote("Path '%s' is not a directory", $dir);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Asks the user for which table CRUD stored routines must be created.
   *
   * @param array $tables The names of all tables in the database.
   *
   * @return string
   */
  private function askTableName(array $tables): string
  {
    $this->showTables($tables);

    while (true)
    {
      $question = new Question('Please enter <note>table name</note>: ');
      $table    = $this->helper->ask($this->input, $this->output, $question);

      if (in_array($table, $tables))
      {
        return $table;
      }

      $this->io->logNote("Table '%s' not exist.", $table);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Asking function for create or not stored procedure.
   *
   * @param string $table     The name of the table for which a stored routine must be generated.
   * @param string $operation The operation for which a stored routines must be generated.
   * @param string $dir       The target directory for the generated stored routines.
   */
  private function generateRoutine(string $table, string $operation, string $dir): void
  {
    $question = sprintf('Create stored routine for <dbo>%s</dbo>? [Yes]: ', $operation);
    $question = new ConfirmationQuestion($question, true);
    if ($this->helper->ask($this->input, $this->output, $question))
    {
      $defaultRoutineName = strtolower(sprintf('%s_%s', $table, $operation));

      $question    = new Question(sprintf('Please enter routine name [<dbo>%s</dbo>]: ',
                                          OutputFormatter::escape($defaultRoutineName)),
                                  $defaultRoutineName);
      $routineName = $this->helper->ask($this->input, $this->output, $question);
      $filename    = sprintf('%s/%s.psql', $dir, $routineName);

      if (file_exists($filename))
      {
        $this->io->writeln(sprintf('File <fso>%s</fso> already exists', $filename));
        $question = 'Overwrite it? [No]: ';
        $question = new ConfirmationQuestion($question, false);
        $write    = $this->helper->ask($this->input, $this->output, $question);
      }
      else
      {
        $write = true;
      }

      if ($write)
      {
        $code = $this->worker->generateRoutine($table, $operation, $routineName);
        $this->writeTwoPhases($filename, $code);
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Main function for asking.
   *
   * @param string   $table      The name of the table for which stored routines must be generated.
   * @param string[] $operations The operations for which  stored routines must be generated.
   * @param string   $dir        The target directory for the generated stored routines.
   */
  private function generateRoutines(string $table, array $operations, string $dir): void
  {
    foreach ($operations as $operation)
    {
      $this->generateRoutine($table, $operation, $dir);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Check option -t for show all tables.
   *
   * @param array $tables The names of all tables in the database.
   */
  private function showTables(array $tables): void
  {
    if ($this->input->getOption('tables'))
    {
      $tableData = array_chunk($tables, 3);
      $table     = new Table($this->output);
      $table->setRows($tableData);
      $table->render();
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
