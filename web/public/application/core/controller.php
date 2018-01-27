<?php

namespace Whatswrong;

class Controller {
	
	function input_data_valid( array $input, array $accepted_keys ): bool
	{		
		foreach ( $input as $key => $value ) {
			if( !in_array( $key, $accepted_keys ) ) return false;			
		}
		return true;
	}
	
	
	function send_response( string $status, array $response )
	{
		header( 'HTTP/1.1 ' . $status );
		header( 'Status: ' . $status );
		die( json_encode( $response ) );
	}
	
}