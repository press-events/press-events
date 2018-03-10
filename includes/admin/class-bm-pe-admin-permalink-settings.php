<?php
/**
 * Add permalink options to permalink page
 *
 * @package PressEvents/Classes/Admin
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BM_PE_Admin_Permalink_Settings', false ) ) :
/**
 * BM_PE_Admin_Permalink_Settings class.
 */
class BM_PE_Admin_Permalink_Settings {

	/**
	 * Permalink settings.
	 *
	 * @var array
	 */
	private $permalinks = array();

	/**
	 * Add to WordPress permalink page
	 */
	public function __construct() {
		$this->settings_init();
		$this->settings_save();
	}

	/**
	 * Init permalink settings.
	 */
	public function settings_init() {
		// Add a section to the permalinks page
		add_settings_section( 'press-events-permalink', __( 'Event permalinks', 'press-events' ), null, 'permalink' );

		// Add our settings
		add_settings_field(
			'press_events_event_archive_slug', // id
			__( 'Event archive', 'press-events' ),  // setting title
			array( $this, 'event_archive_slug_input' ), // display callback
			'permalink', // settings page
			'press-events-permalink' // settings section
		);
		add_settings_field(
			'press_events_event_slug', // id
			__( 'Event base', 'press-events' ),  // setting title
			array( $this, 'event_slug_input' ), // display callback
			'permalink', // settings page
			'press-events-permalink' // settings section
		);
		add_settings_field(
			'press_events_event_category_slug', // id
			__( 'Event category base', 'press-events' ),  // setting title
			array( $this, 'event_category_slug_input' ), // display callback
			'permalink', // settings page
			'press-events-permalink' // settings section
		);
		add_settings_field(
			'press_events_event_tag_slug', // id
			__( 'Event tag base', 'press-events' ), // setting title
			array( $this, 'event_tag_slug_input' ), // display callback
			'permalink', // settings page
			'press-events-permalink' // settings section
		);

		$this->permalinks = bm_pe_get_permalink_structure();
	}

	/**
	 * Show a slug input box.
	 */
	public function event_archive_slug_input() {
		?>
		<input name="press_events_event_archive_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['event_archive'] ); ?>" placeholder="<?php echo esc_attr_x( 'events', 'slug', 'press-events' ) ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function event_slug_input() {
		?>
		<input name="press_events_event_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['event_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'event', 'slug', 'press-events' ) ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function event_category_slug_input() {
		?>
		<input name="press_events_event_category_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['category_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'event-category', 'slug', 'press-events' ) ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function event_tag_slug_input() {
		?>
		<input name="press_events_event_tag_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['tag_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'event-tag', 'slug', 'press-events' ) ?>" />
		<?php
	}

	/**
	 * Save the settings.
	 */
	 public function settings_save() {
 		if ( ! is_admin() ) {
 			return;
 		}

 		// We need to save the options ourselves; settings api does not trigger save for the permalinks page.
 		if ( isset( $_POST['permalink_structure'] ) ) {
 			$permalinks = (array) get_option( 'press_events_permalinks', array() );

			$permalinks['event_archive'] = bm_pe_sanitize_permalink( trim( $_POST['press_events_event_archive_slug'] ) );
 			$permalinks['event_base'] = bm_pe_sanitize_permalink( trim( $_POST['press_events_event_slug'] ) );
 			$permalinks['category_base'] = bm_pe_sanitize_permalink( trim( $_POST['press_events_event_category_slug'] ) );
 			$permalinks['tag_base'] = bm_pe_sanitize_permalink( trim( $_POST['press_events_event_tag_slug'] ) );

 			update_option( 'press_events_permalinks', $permalinks );
 		}
 	}

}

endif;
return new BM_PE_Admin_Permalink_Settings();
