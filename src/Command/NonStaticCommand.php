<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Command;

use SetBased\Exception\RuntimeException;
use SetBased\Stratum\Helper\NonStatic;
use SetBased\Stratum\Style\StratumStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command to make a non static class from a static class.
 */
class NonStaticCommand extends Command
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The output decorator
   *
   * @var StratumStyle
   */
  protected $io;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Makes non static implementation of a static class.
   *
   * @param string $sourceName The filename with the static class.
   * @param string $targetName The filename where the non static class must be written.
   */
  public static function staticToStatic($sourceName, $targetName)
  {
    $source      = file_get_contents($sourceName);
    $sourceClass = basename($sourceName, '.php');
    $targetClass = basename($targetName, '.php');

    $source = NonStatic::nonStatic($source, $sourceClass, $targetClass);

    file_put_contents($targetName, $source);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this->setName('non-static')
         ->setDescription('Makes non static implementation of a static class')
         ->addArgument('source', InputArgument::REQUIRED, 'The filename with the static class')
         ->addArgument('target', InputArgument::REQUIRED, 'The filename where the non static class must be written');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes the actual PhpStratum program. Returns 0 is everything went fine. Otherwise, returns non-zero.
   *
   * @param InputInterface  $input  An InputInterface instance
   * @param OutputInterface $output An OutputInterface instance
   *
   * @return int
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->io = new StratumStyle($input, $output);

    $sourceFilename = $input->getArgument('source');
    $targetFilename = dirname($sourceFilename).'/'.basename($input->getArgument('target'));

    if (basename($sourceFilename)===basename($targetFilename))
    {
      throw new RuntimeException('Source and target files is the same file');
    }

    self::staticToStatic($sourceFilename, $targetFilename);

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
