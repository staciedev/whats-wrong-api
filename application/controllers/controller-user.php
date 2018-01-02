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
		if( !empty( $_POST['email'] ) && !empty( $_POST['password'] ) )						
			$this->inneraction_login( $_POST['email'], $_POST['password'] );		
	}	
	
	
	// in a separate function to make it independent from data source
	private function inneraction_login( string $email, string $password )
	{		
		
		if( empty( $email ) || empty( $password ) )
			$this->send_response( '400 Bad Request', [ 'messageKey' => 'missingRequiredData' ] );	
			
		$authorization = new Authorization();
		
		$authorization->login_with_password( $_POST['email'], $_POST['password'] );
		
		switch ( $authorization->get_status() ) {
			case Authorization::AUTHORIZED:					
				$this->send_response( '200 OK', [ 'token' => $authorization->get_user()->get_token() ] );					
				
			case Authorization::NO_USER:
			case Authorization::WRONG_PASS:					
				$this->send_response( '401 Unauthorized', [ 'messageKey' => 'loginFailed' ] );					
			
			case Authorization::NOT_CONFIRMED:
				$this->send_response( '401 Unauthorized', [ 'messageKey' => 'mailNotConfirmed' ] );	
								
		}	
	}
	
	
	function action_test_token()
	{		
		
		$authorization = new Authorization();			
		
		if( !empty( $_POST['user_token'] ) ) {
			
			$authorization->login_with_token( $_POST['user_token'] );
			
			switch ( $authorization->get_status() ) {
				case Authorization::AUTHORIZED:
					$this->send_response( '200 OK', [ 'messageKey' => 'tokenValid' ] );					
					
				default:
					$this->send_response( '401 Unauthorized', [ 'messageKey' => 'tokenInvalid' ] );					
			}		
			
		}			
		
	}
	
	
	public function action_register()
	{		
		
		$registration = new Registration();
		
		if( !empty( $_POST['email'] ) && !empty( $_POST['password'] ) ) {	
			
			$registration->register( $_POST['email'], $_POST['password'] );
			
			switch ( $registration->get_status() ) {
				case Registration::REGISTERED:
					$this->send_response( '200 OK', [ '_id' => $registration->get_user()->get__id() ] );					
					
				case Registration::EMAIL_EXISTS:
					$this->send_response( '400 User exists', [ 'messageKey' => 'emailExists' ] );
					
				case Registration::EMAIL_INVALID:
				case Registration::PASSWORD_INVALID:
					$this->send_response( '400 Invalid data', [ 'messageKey' => 'invalidData' ] );
					
				case Registration::TOKEN_NOT_GENERATED:
					$this->send_response( '400 Confirmation token not created', [ 'messageKey' => 'confirmationTokenNotCreated' ] );					
					
				case Registration::EMAIL_NOT_SENT:	
					$this->send_response( '400 Failed to send email', [ 'messageKey' => 'emailNotSent' ] );					
					
				case Registration::DATABASE_ERROR:		
					$this->send_response( '400 Failed to create database entry', [ 'messageKey' => 'entryNotCreated' ] );
				
			}
			
		}		
		
	}
	
	
	public function action_confirm( array $params ) 
	{
		$result = [];
		
		if( !empty( $params['conf_token'] ) ) {
			
			$registration = new Registration();
			
			$registration->confirm_user( $params['conf_token'] );
			
			switch ( $registration->get_status() ) {					
				case Registration::REGISTERED:
				// TODO: make it possible to login here if necessary
				if( App::$config::LOGIN_AFTER_CONFIRMATION ) {
					$this->send_response( '200 OK', [ 'token' => $registration->get_user()->get_token() ] );
				}
				else $this->send_response( '200 OK', [ 'messageKey' => 'emailConfirmed' ] );					
					
				case Registration::DATABASE_ERROR:
					$this->send_response( '400 Failed to update database entry', [ 'messageKey' => 'entryNotUpdated' ] );					
					
				case Registration::TOKEN_NOT_FOUND:
					$this->send_response( '400 Invalid confirmation token', [ 'messageKey' => 'confirmationTokenInvalid' ] );

			}
			
		}			
			
	}
	
	
}