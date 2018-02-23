<?php
/**
 * The template for displaying event description on single-event.php template
 *
 * This template can be overridden by copying it to yourtheme/press-events/single-event/description.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="event-description">

	<h3><?php _e( 'Description', 'press-events' ); ?></h3>

	<?php the_content(); ?>

</div>
