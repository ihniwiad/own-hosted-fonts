<?php


/**
 * Var
 */

$ohf_menu_slug = 'own-hosted-fonts';
$ohf_setting_prefix = 'ohf_';
$ohf_font_setting_prefix = $ohf_setting_prefix . 'use_';
$data_file = file_get_contents( OHF_FILE_PATH . 'data.json' );
$data_json = json_decode( $data_file );