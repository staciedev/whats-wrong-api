<?php

namespace Whatswrong;
use Whatswrong\Issue\CRUD;

class ControllerIssue extends Controller {
	
	function __construct()
	{
		
	}
	
	
	function action_create()
	{				
		
		$accepted_keys = [
			'title',
			'description',
			'main_image',
			'gallery',
			'coords',
			'address',
			'categories', // IDs only
			'tags' // IDs only
		];
		
		if( !$this->input_data_valid( $_POST, $accepted_keys ) )
			$this->send_response( '400 Bad Request', [] );

		$service = new CRUD();
		
		$service->create( $_POST );
		
		switch ( $service->get_status() ) {
			
			case CRUD::NOT_AUTHORIZED:
				$this->send_response( '401 Unauthorized', [ 'messageKey' => 'tokenInvalid' ] );
				break;
				
			case CRUD::INVALID_INPUT:
				$this->send_response( '400 Bad Request', [ 'messageKey' => $service->get_error_msg() ] );
				break;
				
			case CRUD::DATABASE_ERROR:
				$this->send_response( '400 Failed to create database entry', [ 'messageKey' => 'entryNotCreated' ] );
				break;
			
			case CRUD::SUCCESS:
				$this->send_response( '200 OK', [ '_id' => $service->get_inserted_id() ] );
				break;
				
			default:
				$this->send_response( '400 Unknown error', [] );				
		}	
		
	}	
	
	
	function action_update()
	{				
		
	}	
	
	
	function action_delete()
	{				
		
	}
	
	
	function action_get_one()
	{				
		
	}
		
	
	function action_get_many()
	{				
		
	}
	
	
}