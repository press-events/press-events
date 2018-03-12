<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @package PressEvents/Classes
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * @since 1.0.0
 */
final class BM_Press_Events {

    /**
     * Press Events version.
     *
     * @var string
     */
    public $version = '1.0.6';

    /**
	 * Countries instance.
	 *
	 * @var BM_PE_Countries
	 */
	public $countries = null;

    /**
	 * Date i18n instance.
	 *
	 * @var BM_PE_Date_i18n
	 */
	public $date_i18n = null;

    /**
	 * Query instance.
	 *
	 * @var BM_PE_Query
	 */
	public $query = null;

	/**
	 * The single instance of the class.
	 *
	 * @var BM_Press_Events
	 * @since 1.0.0
	 */
	protected static $_instance = null;

    /**
	 * Main Press Events Instance.
	 *
	 * @since 1.0.0
	 * @return BM_Press_Events - Main instance.
	 */
    public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

    /**
	 * Throw error on object clone.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'press-events' ), '1.0.0' );
	}

    /**
	 * Disable unserializing of the class.
	 *
     * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'press-events' ), '1.0.0' );
	}

    /**
	 * Press Events Constructor.
	 */
	public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();

		do_action( 'press_events_loaded' );
	}

    /**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
        register_activation_hook( BM_PE_PLUGIN_FILE, array( 'BM_PE_Install', 'init' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
        add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
		add_action( 'init', array( $this, 'init' ), 0 );
	}

    /**
     * Define PE Constants.
     */
    private function define_constants() {
        $this->define( 'BM_PE_ABSPATH', dirname( BM_PE_PLUGIN_FILE ) . '/' );
        $this->define( 'BM_PE_PLUGIN_BASENAME', plugin_basename( BM_PE_PLUGIN_FILE ) );
        $this->define( 'BM_PE_VERSION', $this->version );
		$this->define( 'BM_PE_TEMPLATE_DEBUG_MODE', false );
    }

    /**
     * Define constant if not already set.
     *
     * @param string $name
     * @param string|bool $value
     */
    private function define( $name, $value ) {
    	if ( !defined( $name ) ) {
    		define( $name, $value );
    	}
    }

    /**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'BM_PE_DOING_AJAX' );
			case 'frontend':
				return ( ! is_admin() || defined( 'BM_PE_DOING_AJAX' ) );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
        /**
		 * Class autoloader.
		 */
        include_once( BM_PE_ABSPATH . 'includes/class-bm-pe-autoloader.php' );

        /**
		 * Abstract classes.
		 */
        include_once( BM_PE_ABSPATH . 'includes/abstracts/abstract-bm-pe-data.php' );
		include_once( BM_PE_ABSPATH . 'includes/abstracts/abstract-bm-pe-event-location.php' ); // Event Locations
		include_once( BM_PE_ABSPATH . 'includes/abstracts/abstract-bm-pe-event-organiser.php' ); // Event Organisers
		include_once( BM_PE_ABSPATH . 'includes/abstracts/abstract-bm-pe-event.php' ); // Events
        include_once( BM_PE_ABSPATH . 'includes/abstracts/abstract-bm-pe-object-query.php' );

        /**
		 * Core classes.
		 */
 		include_once( BM_PE_ABSPATH . 'includes/class-bm-pe-admin-bar.php' );
		include_once( BM_PE_ABSPATH . 'includes/class-bm-pe-ajax.php' );
		include_once( BM_PE_ABSPATH . 'includes/class-bm-pe-calendar.php' );
        include_once( BM_PE_ABSPATH . 'includes/class-bm-pe-countries.php' );
        include_once( BM_PE_ABSPATH . 'includes/class-bm-pe-date-i18n.php' );
        include_once( BM_PE_ABSPATH . 'includes/class-bm-pe-datetime.php' );
        include_once( BM_PE_ABSPATH . 'includes/class-bm-pe-event-query.php' ); // Event query.
        include_once( BM_PE_ABSPATH . 'includes/class-bm-pe-i18n.php' );
        include_once( BM_PE_ABSPATH . 'includes/class-bm-pe-install.php' );
        include_once( BM_PE_ABSPATH . 'includes/class-bm-pe-post-types.php' ); // Registers post types.
        include_once( BM_PE_ABSPATH . 'includes/class-bm-pe-query.php' );
        include_once( BM_PE_ABSPATH . 'includes/bm-pe-template-hooks.php' );

        /**
         * Functions
         */
        include_once( BM_PE_ABSPATH . 'includes/bm-pe-core-functions.php' );

		if ( $this->is_request( 'admin' ) ) {
			include_once( BM_PE_ABSPATH . 'includes/admin/class-bm-pe-admin.php' );
		}

		if ( $this->is_request( 'frontend' ) ) {
            include_once( BM_PE_ABSPATH . 'includes/class-bm-pe-assets.php' ); // Frontend Scripts.
			include_once( BM_PE_ABSPATH . 'includes/class-bm-pe-template-loader.php' ); // Template Loader.
		}

        $this->theme_support_includes();
	}

    /**
	 * Function used to Init Template Functions - This makes them pluggable by plugins and themes.
	 */
	public function include_template_functions() {
        include_once( BM_PE_ABSPATH . 'includes/bm-pe-template-functions.php' );
	}

    /**
	 * Include classes for theme support.
	 *
	 * @since 1.0.0
	 */
    private function theme_support_includes() {
        $theme_support = array( 'twentyseventeen' );

        if ( $this->is_active_theme( $theme_support ) ) {
			switch ( get_template() ) {
				case 'twentyseventeen':
					include_once( BM_PE_ABSPATH . 'includes/theme-support/class-bm-pe-twenty-seventeen.php' );
					break;
			}
		}
	}

    /**
	 * Check the active theme.
	 *
	 * @since  1.0.0
	 * @param  string $theme Theme slug to check.
	 * @return bool
	 */
    private function is_active_theme( $theme ) {
        return is_array( $theme ) ? in_array( get_template(), $theme, true ) : get_template() === $theme;
    }

    /**
	 * Ensure theme and server variable compatibility and setup image sizes.
	 */
	public function setup_environment() {
		$this->add_thumbnail_support();
	}

	/**
	 * Ensure post thumbnail support is turned on.
	 */
	private function add_thumbnail_support() {
		if ( ! current_theme_supports( 'post-thumbnails' ) ) {
			add_theme_support( 'post-thumbnails' );
		}
		add_post_type_support( 'press_event', 'thumbnail' );
	}

    /**
	 * Init Press Events when WordPress Initialises.
	 */
	public function init() {
		// Before init action.
		do_action( 'before_press_events_init' );

		// Set up localisation.
		$this->load_plugin_textdomain();

        $this->countries = new BM_PE_Countries(); // Countries class.
        $this->date_i18n = new BM_PE_Date_i18n(); // Date i18n class.
        $this->query = new BM_PE_Query(); // BM_PE_Query

		// Init action.
		do_action( 'press_events_init' );
	}

    /**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Press_Events_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 */
	private function load_plugin_textdomain() {
		$plugin_i18n = new BM_PE_i18n();
		$plugin_i18n->set_domain('press-events');

        add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

    /**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', BM_PE_PLUGIN_FILE ) );
	}

    /**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( BM_PE_PLUGIN_FILE ) );
	}

    /**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'press_events_template_path', 'press-events/' );
	}

	/**
	 * Get Ajax URL.
	 *
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php' );
	}

}
