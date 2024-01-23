<?php

/**
 * @link       https://itsmeit.co
 * @package    Email_Maketting
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$marketing_table = $wpdb->prefix . 'email_marketing';
$subscribe_table = $wpdb->prefix . 'email_subscribe';
$contact_table   = $wpdb->prefix . 'email_contact';

// Xóa bảng nếu tồn tại
if ($wpdb->get_var("SHOW TABLES LIKE '$marketing_table'") == $marketing_table) {
    $wpdb->query("DROP TABLE IF EXISTS $marketing_table");
}
if ($wpdb->get_var("SHOW TABLES LIKE '$subscribe_table'") == $subscribe_table) {
    $wpdb->query("DROP TABLE IF EXISTS $subscribe_table");
}
if ($wpdb->get_var("SHOW TABLES LIKE '$contact_table'") == $contact_table) {
    $wpdb->query("DROP TABLE IF EXISTS $contact_table");
}