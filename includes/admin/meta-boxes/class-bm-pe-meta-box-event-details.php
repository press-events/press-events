<?php
/**
 * Event details
 *
 * Displays the event details box, tabbed.
 *
 * @package PressEvents/Classes/Admin/MetaBoxes
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BM_PE_Meta_Box_Event_Details class.
 */
class BM_PE_Meta_Box_Event_Details {

    /**
	 * Output the metabox.
	 */
	public static function output() {
		include( 'views/html-event-details.php' );
	}

	/**
  	 * Return array of tabs to show.
	 * @return array
	 */
	private static function get_meta_box_tabs() {
		$tabs = apply_filters( 'press_events_event_details_tabs', array(
			'date_time' => array(
				'label'    => __( 'Date & Time', 'press-events' ),
				'target'   => 'date_time_event_details',
				'class'    => array('active'),
				'icon'	   => 'dashicons dashicons-clock',
				'priority' => 10
			),
			'location' => array(
				'label'    => __( 'Location', 'press-events' ),
				'target'   => 'location_event_details',
				'class'    => array(),
				'icon'	   => 'dashicons dashicons-location',
				'priority' => 15
			),
			'organisers' => array(
				'label'    => __( 'Organisers', 'press-events' ),
				'target'   => 'organiser_event_details',
				'class'    => array(),
				'icon'	   => 'dashicons dashicons-groups',
				'priority' => 20
			),
		) );

		// Sort tabs based on priority.
		uasort( $tabs, array( __CLASS__, 'meta_box_tabs_sort' ) );

		return $tabs;
	}

	/**
	 * Callback to sort tabs on priority.
	 *
	 * @since 1.0.0
	 * @param int $a First item.
	 * @param int $b Second item.
	 *
	 * @return bool
	 */
	private static function meta_box_tabs_sort( $a, $b ) {
		if ( ! isset( $a['priority'], $b['priority'] ) ) {
			return -1;
		}
		if ( $a['priority'] == $b['priority'] ) {
			return 0;
		}
		return $a['priority'] < $b['priority'] ? -1 : 1;
	}

	/**
	 * Show tab content/settings.
	 */
	private static function output_tabs() {
		global $post, $event;
		$event = $post->ID ? bm_pe_get_event( $post->ID ) : new BM_PE_Event;

		include( 'views/html-event-details-date-time.php' );
		include( 'views/html-event-details-location.php' );
		include( 'views/html-event-details-organisers.php' );
	}

	/**
	 * Save meta box details.
	 *
	 * @param int $post_id
	 * @param $post
	 */
	public static function save( $post_id, $post ) {
		// set timezone_string or gmt_offset
		if ( !empty($_POST['_event_timezone']) && preg_match('/^UTC[+-]/', $_POST['_event_timezone']) ) {
			$_POST['gmt_offset'] = $_POST['_event_timezone'];
			$_POST['gmt_offset'] = preg_replace('/UTC\+?/', '', $_POST['gmt_offset']);
			$_POST['timezone_string'] = null;

			// Convert to offset (to convert time to UTC)
			$offset = $_POST['gmt_offset'] ? $_POST['gmt_offset'] : 0;
	        $offset = floatval( $offset ) * HOUR_IN_SECONDS;
		} else {
			$_POST['timezone_string'] = $_POST['_event_timezone'];
			$_POST['gmt_offset'] = null;

			// Get offset (to convert time to UTC)
			$timezone_object = new DateTimeZone( $_POST['timezone_string'] );
	        $offset = $timezone_object->getOffset( new DateTime( 'now' ) );
		}

		$event_meta = array(
			// date & time
			'_all_day_event' => isset( $_POST['_all_day_event'] ) && is_string( $_POST['_all_day_event'] ) ? bm_pe_clean( $_POST['_all_day_event'] ) : null,
			'_event_starts' => isset( $_POST['_event_start_date'] ) && isset( $_POST['_event_start_time'] ) ? strtotime( $_POST['_event_start_date'] .' '. ( $_POST['_all_day_event'] !== 'yes' ? $_POST['_event_start_time'] : null ) ) - $offset : null,
			'_event_ends' => isset( $_POST['_event_end_date'] ) && isset( $_POST['_event_end_time'] ) ? strtotime( $_POST['_event_end_date'] .' '. ( $_POST['_all_day_event'] !== 'yes' ? $_POST['_event_end_time'] : null ) ) - $offset : null,
			'_event_gmt_offset' => bm_pe_clean( $_POST['gmt_offset'] ),
			'_event_timezone_string' => bm_pe_clean( $_POST['timezone_string'] ),
			// location
			'_event_location' => isset( $_POST['_event_location'] ) && $_POST['_event_location'] !== 'new' ? bm_pe_clean( $_POST['_event_location'] ) : null,
			'_show_google_map' => isset( $_POST['_show_google_map'] ) && is_string( $_POST['_show_google_map'] ) ? bm_pe_clean( $_POST['_show_google_map'] ) : null,
			// organisers
			'_event_organisers' => isset( $_POST['_event_organisers'] ) ? bm_pe_clean( $_POST['_event_organisers'] ) : null,
		);

		// loop through $post_meta and save data
		foreach ( $event_meta as $meta_key => $meta_value ) {
			update_post_meta( $post_id, $meta_key, $meta_value );
		}
	}

}
