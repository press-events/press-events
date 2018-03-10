<?php
/**
 * Press Events Meta Boxes
 *
 * Sets up the write panels used by events and locations (custom post types).
 *
 * @package PressEvents/Classes/Admin
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * BM_PE_Admin_Meta_Boxes.
 */
class BM_PE_Admin_Meta_Boxes {

	/**
	 * Is meta boxes saved once?
	 *
	 * @var boolean
	 */
	private static $saved_meta_boxes = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		// Save Event Meta Boxes
		add_action( 'press_events_process_pe_event_meta', 'BM_PE_Meta_Box_Event_Details::save', 10, 2 );
	}

	/**
	 * Add PE Meta boxes.
	 */
	public function add_meta_boxes() {
		// Events
		add_meta_box( 'press-events-event-details', __( 'Event Details', 'press-events' ), 'BM_PE_Meta_Box_Event_Details::output', 'pe_event', 'normal', 'high' );
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the nonce
		if ( empty( $_POST['press_events_save_data'] ) || ! wp_verify_nonce( $_POST['press_events_save_data'], 'press_events_meta_nonce' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		self::$saved_meta_boxes = true;

		// save meta box
		do_action( 'press_events_process_' . $post->post_type . '_meta', $post_id, $post );
	}

}
new BM_PE_Admin_Meta_Boxes();
