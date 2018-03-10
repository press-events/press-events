<?php
/**
 * Press Events Ajax Wrapper
 *
 * Frontend and admin ajax functions
 *
 * @package PressEvents/Classes
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * BM_PE_Ajax class.
 */
class BM_PE_Ajax {

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
		add_action( 'template_redirect', array( __CLASS__, 'do_pe_ajax' ), 0 );
		self::add_ajax_events();
	}

	/**
	 * Get PE Ajax Endpoint.
	 *
	 * @param  string $request Optional
	 * @return string
	 */
	public static function get_endpoint( $request = '' ) {
		return esc_url_raw( apply_filters( 'press_events_ajax_get_endpoint', add_query_arg( 'pe-ajax', $request, home_url( '/' ) ), $request ) );
	}

	/**
	 * Set PE AJAX constant and headers.
	 */
	public static function define_ajax() {
		if ( ! empty( $_GET['pe-ajax'] ) ) {
			bm_pe_maybe_define_constant( 'BM_PE_DOING_AJAX', true );

			@ini_set( 'display_errors', 0 ); // Turn off display_errors during AJAX events to prevent malformed JSON

			$GLOBALS['wpdb']->hide_errors();
		}
	}

	/**
	 * Check for PE Ajax request and fire action.
	 */
	public static function do_pe_ajax() {
		global $wp_query;

		if ( ! empty( $_GET['pe-ajax'] ) ) {
			$wp_query->set( 'pe-ajax', sanitize_text_field( $_GET['pe-ajax'] ) );
		}

		if ( $action = $wp_query->get( 'pe-ajax' ) ) {
			self::pe_ajax_headers();
			do_action( 'pe_ajax_' . sanitize_text_field( $action ) );
			wp_die();
		}
	}

	/**
	 * Send headers for PE Ajax Requests.
	 *
	 * @since 1.0.0
	 */
	private static function pe_ajax_headers() {
		send_origin_headers();
		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		@header( 'X-Robots-Tag: noindex' );
		send_nosniff_header();
		status_header( 200 );
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 */
	public static function add_ajax_events() {
		// EVENT => nopriv
		$ajax_events = array(
			'feature_event' => false,
			'get_location' => false,
			'update_location' => false,
			'delete_location' => false,
			'get_organisers' => false,
			'update_organiser' => false,
			'get_calendar' => true,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_press_events_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_press_events_' . $ajax_event, array( __CLASS__, $ajax_event ) );
				// PE AJAX can be used for frontend ajax requests.
				add_action( 'pe_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Toggle Featured status of an event from admin.
	 */
	public static function feature_event() {
		if ( current_user_can( 'edit_pe_events' ) && check_admin_referer( 'press-events-feature-event' ) ) {
			$event = bm_pe_get_event( absint( $_GET['event_id'] ) );

			if ( $event ) {
				update_post_meta( $event->get_id(), '_featured_event', ! $event->is_featured() );
			}
		}

		wp_safe_redirect( wp_get_referer() ? remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) : admin_url( 'edit.php?post_type=pe_event' ) );
		exit;
	}

	/**
	 * Get a location
	 */
	public function get_location() {
		check_ajax_referer( 'get-location', 'security' );

		$location = bm_pe_get_location( $_POST['event_location_id'] );

		// success
	    wp_send_json_success( array(
			'id' => $location->get_id('edit'),
			'title' => $location->get_id('edit') === 0 ? 'New Location' : $location->get_title('edit'),
			'address' => $location->get_address('edit'),
			'city' => $location->get_city('edit'),
			'postcode' => $location->get_postcode('edit'),
			'county' => $location->get_county('edit'),
			'country' => $location->get_country('edit')
	    ) );
		exit;
	}

	/**
	 * Update a location or add new post if id dosent exist
	 */
	public function update_location() {
		check_ajax_referer( 'update-location', 'security' );

		if ( ! current_user_can( 'publish_pe_event_locations' ) ) {
	        wp_send_json_error( __( 'You don\'t have permision to add or edit locations.', 'press-events' ) );
	    }

		// Get the form fields and sanitize.
		$data = array(
			'id' => intval( $_POST['data']['id'] ),
	    	'title' => bm_pe_clean( $_POST['data']['title'] ),
	    	'address' => bm_pe_clean( $_POST['data']['address'] ),
	    	'city' => bm_pe_clean( $_POST['data']['city'] ),
	    	'postcode' => bm_pe_clean( $_POST['data']['postcode'] ),
	    	'county' => bm_pe_clean( $_POST['data']['county'] ),
	    	'country' => bm_pe_clean( $_POST['data']['country'] )
		);

		if ( empty( $data['title'] ) ) {
	        wp_send_json_error( __( 'You need to add a title.', 'press-events' ) );
	    }

		$data['id'] = wp_insert_post( array(
			'ID' => $data['id'],
	        'post_title' => $data['title'],
	        'post_type' => 'pe_event_location',
	        'post_status' =>  'publish',
	        'meta_input' => array(
	            '_location_address' => $data['address'],
	            '_location_city' => $data['city'],
	            '_location_postcode' => $data['postcode'],
	            '_location_county' => $data['county'],
	            '_location_country' => $data['country']
	        )
	    ) );

		// build select
		$options = array(
            '' => array( '— '. __( 'Select location', 'press-events' ) .' —' ),
            'new' => array( __( 'Add new location', 'press-events' ) )
        );

		foreach ( bm_pe_get_locations( array(
			'posts_per_page' => -1, 'orderby'=> 'title', 'order' => 'ASC'
		) ) as $order => $location ) {
			$options[ $location->get_id('edit') ] = array(
				$location->get_title('edit'),
				'custom_attributes' => array(
					'data-id' => $location->get_id('edit')
				)
			);
		}

		ob_start();

		bm_pe_select_input( array(
	        'id' => '_event_location_select',
	        'label' => __( 'Add a location', 'press-events' ),
	        'options' => $options,
			'bare' => true
		) );

		$select = ob_get_contents();

		ob_end_clean();

		// success
	    wp_send_json_success( array(
			'location' => $data,
			'selectHtml' => $select
		) );
		exit;
	}

	/**
	 * Delete a given location
	 */
	public static function delete_location() {
		check_ajax_referer( 'delete-location', 'security' );

		if ( ! current_user_can( 'delete_pe_event_locations' ) ) {
	        wp_send_json_error( __( 'You don\'t have permision to delete locations.', 'press-events' ) );
	    }

		$id = isset( $_POST['location_id'] ) ? intval( $_POST['location_id'] ) : false;

		if ( ! $id ) {
			wp_send_json_error( __( 'No id set', 'press-events' ) );
		}

		// @todo check if location attach to another post?

		wp_delete_post( $id );

		// success
	    wp_send_json_success( $id );
		exit;
	}

	/**
	 * Get the event organiser
	 */
	public static function get_organisers() {
		check_ajax_referer( 'get-organisers', 'security' );

		$ids = array_unique( explode( ',', $_POST['event_organiser_ids'] ) );

		$ids = array_filter( $ids, function($id) {
			return ($id !== null && $id !== false && $id !== '');
		} );

		if ( empty( $ids ) ) {
			wp_send_json_error( __( 'No ids set', 'press-events' ) );
		}

		$get_organisers = bm_pe_get_organisers( array(
			'include' => $ids
		) );

		$organisers = array();

		foreach ( $ids as $id ) {
			if ( isset( $get_organisers[$id] ) ) {
				$organisers[] = array(
					'id' => $get_organisers[ $id ]->get_id('edit'),
					'display_name' => $get_organisers[ $id ]->get_id('edit') === 0 ? 'New Organiser' : $get_organisers[ $id ]->get_title('edit'),
					'user_email' => $get_organisers[ $id ]->get_email('edit'),
					'user_url' => $get_organisers[ $id ]->get_url('edit'),
					'user_phone' => $get_organisers[ $id ]->get_phone('edit')
				);
			}
		}

		// success
	    wp_send_json_success( array(
			'organisers' => $organisers,
			'ids' => $ids
		) );
		exit;
	}

	/**
	 * Update an event organiser
	 */
	public function update_organiser() {
		check_ajax_referer( 'update-organiser', 'security' );

		if ( ! current_user_can( 'edit_users' ) ) {
	        wp_send_json_error( __( 'You don\'t have permision to edit users.', 'press-events' ) );
	    }

		// Get the form fields and sanitize.
		$data = array(
			'ID' => intval( $_POST['data']['id'] ),
			'role' => 'event_organiser',
			'user_login' => bm_pe_clean( $_POST['data']['display_name'] ),
	    	'display_name' => bm_pe_clean( $_POST['data']['display_name'] ),
	    	'user_email' => sanitize_email( $_POST['data']['user_email'] ),
	    	'user_url' => bm_pe_clean( $_POST['data']['user_url'] ),
	    	'user_phone' => bm_pe_clean( $_POST['data']['user_phone'] ),
			'user_pass' => null
		);

		if ( empty( $data['user_login'] ) ) {
	        wp_send_json_error( __( 'You need to enter a display name.', 'press-events' ) );
	    }

		if ( $data['user_email'] !== '' && ! is_email( $data['user_email'] ) ) {
	        wp_send_json_error( __( 'You need to enter a valid email.', 'press-events' ) );
		}

		$data['ID'] = wp_insert_user( $data );

		if ( is_wp_error( $data['ID'] ) ) {
	        wp_send_json_error( $data['ID']->get_error_message() );
		}

		// Add phone number
		update_user_meta( $data['ID'], 'phone', $data['user_phone'] );

		$options = array(
            '' => array( '— '. __( 'Select organisers', 'press-events' ) .' —' ),
            '0' => array(
				__( 'Add new organiser', 'press-events' ),
				'custom_attributes' => array(
					'data-id' => 0
				)
			)
        );

		foreach ( bm_pe_get_organisers() as $organiser ) {
			$options[ $organiser->get_id('edit') ] = array(
				$organiser->get_title( 'edit' ),
				'custom_attributes' => array(
					'data-id' => $organiser->get_id('edit')
				)
			);
		}

		ob_start();

		bm_pe_select_input( array(
	        'id' => '_event_organiser_select',
	        'label' => __( 'Add an organiser', 'press-events' ),
	        'options' => $options,
			'bare' => true
	    ) );

		$select = ob_get_contents();

		ob_end_clean();

		// success
	    wp_send_json_success( array(
			'organiser' => $data,
			'selectHtml' => $select
		) );
		exit;
	}

	/**
	 * get_calendar
	 */
	public function get_calendar() {
		check_ajax_referer( 'get-calendar', 'security' );

		// Get data
		$query_vars = BM_Press_Events()->query->get_query_vars();
		$data = array(
			'archive_query' => get_query_var( $query_vars['archive_query'] ),
			'archive_type' => get_query_var( $query_vars['archive_type'] ) !== '' ? get_query_var( $query_vars['archive_type'] ) : bm_pe_get_option( 'archive-type', 'pe-general-events', 'list' ),
			'archive_month' => get_query_var( $query_vars['archive_month'] ),
			'archive_category' => get_query_var( $query_vars['archive_category'] ),
			'archive_tag' => get_query_var( $query_vars['archive_tag'] )
		);

		// Load calendar
		do_action( 'press_events_before_get_calendar', $data );
		global $calendar;

		// Get HTML
		ob_start();

		bm_bm_pe_get_template_part( 'content-archive', $calendar->get_type() );
		$html = ob_get_contents();

		ob_end_clean();

		// success
		wp_send_json_success( array(
			'title' => $calendar->current_month->format('F, Y'),
			'navigation' => array(
				'previous' => $calendar->get_previous_month(),
				'next' => $calendar->get_next_month()
			),
			'month' => $calendar->current_month->format('Y-m'),
			'html' => $html
		) );
		exit;
	}

}

BM_PE_Ajax::init();
