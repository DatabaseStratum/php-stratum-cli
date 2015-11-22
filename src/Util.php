<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * phpStratum
 *
 * @copyright 2005-2015 Paul Water / Set Based IT Consultancy (https://www.setbased.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link
 */
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum;

use SetBased\Stratum\Exception\RuntimeException;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Static class for miscellaneous functions.
 */
class Util
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the value of a setting.
   *
   * @param array  $theSettings      The settings as returned by parse_ini_file.
   * @param bool   $theMandatoryFlag If set and setting $theSettingName is not found in section $theSectionName
   *                                 an exception will be thrown.
   * @param string $theSectionName   The name of the section of the requested setting.
   * @param string $theSettingName   The name of the setting of the requested setting.
   *
   * @return array|null
   */
  public static function getSetting($theSettings, $theMandatoryFlag, $theSectionName, $theSettingName)
  {
    // Test if the section exists.
    if (!array_key_exists($theSectionName, $theSettings))
    {
      if ($theMandatoryFlag)
      {
        throw new RuntimeException("Section '%s' not found in configuration file.", $theSectionName);
      }
      else
      {
        return null;
      }
    }

    // Test if the setting in the section exists.
    if (!array_key_exists($theSettingName, $theSettings[$theSectionName]))
    {
      if ($theMandatoryFlag)
      {
        throw new RuntimeException("Setting '%s' not found in section '%s' configuration file.",
                                   $theSettingName,
                                   $theSectionName);
      }
      else
      {
        return null;
      }
    }

    return $theSettings[$theSectionName][$theSettingName];
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
   * @param string $theFilename The name of the file were the data must be stored.
   * @param string $theData     The data that must be written.
   */
  public static function writeTwoPhases($theFilename, $theData)
  {
    $write_flag = true;
    if (file_exists($theFilename))
    {
      $old_data = file_get_contents($theFilename);
      if ($theData==$old_data) $write_flag = false;
    }

    if ($write_flag)
    {
      $tmp_filename = $theFilename.'.tmp';
      file_put_contents($tmp_filename, $theData);
      rename($tmp_filename, $theFilename);
      echo "Wrote: '", $theFilename, "'.\n";
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
