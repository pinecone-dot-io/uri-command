<?
/*
Plugin Name: Dynamic Links in Menu Items
Plugin URI: 
Description: 
Author: 
Version: 0.0.1
Author URI: 
*/

if( is_admin() )
	require dirname( __FILE__ ).'/admin.php';

// testing only
function dynamic_test(){
	//dbug( func_get_args() );
	
	return 'http://google.com/';
}

/*
*	parse the uri and return permalink
*	@param string
*	@param string
*	@param string
*	@return string
*/
function dynamic_nav_menu_clean_url( $good_protocol_url, $original_url, $_context ){
	if( $_context != 'display' || strpos($original_url, 'wp://') !== 0 )
		return $good_protocol_url; 
		
	$parsed = parse_url( $original_url );
	
	$function = $parsed['host'];
	
	// @TODO make an option whehter to show wp:// in htnml, maybe for dev?
	if( !is_callable($function) )
		return 'dynamic_nav_menu_clean_url fail!';
	
	$path = isset( $parsed['path'] ) ? array_filter( explode('/', $parsed['path']) ) : array();
	//dbug( $path, '$path' );
	
	isset( $parsed['query'] ) ? parse_str( $parsed['query'], $query ) : $query = array();
	//dbug( $query, '$query' );
	
	$good_protocol_url = call_user_func_array( $function, $query );
	//dbug( $good_protocol_url );
	
	return $good_protocol_url;
}
add_filter( 'clean_url', 'dynamic_nav_menu_clean_url', 10, 3 );

/*
*
*	@param array allowed protocols
*	@return array
*/
function dynamic_nav_menu_protocols( $protocols ){
	$protocols[] = 'wp';
	
	return $protocols;
}
add_filter( 'kses_allowed_protocols', 'dynamic_nav_menu_protocols' );
