<?php
/**
 * Installation related functions and actions.
 *
 * @author Burn Media Ltd
 * @package PressEvents/Classes
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BM_PE_Install Class.
 */
class BM_PE_Install {

	/**
	 * Install Press Events
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		self::create_roles();
		self::setup_environment();

		do_action( 'press_events_flush_rewrite_rules' );
		do_action( 'press_events_installed' );
	}

	/**
	 * Create the Post Types
	 *
 	 * @since 1.0.0
 	 */
 	private static function setup_environment() {
		BM_PE_Post_types::register_post_types();
 	}

	/**
	 * Create user roles
	 *
	 * @since 1.0.0
	 */
 	public static function create_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
		}

		// Event organiser role.
		add_role(
			'event_organiser',
			__( 'Event organiser', 'press-events' ),
			array(
				'level_9'                => true,
				'level_8'                => true,
				'level_7'                => true,
				'level_6'                => true,
				'level_5'                => true,
				'level_4'                => true,
				'level_3'                => true,
				'level_2'                => true,
				'level_1'                => true,
				'level_0'                => true,
				'read'                   => true,
				'read_private_pages'     => true,
				'read_private_posts'     => true,
				'edit_users'             => true,
				'edit_posts'             => true,
				'edit_pages'             => true,
				'edit_published_posts'   => true,
				'edit_published_pages'   => true,
				'edit_private_pages'     => true,
				'edit_private_posts'     => true,
				'edit_others_posts'      => true,
				'edit_others_pages'      => true,
				'publish_posts'          => true,
				'publish_pages'          => true,
				'delete_posts'           => true,
				'delete_pages'           => true,
				'delete_private_pages'   => true,
				'delete_private_posts'   => true,
				'delete_published_pages' => true,
				'delete_published_posts' => true,
				'delete_others_posts'    => true,
				'delete_others_pages'    => true,
				'manage_categories'      => true,
				'manage_links'           => true,
				'moderate_comments'      => true,
				'upload_files'           => true,
				'export'                 => true,
				'import'                 => true,
				'list_users'             => true,
			)
		);

		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $group ) {
			foreach ( $group as $capability ) {
				$wp_roles->add_cap( 'event_organiser', $capability );
				$wp_roles->add_cap( 'administrator', $capability );
			}
		}
	}

	/**
	 * Get Press Events capabilities.
	 *
	 * @return array
	 */
	private static function get_core_capabilities() {
		$capabilities = array();

		$capabilities['core'] = array(
			'manage_press_events',
		);

		$capability_types = array( 'pe_event', 'pe_event_location' );

		foreach ( $capability_types as $capability_type ) {
			$capabilities[ $capability_type ] = array(
				// Post type.
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",
				// Terms.
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms",
			);
		}

		return $capabilities;
	}
}
