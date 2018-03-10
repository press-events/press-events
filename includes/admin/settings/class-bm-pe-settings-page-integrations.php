<?php
/**
 * Integration settings tab content
 *
 * @package PressEvents/Admin/Settings
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BM_PE_Settings_Page_Integrations class.
 */
class BM_PE_Settings_Page_Integrations {

	/**
	 * What is the ID of this tab
	 */
	public static $tab_id = 'pe-integrations';

	/**
	 * Add tab to settings page
	 */
	public static function init() {
		add_filter( 'press_events_settings_tabs_array', array( __class__, 'tab' ) );
		add_filter( 'press_events_settings_sections_array', array( __class__, 'sections' ) );
		add_filter( 'press_events_settings_fields_array', array( __class__, 'fields' ) );

		add_action( 'press_events_settings_section_'. self::$tab_id, array( __class__, 'output' ) );
	}

    /**
     * Add tab to settings page
     */
    public static function tab( $tabs ) {
		$tabs[] = array(
			'id' => self::$tab_id,
			'title' => __( 'Integrations', 'press-events' )
		);

		return $tabs;
    }

	/**
     * Add sections to tab
     */
    public static function sections( $sections ) {
		// google maps
		$sections[] = array(
			'tab' => self::$tab_id,
			'id' => self::$tab_id .'-google-maps',
			'title' => __( 'Google maps API', 'press-events' ),
			'desc' => __( 'If you want to show Google maps on event and location pages you need to enter your Google Maps API key below.', 'press-events' )
		);

		return $sections;
    }

	/**
     * Add field to section
     */
    public static function fields( $fields ) {
		// -google-maps
		$fields[ self::$tab_id .'-google-maps' ] = array(
			array(
				'name' => 'api-key',
                'label' => __( 'API Key', 'press-events' ),
				'help' => __( 'Enter your unique Google Maps API key', 'press-events' ),
				'type' => 'text'
			),
		);

		return $fields;
    }

	/**
	 * Output settings
	 */
	public static function output() {
		do_settings_sections( self::$tab_id );
		settings_fields( self::$tab_id );
	}

}
