<?php

namespace uri_command;

/*
*	let wp:// protocols in menu items validate
*	attached to `wp_update_nav_menu_item` action
*	@param int
*	@param int post id
*	@param array
*	@return NULL
*/
function update_nav_menu_item( $menu_id, $menu_item_db_id, $args ){
	$url = esc_url_raw( $args['menu-item-url'], array('wp') );
	
	if( $args['menu-item-url'] ){
		//dbug( $args['menu-item-url'] );
		//ddbug( $url );
	}
	
	if( $url )
		update_post_meta( $menu_item_db_id, '_menu_item_url', $url );
}
add_filter( 'wp_update_nav_menu_item', __NAMESPACE__.'\update_nav_menu_item', 10, 3 );
