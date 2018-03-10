<?php
/**
 * Add extra profile fields for users in admin
 *
 * @package PressEvents/Classes/Admin
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'PE_User_Contacts', false ) ) :

/**
 * BM_PE_User_Contacts Class.
 */
class BM_PE_User_Contacts {

    /**
	 * Hook in sections.
	 */
	public function __construct() {
        add_filter( 'user_contactmethods', array( $this, 'add_user_contact_methods' ) );
	}

    /**
     * Add user contact methods
     */
    public function add_user_contact_methods( $user_contact ) {
    	$user_contact['phone'] = __( 'Phone', 'press-events' );
    	return $user_contact;
    }

}

endif;

new BM_PE_User_Contacts();
