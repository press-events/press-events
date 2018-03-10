<?php
/**
 * Press Events Organiser Specific Functions
 *
 * Organiser specific functions available on both the front-end and admin.
 *
 * @author Burn Media Ltd
 * @package PressEvents/Functions
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all the eligable event organisers
 *
 * @since 1.0.0
 */
function bm_pe_get_organisers( $args = array() ) {
	$args['role__in'] = apply_filters( 'event_organiser_roles', array(
		'event_organiser'
	) );

	$users = get_users( $args );

	$organisers = array();

	foreach ( $users as $user ) {
		try {
			$organisers[$user->ID] = new BM_PE_Event_Organiser($user->ID);
		} catch (Exception $e) {
			// Organiser dosen't exist
		}
	}

	// Add empty
	if ( isset( $args['include'] ) && is_array( $args['include'] ) && in_array( 0, $args['include'] ) ) {
		$organisers[0] = new BM_PE_Event_Organiser(0);
	}

	return $organisers;
}
