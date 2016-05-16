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

  /**
   * String for separating methods nd other parts of the generated code.
   *
   * @var string
   */
  private $separator;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   */
  public function __construct()
  {
    $this->separator = '//'.str_repeat('-', self::C_PAGE_WIDTH - 2);
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
        $this->appendLine($line, true);
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
   * Returns the code as a string with proper indentation.
   *
   * @return string
   */
  public function getCode()
  {
    $lines             = [];
    $this->indentLevel = 0;

    foreach ($this->lines as $line)
    {
      switch ($line)
      {
        case '{':
          $lines[] = $this->addIndentation($line);
          $this->indentLevel += 1;
          break;

        case '}':
          $this->indentLevel = max(0, $this->indentLevel - 1);
          $lines[]           = $this->addIndentation($line);
          break;

        case $this->separator:
          $lines[] = substr($this->addIndentation($line), 0, self::C_PAGE_WIDTH);
          break;

        default:
          $lines[] = $this->addIndentation($line);
          break;
      }
    }

    return implode(PHP_EOL, $lines).PHP_EOL;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the code as an array of strings (without indentation).
   *
   * @return \string[]
   */
  public function getLines()
  {
    return $this->lines;
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
    if ($line===null || $line==='')
    {
      return '';
    }

    return str_repeat(' ', self::$indentation * $this->indentLevel).$line;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends a line of code this this code.
   *
   * @param string $line The line of code to be appended.
   * @param bool   $trim If true the line of code will be trimmed before appending.
   *
   */
  private function appendLine($line, $trim)
  {
    if ($trim) $line = trim($line);

    $this->lines[] = $line;;
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
    $this->appendLine($this->separator, false);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
