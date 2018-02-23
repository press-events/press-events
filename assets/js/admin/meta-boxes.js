(function($) {
	'use strict';

	/**
	 * Tabbed meta box
	 */
	var pe_meta_boxes = {

	    /**
	     * Initialise tabbed meta box actions
	     */
	    init: function() {

	        $( '.tabbed-meta-box-wrapper' )
	            .on( 'click', '.press-tabs li a', this.tabToggle );

	    },

	    /**
	     * Handle the tab toggle
	     */
	    tabToggle: function( event ) {

	        event.preventDefault();

	        var target = $( this ).attr( 'href' );

	        $( '.tabbed-meta-box-wrapper .press-panel > div' ).removeClass( 'active' );
	        $( '.tabbed-meta-box-wrapper .press-panel > div'+ target ).addClass( 'active' );

	        $( '.tabbed-meta-box-wrapper .press-tabs li.active' ).removeClass( 'active' );
	        $( this ).parent( 'li' ).addClass( 'active' );

	    }

	};

	pe_meta_boxes.init();

})( jQuery );
