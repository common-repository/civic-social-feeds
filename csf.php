<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://www.civicuk.com/
 * @since             1.0.0
 * @package           Csf
 *
 * @wordpress-plugin
 * Plugin Name:       Civic Social Feeds
 * Plugin URI:        https://www.civicuk.com/services
 * Description:       Provides Wordpress administrators a configuration page to set up credentials for various social networks in order to access API’s and gets feeds to display.
 * Version:           1.1.0
 * Author:            Civic Uk
 * Author URI:        https://www.civicuk.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       csf
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'CIVIC_SOCIAL_FEEDS_VERSION', '1.1.0' );

/**
 * The code that runs during plugin activation.
 */
function csf_activate_csf() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-csf-activator.php';
    csf_cron_activation();
	Csf_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function csf_deactivate_csf() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-csf-deactivator.php';
    csf_cron_deactivation();
	Csf_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'csf_activate_csf' );
register_deactivation_hook( __FILE__, 'csf_deactivate_csf' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-csf.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_csf() {

	$plugin = new Csf();
	$plugin->run();

}

function csf_cron_activation() {
    if( !wp_next_scheduled( 'civic_social_feeds_cron' ) ) {
        wp_schedule_event(time(), 'hourly', 'civic_social_feeds_cron');
    }
}

function csf_cron_deactivation() {
    $timestamp = wp_next_scheduled ('civic_social_feeds_cron');
    wp_unschedule_event ($timestamp, 'civic_social_feeds_cron');
}

add_filter( 'cron_schedules', 'csf_five_min_cron_trigger' );

function csf_five_min_cron_trigger( $schedules ) {
    $schedules['five_min'] = array(
        'interval' => 300,
        'display'  =>  'Every Five Minutes' ,
    );
    return $schedules;
}

add_filter( 'cron_schedules', 'csf_thirty_min_cron_trigger' );

function csf_thirty_min_cron_trigger( $schedules ) {
    $schedules['thirty_min'] = array(
        'interval' => 1800,
        'display'  =>  'Every Thirty Minutes' ,
    );
    return $schedules;
}

run_csf();
