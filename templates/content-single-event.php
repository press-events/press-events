<?php
/**
 * The template for displaying event content in the single-event.php template
 *
 * This template can be overridden by copying it to yourtheme/press-events/content-single-event.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Hook press_events_before_single_event.
 */
do_action( 'press_events_before_single_event' );
?>

<div id="event-<?php the_ID(); ?>" <?php post_class(); ?>>

    <?php
		/**
		 * press_events_before_single_event_content
		 */
		do_action( 'press_events_before_single_event_content' );
	?>

	<div class="event-content">
		<?php
			/**
			 * press_events_single_event_content
			 */
			do_action( 'press_events_single_event_content' );
		?>
	</div>

	<div class="event-sidebar">
		<?php
			/**
			 * press_events_single_event_sidebar
			 */
			do_action( 'press_events_single_event_sidebar' );
		?>
	</div>

    <?php
		/**
		 * press_events_after_single_event_content
		 */
		do_action( 'press_events_after_single_event_content' );
	?>

</div>

<?php
/**
 * Hook press_events_after_single_event.
 */
do_action( 'press_events_after_single_event' );
?>
