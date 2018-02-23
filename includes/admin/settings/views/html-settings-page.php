<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !has_action( 'press_events_settings_section_' . $current_tab ) ) {
	wp_safe_redirect( admin_url( 'edit.php?post_type=pe_event&page=pe-settings&tab=pe-general' ) );
	exit;
}
?>
<div class="wrap press-events">

    <?php settings_errors(); ?>

    <nav class="nav-tab-wrapper press-nav-tab-wrapper">
		<?php
		foreach ( $tabs as $tab ) {
			echo '<a href="' . esc_html( admin_url( 'edit.php?post_type=pe_event&page=pe-settings&tab=' . esc_attr( $tab['id'] ) ) ) . '" class="nav-tab ' . ( $current_tab === $tab['id'] ? 'nav-tab-active' : '' ) . '">' . esc_html( $tab['title'] ) . '</a>';
		}
		do_action( 'press_events_settings_tabs' );
		?>
	</nav>

    <form method="post" action="options.php">

		<?php
			do_action( 'press_events_settings_section_' . $current_tab );
			submit_button();
		?>

    </form>

</div>
