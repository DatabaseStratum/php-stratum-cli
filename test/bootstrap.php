<?php
//----------------------------------------------------------------------------------------------------------------------

error_reporting(E_ALL);
date_default_timezone_set( 'Europe/Amsterdam' );

set_include_path(get_include_path() . PATH_SEPARATOR . getcwd().'/include');

ini_set('memory_limit', '10000M');

require_once( __DIR__.'/../vendor/autoload.php' );
require_once( 'DataLayer.php' );
