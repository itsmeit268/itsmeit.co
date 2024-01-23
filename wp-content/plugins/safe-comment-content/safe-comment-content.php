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
 * @package           Safe_Comment_Content
 *
 * @wordpress-plugin
 * Plugin Name:       Safe Comment Content
 * Plugin URI:        https://itsmeit.co
 * Description:       Encode strings or words that are sensitive, vulgar, or violate guidelines within the comments.
 * Version:           1.0.0
 * Author:            itsmeit | Technology Blogs
 * Author URI:        https://itsmeit.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       safe-comment-content
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SAFE_COMMENT_CONTENT_VERSION', '1.0.0' );
define( 'SAFE_COMMENT_CONTENT_ENDPOINT', 'pr_go' );
define( 'SAFE_COMMENT_CONTENT_BASE', plugin_basename(__FILE__ ));
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-safe-comment-content-activator.php
 */
function activate_safe_comment_content() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-safe-comment-content-activator.php';
	Safe_Comment_Content_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-safe-comment-content-deactivator.php
 */
function deactivate_safe_comment_content() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-safe-comment-content-deactivator.php';
	Safe_Comment_Content_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_safe_comment_content' );
register_deactivation_hook( __FILE__, 'deactivate_safe_comment_content' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-safe-comment-content.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 */
function run_safe_comment_content() {

	$plugin = new Safe_Comment_Content();
	$plugin->run();

}
run_safe_comment_content();
