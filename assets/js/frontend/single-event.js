jQuery( function( $ ) {

    /**
     * Watch container width
     */
    $(window).on('resize', function () {
        $( '.single-pe_event .pe_event' ).each(function(){
            if ( $(this).width() > 880 && $.trim( $('.event-sidebar', this).html() ).length > 0 ) {
                $(this).addClass('wide-container');
            } else {
                $(this).removeClass('wide-container');
            }
        });
    }).resize();

    /**
     * Run tipTip
     */
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

    /**
     * Event page gallery
     */

} );
