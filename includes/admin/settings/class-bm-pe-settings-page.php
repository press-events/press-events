<?php
/**
 * Setting page wrapper
 *
 * @package PressEvents/Admin/Settings
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BM_PE_Settings_Page class.
 */
class BM_PE_Settings_Page {

	/**
	 * Load the sections and fields
	 */
	public static function load_settings() {
		BM_PE_Settings_Page_General::init();
		BM_PE_Settings_Page_Integrations::init();
	}

	/**
	 * Settings tabs
	 */
	public static function get_settings_tabs() {
		$tabs = apply_filters( 'press_events_settings_tabs_array', array() );
		return $tabs;
	}

	/**
	 * Settings sections
	 */
	public static function get_settings_sections() {
		$sections = apply_filters( 'press_events_settings_sections_array', array() );
		return $sections;
	}

	/**
	 * Settings fields
	 */
	public static function get_settings_fields() {
		$fields = apply_filters( 'press_events_settings_fields_array', array() );
		return $fields;
	}

	/**
	 * What is the current tab?
	 */
	private static function get_current_tab() {
		return isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'pe-settings-page-general';
	}

    /**
	 * Output the settings wrapper.
	 */
	public static function output() {
        $tabs = self::get_settings_tabs();
		$current_tab = self::get_current_tab();

		include( 'views/html-settings-page.php' );
	}

}
