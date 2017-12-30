<?php

namespace Whatswrong;

class App
{
	
	// configurations
	public static $config;
	
	public static $db = null;
	public static $router = null;
	public static $complete_url;
	public static $basedir;
	
	
	static function init() {
		self::$config = new Config;
		
		$protocol = $_SERVER['SERVER_PROTOCOL'];
		$protocol = explode( '/', $protocol );
		$protocol = $protocol[0];
		self::$complete_url = strtolower( $protocol ) . '://' . $_SERVER['HTTP_HOST'] . '/' . self::$config::$start_url;
		
		self::$basedir = dirname(__FILE__).'/../..';
		
		self::$router = new \AltoRouter();
		self::$router->setBasePath( self::$config::$start_url );
		
		self::db_connect();
	}
	
	
	// initializing db connection
	static function db_connect() {
		$client = new \MongoDB\Client( self::$config::$db_host );
		$db_name = self::$config::$db_name;
		self::$db = $client->$db_name;					
	}
	
	
	static function content_dir() {
		return self::$basedir. '/content';
	}
	
	
	static function content_url() {
		return self::$complete_url. '/content';
	}
	
}