<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Helper;

use SetBased\Helper\CodeStore\PhpCodeStore;

//----------------------------------------------------------------------------------------------------------------------
/**
 * A helper class for generation proper MySQL compound SQL with proper indentation.
 */
class CompoundSyntaxCodeStore extends PhpCodeStore
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The current indent level of the code.
   *
   * @var int
   */
  private $indentLevel = 0;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Return last line length.
   *
   * @return int
   */
  public function lengthLastLine()
  {
    return strlen($this->lines[count($this->lines) - 1]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends a line of code this this code.
   *
   * @param string $line The line of code to be appended.
   * @param bool   $trim If true the line of code will be trimmed before appending.
   */
  protected function appendLine($line, $trim)
  {
    if ($trim) $line = trim($line);

    $words = explode(' ', $line);
    if (count($words)>0)
    {
      switch ($words[0])
      {
        case '(':
        case 'begin':
        case 'if':
          $line = $this->addIndentation($line,$this->indentLevel);
          $this->indentLevel += 1;
          break;

        case ')':
        case 'end':
          $this->indentLevel = max(0, $this->indentLevel - 1);
          $line              = $this->addIndentation($line,$this->indentLevel);
          break;

        default:
          $line = $this->addIndentation($line,$this->indentLevel);
          break;
      }
    }

    $this->lines[] = $line;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
