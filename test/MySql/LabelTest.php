<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

use ReflectionClass;

//----------------------------------------------------------------------------------------------------------------------
class LabelTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Check all labels exist in data schema.
   */
  public function test1()
  {
    $c = new C();
    $fooClass = new ReflectionClass($c);
    $constants = $fooClass->getConstants();

    $tableConstants = DataLayer::testLabel();
    foreach ($constants as $name => $value)
    {
      $check = DataLayer::searchInRowSet('tst_label', $name, $tableConstants);
      $this->assertNotNull($check);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
