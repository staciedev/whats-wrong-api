<?php

namespace Whatswrong\User;

use Whatswrong\DataMapper;
use Whatswrong\App;

class UserMapper extends DataMapper {
	
	private $collection_name = 'users';
	
	private $properties_to_store = [		
		'email',
		'passwd',
		'verified',
		'confirmation_token'
	];
		
		
	public function fetch_by_email( User $user, string $email ) 
	{		
		
		try {
			$data = App::$db->{$this->collection_name}->findOne( [ 'email' => $email ] );
		}
		catch ( \Exception $e ) { 
			error_log( $e->getMessage() );
			die( json_encode( [ 'error' => $e->getMessage() ] ) );   				
		}
		
		if ( $data ) {
			$data_array = $data->getArrayCopy();			
      $this->apply_values( $user, $data_array );
    }
		
	}
	
	
	public function fetch_by_confirmation( User $user, string $conf_token )
	{
		try {
			$data = App::$db->{$this->collection_name}->findOne( [ 'confirmation_token' => $conf_token ] );
		}
		catch ( \Exception $e ) { 
			error_log( $e->getMessage() );
			die( json_encode( [ 'error' => $e->getMessage() ] ) );   				
		}
		
		if ( $data ) {
			$data_array = $data->getArrayCopy();			
      $this->apply_values( $user, $data_array );
    }
	}
	
	
	public function create( User $user )
	{
		$data_to_insert = [];
		
		foreach ( $this->properties_to_store as $property ) {
			$method = 'get_' . $property;		
			if ( method_exists( $user, $method ) ) {				
        $data_to_insert[$property] = $user->$method();
      }	
		}
		
		try {			
			$insert_result = App::$db->{$this->collection_name}->insertOne( $data_to_insert );			
		}
		catch ( \Exception $e ) { 
			error_log( $e->getMessage() );
			die( json_encode( [ 'error' => $e->getMessage() ] ) );   				
		}
		
		$inserted_id = $insert_result->getInsertedId();
		$user->set__id( $inserted_id );
	}
	
	
}