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

			<?php
				$has_event = false;

				foreach ( $calendar->get_calendar_days() as $day ) {
					if ( ! isset( $day['events'] ) || empty( $day['events'] ) ) continue;

					bm_pe_get_template( 'archive/list/day.php', array(
						'datetime' => isset( $day['datetime'] ) ? $day['datetime'] : null,
						'events' => isset( $day['events'] ) ? $day['events'] : null,
					) );

					$has_event = true;
				}

				if ( ! $has_event ) {
					do_action( 'bm_pe_no_events_found' );
				}
			?>

		</div>

	<?php
		/**
		 * press_events_after_archive_event hook.
		 */
		do_action( 'press_events_after_archive_event' );
	?>
