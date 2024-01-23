<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Foxiz_Upgrade_Db' ) ) {
	class Foxiz_Upgrade_Db {

		protected static $instance = null;
		private $wpdb;
		const TABLE_NAME = 'rb_personalize';

		static function get_instance() {

			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {

			self::$instance = $this;
			global $wpdb;
			$this->wpdb = $wpdb;

			$this->create_db_table();
		}

		public function get_table_name() {

			return $this->wpdb->prefix . self::TABLE_NAME;
		}

		function create_db_table() {

			$table_name = $this->get_table_name();
			$collate    = $this->wpdb->get_charset_collate();

			if ( $this->wpdb->get_var( $this->wpdb->prepare( 'SHOW TABLES LIKE %s', $this->wpdb->esc_like( $table_name ) ) ) === $table_name ) {
				return false;
			}

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			$sql = "CREATE TABLE {$table_name} (
					id bigint(11) not null AUTO_INCREMENT,
					identifier varchar(50),
					user_id bigint(11) default 0,
					ip varchar (50),
					action_key varchar(50),
					action_id int(11),
					action_value varchar(50) default null,
					created datetime not null default '0000-00-00 00:00:00',			
					PRIMARY KEY (id)
			) {$collate}";

			dbDelta( $sql );

			/** update data */
			$this->upgrade_follow_categories();
			$this->upgrade_follow_writers();
			$this->upgrade_bookmarks();

			return true;
		}

		/**
		 * @return false
		 */
		function upgrade_follow_categories() {

			$table_name = $this->get_table_name();
			$old_key    = 'rb_user_category_data';
			$action_key = 'follow_cat';

			$prepare = $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->usermeta} WHERE meta_key = %s ORDER BY user_id", $old_key );

			$subs = $this->wpdb->get_results( $prepare );

			if ( empty( $subs ) || is_wp_error( $subs ) ) {
				return false;
			}

			$current = current_time( 'mysql' );
			foreach ( $subs as $meta ) {
				$categories = maybe_unserialize( $meta->meta_value );
				if ( is_array( $categories ) ) {
					foreach ( $categories as $id ) {
						if ( $this->wpdb->get_var( $this->wpdb->prepare( "SELECT COUNT(*) FROM {$table_name} WHERE user_id = %d AND action_key = %s AND action_id = %d", $meta->user_id, $action_key, $id ) ) ) {
							continue;
						}

						$this->wpdb->query( $this->wpdb->prepare( "INSERT INTO {$table_name} (user_id, action_key, action_id, created) VALUES (%d, %s, %d, %s)", $meta->user_id, $action_key, $id, $current ) );
					}
				}
			}

			$this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->wpdb->usermeta} WHERE meta_key = %s", $old_key ) );
		}

		/**
		 * @return false
		 */
		function upgrade_follow_writers() {

			$table_name = $this->get_table_name();
			$old_key    = 'rb_user_author_data';
			$action_key = 'follow_auth';

			$prepare = $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->usermeta} WHERE meta_key = %s ORDER BY user_id", $old_key );

			$subs = $this->wpdb->get_results( $prepare );

			if ( empty( $subs ) || is_wp_error( $subs ) ) {
				return false;
			}

			$current = current_time( 'mysql' );
			foreach ( $subs as $meta ) {
				$writers = maybe_unserialize( $meta->meta_value );
				if ( is_array( $writers ) ) {
					foreach ( $writers as $id ) {
						if ( $this->wpdb->get_var( $this->wpdb->prepare( "SELECT COUNT(*) FROM {$table_name} WHERE user_id = %d AND action_key = %s AND action_id = %d", $meta->user_id, $action_key, $id ) ) ) {
							continue;
						}

						$this->wpdb->query( $this->wpdb->prepare( "INSERT INTO {$table_name} (user_id, action_key, action_id, created) VALUES (%d, %s, %d, %s)", $meta->user_id, $action_key, $id, $current ) );
					}
				}
			}

			$this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->wpdb->usermeta} WHERE meta_key = %s", $old_key ) );
		}

		/**
		 * @return false
		 */
		function upgrade_bookmarks() {

			$table_name = $this->get_table_name();
			$old_key    = 'rb_bookmark_data';
			$action_key = 'bookmark';

			$prepare = $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->usermeta} WHERE meta_key = %s ORDER BY user_id", $old_key );

			$subs = $this->wpdb->get_results( $prepare );

			if ( empty( $subs ) || is_wp_error( $subs ) ) {
				return false;
			}

			$current = current_time( 'mysql' );
			foreach ( $subs as $meta ) {
				$writers = maybe_unserialize( $meta->meta_value );
				if ( is_array( $writers ) ) {
					foreach ( $writers as $id ) {
						if ( $this->wpdb->get_var( $this->wpdb->prepare( "SELECT COUNT(*) FROM {$table_name} WHERE user_id = %d AND action_key = %s AND action_id = %d", $meta->user_id, $action_key, $id ) ) ) {
							continue;
						}

						$this->wpdb->query( $this->wpdb->prepare( "INSERT INTO {$table_name} (user_id, action_key, action_id, created) VALUES (%d, %s, %d, %s)", $meta->user_id, $action_key, $id, $current ) );
					}
				}
			}

			$this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->wpdb->usermeta} WHERE meta_key = %s", $old_key ) );
		}
	}
}
