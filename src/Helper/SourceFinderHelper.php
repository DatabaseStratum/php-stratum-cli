<?php
declare(strict_types=1);

namespace SetBased\Stratum\Helper;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Helper class for finding sources of stored routines.
 */
class SourceFinderHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The directory of the stratum configuration file.
   *
   * @var string
   */
  private $basedir;

  //--------------------------------------------------------------------------------------------------------------------

  /**
   * SourceFinderHelper constructor.
   *
   * @param string $basedir The directory of the stratum configuration file.
   */
  public function __construct(string $basedir)
  {
    $this->basedir = $basedir;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the leading directory without wild cards of a pattern.
   *
   * @param string $pattern The pattern.
   *
   * @return string
   */
  private static function getLeadingDir(string $pattern): string
  {
    $dir = $pattern;

    $pos = strpos($dir, '*');
    if ($pos!==false) $dir = substr($dir, 0, $pos);

    $pos = strpos($dir, '?');
    if ($pos!==false) $dir = substr($dir, 0, $pos);

    $pos = strrpos($dir, '/');
    if ($pos!==false)
    {
      $dir = substr($dir, 0, $pos);
    }
    else
    {
      $dir = '.';
    }

    return $dir;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Finds sources of stored routines.
   *
   * @param string $sources The value of the sources parameter.
   *
   * @return string[]
   */
  public function findSources(string $sources): array
  {
    $patterns = $this->sourcesToPatterns($sources);

    $sources = [];
    foreach ($patterns as $pattern)
    {
      $tmp     = $this->findSourcesInPattern($pattern);
      $sources = array_merge($sources, $tmp);
    }

    $sources = array_unique($sources);
    sort($sources);

    return $sources;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Finds sources of stored routines in a pattern.
   *
   * @param string $pattern The pattern of the sources.
   *
   * @return string[]
   */
  private function findSourcesInPattern(string $pattern): array
  {
    $sources = [];

    $directory = new RecursiveDirectoryIterator(self::getLeadingDir($pattern));
    $directory->setFlags(RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
    $files = new RecursiveIteratorIterator($directory);
    foreach ($files as $fullPath => $file)
    {
      // If the file is a source file with stored routine add it to my sources.
      if ($file->isFile() && SelectorHelper::matchPath($pattern, $fullPath))
      {
        $sources[] = $fullPath;
      }
    }

    return $sources;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Reads a list of patterns from a file.
   *
   * @param string $filename The name of the file with a list of patterns.
   *
   * @return string[]
   */
  private function readPatterns(string $filename): array
  {
    $path  = $this->basedir.'/'.$filename;
    $lines = explode(PHP_EOL, file_get_contents($path));

    $patterns = [];
    foreach ($lines as $line)
    {
      $line = trim($line);
      if ($line<>'')
      {
        $patterns[] = $line;
      }
    }

    return $patterns;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Converts the sources parameter to a list a patterns.
   *
   * @param string $sources The value of the sources parameter.
   *
   * @return string[]
   */
  private function sourcesToPatterns(string $sources): array
  {
    if (substr($sources, 0, 5)=='file:')
    {
      $patterns = $this->readPatterns(substr($sources, 5));
    }
    else
    {
      $patterns = [$sources];
    }

    return $patterns;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
