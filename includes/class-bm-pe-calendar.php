<?php
/**
 * Press Events Calendar class build the calendar and get events on given days
 *
 * @since 1.0.0
 * @package PressEvents/Classes/Events
 * @category Class
 * @author Burn Media Ltd
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class BM_PE_Calendar {

    /**
     * Current month
     *
     * @var object BM_PE_DateTime
     */
    public $current_month;

    /**
     * Array of Week Day Names
     *
     * @var array
     */
    private $weekday_names = array();

    /**
	 * Array of Month Days
	 *
	 * @var array
	 */
	private $month_days = false;

    /**
     * Timezone offset
     */
    public $timezone_offset = false;

    /**
     * Week start (Numeric representation of the day of the week)
     */
	private $week_start = 0;

    /**
     * An array of this months events
     *
	 * @var array of BM_PE_Event objects
     */
    private $events = false;

    /**
     * Month days. First and last second of month.
     *
     * @var string timestamp
     */
    private $month_first_day, $month_last_day;

    /**
	 * Constructor
	 */
	public function __construct( $args ) {
        $this->set_calendar_args( $args );
		$this->set_month();
    }

    /**
     * Get the weekday names
     */
    public function get_weekday_names() {
        return $this->weekday_names;
    }

    /**
     * Get calendar days
     */
    public function get_calendar_days() {
        if ( $this->month_days === false ) {
            $this->set_calendar_days();
        }

        return $this->month_days;
    }

    /**
     * Get events on day
     */
    public function get_days_events( $day ) {
        if ( $this->events === false ) {
            $this->set_events();
        }

        return array_filter( $this->events, function( $event ) use ( $day ) {
            return $event->get_event_start()->getOffsetTimestamp() >= $day && $event->get_event_start()->getOffsetTimestamp() <= $day + 86399;
        } );
    }

    /**
     * Get the next month
     */
    public function get_next_month( $format = 'Y-m' ) {
        $time_string = strtotime( '+1 month' , strtotime( $this->current_month->format( 'Y-m-01' ) ) );
        return date( $format, $time_string );
    }

    /**
     * Get the previous month
     */
    public function get_previous_month( $format = 'Y-m' ) {
        $time_string = strtotime( '-1 month' , strtotime( $this->current_month->format( 'Y-m-01' ) ) );
        return date( $format, $time_string );
    }

    /**
     * Get the calendar type
     */
    public function get_type() {
        return $this->calendar_args['archive_type'];
    }

    /**
     * Get the calendar categories
     *
     * @return array
     */
    public function get_categories() {
        return is_array($this->calendar_args['archive_category']) ? $this->calendar_args['archive_category'] : explode(',', $this->calendar_args['archive_category']);
    }

    /**
     * Get the calendar tags
     *
     * @return array
     */
    public function get_tags() {
        return is_array($this->calendar_args['archive_tag']) ? $this->calendar_args['archive_tag'] : explode(',', $this->calendar_args['archive_tag']);
    }

    /**
     * Set the default calendar arguments
     */
    private function set_calendar_args( $args ) {
        $defualts = array(
            'archive_type' => bm_pe_get_option( 'archive-type', 'pe-general-events', 'list' ),
            'archive_query' => '',
            'archive_month' => 'now',
            'archive_category' => '',
            'archive_tag' => '',
            'timezone_offset' => bm_pe_timezone_offset(),
            'week_start' => 0,
        );

        $this->calendar_args = array_merge( $defualts, $args );
    }

    /**
	 * Sets timezone for the Calendar
	 *
	 * @param null|string $date_string Date string parsed by strtotime for the calendar date. If null set to current timestamp.
	 */
	public function set_month() {
        // set the date
        if( $this->calendar_args['archive_month'] ) {
            try {
                $this->current_month = new BM_PE_DateTime( $this->calendar_args['archive_month'] );
            } catch (Exception $e) {
                $this->current_month = new BM_PE_DateTime( 'now' );
            }
		} else {
			$this->current_month = new BM_PE_DateTime( 'now' );
		}

        $this->current_month->set_utc_offset( $this->calendar_args['timezone_offset'] );

        $this->set_month_start_end();
        $this->set_week_start();

        $this->weekday_names = BM_Press_Events()->date_i18n->get_weekdays( 'short' );
        $this->array_rotate( $this->weekday_names, $this->week_start );
	}

    /**
     * Set the start and end of month
     */
    public function set_month_start_end() {
    	$number_of_days = cal_days_in_month( CAL_GREGORIAN, $this->current_month->format('n'), $this->current_month->format('Y') );

        $this->month_first_day = mktime(0, 0, 0, $this->current_month->format('n'), 1, $this->current_month->format('Y'));
        $this->month_last_day = mktime(23, 59, 59, $this->current_month->format('n'), $number_of_days, $this->current_month->format('Y'));
    }

    /**
	 * Sets the first day of the week
	 *
	 * @param int|string $week_start Day to start on, ex: "Monday" or 0-6 where 0 is Sunday
	 */
	public function set_week_start() {
        if ( ! $this->calendar_args['week_start'] ) {
            $week_start = (int) get_option( 'start_of_week', 1 );
        }

		if( is_int( $this->calendar_args['week_start'] ) ) {
			$this->week_start = $week_start % 7;
		} else {
			$this->week_start = date( 'N', strtotime($week_start) ) % 7;
		}
	}

    /**
	 * Get all the events within this month
     */
    private function set_events() {
        $this->events = bm_pe_get_events( array(
            's' => $this->calendar_args['archive_query'],
            'tax_query' => $this->get_tax_query(),
            'meta_query' => $this->get_meta_query()
        ) );
    }

    /**
     * Build event meta query
     */
    private function get_meta_query() {
        // Only get events in this month
        $meta_query = array(
            'relation' => 'AND'
        );

        $meta_query[] = array(
            array(
                'key' => '_event_starts',
                'value' => $this->month_first_day,
                'compare' => '>=',
                'type' => 'NUMERIC'
            ),
            array(
                'key' => '_event_starts',
                'value' => $this->month_last_day,
                'compare' => '<=',
                'type' => 'NUMERIC'
            )
        );
    }

    /**
     * Build event tax query
     */
    private function get_tax_query() {
        $tax_query = array(
            'relation' => 'AND'
        );

        if ( $this->calendar_args['archive_category'] ) {
            $tax_query[] = array(
    			'taxonomy' => 'pe_event_category',
    			'field' => 'slug',
    			'terms' => is_array($this->calendar_args['archive_category']) ? $this->calendar_args['archive_category'] : explode( ',', $this->calendar_args['archive_category'] ),
                'operator' => 'AND'
    		);
        }

        if ( $this->calendar_args['archive_tag'] ) {
            $tax_query[] = array(
    			'taxonomy' => 'pe_event_tag',
    			'field' => 'slug',
    			'terms' => is_array($this->calendar_args['archive_tag']) ? $this->calendar_args['archive_tag'] : explode( ',', $this->calendar_args['archive_tag'] ),
                'operator' => 'AND'
    		);
        }

        return $tax_query;
    }

    /**
     * Set calendar days
     */
    public function set_calendar_days() {
        $days = array();

        $weekday = (date( 'N', mktime( 0, 0, 1, $this->current_month->format('n'), 1, $this->current_month->format('Y') ) ) - $this->week_start + 7) % 7;

        // add days from previous month
        if( $weekday == 7 ) {
            $weekday = 0;
        } else {
            for ( $i=0; $i < $weekday; $i++ ) {
                $days[] = array(
                    'class' => 'pe-empty'
                );
            }
        }

        $count = $weekday + 1;

        $number_of_days = cal_days_in_month( CAL_GREGORIAN, $this->current_month->format('n'), $this->current_month->format('Y') );

        // add days in month
        for( $i = 1; $i <= $number_of_days; $i++ ) {
            $row_ends = false;

            $datetime = mktime( 0, 0, 0, $this->current_month->format('n'), $i, $this->current_month->format('Y') );
            $today = date( 'd-m-o', $datetime ) == date( 'd-m-o' );

            // Get the events
            $events = $this->get_days_events( $datetime );

            if( $count > 6 ) {
                $row_ends = true;
                $count = 0;
            }

            $days[] = array(
                'class' => $today ? 'pe-today' : null,
                'datetime' => $datetime,
                'events' => $events,
                'row_ends' => $row_ends
            );

            $count++;
        }

        // add days from next month
        if ( $count != 1 ) {
            for ( $i=0; $i < 8 - $count; $i++ ) {
                $days[] = array(
                    'class' => 'pe-empty'
                );
            }
        }

        $this->month_days = $days;
    }

    /**
	 * @param array $data
	 * @param int $steps
	 */
	private function array_rotate( array &$data, $steps ) {
		$count = count( $data );

		if( $steps < 0 ) {
			$steps = $count + $steps;
		}

		$steps = $steps % $count;

		for( $i = 0; $i < $steps; $i++ ) {
			array_push( $data, array_shift( $data ) );
		}
	}

}
