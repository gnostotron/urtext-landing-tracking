<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://urtext.ca
 * @since             1.0.0
 * @package           Urtext_Landing_Tracking
 *
 * @wordpress-plugin
 * Plugin Name:       Ur-Text Simple Landing Tracking
 * Plugin URI:        https://urtext.ca
 * Description:       Add custom UTM codes to post URLs that track visitor landings.  Automatically post UTM-coded post URLs to social media using syndicated feeds.  Use UTM codes to track visitors through your site.  Visitor data is fully anonymized and privacy law compliant.
 * Version:           1.0.0
 * Author:            Colin MacNeill
 * Author URI:        https://urtext.ca/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       urtext-landing-tracking
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'URTEXT_LANDING_TRACKING_VERSION', '1.0.0' );
define( 'URTEXT_LANDING_TRACKING_DB_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-urtext-landing-tracking-activator.php
 */
function activate_urtext_landing_tracking() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-urtext-landing-tracking-activator.php';
	Urtext_Landing_Tracking_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-urtext-landing-tracking-deactivator.php
 */
function deactivate_urtext_landing_tracking() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-urtext-landing-tracking-deactivator.php';
	Urtext_Landing_Tracking_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_urtext_landing_tracking' );
register_deactivation_hook( __FILE__, 'deactivate_urtext_landing_tracking' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-urtext-landing-tracking.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_urtext_landing_tracking() {

	$plugin = new Urtext_Landing_Tracking();
	$plugin->run();

}
run_urtext_landing_tracking();
