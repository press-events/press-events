<?php
/**
 * The template for displaying event list days.
 *
 * This template can be overridden by copying it to yourtheme/press-events/archive/list/day.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="archive-event-day">

	<small class="event-day-title" datetime="<?php echo date('Y-m-d', $datetime); ?>"><?php echo date( bm_pe_date_format(), $datetime ); ?></small>

	<?php if ( ! empty( $events ) ) { ?>

		<div class="archive-event-day-events">

			<?php foreach ( $events as $event ) { ?>

				<?php bm_pe_get_template( 'archive/list/event.php', array(
		            'event' => $event
		        ) ); ?>

			<?php } ?>

		</div>

	<?php } ?>

</div>
