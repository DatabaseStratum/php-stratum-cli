<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Command;

use SetBased\Exception\FallenException;
use SetBased\Stratum\MySql\DataLayer;
use SetBased\Stratum\MySql\Helper\Crud\DeleteRoutine;
use SetBased\Stratum\MySql\Helper\Crud\InsertRoutine;
use SetBased\Stratum\MySql\Helper\Crud\SelectRoutine;
use SetBased\Stratum\MySql\Helper\Crud\UpdateRoutine;
use SetBased\Stratum\MySql\StaticDataLayer;
use SetBased\Stratum\Style\StratumStyle;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Base class for commands which needs to connect to a MySQL instance.
 */
class CrudCommand extends BaseCommand
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The output decorator
   *
   * @var StratumStyle
   */
  protected $io;

  /**
   * Database name.
   *
   * @var string
   */
  private $dataSchema;

  /**
   * Helper for questions.
   *
   * @var SymfonyQuestionHelper
   */
  private $helper;

  /**
   * InputInterface.
   *
   * @var InputInterface
   */
  private $input;

  /**
   * OutputInterface.
   *
   * @var OutputInterface
   */
  private $output;

  /**
   * Source directory.
   *
   * @var string
   */
  private $sourceDirectory;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this->setName('crud')
         ->setDescription('This is an interactive command for generating stored procedures for CRUD operations.')
         ->addArgument('config file', InputArgument::OPTIONAL, 'The audit configuration file')
         ->addOption('tables', 't', InputOption::VALUE_NONE, 'Show all tables');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->io = new StratumStyle($input, $output);

    $this->input  = $input;
    $this->output = $output;

    $configFileName = $input->getArgument('config file');
    $settings       = $this->readConfigFile($configFileName);

    $this->sourceDirectory = $this->getSetting($settings, true, 'loader', 'source_directory');
    $host                  = $this->getSetting($settings, true, 'database', 'host');
    $user                  = $this->getSetting($settings, true, 'database', 'user');
    $password              = $this->getSetting($settings, true, 'database', 'password');
    $this->dataSchema      = $this->getSetting($settings, true, 'database', 'database');

    DataLayer::connect($host, $user, $password, $this->dataSchema);
    DataLayer::setIo($this->io);

    $tableList = DataLayer::getTablesNames($this->dataSchema);

    $this->helper = new QuestionHelper();

    $this->printAllTables($tableList);

    $this->startAsking($tableList);

    DataLayer::disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Asking function for create or not stored procedure.
   *
   * @param string $spType    Stored procedure type {insert|update|delete|select}.
   * @param string $tableName The table name.
   */
  private function askForCreateSP($spType, $tableName)
  {
    $question = sprintf('Create SP for <dbo>%s</dbo> ? (default Yes): ', $spType);
    $question = new ConfirmationQuestion($question, true);
    if ($this->helper->ask($this->input, $this->output, $question))
    {
      $defaultSpName = strtolower(sprintf('%s_%s', $tableName, $spType));
      $fileName      = sprintf('%s/%s.psql', $this->sourceDirectory, $defaultSpName);

      $question = new Question(sprintf('Please enter filename (%s): ', $fileName), $fileName);
      $spName   = $this->helper->ask($this->input, $this->output, $question);

      if ($spName!==$fileName)
      {
        $spName   = strtolower($spName);
        $fileName = sprintf('%s/%s.psql', $this->sourceDirectory, $spName);
      }
      else
      {
        $spName = $defaultSpName;
      }

      if (file_exists($fileName))
      {
        $this->io->writeln(sprintf('File <fso>%s</fso> already exists', $fileName));
        $question = 'Overwrite it ? (default No): ';
        $question = new ConfirmationQuestion($question, false);
        if ($this->helper->ask($this->input, $this->output, $question))
        {
          $code = $this->generateSP($tableName, $spType, $spName);
          $this->writeTwoPhases($fileName, $code);
        }
      }
      else
      {
        $code = $this->generateSP($tableName, $spType, $spName);
        $this->writeTwoPhases($fileName, $code);
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generate code for stored routine.
   *
   * @param string $tableName The table name.
   * @param string $spType    Stored routine type {insert|update|delete|select}.
   * @param string $spName    Stored routine name.
   *
   * @return string
   */
  private function generateSP($tableName, $spType, $spName)
  {
    switch ($spType)
    {
      case 'UPDATE':
        $routine = new UpdateRoutine($this->input,
                                     $this->output,
                                     $this->helper,
                                     $spType,
                                     $spName,
                                     $tableName,
                                     $this->dataSchema);
        break;

      case 'DELETE':
        $routine = new DeleteRoutine($this->input,
                                     $this->output,
                                     $this->helper,
                                     $spType, $spName,
                                     $tableName,
                                     $this->dataSchema);
        break;

      case 'SELECT':
        $routine = new SelectRoutine($this->input,
                                     $this->output,
                                     $this->helper,
                                     $spType, $spName,
                                     $tableName,
                                     $this->dataSchema);
        break;

      case 'INSERT':
        $routine = new InsertRoutine($this->input,
                                     $this->output,
                                     $this->helper,
                                     $spType,
                                     $spName,
                                     $tableName,
                                     $this->dataSchema);
        break;

      default:
        throw new FallenException("Unknown type '%s'", $spType);
    }

    return $routine->getCode();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Check option -t for show all tables.
   *
   * @param array[] $tableList All existing tables from data schema.
   */
  private function printAllTables($tableList)
  {
    if ($this->input->getOption('tables'))
    {
      $tableData = array_chunk($tableList, 4);
      $array     = [];
      foreach ($tableData as $parts)
      {
        $partsArray = [];
        foreach ($parts as $part)
        {
          $partsArray[] = $part['table_name'];
        }
        $array[] = $partsArray;
      }
      $table = new Table($this->output);
      $table->setRows($array);
      $table->render();
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads configuration parameters from the configuration file.
   *
   * @param string $configFilename
   *
   * @return array
   */
  private function readConfigFile($configFilename)
  {
    $settings = parse_ini_file($configFilename, true);

    return $settings;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Main function for asking.
   *
   * @param array[] $tableList All existing tables from data schema.
   */
  private function startAsking($tableList)
  {
    $question  = new Question('Please enter <note>TABLE NAME</note>: ');
    $tableName = $this->helper->ask($this->input, $this->output, $question);

    $key = StaticDataLayer::searchInRowSet('table_name', $tableName, $tableList);
    if (!isset($key))
    {
      $this->io->logNote("Table '%s' not exist.", $tableName);
    }
    else
    {
      $this->askForCreateSP('INSERT', $tableName);
      $this->askForCreateSP('UPDATE', $tableName);
      $this->askForCreateSP('DELETE', $tableName);
      $this->askForCreateSP('SELECT', $tableName);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
