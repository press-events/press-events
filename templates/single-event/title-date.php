<?php
/**
 * The template for displaying event date in the single-event.php template
 *
 * This template can be overridden by copying it to yourtheme/press-events/single-event/title-date.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $event;

$event_starts = bm_pe_format_datetime( $event->get_event_start() );
$event_ends = bm_pe_format_datetime( $event->get_event_end() );
?>

<div class="event-title-date">
	<?php if ( $event->is_one_day_event() ) {
		echo $event_starts;
	} else {
		echo $event_starts .' – '. $event_ends;
	} ?>
</div>
