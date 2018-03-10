<?php
/**
 * Abstract Event Organiser Class
 *
 * The Press Events event organiser class handles individual event data.
 *
 * @version 1.0.0
 * @package PressEvents/Abstracts
 * @author Burn Media Ltd
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BM_PE_Event_Organiser extends BM_PE_Data {

    /**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'event_organiser';

    /**
     * Accepted user type
     *
     * @var array
     */
    protected $user_roles = array(
        'administrator',
        'event_organiser'
    );

	/**
	 * Cache group.
	 *
	 * @var string
	 */
	protected $cache_group = 'event_organisers';

    /**
	 * Stores event organisers data.
	 *
	 * @var array
	 */
	protected $data = array(
		'title' => '',
        'email' => '',
        'url' => '',
        'phone' => '',
	);

    /**
	 * Get the event organiser if ID are passed, otherwise the event organiser is new and empty.
	 *
	 * @param int|BM_PE_Event_Organiser|object $organisers event organisers to init.
	 */
	public function __construct( $organiser = 0 ) {
		parent::__construct( $organiser );

		if ( is_numeric( $organiser ) && $organiser > 0 ) {
			$this->set_id( $organiser );
		} elseif ( $organiser instanceof self ) {
			$this->set_id( absint( $organiser->get_id() ) );
		} elseif ( ! empty( $organiser->ID ) ) {
			$this->set_id( absint( $organiser->ID ) );
		} else {
			$this->set_object_read( true );
		}

		if ( $this->get_id() > 0 ) {
			$this->read();
		}
	}

    /**
     * Method to read a event organisers from the database.
     *
     * @throws Exception
     */
    public function read() {
        $this->set_defaults();

        if ( ! $this->get_id() || ! ( $user_object = get_userdata( $this->get_id() ) ) || ! $this->accepted_role( $user_object->roles ) ) {
            throw new Exception( __( 'Invalid event organiser.', 'press-events' ) );
        }

        $this->set_props( array(
            'title' => $user_object->display_name,
            'email' => $user_object->user_email,
            'url' => $user_object->user_url,
            'phone' => get_user_meta( $this->get_id(), 'phone', true )
        ) );

        $this->set_object_read( true );
    }

    /**
     * Is the user role accepted
     */
    private function accepted_role( $roles ) {
        $accepted_roles = apply_filters( 'pe_organiser_roles', $this->user_roles );

        foreach ( $accepted_roles as $role ) {
            if ( in_array( $role, $roles ) ) {
            	return true;
            }
        }

        return false;
    }

    /*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get organisers title.
	 *
	 * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_title( $context = 'view' ) {
		return $this->get_prop( 'title', $context );
	}

	/**
	 * Get organisers email.
	 *
	 * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_email( $context = 'view' ) {
		return $this->get_prop( 'email', $context );
	}

	/**
	 * Get organisers url.
	 *
	 * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_url( $context = 'view' ) {
		return $this->get_prop( 'url', $context );
	}

	/**
	 * Get organisers phone.
	 *
	 * @since 1.0.0
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_phone( $context = 'view' ) {
		return $this->get_prop( 'phone', $context );
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
	 * Set organisers title.
	 *
	 * @since 1.0.0
	 * @param string $title organisers title.
	 */
	public function set_title( $title ) {
		$this->set_prop( 'title', $title );
	}

	/**
	 * Set organisers email.
	 *
	 * @since 1.0.0
	 * @param string $email organisers email.
	 */
	public function set_email( $email ) {
		$this->set_prop( 'email', $email );
	}

	/**
	 * Set organisers url (website).
	 *
	 * @since 1.0.0
	 * @param string $url organisers url.
	 */
	public function set_url( $url ) {
		$this->set_prop( 'url', $url );
	}

	/**
	 * Set organisers phone.
	 *
	 * @since 1.0.0
	 * @param string $phone organisers bio.
	 */
	public function set_phone( $phone ) {
		$this->set_prop( 'phone', $phone );
	}

}
