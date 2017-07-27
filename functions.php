<?php

namespace URI_Command;

/**
*
*   @param string
*   @return
*/
function dynamic_nav_parse($original_url)
{
    $parsed = parse_url( $original_url );
    
    $function = dynamic_nav_parse_r( $parsed['host'] );
    // this could all change
    
    $path = isset( $parsed['path'] ) ? array_values( array_filter(explode('/', $parsed['path'])) ) : [];
    isset( $parsed['query'] ) ? parse_str( $parsed['query'], $query ) : $query = [];
    
    // parse query variables into function arguments
    $query = dynamic_nav_parse_r( $query );
    
    if (is_callable($function)) {
        $good_protocol_url = call_user_func_array( $function, $query );
    } elseif ($path && is_callable([$function, $path[0]])) {
        $good_protocol_url = call_user_func_array( [$function, $path[0]], $query );
    } else { // @TODO make an option whether to show wp:// in html, maybe for dev?
        return '#uri-command-fail';
    }
    
    return $good_protocol_url;
}

/**
*   recursive function that checks for dynamic variables in url query
*   @param mixed
*   @return mixed
*/
function dynamic_nav_parse_r($mixed)
{
    if (is_array($mixed) || is_object($mixed)) {
        $parsed = [];
        foreach ($mixed as $k => $v) {
            $key = dynamic_nav_parse_r( $k );
            $val = dynamic_nav_parse_r( $v );
            
            // @TODO check that key is not array
            $parsed[$key] = $val;
        }
    } elseif (is_string($mixed) && $json = json_decode($mixed)) {
        $parsed = dynamic_nav_parse_r( $json );
    } elseif (is_string($mixed) && (strpos($mixed, '$') === 0) && ($index = substr($mixed, 1)) && isset($GLOBALS[$index])) {
        $parsed = $GLOBALS[$index];
    } else {
        $parsed = $mixed;
    }
        
    return $parsed;
}

/**
*
*   @return string
*/
function version()
{
    $data = get_plugin_data( __DIR__.'/_plugin.php' );
    return $data['Version'];
}
