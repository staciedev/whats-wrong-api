<?php

namespace Whatswrong;

class App
{
	
	// configurations
	public static $config;
	
	// authorization object 
	public static $auth = null;
	
	public static $db = null;
	public static $router = null;
	public static $base_url; 
	public static $complete_url; // may include subdirectories, e.g. on localhost
	public static $basedir;		
		
	
	static function init() {
		self::$config = new Config;
		
		// application url
		$protocol = $_SERVER['SERVER_PROTOCOL'];
		$protocol = explode( '/', $protocol );
		$protocol = $protocol[0];
		self::$base_url = strtolower( $protocol ) . '://' . $_SERVER['HTTP_HOST'];
		self::$complete_url = self::$base_url;
		if( !empty( self::$config::START_URL ) ) 
			self::$complete_url .= '/' . trim( self::$config::START_URL, '/' );
		
		// application base directory
		self::$basedir = dirname(__FILE__).'/../..';
		
		// routing
		self::$router = new \AltoRouter();
		self::$router->setBasePath( self::$config::START_URL );
		
		// database connection
		self::db_connect();	
		
	}	
	
	
	// initializing db connection
	static function db_connect() {
		$client = new \MongoDB\Client( self::$config::DB_HOST );
		$db_name = self::$config::DB_NAME;
		self::$db = $client->$db_name;					
	}
	
	
	static function content_dir() {
		return self::$basedir. '/content';
	}
	
	
	static function content_url() {
		return self::$complete_url. '/content';
	}
	
	
	static function get_logged_user()
	{
		if( !empty( self::$auth ) )
			return self::$auth->get_user();
			
		return null;
	}
	
}