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
 * is_bm_press_events - Returns true if on a page which uses a Press Events template.
 *
 * @return bool
 */
function is_bm_press_events() {
	return apply_filters( 'is_press_events', ( is_bm_pe_event() || is_bm_pe_event_taxonomy() || is_bm_pe_event_archive() ) ? true : false );
}

/**
 * is_bm_pe_event_taxonomy - Returns true when viewing a event taxonomy archive.
 *
 * @return bool
 */
function is_bm_pe_event_taxonomy() {
    return is_tax( get_object_taxonomies( 'pe_event' ) );
}

/**
 * is_bm_pe_event_page - Returns true when viewing any event page
 *
 * @return bool
 */
function is_bm_pe_event_page() {
	if ( is_bm_pe_event() ) {
		return true;
	} elseif ( is_bm_pe_event_taxonomy() ) {
		return true;
	} elseif ( is_bm_pe_event_archive() ) {
		return true;
	}

	return false;
}

/**
 * is_bm_pe_event_query - Returns true when displaying event query
 *
 * @return bool
 */
function is_bm_pe_event_query() {
	global $wp_query;
	return apply_filters( 'press_events_is_event_query', ! empty( $wp_query->get( 'bm_pe_query' ) ) );
}

/**
 * is_bm_pe_event - Returns true when viewing a single event.
 *
 * @return bool
 */
function is_bm_pe_event() {
	return is_singular( array( 'pe_event' ) );
}

/**
 * is_bm_pe_event_archive - Returns true when viewing the event type archive.
 *
 * @return bool
 */
function is_bm_pe_event_archive() {
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
