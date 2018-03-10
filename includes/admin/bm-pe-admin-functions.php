<?php
/**
 * Press Events Admin Functions
 *
 * @package PressEvents/Classes/Admin
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get all Press Events screen ids.
 *
 * @return array
 */
function bm_pe_get_screen_ids() {
	$screen_ids = array(
		'edit-pe_event',
		'pe_event',
		'edit-pe_event_category',
		'edit-pe_event_tag',
		'edit-pe_event_location',
		'pe_event_location',
		'pe_event_page_pe-settings',
	);

	return apply_filters( 'press_events_screen_ids', $screen_ids );
}
