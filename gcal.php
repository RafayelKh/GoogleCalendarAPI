<?php
/**
 * Plugin Name: Google Calendar API
 * Description: Allow work Google Calendar API wrappers
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define MMGC_ABSPATH.
if ( ! defined( 'GCAL_ABSPATH' ) ) {
	define( 'GCAL_ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Include files */
include_once GCAL_ABSPATH . 'includes/gcal-functions.php';
