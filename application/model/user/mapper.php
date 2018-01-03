<?php

namespace Whatswrong\User;

use Whatswrong\DataMapper;
use Whatswrong\App;
use MongoDB\BSON\ObjectId;

class UserMapper extends DataMapper {
	
	private $collection_name = 'users';
	
		
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
	
	
	private function user_to_db_object( User $user )
	{
		$data = [];
		
		if( !empty( $user->get__id() ) )
		$data['_id'] = new ObjectId( $user->get__id() );
			
		$data['email'] = $user->get_email();
		$data['passwd'] = $user->get_passwd();
		$data['confirmed'] = $user->is_confirmed();	
		
		if( !empty( $user->get_confirmation_token() ) ) 
		$data['confirmation_token'] = $user->get_confirmation_token();
		
		return $data;
	}
	
	
	public function create( User $user ): bool
	{
		$data_to_insert = $this->user_to_db_object( $user );	
		
		try {			
			$insert_result = App::$db->{$this->collection_name}->insertOne( $data_to_insert );			
		}
		catch ( \Exception $e ) { 
			error_log( $e->getMessage() );
			return false; 				
		}
		
		$inserted_id = $insert_result->getInsertedId();		
		$user->set__id( $inserted_id );
		return true;
	}
	
	
	// replaces the complete object, all properties not specified will be deleted
	public function replace( User $user ): int
	{
		$data_to_update = $this->user_to_db_object( $user );
		
		try {			
			$update_result = App::$db->{$this->collection_name}->replaceOne( [ '_id' => new ObjectId( $user->get__id() ) ], $data_to_update );			
		}
		catch ( \Exception $e ) { 
			error_log( $e->getMessage() );
			return 0; 				
		}
		
		return $update_result->getModifiedCount();
	}
	
	
}