<?php

namespace URI_Command;

class Filters
{
    public function __construct()
    {
        add_filter( 'clean_url', [$this, 'dynamic_nav_menu_clean_url'], 10, 3 );
        add_filter( 'esc_html', [$this, 'dynamic_nav_menu_esc_html'], 10, 2 );
        add_filter( 'kses_allowed_protocols', [$this, 'dynamic_nav_menu_protocols'] );
        add_filter( 'the_title', [$this, 'dynamic_nav_menu_the_title'], 1, 2 );
    }

    /**
    *   parse the uri and return permalink
    *   attached to 'clean_url' filter
    *   @param string
    *   @param string
    *   @param string
    *   @return string
    */
    public function dynamic_nav_menu_clean_url($good_protocol_url, $original_url, $_context = '')
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
    
    /**
    *   attached to `esc_html` filter
    *   @param string
    *   @param string
    *   @return
    */
    public function dynamic_nav_menu_esc_html($safe_text, $text)
    {
        if (strpos($text, 'wp://') !== 0) {
            return $safe_text;
        }
        
        $good_protocol_url = dynamic_nav_parse( $text );

        return $good_protocol_url;
    }

    /**
    *   attached to `kses_allowed_protocols` filter
    *   @param array allowed protocols
    *   @return array
    */
    public function dynamic_nav_menu_protocols($protocols)
    {
        $protocols[] = 'wp';
        
        return $protocols;
    }

    /**
    *   attached to `the_title` filter
    *   @param string
    *   @param int
    *   @return string html
    */
    function dynamic_nav_menu_the_title($title, $post_id = 0)
    {
        if (strpos($title, 'wp://') !== 0) {
            return $title;
        }
        
        $good_protocol_url = dynamic_nav_parse( $title );

        return $good_protocol_url;
    }
}
