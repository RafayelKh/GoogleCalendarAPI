<?php

defined( 'ABSPATH' ) || exit;

require GCAL_ABSPATH . '/vendor/autoload.php';

/**
 * Push events to Google Calendat
 */
function gcal_push_events( $events ) {
	/** TODO: here can validate $event data */
	$gcal = GoogleCal::get_instance();

	return $gcal->push_events( $events );
}

/**
 * Get Google Calendar Events
 */
function gcal_get_events() {
	$gcal = GoogleCal::get_instance();

	return $gcal->get_events();
}
