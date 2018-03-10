<?php
/**
 * Press Events Admin Bar
 *
 * @package PressEvents/Classes
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * BM_PE_Admin_Bar
 */
class BM_PE_Admin_Bar {

	public function __construct() {
		add_action( 'admin_bar_menu', array( $this, 'add_pe_admin_menu' ), 80 );
	}

	public function add_pe_admin_menu( $admin_bar ) {
		$admin_bar->add_menu( array(
	        'id' => 'pe-events',
	        'title' => '<span class="ab-icon"></span>'. __( 'Events', 'press-events' ),
	        'href' => get_post_type_archive_link('pe_event'),
	    ));

	    $admin_bar->add_menu( array(
	        'id' => 'pe-events-edit-event',
	        'parent' => 'pe-events',
	        'title' => __( 'Edit events', 'press-events' ),
	        'href' => admin_url('edit.php?post_type=pe_event'),
	    ));

	    $admin_bar->add_menu( array(
	        'id' => 'pe-events-add-event',
	        'parent' => 'pe-events',
	        'title' => __( 'Add new', 'press-events' ),
	        'href' => admin_url('post-new.php?post_type=pe_event'),
	    ));

	    $admin_bar->add_menu( array(
	        'id' => 'pe-events-categories',
	        'parent' => 'pe-events',
	        'title' => __( 'Categories', 'press-events' ),
	        'href' => admin_url('edit-tags.php?taxonomy=pe_event_category&post_type=pe_event'),
	    ));

	    $admin_bar->add_menu( array(
	        'id' => 'pe-events-tags',
	        'parent' => 'pe-events',
	        'title' => __( 'Tags', 'press-events' ),
	        'href' => admin_url('edit-tags.php?taxonomy=pe_event_tag&post_type=pe_event'),
	    ));

	    $admin_bar->add_menu( array(
	        'id' => 'pe-events-location',
	        'parent' => 'pe-events',
	        'title' => __( 'Locations', 'press-events' ),
	        'href' => admin_url('edit.php?post_type=pe_event_location'),
	    ));

	    $admin_bar->add_menu( array(
	        'id' => 'pe-events-settings',
	        'parent' => 'pe-events',
	        'title' => __( 'Settings', 'press-events' ),
	        'href' => admin_url('edit.php?post_type=pe_event&page=pe-settings'),
	    ));
	}

}
new BM_PE_Admin_Bar();
