<?php

/**
 * Plugin Name: Own Hosted Fonts (remove Google Fonts)
 * Plugin URI: https://github.com/ihniwiad/own-hosted-fonts
 * Description: Includes selectable fonts to WordPress Theme, can remove all Google Fonts (and Google APIs) code from Theme.
 * Version: 1.0.5
 * Author: ihniwiad
 * Text Domain: own-hosted-fonts
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;


if ( ! defined( 'OWHOF_FILE' ) ) {
    define( 'OWHOF_FILE', __FILE__ );
}
if ( ! defined( 'OWHOF_FILE_PATH' ) ) {
    define( 'OWHOF_FILE_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'OWHOF_BASENAME' ) ) {
    define( 'OWHOF_BASENAME', plugin_basename( OWHOF_FILE ) );
}
// get plugin url
$home_url = get_bloginfo( 'url' ) . '/';
if ( ! defined( 'OWHOF_HOME_URL' ) ) {
    define( 'OWHOF_HOME_URL', $home_url );
}
$plugin_url = plugin_dir_url( __FILE__ ); // do NOT use `plugin_dir_path( __FILE__ )` here!
$root_related_plugin_url = explode( str_replace( 'https://', 'http://', $home_url ), str_replace( 'https://', 'http://', $plugin_url ) )[ 1 ];
if ( ! defined( 'OWHOF_PLUGIN_URL' ) ) {
    define( 'OWHOF_PLUGIN_URL', $plugin_url );
}
if ( ! defined( 'OWHOF_ROOT_RELATED_PLUGIN_URL' ) ) {
    define( 'OWHOF_ROOT_RELATED_PLUGIN_URL', $root_related_plugin_url );
}



/**
 * Load plugin textdomain.
 */

function ohf_load_textdomain() {
	load_plugin_textdomain( 'own-hosted-fonts', false, dirname( plugin_basename( OWHOF_FILE ) ) . '/languages' );
}
add_action( 'init', 'ohf_load_textdomain' );



/**
 * Plugin contents
 */

include 'inc/variables.php';
include 'inc/remove-google-apis.php';
include 'inc/settings.php';
include 'inc/create-files.php';
include 'inc/preloads.php';
include 'inc/include-fonts.php';



