<?php
/**
 * The template for displaying archive filters.
 *
 * This template can be overridden by copying it to yourtheme/press-events/archive/filters.php.
 *
 * @package Press Events
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $calendar;

$query_vars = BM_Press_Events()->query->get_query_vars();
?>

<form method="get" class="archive-event-filters pe-row pe-row-center">

	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'get-calendar' ); ?>">
	<input type="hidden" id="target-month" name="<?php echo $query_vars['archive_month']; ?>" value="<?php echo $calendar->current_month->format('Y-m'); ?>">

	<div class="main-search-input">
		<div class="main-search-input-item">
			<input type="text" name="<?php echo $query_vars['archive_query']; ?>" placeholder="<?php _e( 'Search', 'press-events' ); ?>" value="<?php echo $calendar->calendar_args['archive_query']; ?>">
		</div>

		<?php if ( $categories = bm_pe_get_terms(array(
			'taxonomy' => 'pe_event_category',
			'hide_empty' => false
		)) ) { ?>

			<div class="main-search-input-item">
				<select class="chosen-select" name="<?php echo $query_vars['archive_category']; ?>">
					<option value=""><?php _e( 'All Categories', 'press-events' ); ?></option>
					<?php foreach ( $categories as $category ) { ?>
						<option value="<?php echo $category->slug; ?>" <?php echo in_array( $category->slug, $calendar->get_categories() ) ? 'selected="selected"' : null; ?>><?php echo $category->name; ?></option>
					<?php } ?>
				</select>
			</div>

		<?php } ?>

		<button class="button"><?php _e( 'Search', 'press-events' ); ?></button>
	</div>

	<div class="filters-wrapper">

		<?php if ( $tags = bm_pe_get_terms(array(
			'taxonomy' => 'pe_event_tag',
			'hide_empty' => false
		)) ) { ?>

			<div class="filters-item tags panel-dropdown">
				<a href="#"><?php _e( 'Tagged', 'press-events' ); ?> <span class="down-icon"></span></a>
				<div class="panel-dropdown-content">
					<ul class="tag-list">
						<?php foreach ( $tags as $key => $tag ) { ?>
							<li>
								<label for="check-<?php echo $tag->slug; ?>">
									<input id="check-<?php echo $tag->slug; ?>" value="<?php echo $tag->slug; ?>" type="checkbox" name="<?php echo $query_vars['archive_tag']; ?>[]" <?php echo in_array( $tag->slug, $calendar->get_tags() ) ? 'checked="checked"' : null; ?>>
									<?php echo $tag->name; ?>
								</label>
							</li>
						<?php } ?>
					</ul>

					<button class="button"><?php _e( 'Apply', 'press-events' ); ?></button>
				</div>
			</div>

		<?php } ?>

		<?php
			// Get archive views
	  		$archive_types = bm_pe_get_option( 'archive-views', 'pe-general-events', array( 'list' => 'List', 'calendar' => 'Calendar' ) );

			if ( count($archive_types) > 1 ) { ?>

				<div class="filters-item archive-view">
					<select class="chosen-select" name="<?php echo $query_vars['archive_type']; ?>">

						<?php foreach ( $archive_types as $id => $value ) { ?>
							<option value="<?php echo $id; ?>" <?php echo selected( $id, $calendar->get_type() ); ?>><?php echo ucfirst($value); ?></option>
						<?php } ?>

					</select>
				</div>

			<?php } // count($archive_types) > 1 ?>

	</div>

</form>
