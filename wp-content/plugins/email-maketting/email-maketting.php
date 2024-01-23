<?php

/**
 *
 * @link              https://itsmeit.co
 * @since             1.0.0
 * @package           Email_Maketting
 *
 * @wordpress-plugin
 * Plugin Name:       Email Maketting
 * Plugin URI:        https://itsmeit.co
 * Description:       This is a description of the plugin.
 * Version:           1.0.0
 * Author:            itsmeit.co
 * Author URI:        https://itsmeit.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       email-maketting
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'EMAIL_MAKETTING_NAME', 'Email Maketting');
define( 'EMAIL_MAKETTING_VERSION', '1.0.0' );
define( 'EMAIL_MAKETTING_PLUGIN_FILE',	__FILE__ );
define( 'EMAIL_MAKETTING_PLUGIN_BASE',	plugin_basename(EMAIL_MAKETTING_PLUGIN_FILE ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-email-maketting-activator.php
 */
function activate_email_maketting() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-email-maketting-activator.php';
	Email_Maketting_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-email-maketting-deactivator.php
 */
function deactivate_email_maketting() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-email-maketting-deactivator.php';
	Email_Maketting_Deactivator::deactivate();
}

function email_marketing_uninstall() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-email-maketting-deactivator.php';
    Email_Maketting_Uninstall::uninstall();
}

register_activation_hook( __FILE__, 'activate_email_maketting' );
register_deactivation_hook( __FILE__, 'deactivate_email_maketting' );
register_uninstall_hook(__FILE__, 'email_marketing_uninstall');


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-email-maketting.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_email_maketting() {

	$plugin = new Email_Maketting();
	$plugin->run();

}
run_email_maketting();
