<?php
/**
 * The Template for displaying all single events
 *
 * This template can be overridden by copying it to yourtheme/press-events/single-event.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

	<?php
		/**
		 * press_events_before_main_content hook.
		 */
		do_action( 'press_events_before_main_content' );
	?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php bm_bm_pe_get_template_part( 'content', 'single-event' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * press_events_after_main_content hook.
		 */
		do_action( 'press_events_after_main_content' );
	?>
