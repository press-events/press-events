<?php
/**
 * The Template for displaying event archive as a list
 *
 * This template can be overridden by copying it to yourtheme/press-events/archive-event-list.php.
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
		 * press_events_before_main_content hook.
		 */
		do_action( 'press_events_before_main_content' );
	?>

		<div class="press-events-archive">

			<?php bm_bm_pe_get_template_part( 'content-archive', $calendar->get_type() ); ?>

		</div>

	<?php
		/**
		 * press_events_after_main_content hook.
		 */
		do_action( 'press_events_after_main_content' );
	?>
