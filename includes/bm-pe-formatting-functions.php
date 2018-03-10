<?php
/**
 * Press Events Formatting
 *
 * Functions for formatting data.
 *
 * @author Burn Media Ltd
 * @package PressEvents/Functions
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Convert mysql datetime to PHP timestamp, forcing UTC. Wrapper for strtotime.
 *
 * @since 1.0.0
 * @return int
 */
function bm_pe_string_to_timestamp( $time_string, $from_timestamp = null ) {
    $original_timezone = date_default_timezone_get();

    // @codingStandardsIgnoreStart
    date_default_timezone_set( 'UTC' );

    if ( null === $from_timestamp ) {
        $next_timestamp = strtotime( $time_string );
    } else {
        $next_timestamp = strtotime( $time_string, $from_timestamp );
    }

    date_default_timezone_set( $original_timezone );
    // @codingStandardsIgnoreEnd

    return $next_timestamp;
}

/**
 * Get timezone offset in seconds.
 *
 * @since 1.0.0
 * @return float
 */
function bm_pe_timezone_offset() {
    if ( $timezone = get_option( 'timezone_string' ) ) {
        $timezone_object = new DateTimeZone( $timezone );
        return $timezone_object->getOffset( new DateTime( 'now' ) );
    } else {
        return floatval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS;
    }
}

/**
 * Press Events Timezone - helper to retrieve the timezone string for the site.
 *
 * @since 1.0.0
 * @return string PHP timezone string for the site
 */
function bm_pe_timezone_string() {

    // if site timezone string exists, return it
    if ( $timezone = get_option( 'timezone_string' ) ) {
        return $timezone;
    }

    // get UTC offset, if it isn't set then return UTC
    if ( 0 === ( $utc_offset = intval( get_option( 'gmt_offset', 0 ) ) ) ) {
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
 * Converts a string (e.g. yes or no) to a bool.
 *
 * @since 1.0.0
 * @param string $string
 * @return bool
 */
function bm_pe_string_to_bool( $string ) {
    return is_bool( $string ) ? $string : ( 'yes' === $string || 1 === $string || 'true' === $string || '1' === $string );
}

/**
 * Converts a bool to a 'yes' or 'no'.
 *
 * @since 1.0.0
 * @param bool $bool String to convert.
 * @return string
 */
function bm_pe_bool_to_string( $bool ) {
	if ( ! is_bool( $bool ) ) {
		$bool = bm_pe_string_to_bool( $bool );
	}
	return true === $bool ? 'yes' : 'no';
}

/**
 * Convert a number to hours and minutes
 *
 * @since 1.0.0
 * @param string $string
 * @return bool
 */
function bm_pe_offset_value_to_name( $decimal ) {
    $minus = $decimal < 0 ? 'UTC' : 'UTC+';

    // hour
    $hour = (int)$decimal;
    $hour = ltrim( $hour, 0 ) == '' ? 0 : ltrim( $hour, 0 );

    // minute
    $minute = fmod($decimal, 1) * 60;
    $minute = $minute < 0 ? abs($minute) : $minute;
    $minute = $minute == 0 ? null : ':'. $minute;

    return $minus . $hour . $minute;
}

/**
 * Format a date for output.
 *
 * @since  1.0.0
 * @param  BM_PE_DateTime $date   Instance of BM_PE_DateTime.
 * @param  string $format Data format.
 * @return string
 */
function bm_pe_format_datetime( $date, $format = '' ) {
	if ( ! $format ) {
		$format = bm_pe_date_format();
	}
	if ( ! is_a( $date, 'BM_PE_DateTime' ) ) {
		return '';
	}
	return $date->date_i18n( $format );
}

/**
 * Press Events Date Format - Allows to change date format for Press Events.
 *
 * @return string
 */
function bm_pe_date_format() {
	return apply_filters( 'press_events_date_format', get_option( 'date_format' ) );
}

/**
 * Press Events Time Format - Allows to change time format for Press Events.
 *
 * @return string
 */
function bm_pe_time_format() {
	return apply_filters( 'press_events_time_format', get_option( 'time_format' ) );
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 *
 * @param string|array $var
 * @return string|array
 */
function bm_pe_clean( $var ) {
    if ( is_array( $var ) ) {
        return array_map( 'bm_pe_clean', $var );
    } else {
        return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
    }
}

/**
 * Sanitize permalink values before insertion into DB.
 *
 * Cannot use bm_pe_clean because it sometimes strips % chars and breaks the user's setting.
 *
 * @since  1.0.5
 * @param  string $value Permalink.
 * @return string
 */
function bm_pe_sanitize_permalink( $value ) {
    global $wpdb;

    $value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );

    if ( is_wp_error( $value ) ) {
        $value = '';
    }

    $value = esc_url_raw( trim( $value ) );
    $value = str_replace( 'http://', '', $value );
    return untrailingslashit( $value );
}
