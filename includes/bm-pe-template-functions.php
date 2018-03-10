<?php
/**
 * Press Events Template Functions
 *
 * Functions for the templating system.
 *
 * @author Burn Media Ltd
 * @package PressEvents/Functions
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * When the_post is called, put event data into a global.
 *
 * @param mixed $post Post Object.
 * @return BM_PE_Event
 */
function bm_pe_setup_event_data( $post ) {
	unset( $GLOBALS['event'] );

	if ( is_int( $post ) ) {
		$the_post = get_post( $post );
	} else {
		$the_post = $post;
	}

	if ( empty( $the_post->post_type ) || $the_post->post_type !== 'pe_event' ) {
		return;
	}

	$GLOBALS['event'] = bm_pe_get_event( $the_post );

	return $GLOBALS['event'];
}
add_action( 'the_post', 'bm_pe_setup_event_data' );

/**
 * Add body classes for Events pages.
 *
 * @param  array $classes Body Classes.
 * @return array
 */
function bm_pe_body_class( $classes ) {
	$classes = (array) $classes;

	if ( is_BM_Press_Events() ) {
		$classes[] = 'press-events';
		$classes[] = 'press-events-page';
	}

	return array_unique( $classes );
}

/**
 * Adds extra post classes for events.
 *
 * @since 1.0.0
 * @param array $classes Current classes.
 * @param string|array $class Additional class.
 * @param int $post_id Post ID.
 * @return array
 */
function bm_pe_event_post_class( $classes, $class = '', $post_id = '' ) {
	if ( ! $post_id || ! in_array( get_post_type( $post_id ), array( 'pe_event' ), true ) ) {
		return $classes;
	}

	$event = bm_pe_get_event( $post_id );

	if ( $event->get_id() > 0 ) {
		$classes[] = 'event';

		if ( $event->is_featured() ) {
			$classes[] = 'featured-event';
		}

	}

    $key = array_search( 'hentry', $classes, true );

    if ( false !== $key ) {
		unset( $classes[ $key ] );
	}

    return $classes;
}

/**
 * Output generator tag.
 */
function bm_pe_generator_tag( $gen, $type ) {
    switch ( $type ) {
		case 'html':
            $gen .= "\n" . '<meta name="generator" content="Press Events ' . esc_attr( BM_PE_VERSION ) . '">';
            break;
        case 'xhtml':
            $gen .= "\n" . '<meta name="generator" content="Press Events ' . esc_attr( BM_PE_VERSION ) . '" />';
            break;
    }

    return $gen;
}

/**
 * Output generator tag.
 */
function bm_pe_taxonomy_url( $termlink, $term, $taxonomy ) {
	if ( $taxonomy == 'pe_event_category' ) {
        $query_vars = BM_Press_Events()->query->get_query_vars();
		$termlink = add_query_arg( $query_vars['archive_category'], $term->slug, get_post_type_archive_link( 'pe_event' ) );
	} elseif ( $taxonomy == 'pe_event_tag' ) {
        $query_vars = BM_Press_Events()->query->get_query_vars();
		$termlink = add_query_arg( $query_vars['archive_tag'], $term->slug, get_post_type_archive_link( 'pe_event' ) );
	}

	return $termlink;
}

if ( ! function_exists( 'bm_pe_set_calendar_global' ) ) {
	/**
	 * Set the global calendar event.
	 */
	function bm_pe_set_calendar_global( $vars = false ) {
		if ( ! $vars ) {
			$query_vars = BM_Press_Events()->query->get_query_vars();
			$vars = array(
				'archive_type' => get_query_var( $query_vars['archive_type'] ) !== '' ? get_query_var( $query_vars['archive_type'] ) : bm_pe_get_option( 'default-archive-view', 'pe-general-events', 'list' ),
				'archive_query' => get_query_var( $query_vars['archive_query'] ),
				'archive_month' => get_query_var( $query_vars['archive_month'] ),
				'archive_category' => get_query_var( $query_vars['archive_category'] ),
				'archive_tag' => get_query_var( $query_vars['archive_tag'] )
			);
		}

		$GLOBALS['calendar'] = new BM_PE_Calendar( $vars );
	}
}

if ( ! function_exists( 'bm_pe_archive_header' ) ) {
	/**
	 * Output the event archive header.
	 */
	function bm_pe_archive_header() {
		if ( ! is_ajax() ) {
			bm_pe_get_template( 'archive/header.php' );
		}
	}
}

if ( ! function_exists( 'bm_pe_archive_filters' ) ) {
	/**
	 * Output the event filters.
	 */
	function bm_pe_archive_filters() {
		if ( ! is_ajax() ) {
			bm_pe_get_template( 'archive/filters.php' );
		}
	}
}

if ( ! function_exists( 'bm_pe_no_events_found' ) ) {
	/**
	 * Output the no events found notice
	 */
	function bm_pe_no_events_found() {
		bm_pe_get_template( 'archive/no-events-found.php' );
	}
}

/**
 * Single event
 */
if ( !function_exists( 'bm_pe_single_event_archive_link' ) ) {
	/**
	 * Output the event archive button.
	 */
	function bm_pe_single_event_archive_link() {
		bm_pe_get_template( 'single-event/all-events.php' );
	}
}

if ( !function_exists( 'bm_pe_single_event_title' ) ) {
	/**
	 * Output the event title in event summary.
	 */
	function bm_pe_single_event_title() {
		bm_pe_get_template( 'single-event/title.php' );
	}
}

if ( !function_exists( 'bm_pe_single_event_date' ) ) {
	/**
	 * Output the event date below the title.
	 */
	function bm_pe_single_event_date() {
		bm_pe_get_template( 'single-event/title-date.php' );
	}
}

if ( !function_exists( 'bm_pe_single_event_featured_image' ) ) {
	/**
	 * Output the event image before the event summary.
	 */
	function bm_pe_single_event_featured_image() {
		bm_pe_get_template( 'single-event/featured-image.php' );
	}
}

if ( !function_exists( 'bm_pe_single_event_description' ) ) {
	/**
	 * Output the event description.
	 */
	function bm_pe_single_event_description() {
		bm_pe_get_template( 'single-event/description.php' );
	}
}

if ( !function_exists( 'bm_pe_single_event_date_time' ) ) {
	/**
	 * Output the event date & time.
	 */
	function bm_pe_single_event_date_time() {
		bm_pe_get_template( 'single-event/date-time.php' );
	}
}

if ( !function_exists( 'bm_pe_single_event_organisers' ) ) {
	/**
	 * Output the event organisers.
	 */
	function bm_pe_single_event_organisers() {
		bm_pe_get_template( 'single-event/organisers.php' );
	}
}

if ( !function_exists( 'bm_pe_single_event_location' ) ) {
	/**
	 * Output the event location..
	 */
	function bm_pe_single_event_location() {
		bm_pe_get_template( 'single-event/location.php' );
	}
}

if ( !function_exists( 'bm_pe_single_event_share' ) ) {
	/**
	 * Output the event social share.
	 */
	function bm_pe_single_event_share() {
		bm_pe_get_template( 'single-event/share.php' );
	}
}

if ( !function_exists( 'bm_pe_single_event_tags' ) ) {
	/**
	 * Output the event tags.
	 */
	function bm_pe_single_event_tags() {
		bm_pe_get_template( 'single-event/tags.php' );
	}
}
