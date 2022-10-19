<?php 

/**
 * Test – this content is only for testing and should not be included!
 */

function ohf_testing_google_apis_removal() {
    ?>
        <meta http-equiv="test-1" content="test successfull" data-hint="remaining" />
        <meta http-equiv="test-2" content=".googleapis." data-hint="must be removed" />
        <meta http-equiv="test-3" content="no google apis content" data-hint="remaining" />
        <link rel='dns-prefetch' href='//fonts.googleapis.com.example.com' data-hint="must be removed" />
        <link rel='stylesheet' href='//fonts.googleapis.com.example.com/css?family=Montserrat%3A400%7CSource+Sans+Pro%3A400%2C400i%2C700&#038;subset=latin%2Clatin-ext&#038;display=swap&#038;ver=5.8' type='text/css' media='all' />
    <?php
}
add_action( 'wp_head', 'ohf_testing_google_apis_removal' );