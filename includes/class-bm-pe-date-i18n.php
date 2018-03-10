<?php
/**
 * Press Events date i18n
 *
 * The Press Events date i18n class stores day and month info.
 *
 * @since 1.0.0
 * @package PressEvents/i18n
 * @category Class
 * @author Burn Media Ltd
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * class BM_PE_Date_i18n
 */
class BM_PE_Date_i18n {

    /**
     * Weekdays
     *
     * @var array Array of weekdays
     */
    private $weekdays;

    /**
     * Months
     *
     * @var array Array of months
     */
    private $months;

    /**
     * Meridiem
     *
     * @var array Array of meridiem
     */
    private $meridiem;

    /**
	 * Get all weekdays (full, short & initialized weekdays).
	 *
     * @param $type (full, short, initial)
	 * @return array
	 */
	public function get_weekdays( $type = 'full' ) {
		if ( empty( $this->weekdays ) ) {
			$this->build_localized_weekdays();
		}

		return $this->weekdays[ $type ];
	}

    /**
	 * Get all months (long & short).
     *
     * @param $type (full, short)
	 * @return array
	 */
	public function get_months( $type = 'full' ) {
		if ( empty( $this->months ) ) {
			$this->build_localized_months();
		}

		return $this->months[ $type ];
    }

    /**
     * Get meridiem (AM, PM)
     *
     * @param $meridiem (am, pm)
	 * @return array
     */
    public function get_meridiem( $meridiem = 'am' ) {
		if ( empty( $this->meridiem ) ) {
			$this->build_localized_meridiem();
		}

        return $this->meridiem[$meridiem];
    }

    /**
	 * Build array of localized (full, short & initialized weekdays).
	 */
	private function build_localized_weekdays() {
		global $wp_locale;

        // Get start of week
        $weekdays = array();

    	for ( $i = 0; $i < 7; $i++ ) {
            $weekdays[] = $wp_locale->get_weekday( $i );
    	}

    	foreach ( $weekdays as $weekday ) {
			$this->weekdays['full'][] = $weekday;
			$this->weekdays['short'][] = $wp_locale->get_weekday_abbrev( $weekday );
			$this->weekdays['initial'][] = $wp_locale->get_weekday_initial( $weekday );
    	}
	}

    /**
	 * Build array of localized full and short months.
	 *
	 * @since 1.0.0
	 */
	private function build_localized_months() {
		global $wp_locale;

		for ( $i = 1; $i <= 12; $i++ ) {
			$month_number = str_pad( $i, 2, '0', STR_PAD_LEFT );
            $month = $wp_locale->get_month( $month_number );

            $this->months['full'][$month_number] = $month;
            $this->months['short'][$month_number] = $wp_locale->get_month_abbrev( $month );
		}
	}

    /**
	 * Build array of localized meridiem.
	 *
	 * @since 1.0.0
	 */
	private function build_localized_meridiem() {
		global $wp_locale;

        $this->meridiem['am'] = $wp_locale->get_meridiem( 'am' );
        $this->meridiem['pm'] = $wp_locale->get_meridiem( 'pm' );
        $this->meridiem['AM'] = $wp_locale->get_meridiem( 'AM' );
        $this->meridiem['PM'] = $wp_locale->get_meridiem( 'PM' );
	}

}
