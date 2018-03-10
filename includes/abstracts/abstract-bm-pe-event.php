<?php
/**
 * Abstract Event Class
 *
 * The Press Events event class handles individual event data.
 *
 * @version 1.0.0
 * @package PressEvents/Abstracts
 * @author Burn Media Ltd
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BM_PE_Event extends BM_PE_Data {

    /**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'event';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'pe_event';

	/**
	 * Cache group.
	 *
	 * @var string
	 */
	protected $cache_group = 'events';

    /**
	 * Stores event data.
	 *
	 * @var array
	 */
	protected $data = array(
		'title' => '',
        'slug' => '',
        'date_created' => '',
        'date_modified' => '',
        'status' => '',
        'description' => '',
        'short_description' => '',
        'comments_allowed' => '',
        'comment_count' => 0,
        'all_day_event' => '',
        'timezone' => '',
        'event_start' => '',
        'event_end' => '',
        'event_location' => '',
        'event_organisers' => '',
        'google_map' => '',
        'featured_event' => '',
	);

    /**
	 * Get the event if ID is passed, otherwise the event is new and empty.
	 *
	 * @param int|BM_PE_Event|object $event event to init.
	 */
	public function __construct( $event = 0 ) {
		parent::__construct( $event );

		if ( is_numeric( $event ) && $event > 0 ) {
			$this->set_id( $event );
		} elseif ( $event instanceof self ) {
			$this->set_id( absint( $event->get_id() ) );
		} elseif ( ! empty( $event->ID ) ) {
			$this->set_id( absint( $event->ID ) );
		} else {
			$this->set_object_read( true );
		}

		if ( $this->get_id() > 0 ) {
			$this->read();
		}
	}

    /**
     * Method to read a event from the database.
     *
     * @throws Exception
     */
    public function read() {
        $this->set_defaults();

        if ( !$this->get_id() || ! ( $post_object = get_post( $this->get_id() ) ) || $this->post_type !== $post_object->post_type ) {
            throw new Exception( __( 'Invalid event.', 'press-events' ) );
        }

        $this->set_props( array(
            'title' => $post_object->post_title,
            'slug' => $post_object->post_name,
            'date_created' => 0 < $post_object->post_date_gmt ? bm_pe_string_to_timestamp( $post_object->post_date_gmt ) : null,
			'date_modified' => 0 < $post_object->post_modified_gmt ? bm_pe_string_to_timestamp( $post_object->post_modified_gmt ) : null,
			'status' => $post_object->post_status,
			'description' => $post_object->post_content,
			'short_description' => $post_object->post_content,
			'comments_allowed' => 'open' == $post_object->comment_status && 'on' == bm_pe_get_option( 'enable-comments', 'pe-general-events', 'on' ),
			'comment_count' => $post_object->comment_count,
        ) );

        $this->read_event_data();

        $this->set_object_read( true );
    }

    /**
	 * Read event data.
	 *
	 * @since 1.0.0
	 */
	protected function read_event_data() {
		$id = $this->get_id();

		$this->set_props( array(
            'all_day_event' => get_post_meta( $id, '_all_day_event', true ),
            'timezone' => bm_pe_event_timezone_string( $id ),
            'event_start' => get_post_meta( $id, '_event_starts', true ),
            'event_end' => get_post_meta( $id, '_event_ends', true ),
            'event_location' => get_post_meta( $id, '_event_location', true ),
            'event_organisers' => get_post_meta( $id, '_event_organisers', true ),
            'google_map' => get_post_meta( $id, '_show_google_map', true ),
            'featured_event' => get_post_meta( $id, '_featured_event', true ),
		) );
	}

    /*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get event title.
	 *
	 * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_title( $context = 'view' ) {
		return $this->get_prop( 'title', $context );
	}

    /**
	 * Get event slug.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_slug( $context = 'view' ) {
		return $this->get_prop( 'slug', $context );
	}

    /**
     * Get event created date.
     *
     * @since 1.0.0
     * @param string $context What the value is for. Valid values are view and edit.
     * @return BM_PE_DateTime|NULL object if the date is set or null if there is no date.
     */
    public function get_date_created( $context = 'view' ) {
    	return $this->get_prop( 'date_created', $context );
    }

    /**
	 * Get event modified date.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return BM_PE_DateTime|NULL object if the date is set or null if there is no date.
	 */
	public function get_date_modified( $context = 'view' ) {
		return $this->get_prop( 'date_modified', $context );
	}

    /**
	 * Get event status.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_status( $context = 'view' ) {
		return $this->get_prop( 'status', $context );
	}

    /**
	 * Get event description.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_description( $context = 'view' ) {
		return $this->get_prop( 'description', $context );
	}

    /**
	 * Get event short description.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_short_description( $context = 'view' ) {
		return $this->get_prop( 'short_description', $context );
	}

    /**
	 * Return if comments are allowed.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_comments_allowed( $context = 'view' ) {
		return $this->get_prop( 'comments_allowed', $context );
	}

    /**
	 * Return comment count.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_comment_count( $context = 'view' ) {
		return $this->get_prop( 'comment_count', $context );
	}

    /**
	 * Return if is all day event.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_all_day_event( $context = 'view' ) {
		return $this->get_prop( 'all_day_event', $context );
	}

    /**
	 * Get event timezone.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_timezone( $context = 'view' ) {
		return $this->get_prop( 'timezone', $context );
	}

    /**
	 * Get the event start
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return BM_PE_DateTime|NULL object if the date is set or null if there is no date.
	 */
	public function get_event_start( $context = 'view' ) {
		return $this->get_prop( 'event_start', $context );
	}

    /**
	 * Get the event end
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return BM_PE_DateTime|NULL object if the date is set or null if there is no date.
	 */
	public function get_event_end( $context = 'view' ) {
		return $this->get_prop( 'event_end', $context );
	}

    /**
	 * Get the event location
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return BM_PE_Event_Location|NULL object if the date is set or null if there is no date.
	 */
	public function get_event_location( $context = 'view' ) {
		return $this->get_prop( 'event_location', $context );
	}

    /**
	 * Get the event organisers
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return BM_PE_Event_Organiser|NULL object if the date is set or null if there is no date.
	 */
	public function get_event_organisers( $context = 'view' ) {
		return $this->get_prop( 'event_organisers', $context );
	}

    /**
	 * Return if should show Google Map.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_google_map( $context = 'view' ) {
		return $this->get_prop( 'google_map', $context );
	}

    /**
	 * Return if is a featured event.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_featured_event( $context = 'view' ) {
		return $this->get_prop( 'featured_event', $context );
	}

    /*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	*/

    /**
	 * Checks if an event is all day.
	 *
	 * @return bool
	 */
	public function is_all_day_event() {
		return apply_filters( 'press_events_all_day_event', true === $this->get_all_day_event(), $this );
	}

    /**
	 * Checks if an event is on one day
	 *
	 * @return bool
	 */
	public function is_one_day_event() {
        $start = $this->get_event_start()->format('Y-m-d');
        $end = $this->get_event_end()->format('Y-m-d');

		return apply_filters( 'press_events_one_day_event', $start == $end, $this );
	}

    /**
	 * Checks if the event has a location.
	 *
	 * @return bool
	 */
	public function has_location() {
    	return apply_filters( 'press_events_has_location', 0 !== $this->get_event_location()->get_id(), $this );
	}

    /**
	 * Checks if we should show a google map.
	 *
	 * @return bool
	 */
	public function show_google_map() {
    	return apply_filters( 'press_events_show_google_map', true === $this->get_google_map() && '' !== bm_pe_get_option( 'api-key', 'pe-integrations-google-maps' ), $this );
	}

    /**
	 * Checks if the event has an organiser.
	 *
	 * @return bool
	 */
	public function has_organiser() {
    	return apply_filters( 'press_events_has_organiser', null !== $this->get_event_organisers(), $this );
	}

    /**
     * Checks if the event is featured
     */
    public function is_featured() {
		return apply_filters( 'press_events_featured', true === $this->get_featured_event(), $this );
    }

    /**
     * Checks if the event allows comments
     */
    public function comments_open() {
		return apply_filters( 'press_events_comments_open', true === $this->get_comments_allowed(), $this );
    }

    /*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set event title.
	 *
	 * @since 1.0.0
	 * @param string $title Event title.
	 */
	public function set_title( $title ) {
		$this->set_prop( 'title', $title );
	}

    /**
	 * Set event slug.
	 *
     * @since 1.0.0
	 * @param string $slug Event slug.
	 */
	public function set_slug( $slug ) {
		$this->set_prop( 'slug', $slug );
	}

    /**
	 * Set event created date.
	 *
	 * @since 1.0.0
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 */
	public function set_date_created( $date = null ) {
		$this->set_date_prop( 'date_created', $date );
	}

    /**
	 * Set event modified date.
	 *
     * @since 1.0.0
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 */
	public function set_date_modified( $date = null ) {
		$this->set_date_prop( 'date_modified', $date );
	}

    /**
	 * Set event status.
	 *
     * @since 1.0.0
	 * @param string $status Event status.
	 */
	public function set_status( $status ) {
		$this->set_prop( 'status', $status );
	}

    /**
	 * Set event description.
	 *
     * @since 1.0.0
	 * @param string $description Events description.
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', $description );
	}

    /**
	 * Set event short description.
	 *
     * @since 1.0.0
	 * @param string $description Events description.
	 */
	public function set_short_description( $description ) {
        $short_description = substr( $description, 0, 250 );
        $short_description .= strlen( $description ) > 250 ? '...' : '';

		$this->set_prop( 'short_description', $short_description );
	}

    /**
	 * Set if comments are allowed.
	 *
     * @since 1.0.0
	 * @param bool $comments_allowed Comments allowed or not.
	 */
	public function set_comments_allowed( $comments_allowed ) {
		$this->set_prop( 'comments_allowed', bm_pe_string_to_bool( $comments_allowed ) );
	}

    /**
	 * Set if comment count.
	 *
     * @since 1.0.0
	 * @param bool $comment_count Comment count.
	 */
	public function set_comment_count( $comment_count ) {
		$this->set_prop( 'comment_count', $comment_count );
	}

    /**
	 * Set if is all day event.
	 *
     * @since 1.0.0
	 * @param bool $all_day_event All day event.
	 */
	public function set_all_day_event( $all_day_event ) {
		$this->set_prop( 'all_day_event', bm_pe_string_to_bool( $all_day_event ) );
	}

    /**
	 * Set event timezone.
	 *
     * @since 1.0.0
	 * @param string $timezone Events timezone.
	 */
	public function set_timezone( $timezone ) {
		$this->set_prop( 'timezone', $timezone );
    }

    /**
	 * Set event start date.
	 *
     * @since 1.0.0
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 */
	public function set_event_start( $date = null ) {
		$this->set_date_prop( 'event_start', $date, $this->get_id() );
	}

    /**
	 * Set event end date.
	 *
     * @since 1.0.0
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 */
	public function set_event_end( $date = null ) {
		$this->set_date_prop( 'event_end', $date, $this->get_id() );
    }

    /**
	 * Set event location
	 *
     * @since 1.0.0
	 */
	public function set_event_location( $location ) {
        try {
    		$this->set_prop( 'event_location', new BM_PE_Event_Location($location) );
        } catch (Exception $e) {
            // Return empty BM_PE_Event_Location
            $this->set_prop( 'event_location', new BM_PE_Event_Location(0) );
        }
	}

    /**
	 * Set event organisers
	 *
     * @since 1.0.0
	 */
	public function set_event_organisers( $organisers ) {
        $organisers_ids = explode( ',', $organisers );
        $organisers = array();

        foreach ( $organisers_ids as $id ) {
            try {
                $organisers[] = new BM_PE_Event_Organiser($id);
            } catch (Exception $e) {
                // Organiser dosen't exist
            }
        }

        // remove empty organisers
        $organisers = array_filter( $organisers, function( $organiser ) {
            return $organiser->get_id() > 0;
        });

        if ( empty( $organisers ) ) {
            $organisers = null;
        }

		$this->set_prop( 'event_organisers', $organisers );
	}

    /**
	 * Set if is we should show Google Map.
	 *
     * @since 1.0.0
	 * @param bool $google_map All day event.
	 */
	public function set_google_map( $google_map ) {
        $google_map = bm_pe_get_option( 'api-key', 'pe-integrations-google-maps', '' ) == '' ? false : bm_pe_string_to_bool( $google_map );
		$this->set_prop( 'google_map', $google_map );
	}

    /**
	 * Set if is featured event.
	 *
     * @since 1.0.0
	 * @param bool $featured_event All day event.
	 */
	public function set_featured_event( $featured_event ) {
		$this->set_prop( 'featured_event', bm_pe_string_to_bool( $featured_event ) );
	}

}
