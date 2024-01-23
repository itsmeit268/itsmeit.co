<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://itsmeit.co
 * @since      1.0.0
 *
 * @package    Prevent_Spam_Register
 * @subpackage Prevent_Spam_Register/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Prevent_Spam_Register
 * @subpackage Prevent_Spam_Register/includes
 * @author     itsmeit.co <itsmeit.biz@gmail.com>
 */
class Prevent_Spam_Register_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'prevent-spam-register',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
