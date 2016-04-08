<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * PhpStratum
 *
 * @copyright 2005-2015 Paul Water / Set Based IT Consultancy (https://www.setbased.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link
 */
//----------------------------------------------------------------------------------------------------------------------
class ParameterTypeTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type table must show table.
   */
  public function test1()
  {
    $reflection_class  = new \ReflectionClass('\DataLayer');
    $reflection_method = $reflection_class->getMethod('testParameterType');

    $doc_block = $reflection_method->getDocComment();

    $this->assertContains('@param float $p_php_type1', $doc_block);
    $this->assertContains('@param int   $p_php_type2', $doc_block);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
