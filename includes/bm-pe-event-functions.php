<?php
/**
 * Press Events Event Functions
 *
 * Functions specific for Events.
 *
 * @author Burn Media Ltd
 * @package PressEvents/Functions
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Function for returning events
 */
function bm_pe_get_events( $args = array() ) {
	$query = new BM_PE_Event_Query( $args );
	return $query->get_events();
}

/**
 * Function for returning an event
 *
 * @since 1.0.0
 */
function bm_pe_get_event( $event = null ) {
	if ( ! did_action( 'press_events_init' ) ) {
		return false;
	}

	if ( $event instanceof WP_Post || is_object( $event ) ) {
		$event = $event->ID;
	}

	if ( ! $event ) {
		$event = get_the_ID();
	}

	return apply_filters( 'press_events_get_event', new BM_PE_Event( $event ) );
}

/**
 * Press Events Event Timezone - helper to retrieve the timezone string for a given event.
 *
 * @since 1.0.0
 * @return string PHP timezone string for an event
 */
function bm_pe_event_timezone_string( $event_id = false ) {
	if ( ! $event_id ) {
		$event_id = get_the_ID();
	}

    // if site timezone string exists, return it
    if ( $timezone = get_post_meta( $event_id, '_event_timezone_string', true ) ) {
        return $timezone;
    }

    // get UTC offset, if it isn't set then return UTC
	$offset = get_post_meta( $event_id, '_event_gmt_offset', true ) ? get_post_meta( $event_id, '_event_gmt_offset', true ) : 0;
    if ( 0 === ( $utc_offset = intval( $offset ) ) ) {
        return 'UTC';
    }

    // adjust UTC offset from hours to seconds
    $utc_offset *= 3600;

    // attempt to guess the timezone string from the UTC offset
    if ( $timezone = timezone_name_from_abbr( '', $utc_offset ) ) {
        return $timezone;
    }

    // last try, guess timezone string manually
    foreach ( timezone_abbreviations_list() as $abbr ) {
        foreach ( $abbr as $city ) {
            if ( (bool) date( 'I' ) === (bool) $city['dst'] && $city['timezone_id'] && intval( $city['offset'] ) === $utc_offset ) {
                return $city['timezone_id'];
            }
        }
    }

    // fallback to UTC
    return 'UTC';
}

/**
 * Press Events Event Timezone Offset - helper to retrieve the timezone offset for a given event.
 *
 * @since 1.0.0
 * @return float
 */
function bm_pe_event_timezone_offset( $event_id = false ) {
	if ( ! $event_id ) {
		$event_id = get_the_ID();
	}

    if ( $timezone = get_post_meta( $event_id, '_event_timezone_string', true ) ) {
        $timezone_object = new DateTimeZone( $timezone );
        return $timezone_object->getOffset( new DateTime( 'now' ) );
    } else {
		$offset = get_post_meta( $event_id, '_event_gmt_offset', true ) ? get_post_meta( $event_id, '_event_gmt_offset', true ) : 0;
        return floatval( $offset ) * HOUR_IN_SECONDS;
    }
}
