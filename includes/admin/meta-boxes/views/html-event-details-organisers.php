<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="organiser_event_details">

	<?php
		$options = array(
            '' => array( '— '. __( 'Select organisers', 'press-events' ) .' —' ),
            '0' => array( 
				__( 'Add new organiser', 'press-events' ),
				'custom_attributes' => array(
					'data-id' => 0
				)
			)
        );

		foreach ( bm_pe_get_organisers() as $organiser ) {
			$options[ $organiser->get_id('edit') ] = array(
				$organiser->get_title( 'edit' ),
				'custom_attributes' => array(
					'data-id' => $organiser->get_id('edit')
				)
			);
		}
	?>

    <!-- Select location -->
    <?php bm_pe_select_input( array(
        'id' => '_event_organiser_select',
        'label' => __( 'Add an organiser', 'press-events' ),
        'options' => $options
    ) ); ?>

	<!-- Hidden input (contains all event organisers) -->
    <?php bm_pe_hidden_input( array(
        'id' => '_event_organisers'
    ) ); ?>

	<hr>

	<!-- Organiser drag/drop -->
	<div id="organisers-drag-drop-wrapper">

	</div>

	<script type="text/template" data-template="organiser-item">
		<div class="organiser" data-id="${id}" data-title="${tite}">
			<div class="top-bar">
				<h3>${display_name}<span class="main"><?php echo ' — ' . __( 'Primary organiser', 'press-events' ); ?></span></h3>
				<div class="actions">
					<a href="#" class="drag dashicons dashicons-menu"></a>
					<a href="#" class="edit dashicons dashicons-edit"></a>
					<a href="#" class="remove">Remove</a>
				</div>
			</div>

			<div class="edit-wrapper">
				<div class="field-wrapper">
					<div class="label-wrapper"><label for="organiser_details"><?php _e( 'Organiser details', 'press-events' ); ?></label></div>

					<div class="input-wrapper">

						<div class="sub-input half-left">
							<label for="display_name"><?php _e( 'Display name', 'press-events' ); ?></label>
							<?php bm_pe_text_input( array(
								'name' => 'display_name',
								'id' => null,
								'bare' => true,
								'value' => '${display_name}'
							) ); ?>
						</div>

						<div class="sub-input half-right">
							<label for="user_email"><?php _e( 'Contact email', 'press-events' ); ?></label>
							<?php bm_pe_text_input( array(
								'name' => 'user_email',
								'id' => null,
								'bare' => true,
								'value' => '${user_email}'
							) ); ?>
						</div>

						<div class="sub-input half-left">
							<label for="user_url"><?php _e( 'Website URL', 'press-events' ); ?></label>
							<?php bm_pe_text_input( array(
								'name' => 'user_url',
								'id' => null,
								'bare' => true,
								'value' => '${user_url}'
							) ); ?>
						</div>

						<div class="sub-input half-right">
							<label for="user_phone"><?php _e( 'Phone number', 'press-events' ); ?></label>
							<?php bm_pe_text_input( array(
								'name' => 'user_phone',
								'id' => null,
								'bare' => true,
								'value' => '${user_phone}'
							) ); ?>
						</div>

						<a href="#" class="save button button-primary button-large">Save</a>

					</div>
				</div>
			</div>
		</div>
	</script>

	<script type="text/template" data-template="organiser-error">
		<div class="small-error">
			<p>${error}</p>
		</div>
	</script>

</div>
