<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="location_event_details">

	<?php // build saved locations
		$options = array(
            '' => array( '— '. __( 'Select location', 'press-events' ) .' —' ),
            'new' => array( __( 'Add new location', 'press-events' ) )
        );

		foreach ( bm_pe_get_locations( array(
			'posts_per_page' => -1, 'orderby'=> 'title', 'order' => 'ASC'
		) ) as $order => $location ) {
			$options[ $location->get_id('edit') ] = array(
				$location->get_title('edit'),
				'custom_attributes' => array(
					'data-id' => $location->get_id('edit')
				)
			);
		}
	?>

    <!-- Select location -->
    <?php bm_pe_select_input( array(
        'id' => '_event_location_select',
        'label' => __( 'Add a location', 'press-events' ),
        'options' => $options
    ) ); ?>

 	<!-- Hidden input (contains event location) -->
    <?php bm_pe_hidden_input( array(
        'id' => '_event_location',
		'value' => $event->get_event_location()->get_id() > 0 ? $event->get_event_location()->get_id() : ''
    ) ); ?>

    <hr>

	<!-- Location -->
	<div id="location-list-wrapper">

	</div>

	<script type="text/template" data-template="location-item">
		<div class="location" data-id="${id}" data-title="${title}" data-address="${address}" data-city="${city}" data-postcode="${postcode}" data-county="${county}" data-country="${country}">
			<div class="top-bar">
				<h3>${title}</h3>
				<div class="actions">
					<a href="#" class="edit dashicons dashicons-edit"></a>
					<a href="#" class="remove">Remove</a>
				</div>
			</div>

			<div class="edit-wrapper">

				<?php bm_pe_text_input( array(
					'id' => null,
					'name' => 'location_title',
					'label' => __( 'Location title', 'press-events' ),
					'value' => '${title}'
				) ); ?>

				<div class="field-wrapper">
					<div class="label-wrapper"><label for="_event_location_title"><?php _e( 'Location address', 'press-events' ); ?></label></div>

					<div class="input-wrapper">
						<div class="sub-input">
							<label for="event_location_address"><?php _e( 'Street address', 'press-events' ); ?></label>
							<?php bm_pe_text_input( array(
								'name' => 'location_address',
								'id' => null,
								'bare' => true,
								'value' => '${address}'
							) ); ?>
						</div>

						<div class="sub-input half-left">
							<label for="event_location_city"><?php _e( 'Town/City', 'press-events' ); ?></label>
							<?php bm_pe_text_input( array(
								'name' => 'location_city',
								'id' => null,
								'bare' => true,
								'value' => '${city}'
							) ); ?>
						</div>

						<div class="sub-input half-right">
							<label for="event_location_postcode"><?php _e( 'Postal/Zip Code', 'press-events' ); ?></label>
							<?php bm_pe_text_input( array(
								'name' => 'location_postcode',
								'id' => null,
								'bare' => true,
								'value' => '${postcode}'
							) ); ?>
						</div>

						<div class="sub-input half-left">
							<label for="event_location_county"><?php _e( 'County', 'press-events' ); ?></label>
							<?php bm_pe_text_input( array(
								'name' => 'location_county',
								'id' => null,
								'bare' => true,
								'value' => '${county}'
							) ); ?>
						</div>

						<div class="sub-input half-right">
							<label for="event_location_country"><?php _e( 'Country', 'press-events' ); ?></label>
							<?php bm_pe_text_input( array(
								'name' => 'location_country',
								'id' => null,
								'bare' => true,
								'value' => '${country}'
							) ); ?>
						</div>

						<div class="sub-input half-left">
							<a href="#" class="save button button-primary button-large">Save</a>
							<a href="#" class="delete-location delete">Delete location</a>
						</div>

					</div>
				</div>

			</div>
		</div>
	</script>

	<script type="text/template" data-template="location-error">
		<div class="small-error">
			<p>${error}</p>
		</div>
	</script>

	<!-- Show Google Map -->
	<?php bm_pe_hidden_input( array(
		'id' => '_show_google_map',
		'value' => 'no'
	) ); ?>

	<div class="_show_google_map_field field-wrapper">
		<div class="label-wrapper">
			<label for="_show_google_map">Show Google Map</label>
		</div>

		<div class="input-wrapper">
			<?php if ( bm_pe_get_option( 'api-key', 'pe-integrations-google-maps' ) == '' ) : ?>
				<div class="small-error">
					<p><?php echo sprintf( __( 'To activate Google maps you need to enter a Google Maps API in the <a href="%s">Press Events settings</a>.', 'press-events' ),
						admin_url( 'edit.php?post_type=pe_event&page=pe-settings&tab=pe-integrations' )
					); ?></p>
				</div>
			<?php else :
				bm_pe_checkbox( array(
					'id' => '_show_google_map',
					'label' => __( 'Show Google Map', 'press-events' ),
					'bare' => true
				) );
			endif; ?>
		</div>
	</div>

</div>
