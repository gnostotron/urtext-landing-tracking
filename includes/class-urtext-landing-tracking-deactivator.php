<?php

/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    Urtext_Landing_Tracking
 * @subpackage Urtext_Landing_Tracking/includes
 * @author     Colin MacNeill <wpsupport@urtext.ca>
 */
class Urtext_Landing_Tracking_Deactivator {

	/**
	 * Deactivation functions 
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		require_once plugin_dir_path( __FILE__ ) . 'class-urtext-landing-tracking.php';
		Urtext_Landing_Tracking::remove_cron_jobs();
	}

}
