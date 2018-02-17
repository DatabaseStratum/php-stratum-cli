<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\MySql\Wrapper;

/**
 * Class for generating a wrapper method for a stored procedure selecting a large amount of rows.
 */
class BulkWrapper extends Wrapper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function enhancePhpDocParameters(&$parameters)
  {
    $this->imports[] = 'SetBased\Stratum\BulkHandler';

    $parameter = ['php_name'             => '$bulkHandler',
                  'description'          => 'The bulk row handler',
                  'php_type'             => 'BulkHandler',
                  'data_type_descriptor' => null];

    $parameters = array_merge([$parameter], $parameters);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function getDocBlockReturnType()
  {
    return '';
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeResultHandler($routine)
  {
    $routine_args = $this->getRoutineArgs($routine);
    $this->codeStore->append('self::executeBulk($bulkHandler, \'CALL '.$routine['routine_name'].'('.$routine_args.')\');');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function writeRoutineFunctionLobFetchData($routine)
  {
    // Nothing to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function writeRoutineFunctionLobReturnData()
  {
    // Nothing to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
