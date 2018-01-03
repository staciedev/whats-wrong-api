<?php

namespace Whatswrong;

class Controller {
	
	// May hold some logic parsing user request.
	// Or nothing, then it should be deleted.
	
	function send_response( string $status, array $response )
	{
		header( 'HTTP/1.1 ' . $status );
		header( 'Status: ' . $status );
		die( json_encode( $response ) );
	}
	
}