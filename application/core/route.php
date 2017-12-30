<?php
namespace Whatswrong;

class Route {
	
	static function start()
	{
		// default controller and action
		$controller_name = '';
		$action_name = '';	
		
		// TODO: make an array of modules?
		
		// site routes
		App::$router->map( 'POST', '/user/login', array( 'm' => 'user', 'c' => 'user', 'a' => 'login' ), 'log_user_in' ); # authorize user	
		
		
		$match = App::$router->match();
		
		if ( !empty( $match['target']['c'] ) && !empty( $match['target']['a'] ) ) {
			$controller_name = $match['target']['c'];
			$action_name = $match['target']['a'];
			$module_name = ( !empty( $match['target']['m'] ) ) ? $match['target']['m'] : $match['target']['c'];
		}
		else {						
			Route::error404();
			return;
		}

		// include all files in the module folder
				
		$module_dir = "application/model/" . $module_name;
		
		if( file_exists( $module_dir ) && is_dir( $module_dir ) ) {
			self::_require_all( $module_dir );
		}

		// include controller file 
		$controller_file = strtolower( 'controller-' . $controller_name ) . '.php';
		$controller_path = "application/controllers/" . $controller_file;
		if ( file_exists( $controller_path ) ) {
			include "application/controllers/" . $controller_file;
		}
		else {
						
			Route::error404();
			return;
		}
		
		// creting controllers
		$controller_class = 'Whatswrong\Controller' . ucfirst( $controller_name );
		$controller = new $controller_class;
		$action = 'action_' . $action_name;
		
		if( method_exists( $controller, $action ) )	{			
			$params = $match['params'];			
			
			// calling controller action
			$controller->$action( $params );			
		}
		else {
			// throw exception?
			Route::error404();
			return;
		}
	
	}
	
	
	static function  _require_all( $dir, $depth = 0 ) 
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
	
	
	static function error404()
	{      
      header('HTTP/1.1 404 Not Found');
		  header("Status: 404 Not Found");		  
  }
}