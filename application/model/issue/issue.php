<?php
namespace Whatswrong\Issue;
use Whatswrong\App;
use Firebase\JWT\JWT;

class Issue {	
	
	private $_id = ''; 
	private $owner_id = '';
	private $title = '';
	private $description = '';
	private $votes = 0;
	private $views = 0;	
	private $main_image = ''; // ID or Media object?
	private $gallery = []; // IDs or objects?
	private $coords = null;
	private $address = '';
	private $status = '';
	private $cost = null;
	private $categories = []; // 
	private $tags = [];
	
	
	public function __construct( array $p )
	{
		// required shit 
		if( empty( $p['title'] ) )
			throw new \Exception( 'issueTitleRequired' );	
		$this->title = $p['title'];
			
		if( empty( $p['coords']['lat'] ) || empty( $p['coords']['lng'] ) || count( $p['coords'] ) != 2 )
			throw new \Exception( 'issueLocationRequired' );			
		$this->coords = $p['coords'];
		
		if( empty( $p['ownerID'] ) )
			throw new \Exception( 'issueOwnerIDRequired' );	
		$this->owner_id = $p['ownerID'];	
		
		// all other properties
		if( !empty( $p['ID'] ) )
			$this->_id = $p['ID'];
			
		if( !empty( $p['description'] ) )	
			$this->description = $p['description'];
			
		if( !empty( $p['votes'] ) && is_numeric( $p['votes'] ) && intval( $p['votes'] ) >= 0 )
			$this->votes = $p['votes'];
		else
			$this->votes = 0;
			
		if( !empty( $p['views'] ) && is_numeric( $p['views'] ) && intval( $p['views'] ) >= 0 )
			$this->views = $p['views'];
		else
			$this->views = 0;		
			
		if( !empty( $p['address'] ) )	
			$this->address = $p['address'];
		
		if( !empty( $p['status'] ) )	
			$this->status = $p['status'];
		else 
			$this->status = 'onhold';
		
		$this->cost = [];	
		if( !empty( $p['cost'] ) && is_array( $p['cost'] ) )		
			foreach ( $p['cost'] as $currency => $sum ) {
				if( is_numeric( $sum ) ) 
					$this->cost[ $currency ] = $sum; 
			}
		
		if( !empty( $p['categories'] ) && is_array( $p['categories'] ) )		
			$this->categories = $p['categories'];
		
		if( !empty( $p['tags'] ) && is_array( $p['tags'] ) )		
			$this->tags = $p['tags'];
	}
	
	
	/*
	* Getters
	*/
	public function get__id(): string
	{
		return $this->_id;
	}
	public function get_owner_id(): string
	{		
		return $this->owner_id;
	}
	public function get_title(): string
	{		
		return $this->title;
	}
	public function get_description(): string
	{		
		return $this->description;
	}
	public function get_votes(): int
	{				
		return $this->votes;
	}
	public function get_views(): int
	{				
		return $this->views;
	}
	public function get_coords(): array
	{		
		return $this->coords;
	}
	public function get_address(): string
	{		
		return $this->address;
	}
	public function get_status(): string
	{		
		return $this->status;
	}
	public function get_cost(): array
	{					
		return $this->cost;
	}
	public function get_categories(): array
	{
		return $this->categories;
	}
	public function get_tags(): array
	{
		return $this->tags;
	}
	public function get_main_image(): string
	{
		return $this->main_image;
	}
	public function get_gallery(): array
	{
		return $this->gallery;
	}	
	
	
	/*
	* Setters
	*/
	// public function set__id( string $id )
	// {		
	// 	$this->_id = $id;
	// }
	// public function set_owner_id( string $owner_id )
	// {		
	// 	$this->owner_id = $owner_id;
	// }
	// public function set_title( string $title )
	// {		
	// 	$this->title = $title;
	// }
	// public function set_description( string $description )
	// {		
	// 	$this->description = $description;
	// }
	// public function set_votes( int $votes )
	// {		
	// 	if( $votes >= 0 )
	// 	$this->votes = $votes;
	// }
	// public function set_views( int $views )
	// {		
	// 	if( $views >= 0 )
	// 	$this->views = $views;
	// }
	// public function set_coords( array $coords )
	// {		
	// 	if( !empty( $coords['lat'] ) && !empty( $coords['lng'] ) && count( $coords ) == 2 )
	// 	$this->coords = $coords;
	// }
	// public function set_address( string $address )
	// {		
	// 	$this->address = $address;
	// }
	// public function set_status( string $status )
	// {		
	// 	$this->status = $status;
	// }
	// public function set_cost( array $cost )
	// {		
	// 	foreach ( $cost as $currency => $sum ) {
	// 		if( !is_numeric( $sum ) ) return;
	// 	}		
	// 	$this->cost = $cost;
	// }
	// public function set_categories( array $categories )
	// {
	// 	$this->categories = $categories;
	// }
	// public function set_tags( array $tags )
	// {
	// 	$this->tags = $tags;
	// }	
	
}