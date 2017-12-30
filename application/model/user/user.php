<?php
namespace Whatswrong\User;
use Whatswrong\App;
use Firebase\JWT\JWT;

class User {	
	
	private $id = ''; 
	private $email = '';
	private $passwd = '';
	private $verified = false;
	private $token = '';
	
	
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
	
	public function generate_token(): string 
	{		
		$token_id    = base64_encode( random_bytes(32) );
    $issued_at   = time();
    $not_before  = $issued_at + 10; // Adding 10 seconds
    $expire     = $not_before + 3600 * 3; // Adding 3 hours
    $server_name = App::$complete_url; // Server name will be a domain name    
    
    // Create the token as an array     
    $data = [
        'iat'  => $issued_at, // Issued at: time when the token was generated
        'jti'  => $token_id, // Json Token Id: an unique identifier for the token
        'iss'  => $server_name, // Issuer
        'nbf'  => $not_before, // Not before
        'exp'  => $expire, // Expire
        'data' => [
            'userId'   => $this->id, // User ID from the users table
            'userEmail' => $this->email, // User email
        ]
    ];
		
		$secret_key = base64_decode( App::$config::JWT_SECRET_KEY );
		
		$jwt = JWT::encode(
        $data,
        $secret_key,
        'HS512' // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );        
    
		return $jwt;
	}
	
	
	public function reset_token() 
	{
		$this->token = $this->generate_token();
	}
	
	
	/*
	* Getters
	*/
	public function get__id(): string
	{
		return $this->id;
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
	
	
	/*
	* Setters
	*/
	public function set__id( string $id )
	{		
		$this->id = $id;
	}
	public function set_email( string $email )
	{
		$this->email = $email;
	}
	public function set_passwd( string $passwd )
	{
		$this->passwd = $passwd;
	}
	public function set_verified( bool $verified )
	{
		$this->verified = $verified;
	}
	
}