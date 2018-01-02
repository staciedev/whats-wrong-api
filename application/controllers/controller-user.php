<?php

namespace Whatswrong;
use Whatswrong\User\Authorization;
use Whatswrong\User\Registration;


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
	
	
	public function action_register()
	{
		$result = [];
		
		$registration = new Registration();
		
		if( !empty( $_POST['email'] ) && !empty( $_POST['password'] ) ) {	
			
			$registration->register( $_POST['email'], $_POST['password'] );
			
			switch ( $registration->get_status() ) {
				case Registration::REGISTERED:
					header('HTTP/1.1 200 OK');
				  header("Status: 200 OK");
					$result = [
						'_id' => $registration->get_user()->get__id()
						];					
					break;
					
				case Registration::EMAIL_EXISTS:
					header('HTTP/1.1 400 User exists');
					header("Status: 400 User exists");
					$result = [
						'messageKey' => 'emailExists'
						];
					break;
					
				case Registration::EMAIL_INVALID:
				case Registration::PASSWORD_INVALID:
					header('HTTP/1.1 400 Invalid data');
					header("Status: 400 Invalid data");
					$result = [
						'messageKey' => 'invalidData'
						];
					break;
					
				case Registration::TOKEN_NOT_GENERATED:
					header('HTTP/1.1 400 Confirmation token not created');
					header("Status: 400 Confirmation token not created");
					$result = [
						'messageKey' => 'confirmationTokenNotCreated'
						];
					break;				
				
			}
			
		}
		
		echo json_encode( $result );
	}
	
	
}