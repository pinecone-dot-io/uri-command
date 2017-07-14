<?php
    
if (is_admin()) {
    require __DIR__.'/admin.php';
}

/**
*   parse the uri and return permalink
*   attached to 'clean_url' filter
*   @param string
*   @param string
*   @param string
*   @return string
*/
function dynamic_nav_menu_clean_url($good_protocol_url, $original_url, $_context = '')
{
    // allow it to save to db without validating
    if (in_array($_context, ['db']) && strpos($original_url, 'wp://') === 0) {
        return $original_url;
    }
    
    // don't try to parse non wp:// uris
    if (!in_array($_context, ['display']) || strpos($original_url, 'wp://') !== 0) {
        return $good_protocol_url;
    }
    
    $good_protocol_url = dynamic_nav_parse( $original_url );
    
    return $good_protocol_url;
}
add_filter( 'clean_url', 'dynamic_nav_menu_clean_url', 10, 3 );

/**
*
*   @param string
*   @param string
*   @return
*/
function dynamic_nav_menu_esc_html($safe_text, $text)
{
    if (strpos($text, 'wp://') !== 0) {
        return $safe_text;
    }
    
    $good_protocol_url = dynamic_nav_parse( $text );
    
    return $good_protocol_url;
}
add_filter( 'esc_html', 'dynamic_nav_menu_esc_html', 10, 2 );

/**
*
*   @param string
*   @param int
*   @return
*/
function dynamic_nav_menu_the_title($title, $post_id = 0)
{
    if (strpos($title, 'wp://') !== 0) {
        return $title;
    }
    
    $good_protocol_url = dynamic_nav_parse( $title );
    
    return $good_protocol_url;
}
add_filter( 'the_title', 'dynamic_nav_menu_the_title', 1, 2 );

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
*   attached to `kses_allowed_protocols` filter
*   @param array allowed protocols
*   @return array
*/
function dynamic_nav_menu_protocols($protocols)
{
    $protocols[] = 'wp';
    
    return $protocols;
}
add_filter( 'kses_allowed_protocols', 'dynamic_nav_menu_protocols' );
