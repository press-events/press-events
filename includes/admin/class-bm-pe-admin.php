<?php
/**
 * Press Events Admin
 *
 * @package PressEvents/Classes/Admin
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * BM_PE_Admin class.
 */
class BM_PE_Admin {

    /**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'current_screen', array( $this, 'conditional_includes' ) );
		add_action( 'admin_init', array( $this, 'buffer' ), 1 );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
		add_filter( 'plugin_action_links_'. BM_PE_PLUGIN_BASENAME, array( $this, 'add_action_links' ) );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		include_once( dirname( __FILE__ ) . '/class-bm-pe-admin-assets.php' );
		include_once( dirname( __FILE__ ) . '/class-bm-pe-admin-meta-boxes.php' );
		include_once( dirname( __FILE__ ) . '/class-bm-pe-admin-post-types.php' );
		include_once( dirname( __FILE__ ) . '/class-bm-pe-admin-settings.php' );
		include_once( dirname( __FILE__ ) . '/class-bm-pe-admin-user-contacts.php' );

		include_once( dirname( __FILE__ ) . '/bm-pe-admin-functions.php' );
		include_once( dirname( __FILE__ ) . '/bm-pe-meta-box-functions.php' );
	}

	/**
	 * Include admin files conditionally.
	 */
	public function conditional_includes() {
		if ( !$screen = get_current_screen() ) {
			return;
		}

		switch ( $screen->id ) {
			case 'options-permalink' :
				include( dirname( __FILE__ ) . '/class-bm-pe-admin-permalink-settings.php' );
			break;
		}
	}

	/**
	 * Output buffering allows admin screens to make redirects later on.
	 */
	public function buffer() {
		ob_start();
	}

    /**
	 * Change the admin footer text on Press Events admin pages.
	 *
	 * @since 1.0.0
	 * @param  string $footer_text
	 * @return string
	 */
	public function admin_footer_text( $footer_text ) {
		$current_screen = get_current_screen();

		// Check to make sure we're on a Press Events admin page.
		if ( isset( $current_screen->id ) && apply_filters( 'press_events_display_admin_footer_text', in_array( $current_screen->id, bm_pe_get_screen_ids() ) ) ) {
			$footer_text = __( 'Thank you for using Press Events.', 'press-events' );
		}

		return $footer_text;
	}

    /**
	 * Add a settings link to plugin actions
	 *
	 * @since 1.0.0
	 * @param array $links
	 * @return array
	 */
	public function add_action_links( $links ) {
		$pe_links = array(
			'<a href="'. admin_url('edit.php?post_type=pe_event&page=pe-settings') .'">'. __( 'Settings', 'press-events' ) .'</a>',
		);

		return array_merge( $links, $pe_links );
	}

}
return new BM_PE_Admin();
