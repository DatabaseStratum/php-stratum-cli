<?php
//----------------------------------------------------------------------------------------------------------------------
class QuoteTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    TST_DL::Connect( 'localhost', 'test', 'test', 'test' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function genericValid( $theColumn, $theValue )
  {
    $n = TST_DL::Test01( ($theColumn=='int')           ? $theValue : null,        // tst_c00 int
                         ($theColumn=='smallint')      ? $theValue : null,        // tst_c01 smallint
                         ($theColumn=='tinyint')       ? $theValue : null,        // tst_c02 tinyint
                         ($theColumn=='mediumint')     ? $theValue : null,        // tst_c03 mediumint
                         ($theColumn=='bigint')        ? $theValue : null,        // tst_c04 bigint
                         ($theColumn=='decimal)')      ? $theValue : null,        // tst_c05 decimal(10,2)
                         ($theColumn=='float')         ? $theValue : null,        // tst_c06 float
                         ($theColumn=='double')        ? $theValue : null,        // tst_c07 double
                         ($theColumn=='bit')           ? $theValue : null,        // tst_c08 bit
                         ($theColumn=='date')          ? $theValue : null,        // tst_c09 date
                         ($theColumn=='datetime')      ? $theValue : null,        // tst_c10 datetime
                         ($theColumn=='timestamp')     ? $theValue : null,        // tst_c11 timestamp
                         ($theColumn=='time')          ? $theValue : null,        // tst_c12 time
                         ($theColumn=='year')          ? $theValue : null,        // tst_c13 year
                         ($theColumn=='char')          ? $theValue : null,        // tst_c14 char(10)
                         ($theColumn=='varchar')       ? $theValue : null,        // tst_c15 varchar(10)
                         ($theColumn=='binary')        ? $theValue : null,        // tst_c16 binary(10)
                         ($theColumn=='varbinary')     ? $theValue : null,        // tst_c17 varbinary(10)
                         ($theColumn=='enum')          ? $theValue : null,        // tst_c26 enum('a','b')
                         ($theColumn=='set')           ? $theValue : null );      // tst_c27 set('a','b')
    $this->assertEquals( 1, $n );
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function genericInvalid( $theColumn, $theValue )
  {
    try
    {
       $n = TST_DL::Test01( ($theColumn=='int')        ? $theValue : null,        // tst_c00 int
                         ($theColumn=='smallint')      ? $theValue : null,        // tst_c01 smallint
                         ($theColumn=='tinyint')       ? $theValue : null,        // tst_c02 tinyint
                         ($theColumn=='mediumint')     ? $theValue : null,        // tst_c03 mediumint
                         ($theColumn=='bigint')        ? $theValue : null,        // tst_c04 bigint
                         ($theColumn=='decimal)')      ? $theValue : null,        // tst_c05 decimal(10,2)
                         ($theColumn=='float')         ? $theValue : null,        // tst_c06 float
                         ($theColumn=='double')        ? $theValue : null,        // tst_c07 double
                         ($theColumn=='bit')           ? $theValue : null,        // tst_c08 bit
                         ($theColumn=='date')          ? $theValue : null,        // tst_c09 date
                         ($theColumn=='datetime')      ? $theValue : null,        // tst_c10 datetime
                         ($theColumn=='timestamp')     ? $theValue : null,        // tst_c11 timestamp
                         ($theColumn=='time')          ? $theValue : null,        // tst_c12 time
                         ($theColumn=='year')          ? $theValue : null,        // tst_c13 year
                         ($theColumn=='char')          ? $theValue : null,        // tst_c14 char(10)
                         ($theColumn=='varchar')       ? $theValue : null,        // tst_c15 varchar(10)
                         ($theColumn=='binary')        ? $theValue : null,        // tst_c16 binary(10)
                         ($theColumn=='varbinary')     ? $theValue : null,        // tst_c17 varbinary(10)
                         ($theColumn=='enum')          ? $theValue : null,        // tst_c26 enum('a','b')
                         ($theColumn=='set')           ? $theValue : null );      // tst_c27 set('a','b')
      $this->assertTrue( false );
    }
    catch( Exception $e )
    {
      $this->assertTrue( true );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Test all column types are quoted properly.
   */
  public function testValid()
  {
    $tests[] = array( 'column' => 'int',        'value'  =>   1  );
    $tests[] = array( 'column' => 'int',        'value'  =>  '1' );
    $tests[] = array( 'column' => 'smallint',   'value'  =>   1  );
    $tests[] = array( 'column' => 'smallint',   'value'  =>  '1' );
    $tests[] = array( 'column' => 'tinyint',    'value'  =>   1  );
    $tests[] = array( 'column' => 'tinyint',    'value'  =>  '1' );
    $tests[] = array( 'column' => 'mediumint',  'value'  =>   1  );
    $tests[] = array( 'column' => 'mediumint',  'value'  =>  '1' );
    $tests[] = array( 'column' => 'bigint',     'value'  =>   1  );
    $tests[] = array( 'column' => 'bigint',     'value'  =>  '1' );

    $tests[] = array( 'column' => 'decimal',    'value'  =>   0.1  );
    $tests[] = array( 'column' => 'decimal',    'value'  =>  '0.1' );
    $tests[] = array( 'column' => 'float',      'value'  =>   0.1  );
    $tests[] = array( 'column' => 'float',      'value'  =>  '0.1' );
    $tests[] = array( 'column' => 'double',     'value'  =>   0.1  );
    $tests[] = array( 'column' => 'double',     'value'  =>  '0.1' );

    $tests[] = array( 'column' => 'bit',        'value'  =>   1010  );
    $tests[] = array( 'column' => 'bit',        'value'  =>  '1010' );

    $tests[] = array( 'column' => 'date',       'value'  =>  '2000-01-01' );
    $tests[] = array( 'column' => 'datetime',   'value'  =>  '2000-01-01 10:00:00' );
    $tests[] = array( 'column' => 'timestamp',  'value'  =>   2102101 );
    $tests[] = array( 'column' => 'time',       'value'  =>  '10:00:00' );
    $tests[] = array( 'column' => 'year',       'value'  =>   2000 );
    $tests[] = array( 'column' => 'year',       'value'  =>  '2000');

    $tests[] = array( 'column' => 'char',       'value'  =>   1234 );
    $tests[] = array( 'column' => 'char',       'value'  =>  'abc' );
    $tests[] = array( 'column' => 'char',       'value'  =>  "0xC8 ' --" );

    $tests[] = array( 'column' => 'varchar',    'value'  =>   1234 );
    $tests[] = array( 'column' => 'varchar',    'value'  =>  'abc' );
    $tests[] = array( 'column' => 'varchar',    'value'  =>  "0xC8 ' --" );

    $tests[] = array( 'column' => 'binary',     'value'  =>   1010  );
    $tests[] = array( 'column' => 'binary',     'value'  =>  '1010' );
    $tests[] = array( 'column' => 'binary',     'value'  =>  "\xFF\x7F\x80\x5c\x00\x10" );
    $tests[] = array( 'column' => 'varbinary',  'value'  =>   1010  );
    $tests[] = array( 'column' => 'varbinary',  'value'  =>  '1010' );
    $tests[] = array( 'column' => 'varbinary',  'value'  =>  "\xFF\x7F\x80\x5c\x00\x10" );

    $tests[] = array( 'column' => 'enum',       'value'  =>  'a' );
    $tests[] = array( 'column' => 'enum',       'value'  =>  'b' );

    $tests[] = array( 'column' => 'set',        'value'  =>  'a' );
    $tests[] = array( 'column' => 'set',        'value'  =>  'b' );

    foreach( $tests as $test )
    {
      $this->genericValid( $test['column'], $test['value'] );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Test illegal values will raise an exception.
   */
  public function testInvalid()
  {
    $tests[] = array( 'column' => 'int',        'value'  =>  'abc' );
    $tests[] = array( 'column' => 'smallint',   'value'  =>  'abc' );
    $tests[] = array( 'column' => 'tinyint',    'value'  =>  'abc' );
    $tests[] = array( 'column' => 'mediumint',  'value'  =>  'abc' );
    $tests[] = array( 'column' => 'bigint',     'value'  =>  'abc' );

    $tests[] = array( 'column' => 'decimal',    'value'  =>  'abc' );
    $tests[] = array( 'column' => 'float',      'value'  =>  'abc' );
    $tests[] = array( 'column' => 'double',     'value'  =>  'abc' );

    $tests[] = array( 'column' => 'bit',        'value'  =>  'abc' );

    $tests[] = array( 'column' => 'date',       'value'  =>  'qwerty' );
    $tests[] = array( 'column' => 'datetime',   'value'  =>  'qwerty' );
    $tests[] = array( 'column' => 'timestamp',  'value'  =>  'qwerty' );
    $tests[] = array( 'column' => 'time',       'value'  =>  'qwerty' );

    $tests[] = array( 'column' => 'year',       'value'  =>  'abc');

    $tests[] = array( 'column' => 'enum',       'value'  =>  'c' );
    $tests[] = array( 'column' => 'set',        'value'  =>  'c' );

    foreach( $tests as $test )
    {
      $this->genericInvalid( $test['column'], $test['value'] );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------

}
//----------------------------------------------------------------------------------------------------------------------
