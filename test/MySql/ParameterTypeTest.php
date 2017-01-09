<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

//----------------------------------------------------------------------------------------------------------------------
class ParameterTypeTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type table must show table.
   */
  public function test1()
  {
    $reflection_class  = new \ReflectionClass('SetBased\Stratum\Test\MySql\DataLayer');
    $reflection_method = $reflection_class->getMethod('tstTestParameterType');

    $doc_block = $reflection_method->getDocComment();

    $this->assertContains('@param float $pPhpType1', $doc_block);
    $this->assertContains('@param int   $pPhpType2', $doc_block);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
