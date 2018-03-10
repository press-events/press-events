<?php
/**
 * The template for displaying calendar day.
 *
 * This template can be overridden by copying it to yourtheme/press-events/archive/calendar/day.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div <?php echo $class ? 'class="'. $class .'"' : null; ?>>

	<?php if ( $datetime !== null ) : ?>

		<div class="day-meta">
			<time class="day" datetime="<?php echo date('Y-m-d', $datetime); ?>"><?php echo date('j', $datetime); ?></time>
			<span class="weekday"><?php echo date('D', $datetime); ?></span>
		</div>

		<?php if ( ! empty( $events ) ) { ?>

			<div class="pe-calendar-events-list">

				<?php foreach ( $events as $event ) { ?>

					<?php bm_pe_get_template( 'archive/calendar/event.php', array(
			            'event' => $event
			        ) ); ?>

				<?php } ?>

			</div>

		<?php } ?>

	<?php endif; ?>

</div>
