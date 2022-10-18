<?php

/**
 * Plugin Name: Own Hosted Fonts (remove Google Fonts)
 * Plugin URI: https://github.com/ihniwiad/own-hosted-fonts
 * Description: Includes selectable fonts to WordPress Theme, can remove all Google Fonts (and Google APIs) code from Theme.
 * Version: 1.0
 * Author: ihniwiad
 * Text Domain: own-hosted-fonts
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;


if ( ! defined( 'OHF_FILE' ) ) {
    define( 'OHF_FILE', __FILE__ );
}
if ( ! defined( 'OHF_FILE_PATH' ) ) {
    define( 'OHF_FILE_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'OHF_BASENAME' ) ) {
    define( 'OHF_BASENAME', plugin_basename( OHF_FILE ) );
}
// get plugin url
$homeUrl = get_bloginfo( 'url' ) . '/';
$plugin_url = plugin_dir_url( __FILE__ ); // do NOT use `plugin_dir_path( __FILE__ )` here!
$root_related_plugin_url = explode( str_replace( 'https://', 'http://', $homeUrl ), str_replace( 'https://', 'http://', $plugin_url ) )[ 1 ];
if ( ! defined( 'OHF_PLUGIN_URL' ) ) {
    define( 'OHF_PLUGIN_URL', $plugin_url );
}
if ( ! defined( 'ROOT_RELATED_PLUGIN_URL' ) ) {
    define( 'ROOT_RELATED_PLUGIN_URL', $root_related_plugin_url );
}



/**
 * Load plugin textdomain.
 */

function ohf_load_textdomain() {
	load_plugin_textdomain( 'own-hosted-fonts', false, dirname( plugin_basename( OHF_FILE ) ) . '/languages' );
}
add_action( 'init', 'ohf_load_textdomain' );



/**
 * Test – this file is only for testing and should not be included!
 */

// include 'inc/test.php';


/**
 * Plugin contents
 */

include 'inc/variables.php';
include 'inc/remove-google-apis.php';
include 'inc/settings.php';
include 'inc/create-files.php';
include 'inc/preloads.php';
include 'inc/include-fonts.php';



