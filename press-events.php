<?php
/**
 * Plugin Name: Press Events
 * Plugin URI: http://wordpresseventsplugin.com/
 * Description: An event managment plugin that helps you manage and share events.
 * Version: 1.0.0
 * Author: Burn Media Ltd
 * Author URI: http://burnmedia.co.uk/
 * Requires at least: 4.5
 * Tested up to: 4.8
 *
 * Text Domain: press-events
 * Domain Path: /i18n/languages/
 *
 * @package PressEvents
 * @author Burn Media Ltd
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Used for referring to the plugin file or basename
if ( !defined( 'PE_PLUGIN_FILE' ) ) {
	define( 'PE_PLUGIN_FILE', __FILE__ );
}

// Include the main Press_Events class.
include_once dirname( __FILE__ ) . '/includes/class-press-events.php';

/**
 * Main instance of Press Events.
 *
 * @since 1.0.0
 */
function pe() {
	return Press_Events::instance();
}
pe();
