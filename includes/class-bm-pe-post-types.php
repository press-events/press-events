<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @package PressEvents/Classes
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BM_PE_Post_types Class.
 */
class BM_PE_Post_types {

	/**
	 * Init Posts types and taxonomies.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_action( 'press_events_flush_rewrite_rules', array( __CLASS__, 'flush_rewrite_rules' ) );
	}

    /**
     * Flush rewrite rules
     */
    public static function flush_rewrite_rules() {
        flush_rewrite_rules();
    }

	/**
	 * Register core taxonomies
	 */
	public static function register_taxonomies() {
		if ( ! is_blog_installed() ) {
			return;
		}

		if ( taxonomy_exists( 'pe_event_category' ) ) {
			return;
		}

		do_action( 'press_events_register_taxonomy' );

		$permalinks = bm_pe_get_permalink_structure();

		register_taxonomy( 'pe_event_category', array( 'pe_event' ),
			apply_filters( 'press_events_taxonomy_args_event_category', array(
				'hierarchical' => true,
				'label' => __( 'Categories', 'press-events' ),
				'labels' => array(
					'name' => __( 'Event categories', 'press-events' ),
					'singular_name' => __( 'Category', 'press-events' ),
					'menu_name' => _x( 'Categories', 'Admin menu name', 'press-events' ),
					'search_items' => __( 'Search categories', 'press-events' ),
					'all_items' => __( 'All categories', 'press-events' ),
					'parent_item' => __( 'Parent category', 'press-events' ),
					'parent_item_colon' => __( 'Parent category:', 'press-events' ),
					'edit_item' => __( 'Edit category', 'press-events' ),
					'update_item' => __( 'Update category', 'press-events' ),
					'add_new_item' => __( 'Add new category', 'press-events' ),
					'new_item_name' => __( 'New category name', 'press-events' ),
					'not_found' => __( 'No categories found', 'press-events' ),
				),
				'show_ui' => true,
				'query_var' => true,
				'rewrite' => array(
					'slug' => $permalinks['category_rewrite_slug'],
					'with_front' => false,
					'hierarchical' => true,
				),
			) )
		);

		register_taxonomy( 'pe_event_tag', array( 'pe_event' ),
			apply_filters( 'press_events_taxonomy_args_event_category', array(
				'hierarchical' => false,
				'label' => __( 'Event tags', 'press-events' ),
				'labels' => array(
					'name' => __( 'Event tags', 'press-events' ),
					'singular_name' => __( 'Tag', 'press-events' ),
					'menu_name' => _x( 'Tags', 'Admin menu name', 'press-events' ),
					'search_items' => __( 'Search tags', 'press-events' ),
					'all_items' => __( 'All tags', 'press-events' ),
					'edit_item' => __( 'Edit tag', 'press-events' ),
					'update_item' => __( 'Update tag', 'press-events' ),
					'add_new_item' => __( 'Add new tag', 'press-events' ),
					'new_item_name' => __( 'New tag name', 'press-events' ),
					'popular_items' => __( 'Popular tags', 'press-events' ),
					'separate_items_with_commas' => __( 'Separate tags with commas', 'press-events' ),
					'add_or_remove_items' => __( 'Add or remove tags', 'press-events' ),
					'choose_from_most_used' => __( 'Choose from the most used tags', 'press-events' ),
					'not_found' => __( 'No tags found', 'press-events' ),
				),
				'show_ui' => true,
				'query_var' => true,
				'rewrite' => array(
					'slug' => $permalinks['tag_rewrite_slug'],
					'with_front' => false
				),
			) )
		);
	}

    /**
	 * Register core post types.
	 */
	public static function register_post_types() {
		do_action( 'press_events_register_post_type' );

		$permalinks = bm_pe_get_permalink_structure();

		register_post_type( 'pe_event',
			apply_filters( 'press_events_register_post_type_event', array(
				'labels' => array(
                    'name' => __( 'Events', 'press-events' ),
					'singular_name' => __( 'Event', 'press-events' ),
					'all_items' => __( 'All Events', 'press-events' ),
					'menu_name' => _x( 'Events', 'Admin menu name', 'press-events' ),
					'add_new' => __( 'Add New', 'press-events' ),
					'add_new_item' => __( 'Add new event', 'press-events' ),
					'edit' => __( 'Edit', 'press-events' ),
					'edit_item' => __( 'Edit event', 'press-events' ),
					'new_item' => __( 'New event', 'press-events' ),
					'view' => __( 'View event', 'press-events' ),
					'view_item' => __( 'View event', 'press-events' ),
					'view_items' => __( 'View events', 'press-events' ),
					'search_items' => __( 'Search events', 'press-events' ),
					'not_found' => __( 'No events found', 'press-events' ),
					'not_found_in_trash' => __( 'No events found in trash', 'press-events' ),
					'parent' => __( 'Parent event', 'press-events' ),
					'featured_image' => __( 'Event image', 'press-events' ),
					'set_featured_image' => __( 'Set event image', 'press-events' ),
					'remove_featured_image' => __( 'Remove event image', 'press-events' ),
					'use_featured_image' => __( 'Use as event image', 'press-events' ),
					'insert_into_item' => __( 'Insert into event', 'press-events' ),
					'uploaded_to_this_item' => __( 'Uploaded to this event', 'press-events' ),
					'filter_items_list' => __( 'Filter events', 'press-events' ),
					'items_list_navigation' => __( 'Events navigation', 'press-events' ),
					'items_list' => __( 'Events list', 'press-events' ),
				),
				'description' => __( 'This is where you can add new events.', 'press-events' ),
				'public' => true,
				'show_ui' => true,
				'publicly_queryable' => true,
				'exclude_from_search' => false,
				'hierarchical' => false,
				'rewrite' => $permalinks['event_rewrite_slug'] ? array(
					'slug' => $permalinks['event_rewrite_slug'],
					'with_front' => false,
					'feeds' => true
				) : false,
				'menu_position' => 5,
				'query_var' => true,
				'supports' => array( 'title', 'editor', 'thumbnail', 'comments' ),
				'has_archive' => $permalinks['event_archive_slug'],
                'show_in_nav_menus' => true,
				'show_in_rest' => true
			) )
		);

		register_post_type( 'pe_event_location',
			apply_filters( 'press_events_register_post_type_event_location', array(
                'labels' => array(
                    'name' => __( 'Event Locations', 'press-events' ),
					'singular_name' => __( 'Event Location', 'press-events' ),
					'all_items' => __( 'Locations', 'press-events' ),
					'menu_name' => _x( 'Locations', 'Admin menu name', 'press-events' ),
					'add_new' => __( 'Add New', 'press-events' ),
					'add_new_item' => __( 'Add new location', 'press-events' ),
					'edit' => __( 'Edit', 'press-events' ),
					'edit_item' => __( 'Edit location', 'press-events' ),
					'new_item' => __( 'New location', 'press-events' ),
					'view' => __( 'View location', 'press-events' ),
					'view_item' => __( 'View location', 'press-events' ),
					'view_items' => __( 'View locations', 'press-events' ),
					'search_items' => __( 'Search locations', 'press-events' ),
					'not_found' => __( 'No locations found', 'press-events' ),
					'not_found_in_trash' => __( 'No locations found in trash', 'press-events' ),
					'parent' => __( 'Parent location', 'press-events' ),
					'featured_image' => __( 'Location image', 'press-events' ),
					'set_featured_image' => __( 'Set location image', 'press-events' ),
					'remove_featured_image' => __( 'Remove location image', 'press-events' ),
					'use_featured_image' => __( 'Use as location image', 'press-events' ),
					'insert_into_item'    => __( 'Insert into location', 'press-events' ),
					'uploaded_to_this_item' => __( 'Uploaded to this location', 'press-events' ),
					'filter_items_list' => __( 'Filter locations', 'press-events' ),
					'items_list_navigation' => __( 'Locations navigation', 'press-events' ),
					'items_list' => __( 'Locations list', 'press-events' ),
				),
				'public' => false,
				'show_ui' => false,
				'hierarchical' => false,
				'supports' => array( 'title' ),
                'show_in_menu' => false,
				'rewrite' => false,
			) )
		);

		do_action( 'press_events_after_register_post_type' );
	}

}
BM_PE_Post_types::init();
