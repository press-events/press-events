<?php
/**
 * Twenty Seventeen support.
 *
 * @since 1.0.0
 * @package PressEvents/ThemeSupport
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BM_PE_Twenty_Seventeen {

	/**
	 * Theme Constructor
	 */
	public function __construct() {
        add_filter( 'body_class', array( $this, 'body_classes' ), 100 );
        add_filter( 'wp_enqueue_scripts', array( $this, 'head_inline_style' ), 100 );
	}

    /**
     * Filter body classes on event pages
     */
    public function body_classes( $classes ) {
        $is_event_view = is_BM_Press_Events();

        /**
         * Remove sidebar from event views
         */
        $remove_sidebar_class = apply_filters( 'press_events_twenty_seventeen_remove_sidebar_class', $is_event_view, $classes );
        if ( $remove_sidebar_class && $index = array_search( 'has-sidebar', $classes ) ) {
            unset( $classes[$index] );
        }

        /**
         * Convert page to single column
         */
        $convert_to_one_column = apply_filters( 'press_events_twenty_seventeen_convert_to_one_column', $is_event_view, $classes );
 		if ( $convert_to_one_column && $index = array_search( 'page-two-column', $classes ) ) {
 			$classes[$index] = 'page-one-column';
 		}

        return $classes;
    }

	/**
	 * Add some css to the head
	 */
	public function head_inline_style() {
		if ( is_bm_pe_event() ) {
			$css = "body.press-events .single-featured-image-header {
			    display: none;
			}";

			wp_register_style( 'press-events-twenty-seventeen-inline', false, array( 'press-events-general' )  );
		    wp_enqueue_style( 'press-events-twenty-seventeen-inline' );
		    wp_add_inline_style( 'press-events-twenty-seventeen-inline', $css );
		}
	}

}
new BM_PE_Twenty_Seventeen();
