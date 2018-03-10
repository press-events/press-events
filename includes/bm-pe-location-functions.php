<?php
/**
 * Press Events Location Specific Core Functions
 *
 * Location specific core functions available on both the front-end and admin.
 *
 * @author Burn Media Ltd
 * @package PressEvents/Functions
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get an event location
 */
function bm_pe_get_location( $location = null ) {
	if ( ! did_action( 'press_events_init' ) ) {
		return false;
	}

	if ( $location instanceof WP_Post || is_object( $location ) ) {
		$location = $location->ID;
	}

	if ( ! $location ) {
		$location = get_the_ID();
	}

	return apply_filters( 'press_events_get_location', new BM_PE_Event_Location( $location ) );
}

/**
 * Get all the locations
 */
function bm_pe_get_locations( $args = array() ) {
    $args['post_type'] = 'pe_event_location';

	$results = new WP_Query( $args );

	if ( $results->posts ) {
		$results->posts = array_map( 'bm_pe_get_location', $results->posts );
	}

	return apply_filters( 'press_events_event_location_object_query', $results->posts, $args );
}
