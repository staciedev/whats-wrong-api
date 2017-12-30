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
		self::$complete_url = strtolower( $protocol ) . '://' . $_SERVER['HTTP_HOST'] . '/' . self::$config::START_URL;
		
		self::$basedir = dirname(__FILE__).'/../..';
		
		self::$router = new \AltoRouter();
		self::$router->setBasePath( self::$config::START_URL );
		
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
	
}