<?php
/**
 * General settings tab content
 *
 * @package PressEvents/Admin/Settings
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BM_PE_Settings_Page_General class.
 */
class BM_PE_Settings_Page_General {

	/**
	 * What is the ID of this tab
	 */
	public static $tab_id = 'pe-general';

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
			'title' => __( 'General', 'press-events' )
		);

		return $tabs;
    }

	/**
     * Add sections to tab
     */
    public static function sections( $sections ) {
		// general
		$sections[] = array(
			'tab' => self::$tab_id,
			'id' => self::$tab_id,
			'title' => __( 'General options', 'press-events' )
		);

		// events
		$sections[] = array(
			'tab' => self::$tab_id,
			'id' => self::$tab_id .'-events',
			'title' => __( 'Event options', 'press-events' )
		);

		// currency
		/*
		$sections[] = array(
			'tab' => self::$tab_id,
			'id' => self::$tab_id .'-currency',
			'desc' => __( 'The following options allow you to control how the prices are displayed in the frontend.', 'press-events' ),
			'title' => __( 'Currency options', 'press-events' )
		);
		*/

		return $sections;
    }

	/**
     * Add field to section
     */
    public static function fields( $fields ) {

		// -general
		$fields[ self::$tab_id ] = array(
			array(
				'name' => 'base-country',
                'label' => __( 'Base country', 'press-events' ),
				'help' => __( 'The default country for your event locations. This is overridden in each event.', 'press-events' ),
				'type' => 'select',
				'default' => 'GB',
				'options' => BM_Press_Events()->countries->get_countries()
			)
		);

		$default_template = array( 'page.php' => __( 'Default Template', 'press-events' ) );
		$theme_templates = wp_get_theme()->get_page_templates();

		$fields[ self::$tab_id ][] = array(
			'name' => 'event-template',
            'label' => __( 'Event page template', 'press-events' ),
			'help' => __( 'Select the default page template for the event pages. These are based on the current themes available templates.', 'press-events' ),
			'type' => 'select',
			'options' => array_merge( $default_template, $theme_templates )
		);

		// -events
		$fields[ self::$tab_id .'-events' ] = array(
			array(
				'name' => 'archive-views',
				'label' => __( 'Archive views', 'press-events' ),
				'help' => __( 'Available archive views.', 'press-events' ),
				'type' => 'multicheck',
				'default' => array(
					'list' => 'list',
					'calendar' => 'calendar'
				),
				'options' => apply_filters( 'press_events_archive_views', array(
					'list' => 'List',
					'calendar' => 'Calendar'
				) )
			),
			array(
				'name' => 'default-archive-view',
				'label' => __( 'Default archive view', 'press-events' ),
				'help' => __( 'Choose the default event archive view.', 'press-events' ),
				'type' => 'select',
				'default' => 'list',
				'options' => apply_filters( 'press_events_archive_views', array(
					'list' => 'List',
					'calendar' => 'Calendar'
				) )
			),
			array(
				'name' => 'ajax-archive',
				'label' => __( 'Enable AJAX archives', 'press-events' ),
				'help' => false,
				'default' => 'on',
				'type' => 'checkbox'
			),
			array(
				'name' => 'enable-comments',
				'label' => __( 'Enable comments', 'press-events' ),
				'help' => false,
				'type' => 'checkbox'
			),
		);

		$currency_options = bm_pe_get_currencies();
		foreach ( $currency_options as $code => $name ) {
			$currency_options[ $code ] = $name . ' (' . bm_pe_get_currency_symbol( $code ) . ')';
		}

		// -currency
		$fields[ self::$tab_id .'-currency' ] = array(
			array(
				'name' => 'code',
				'label' => __( 'Currency', 'press-events' ),
				'help' => __( 'Select the default currency for the events.', 'press-events' ),
				'type' => 'select',
				'default' => 'GBP',
				'options' => $currency_options
			),
			array(
				'name' => 'position',
				'label' => __( 'Currency position', 'press-events' ),
				'help' => __( 'This controls the position of the currency symbol.', 'press-events' ),
				'type' => 'select',
				'default' => 'left',
				'options' => array(
					'left' => __( 'Left', 'press-events' ) . ' (' . bm_pe_get_currency_symbol() . '&#x200e;99.99)',
					'right' => __( 'Right', 'press-events' ) . ' (99.99' . bm_pe_get_currency_symbol() . '&#x200f;)',
					'left_space' => __( 'Left with space', 'press-events' ) . ' (' . bm_pe_get_currency_symbol() . '&#x200e;&nbsp;99.99)',
					'right_space' => __( 'Right with space', 'press-events' ) . ' (99.99&nbsp;' . bm_pe_get_currency_symbol() . '&#x200f;)',
				)
			),
			array(
				'name' => 'thousand-separator',
				'label' => __( 'Thousand separator', 'press-events' ),
				'help' => __( 'This sets the thousand separator to display on prices.', 'press-events' ),
				'type' => 'text',
				'size' => 'small',
				'default' => ','
			),
			array(
				'name' => 'decimal-separator',
				'label' => __( 'Decimal separator', 'press-events' ),
				'help' => __( 'This sets the decimal separator to display on prices.', 'press-events' ),
				'type' => 'text',
				'size' => 'small',
				'default' => '.'
			),
			array(
				'name' => 'decimals',
				'label' => __( 'Number of decimals', 'press-events' ),
				'help' => __( 'This sets the number of decimals to display on prices.', 'press-events' ),
				'type' => 'number',
				'size' => 'small',
				'default' => '2'
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
