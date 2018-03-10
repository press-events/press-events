<?php
/**
 * Template Loader
 *
 * @version 1.0.0
 * @package PressEvents/Classes
 * @author Burn Media Ltd
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class BM_PE_Template_Loader {

    /**
     * Has wp_head been triggered.
     */
    public static $wp_head = false;

    /**
	 * Hook in methods.
	 */
	public static function init() {
		add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );

        add_action( 'wp_head', array( __CLASS__, 'blank_post' ), 100 );
        add_action( 'wp_head', array( __CLASS__, 'wp_head_finished' ), 999 );

        // don't query the database for the blank post
		wp_cache_set( self::blank_post_object()->ID, self::blank_post_object(), 'posts' );
		wp_cache_set( self::blank_post_object()->ID, array( true ), 'post_meta' );


	}

    /**
	 * Load a page template.
	 *
	 * @param mixed $template
	 * @return string
	 */
	public static function template_loader( $template ) {
		if ( is_BM_Press_Events() ) {
            $template = locate_template( bm_pe_get_option( 'event-template', 'pe-general', 'page.php' ) );
            add_action( 'loop_start', array( __CLASS__, 'event_template_loader' ) );
        }

		return $template;
	}

    /**
	 * Load an event template.
	 *
	 * @param mixed $template
	 * @return string
	 */
	public static function event_template_loader( $query ) {
        do_action( 'press_events_filter_the_page_title' );

        if ( $query->is_main_query() && self::$wp_head ) {
            // Create global blank post
    		add_action( 'the_post', array( __CLASS__, 'create_blank_post' ) );
            // Hide the post edit link
            add_filter( 'edit_post_link', array( __CLASS__, 'hide_edit_link' ), 5, 3 );
    		// Load event template into the_content
    		add_filter( 'the_content', array( __CLASS__, 'load_event_template' ) );
            // force a blank comments template
			add_filter( 'comments_template', array( __CLASS__, 'comments_template' ) );
    		// only do this once
    		remove_action( 'loop_start', array( __CLASS__, 'event_template_loader' ) );
        }
	}

	/**
	 * Load event templates
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public static function load_event_template( $contents = '' ) {
        remove_filter( 'the_content', array( __CLASS__, 'load_event_template' ) );

        self::restoreQuery();

        ob_start();

        if ( is_bm_pe_event() ) {
            bm_pe_get_template( 'single-event.php' );
        } elseif ( is_bm_pe_event_archive() || is_bm_pe_event_taxonomy() ) {
            do_action( 'press_events_before_get_calendar' );
            bm_pe_get_template( 'archive-event.php' );
        }

        $contents = ob_get_clean();

        // make sure the loop ends after our template is included
        if ( ! is_404() ) {
            self::end_event_query();
        }

        return $contents;
	}

    /**
     * Restore the original query after spoofing it.
	 */
	public static function restoreQuery() {
        global $wp_query;

        if ( ! isset( $wp_query->spoofed ) || ! $wp_query->spoofed ) {
        	return;
        }

        array_pop( $wp_query->posts );

        $wp_query->post_count = count( $wp_query->posts );

        if ( $wp_query->post_count > 0 ) {
        	$wp_query->rewind_posts();
        	wp_reset_postdata();
        } elseif ( 0 === $wp_query->post_count ) {
        	$wp_query->current_post = -1;
        	unset( $wp_query->post );
        }

        unset( $wp_query->spoofed );
	}

    /**
     * Blank post
     */
    private static function blank_post_object() {
        return (object) array(
            'ID' => 0,
            'post_status' => 'draft',
            'post_author' => 0,
            'post_parent' => 0,
            'post_type' => 'page',
            'post_date' => 0,
            'post_date_gmt' => 0,
            'post_modified' => 0,
            'post_modified_gmt' => 0,
            'post_content' => '',
            'post_title' => '',
            'post_excerpt' => '',
            'post_content_filtered' => '',
            'post_mime_type' => '',
            'post_password' => '',
            'post_name' => '',
            'guid' => '',
            'menu_order' => 0,
            'pinged' => '',
            'to_ping' => '',
            'ping_status' => '',
            'comment_status' => 'closed',
            'comment_count' => 0,
            'is_404' => false,
            'is_page' => false,
            'is_single' => false,
            'is_archive' => false,
            'is_tax' => false,
        );
    }

    /**
     * Setup the blank post
     */
    public static function blank_post() {
        if ( is_single() && post_password_required() || is_feed() ) {
            return;
		}

        global $wp_query;

        if ( $wp_query->is_main_query() && is_bm_pe_event_page() ) {
    		$GLOBALS['post'] = self::blank_post_object();
    		$wp_query->posts[] = self::blank_post_object();
    		$wp_query->post_count = count( $wp_query->posts );
    	    $wp_query->spoofed = true;
    		$wp_query->rewind_posts();
    	}
    }

    /**
	 * Query is complete: stop the loop from repeating.
	 */
	private static function end_event_query() {
		global $wp_query;

		$wp_query->current_post = -1;
		$wp_query->post_count = 0;
	}

    /**
     * Stores true if wp_head has finished
     */
    public static function wp_head_finished() {
        self::$wp_head = true;
 	}

    /**
     * Create blank global post just once
     */
    public static function create_blank_post() {
        $GLOBALS['post'] = self::blank_post_object();
        remove_action( 'the_post', array( __CLASS__, 'create_blank_post' ) );
    }

    /**
     * Hide the blank post edit link
     */
    public static function hide_edit_link( $link, $post_ID, $text ) {
        if ( $post_ID == 0 ) {
            return null;
        }

        return $link;
    }

    /**
     * Show the custom event comment template
     */
    public static function comments_template( $template ) {
        global $event;

        if ( ! $event->comments_open() ) {
            return bm_pe_locate_template( 'single-event/no-comments.php' );
        }
    }

}

BM_PE_Template_Loader::init();
