<?php
/**
 * The template for displaying event date and time in the single-event.php template
 *
 * This template can be overridden by copying it to yourtheme/press-events/single-event/date-time.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $event;
?>

<div class="event-date-time">

	<h3><?php _e( 'Date & Time', 'press-events' ); ?></h3>

    <?php if ( $event->is_one_day_event() ) { ?>

        <time datetime="<?php echo $event->get_event_start()->__toString(); ?>">
            <span class="start-date"><span><?php echo bm_pe_format_datetime( $event->get_event_start(), bm_pe_date_format() ); ?></span>

            <?php if ( ! $event->is_all_day_event() ) { ?>
                <br>
                <span class="start-date-time"><?php echo bm_pe_format_datetime( $event->get_event_start(), bm_pe_time_format() ); ?></span></span>
                <span class="end-date"><?php _e( 'to', 'press-events' ); ?> <span class="end-date-time"><?php echo bm_pe_format_datetime( $event->get_event_end(), bm_pe_time_format() ); ?></span></span>
            <?php } ?>
        </time>

    <?php } else { ?>

        <time datetime="<?php echo $event->get_event_start()->__toString(); ?>">
            <span class="start-date"><span><?php echo bm_pe_format_datetime( $event->get_event_start(), bm_pe_date_format() ); ?></span><?php if ( ! $event->is_all_day_event() ) { ?>, <span class="start-date-time"><?php echo bm_pe_format_datetime( $event->get_event_start(), bm_pe_time_format() ); ?></span><?php } ?></span>
            <span class="end-date"><?php _e( 'to', 'press-events' ); ?> <span><?php echo bm_pe_format_datetime( $event->get_event_end(), bm_pe_date_format() ); ?></span><?php if ( ! $event->is_all_day_event() ) { ?>, <span class="end-date-time"><?php echo bm_pe_format_datetime( $event->get_event_end(), bm_pe_time_format() ); ?></span><?php } ?></span>
        </time>

    <?php } ?>

    <?php
		// Timezone
		if ( bm_pe_timezone_offset() == bm_pe_event_timezone_offset( $event->get_id() ) ) {

			$tzstring = '';

		} else {

			// get timezone meta
			$current_offset = get_post_meta( $event->get_id(), '_event_gmt_offset', true );
			$tzstring = get_post_meta( $event->get_id(), '_event_timezone_string', true );

			if ( empty($tzstring) ) { // Create a UTC+- zone if no timezone string exists
				if ( 0 == $current_offset ) {
					$tzstring = 'UTC+0';
				} elseif ( $current_offset < 0 ) {
					$tzstring = 'UTC' . $current_offset;
				} else {
					$tzstring = 'UTC+' . $current_offset;
				}
			} else {
				$current_offset = '';
			}

		}

		if ( $tzstring ) { ?>
			<span class="timezone"><?php echo $current_offset == '' ? $tzstring : bm_pe_offset_value_to_name( $current_offset ); ?></span>
		<?php
		}
	?>

</div>
