<?php
//----------------------------------------------------------------------------------------------------------------------
/**  
    tst_c05 decimal(10,2)
    
    tst_c06 float
    tst_c07 double
    
    tst_c08 bit
    
    tst_c09 date
    
    tst_c10 datetime
    
    tst_c11 timestamp
    
    tst_c12 time
    
    tst_c13 year
    
    tst_c14 char(10)
    tst_c15 varchar(10)
    tst_c16 binary(10)
    tst_c17 varbinary(10)
*/
//----------------------------------------------------------------------------------------------------------------------
class Quote1Test extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    TST_DL::Connect( 'localhost', 'test', 'test', 'test' );  
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Test int is quoted properly.
   */
  public function testInt()
  {
    TST_DL::Test01(    1,       // tst_c00 int
                    null,       // tst_c01 smallint
                    null,       // tst_c02 tinyint
                    null,       // tst_c03 mediumint           
                    null,       // tst_c04 bigint
                    null,       // tst_c05 decimal(10,2)
                    null,       // tst_c06 float
                    null,       // tst_c07 double
                    null,       // tst_c08 bit
                    null,       // tst_c09 date
                    null,       // tst_c10 datetime
                    null,       // tst_c11 timestamp
                    null,       // tst_c12 time
                    null,       // tst_c13 year
                    null,       // tst_c14 char(10)            
                    null,       // tst_c15 varchar(10)
                    null,       // tst_c16 binary(10)
                    null,       // tst_c17 varbinary(10)
                    null,       // tst_c26 enum('a','b')
                    null );     // tst_c27 set('a','b')
    $this->assertTrue( true );         
                                       
    TST_DL::Test01(  '1',       // tst_c00 int
                    null,       // tst_c01 smallint
                    null,       // tst_c02 tinyint
                    null,       // tst_c03 mediumint           
                    null,       // tst_c04 bigint
                    null,       // tst_c05 decimal(10,2)
                    null,       // tst_c06 float
                    null,       // tst_c07 double
                    null,       // tst_c08 bit
                    null,       // tst_c09 date
                    null,       // tst_c10 datetime
                    null,       // tst_c11 timestamp
                    null,       // tst_c12 time
                    null,       // tst_c13 year
                    null,       // tst_c14 char(10)            
                    null,       // tst_c15 varchar(10)
                    null,       // tst_c16 binary(10)
                    null,       // tst_c17 varbinary(10)
                    null,       // tst_c26 enum('a','b')
                    null );     // tst_c27 set('a','b')
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test a non-int value for a int column raises an exception.    
   * @expectedException Exception
   */                              
  public function testInvalidInt() 
  {                                                            
    TST_DL::Test01( 'abc',      // tst_c00 int
                    null,       // tst_c01 smallint
                    null,       // tst_c02 tinyint
                    null,       // tst_c03 mediumint           
                    null,       // tst_c04 bigint
                    null,       // tst_c05 decimal(10,2)
                    null,       // tst_c06 float
                    null,       // tst_c07 double
                    null,       // tst_c08 bit
                    null,       // tst_c09 date
                    null,       // tst_c10 datetime
                    null,       // tst_c11 timestamp
                    null,       // tst_c12 time
                    null,       // tst_c13 year
                    null,       // tst_c14 char(10)            
                    null,       // tst_c15 varchar(10)
                    null,       // tst_c16 binary(10)
                    null,       // tst_c17 varbinary(10)
                    null,       // tst_c26 enum('a','b')
                    null );     // tst_c27 set('a','b')
  }
     
  //--------------------------------------------------------------------------------------------------------------------
  /** Test smalint is quoted properly.
   */
  public function testSmallint()
  {
    TST_DL::Test01( null,        // tst_c00 int
                       1,        // tst_c01 smallint
                    null,        // tst_c02 tinyint
                    null,        // tst_c03 mediumint      
                    null,        // tst_c04 bigint
                    null,        // tst_c05 decimal(10,2)
                    null,        // tst_c06 float
                    null,        // tst_c07 double
                    null,        // tst_c08 bit
                    null,        // tst_c09 date
                    null,        // tst_c10 datetime
                    null,        // tst_c11 timestamp
                    null,        // tst_c12 time
                    null,        // tst_c13 year
                    null,        // tst_c14 char(10)       
                    null,        // tst_c15 varchar(10)
                    null,        // tst_c16 binary(10)
                    null,        // tst_c17 varbinary(10)
                    null,        // tst_c26 enum('a','b')
                    null );      // tst_c27 set('a','b')             
    $this->assertTrue( true );
    
    TST_DL::Test01( null,        // tst_c00 int
                     '1',        // tst_c01 smallint
                    null,        // tst_c02 tinyint
                    null,        // tst_c03 mediumint      
                    null,        // tst_c04 bigint
                    null,        // tst_c05 decimal(10,2)
                    null,        // tst_c06 float
                    null,        // tst_c07 double
                    null,        // tst_c08 bit
                    null,        // tst_c09 date
                    null,        // tst_c10 datetime
                    null,        // tst_c11 timestamp
                    null,        // tst_c12 time
                    null,        // tst_c13 year
                    null,        // tst_c14 char(10)       
                    null,        // tst_c15 varchar(10)
                    null,        // tst_c16 binary(10)
                    null,        // tst_c17 varbinary(10)
                    null,        // tst_c26 enum('a','b')
                    null );      // tst_c27 set('a','b')
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test a non-smallint value for a smallint column raises an exception.    
   * @expectedException Exception
   */                              
  public function testInvalidSmallint()
  {
    TST_DL::Test01( null,        // tst_c00 int
                   'abc',        // tst_c01 smallint
                    null,        // tst_c02 tinyint
                    null,        // tst_c03 mediumint      
                    null,        // tst_c04 bigint
                    null,        // tst_c05 decimal(10,2)
                    null,        // tst_c06 float
                    null,        // tst_c07 double
                    null,        // tst_c08 bit
                    null,        // tst_c09 date
                    null,        // tst_c10 datetime
                    null,        // tst_c11 timestamp
                    null,        // tst_c12 time
                    null,        // tst_c13 year
                    null,        // tst_c14 char(10)       
                    null,        // tst_c15 varchar(10)
                    null,        // tst_c16 binary(10)
                    null,        // tst_c17 varbinary(10)
                    null,        // tst_c26 enum('a','b')
                    null );      // tst_c27 set('a','b')
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test tinyint is quoted properly.
   */
  public function testTinyint()
  {
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                       1,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b') 
    $this->assertTrue( true );
    
    TST_DL::Test01( null,           // tst_c00 int
                    null,           // tst_c01 smallint
                     '1',           // tst_c02 tinyint
                    null,           // tst_c03 mediumint      
                    null,           // tst_c04 bigint
                    null,           // tst_c05 decimal(10,2)
                    null,           // tst_c06 float
                    null,           // tst_c07 double
                    null,           // tst_c08 bit
                    null,           // tst_c09 date
                    null,           // tst_c10 datetime
                    null,           // tst_c11 timestamp
                    null,           // tst_c12 time
                    null,           // tst_c13 year
                    null,           // tst_c14 char(10)       
                    null,           // tst_c15 varchar(10)
                    null,           // tst_c16 binary(10)
                    null,           // tst_c17 varbinary(10)
                    null,           // tst_c26 enum('a','b')
                    null );         // tst_c27 set('a','b')
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test a non-tinyint value for a tinyint column raises an exception.    
   * @expectedException Exception
   */                              
  public function testInvalidTinyint()
  {
    TST_DL::Test01(  null,          // tst_c00 int
                     null,          // tst_c01 smallint
                    'abc',          // tst_c02 tinyint
                     null,          // tst_c03 mediumint      
                     null,          // tst_c04 bigint
                     null,          // tst_c05 decimal(10,2)
                     null,          // tst_c06 float
                     null,          // tst_c07 double
                     null,          // tst_c08 bit
                     null,          // tst_c09 date
                     null,          // tst_c10 datetime
                     null,          // tst_c11 timestamp
                     null,          // tst_c12 time
                     null,          // tst_c13 year
                     null,          // tst_c14 char(10)       
                     null,          // tst_c15 varchar(10)
                     null,          // tst_c16 binary(10)
                     null,          // tst_c17 varbinary(10)
                     null,          // tst_c26 enum('a','b')
                     null );        // tst_c27 set('a','b')  
  }
     
  //--------------------------------------------------------------------------------------------------------------------
  /** Test mediumint is quoted properly.
   */
  public function testMediumint()
  {
    TST_DL::Test01( null,           // tst_c00 int
                    null,           // tst_c01 smallint
                    null,           // tst_c02 tinyint
                       1,           // tst_c03 mediumint      
                    null,           // tst_c04 bigint
                    null,           // tst_c05 decimal(10,2)
                    null,           // tst_c06 float
                    null,           // tst_c07 double
                    null,           // tst_c08 bit
                    null,           // tst_c09 date
                    null,           // tst_c10 datetime
                    null,           // tst_c11 timestamp
                    null,           // tst_c12 time
                    null,           // tst_c13 year
                    null,           // tst_c14 char(10)       
                    null,           // tst_c15 varchar(10)
                    null,           // tst_c16 binary(10)
                    null,           // tst_c17 varbinary(10)
                    null,           // tst_c26 enum('a','b')
                    null );         // tst_c27 set('a','b')
    $this->assertTrue( true );
    
    TST_DL::Test01( null,            // tst_c00 int
                    null,            // tst_c01 smallint
                    null,            // tst_c02 tinyint
                    '1',             // tst_c03 mediumint      
                    null,            // tst_c04 bigint
                    null,            // tst_c05 decimal(10,2)
                    null,            // tst_c06 float
                    null,            // tst_c07 double
                    null,            // tst_c08 bit
                    null,            // tst_c09 date
                    null,            // tst_c10 datetime
                    null,            // tst_c11 timestamp
                    null,            // tst_c12 time
                    null,            // tst_c13 year
                    null,            // tst_c14 char(10)       
                    null,            // tst_c15 varchar(10)
                    null,            // tst_c16 binary(10)
                    null,            // tst_c17 varbinary(10)
                    null,            // tst_c26 enum('a','b')
                    null );          // tst_c27 set('a','b')    
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test a non-mediumint value for a mediumint column raises an exception.    
   * @expectedException Exception
   */                              
  public function testInvalidSMediumint()
  {
    TST_DL::Test01( null,            // tst_c00 int
                    null,            // tst_c01 smallint
                    null,            // tst_c02 tinyint
                   'abc',            // tst_c03 mediumint      
                    null,            // tst_c04 bigint
                    null,            // tst_c05 decimal(10,2)
                    null,            // tst_c06 float
                    null,            // tst_c07 double
                    null,            // tst_c08 bit
                    null,            // tst_c09 date
                    null,            // tst_c10 datetime
                    null,            // tst_c11 timestamp
                    null,            // tst_c12 time
                    null,            // tst_c13 year
                    null,            // tst_c14 char(10)       
                    null,            // tst_c15 varchar(10)
                    null,            // tst_c16 binary(10)
                    null,            // tst_c17 varbinary(10)
                    null,            // tst_c26 enum('a','b')
                    null );          // tst_c27 set('a','b')     
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test bigint is quoted properly.
   */
  public function testBigint()
  {
    TST_DL::Test01( null,          // tst_c00 int
                    null,          // tst_c01 smallint
                    null,          // tst_c02 tinyint
                    null,          // tst_c03 mediumint      
                       1,          // tst_c04 bigint
                    null,          // tst_c05 decimal(10,2)
                    null,          // tst_c06 float
                    null,          // tst_c07 double
                    null,          // tst_c08 bit
                    null,          // tst_c09 date
                    null,          // tst_c10 datetime
                    null,          // tst_c11 timestamp
                    null,          // tst_c12 time
                    null,          // tst_c13 year
                    null,          // tst_c14 char(10)       
                    null,          // tst_c15 varchar(10)
                    null,          // tst_c16 binary(10)
                    null,          // tst_c17 varbinary(10)
                    null,          // tst_c26 enum('a','b')
                    null );        // tst_c27 set('a','b')
    $this->assertTrue( true );
    
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    '1',          // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test a non-bigint value for a bigint column raises an exception.    
   * @expectedException Exception
   */                              
  public function testInvalidBigint()
  {
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                   'abc',         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test decimal is quoted properly.
   */
  public function testDecimal()
  {
    TST_DL::Test01( null,          // tst_c00 int
                    null,          // tst_c01 smallint
                    null,          // tst_c02 tinyint
                    null,          // tst_c03 mediumint      
                    null,          // tst_c04 bigint
                       1,          // tst_c05 decimal(10,2)
                    null,          // tst_c06 float
                    null,          // tst_c07 double
                    null,          // tst_c08 bit
                    null,          // tst_c09 date
                    null,          // tst_c10 datetime
                    null,          // tst_c11 timestamp
                    null,          // tst_c12 time
                    null,          // tst_c13 year
                    null,          // tst_c14 char(10)       
                    null,          // tst_c15 varchar(10)
                    null,          // tst_c16 binary(10)
                    null,          // tst_c17 varbinary(10)
                    null,          // tst_c26 enum('a','b')
                    null );        // tst_c27 set('a','b')
    $this->assertTrue( true );
    
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                     '1',         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test a non-decimal value for a decimal column raises an exception.    
   * @expectedException Exception
   */                              
  public function testInvalidDecimal()
  {
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                   'abc',         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test float is quoted properly.
   */
  public function testFloat()
  {
    TST_DL::Test01( null,          // tst_c00 int
                    null,          // tst_c01 smallint
                    null,          // tst_c02 tinyint
                    null,          // tst_c03 mediumint      
                    null,          // tst_c04 bigint
                    null,          // tst_c05 decimal(10,2)
                       1,          // tst_c06 float
                    null,          // tst_c07 double
                    null,          // tst_c08 bit
                    null,          // tst_c09 date
                    null,          // tst_c10 datetime
                    null,          // tst_c11 timestamp
                    null,          // tst_c12 time
                    null,          // tst_c13 year
                    null,          // tst_c14 char(10)       
                    null,          // tst_c15 varchar(10)
                    null,          // tst_c16 binary(10)
                    null,          // tst_c17 varbinary(10)
                    null,          // tst_c26 enum('a','b')
                    null );        // tst_c27 set('a','b')
    $this->assertTrue( true );
    
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                     '1',         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test a non-float value for a float column raises an exception.    
   * @expectedException Exception
   */                              
  public function testInvalidFloat()
  {
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                   'abc',         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test double is quoted properly.
   */
  public function testDouble()
  {
    TST_DL::Test01( null,          // tst_c00 int
                    null,          // tst_c01 smallint
                    null,          // tst_c02 tinyint
                    null,          // tst_c03 mediumint      
                    null,          // tst_c04 bigint
                    null,          // tst_c05 decimal(10,2)
                    null,          // tst_c06 float
                       1,          // tst_c07 double
                    null,          // tst_c08 bit
                    null,          // tst_c09 date
                    null,          // tst_c10 datetime
                    null,          // tst_c11 timestamp
                    null,          // tst_c12 time
                    null,          // tst_c13 year
                    null,          // tst_c14 char(10)       
                    null,          // tst_c15 varchar(10)
                    null,          // tst_c16 binary(10)
                    null,          // tst_c17 varbinary(10)
                    null,          // tst_c26 enum('a','b')
                    null );        // tst_c27 set('a','b')
    $this->assertTrue( true );
    
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                     '1',         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test a non-double value for a double column raises an exception.    
   * @expectedException Exception
   */                              
  public function testInvalidDouble()
  {
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                   'abc',         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test bit is quoted properly.
   */
  public function testBit()
  {    
    TST_DL::Test01( null,          // tst_c00 int
                    null,          // tst_c01 smallint
                    null,          // tst_c02 tinyint
                    null,          // tst_c03 mediumint      
                    null,          // tst_c04 bigint
                    null,          // tst_c05 decimal(10,2)
                    null,          // tst_c06 float
                    null,          // tst_c07 double
                     101,          // tst_c08 bit
                    null,          // tst_c09 date
                    null,          // tst_c10 datetime
                    null,          // tst_c11 timestamp
                    null,          // tst_c12 time
                    null,          // tst_c13 year
                    null,          // tst_c14 char(10)       
                    null,          // tst_c15 varchar(10)
                    null,          // tst_c16 binary(10)
                    null,          // tst_c17 varbinary(10)
                    null,          // tst_c26 enum('a','b')
                    null );        // tst_c27 set('a','b')
    $this->assertTrue( true );
    
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                  '1010',         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test a non-bit value for a bit column raises an exception.    
   * @expectedException Exception
   */                              
  public function testInvalidBit()
  {
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                   'abc',         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')
  }
   
  //--------------------------------------------------------------------------------------------------------------------
  /** Test date is quoted properly.
   */
  public function testDate()
  {            
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
            '2000-01-01',         // tst_c09 date
                    null,        // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test datetime is quoted properly.
   */
  public function testDatetime()
  {            
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
   '2000-01-01 10:00:00',         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test timestamp is quoted properly.
   */
  public function testTimestamp()
  {            
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                 2102101,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test a non-timestamp value for a timestamp column raises an exception.    
   * @expectedException Exception
   */                              
  public function testInvalidTimestamp()
  {
    TST_DL::Test01( null,         // tst_c00 int          
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                   'abc',         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test time is quoted properly.
   */
  public function testTime()
  {            
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
              '10:00:00',         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test year is quoted properly.
   */
  public function testYear()
  {
    TST_DL::Test01( null,          // tst_c00 int
                    null,          // tst_c01 smallint
                    null,          // tst_c02 tinyint
                    null,          // tst_c03 mediumint      
                    null,          // tst_c04 bigint
                    null,          // tst_c05 decimal(10,2)
                    null,          // tst_c06 float
                    null,          // tst_c07 double
                    null,          // tst_c08 bit
                    null,          // tst_c09 date
                    null,          // tst_c10 datetime
                    null,          // tst_c11 timestamp
                    null,          // tst_c12 time
                    2020,          // tst_c13 year
                    null,          // tst_c14 char(10)       
                    null,          // tst_c15 varchar(10)
                    null,          // tst_c16 binary(10)
                    null,          // tst_c17 varbinary(10)
                    null,          // tst_c26 enum('a','b')
                    null );        // tst_c27 set('a','b')
    $this->assertTrue( true );
    
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                  '2020',         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test a non-year value for a year column raises an exception.    
   * @expectedException Exception
   */                              
  public function testInvalidYear()
  {
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                   'abc',         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')
  }  
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test char is quoted properly.
   */
  public function testChar()
  {            
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                      10,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
    

    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                   '101',         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );

    
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                   'abc',         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Test varchar is quoted properly.
   */
  public function testVarchar()
  {            
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                      10,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
    

    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                   '101',         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
  
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                   'abc',         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
 }

 //--------------------------------------------------------------------------------------------------------------------
  /** Test binary is quoted properly.
   */
  public function testBinary()
  {    
    TST_DL::Test01( null,          // tst_c00 int
                    null,          // tst_c01 smallint
                    null,          // tst_c02 tinyint
                    null,          // tst_c03 mediumint      
                    null,          // tst_c04 bigint
                    null,          // tst_c05 decimal(10,2)
                    null,          // tst_c06 float
                    null,          // tst_c07 double
                    null,          // tst_c08 bit
                    null,          // tst_c09 date
                    null,          // tst_c10 datetime
                    null,          // tst_c11 timestamp
                    null,          // tst_c12 time
                    null,          // tst_c13 year
                    null,          // tst_c14 char(10)       
                    null,          // tst_c15 varchar(10)
                    1010,          // tst_c16 binary(10)
                    null,          // tst_c17 varbinary(10)
                    null,          // tst_c26 enum('a','b')
                    null );        // tst_c27 set('a','b')
    $this->assertTrue( true );
    
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                   '1010',         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
        
    TST_DL::Test01( null,                       // tst_c00 int
                    null,                       // tst_c01 smallint
                    null,                       // tst_c02 tinyint
                    null,                       // tst_c03 mediumint      
                    null,                       // tst_c04 bigint
                    null,                       // tst_c05 decimal(10,2)
                    null,                       // tst_c06 float
                    null,                       // tst_c07 double
                    null,                       // tst_c08 bit
                    null,                       // tst_c09 date
                    null,                       // tst_c10 datetime
                    null,                       // tst_c11 timestamp
                    null,                       // tst_c12 time
                    null,                       // tst_c13 year
                    null,                       // tst_c14 char(10)       
                    null,                       // tst_c15 varchar(10)
                    "\xFF\x7F\x80\x5c\x00\x10", // tst_c16 binary(10)
                    null,                       // tst_c17 varbinary(10)
                    null,                       // tst_c26 enum('a','b')
                    null );                     // tst_c27 set('a','b')      
    $this->assertTrue( true );  
  }
  
 //--------------------------------------------------------------------------------------------------------------------
  /** Test varbinary is quoted properly.
   */
  public function testVarbinary()
  {    
    TST_DL::Test01( null,          // tst_c00 int
                    null,          // tst_c01 smallint
                    null,          // tst_c02 tinyint
                    null,          // tst_c03 mediumint      
                    null,          // tst_c04 bigint
                    null,          // tst_c05 decimal(10,2)
                    null,          // tst_c06 float
                    null,          // tst_c07 double
                    null,          // tst_c08 bit
                    null,          // tst_c09 date
                    null,          // tst_c10 datetime
                    null,          // tst_c11 timestamp
                    null,          // tst_c12 time
                    null,          // tst_c13 year
                    null,          // tst_c14 char(10)       
                    null,          // tst_c15 varchar(10)
                    null,          // tst_c16 binary(10)
                    1010,          // tst_c17 varbinary(10)
                    null,          // tst_c26 enum('a','b')
                    null );        // tst_c27 set('a','b')
    $this->assertTrue( true );
    
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                   '1010',        // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
    
    TST_DL::Test01( null,                       // tst_c00 int
                    null,                       // tst_c01 smallint
                    null,                       // tst_c02 tinyint
                    null,                       // tst_c03 mediumint      
                    null,                       // tst_c04 bigint
                    null,                       // tst_c05 decimal(10,2)
                    null,                       // tst_c06 float
                    null,                       // tst_c07 double
                    null,                       // tst_c08 bit
                    null,                       // tst_c09 date
                    null,                       // tst_c10 datetime
                    null,                       // tst_c11 timestamp
                    null,                       // tst_c12 time
                    null,                       // tst_c13 year
                    null,                       // tst_c14 char(10)       
                    null,                       // tst_c15 varchar(10)
                    null,                       // tst_c16 binary(10)
                    "\xFF\x7F\x80\x5c\x00\x10", // tst_c17 varbinary(10)
                    null,                       // tst_c26 enum('a','b')
                    null );                     // tst_c27 set('a','b')      
    $this->assertTrue( true );    
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Test enum is quoted properly.
   */
  public function testEnum()
  {    
    TST_DL::Test01( null,          // tst_c00 int
                    null,          // tst_c01 smallint
                    null,          // tst_c02 tinyint
                    null,          // tst_c03 mediumint      
                    null,          // tst_c04 bigint
                    null,          // tst_c05 decimal(10,2)
                    null,          // tst_c06 float
                    null,          // tst_c07 double
                    null,          // tst_c08 bit
                    null,          // tst_c09 date
                    null,          // tst_c10 datetime
                    null,          // tst_c11 timestamp
                    null,          // tst_c12 time
                    null,          // tst_c13 year
                    null,          // tst_c14 char(10)       
                    null,          // tst_c15 varchar(10)
                    null,          // tst_c16 binary(10)
                    null,          // tst_c17 varbinary(10)
                     'a',          // tst_c26 enum('a','b')
                    null );        // tst_c27 set('a','b')
    $this->assertTrue( true );
    
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                     'b',         // tst_c26 enum('a','b')
                    null );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Test set is quoted properly.
   */
  public function testSet()
  {    
    TST_DL::Test01( null,          // tst_c00 int
                    null,          // tst_c01 smallint
                    null,          // tst_c02 tinyint
                    null,          // tst_c03 mediumint      
                    null,          // tst_c04 bigint
                    null,          // tst_c05 decimal(10,2)
                    null,          // tst_c06 float
                    null,          // tst_c07 double
                    null,          // tst_c08 bit
                    null,          // tst_c09 date
                    null,          // tst_c10 datetime
                    null,          // tst_c11 timestamp
                    null,          // tst_c12 time
                    null,          // tst_c13 year
                    null,          // tst_c14 char(10)       
                    null,          // tst_c15 varchar(10)
                    null,          // tst_c16 binary(10)
                    null,          // tst_c17 varbinary(10)
                    null,          // tst_c26 enum('a','b')
                     'a' );        // tst_c27 set('a','b')
    $this->assertTrue( true );
    
    TST_DL::Test01( null,         // tst_c00 int
                    null,         // tst_c01 smallint
                    null,         // tst_c02 tinyint
                    null,         // tst_c03 mediumint      
                    null,         // tst_c04 bigint
                    null,         // tst_c05 decimal(10,2)
                    null,         // tst_c06 float
                    null,         // tst_c07 double
                    null,         // tst_c08 bit
                    null,         // tst_c09 date
                    null,         // tst_c10 datetime
                    null,         // tst_c11 timestamp
                    null,         // tst_c12 time
                    null,         // tst_c13 year
                    null,         // tst_c14 char(10)       
                    null,         // tst_c15 varchar(10)
                    null,         // tst_c16 binary(10)
                    null,         // tst_c17 varbinary(10)
                    null,         // tst_c26 enum('a','b')
                     'b' );       // tst_c27 set('a','b')      
    $this->assertTrue( true );
  }
    
  //--------------------------------------------------------------------------------------------------------------------
}         

//----------------------------------------------------------------------------------------------------------------------