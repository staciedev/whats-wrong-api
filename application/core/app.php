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
		
		// application url
		$protocol = $_SERVER['SERVER_PROTOCOL'];
		$protocol = explode( '/', $protocol );
		$protocol = $protocol[0];
		self::$complete_url = strtolower( $protocol ) . '://' . $_SERVER['HTTP_HOST'] . '/' . self::$config::START_URL;
		
		// application base directory
		self::$basedir = dirname(__FILE__).'/../..';
		
		// routing
		self::$router = new \AltoRouter();
		self::$router->setBasePath( self::$config::START_URL );
		
		// database connection
		self::db_connect();		
		
	}
	
	// includes all files in module folder recursively
	private static function  _require_all( $dir, $depth = 0 ) 
	{
		$max_scan_depth = 10;
  	if ( $depth > $max_scan_depth ) {
    	return;
	  }
    // require all php files
    $scan = glob( "$dir/*" );
    foreach ( $scan as $path ) {
      if ( preg_match( '/\.php$/', $path ) ) {
        require_once $path;
      }
      elseif ( is_dir( $path ) ) {
        self::_require_all( $path, $depth + 1 );
      }
  	}
  }
	
	
	// loads application module.
	// One module is one folder in application/model
	static function load_module( $module_name )
	{
		$module_dir = "application/model/" . $module_name;
		
		if( file_exists( $module_dir ) && is_dir( $module_dir ) ) {
			self::_require_all( $module_dir );
		}
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