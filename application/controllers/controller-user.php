<?php

namespace Whatswrong;
use Whatswrong\User\Authorization;


class ControllerUser extends Controller {
	
	function __construct()
	{
		
	}
	
	
	function action_login()
	{
		$result = [];
		
		$authorization = new Authorization();			
		
		if( !empty( $_POST['email'] ) && !empty( $_POST['password'] ) ) {			
			
			$authorization->login_with_password( $_POST['email'], $_POST['password'] );
			
			switch ( $authorization->get_status() ) {
				case Authorization::AUTHORIZED:
					header('HTTP/1.1 200 OK');
				  header("Status: 200 OK");
					$result = [
						'token' => $authorization->get_user()->get_token()
						];					
					break;
					
				case Authorization::NO_USER:
				case Authorization::WRONG_PASS:
					header('HTTP/1.1 401 Unauthorized');
				  header("Status: 401 Unauthorized");
					$result = [
						'messageKey' => 'loginFailed'
						];
					break;
				
				case Authorization::NOT_VERIFIED:
					header('HTTP/1.1 401 Unauthorized');
				  header("Status: 401 Unauthorized");
					$result = [
						'messageKey' => 'mailNotVerified'
						];							
				
			}			
			
		}	
		
		echo json_encode( $result );		
		
	}	
	
	
	function action_test_token()
	{
		$result = [];
		
		$authorization = new Authorization();			
		
		if( !empty( $_POST['user_token'] ) ) {
			
			$authorization->login_with_token( $_POST['user_token'] );
			
			switch ( $authorization->get_status() ) {
				case Authorization::AUTHORIZED:
					header('HTTP/1.1 200 OK');
				  header("Status: 200 OK");
					$result = [
						'messageKey' => 'tokenValid'
						];					
					break;
					
				default:
					header('HTTP/1.1 401 Unauthorized');
				  header("Status: 401 Unauthorized");
					$result = [
						'messageKey' => 'tokenInvalid'
						];
					break;										
				
			}		
			
		}
		
		echo json_encode( $result );		
		
	}
	
	
}