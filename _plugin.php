<?php
/**
*   Plugin Name:    URI COMMAND
*   Plugin URI:     https://github.com/pinecone-dot-io/uri-command
*   Author:         postpostmodern, pinecone-dot-website
*   Author URI:     https://rack.and.pinecone.website
*   Description:
*   License:        GPL-2.0+
*   License URI:    http://www.gnu.org/licenses/gpl-2.0.txt
*   Version:        0.2.0
*/

register_activation_hook( __FILE__, create_function("", '$ver = "5.4"; if( version_compare(phpversion(), $ver, "<") ) die( "This plugin requires PHP version $ver or greater be installed." );') );

require __DIR__.'/index.php';
