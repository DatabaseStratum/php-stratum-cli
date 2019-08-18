<?php
declare(strict_types=1);

namespace SetBased\Stratum\Helper;

/**
 * Helper class for generating code for a non static class based on a static class.
 */
class NonStatic
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the code for a non static class based on a static class.
   *
   * @param string      $source      The source code of the static class.
   * @param string|null $sourceClass The name of the static class.
   * @param string|null $targetClass The name of the non static class.
   *
   * @return string
   */
  public static function nonStatic(string $source, ?string $sourceClass = null, ?string $targetClass = null): string
  {
    // Replace static fields.
    $source = preg_replace('/(public|protected|private)\s+static(\s+)\$/i', '${1}${2}$', $source);

    // Replace usage of static fields.
    $source = preg_replace('/self::\$/', '$this->', $source);

    // Replace static methods.
    $source = preg_replace('/(public|protected|private)\s+static(\s+)(function)/i', '${1}${2}${3}', $source);

    // Replace invocation of static methods.
    $source = preg_replace('/self::/', '$this->', $source);

    // Replace class name.
    if ($sourceClass!==null)
    {
      $source = preg_replace('/(class)(\s+)'.$sourceClass.'/', '${1}${2}'.$targetClass, $source);
    }

    return $source;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
