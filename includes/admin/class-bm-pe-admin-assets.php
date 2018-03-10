<?php
/**
 * Press Events Admin Assets
 *
 * @package PressEvents/Classes/Admin
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'PE_Admin_Assets', false ) ) :

/**
 * BM_PE_Admin_Assets Class.
 */
class BM_PE_Admin_Assets {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Enqueue styles.
	 */
	public function admin_styles() {
		global $wp_scripts;

		$screen = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		// Register admin styles
		wp_register_style( 'jquery-timepicker', BM_Press_Events()->plugin_url() . '/assets/css/jquery-timepicker/jquery.timepicker.min.css', array(), '1.7.1' );
		wp_register_style( 'bootstrap-datepicker', BM_Press_Events()->plugin_url() . '/assets/css/bootstrap-datepicker/bootstrap-datepicker.standalone.css', array(), '1.7.1' );
		wp_register_style( 'press_events_admin_menu_styles', BM_Press_Events()->plugin_url() . '/assets/css/menu.css', array(), BM_PE_VERSION );
		wp_register_style( 'press_events_admin_styles', BM_Press_Events()->plugin_url() . '/assets/css/admin.css', array(), BM_PE_VERSION );

		// Add RTL support for admin styles
		wp_style_add_data( 'press_events_admin_menu_styles', 'rtl', 'replace' );
		wp_style_add_data( 'press_events_admin_styles', 'rtl', 'replace' );

		// Sitewide CSS
		wp_enqueue_style( 'jquery-timepicker' );
		wp_enqueue_style( 'bootstrap-datepicker' );
		wp_enqueue_style( 'press_events_admin_menu_styles' );

		// Admin styles for PE pages only
		if ( in_array( $screen_id, bm_pe_get_screen_ids() ) ) {
			wp_enqueue_style( 'press_events_admin_styles' );
			wp_enqueue_style( 'wp-color-picker' );
		}
	}

	/**
	 * Enqueue scripts.
	 */
	public function admin_scripts() {
		global $wp_query, $post;

		$screen = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		$pe_screen_id = sanitize_title( __( 'Press Events', 'press-events' ) );
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register scripts
		wp_register_script( 'bootstrap-datepicker', BM_Press_Events()->plugin_url() . '/assets/js/bootstrap-datepicker/bootstrap-datepicker'. $suffix .'.js', array( 'jquery' ), '1.7.1', true );
		wp_register_script( 'jquery-timepicker', BM_Press_Events()->plugin_url() . '/assets/js/jquery-timepicker/jquery.timepicker'. $suffix .'.js', array( 'jquery' ), '1.11.13', true );
		wp_register_script( 'datepair-js', BM_Press_Events()->plugin_url() . '/assets/js/datepair-js/datepair'. $suffix .'.js', array(), '0.4.15', true );
		wp_register_script( 'jquery-datepair-js', BM_Press_Events()->plugin_url() . '/assets/js/datepair-js/jquery.datepair'. $suffix .'.js', array( 'jquery', 'datepair-js' ), '0.4.15', true );
		wp_register_script( 'jquery-tiptip', BM_Press_Events()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip'. $suffix .'.js', array( 'jquery' ), '1.3', true );
		wp_register_script( 'php-date-formatter', BM_Press_Events()->plugin_url() . '/assets/js/php-date-formatter/php-date-formatter'. $suffix .'.js', array( 'jquery' ), '1.3.4', true );
		wp_register_script( 'press-events-admin', BM_Press_Events()->plugin_url() . '/assets/js/admin/press-events-admin'. $suffix .'.js', array( 'jquery', 'jquery-tiptip', 'bootstrap-datepicker' ), BM_PE_VERSION, true );
		wp_register_script( 'pe-admin-meta-boxes', BM_Press_Events()->plugin_url() . '/assets/js/admin/meta-boxes'. $suffix .'.js', array( 'jquery', 'jquery-ui-sortable', 'bootstrap-datepicker', 'jquery-timepicker', 'jquery-datepair-js', 'jquery-tiptip', 'php-date-formatter' ), BM_PE_VERSION, true );

		$params = array(
			'date_vars' => array(
				'days' => BM_Press_Events()->date_i18n->get_weekdays(),
				'daysShort' => BM_Press_Events()->date_i18n->get_weekdays( 'short' ),
				'daysMin' => BM_Press_Events()->date_i18n->get_weekdays( 'initial' ),
				'months' => array_values( BM_Press_Events()->date_i18n->get_months() ),
				'monthsShort' => array_values( BM_Press_Events()->date_i18n->get_months( 'short' ) ),
				'clear' => esc_attr__( 'Clear', 'press-events' ),
				'today' => esc_attr__( 'Today', 'press-events' ),
				'titleFormat' => esc_attr( 'MM yyyy' ),
				'meridiem' => array(
					BM_Press_Events()->date_i18n->get_meridiem('am'),
					BM_Press_Events()->date_i18n->get_meridiem('pm')
				)
			),
			'time_vars' => array(
				'am' => BM_Press_Events()->date_i18n->get_meridiem('am'),
				'pm' => BM_Press_Events()->date_i18n->get_meridiem('pm'),
				'AM' => BM_Press_Events()->date_i18n->get_meridiem('AM'),
				'PM' => BM_Press_Events()->date_i18n->get_meridiem('PM'),
				'decimal' => '.',
				'mins' => _x( 'mins', 'time-picker', 'press-events' ),
				'hr' => _x( 'hr', 'time-picker', 'press-events' ),
				'hrs' => _x( 'hrs', 'time-picker', 'press-events' ),
			)
		);

		wp_localize_script( 'press-events-admin', 'pe_admin_vars', $params );

		// Press Events admin pages.
		if ( in_array( $screen_id, bm_pe_get_screen_ids() ) ) {
			wp_enqueue_script( 'press-events-admin' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'wp-color-picker' );
		}

		// Meta boxes
		if ( in_array( $screen_id, array( 'pe_event', 'edit-pe_event' ) ) ) {
			wp_register_script( 'pe-admin-event-meta-boxes', BM_Press_Events()->plugin_url() . '/assets/js/admin/meta-boxes-event'. $suffix .'.js', array( 'pe-admin-meta-boxes' ), BM_PE_VERSION, true );

			wp_enqueue_script( 'pe-admin-event-meta-boxes' );

			$params = array(
				'post_id' => isset( $post->ID ) ? $post->ID : '',
				'ajax_url' => BM_Press_Events()->ajax_url(),
				'get_location_nonce' => wp_create_nonce( 'get-location' ),
				'update_location_nonce' => wp_create_nonce( 'update-location' ),
				'delete_location_nonce' => wp_create_nonce( 'delete-location' ),
				'get_locations_nonce' => wp_create_nonce( 'get-locations' ),
				'get_organisers_nonce' => wp_create_nonce( 'get-organisers' ),
				'update_organiser_nonce' => wp_create_nonce( 'update-organiser' ),
				'date_format' => bm_pe_date_format(),
				'time_format' => bm_pe_time_format(),
				'date_vars' => $params['date_vars']
			);
			wp_localize_script( 'pe-admin-event-meta-boxes', 'pe_admin_meta_boxes_event', $params );
		}
	}

}

endif;

return new BM_PE_Admin_Assets();
