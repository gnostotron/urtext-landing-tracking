<?php

/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    Urtext_Landing_Tracking
 * @subpackage Urtext_Landing_Tracking/includes
 * @author     Colin MacNeill <wpsupport@urtext.ca>
 */
class Urtext_Landing_Tracking_Activator {

	/**
	 * Activation  functions
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::update_database();
		require_once plugin_dir_path( __FILE__ ) . 'class-urtext-landing-tracking.php';
		Urtext_Landing_Tracking::setup_cron_jobs();
	}

	/**
	 * Update the database layouts
	 *
	 * @since	1.0.0
	 */
	public static function update_database() {
		if (version_compare(get_option( 'urtext_landing_tracking_db_version', '0.0.0' ), URTEXT_LANDING_TRACKING_DB_VERSION, ">=")) {
			return; 
		}

		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$table_name = $wpdb->prefix . 'urtext_landing_tracking_sessions';
		$sql = "CREATE TABLE $table_name (
			request_hash varchar(32) NOT NULL,
			request_date date DEFAULT NOW() NOT NULL,
			request varchar(255) NOT NULL,
			slug varchar(128) NOT NULL,
			post_id int(11) DEFAULT NULL,
			utm_fields text DEFAULT NULL,
			request_count int(11) DEFAULT 0,
			PRIMARY KEY(request_hash)
		);";
		dbDelta( $sql );

		add_option( 'urtext_landing_tracking_db_version', URTEXT_LANDING_TRACKING_DB_VERSION );
	}
	
}
