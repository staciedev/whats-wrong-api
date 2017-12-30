<?php

namespace Whatswrong\User;

use Whatswrong\DataMapper;
use Whatswrong\App;

class UserMapper extends DataMapper {
	
	private $collection_name = 'users';
		
	public function fetch_by_email( User $user, string $email ) 
	{		
		
		try {
			$data = App::$db->{$this->collection_name}->findOne( [ 'email' => $email ] );
		}
		catch ( Exception $e ) { 
			error_log( $e->get_message() );
			die( json_encode( [ 'error' => $e->get_message() ] ) );   				
		}
		
		if ( $data ) {
			$data_array = $data->getArrayCopy();			
      $this->apply_values( $user, $data_array );
    }
		
	}
	
	
}