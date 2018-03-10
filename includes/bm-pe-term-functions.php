<?php
/**
 * Press Events Term Functions
 *
 * Term specific functions available on both the front-end and admin.
 *
 * @author Burn Media Ltd
 * @package PressEvents/Functions
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Press events wrapper for get_terms()
 *
 * @return array
 */
function bm_pe_get_terms( $args = array() ) {
	$defaults = array(
		'taxonomy' => 'pe_event_tag'
	);

	$args = wp_parse_args( $args, $defaults );

	$terms = get_terms( $args );

	return apply_filters( 'press_events_get_terms', $terms );
}
