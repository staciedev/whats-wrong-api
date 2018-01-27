<?php
namespace Whatswrong\Issue;

use Whatswrong\App;

class CRUD {	
	
	private $mapper;
	private $issue;
	private $status = 0; // operation status
	private $error_msg;
	private $inserted_id = '';
	
	const SUCCESS = 1;
	const INVALID_INPUT = 2;
	const DATABASE_ERROR = 3;
	const NOT_AUTHORIZED = 4;	
	
	
	public function __construct( array $data = [] ) {			
		$this->mapper = new IssueMapper();
	}
		
	
	public function create( array $data )
	{
		// check user rights
		$logged_user = App::get_logged_user();
		
		// fail if no auth data
		if( !$logged_user || !$logged_user->get__id() ) { 
			// TODO: user should be NULL if not authorized. Change it in Authorization service. Remove this second condition			
			$this->status = self::NOT_AUTHORIZED;
			return;						
		}
		
		// set owner id
		// (Attention: we currently only allow ownerID to be taken from logged in user)
		$data['ownerID'] = $logged_user->get__id();
		
		// user authorized, create issue 
		
		try {
			$this->issue = new Issue( $data );
		}
		catch( \Exception $e ) {
			$this->error_msg = $e->getMessage();
			$this->status = self::INVALID_INPUT;			
			return;
		}
		
		$this->inserted_id = $this->mapper->create( $this->issue );
		if( empty( $this->inserted_id ) ) {
			$this->status = self::DATABASE_ERROR;
			return;
		}
		
		$this->status = self::SUCCESS;
		
	}
	
	// TODO: here we can return null, if switch to PHP 7.1
	public function get_issue(): Issue 
	{
		return $this->issue;
	}
	
	
	public function get_status(): int 
	{
		return $this->status;
	}
	
	public function get_error_msg(): string 
	{
		return $this->error_msg;
	}
	
	public function get_inserted_id(): string 
	{
		return $this->inserted_id;
	}
	
	
}