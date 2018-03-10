<?php
/**
 * Press Events Countries
 *
 * The Press Events countries class stores country/state data.
 *
 * @since 1.0.0
 * @package PressEvents/Classes
 * @category Class
 * @author Burn Media Ltd
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class BM_PE_Countries {

    /**
     * Countries
     *
     * @var array Array of countries
     */
    private $countries;

    /**
	 * Get all countries.
     *
	 * @return array
	 */
	public function get_countries() {
		if ( empty( $this->countries ) ) {
			$this->countries = apply_filters( 'press_events_countries', include( BM_Press_Events()->plugin_path() . '/i18n/countries.php' ) );

            if ( apply_filters( 'press_events_sort_countries', true ) ) {
				asort( $this->countries );
			}
		}

		return $this->countries;
	}

}
