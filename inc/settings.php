<?php


/**
 * Include jQuery to admin only
 */

add_action( 'admin_enqueue_scripts', function() {
    wp_enqueue_script( 'jquery' );
} );


/**
 * Add settings
 */

// Add menu item
function ohf_add_settings_page() {
    global $ohf_menu_slug;
    add_options_page( 
        'Own Hosted Fonts', // $page_title
        'Own Hosted Fonts', // $menu_title
        'manage_options', // $capability
        $ohf_menu_slug, // $menu_slug
        'ohf_settings_page', // $callback
        // $position
    );
}
add_action( 'admin_menu', 'ohf_add_settings_page' );


// Add settings page to menu item

function ohf_settings_page() {
    global $data_json;
    ?>
        <div class="wrap">
            <h2><?php _e( 'Own Hosted Fonts Settings', 'own-hosted-fonts' ); ?></h2>
            <form action="options.php" method="post">
                <?php
                    submit_button();
                    do_settings_sections( 'ohf_settings' ); // page
                    settings_fields( 'ohf_settings_group' ); // settings group (may have multiple sections)
                    submit_button();
                ?>
            </form>
            <?php
                // JS for UI of function `ohf_render_multi_checkboxes_input()`
            ?>
            <script>
if ( window.jQuery ) {  
    ( function( $ ) {
        $( document.currentScript ).parent().find( '[data-ohf-ui="input-wrapper"]' ).each( function() {
            var $checkboxes = $( this ).find( '[data-ohf-ui="checkbox"]' );
            $.fn.updateValue = function() {
                var $input = $( this );
                var $checkboxes = $( this ).closest( '[data-ohf-ui="input-wrapper"]' ).find( '[data-ohf-ui="checkbox"]' )
                var newValueArray = [];
                $checkboxes.each( function() {
                    var $checkbox = $( this );
                    if ( $checkbox.prop( 'checked' ) ) {
                        newValueArray.push( $checkbox.val() );
                    }
                } );
                $input.val( newValueArray.join( '|' ) );
            }
            // initial update (done by PHP)
            // var $input = $( this ).find( '[data-ohf-ui="input"]' );
            // $input.updateValue();
            $checkboxes.on( 'change', function() {
                var $input = $( this ).closest( '[data-ohf-ui="input-wrapper"]' ).find( '[data-ohf-ui="input"]' );
                $input.updateValue();
            } );
        } );
    } )( jQuery );
}
else {
    console.error( 'Missing jQuery plugin.' );
}
            </script>
        </div>
    <?php 
}


// Register settings
function ohf_settings_page_setup() {
    global $data_json;
    global $ohf_setting_prefix;
    global $ohf_font_setting_prefix;


    // options section
    add_settings_section(
        'ohf_settings_options_section', // id
        __( 'Options', 'own-hosted-fonts' ), // title
        null, // callback function
        'ohf_settings' // page
    );

    // fields for section
    add_settings_field(
        $ohf_setting_prefix . 'remove_google_fonts', // id
        esc_html__( 'Remove Google Fonts &amp; Google APIs (recommended)', 'own-hosted-fonts' ), // title
        'ohf_render_custom_checkbox', // callback, use unique function name
        'ohf_settings', // page
        'ohf_settings_options_section', // section = 'default'
        array(
            $ohf_setting_prefix . 'remove_google_fonts',
            'label_for' => $ohf_setting_prefix . 'remove_google_fonts'
        ) // args = array()
    );
    register_setting(
        'ohf_settings_group', // settings group
        $ohf_setting_prefix . 'remove_google_fonts' // id
    );

    add_settings_field(
        $ohf_setting_prefix . 'use_preloads', // id
        esc_html__( 'Use Preloads', 'own-hosted-fonts' ), // title
        'ohf_render_custom_checkbox', // callback, use unique function name
        'ohf_settings', // page
        'ohf_settings_options_section', // section = 'default'
        array(
            $ohf_setting_prefix . 'use_preloads',
            'label_for' => $ohf_setting_prefix . 'use_preloads'
        ) // args = array()
    );
    register_setting(
        'ohf_settings_group', // settings group
        $ohf_setting_prefix . 'use_preloads' // id
    );


    // fonts section
    add_settings_section(
        'ohf_settings_fonts_section', // id
        __( 'Fonts (own hosted)', 'own-hosted-fonts' ), // title
        null, // callback function
        'ohf_settings' // page
    );

    if ( isset( $data_json ) && isset( $data_json->fonts ) ) {
        foreach ( $data_json->fonts as $font ) {
            if ( isset( $font->name ) && isset( $font->variants ) ) {
                $font_id = str_replace( ' ', '_', str_replace( '-', '_', strtolower( $font->name ) ) );

                // fields for section
                add_settings_field(
                    $ohf_font_setting_prefix . $font_id, // id
                    $font->name, // title
                    'ohf_render_multi_checkboxes_input', // callback, use unique function name
                    'ohf_settings', // page
                    'ohf_settings_fonts_section', // section = 'default'
                    array(
                        'id' => $ohf_font_setting_prefix . $font_id,
                        'label_for' => $ohf_font_setting_prefix . $font_id,
                        'data' => $font->variants // custom data (font variants data)
                    ) // args = array()
                );

                // register each field
                register_setting(
                    'ohf_settings_group', // settings group
                    $ohf_font_setting_prefix . $font_id // id
                );
            }
        }
    }
}
// Shared  across sections
// modified from https://wordpress.stackexchange.com/questions/129180/add-multiple-custom-fields-to-the-general-settings-page
function ohf_render_custom_input_field( $args ) {
    $options = get_option( $args[ 0 ] );
    echo '<input type="text" id="'  . $args[ 0 ] . '" name="'  . $args[ 0 ] . '" value="' . htmlspecialchars( $options ) . '" />';
}
function ohf_render_custom_checkbox( $args ) {
    $options = get_option( $args[ 0 ] );
    echo '<label><input type="checkbox" id="'  . $args[ 0 ] . '" name="' . $args[ 0 ] . '" value="1"' . ( ( $options ) ? 'checked' : '' ) . ' />' . __( 'Yes', 'own-hosted-fonts' ) . '</label>';
}
function ohf_render_custom_textarea_field( $args ) {
    $options = get_option( $args[ 0 ] );
    echo '<textarea  id="'  . $args[ 0 ] . '" name="'  . $args[ 0 ] . '" rows="6" cols="80" style="font-family:SFMono-Regular,Menlo,Monaco,Consolas,\'Liberation Mono\',\'Courier New\',monospace;">' . $options . '</textarea>';
}
function ohf_render_multi_checkboxes_input( $args ) {
    $option_val = get_option( $args[ 'id' ] );
    ?>
        <div data-ohf-ui="input-wrapper">
            <?php
                echo '<input data-ohf-ui="input" type="hidden" name="' . $args[ 'id' ] . '" value="' . $option_val . '" />';

                // get active font variants from value string
                $checked_values = explode( '|', $option_val );

                if ( isset( $args[ 'data' ] ) ) {
                    $font_variants = $args[ 'data' ];
                    // echo '<pre>';
                    // print_r( $args );
                    // echo '</pre>';
                    echo '<ul>';
                    foreach ( $font_variants as $font_variant ) {
                        if ( isset( $font_variant->name ) && isset( $font_variant->file_slug ) ) {
                            echo '<li><label><input data-ohf-ui="checkbox" type="checkbox" name="' . $font_variant->file_slug . '" value="' . $font_variant->file_slug . '"' . ( ( in_array( $font_variant->file_slug, $checked_values ) ) ? 'checked' : '' ) . ' />' . $font_variant->name . '</label></li>';
                        }
                    }
                    echo '</ul>';
                }
            ?>
        </div>
    <?php
}
add_action( 'admin_init', 'ohf_settings_page_setup' );


/**
 * Add settings link on plugin page
 */

function ohf_add_plugin_settings_link( $links ) {
    global $ohf_menu_slug;
    $settings_link = '<a href="options-general.php?page=' . $ohf_menu_slug . '">' . __( 'Settings' ) . '</a>'; 
    array_unshift( $links, $settings_link ); 
    return $links; 
}
add_filter( 'plugin_action_links_' . OHF_BASENAME, 'ohf_add_plugin_settings_link', 10, 2 );




