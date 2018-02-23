<?php
/**
 * The template for displaying calendar day events.
 *
 * This template can be overridden by copying it to yourtheme/press-events/archive/calendar/event.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div id="event-<?php echo $event->get_id(); ?>" <?php post_class( 'pe-calendar-event', $event->get_id() ); ?> itemscope itemtype="http://data-vocabulary.org/Event">

	<div class="content">
		<a href="<?php echo get_permalink( $event->get_id() ); ?>" class="event-title" itemprop="url"><span class="name"><?php echo $event->get_title(); ?></span></a>
	</div>

</div>
