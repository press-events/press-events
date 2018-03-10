<?php
/**
 * Press Events Conditional Functions
 *
 * @package PressEvents/Functions
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * is_press_events - Returns true if on a page which uses a Press Events template.
 *
 * @return bool
 */
function is_BM_Press_Events() {
	return apply_filters( 'is_press_events', ( is_event() || is_event_taxonomy() || is_event_archive() ) ? true : false );
}

/**
 * is_event_taxonomy - Returns true when viewing a event taxonomy archive.
 *
 * @return bool
 */
function is_event_taxonomy() {
    return is_tax( get_object_taxonomies( 'pe_event' ) );
}

/**
 * is_event_page - Returns true when viewing any event page
 *
 * @return bool
 */
function is_event_page() {
	if ( is_event() ) {
		return true;
	} elseif ( is_event_taxonomy() ) {
		return true;
	} elseif ( is_event_archive() ) {
		return true;
	}

	return false;
}

/**
 * is_event_query - Returns true when displaying event query
 *
 * @return bool
 */
function is_event_query() {
	global $wp_query;
	return apply_filters( 'press_events_is_event_query', ! empty( $wp_query->get( 'pe_query' ) ) );
}

/**
 * is_event - Returns true when viewing a single event.
 *
 * @return bool
 */
function is_event() {
	return is_singular( array( 'pe_event' ) );
}

/**
 * is_event_archive - Returns true when viewing the event type archive.
 *
 * @return bool
 */
function is_event_archive() {
	return is_post_type_archive( 'pe_event' );
}

if ( ! function_exists( 'is_ajax' ) ) {
	/**
	 * Is_ajax - Returns true when the page is loaded via ajax.
	 *
	 * @return bool
	 */
	function is_ajax() {
		return defined( 'BM_PE_DOING_AJAX' );
	}
}
