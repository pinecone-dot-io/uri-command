<?php
/*
Plugin Name:	URI COMMAND
Plugin URI:		https://github.com/pinecone-dot-io/uri-command
Description: 
Author: 
Version:		0.1.0
Author URI: 
*/

register_activation_hook( __FILE__, create_function("", '$ver = "5.3"; if( version_compare(phpversion(), $ver, "<") ) die( "This plugin requires PHP version $ver or greater be installed." );') );

require __DIR__.'/index.php';
