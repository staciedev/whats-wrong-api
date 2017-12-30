<?php

namespace Whatswrong;

class DataMapper {	
	
	/**
  * Method for populating the given instance with values from the array via setters  
	* source: https://github.com/teresko/palladium/blob/master/src/Palladium/Mapper/Identity.php
	*
  * @param object $instance The object to be populated with values
  * @param array $parameters A key-value array, that will be matched to setters
  */
  public function apply_values( $instance, array $parameters )
  {		
		
    foreach ( $parameters as $key => $value ) {			
    	$method = 'set_' . $key;			
      if ( method_exists( $instance, $method ) ) {				
        $instance->{$method}( $value );
      }
    }
  }
	
}