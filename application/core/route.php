<?php
namespace Whatswrong;

/*
* This class defines site routes and loads app modules according to current route
*/
class Route {
	
	// array of loaded modules
	private static $modules = [];
	
	
	// site routes
	static function define_routes()
	{
		// users
		App::$router->map( 'POST', '/user/login', array( 'm' => [ 'user' ], 'c' => 'user', 'a' => 'login' ), 'log_user_in' ); # authorize user	
		App::$router->map( 'POST', '/user/token-test', array( 'm' => [ 'user' ], 'c' => 'user', 'a' => 'test_token' ), 'test_user_token' ); # TODO: a test route, should be deleted
		App::$router->map( 'POST', '/user/register', array( 'm' => [ 'user' ], 'c' => 'user', 'a' => 'register' ), 'register_user' ); # register user
		App::$router->map( 'GET', '/user/confirm/[a:conf_token]', array( 'm' => [ 'user' ], 'c' => 'user', 'a' => 'confirm' ), 'confirm_email' ); # confirm user email 	
		
		// issues
		App::$router->map( 'POST', '/issue/create', array( 'm' => [ 'user', 'issue' ], 'c' => 'issue', 'a' => 'create' ), 'create_issue' ); # create issue
	}
	
	
	static function start()
	{
		// default controller and action
		$controller_name = '';
		$action_name = '';		
		
		// get routes
		self::define_routes();		
		
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
				self::load_module( $module );
			}
		}	
		
		// set logged user data if User module is loaded
		if( self::is_module_loaded( 'user' ) ) self::init_auth();

		// include controller file 
		$controller_file = strtolower( 'controller-' . $controller_name ) . '.php';
		$controller_path = dirname( __FILE__ ) . "/../controllers/" . $controller_file;
		if ( file_exists( $controller_path ) ) {
			include $controller_path;
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
	
	
	// loads application module.
	// One module is one folder in application/model
	static function load_module( $module_name )
	{
		$module_dir = dirname( __FILE__ ) . "/../model/" . $module_name;
		
		if( file_exists( $module_dir ) && is_dir( $module_dir ) ) {
			require_all( $module_dir );
			self::$modules[] = $module_name;
		}
	}
	
	
	// check if module is loaded
	static function is_module_loaded( $module_name ): bool
	{		
		return in_array( $module_name, self::$modules );
	}
	
	
	// Authorization: setting logged user
	static function init_auth()	
	{
		// get token from Authorization header
		$headers = getallheaders();
		
		if( isset( $headers['Authorization'] ) ) {
			$jwt = $headers['Authorization'];			
			
			App::$auth = new User\Authorization();
			App::$auth->login_with_token( $jwt );
		}		
		
	}
	
	
	static function error404()
	{      
      header('HTTP/1.1 404 Not Found');
		  header("Status: 404 Not Found");		  
  }
}