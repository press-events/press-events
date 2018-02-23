<?php
/**
 * The template for displaying event tags on single-event.php template
 *
 * This template can be overridden by copying it to yourtheme/press-events/single-event/tags.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $terms = get_the_terms( get_the_id(), 'pe_event_tag' ) ) {
	return; // No tags
}

?>

<div class="event-tags">

	<h3><?php _e( 'Tags', 'press-events' ); ?></h3>

	<ul class="event-tag-list">

		<?php foreach ( $terms as $term ) { ?>
			<li>
				<a href="<?php echo get_term_link( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></a>
			</li>
		<?php } ?>

	</ul>

</div>
