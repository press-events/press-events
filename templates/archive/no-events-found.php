<?php
/**
 * Displayed when no events are found
 *
 * This template can be overridden by copying it to yourtheme/press-events/archive/no-events-found.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<p class="press-events-info">
    <?php _e( 'No events were found matching your selection.', 'press-events' ); ?>
</p>
