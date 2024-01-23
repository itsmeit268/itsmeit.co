<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

/** system info */
if ( ! class_exists( 'rbSubPageBbp' ) ) {
	class rbSubPageBbp extends RB_ADMIN_SUB_PAGE {

		private static $instance;

		public function __construct() {

			self::$instance = $this;
			add_action( 'admin_init', [ $this, 'redirect' ], 99 );

			parent::__construct();
		}

		public static function get_instance() {

			if ( self::$instance === null ) {
				return new self();
			}

			return self::$instance;
		}

		public function set_sub_page() {

			$this->page_title = esc_html__( 'Ruby bbPress', 'foxiz-core' );
			$this->menu_title = esc_html__( 'Ruby bbPress', 'foxiz-core' );
			$this->position   = 15;

			$this->menu_slug  = 'ruby-bbpress';
			$this->capability = 'administrator';
		}

		public function get_slug() {

			if ( ! $this->validate() ) {
				return 'admin/templates/validate';
			}

			return false;
		}

		public function get_name() {

			if ( ! $this->validate() ) {
				return 'redirect';
			} else {
				return false;
			}
		}

		public function redirect() {

			global $pagenow;
			if ( $this->validate() ) {
				if ( $pagenow == 'admin.php' && isset( $_GET['page'] ) && 'ruby-bbpress' === $_GET['page'] ) {
					wp_safe_redirect( admin_url( 'options-general.php?page=ruby-bbp-supported' ) );
					exit;
				}
			}
		}
	}
}