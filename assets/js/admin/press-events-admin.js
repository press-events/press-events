/* global pe_admin_vars */
(function ( $ ) {

	if ( 'undefined' !== typeof pe_admin_vars.date_vars && pe_admin_vars.date_vars != null ) {
		$.fn.datepicker.dates.en = pe_admin_vars.date_vars;
	}

	// Run tipTip
	function runTipTip() {
		// Remove any lingering tooltips
		$( '#tiptip_holder' ).removeAttr( 'style' );
		$( '#tiptip_arrow' ).removeAttr( 'style' );
		$( '.press-tip' ).tipTip({
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200
		});
	}

	runTipTip();

	// Color picker
	$('.press-color-picker').wpColorPicker();

}( jQuery ));
