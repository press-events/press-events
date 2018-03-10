<?php
/**
 * Abstract Event Location Class
 *
 * The Press Events event location class handles individual event data.
 *
 * @version 1.0.0
 * @package PressEvents/Abstracts
 * @author Burn Media Ltd
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BM_PE_Event_Location extends BM_PE_Data {

    /**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'event_location';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'pe_event_location';

	/**
	 * Cache group.
	 *
	 * @var string
	 */
	protected $cache_group = 'event_locations';

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
        'address' => '',
        'city' => '',
        'postcode' => '',
        'county' => '',
        'country' => '',
	);

    /**
	 * Get the event location if ID is passed, otherwise the event location is new and empty.
	 *
	 * @param int|BM_PE_Event_Location|object $location event location to init.
	 */
	public function __construct( $location = 0 ) {
		parent::__construct( $location );

		if ( is_numeric( $location ) && $location > 0 ) {
			$this->set_id( $location );
		} elseif ( $location instanceof self ) {
			$this->set_id( absint( $location->get_id() ) );
		} elseif ( ! empty( $location->ID ) ) {
			$this->set_id( absint( $location->ID ) );
		} else {
			$this->set_object_read( true );
		}

		if ( $this->get_id() > 0 ) {
			$this->read();
		}
	}

    /**
     * Method to read a event location from the database.
     *
     * @throws Exception
     */
    public function read() {
        $this->set_defaults();

        if ( !$this->get_id() || ! ( $post_object = get_post( $this->get_id() ) ) || $this->post_type !== $post_object->post_type ) {
            throw new Exception( __( 'Invalid event location.', 'press-events' ) );
        }

        $this->set_props( array(
            'title' => $post_object->post_title,
            'slug' => $post_object->post_name,
            'date_created' => 0 < $post_object->post_date_gmt ? bm_pe_string_to_timestamp( $post_object->post_date_gmt ) : null,
			'date_modified' => 0 < $post_object->post_modified_gmt ? bm_pe_string_to_timestamp( $post_object->post_modified_gmt ) : null,
			'status' => $post_object->post_status,
        ) );

        $this->read_event_location_data();

        $this->set_object_read( true );
    }

    /**
	 * Read event location data.
	 *
	 * @since 1.0.0
	 */
	protected function read_event_location_data() {
		$id = $this->get_id();

		$this->set_props( array(
            'address' => get_post_meta( $id, '_location_address', true ),
            'city' => get_post_meta( $id, '_location_city', true ),
            'postcode' => get_post_meta( $id, '_location_postcode', true ),
            'county' => get_post_meta( $id, '_location_county', true ),
            'country' => get_post_meta( $id, '_location_country', true ),
		) );
	}

    /*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get event location title.
	 *
	 * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_title( $context = 'view' ) {
		return $this->get_prop( 'title', $context );
	}

    /**
	 * Get event location slug.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_slug( $context = 'view' ) {
		return $this->get_prop( 'slug', $context );
	}

    /**
     * Get event location created date.
     *
     * @since 1.0.0
     * @param string $context What the value is for. Valid values are view and edit.
     * @return BM_PE_DateTime|NULL object if the date is set or null if there is no date.
     */
    public function get_date_created( $context = 'view' ) {
    	return $this->get_prop( 'date_created', $context );
    }

    /**
	 * Get event location modified date.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return BM_PE_DateTime|NULL object if the date is set or null if there is no date.
	 */
	public function get_date_modified( $context = 'view' ) {
		return $this->get_prop( 'date_modified', $context );
	}

    /**
	 * Get event location status.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_status( $context = 'view' ) {
		return $this->get_prop( 'status', $context );
	}

    /**
	 * Get event location address.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_address( $context = 'view' ) {
		return $this->get_prop( 'address', $context );
	}

    /**
	 * Get event location city.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_city( $context = 'view' ) {
		return $this->get_prop( 'city', $context );
	}

    /**
	 * Get event location postcode.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_postcode( $context = 'view' ) {
		return $this->get_prop( 'postcode', $context );
	}

    /**
	 * Get event location county.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_county( $context = 'view' ) {
		return $this->get_prop( 'county', $context );
	}

    /**
	 * Get event location country.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_country( $context = 'view' ) {
		return $this->get_prop( 'country', $context );
	}

    /**
     * Get full address from multiple props.
	 *
     * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_full_address( $context = 'view' ) {
        $address = $this->get_address( $context ) == '' ? null : $this->get_address( $context ) . ' ';
        $city = $this->get_city( $context ) == '' ? null : $this->get_city( $context ) . ', ';
        $county = $this->get_county( $context ) == '' ? null : $this->get_county( $context ) . ' ';
        $postcode = $this->get_postcode( $context ) == '' ? null : $this->get_postcode( $context );
        $country = $this->get_country( $context ) == '' ? null : ' · ' . $this->get_country( $context );

		return $address . $city . $county . $postcode . $country;
	}

    /*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	*/

    /*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set event location title.
	 *
	 * @since 1.0.0
	 * @param string $title event location title.
	 */
	public function set_title( $title ) {
		$this->set_prop( 'title', $title );
	}

    /**
	 * Set event location slug.
	 *
     * @since 1.0.0
	 * @param string $slug event location slug.
	 */
	public function set_slug( $slug ) {
		$this->set_prop( 'slug', $slug );
	}

    /**
	 * Set event location created date.
	 *
	 * @since 1.0.0
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 */
	public function set_date_created( $date = null ) {
		$this->set_date_prop( 'date_created', $date );
	}

    /**
	 * Set event location modified date.
	 *
     * @since 1.0.0
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 */
	public function set_date_modified( $date = null ) {
		$this->set_date_prop( 'date_modified', $date );
	}

    /**
	 * Set event location status.
	 *
     * @since 1.0.0
	 * @param string $status event location status.
	 */
	public function set_status( $status ) {
		$this->set_prop( 'status', $status );
	}

    /**
	 * Set event location address
	 *
     * @since 1.0.0
	 * @param string $address event location address.
	 */
	public function set_address( $address ) {
		$this->set_prop( 'address', $address );
	}

    /**
	 * Set event location city
	 *
     * @since 1.0.0
	 * @param string $city event location city.
	 */
	public function set_city( $city ) {
		$this->set_prop( 'city', $city );
	}

    /**
	 * Set event location postcode
	 *
     * @since 1.0.0
	 * @param string $postcode event location postcode.
	 */
	public function set_postcode( $postcode ) {
		$this->set_prop( 'postcode', $postcode );
	}

    /**
	 * Set event location county
	 *
     * @since 1.0.0
	 * @param string $county event location county.
	 */
	public function set_county( $county ) {
		$this->set_prop( 'county', $county );
	}

    /**
	 * Set event location country
	 *
     * @since 1.0.0
	 * @param string $country event location country.
	 */
	public function set_country( $country ) {
		$this->set_prop( 'country', $country );
	}

}
