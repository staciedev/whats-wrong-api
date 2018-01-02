<?php
namespace Whatswrong\User;
use Whatswrong\App;
use Firebase\JWT\JWT;

class User {	
	
	private $_id = ''; 
	private $email = '';
	private $passwd = '';
	private $verified = false;
	private $token = '';
	private $confirmation_token = '';
	
	private static $fields_for_token = [
		'_id',
		'email'
	];
	
	
	public function encode_password( string $password ): string 
	{
		return md5( $password );
	}	
	
	public function match_password( $password ): bool 
	{
		return $this->passwd == $this->encode_password( $password );
	}
	
	
	public function get_token(): string 
	{
		if( $this->token ) return $this->token;
		
		$this->reset_token();
		return $this->token;
	}	
	
	
	public function reset_token() 
	{
		$this->token = $this->generate_token();
	}
		
	
	public function generate_token(): string 
	{		
		$token_id    = base64_encode( random_bytes(32) );
    $issued_at   = time();
    $not_before  = $issued_at + 10; // Adding 10 seconds
    $expire     = $not_before + 3600 * 3; // Adding 3 hours TODO: move this to config
    $server_name = App::$complete_url; // Server name will be a domain name 
		
		$user_data = [];
		foreach ( self::$fields_for_token as $field ) {
			if( isset( $this->$field ) )
				$user_data[$field] = $this->$field;
		}   
    
    // Create the token as an array     
    $data = [
        'iat'  => $issued_at, // Issued at: time when the token was generated
        'jti'  => $token_id, // Json Token Id: an unique identifier for the token
        'iss'  => $server_name, // Issuer
        'nbf'  => $not_before, // Not before
        'exp'  => $expire, // Expire
				
        'data' => $user_data, // All user properties 
    ];
		
		$secret_key = base64_decode( App::$config::JWT_SECRET_KEY );
		
		$jwt = JWT::encode(
        $data,
        $secret_key,
        'HS512' // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );        
    
		return $jwt;
	}	
	
		
	public function populate_from_token( string $token )
	{
		$secret_key = base64_decode( App::$config::JWT_SECRET_KEY );
		
		try {
			$token = JWT::decode( $token, $secret_key, array( 'HS512' ) );
			if( !empty( $token->data ) ) {
				$this->populate( (array) $token->data );
			}
				
		}
		catch( \Exception $e ) {
			error_log( 'Token couldn\'t be decoded.' );
		}	  
	  
	}
	
	
	// TODO: this function is a duplicate of the one in DataMapper
	// think of deleting it in DataMapper and moving it to new Domain object class extended by User
	public function populate( array $parameters )
	{
		foreach ( $parameters as $key => $value ) {			
    	$method = 'set_' . $key;			
      if ( method_exists( $this, $method ) ) {				
        $this->$method( $value );
      }
    }
	}
	
	
	/*
	* Getters
	*/
	public function get__id(): string
	{
		return $this->_id;
	}
	public function get_email(): string 
	{
		return $this->email;
	}
	public function get_passwd(): string 
	{
		return $this->passwd;
	}
	public function is_verified(): bool
	{
		if( $this->verified === true ) return true;
		return false;
	}
	public function get_confirmation_token(): string 
	{
		return $this->confirmation_token;
	}
	
	
	/*
	* Setters
	*/
	public function set__id( string $id )
	{		
		$this->_id = $id;
	}
	public function set_email( string $email )
	{
		$this->email = $email;
	}
	public function set_passwd( string $passwd, bool $encode = false )
	{
		if( $encode )
			$this->passwd = $this->encode_password( $passwd );
		else 
			$this->passwd = $passwd;
	}
	public function set_verified( bool $verified )
	{
		$this->verified = $verified;
	}
	public function set_confirmation_token( string $confirmation_token )
	{
		$this->confirmation_token = $confirmation_token;
	}
	
}