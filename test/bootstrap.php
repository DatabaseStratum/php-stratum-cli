<?php

error_reporting(E_ALL);
date_default_timezone_set( 'Europe/Amsterdam' );

set_include_path(get_include_path() . PATH_SEPARATOR . getcwd().'/include');

require_once( 'DataLayer.php' );