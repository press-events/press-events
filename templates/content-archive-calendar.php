<?php
/**
 * The template for displaying calendar.
 *
 * This template can be overridden by copying it to yourtheme/press-events/archive/calendar/content.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $calendar;

?>

	<?php
		/**
		 * press_events_before_archive_event hook.
		 */
		do_action( 'press_events_before_archive_event' );
	?>

		<div class="archive-event-wrapper">
			<div class="pe-calendar">

				<div class="pe-calendar-head">
					<?php foreach ( $calendar->get_weekday_names() as $name ) { ?>
						<div ><?php echo $name; ?></div>
					<?php } ?>
				</div>

				<div class="pe-calendar-body">
					<div class="pe-calendar-row">

						<?php foreach ( $calendar->get_calendar_days() as $day ) { ?>

							<?php bm_pe_get_template( 'archive/calendar/day.php', array(
					            'class' => isset( $day['class'] ) ? $day['class'] : null,
					            'datetime' => isset( $day['datetime'] ) ? $day['datetime'] : null,
					            'events' => isset( $day['events'] ) ? $day['events'] : null,
					        ) ); ?>

						<?php if ( isset( $day['row_ends'] ) && $day['row_ends'] ) { ?>
							</div><div class="pe-calendar-row">
						<?php } ?>

					<?php } ?>

				</div><!-- .pe-calendar-row -->
				</div>

			</div>
		</div>

	<?php
		/**
		 * press_events_after_archive_event hook.
		 */
		do_action( 'press_events_after_archive_event' );
	?>
