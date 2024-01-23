<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Email_Maketting
 * @subpackage Email_Maketting/includes
 * @author     itsmeit.co <buivanloi.2010@gmail.com>
 */
class Email_Maketting_Activator {

	public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $marketing_table = $wpdb->prefix . 'email_marketing';
        $subscribe_table = $wpdb->prefix . 'email_subscribe';
        $contact_table = $wpdb->prefix . 'email_contact';

        if ($wpdb->get_var("SHOW TABLES LIKE '$marketing_table'") != $marketing_table) {

            $sql = "CREATE TABLE $marketing_table (
                id INT(11) NOT NULL AUTO_INCREMENT,
                name VARCHAR(50) NOT NULL,
                email VARCHAR(100) NOT NULL,
                create_date DATETIME NOT NULL,
                link VARCHAR(255) NOT NULL,
                send_count INT(11) UNSIGNED NOT NULL,
                allow TINYINT(1) NOT NULL DEFAULT 0,
                browser VARCHAR(255) NOT NULL,
                post_id INT(11) NOT NULL,
                category_id INT(11) NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate";


            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

        if ($wpdb->get_var("SHOW TABLES LIKE '$subscribe_table'") != $subscribe_table) {

            $sql = "CREATE TABLE $subscribe_table (
                id INT(11) NOT NULL AUTO_INCREMENT,
                name VARCHAR(50) NOT NULL,
                email VARCHAR(100) NOT NULL,
                post_url VARCHAR(100) NOT NULL,
                verification_code VARCHAR(100) NOT NULL,
                create_date DATETIME NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

        if ($wpdb->get_var("SHOW TABLES LIKE '$contact_table'") != $contact_table) {

            $sql = "CREATE TABLE $contact_table (
                id INT (11) AUTO_INCREMENT,
                name VARCHAR(50) NOT NULL,
                subject VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                email VARCHAR(100) NOT NULL,
                sent_count INT DEFAULT 0,
                create_date DATETIME NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
	}

}
