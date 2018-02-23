<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="tabbed-meta-box-wrapper">

    <ul class="press-tabs">
        <?php foreach ( self::get_meta_box_tabs() as $key => $tab ) : ?>
            <li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo esc_attr( isset( $tab['class'] ) ? implode( ' ' , (array) $tab['class'] ) : '' ); ?>">
				<a href="#<?php echo $tab['target']; ?>">
					<?php if ( isset($tab['icon']) && $tab['icon'] !== '' ) { ?>
	                    <span class="icon <?php echo $tab['icon']; ?>"></span>
	                <?php } ?>
	                <span class="label"><?php echo esc_html( $tab['label'] ); ?></span>
				</a>
            </li>
        <?php endforeach; ?>
        <?php do_action( 'press_events_event_details_tabs' ); ?>
    </ul>

	<div class="press-panel">
		<?php
		    self::output_tabs();
		    do_action( 'press_events_event_details_panels' );
		    wp_nonce_field( 'press_events_meta_nonce', 'press_events_save_data' );
		?>
	</div>

	<div class="clear"></div>

</div><!-- .tabbed-meta-box-wrapper -->
