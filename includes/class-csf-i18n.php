<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.civicuk.com/
 * @since      1.0.0
 *
 * @package    Csf
 * @subpackage Csf/includes
 */

/**
 * Define the internationalization functionality.
 *
 * @since      1.0.0
 * @package    Csf
 * @subpackage Csf/includes
 * @author     CIVIC UK <info@civicuk.com>
 */
class Csf_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'csf',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
