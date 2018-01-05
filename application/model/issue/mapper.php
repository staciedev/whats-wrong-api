<?php

namespace Whatswrong\Issue;

use Whatswrong\DataMapper;
use Whatswrong\App;
use MongoDB\BSON\ObjectId;

class IssueMapper extends DataMapper {
	
	private $collection_name = 'issues';	
		
	public function fetch_by__id( string $_id ): Issue 
	{				
		
	}		
	
	private function issue_to_db_object( Issue $issue )
	{
		$data = [];
		
		if( !empty( $_id = $issue->get__id() ) ) 
			$data['_id'] = new ObjectId( $_id );				
		
		if( !empty( $owner_id = $issue->get_owner_id() ) )
			$data['owner_id'] = $owner_id;
			
		if( !empty( $title = $issue->get_title() ) )
			$data['title'] = $title;
			
		if( !empty( $coords = $issue->get_coords() ) )
			$data['coords'] = $coords;
			
		if( !empty( $description = $issue->get_description() ) )
			$data['description'] = $description;
			
		if( !empty( $votes = $issue->get_votes() ) )
			$data['votes'] = $votes;
			
		if( !empty( $views = $issue->get_views() ) )
			$data['views'] = $views;
			
		if( !empty( $main_image = $issue->get_main_image() ) )
			$data['main_image'] = $main_image;
			
		if( !empty( $gallery = $issue->get_gallery() ) )
			$data['gallery'] = $gallery;
			
		if( !empty( $address = $issue->get_address() ) )
			$data['address'] = $address;
			
		if( !empty( $status = $issue->get_status() ) )
			$data['status'] = $status;
			
		if( !empty( $cost = $issue->get_cost() ) )
			$data['cost'] = $cost;
			
		if( !empty( $categories = $issue->get_categories() ) )
			$data['categories'] = $categories;
			
		if( !empty( $tags = $issue->get_tags() ) )
			$data['tags'] = $tags;
		
		return $data;
	}
	
	
	// return inserted ID
	public function create( Issue $issue ): string
	{
		$data_to_insert = $this->issue_to_db_object( $issue );	
		
		try {			
			$insert_result = App::$db->{$this->collection_name}->insertOne( $data_to_insert );			
		}
		catch ( \Exception $e ) { 
			error_log( $e->getMessage() );
			return ''; 				
		}		
		
		return $insert_result->getInsertedId();
	}	
	
	
	// replaces the complete object, all properties not specified will be deleted
	// TODO: not sure how it should work for now
	public function replace( Issue $issue ): int
	{
		
	}
	
	
}