<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://itsmeit.co
 * @since             1.0.0
 * @package           Prevent_Spam_Register
 *
 * @wordpress-plugin
 * Plugin Name:       Prevent Spam Register
 * Plugin URI:        https://itsmeit.co
 * Description:       Prevent spam users registering with fake emails or temporary emails
 * Version:           1.0.0
 * Author:            itsmeit.co
 * Author URI:        https://itsmeit.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       prevent-spam-register
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
define( 'PREVENT_SPAM_REGISTER_VERSION', '1.0.0' );
define( 'PREVENT_SPAM_REGISTER_NAME', 'Prevent Spam Register' );
define( 'PREVENT_SPAM_REGISTER_PLUGIN_FILE', __FILE__ );
define( 'PREVENT_SPAM_REGISTER_PLUGIN_BASE', plugin_basename(PREVENT_SPAM_REGISTER_PLUGIN_FILE ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-prevent-spam-register-activator.php
 */
function activate_prevent_spam_register() {
	require_once plugin_dir_path( PREVENT_SPAM_REGISTER_PLUGIN_FILE ) . 'includes/class-prevent-spam-register-activator.php';
	Prevent_Spam_Register_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-prevent-spam-register-deactivator.php
 */
function deactivate_prevent_spam_register() {
	require_once plugin_dir_path( PREVENT_SPAM_REGISTER_PLUGIN_FILE ) . 'includes/class-prevent-spam-register-deactivator.php';
	Prevent_Spam_Register_Deactivator::deactivate();
}

register_activation_hook( PREVENT_SPAM_REGISTER_PLUGIN_FILE, 'activate_prevent_spam_register' );
register_deactivation_hook( PREVENT_SPAM_REGISTER_PLUGIN_FILE, 'deactivate_prevent_spam_register' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( PREVENT_SPAM_REGISTER_PLUGIN_FILE ) . 'includes/class-prevent-spam-register.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_prevent_spam_register() {

	$plugin = new Prevent_Spam_Register();
	$plugin->run();

}
run_prevent_spam_register();
