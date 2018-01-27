<?php

// Helper functions doing whatever

// gets all headers from request 
if ( !function_exists( 'getallheaders') ): 
  function getallheaders()
  { 
    $headers = array (); 
    foreach ( $_SERVER as $name => $value ) { 
    	if ( substr( $name, 0, 5 ) == 'HTTP_' ) { 
      	$headers[ str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $name, 5 ) ) ) ) ) ] = $value; 
      } 
    } 
    return $headers; 
  } 
endif;


// includes all files in module folder recursively
if( !function_exists( 'require_all' ) ):
	function require_all( $dir, $depth = 0 ) 
	{
		$max_scan_depth = 10;
		if ( $depth > $max_scan_depth ) {
			return;
		}
		// require all php files
		$scan = glob( "$dir/*" );
		foreach ( $scan as $path ) {
			if ( preg_match( '/\.php$/', $path ) ) {
				require_once $path;
			}
			elseif ( is_dir( $path ) ) {
				require_all( $path, $depth + 1 );
			}
		}
	}
endif;
