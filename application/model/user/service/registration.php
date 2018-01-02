<?php
namespace Whatswrong\User;

use Whatswrong\App;

class Registration {	
	
	private $mapper;
	private $user;
	private $status; // operation status: see codes below
	
	const REGISTERED = 1;
	const EMAIL_EXISTS = 2;
	const EMAIL_INVALID = 3;
	const PASSWORD_INVALID = 4;
	const TOKEN_NOT_GENERATED = 5;		
	const EMAIL_NOT_SENT = 6;
	const ENTRY_NOT_CREATED = 7;	
	
	
	public function __construct() {
		$this->user = new User();
		$this->mapper = new UserMapper();
	}
	
	
	// sets registration status
	public function register( string $email, string $password )
	{		
		
		$this->mapper->fetch_by_email( $this->user, $email );
		
		// email exists
		if( $this->user->get__id() ) {
			$this->status = self::EMAIL_EXISTS;			
			return;
		}		
		// email is not a valid email
		if( !$email = filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			$this->status = self::EMAIL_INVALID;			
			return;
		}		
		// password is invalid
		if( !$this->password_valid( $password ) ) {
			$this->status = self::PASSWORD_INVALID;			
			return;
		}		
		
		// if everything ok, register user
		
		// fill in the object fields
		$this->user->set_email( $email );
		$this->user->set_passwd( $password, true );		
		
		// generate a confirmation token
		// TODO: rename all jwt tokens to 'jwt' instead of 'token'
		$confirmation_token = $this->generate_confirmation();
		
		if( !$confirmation_token ) {
			$this->status = self::TOKEN_NOT_GENERATED;
			return;
		}
		
		$this->user->set_confirmation_token( $confirmation_token );
		
		// TODO: uncomment this when live and test email sending
		// if( !$this->send_confirmation_link() ) {
		// 	$this->status = self::EMAIL_NOT_SENT;
		// 	return;
		// }
		
		if( !$this->mapper->create( $this->user ) ) {
			$this->status = self::ENTRY_NOT_CREATED;
			return;
		}
		
		$this->status = self::REGISTERED;		
		
	}
	
	
	public function password_valid( string $password ): bool
	{		
		$result = 
		strlen( $password ) > 8 &&
		strlen( $password ) < 16;
		
		return $result;		
	}	
	
	
	// TODO: move it from the class to a separate helpers file?
	private function random_string( int $length = 16 ): string 
	{
		$base = '0123456789abcdefghijklmnopqrstuvwxyz';
						
		$string = '';
		for ( $i=0; $i < $length; $i++ ) { 
			$index = rand( 0, strlen($base)-1 );
			$sym = $base[$index];
			$string .= $sym;
		}
		
		return $string;
	}
	
	
	public function generate_confirmation(): string 
	{		
		
		// check if this token is unique
		$unique = false;		
		$attempts = 10;
		
		$mb_user = new User();
		
		// ensuring we don't get an endless loop
		$counter = 0;
		
		// generating a unique id
		while( !$unique ) {
			
			$mb_token = $this->random_string();				
			
			$this->mapper->fetch_by_confirmation( $mb_user, $mb_token );
			if( !$mb_user->get__id() ) {
				$unique = true;					
			}				
			
			if( $counter == $attempts ) {
				error_log( 'Unable to generate a confirmation token, ' . $attempts . ' attempts made.' );
				return '';
			}
			
			$counter++;								
		}
		
		return $mb_token;
	}	
	
	
	public function send_confirmation_link(): bool
	{
		$email = $this->user->get_email();
		$conf_token = $this->user->get_confirmation_token();
		
		if( $email && $conf_token ) {
			$link = App::$complete_url . '/user/confirm/' . $conf_token;
			$message = sprintf( "To confirm your email address please follow this link:\n%s", $link );			
			return mail ( $email , 'Confirm your email on WhatsWrong project', $message );
		}
		return false;
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