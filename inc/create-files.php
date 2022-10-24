<?php 

/**
 * Hook into options page after save
 */



if ( 
    isset( $data_json )
    && isset( $data_json->fonts )
    && isset( $data_json->main_folder )
    && isset( $data_json->css_file )
    && isset( $data_json->template )
    && isset( $data_json->template->replace_patterns )
    && isset( $data_json->template->replace_patterns->comment )
    && isset( $data_json->template->replace_patterns->name )
    && isset( $data_json->template->replace_patterns->font_style )
    && isset( $data_json->template->replace_patterns->font_weight )
    && isset( $data_json->template->replace_patterns->url_list )
    && isset( $data_json->template->replace_patterns->url )
    && isset( $data_json->template->replace_patterns->format )
    && isset( $data_json->template->font_face )
    && isset( $data_json->template->url_list_item )
) {
    foreach ( $data_json->fonts as $font ) {
        if ( isset( $font->name ) && isset( $font->variants ) && isset( $font->folder ) && isset( $font->file_trunc ) && isset( $font->file_types ) ) {
            $font_id = str_replace( ' ', '_', str_replace( '-', '_', strtolower( $font->name ) ) );

            $args = array(
                'font_id' =>  $font_id,
                'font' => $font
            );

            // read more here: https://stackoverflow.com/questions/2843356/can-i-pass-arguments-to-my-function-through-add-action
            add_action( 'update_option_' . $ohf_font_setting_prefix . $font_id, 
                function( $old_value, $new_value ) use ( $args ) { 
                    ohf_update_files_after_options_save( $old_value, $new_value, $args ); 
                }, 10, 3
            );
        }
    }
}

// callback function for hook `update_option_...`
function ohf_update_files_after_options_save( $old_value, $new_value, $args ) {
    global $data_json;
    global $ohf_font_setting_prefix;

    $font_id = $args[ 'font_id' ];
    $font = $args[ 'font' ];

    // TEST

    // open & write file
    
    // $file_path = OHF_FILE_PATH . $data_json->main_folder . '/' . 'debug.txt';
    // $file = fopen( $file_path, "w" ) or die( "Unable to open file!" );
    // fwrite( $file, 'font name: ' . $font->name . "\n" . 'old: ' . $old_value . "\n" . 'new: ' . $new_value . "\n" . 'saved: ' . date( "Y-m-d H:i:s" ) );
    // fclose( $file );

    // /TEST


    if ( $old_value != $new_value ) {
        // font config has changed, rebuild CSS file

        // get active font variants from new value string
        $active_file_slugs = explode( '|', $new_value );
        $templ = $data_json->template;
        $repl_patt = $templ->replace_patterns;
        $font_face_text = '';
        $style_preload_text = '';
        $fonts_preload_text = '';

        foreach ( $active_file_slugs as $file_slug ) {

            if ( ! empty( $file_slug ) ) {

                $url_items = [];

                foreach ( $font->file_types as $index => $file_type ) {
                    $url = ROOT_RELATED_PLUGIN_URL . $data_json->main_folder . '/' . $font->folder . '/' . $font->file_trunc . $file_slug . '.' . $file_type;

                    // $test_content .= "\n     " . $url;
                    $text = $templ->url_list_item;
                    $text = str_replace( $repl_patt->format, $file_type, $text );
                    $url_items[] = str_replace( $repl_patt->url, '/' . $url, $text );

                    // use 1st item, make style preload text
                    if ( $index == 0 && isset( $templ->font_preload ) ) {
                        $font_preload_item = $templ->font_preload;
                        $font_preload_item = str_replace( $repl_patt->url, OHF_HOME_URL . $url, $font_preload_item );
                        $font_preload_item = str_replace( $repl_patt->format, $file_type, $font_preload_item );
                        $fonts_preload_text .= $font_preload_item . "\n";
                    }
                }

                $urls_string = implode( ",\n", $url_items );

                $text = $templ->font_face;
                $text = str_replace( $repl_patt->comment, $font->name . ' ' . $file_slug, $text );
                $text = str_replace( $repl_patt->name, $font->name, $text );

                // get font style
                if ( strpos( strtolower( $file_slug ), 'italic' ) !== false ) {
                    $font_style = 'italic';
                }
                else {
                    $font_style = 'normal';
                }
                $text = str_replace( $repl_patt->font_style, $font_style, $text );

                // get fon weight
                // TODO: what about “Thin”, “Light”, “Medium”, “Bold”, “Black”, etc?
                if ( strtolower( $file_slug ) == 'regular' || strtolower( $file_slug ) == 'italic' ) {
                    $font_weight = 400;
                }
                else {
                   $font_weight = ( int ) $file_slug;
                }
                $text = str_replace( $repl_patt->font_weight, $font_weight, $text );

                // add urls
                $text = str_replace( $repl_patt->url_list, $urls_string, $text );

                $font_face_text .= $text . "\n\n";

            } // if ( ! empty( $file_slug ) )
        }

        // prepare paths
        $font_folder_path = OHF_FILE_PATH . $data_json->main_folder . '/' . $font->folder;
        $font_folder_url = OHF_PLUGIN_URL . $data_json->main_folder . '/' . $font->folder;
        $css_path = $data_json->main_folder . '/' . $font->folder . '/' . $data_json->css_file;

        // write css file befor trying to get file time version param
        // open & write style file
        $file_path = $font_folder_path . '/' . $data_json->css_file;
        $file = fopen( $file_path, "w" ) or die( "Unable to create file!" );
        fwrite( $file, $font_face_text );
        fclose( $file );

        // get file time version param (after file is written)
        $css_version = file_exists( $file_path ) ? filemtime( $file_path ) : 'null';
        $css_file_url = $font_folder_url . '/' . $data_json->css_file . '?v=' . $css_version; 

        // make style preload text
        if ( isset( $templ->style_preload ) ) {
            $style_preload_text = $templ->style_preload;
            $style_preload_text = str_replace( $repl_patt->url, $css_file_url, $style_preload_text );
        }

        // open & write preload file
        $file_path = $font_folder_path . '/' . $data_json->preload_file;
        $file = fopen( $file_path, "w" ) or die( "Unable to create file!" );
        fwrite( $file, $style_preload_text . "\n" . $fonts_preload_text );
        fclose( $file );
    }
}