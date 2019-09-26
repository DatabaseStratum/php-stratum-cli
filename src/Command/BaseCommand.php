<?php
declare(strict_types=1);

namespace SetBased\Stratum\Frontend\Command;

use SetBased\Stratum\Backend\Backend;
use SetBased\Stratum\Backend\StratumStyle;
use SetBased\Stratum\Frontend\Helper\StratumConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base command for other commands of PhpStratum.
 */
class BaseCommand extends Command
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The configuration object.
   *
   * @var StratumConfig
   */
  protected $config;

  /**
   * The Output decorator.
   *
   * @var StratumStyle
   */
  protected $io;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the Backend Factory as specified in the PhpStratum configuration file.
   *
   * @return Backend
   */
  protected function createBackendFactory(): Backend
  {
    $class = $this->config->manString('stratum.backend');

    return new $class();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Writes a file in two phase to the filesystem.
   *
   * First write the data to a temporary file (in the same directory) and than renames the temporary file. If the file
   * already exists and its content is equal to the data that must be written no action  is taken. This has the
   * following advantages:
   * * In case of some write error (e.g. disk full) the original file is kept in tact and no file with partially data
   * is written.
   * * Renaming a file is atomic. So, running processes will never read a partially written data.
   *
   * @param string $filename The name of the file were the data must be stored.
   * @param string $data     The data that must be written.
   */
  protected function writeTwoPhases(string $filename, string $data): void
  {
    $write_flag = true;
    if (file_exists($filename))
    {
      $old_data = file_get_contents($filename);
      if ($data==$old_data) $write_flag = false;
    }

    if ($write_flag)
    {
      $tmp_filename = $filename.'.tmp';
      file_put_contents($tmp_filename, $data);
      rename($tmp_filename, $filename);

      $this->io->text(sprintf('Wrote <fso>%s</fso>', OutputFormatter::escape($filename)));
    }
    else
    {
      $this->io->text(sprintf('File <fso>%s</fso> is up to date', OutputFormatter::escape($filename)));
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates the PhpStratum Styl object.
   *
   * @param InputInterface  $input  The input object.
   * @param OutputInterface $output The output object.
   */
  protected function createStyle(InputInterface $input, OutputInterface $output): void
  {
    $this->io = new StratumStyle($input, $output);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads the PhpStratum configuration file.
   *
   * @param InputInterface $input The input object.
   */
  protected function readConfigFile(InputInterface $input): void
  {
    $this->config = new StratumConfig($input->getArgument('config file'));
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
