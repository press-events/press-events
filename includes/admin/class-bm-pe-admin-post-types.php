<?php
/**
 * Post Types Admin
 *
 * @package PressEvents/Classes/Admin
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PE_Admin_Post_Types', false ) ) :

/**
 * BM_PE_Admin_Post_Types Class.
 *
 * Handles the edit posts views and some functionality on the edit post screen for WC post types.
 */
class BM_PE_Admin_Post_Types {

    /**
     * Constructor.
     */
    public function __construct() {
    	// Load correct list table classes for current screen.
    	add_action( 'current_screen', array( $this, 'setup_screen' ) );
    	add_action( 'check_ajax_referer', array( $this, 'setup_screen' ) );
    }

    /**
	 * Looks at the current screen and loads the correct list table handler.
	 *
	 * @since 3.3.0
	 */
	public function setup_screen() {
		$screen_id = false;

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			$screen_id = isset( $screen, $screen->id ) ? $screen->id : '';
		}

		if ( ! empty( $_REQUEST['screen'] ) ) { // WPCS: input var ok.
			$screen_id = bm_pe_clean( wp_unslash( $_REQUEST['screen'] ) ); // WPCS: input var ok, sanitization ok.
		}

		switch ( $screen_id ) {
			case 'edit-pe_event' :
				include_once( 'list-tables/class-bm-pe-admin-list-table-events.php' );
				new BM_PE_Admin_List_Table_Events();
				break;
		}
	}

}

endif;

new BM_PE_Admin_Post_Types();
