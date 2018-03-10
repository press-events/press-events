<?php
/**
 * Handle frontend styles and scripts
 *
 * @version 1.0.0
 * @package PressEvents/Classes
 * @author Burn Media Ltd
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BM_PE_Frontend_Assets Class.
 */
class BM_PE_Frontend_Assets {

	/**
	 * Contains an array of script handles registered by Press Events.
	 * @var array
	 */
	private static $scripts = array();

	/**
	 * Contains an array of script handles registered by Press Events.
	 * @var array
	 */
	private static $styles = array();

	/**
	 * Contains an array of script handles localized by Press Events.
	 * @var array
	 */
	private static $wp_localize_scripts = array();

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		add_action( 'wp_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
		add_action( 'wp_print_footer_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
	}

	/**
	 * Get styles for the frontend.
	 *
	 * @return array
	 */
	public static function get_styles() {
		return apply_filters( 'press_events_enqueue_styles', array(
			'magnific-popup' => array(
				'src' => self::get_asset_url( 'assets/css/magnific-popup/magnific-popup.css' ),
				'deps' => array(),
				'version' => BM_PE_VERSION,
				'media' => 'all',
				'has_rtl' => false,
			),
			'press-events-general' => array(
				'src' => self::get_asset_url( 'assets/css/press-events.css' ),
				'deps' => '',
				'version' => BM_PE_VERSION,
				'media' => 'all',
				'has_rtl' => true,
			),
			'press-events-menu' => array(
				'src' => self::get_asset_url( 'assets/css/menu.css' ),
				'deps' => '',
				'version' => BM_PE_VERSION,
				'media' => 'all',
				'has_rtl' => true,
			),
		) );
	}

	/**
	 * Register/queue frontend scripts.
	 */
	public static function load_scripts() {
		global $post;

		if ( ! did_action( 'before_press_events_init' ) ) {
			return;
		}

		self::register_scripts();
		self::register_styles();

		if ( is_bm_pe_event_archive() ) {
			self::enqueue_script( 'chosen' );
			self::enqueue_script( 'pe-archive-event' );
		}

		if ( is_bm_pe_event() ) {
			self::enqueue_style( 'magnific-popup' );
			self::enqueue_script( 'pe-single-event' );
		}

		// Global frontend scripts
		self::enqueue_script( 'press-events' );

		// CSS Styles
		if ( $enqueue_styles = self::get_styles() ) {
			foreach ( $enqueue_styles as $handle => $args ) {
				$args['has_rtl'] = isset( $args['has_rtl'] ) ? isset( $args['has_rtl'] ) : false;

				self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'], $args['has_rtl'] );
			}
		}
	}

	/**
	 * Localize a PE script once.
	 *
	 * @access private
	 * @since 1.0.0
	 * @param string $handle
	 */
	private static function localize_script( $handle ) {
		if ( ! in_array( $handle, self::$wp_localize_scripts ) && wp_script_is( $handle ) && ( $data = self::get_script_data( $handle ) ) ) {
			$name = str_replace( '-', '_', $handle ) . '_params';
			self::$wp_localize_scripts[] = $handle;
			wp_localize_script( $handle, $name, apply_filters( $name, $data ) );
		}
	}

	/**
	 * Register all PE scripts.
	 */
	private static function register_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$register_scripts = array(
			'magnific-popup' => array(
				'src' => self::get_asset_url( 'assets/js/magnific-popup/jquery.magnific-popup' . $suffix . '.js' ),
				'deps' => array(),
				'version' => '4.1.1',
			),
			'chosen' => array(
				'src' => self::get_asset_url( 'assets/js/chosen/chosen.jquery' . $suffix . '.js' ),
				'deps' => array(),
				'version' => '1.8.3',
			),
			'jquery-tiptip' => array(
				'src' => self::get_asset_url( 'assets/js/jquery-tiptip/jquery.tipTip'. $suffix .'.js' ),
				'deps' => array( 'jquery' ) ,
				'version' => '1.3',
			),
			'pe-archive-event' => array(
				'src' => self::get_asset_url( 'assets/js/frontend/press-events-archive'. $suffix .'.js' ),
				'deps' => array( 'jquery' ),
				'version' => BM_PE_VERSION
			),
			'pe-single-event' => array(
				'src' => self::get_asset_url( 'assets/js/frontend/single-event'. $suffix .'.js' ),
				'deps' => array( 'jquery', 'jquery-tiptip' ),
				'version' => BM_PE_VERSION,
			),
			'press-events' => array(
				'src' => self::get_asset_url( 'assets/js/frontend/press-events'. $suffix .'.js' ),
				'deps' => array( 'jquery' ),
				'version' => BM_PE_VERSION
			),
		);

		foreach ( $register_scripts as $name => $props ) {
			self::register_script( $name, $props['src'], $props['deps'], $props['version'] );
		}
	}

	/**
	 * Register all PE styles.
	 */
	private static function register_styles() {
		$register_styles = array(
			// enquee dep styles here
		);

		foreach ( $register_styles as $name => $props ) {
			self::register_style( $name, $props['src'], $props['deps'], $props['version'], 'all', $props['has_rtl'] );
		}
	}

	/**
	 * Register a script for use.
	 *
	 * @uses   wp_register_script()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  boolean  $in_footer
	 */
	private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = BM_PE_VERSION, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}

	/**
	 * Register a style for use.
	 *
	 * @uses   wp_register_style()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  string   $media
	 * @param  boolean  $has_rtl
	 */
	private static function register_style( $handle, $path, $deps = array(), $version = BM_PE_VERSION, $media = 'all', $has_rtl = false ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $path, $deps, $version, $media );
		if ( $has_rtl ) {
			wp_style_add_data( $handle, 'rtl', 'replace' );
		}
	}

	/**
	 * Return asset URL.
	 *
	 * @param string $path
	 * @return string
	 */
	private static function get_asset_url( $path ) {
		return apply_filters( 'press_events_get_asset_url', plugins_url( $path, BM_PE_PLUGIN_FILE ), $path );
	}

	/**
	 * Register and enqueue a script for use.
	 *
	 * @uses   wp_enqueue_script()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  boolean  $in_footer
	 */
	private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = BM_PE_VERSION, $in_footer = true ) {
		if ( ! in_array( $handle, self::$scripts ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}


	/**
	 * Register and enqueue a styles for use.
	 *
	 * @uses   wp_enqueue_style()
	 * @access private
	 * @param  string   $handle
	 * @param  string   $path
	 * @param  string[] $deps
	 * @param  string   $version
	 * @param  string   $media
	 * @param  boolean  $has_rtl
	 */
	private static function enqueue_style( $handle, $path = '', $deps = array(), $version = BM_PE_VERSION, $media = 'all', $has_rtl = false ) {
		if ( ! in_array( $handle, self::$styles ) && $path ) {
			self::register_style( $handle, $path, $deps, $version, $media, $has_rtl );
		}
		wp_enqueue_style( $handle );
	}

	/**
	 * Return data for script handles.
	 * @access private
	 * @param  string $handle
	 * @return array|bool
	 */
	private static function get_script_data( $handle ) {
		switch ( $handle ) {
			case 'pe-archive-event' :
				$params = array(
					'ajax_url' => BM_Press_Events()->ajax_url(),
					'pe_ajax_url' => BM_PE_Ajax::get_endpoint( "%%endpoint%%" ),
					'ajax_archive' => bm_pe_get_option( 'ajax-archive', 'pe-general-events', 'on' )
				);
				break;

			default:
				$params = false;
		}

		return apply_filters( 'press_events_get_script_data', $params, $handle );
	}

	/**
	 * Localize scripts only when enqueued.
	 */
	public static function localize_printed_scripts() {
		foreach ( self::$scripts as $handle ) {
			self::localize_script( $handle );
		}
	}

}

BM_PE_Frontend_Assets::init();
