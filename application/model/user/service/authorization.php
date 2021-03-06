<?php
namespace Whatswrong\User;

class Authorization {	
	
	private $mapper;
	private $user;
	private $status; // operation status: AUTHORIZED|NO_USER|WRONG_PASS|NOT_VERIFIED
	
	const AUTHORIZED = 1;
	const NO_USER = 2;
	const WRONG_PASS = 3;
	const NOT_VERIFIED = 4;
	
	
	public function __construct() {
		$this->user = new User();
		$this->mapper = new UserMapper();
	}
	
	
	// sets authorization status
	public function login_with_password( string $email, string $password )
	{		
		$this->mapper->fetch_by_email( $this->user, $email );
		
		// email not found
		if( !$this->user->get__id() ) {
			$this->status = self::NO_USER;			
			return;
		}
		// password doesn't match 
		if( !$this->user->match_password( $password ) ) {
			$this->status = self::WRONG_PASS;			
			return;
		}
		// user email not verified 
		if( !$this->user->is_verified() ) {
			$this->status = self::NOT_VERIFIED;			
			return;
		}
		
		$this->status = self::AUTHORIZED;		
		
	}
	
	
	public function check_user_pass( User $user, string $password ): bool 
	{
		
	}
		
	
	public function get_user(): User 
	{
		return $this->user;
	}
	
	
	public function get_status(): int 
	{
		return $this->status;
	}
	
	
}