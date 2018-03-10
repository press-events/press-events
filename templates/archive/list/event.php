<?php
/**
 * The template for displaying event list days.
 *
 * This template can be overridden by copying it to yourtheme/press-events/archive/list/event.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div id="event-<?php echo $event->get_id(); ?>" <?php post_class( 'pe-row', $event->get_id() ); ?> itemscope itemtype="http://data-vocabulary.org/Event">

	<div class="event-time pe-row-item pe-row-item-shrink">

		<?php if ( ! $event->is_all_day_event() ) { ?>
			<time itemprop="startDate" datetime="<?php echo $event->get_event_start()->__toString(); ?>"><?php echo bm_pe_format_datetime( $event->get_event_start(), bm_pe_time_format() ); ?></time>
		<?php } else { ?>
			<time><?php _e( 'All day', 'press-events' ); ?></time>
		<?php } ?>

	</div>

	<div class="event-details pe-row-item">
		<a href="<?php echo get_permalink( $event->get_id() ); ?>" class="event-title" itemprop="url">
			<h4><?php echo $event->get_title(); ?></h4>
		</a>

		<?php if ( $terms = get_the_terms( $event->get_id(), 'pe_event_category' ) ) { ?>
			<div class="categories">
				<?php
					$categories = array();
					foreach ( $terms as $term ) {
						$categories[] = '<a href="'. get_term_link( $term ) .'" itemprop="eventType">'. $term->name .'</a>';
					}
					echo implode( ', ', $categories );
				?>
			</div>
		<?php } ?>
	</div>

</div>
