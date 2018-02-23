<?php
/**
 * The template for displaying event featured image in the single-event.php template
 *
 * This template can be overridden by copying it to yourtheme/press-events/single-event/event-image.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! has_post_thumbnail() ) {
	return; // No image
}
?>

<?php
	$html  = '<div class="event-image-wrapper">';
	$html .= get_the_post_thumbnail( get_the_ID(), 'full' );
	$html .= '</div>';

	echo apply_filters( 'press_events_single_event_image_html', $html, get_post_thumbnail_id( get_the_ID() ) );
?>
