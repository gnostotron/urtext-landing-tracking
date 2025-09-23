<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Urtext_Landing_Tracking
 * @subpackage Urtext_Landing_Tracking/includes
 * @author     Colin MacNeill <wpsupport@urtext.ca>
 */
class Urtext_Landing_Tracking {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Urtext_Landing_Tracking_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'URTEXT_LANDING_TRACKING_VERSION' ) ) {
			$this->version = URTEXT_LANDING_TRACKING_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'urtext-landing-tracking';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Urtext_Landing_Tracking_Loader. Orchestrates the hooks of the plugin.
	 * - Urtext_Landing_Tracking_Admin. Defines all hooks for the admin area.
	 * - Urtext_Landing_Tracking_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-urtext-landing-tracking-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-urtext-landing-tracking-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-urtext-landing-tracking-public.php';

		$this->loader = new Urtext_Landing_Tracking_Loader();

	}

	/**
	 * Set up cron jobs.
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	public static function setup_cron_jobs() {
		if (!wp_next_scheduled('urtext_landing_tracking_cleanup_event')) {
			wp_schedule_event(time(), 'daily', 'urtext_landing_tracking_cleanup_event');
		}
	}

	/**
	 * Remove cron jobs.
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	public static function remove_cron_jobs() {
		wp_clear_scheduled_hook(('urtext_landing_tracking_cleanup_event'));
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Urtext_Landing_Tracking_Admin( $this->get_plugin_name(), $this->get_version() );

		//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings', PHP_INT_MAX );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'update_menu', PHP_INT_MAX );
		$this->loader->add_action( 'wp_before_admin_bar_render', $plugin_admin, 'update_menu', PHP_INT_MAX );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'add_tracking_url_button', PHP_INT_MAX );
		$this->loader->add_action( 'admin_post_urtext_landing_tracking_form', $plugin_admin, 'process_form');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Urtext_Landing_Tracking_Public( $this->get_plugin_name(), $this->get_version() );

		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'template_redirect', $plugin_public, 'track_landing', 20);
		$this->loader->add_filter( 'the_permalink_rss', $plugin_public, 'filter_permalink_rss');
		$this->loader->add_filter( 'document_title_parts', $plugin_public, 'filter_feed_title_rss', 
		PHP_INT_MAX);
		$this->loader->add_action( 'urtext_landing_tracking_cleanup_event', $plugin_public, 'delete_old_tracking_records');
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Urtext_Landing_Tracking_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
