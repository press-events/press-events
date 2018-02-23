<?php
/**
 * The template for displaying archive header.
 *
 * This template can be overridden by copying it to yourtheme/press-events/archive/header.php.
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

<div class="archive-event-header pe-row pe-row-center">

	<div class="archive-title pe-row-item">
		<h1><?php echo $calendar->current_month->format('F, Y'); ?></h1>
	</div>

	<div id="pe-archive-navigation" class="archive-navigation pe-row-item pe-row-item-shrink">
		<a href="<?php echo add_query_arg( 'archive-month', $calendar->get_previous_month() ); ?>" class="previous" data-target-month="<?php echo $calendar->get_previous_month(); ?>"></a>
		<a href="<?php echo add_query_arg( 'archive-month', $calendar->get_next_month() ); ?>" class="next" data-target-month="<?php echo $calendar->get_next_month(); ?>"></a>
	</div>

</div>
