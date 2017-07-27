<?php

namespace URI_Command;

class Admin
{
    public function __construct()
    {
        add_filter( 'wp_update_nav_menu_item', [$this, 'update_nav_menu_item'], 10, 3 );
    }

    /**
    *   let wp:// protocols in menu items validate
    *   attached to `wp_update_nav_menu_item` action
    *   @param int
    *   @param int post id
    *   @param array
    *   @return NULL
    */
    public function update_nav_menu_item($menu_id, $menu_item_db_id, $args)
    {
        $url = esc_url_raw( $args['menu-item-url'], array('wp') );
        
        if ($url) {
            update_post_meta( $menu_item_db_id, '_menu_item_url', $url );
        }
    }
}
