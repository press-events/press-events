<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="date_time_event_details" class="active">
	<!-- All day event -->
	<?php bm_pe_hidden_input( array(
		'id'    => '_all_day_event',
		'value' => 'no',
	) ); ?>
	<?php bm_pe_checkbox( array(
		'id'    => '_all_day_event',
		'label' => __( 'All day event', 'press-events' ),
	) ); ?>

	<div class="event-time field-wrapper">
		<div class="label-wrapper">
			<label for="_event_start_date"><?php _e( 'Event date', 'press-events' ); ?></label>
		</div>

		<div class="input-wrapper">
			<!-- Event Starts -->
			<?php
				$date_format = bm_pe_date_format();
				$time_format = bm_pe_time_format();

				$start_date = bm_pe_format_datetime( $event->get_event_start( 'edit' ), $date_format );
				$start_time = bm_pe_format_datetime( $event->get_event_start( 'edit' ), $time_format );
			?>

			<div class="event-time-wrap left">
				<!-- Hidden input for datepicker -->
				<?php bm_pe_text_input( array(
					'id'    => 'start_date_picker',
					'value' => $start_date == '' ? date( 'o-m-d' ) : date( 'o-m-d', strtotime($start_date) ),
					'class' => 'start date',
					'bare'  => true,
					'custom_attributes' => array(
						'data-input-mask' => '_event_start_date'
					)
				) ); ?>
				<!-- Visible input for 'pretty' format -->
				<?php bm_pe_text_input( array(
					'id'    => '_event_start_date',
					'label' => __( 'Start date', 'press-events' ),
					'value' => $start_date == '' ? date( $date_format ) : $start_date,
					'class' => 'visible-date',
					'bare'  => true,
					'custom_attributes' => array(
						'data-datepicker' => 'start_date_picker'
					)
				) ); ?>

				<!-- Time input -->
				<?php bm_pe_text_input( array(
					'id'    => '_event_start_time',
					'label' => __( 'Start time', 'press-events' ),
					'value' => $start_time == '' ? date( $time_format, 32400 ) : $start_time,
					'class' => 'start time',
					'bare'  => true,
				) ); ?>
			</div>

			<span class="event-date-to"><?php _e( 'to', 'press-events' ); ?></span>

			<div class="event-time-wrap left">
				<!-- Event Ends -->
				<?php
					$end_time = bm_pe_format_datetime( $event->get_event_end( 'edit' ), $time_format );
					$end_date = bm_pe_format_datetime( $event->get_event_end( 'edit' ), $date_format );
				?>

				<!-- Time input -->
				<?php bm_pe_text_input( array(
					'id'    => '_event_end_time',
					'label' => __( 'End time', 'press-events' ),
					'value' => $end_time == '' ? date( $time_format, 36000 ) : $end_time,
					'class' => 'end time',
					'bare'  => true,
				) ); ?>

				<!-- Hidden input for datepicker -->
				<?php bm_pe_text_input( array(
					'id'    => 'end_date_picker',
					'value' => $end_date == '' ? date( 'o-m-d' ) : date( 'o-m-d', strtotime($end_date) ),
					'class' => 'end date',
					'bare'  => true,
					'custom_attributes' => array(
						'data-input-mask' => '_event_end_date'
					)
				) ); ?>
				<!-- Visible input for 'pretty' format -->
				<?php bm_pe_text_input( array(
					'id'    => '_event_end_date',
					'label' => __( 'End date', 'press-events' ),
					'value' => $end_date == '' ? date( $date_format ) : $end_date,
					'class' => 'visible-date',
					'bare'  => true,
					'custom_attributes' => array(
						'data-datepicker' => 'end_date_picker'
					)
				) ); ?>
			</div>

		</div>
	</div>

	<div class="event-timezone field-wrapper">
		<div class="input-wrapper">
			<?php
				// get timezone meta
				$current_offset = get_post_meta( $event->get_id(), '_event_gmt_offset', true );
				$tzstring = get_post_meta( $event->get_id(), '_event_timezone_string', true );

				// get timezone from WP options
				if ( empty($current_offset) && empty($tzstring) ) {
					$current_offset = get_option( 'gmt_offset' );
					$tzstring = get_option( 'timezone_string' );
				}

				if ( empty($tzstring) ) { // Create a UTC+- zone if no timezone string exists
					if ( 0 == $current_offset ) {
						$tzstring = 'UTC+0';
					} elseif ($current_offset < 0) {
						$tzstring = 'UTC' . $current_offset;
					} else {
						$tzstring = 'UTC+' . $current_offset;
					}
				} else {
					$current_offset = '';
				}
			?>
			<a id="timezone-toggle">Timezone: <?php echo $current_offset == '' ? $tzstring : bm_pe_offset_value_to_name( $current_offset ); ?></a>

			<select id="_event_timezone" name="_event_timezone">
				<?php echo wp_timezone_choice( $tzstring, get_user_locale() ); ?>
			</select>
		</div>
	</div>

</div>
