<?php

namespace Whatswrong;

class Config
{	
	// configurations
	
	const START_URL = '/whats-wrong-api';
	
	const DB_NAME = 'whatswrong';
	
	const DB_HOST = 'mongodb://localhost:27017';
	
	const CONTENT_DIR = '/content';	
	
	// TODO: Move all configurations to a text file, possibly json or use Zend config. 
	// See https://www.sitepoint.com/php-authorization-jwt-json-web-tokens/
	// Create a setup process and generate the secret key on setup
	const JWT_SECRET_KEY = 'sSeZuXnO4IFhBt3ANMamvdL2+yTfD9ZBfLDJprlDrSvm1\/FO3iXw0WHIQ24BvSlyQiUz1b+qHafXTvK0NxG\/YA==';
	
	// user configurations
	const LOGIN_AFTER_CONFIRMATION = true;
		
}