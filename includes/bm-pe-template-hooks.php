<?php
/**
 * Press Events Template Hooks
 *
 * Action/filter hooks used for Press Events functions/templates.
 *
 * @package PressEvents/Templates
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_filter( 'body_class', 'pe_body_class' );
add_filter( 'post_class', 'pe_event_post_class', 20, 3 );

/**
 * WP Header.
 *
 * @see pe_generator_tag()
 */
add_action( 'get_the_generator_html', 'pe_generator_tag', 10, 2 );
add_action( 'get_the_generator_xhtml', 'pe_generator_tag', 10, 2 );

/**
 * Edit the event taxonomy url (to forward to a filtered archive not a separate page)
 *
 * @see pe_taxonomy_url()
 */
add_filter( 'term_link', 'pe_taxonomy_url', 10, 3 );

/**
 * Archive calendar
 *
 * @see press_events_set_calendar_global()
 */
add_action( 'press_events_before_get_calendar', 'press_events_set_calendar_global' );

/**
 * Archive event
 *
 * @see press_events_archive_event()
 * @see press_events_archive_header()
 * @see press_events_archive_filters()
 */
add_action( 'press_events_before_archive_event', 'press_events_archive_header', 5 );
add_action( 'press_events_before_archive_event', 'press_events_archive_filters', 10 );

/**
 * No events found
 *
 * @see press_events_no_events_found()
 */
add_action( 'press_events_no_events_found', 'press_events_no_events_found' );

/**
 * Before Single Event Content Div.
 *
 * @see press_events_single_event_archive_link()
 * @see press_events_single_event_title()
 * @see press_events_single_event_date()
 * @see press_events_single_event_featured_image()
 */
add_action( 'press_events_before_single_event_content', 'press_events_single_event_archive_link', 5 );
add_action( 'press_events_before_single_event_content', 'press_events_single_event_title', 10 );
add_action( 'press_events_before_single_event_content', 'press_events_single_event_date', 15 );
add_action( 'press_events_before_single_event_content', 'press_events_single_event_featured_image', 20 );

/**
 * Single Event Content Div.
 *
 * @see press_events_single_event_description()
 * @see press_events_single_event_date_time()
 * @see press_events_single_event_organisers()
 */
add_action( 'press_events_single_event_content', 'press_events_single_event_description', 5 );
add_action( 'press_events_single_event_content', 'press_events_single_event_date_time', 10 );
add_action( 'press_events_single_event_content', 'press_events_single_event_organisers', 15 );

/**
 * Single Event Content Sidebar Div.
 *
 * @see press_events_single_event_location()
 * @see press_events_single_event_share()
 * @see press_events_single_event_tags()
 */
add_action( 'press_events_single_event_sidebar', 'press_events_single_event_location', 5 );
add_action( 'press_events_single_event_sidebar', 'press_events_single_event_share', 10 );
add_action( 'press_events_single_event_sidebar', 'press_events_single_event_tags', 15 );
