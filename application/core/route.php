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
		App::$router->map( 'POST', '/user/login', array( 'm' => [ 'user' ], 'c' => 'user', 'a' => 'login' ), 'log_user_in' ); # authorize user	
		App::$router->map( 'POST', '/user/token-test', array( 'm' => [ 'user' ], 'c' => 'user', 'a' => 'test_token' ), 'test_user_token' ); # TODO: a test route, should be deleted
		App::$router->map( 'POST', '/user/register', array( 'm' => [ 'user' ], 'c' => 'user', 'a' => 'register' ), 'register_user' ); # register user
		App::$router->map( 'POST', '/user/confirm/[a:conf_token]', array( 'm' => [ 'user' ], 'c' => 'user', 'a' => 'confirm' ), 'confirm_email' ); # confirm user email 	
		
		
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
		
		
		// load all modules specified for this route 
		if( !empty( $match['target']['m'] ) && is_array( $match['target']['m'] ) ) {
			foreach ( $match['target']['m'] as $module ) {
				App::load_module( $module );
			}
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
	
	
	static function error404()
	{      
      header('HTTP/1.1 404 Not Found');
		  header("Status: 404 Not Found");		  
  }
}