<?php
/**
 * The template for displaying event location in the single-event.php template
 *
 * This template can be overridden by copying it to yourtheme/press-events/single-event/meta/address.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $event;

if ( ! $event->has_location() ) { return; }

$location = $event->get_event_location();
?>

<div class="event-location">

	<h3><?php _e( 'Location', 'press-events' ); ?></h3>

	<a class="event-location-address" href="https://www.google.com/maps/search/?api=1&amp;query=<?php echo urlencode( $location->get_full_address() ); ?>" target="_blank">
		<address>
			<p><?php echo $location->get_title(); ?></p>
			<p class="location-address"><?php echo $location->get_full_address(); ?></p>
		</address>
	</a>

</div>

<?php if ( $event->has_location() && $event->show_google_map() ) { ?>

	<div class="event-location-map">

		<a href="https://www.google.com/maps/search/?api=1&amp;query=<?php echo urlencode( $location->get_full_address() ); ?>" class="location-map-link" target="_blank noreferrer noopener">
			<img src="https://maps.google.com/maps/api/staticmap?zoom=17&amp;scale=2&amp;size=480x300&amp;markers=color%3Ared%7Csize%3Alarge%7C<?php echo urlencode( $location->get_full_address() ); ?>&amp;sensor=false&amp;key=<?php echo bm_pe_get_option( 'api-key', 'pe-integrations-google-maps' ); ?>" class="google-map" alt="Location image of event venue">
		</a>

	</div>

<?php } ?>
