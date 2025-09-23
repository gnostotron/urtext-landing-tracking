<?php

/**
 *
 * @link       https://urtext.ca
 * @since      1.0.0
 *
 * @package    Urtext_Landing_Tracking
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
delete_option("urtext_landing_tracking_codes");
delete_option("urtext_landing_tracking_custom_fields");
delete_option("urtext_landing_tracking_db_version");

global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}urtext_landing_tracking_sessions");
