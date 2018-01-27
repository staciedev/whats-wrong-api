<?php

/*************************************
*       What's Wrong REST API        *
*************************************/

if( !extension_loaded( 'mongodb' ) ) die( 'MongoDB extention is required.' );

require '../app/vendor/autoload.php';
ini_set('display_errors', 1);

ini_set( 'log_errors', 1 );
ini_set( 'error_log', dirname(__FILE__) . '/debug.log' );

require_once 'application/bootstrap.php';
