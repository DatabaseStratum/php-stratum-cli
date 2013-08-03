<?php

error_reporting(E_ALL);
date_default_timezone_set( 'Europe/Amsterdam' );

set_include_path(get_include_path() . PATH_SEPARATOR . getcwd().'/include');

//----------------------------------------------------------------------------------------------------------------------
//require_once( '../etc/test_config.php' );
const TST_SQL_MODE = 'STRICT_ALL_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_AUTO_VALUE_ON_ZERO,NO_ENGINE_SUBSTITUTION,NO_ZERO_DATE,NO_ZERO_IN_DATE,ONLY_FULL_GROUP_BY';

require_once( 'test_dl.php' );


