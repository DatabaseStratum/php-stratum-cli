<?php
//----------------------------------------------------------------------------------------------------------------------
use \SetBased\Stratum\Exception\RunTimeException;
use \SetBased\Stratum\Exception\ResultException;
use \SetBased\Stratum\MySql\StaticDataLayer;

//----------------------------------------------------------------------------------------------------------------------
class DataLayer extends StaticDataLayer
{
  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function magicConstant01(  )
  {
    return self::executeSingleton1( 'CALL tst_magic_constant01()');
  }

  //-------------------------------------------------------------------------------------------------------------------
  /**
   * Test for magic constant.
   *
   * @return string
   * @throws ResultException
   * @throws RunTimeException
   */
  public static function magicConstant02(  )
  {
    return self::executeSingleton1( 'CALL tst_magic_constant02()');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
