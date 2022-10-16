<?php 

/**
 * Include preloads
 */

if ( get_option( $ohf_setting_prefix . 'use_preloads' ) ) {

    function ohf_include_preloads() {
        global $data_json;
        global $ohf_font_setting_prefix;

        // loop through fonts, check options
        if ( 
            isset( $data_json )
            && isset( $data_json->fonts )
            && isset( $data_json->main_folder )
            && isset( $data_json->preload_file )
        ) {
            $preloads_text = '';

            foreach ( $data_json->fonts as $font ) {
                if ( isset( $font->name ) && isset( $font->folder ) ) {
                    $font_id = str_replace( ' ', '_', str_replace( '-', '_', strtolower( $font->name ) ) );

                    $setting_id = $ohf_font_setting_prefix . $font_id;

                    if ( get_option( $setting_id ) ) {
                        $preload_file = $data_json->main_folder . '/' . $font->folder . '/' . $data_json->preload_file;
                        if ( file_exists( ROOT_RELATED_PLUGIN_URL . $preload_file ) ) {
                            // load file contents, add to prelaods text
                            // echo 'TEST_1: file exists: ' . ROOT_RELATED_PLUGIN_URL . $preload_file;
                            $preload_html = file_get_contents( ROOT_RELATED_PLUGIN_URL . $preload_file );
                            // echo 'TEST_1: file exists: ' . "\n\n" . $preload_html;

                            $preloads_text .= '<!-- preload font “' . $font->name . '” -->' . "\n";
                            $preloads_text .= $preload_html . "\n";
                        }
                        else {
                            echo '<!-- no preload file found for font “' . $font->name . '” -->';
                        }
                    }
                }
            }

            // print prelaods text
            echo $preloads_text;
        }
    }
    add_action( 'wp_head', 'ohf_include_preloads' );

}