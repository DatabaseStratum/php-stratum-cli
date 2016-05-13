<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Helper;

use SetBased\Exception\RuntimeException;

//----------------------------------------------------------------------------------------------------------------------
/**
 * A helper class for generation proper MySQL compound SQL with proper indentation.
 */
class PhpCodeStore
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The maximum width of the generated code (in chars).
   */
  const C_PAGE_WIDTH = 120;

  /**
   * The the number of spaces per indent level.
   *
   * @var int
   */
  public static $indentation = 2;

  /**
   * The current indent level of the code.
   *
   * @var int
   */
  private $indentLevel = 0;

  /**
   * The source code. Each element is a line.
   *
   * @var string[]
   */
  private $lines = [];

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param int $theIndentLevel Start indent level.
   */
  public function __construct($theIndentLevel)
  {
    $this->indentLevel = $theIndentLevel;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends a line or lines of code this this code.
   *
   * @param null|string|string[] $line The line or lines of code to be appended.
   * @param bool                 $trim If true the line or lines of code will be trimmed before appending.
   */
  public function append($line = null, $trim = true)
  {
    switch (true)
    {
      case is_string($line):
        $this->appendLine($line, $trim);
        break;

      case is_array($line):
        $this->appendLines($line, $trim);
        break;

      case is_null($line):
        $this->appendLine('', true);
        break;

      default:
        throw new RuntimeException('Nor a string or array.');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends a part of code to the last line of code.
   *
   * @param string $part The part of code to be to the last line.
   */
  public function appendToLastLine($part)
  {
    $this->lines[count($this->lines) - 1] .= $part;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the generated code as a single string.
   */
  public function getCode()
  {
    return implode(PHP_EOL, $this->lines);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a line of code with the proper amount of indentation.
   *
   * @param string $line The line of code.
   *
   * @return string The indented line of code.
   */
  private function addIndentation($line)
  {
    return str_repeat(' ', self::$indentation * $this->indentLevel).$line;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends a line of code this this code.
   *
   * @param string $line The line of code to be appended.
   * @param bool   $trim If true the line of code will be trimmed before appending.
   */
  private function appendLine($line, $trim)
  {
    if ($trim) $line = trim($line);

    $words = explode(' ', $line);
    if (count($words)>0)
    {
      switch ($words[0])
      {
        case '{':
          $line = $this->addIndentation($line);
          $this->indentLevel += 1;
          break;

        case '}':
          $this->indentLevel = max(0, $this->indentLevel - 1);
          $line              = $this->addIndentation($line);
          break;

        default:
          $line = $this->addIndentation($line);
          break;
      }
    }

    $this->lines[] = $line;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends lines of code this this code.
   *
   * @param string[] $lines The lines of code to be appended.
   * @param bool     $trim  If true the lines of code will be trimmed before appending.
   */
  private function appendLines($lines, $trim)
  {
    foreach ($lines as $line)
    {
      $this->appendLine($line, $trim);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends a comment line to the generated code.
   */
  public function appendSeparator()
  {
    $separator = '';
    for ($i = 0; $i<2 * $this->indentLevel; $i++)
    {
      $separator .= ' ';
    }

    $separator .= '//';

    for ($i = 0; $i<(self::C_PAGE_WIDTH - 2 * $this->indentLevel - 2); $i++)
    {
      $separator .= '-';
    }
    $this->append($separator, false);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
