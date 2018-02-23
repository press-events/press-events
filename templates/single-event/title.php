<?php
/**
 * The template for displaying event title in the single-event.php template
 *
 * This template can be overridden by copying it to yourtheme/press-events/single-event/title.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

the_title( '<h1 class="event-title entry-title">', '</h1>' );
