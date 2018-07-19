<?php
declare(strict_types=1);

namespace SetBased\Stratum\MySql\Exception;

use SetBased\Exception\RuntimeException;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * Exception for situations where the execution of s SQL query has failed.
 */
class DataLayerException extends RuntimeException
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The error code value of the error ($mysqli->errno).
   *
   * @var int
   */
  protected $errno;

  /**
   * Description of the last error ($mysqli->error).
   *
   * @var string
   */
  protected $error;

  /**
   * The executed SQL query or description of function.
   *
   * @var string
   */
  protected $query;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param int    $errno   The error code value of the error ($mysqli->errno).
   * @param string $error   Description of the last error ($mysqli->error).
   * @param string $message The SQL query or function name.
   */
  public function __construct(int $errno, string $error, string $message)
  {
    parent::__construct('%s', self::message($errno, $error, $message));

    $this->errno = $errno;
    $this->error = $error;
    $this->query = $message;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the error code of the error
   *
   * @return int
   */
  public function getErrno(): int
  {
    return $this->errno;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the description of the error.
   *
   * @return string
   */
  public function getError(): string
  {
    return $this->error;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns an array with the lines of the SQL statement. The line where the error occurred will be styled.
   *
   * @param string $style The style for highlighting the line with error.
   *
   * @return array The lines of the SQL statement.
   */
  public function getMarkedQuery(string $style = 'error'): array
  {
    $query   = trim($this->query); // MySQL ignores leading whitespace in queries.
    $message = [];

    if (strpos($query, PHP_EOL)!==false && $this->isQueryError())
    {
      // Query is a multi line query.
      // The format of a 1064 message is: %s near '%s' at line %d
      $error_line = trim(strrchr($this->error, ' '));

      // Prepend each line with line number.
      $lines  = explode(PHP_EOL, $query);
      $digits = ceil(log(sizeof($lines) + 1, 10));
      $format = sprintf('%%%dd %%s', $digits);
      foreach ($lines as $i => $line)
      {
        if (($i + 1)==$error_line)
        {
          $message[] = sprintf('<%s>'.$format.'</%s>', $style, $i + 1, OutputFormatter::escape($line), $style);
        }
        else
        {
          $message[] = sprintf($format, $i + 1, OutputFormatter::escape($line));
        }
      }
    }
    else
    {
      // Query is a single line query or a method name.
      $message[] = $query;
    }

    return $message;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  public function getName()
  {
    return ($this->isQueryError()) ? 'SQL Error' : 'MySQL Error';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns SQL query or function name.
   *
   * @return string
   */
  public function getQuery(): string
  {
    return $this->query;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the error message without the query or method name.
   *
   * @return string
   */
  public function getShortMessage(): string
  {
    $message = 'MySQL Error no: '.$this->errno."\n";
    $message .= $this->error;

    return $message;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns true if this exception is caused by an invalid SQL statement. Otherwise returns false.
   *
   * @return bool
   */
  public function isQueryError(): bool
  {
    return ($this->errno==1064);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Composes the exception message.
   *
   * @param int    $errno The error code value of the error ($mysqli->errno).
   * @param string $error Description of the error ($mysqli->error).
   * @param string $query The SQL query or method name.
   *
   * @return string
   */
  private function message(int $errno, string $error, string $query): string
  {
    $message = 'MySQL Error no: '.$errno."\n";
    $message .= $error;
    $message .= "\n";

    $query = trim($query);
    if (strpos($query, "\n")!==false)
    {
      // Query is a multi line query.
      $message .= "\n";

      // Prepend each line with line number.
      $lines  = explode("\n", $query);
      $digits = ceil(log(sizeof($lines), 10));
      $format = sprintf("%%%dd %%s\n", $digits);
      foreach ($lines as $i => $line)
      {
        $message .= sprintf($format, $i + 1, $line);
      }
    }
    else
    {
      // Query is a single line query or method name.
      $message .= $query;
    }

    return $message;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
