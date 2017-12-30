<?php
namespace Whatswrong\User;

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
		return 'dummy_token_' . time();
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