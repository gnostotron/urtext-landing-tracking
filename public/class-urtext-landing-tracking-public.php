<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Urtext_Landing_Tracking
 * @subpackage Urtext_Landing_Tracking/public
 * @author     Colin MacNeill <wpsupport@urtext.ca>
 */
class Urtext_Landing_Tracking_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Standard UTM fields
	 * 
	 * @since 1.0.0
	 * 
	 */
	private $utm_keys = array(
		"utm_source",
		"utm_medium",
		"utm_campaign",
		"utm_term",
		"utm_content"
	);

	/**
	 * Bot user agent identifiers 
	 * 
	 * @since 1.0.0
	 * 
	 */
	private $bot_uas = array (
		'bot',
		'slurp',
		'crawler',
		'spider',
		'curl',
		'facebook',
		'fetch',
		'python',
		'wget',
		'monitor',
	);

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/urtext-landing-tracking-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/urtext-landing-tracking-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Update the title in RSS feeds to indicate which UTM codes are added to the feed.
	 *
	 * @since    1.0.0
	 */
	function filter_feed_title_rss($title_array) {
		if (!is_feed()) {
			return $title_array;
		}
		if ($title_array['title'] == "") {
			$title_array['title'] = "Full site";
		}

		if (isset($_GET['urtext_code_title'])) {
			$title_array['title'] .= ' - UTM Codes: ' . sanitize_text_field(wp_unslash($_GET['urtext_code_title']));
		}
		return $title_array;
	}

	/**
	 * Add UTM codes to permalinks in feed
	 *
	 * @since    1.0.0
	 */
	function filter_permalink_rss($permalink) {
		if (isset($_GET['urtext_code'])) {
			return $permalink . '?' . sanitize_text_field(urldecode(wp_unslash($_GET['urtext_code'])));
		}
		return $permalink;
	}

	/**
	 * Track the UTMs associated with site landings.
	 * 
	 * Any landing on a post, page, or the home page will be tracked.  A simple filter removes
	 * any traffic that has a bot user agent.
	 *
	 * @since    1.0.0
	 */
	public function track_landing() {
		if (!isset($_SERVER['REQUEST_METHOD']) || !isset($_SERVER['HTTP_USER_AGENT']) || $_SERVER['REQUEST_METHOD'] !== "GET" || get_option("urtext_landing_tracking_suspend", false) || is_404() || is_user_logged_in() || is_admin() || $_SERVER['HTTP_USER_AGENT'] == "") {
			return;
		}
		if (! is_home() && !is_singular()) {
			return;
		}
		foreach ( $this->bot_uas as $identifier ) {
			if ( stripos( sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])), $identifier ) !== false ) {
				return;
			}
		}

		global $wpdb;
		global $post;

		$request_date = (new DateTime())->format('Y-m-d');

		$post_id = 0;
		if (is_singular()) {
			$post_id = $post->ID;
		}
		$utm_fields = array();
		$utm_fields_string = "";

		foreach ($this->utm_keys as $key) {
			if (isset($_GET[$key]) && $_GET[$key] != "") {
				$utm_fields[$key] = sanitize_text_field(urldecode(wp_unslash($_GET[$key])));
				$utm_fields_string .= $utm_fields[$key] . "=" . sanitize_text_field(urldecode(wp_unslash($_GET[$key]))) . "&"; 
			}
		}
		foreach (get_option("urtext_landing_tracking_custom_fields", array()) as $key) {
			if (isset($_GET[$key]) && $_GET[$key] != "") {
				$utm_fields[$key] = sanitize_text_field(urldecode(wp_unslash($_GET[$key])));
				$utm_fields_string .= $utm_fields[$key] . "=" . sanitize_text_field(urldecode(wp_unslash($_GET[$key]))) . "&";  
			}
		}
		$request = "";
		$request_slug = "";
		if (isset($_SERVER['REQUEST_URI'])) {
			$request = sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])); 
			$request_parts = explode("/", $request);
			$request_slug = $request_parts[1];
			if ($request_parts[1] == "" || substr($request_parts[1], 0, 1) == "?") {
				$request_slug = "/";
			}
		}

		$request_hash = md5($request_date . $request_slug . "|" . $utm_fields_string);

		$table_name = $wpdb->prefix . 'urtext_landing_tracking_sessions';
		$wpdb->get_results($wpdb->prepare("INSERT INTO %i (request_hash, request_date, request, slug, post_id, utm_fields, request_count) VALUES (%s, %s, %s, %s, %d, %s, %d) ON DUPLICATE KEY UPDATE request_count = request_count + 1", $table_name, $request_hash, $request_date, $request, $request_slug, $post_id, serialize($utm_fields), 1)); //db call ok; no-cache ok
	}

	/**
	 * Delete any tracking data older than the age set in the settings page.  Cron job runs once a day.
	 * 
	 * @since    1.0.0
	 */

	function delete_old_tracking_records() {
		global $wpdb;
		$days = get_option("urtext_landing_tracking_data_retention_days", 365);
		if ($days <= 0) {
			return;
		}
		$table_name = $wpdb->prefix . 'urtext_landing_tracking_sessions';
		$wpdb->get_results($wpdb->prepare("DELETE FROM %i WHERE request_date < NOW() - INTERVAL %d DAY", $table_name, $days));  //db call ok; no-cache ok
	}
}
