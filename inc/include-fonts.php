<?php 

/**
 * Include font style
 */

function ohf_register_fonts_style() {
    global $data_json;
    global $ohf_font_setting_prefix;

    // loop through fonts, check options
    if ( 
        isset( $data_json )
        && isset( $data_json->fonts )
        && isset( $data_json->main_folder )
        && isset( $data_json->css_file )
    ) {
        foreach ( $data_json->fonts as $font ) {
            if ( isset( $font->name ) && isset( $font->folder ) ) {
                $font_id = str_replace( ' ', '_', str_replace( '-', '_', strtolower( $font->name ) ) );

                $setting_id = $ohf_font_setting_prefix . $font_id;

                if ( get_option( $setting_id ) ) {
                    $css_file = $data_json->main_folder . '/' . $font->folder . '/' . $data_json->css_file;
                    $css_version = file_exists( ROOT_RELATED_PLUGIN_URL . $css_file ) ? filemtime( ROOT_RELATED_PLUGIN_URL . $css_file ) : 'null';
                    wp_enqueue_style( strtolower( $font->name ) . '-font-style', plugins_url( $css_file . '?' . $css_version , OHF_FILE ) );
                }
            }
        }
    }
}
add_action( 'wp_enqueue_scripts', 'ohf_register_fonts_style' );