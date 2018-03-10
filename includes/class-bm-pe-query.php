<?php
/**
 * PE query that modifies the frontend WP_Query
 *
 * @package PressEvents/Classes
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BM_PE_Query Class.
 */
class BM_PE_Query {

	/**
	 * Query vars to add to wp.
	 *
	 * @var array
	 */
	public $query_vars = array();

	/**
	 * Reference to the main event query on the page.
	 *
	 * @var array
	 */
	private static $event_query;

	public function __construct() {
		if ( ! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
			add_action( 'parse_request', array( $this, 'parse_request' ), 0 );
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		}

		$this->init_query_vars();
	}

	/**
	 * Init query vars.
	 */
	public function init_query_vars() {
		// Query vars to add to WP.
		$this->query_vars = array(
			'archive_type' => _x( 'archive-type', 'query_vars', 'press-events' ),
			'archive_query' => _x( 'archive-query', 'query_vars', 'press-events' ),
			'archive_month' => _x( 'archive-month', 'query_vars', 'press-events' ),
			'archive_category' => _x( 'archive-category', 'query_vars', 'press-events' ),
			'archive_tag' => _x( 'archive-tag', 'query_vars', 'press-events' ),
		);
	}

	/**
	 * Add query vars.
	 *
	 * @access public
	 *
	 * @param array $vars Query vars.
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		foreach ( $this->get_query_vars() as $var ) {
			$vars[] = $var;
		}
		return $vars;
	}

	/**
	 * Get query vars.
	 *
	 * @return array
	 */
	public function get_query_vars() {
		return apply_filters( 'press_events_get_query_vars', $this->query_vars );
	}

	/**
	 * Parse the request and look for query vars
	 */
	public function parse_request() {
		global $wp;

		// Map query vars to their keys
		foreach ( $this->get_query_vars() as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) { // WPCS: input var ok, CSRF ok.
				$wp->query_vars[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $var ] ) ); // WPCS: input var ok, CSRF ok.
			} elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
	}

	/**
	 * Hook into event_order and change order of events (by event start time not blog post time)
	 *
	 * @param WP_Query $query Query instance.
	 */
	public function pre_get_posts( $query ) {
		// We only want to affect the main query.
		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( 'pe_event' !== $query->get('post_type') ) {
			return;
		}

		$this->event_query( $query );

		// And remove the pre_get_posts hook.
		$this->remove_event_query();
	}

	/**
	 * Query the events, applying sorting/ordering etc.
	 * This applies to the main WordPress loop.
	 *
	 * @param WP_Query $q Query instance.
	 */
	public function event_query( $query ) {
		// Query vars that affect posts shown.
		$query->set( 'meta_query', $this->get_meta_query( $query->get( 'meta_query' ), true ) );
		$query->set( 'tax_query', $this->get_tax_query( $query->get( 'tax_query' ), true ) );
		$query->set( 'bm_pe_query', 'event_query' );

		self::$event_query = $query;

		do_action( 'press_events_event_query', $query, $this );
	}

	/**
	 * Appends meta queries to an array.
	 *
	 * @param  array $meta_query Meta query.
	 * @param  bool  $main_query If is main query.
	 * @return array
	 */
	public function get_meta_query( $meta_query = array(), $main_query = false ) {
		if ( ! is_array( $meta_query ) ) {
			$meta_query = array();
		}

		return array_filter( apply_filters( 'press_events_event_query_meta_query', $meta_query, $this ) );
	}

	/**
	 * Appends tax queries to an array.
	 *
	 * @param  array $tax_query  Tax query.
	 * @param  bool  $main_query If is main query.
	 * @return array
	 */
	public function get_tax_query( $tax_query = array(), $main_query = false ) {
		if ( ! is_array( $tax_query ) ) {
			$tax_query = array(
				'relation' => 'AND',
			);
		}

		return array_filter( apply_filters( 'press_events_event_query_tax_query', $tax_query, $this ) );
	}

	/**
	 * Remove the query.
	 */
	public function remove_event_query() {
		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
	}

}
