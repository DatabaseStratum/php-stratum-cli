<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Helper;

use SetBased\Helper\CodeStore\CodeStore;

//----------------------------------------------------------------------------------------------------------------------
/**
 * A helper class for generation proper MySQL compound SQL with proper indentation.
 */
class CompoundSyntaxCodeStore extends CodeStore
{
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
   * {@inheritdoc}
   */
  protected function indentationMode($line)
  {
    $mode = 0;

    $line = trim($line);

    $words = explode(' ', $line);
    if (count($words)>0)
    {
      switch ($words[0])
      {
        case 'begin':
        case 'if':
          $mode |= self::C_INDENT_INCREMENT_AFTER;
          break;

        case 'end':
          $mode |= self::C_INDENT_DECREMENT_BEFORE;
          break;
      }
    }

    return $mode;
  }
}

//----------------------------------------------------------------------------------------------------------------------
