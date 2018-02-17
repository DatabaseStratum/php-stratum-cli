<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Command;

use SetBased\Exception\RuntimeException;
use SetBased\Stratum\Style\StratumStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * Base command for other commands of PhpStratum.
 */
class BaseCommand extends Command
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The Output decorator.
   *
   * @var StratumStyle
   */
  protected $io;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the value of a setting.
   *
   * @param array  $settings    The settings as returned by parse_ini_file.
   * @param bool   $mandatory   If set and setting $settingName is not found in section $sectionName an exception
   *                            will be thrown.
   * @param string $sectionName The name of the section of the requested setting.
   * @param string $settingName The name of the setting of the requested setting.
   *
   * @return null|string
   */
  protected static function getSetting($settings, $mandatory, $sectionName, $settingName)
  {
    // Test if the section exists.
    if (!array_key_exists($sectionName, $settings))
    {
      if ($mandatory)
      {
        throw new RuntimeException("Section '%s' not found in configuration file.", $sectionName);
      }
      else
      {
        return null;
      }
    }

    // Test if the setting in the section exists.
    if (!array_key_exists($settingName, $settings[$sectionName]))
    {
      if ($mandatory)
      {
        throw new RuntimeException("Setting '%s' not found in section '%s' configuration file.",
                                   $settingName,
                                   $sectionName);
      }
      else
      {
        return null;
      }
    }

    return $settings[$sectionName][$settingName];
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
  protected function writeTwoPhases($filename, $data)
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
}

//----------------------------------------------------------------------------------------------------------------------
