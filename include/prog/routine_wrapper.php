<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * @author $Author: water $
 *
 * @par Copyright:
 * SET
 *
 * @date $Date: 2013-02-05 20:58:44 +0100 (Tue, 05 Feb 2013) $
 *
 * @version $Revision: 68 $
 */

//----------------------------------------------------------------------------------------------------------------------
/** Throws an execption. Thakes arguments similair to printf.
 */   
function set_assert_failed()
{
  $args    = func_get_args();
  $format  = array_shift( $args );
  $message = vsprintf( $format,  $args );

  throw new Exception( $message );
}

//---------------------------------------------------------------------------------------------------------------------- 
/** @brief abstract supper class for generation stored routine wrapper methods based on the type of the stored routine. 
 */
abstract class SET_RoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /** The constant contain width page (in chars).
   */
  const C_PAGE_WIDTH = 120;

  /** The current level of indentation in the generated code.
   */
  private $myIndentLevel = 1;

  /** Buffer for generated code.
   */
  private $myCode = '';

  //--------------------------------------------------------------------------------------------------------------------
  /** Appends @a $theString to @c $myCode.
   */
  protected function Write( $theString )
  {
    $this->myCode .= $theString;
  }
   
  //--------------------------------------------------------------------------------------------------------------------
  /** Appends @a $theString and a LF to @c $myCode.
      - @a $theString must not contain a LF.
      - Indent level is increased or decreased as @a $theString equals to '{' or '}'.
   */
  protected function WriteLine( $theString=false )
  {
    if ($theString)
    {
      if (trim($theString)=='}') $this->myIndentLevel--;
      for( $i=0; $i<2*$this->myIndentLevel; $i++ ) $this->Write( ' ' );
      $this->myCode .= $theString;
      $this->myCode .= "\n";
      if (trim($theString)=='{') $this->myIndentLevel++;
    }
    else
    {
      $this->myCode .= "\n";
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Appends a comment line to @c $myCode.
   */
  protected function WriteSeparator()
  {
    for( $i=0; $i<2*$this->myIndentLevel; $i++ )
    {
      $this->Write( ' ' );
    }

    $this->Write( '//' );

    for( $i=0; $i<(self::C_PAGE_WIDTH-2*$this->myIndentLevel-2-1); $i++ )
    {
      $this->Write( '-' );
    }
    $this->WriteLine();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Returns @a $theSqlFunctionName after the first underscore in camel case.
      E.g. set_foo_foo => FooFoo.
   */
  private function GetWrapperRoutineName( $theSqlFunctionName )
  {
    $name = preg_replace( '/(_)([a-z])/e', "strtoupper('\\2')", stristr( $theSqlFunctionName, '_' ) );

    return $name;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for escaping the arguments of a stored routine. 
      @param $theArgsTypes An array with the arguments of a strored routine.
   */
  private function WriteEscapedArgs( $theArgsTypes )
  {
    foreach( $theArgsTypes as $i => $arg_type )
    {
      switch ($arg_type)
      {
      case 'tinyint':
      case 'smallint':
      case 'mediumint':
      case 'int':
      case 'bigint':

      case 'decimal':
      case 'float':
      case 'double':
        $this->WriteLine( '$arg'.$i.' = self::QuoteNum($theArg'.$i.');' );
        break;

      case 'char':
      case 'varchar':
        $this->WriteLine( '$arg'.$i.' = self::QuoteString($theArg'.$i.');' );
        break;

      case 'date':
      case 'datetime':
        $this->WriteLine( '$arg'.$i.' = self::QuoteString($theArg'.$i.');' );
        break;
  
      default:
        set_assert_failed( "Unknown arg type '$arg_type'." );
      }
    }
    if (sizeof($theArgsTypes)>0) $this->WriteLine();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for the arguments of the wrapper method for @a $theRoutine.
      @param $theRoutine An arry with the argument types of the stored routine.
   */
  private function GetWrapperArgs( $theRoutine )
  {
    if ($theRoutine['argument_types']) $argument_types = explode( ',', $theRoutine['argument_types'] );
    else                               $argument_types = array();

    if ($theRoutine['type']=='bulk') $ret = '$theBulkHandler';
    else                             $ret = '';

    foreach( $argument_types as $i => $arg_type )
    {
      if ($ret) $ret .= ',';
      switch ($arg_type)
      {
      case 'tinyint':
      case 'smallint':
      case 'mediumint':
      case 'int':
      case 'bigint':

      case 'decimal':
      case 'float':
      case 'double':
        $ret .= '$theArg'.$i;
        break;

      case 'char':
      case 'varchar':
        $ret .= '$theArg'.$i;
        break;

      case 'date':
      case 'datetime':
        $ret .= '$theArg'.$i;
        break;
      
      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':
        $ret .= '$theArg'.$i;
        break;  
        
      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob': 
        $ret .= '$theArg'.$i;
        break;               
        
      default:
        set_assert_failed( "Unknown arg type '$arg_type'." );
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for the arguments for calling the stored routine in a wrapper method.
      @param $theArgsTypes array with the argument type of a Stored Routine.
   */
  protected function GetRoutineArgs( $theArgsTypes )
  {
    $ret = '';
    foreach( $theArgsTypes as $i => $arg_type )
    {
      if ($ret) $ret .= ',';
      switch ($arg_type)
      {
      case 'tinyint':
      case 'smallint':
      case 'mediumint':
      case 'int':
      case 'bigint':

      case 'decimal':
      case 'float':
      case 'double':
        $ret .= '$arg'.$i;
        break;

      case 'char':
      case 'varchar':
        $ret .= '$arg'.$i;
        break;

      case 'date':
      case 'datetime':
        $ret .= '$arg'.$i;
        break;
      
      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':
        $ret .= '$arg'.$i;
        break;  
        
      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob': 
        $ret .= '$arg'.$i;
        break;

      default:
        mmm_assert_failed( "Unknown arg type '$arg_type'." );
      }
    }

    return $ret;
  }
 
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for the arguments for calling the stored routine in a wrapper method.
      @param $theArgsTypes array with the argument type of a Stored Routine.
   */
  protected function GetTypeForLob( $theType )
  {
   $ret = '';
    switch ($theType)
    {
      case 'tinyint':
      case 'smallint':
      case 'mediumint':
      case 'int':
      case 'bigint':
        $ret = 'i';
        break;  
      
      case 'decimal':
      case 'float':
      case 'double':
        $ret = 'd';
        break;
    
      case 'char':
      case 'varchar':
      case 'date':
      case 'datetime':
        $ret = 's';
        break;  
          
      case 'tinytext':
      case 'text':
      case 'mediumtext':
      case 'longtext':        
      case 'tinyblob':
      case 'blob':
      case 'mediumblob':
      case 'longblob': 
        $ret .= 'b';
        break;
  
      default:
          set_assert_failed( "Unknown type '%s'.", $theType );
    }
    
    return $ret;
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Genereert een volledige wrapper methode voor een Stored Routine.
      @param $theRoutine Een rij uit tabel DEV_ROUTINE.
   */
  public function WriteRoutineFunctionWithoutLob( $theRoutine )
  { 
    if ($theRoutine['argument_types']) $argument_types = explode( ',', $theRoutine['argument_types'] );
    else                               $argument_types = array();

    $wrapper_function_name = $this->GetWrapperRoutineName( $theRoutine['routine_name'] );
    $wrapper_args          = $this->GetWrapperArgs( $theRoutine );

    $this->WriteSeparator();
    $this->WriteLine( '/** @sa Stored Routine '.$theRoutine['routine_name'].'.' );
    $this->WriteLine( ' */' );
    $this->WriteLine( 'static function '.$wrapper_function_name.'('.$wrapper_args.')' );
    $this->WriteLine( '{' );
    $this->WriteEscapedArgs( $argument_types );
    $this->WriteResultHandler( $theRoutine, $argument_types );
    $this->WriteLine( '}' );
    $this->WriteLine();
    
    return $this->myCode;
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates a complete wrapper method.
      @param $theRoutine Metadata of the stored routine. 
   */
  public function WriteRoutineFunctionWithLob( $theRoutine )
  {
    if ($theRoutine['argument_types']) $argument_types = explode( ',', $theRoutine['argument_types'] );
    else                               $argument_types = array();

    $wrapper_function_name = $this->GetWrapperRoutineName( $theRoutine['routine_name'] );
    $wrapper_args          = $this->GetWrapperArgs( $theRoutine );

    $this->WriteSeparator();
    $this->WriteLine( '/** @sa Stored Routine '.$theRoutine['routine_name'].'.' );
    $this->WriteLine( ' */' );
    $this->WriteLine( 'static function '.$wrapper_function_name.'('.$wrapper_args.')' );
    $this->WriteLine( '{' );
     
    $i = 0;
    $types = '';
    $num_arg = '';
    $arguments_types = '';
     
    foreach( $argument_types as $theType )
    {
     $types .= $this->GetTypeForLob( $theType );
     $type = $this->GetTypeForLob( $theType );
     
     if ($i!=count($argument_types)-1)
      {
        $num_arg .= '?,';
        if ($type=='b') $arguments_types .='$null, ';
        else $arguments_types .= '$theArg'.$i.", ";
      }
      else 
      {
        $num_arg .= '?';    
        if ($type=='b') $arguments_types .='$null';
        else $arguments_types .= '$theArg'.$i;
      }
      $i++;
    } 
    
    
    $this->Writeline( '$query = "CALL '.$theRoutine['routine_name'].'( '.$num_arg.' )";' );
    $this->Writeline( '$stmt  = self::$ourMySql->prepare( $query );' );
    $this->Writeline( 'if (!$stmt) self::ThrowSqlError( \'prepare failed\' );' );
    $this->WriteLine();  
    //
    $this->WriteLine( '$null = null;' );  
    
    $this->Writeline( '$b = $stmt->bind_param( \''.$types.'\', '.$arguments_types.' );' );    
    $this->Writeline( 'if (!$b) self::ThrowSqlError( \'bind_param failed\' );' );
    $this->WriteLine();

    foreach( $argument_types as $i => $argument )
    {
      if ($this->GetTypeForLob( $argument )=='b')
      {
        $this->Writeline( '$n = strlen( $theArg'.$i.' );' );      
        $this->Writeline( '$p = 0;' );        
        $this->Writeline( 'while ($p<$n)' );
        $this->Writeline( '{' ); 
        $this->Writeline( '$b = $stmt->send_long_data( '.$i.', substr( $theArg'.$i.', $p, MMM_MYSQL_MAX_ALLOWED_PACKET ) );' );
        $this->Writeline( 'if (!$b) self::ThrowSqlError( \'send_long_data failed\' );' );
        $this->Writeline( '$p += MMM_MYSQL_MAX_ALLOWED_PACKET;' );
        $this->Writeline( '}' );
        $this->WriteLine();
      }
    } 
    
    $this->Writeline( '$b = $stmt->execute();' );
    $this->Writeline( 'if (!$b) self::ThrowSqlError( \'execute failed\' );' );
    $this->WriteLine(); 
    $this->Writeline( '$row = array();' );
    $this->Writeline( 'self::stmt_bind_assoc( $stmt, $row );' );
    $this->WriteLine();   
    $this->WriteRoutineFunctionLobFetchData();
    $this->Writeline( '$stmt->close();' );
    $this->Writeline( 'self::$ourMySql->next_result();' );
    $this->WriteLine();   
    $this->WriteRoutineFunctionLobReturnData();
    $this->WriteLine( '}' );
    $this->WriteLine();
    
    return $this->myCode;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Returns @c true if one of the arguments is a BLOB or CLOB.
      @param $theArgumentsType An aaray with the argument types.
   */
  public function IsBlobArgument( $theArgumentsType )
  {
    $has_blob = false;
    foreach( $theArgumentsType as $argument_type )
    {
      switch ($argument_type)
      {
        case 'tinytext':
        case 'text':
        case 'mediumtext':
        case 'longtext':
        
        case 'tinyblob':
        case 'blob':
        case 'mediumblob':
        case 'longblob':   
          
          $has_blob = true; 
          break;
      }
    }
    
    return $has_blob;
  }
          
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates a complete wrapper method.
      @param $theRoutine Metadata of the stored routine. 
   */
  public function WriteRoutineFunction( $theRoutine )
  {
    $theArgumentsType = explode( ',', $theRoutine['argument_types'] );
    
    $has_blob = $this->IsBlobArgument( $theArgumentsType );
       
    if ($has_blob==true)
    {
      return $this->WriteRoutineFunctionWithLob( $theRoutine );
    }
    else
    {
      return $this->WriteRoutineFunctionWithoutLob( $theRoutine );
    }
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for calling the stored routine in the wrapper method.
      @param $theRoutine       An array with the metadata of the stored routine.
      @param $theArgumentTypes An array with the arguments types of the stored routine.
   */
  abstract protected function WriteResultHandler( $theRoutine, $theArgumentTypes );
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for fetching data of a stored routine with one or more LOB arguments.
   */
  /*abstract*/ protected function WriteRoutineFunctionLobFetchData()
  {
    $this->Writeline( '$b = $stmt->fetch();' );
    $this->Writeline( 'if ($b===false) self::ThrowSqlError( \'mysqli_stmt::fetch failed\' );' );
    $this->WriteLine(); 
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for returing the data returend by a stored routine with one or more LOB arguments.
   */
  /*abstract*/ protected function WriteRoutineFunctionLobReturnData()
  {
    $this->Writeline( 'return $row[key($row)];' );
  }
  
 //--------------------------------------------------------------------------------------------------------------------
 /** A factory for creating the appropiate object for generating code for the stored routine @a $theRoutine.
  */  
  static public function CreateRoutineWrapper( $theRoutine )
  {   
    switch ($theRoutine['type'])
    {
    case 'bulk':
      $class = 'SET_RoutineWrapperBulk';
      break;
      
    case 'log':
      $class = 'SET_RoutineWrapperLog';
      break;
      
    case 'none':
      $class = 'SET_RoutineWrapperNone';
      break;

    case 'row0':
      $class = 'SET_RoutineWrapperRow0';
      break;       

    case 'row1':
      $class = 'SET_RoutineWrapperRow1';
      break; 
      
    case 'rows':
      $class = 'SET_RoutineWrapperRows';
      break;
      
    case 'rows_with_key':
      $class = 'SET_RoutineWrapperRowsWithKey';
      break;

    case 'rows_with_index':
      $class = 'SET_RoutineWrapperRowsWithIndex';
      break;      
      
    case 'singleton0':
      $class = 'SET_RoutineWrapperSingleton0';
      break;
    
    case 'singleton1':
      $class = 'SET_RoutineWrapperSingleton1';
      break;      

    default:
      set_assert_failed( "Unknown routine type '%s'.", $theRoutine['columns'] );
    }

    $wrapper = new $class();
    
    return $wrapper;
  } 
    
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------  
/** @brief Class for generating a wrapper function around a stored procedure that uses for large volumes of data.
 */  
class SET_RoutineWrapperBulk extends SET_RoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------  
  /** Generates code for calling the stored routine in the wrapper method.
      @param $theRoutine       An array with the metadata of the stored routine.
      @param $theArgumentTypes An array with the arguments types of the stored routine.
   */ 
  protected function WriteResultHandler( $theRoutine, $theArgumentTypes )
  {
    $routine_args = $this->GetRoutineArgs( $theArgumentTypes );
    $this->WriteLine( 'self::ExecuteBulk( $theBulkHandler, "CALL '.$theRoutine['routine_name'].'('.$routine_args.')" );' );
  } 
  
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------  
/** @brief Class for generating a wrapper function around a stored procedure that returns nothing but writes the rows of
 *  the results sets to the standard out.
 */  
class SET_RoutineWrapperLog extends SET_RoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------  
  /** Generates code for calling the stored routine in the wrapper method.
     @param $theRoutine       An array with the metadata of the stored routine.
     @param $theArgumentTypes An array with the arguments types of the stored routine.
   */
  protected function WriteResultHandler( $theRoutine, $theArgumentTypes )
  {
    $routine_args = $this->GetRoutineArgs( $theArgumentTypes );
    $this->WriteLine( 'self::ExecuteEcho( "CALL '.$theRoutine['routine_name'].'('.$routine_args.')" );' );
  } 
  
  //--------------------------------------------------------------------------------------------------------------------
}


//----------------------------------------------------------------------------------------------------------------------  
/** @brief Class for generating a wrapper function around a stored procedure that doesn't return anything.
 */  
class SET_RoutineWrapperNone extends SET_RoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for calling the stored routine in the wrapper method.
      @param $theRoutine       An array with the metadata of the stored routine.
      @param $theArgumentTypes An array with the arguments types of the stored routine.
   */ 
  protected function WriteResultHandler( $theRoutine, $theArgumentTypes )
  {
    $routine_args = $this->GetRoutineArgs( $theArgumentTypes );
    $this->WriteLine( 'return self::ExecuteNone( "CALL '.$theRoutine['routine_name'].'('.$routine_args.')" );' );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------  
/** @brief Class for generating a wrapper function around a stored procedure that selects 0 or 1 rows. 
 */  
class SET_RoutineWrapperRow0 extends SET_RoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for calling the stored routine in the wrapper method.
      @param $theRoutine       An array with the metadata of the stored routine.
      @param $theArgumentTypes An array with the arguments types of the stored routine.
   */
  protected function WriteResultHandler( $theRoutine, $theArgumentTypes )
  {
    $routine_args = $this->GetRoutineArgs( $theArgumentTypes );
    $this->WriteLine( 'return self::ExecuteRow01( "CALL '.$theRoutine['routine_name'].'('.$routine_args.')" );' );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------  
/** @brief Class for generating a wrapper function around a stored procedure that selects 1 and only 1 rows. 
 */  
class SET_RoutineWrapperRow1 extends SET_RoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for calling the stored routine in the wrapper method.
      @param $theRoutine       An array with the metadata of the stored routine.
      @param $theArgumentTypes An array with the arguments types of the stored routine.
   */
  protected function WriteResultHandler( $theRoutine, $theArgumentTypes )
  {
    $routine_args = $this->GetRoutineArgs( $theArgumentTypes );
    $this->WriteLine( 'return self::ExecuteRow1( "CALL '.$theRoutine['routine_name'].'('.$routine_args.')" );' );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------  
/** @brief Class for generating a wrapper function around a stored procedure that selects 0 or more rows. 
 */  
class SET_RoutineWrapperRows extends SET_RoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for calling the stored routine in the wrapper method.
      @param $theRoutine       An array with the metadata of the stored routine.
      @param $theArgumentTypes An array with the arguments types of the stored routine.
   */
  protected function WriteResultHandler( $theRoutine, $theArgumentTypes )
  {
    $routine_args = $this->GetRoutineArgs( $theArgumentTypes );
    $this->WriteLine( 'return self::ExecuteRows( "CALL '.$theRoutine['routine_name'].'('.$routine_args.')" );' );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
}


//----------------------------------------------------------------------------------------------------------------------  
/** @brief Class for generating a wrapper function around a stored procedure that selects rows on keys. 
 */  
class SET_RoutineWrapperRowsWithKey extends SET_RoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for calling the stored routine in the wrapper method.
      @param $theRoutine       An array with the metadata of the stored routine.
      @param $theArgumentTypes An array with the arguments types of the stored routine.
   */
  protected function WriteResultHandler( $theRoutine, $theArgumentTypes )
  {
    $routine_args = $this->GetRoutineArgs( $theArgumentTypes );
    $key = '';
    foreach( $theRoutine['columns'] as $column ) $key .= '[$row[\''.$column.'\']]';
    
    $this->WriteLine( '$result = self::Query( "CALL '.$theRoutine['routine_name'].'('.$routine_args.')" );' );
    $this->WriteLine( '$ret = array();' );
    $this->WriteLine( 'while($row = $result->fetch_array( MYSQLI_ASSOC )) $ret'.$key.' = $row;' );
    $this->WriteLine( '$result->close();' );
    $this->WriteLine( 'self::$ourMySql->next_result();' );
    $this->WriteLine( 'return  $ret' );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------  
/** @brief Class for generating a wrapper function around a stored procedure that selects rows on index.
 */  
class SET_RoutineWrapperRowsWithIndex extends SET_RoutineWrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for calling the stored routine in the wrapper method.
      @param $theRoutine       An array with the metadata of the stored routine.
      @param $theArgumentTypes An array with the arguments types of the stored routine.
   */
  protected function WriteResultHandler( $theRoutine, $theArgumentTypes )
  {
    $routine_args = $this->GetRoutineArgs( $theArgumentTypes );
    $index = '';
    foreach( $theRoutine['columns'] as $column ) $index .= '[$row[\''.$column.'\']]';

    $this->WriteLine( '$result = self::Query( "CALL '.$theRoutine['routine_name'].'('.$routine_args.')" );' ); 
    $this->WriteLine( '$ret = array();' );
    $this->WriteLine( 'while($row = $result->fetch_array( MYSQLI_ASSOC )) $ret'.$index.'[] = $row;' );
    $this->WriteLine( '$result->close();' );
    $this->WriteLine( 'self::$ourMySql->next_result();' );
    $this->WriteLine( 'return $ret;' );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------  
/** @brief Class for generating a wrapper function around a stored procedure that returns a scalar or 0.
 */  
class SET_RoutineWrapperSingleton0 extends SET_RoutineWrapper
{   
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for calling the stored routine in the wrapper method.
      @param $theRoutine       An array with the metadata of the stored routine.
      @param $theArgumentTypes An array with the arguments types of the stored routine.
   */
  protected function WriteResultHandler( $theRoutine, $theArgumentTypes )
  {
    $routine_args = $this->GetRoutineArgs( $theArgumentTypes );
    $this->WriteLine( 'return self::ExecuteSingleton01( "CALL '.$theRoutine['routine_name'].'('.$routine_args.')" );' );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------  
/** @brief Class for generating a wrapper function around a stored procedure that returns a scalar.
 */  
class SET_RoutineWrapperSingleton1 extends SET_RoutineWrapper
{   
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates code for calling the stored routine in the wrapper method.
      @param $theRoutine       An array with the metadata of the stored routine.
      @param $theArgumentTypes An array with the arguments types of the stored routine.
   */
  protected function WriteResultHandler( $theRoutine, $theArgumentTypes )
  {
    $routine_args = $this->GetRoutineArgs( $theArgumentTypes );
    $this->WriteLine( 'return self::ExecuteSingleton( "CALL '.$theRoutine['routine_name'].'('.$routine_args.')" );' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function WriteRoutineFunctionLobFetchData()
  {
    $this->Writeline( '$b = $stmt->fetch();' );
    $this->Writeline( 'if ($b===false) self::ThrowSqlError( \'mysqli_stmt::fetch failed\' );' );
    $this->WriteLine(); 
    
    /** @todo Test only one row (and not more) in the result set. */ 
    /** @todo Test row has only one column. */ 
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  protected function WriteRoutineFunctionLobReturnData()
  {
    $this->Writeline( 'return $row[key($row)];' );
  }  
  
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
/** @brief Klasse voor een programma voor het aanmaken aan een klasse met wrappers functies voor Stored Routines in het
    SET schema.
 */
class SET_ProgRoutineWrapper
{ 
  /** Processed code function.
   */
  private $myCode = '' ;

  /** The filename of the template.
   */
  private $myTemplateFileName;

  /** The filename where the generated wrapper class must be stored
   */
  private $myWrapperFileName;

  /** The filename of the file with the metadata of all stored procedures.
   */
  private $myMetadataFilename;

  /** The filename of the configuration file.
   */
  private $myConfigurationFileName;
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Generates a complete wrapper method for a Stored Routine.
      @param $theRoutine The row from table DEV_ROUTINE.
   */
  private function WriteRoutineFunction( $theRoutine )
  {
    $wrapper = SET_RoutineWrapper::CreateRoutineWrapper( $theRoutine );

    $this->myCode .= $wrapper->WriteRoutineFunction( $theRoutine );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Returns the metadata @a $routines of stored routines stored in file.
   */
  private function ReadRoutineMetaData()
  {
    $theFileName = $this->myMetadataFilename;
    
    $handle = fopen( $theFileName, 'r' );
    if ($handle===null) set_assert_failed( "Unable to open file '%s'.", $theFileName );
     
    // Skip header row.
    fgetcsv( $handle, 1000, ',' );
    $line_number = 1;
    
    while (($row = fgetcsv( $handle, 1000, ',' ))!==false)
    {  
      $line_number++;
      
      // Test the number of fields in the row.
      $n = sizeof( $row );
      if ($n!=4)
      {
        set_assert_failed( "Error at line %d in file '%s'. Expecting %d fields but found %d fields.",
                           $line_number,
                           $theFileName,
                           4,
                           $n );
      }
        
      $routines[] = array( 'routine_name'   => $row[0],
                           'type'           => $row[1],
                           'argument_types' => $row[2],
                           'columns'        => explode( ',',$row[3] ) );
    }
    
    $err = fclose( $handle );
    if ($err===false) set_assert_failed( "Error closing file '%s'.", $theFileName );
      
    return $routines;  
   }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Getting parameters from a array @a $theSettings whit key @c $theSectionName and @c $theSettingName. 
   */
  private function GetSetting( $theSettings, $theSectionName, $theSettingName )
  {
    // Test if the section exists.
    if (!array_key_exists( $theSectionName, $theSettings ))
    {
      set_assert_failed( "Section '%s' not found in configuration file '%s'.", 
                         $theSectionName, 
                         $this->myConfigurationFileName );
    }
  
    // Test if the setting in the section exists.
    if (!array_key_exists(  $theSettingName, $theSettings[$theSectionName] ))
    {
      set_assert_failed( "Setting '%s' not found in section '%s' configuration file '%s'.", 
                         $theSettingName, 
                         $theSectionName,
                         $this->myConfigurationFileName ); 
    }
    
    return $theSettings[$theSectionName][$theSettingName];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Read parameters from the configuration file @c $myConfigurationFileName.
   */
  private function ReadConfigurationFile()  
  {
    $settings = parse_ini_file( $this->myConfigurationFileName, true ); 
    if ($settings===false) set_assert_failed( "Unable open configuration file" );  
    
    $this->myTemplateFileName = $this->GetSetting( $settings, 'wrapper', 'template' );
    $this->myWrapperFileName  = $this->GetSetting( $settings, 'wrapper', 'wrapper' );
    $this->myMetadataFilename = $this->GetSetting( $settings, 'wrapper', 'metadata');    
   }  
    
  //--------------------------------------------------------------------------------------------------------------------
  /** Construction class for stored routine wrapper.
      @param $ConfigurationFileName The path file configuration.
   */
  public function Run( $ConfigurationFileName )
  { 
    $this->myConfigurationFileName = $ConfigurationFileName;
    
    $this->ReadConfigurationFile(); 
    
    $routines = $this->ReadRoutineMetaData();  
        
    foreach( $routines as $routine )
    {
      $this->WriteRoutineFunction( $routine );
    }

    $replace['  /* AUTO_GENERATED_ROUINE_WRAPPERS */'] =  $this->myCode;

    $code = file_get_contents( $this->myTemplateFileName );
    if ($code===false)
    {
      set_assert_failed("Error reading file %s", $this->myTemplateFileName );
    }

    $code = strtr( $code, $replace );

    $bytes = file_put_contents( $this->myWrapperFileName, $code );
    if ($bytes===false)
    {
      set_assert_failed("Error writing file %s", $this->myWrapperFileName );
    }
    
  }

  //--------------------------------------------------------------------------------------------------------------------
}