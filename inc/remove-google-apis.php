<?php

/**
 * Remove all Google Apis stuff, e.g. Google Fonts
 */

if ( get_option( $ohf_setting_prefix . 'remove_google_fonts' ) ) {

    add_action( 'get_header', 'ad_ob_start' );
    add_action( 'wp_head', 'ad_ob_end_flush', 100 );
    function ad_ob_start() {
        ob_start( 'ad_filter_wp_head_output' );
    }
    function ad_ob_end_flush() {
        ob_end_flush();
    }
    function ad_filter_wp_head_output( $output ) {
        $needle_pattern = '.googleapis.';
        $any_char_in_html_tag = "[a-zA-Z0-9-_ =\"':.;%\+#&?\/]"; // [a-zA-Z0-9-_ =\"':.;%\+#&?\/]
        $pattern = sprintf( 
            "/<+(%s)+(%s)+(%s)+>/", 
            $any_char_in_html_tag,
            $needle_pattern,
            $any_char_in_html_tag,
        );
        $output = preg_replace( $pattern, "", $output );
        return $output;
    }
    
}