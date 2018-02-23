<?php
/**
 * The template for displaying a back to archive link in single-event.php template
 *
 * This template can be overridden by copying it to yourtheme/press-events/single-event/all-events.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="event-archive-link">

	<a href="<?php echo get_post_type_archive_link( 'pe_event' ); ?>">
		<span class="pe-icon"></span>
		<?php _e( 'All events', 'press-events' ); ?>
	</a>

</div>
