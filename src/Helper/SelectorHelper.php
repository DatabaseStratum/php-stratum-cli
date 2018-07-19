<?php
declare(strict_types=1);

namespace SetBased\Stratum\Helper;

/**
 * Utility class for selecting/filtering paths against patterns.
 */
class SelectorHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests whether or not a given path matches a given pattern.
   *
   * (This method is heavily inspired by SelectorUtils::matchPath from phing.)
   *
   * @param string $pattern         The pattern to match against.
   * @param string $path            The path to match.
   * @param bool   $isCaseSensitive Whether or not matching should be performed case sensitively.
   *
   * @return bool
   */
  public static function matchPath(string $pattern, string $path, bool $isCaseSensitive = true): bool
  {
    // Explicitly exclude directory itself.
    if ($path=='' && $pattern=='**/*')
    {
      return false;
    }

    $dirSep         = preg_quote(DIRECTORY_SEPARATOR, '/');
    $trailingDirSep = '(('.$dirSep.')?|('.$dirSep.').+)';

    $patternReplacements = [$dirSep.'\*\*'.$dirSep => $dirSep.'.*'.$trailingDirSep,
                            $dirSep.'\*\*'         => $trailingDirSep,
                            '\*\*'.$dirSep         => '(.*'.$dirSep.')?',
                            '\*\*'                 => '.*',
                            '\*'                   => '[^'.$dirSep.']*',
                            '\?'                   => '[^'.$dirSep.']'];

    $rePattern = preg_quote($pattern, '/');
    $rePattern = str_replace(array_keys($patternReplacements), array_values($patternReplacements), $rePattern);
    $rePattern = '/^'.$rePattern.'$/'.($isCaseSensitive ? '' : 'i');

    return (bool)preg_match($rePattern, $path);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
