<?php
/**
 * The template for displaying event organisers in the single-event.php template
 *
 * This template can be overridden by copying it to yourtheme/press-events/single-event/organisers.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $event;

if ( ! $event->has_organiser() ) {
	return; // No organisers
}

$organisers = $event->get_event_organisers();
?>

<div class="event-organisers">

	<h3><?php _e( 'Organisers', 'press-events' ); ?></h3>

	<ul class="event-organisers-list">
		<?php foreach ( $organisers as $organiser ) { ?>

			<li class="pe-row">
				<div class="organiser-name pe-row-item"><?php echo $organiser->get_title(); ?></div>
				<div class="organiser-actions">

					<?php if ( $organiser->get_url() !== '' ) { ?>
						<a href="<?php echo $organiser->get_url(); ?>" class="website press-tip" target="_blank" data-tip="<?php _e( 'Website', 'press-events' ); ?>: <?php echo $organiser->get_url(); ?>">
							<span class="action-icon"></span>
						</a>
					<?php } ?>

					<?php if ( $organiser->get_email() !== '' ) { ?>
						<a href="mailto:<?php echo $organiser->get_email(); ?>" class="email press-tip" data-tip="<?php _e( 'Email', 'press-events' ); ?>: <?php echo $organiser->get_email(); ?>">
							<span class="action-icon"></span>
						</a>
					<?php } ?>

					<?php if ( $organiser->get_phone() !== '' ) { ?>
						<a href="tel:<?php echo $organiser->get_phone(); ?>" class="phone press-tip" data-tip="<?php _e( 'Phone', 'press-events' ); ?>: <?php echo $organiser->get_phone(); ?>">
							<span class="action-icon"></span>
						</a>
					<?php } ?>
				</div>
			</li>

		<?php } ?>
	</ul>

</div>
