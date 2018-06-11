<?php

namespace SetBased\Stratum\Test\MySql;

/**
 * Test cases for PHP parameter names.
 */
class ParameterTypeTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test parameter names.
   */
  public function test1()
  {
    $reflection_class  = new \ReflectionClass('SetBased\Stratum\Test\MySql\TestDataLayer');
    $reflection_method = $reflection_class->getMethod('tstTestParameterType');

    $doc_block = $reflection_method->getDocComment();

    $this->assertContains('@param float $pPhpType1', $doc_block);
    $this->assertContains('@param int   $pPhpType2', $doc_block);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
