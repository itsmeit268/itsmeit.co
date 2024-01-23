<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

/** system info */
if ( ! class_exists( 'rbSubPageImport', false ) ) {
	class rbSubPageImport extends RB_ADMIN_SUB_PAGE {

		private static $instance;
		public $demos = [];
		public $demos_path;
		public $demos_url;

		/** get_instance */
		static function get_instance() {

			if ( self::$instance === null ) {
				return new self();
			}

			return self::$instance;
		}

		public function __construct() {

			self::$instance = $this;

			parent::__construct();
			$this->demos_path = apply_filters( 'rb_importer_demos_path', trailingslashit( plugin_dir_path( __FILE__ ) . 'demos' ) );
			$this->demos_url  = apply_filters( 'rb_importer_demos_url', trailingslashit( plugin_dir_url( __FILE__ ) . 'demos' ) );
			$this->demos      = RB_ADMIN_CORE::get_instance()->get_imports();
		}

		/** set sub page */
		public function set_sub_page() {

			$this->page_title = esc_html__( 'Demo Importer', 'foxiz-core' );
			$this->menu_title = esc_html__( 'Demo Importer', 'foxiz-core' );
			$this->menu_slug  = 'rb-demo-importer';
			$this->capability = 'administrator';
			$this->position   = 5;
		}

		public function get_slug() {

			if ( ! $this->validate() ) {
				return 'admin/templates/validate';
			} else {
				return 'admin/import/template';
			}
		}

		public function get_name() {

			if ( ! $this->validate() ) {
				return 'redirect';
			} else {
				return false;
			}
		}

		/**get params */
		public function get_params() {

			/** load importer library */
			require_once plugin_dir_path( __FILE__ ) . 'parts.php';

			$params          = [];
			$imported        = get_option( 'rb_imported_demos' );
			$params['demos'] = $this->demos;
			if ( is_array( $params['demos'] ) && count( $params['demos'] ) ) {
				foreach ( $params['demos'] as $directory => $values ) {
					if ( empty( $params['demos'][ $directory ]['preview'] ) ) {
						$params['demos'][ $directory ]['preview'] = $this->demos_url . $directory . '.jpg';
					}
					if ( is_array( $imported ) && ! empty( $imported[ $directory ] ) ) {
						$params['demos'][ $directory ]['imported'] = $imported[ $directory ];
					} else {
						$params['demos'][ $directory ]['imported'] = 'none';
					}
				}
			}
			$params = apply_filters( 'rb_importer_params', $params );

			return $params;
		}
	}
}