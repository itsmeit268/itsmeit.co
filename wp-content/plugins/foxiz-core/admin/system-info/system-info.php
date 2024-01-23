<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'rbSubPageSystemInfo' ) ) {
	class rbSubPageSystemInfo extends RB_ADMIN_SUB_PAGE {

		private static $instance;

		public function __construct() {

			self::$instance = $this;

			parent::__construct();
		}

		public static function get_instance() {

			if ( self::$instance === null ) {
				return new self();
			}

			return self::$instance;
		}

		public function set_sub_page() {

			$this->page_title = esc_html__( 'System Info', 'foxiz-core' );
			$this->menu_title = esc_html__( 'System Info', 'foxiz-core' );
			$this->menu_slug  = 'rb-system-info';
			$this->capability = 'administrator';
			$this->position   = 9999;

			$this->set_params( [
				'system_info' => $this::system_info(),
				'wp_info'     => $this::wordpress_info(),
			] );
		}

		/** system info */
		static function system_info() {

			return [
				'php_version'     => [
					'title'   => esc_html__( 'PHP Version', 'foxiz-core' ),
					'value'   => phpversion(),
					'min'     => '5.6',
					'passed'  => version_compare( phpversion(), '7.0.0' ) >= 0,
					'warning' => esc_html__( 'WordPress recommended PHP version 7.0 or greater to get better performance for your site.', 'foxiz-core' ),
				],
				'memory_limit'    => [
					'title'   => esc_html__( 'Memory Limit', 'foxiz-core' ),
					'value'   => size_format( wp_convert_hr_to_bytes( @ini_get( 'memory_limit' ) ) ),
					'min'     => '64M',
					'passed'  => wp_convert_hr_to_bytes( ini_get( 'memory_limit' ) ) >= 67108864,
					'warning' => esc_html__( 'The memory_limit value is set low. The theme recommended this value to be at least 64MB for the theme in order to work.', 'foxiz-core' ),
				],
				'max_input_vars'  => [
					'title'   => esc_html__( 'Max Input Vars', 'foxiz-core' ),
					'value'   => ini_get( 'max_input_vars' ),
					'min'     => '3000',
					'passed'  => (int) ini_get( 'max_input_vars' ) >= 2000,
					'warning' => esc_html__( 'The max_input_vars value is set low. The theme recommended this value to be at least 3000.', 'foxiz-core' ),
				],
				'post_max_size'   => [
					'title'   => esc_html__( 'Post Max Size', 'foxiz-core' ),
					'value'   => ini_get( 'post_max_size' ),
					'min'     => '32',
					'passed'  => (int) ini_get( 'post_max_size' ) >= 32,
					'warning' => esc_html__( 'The post_max_size value is set low. We recommended this value to be at least 32M.', 'foxiz-core' ),
				],
				'max_upload_size' => [
					'title'   => esc_html__( 'Max Upload Size', 'foxiz-core' ),
					'value'   => size_format( wp_max_upload_size() ),
					'min'     => '32',
					'passed'  => (int) wp_max_upload_size() >= 33554432,
					'warning' => esc_html__( 'The post_max_size value is set low. We recommended this value to be at least 32M.', 'foxiz-core' ),
				],
			];
		}

		/** wordpress info */
		static function wordpress_info() {

			global $wp_version;

			return [
				'wp_version'    => [
					'title' => esc_html__( 'WordPress Version', 'foxiz-core' ),
					'value' => $wp_version,
				],
				'debug_mode'    => [
					'title'   => esc_html__( 'Debug Mode', 'foxiz-core' ),
					'value'   => ( WP_DEBUG ) ? 'Enabled' : 'Disabled',
					'passed'  => ( WP_DEBUG ) ? false : true,
					'warning' => esc_html__( 'Enabling WordPress debug mode might display details about your site\'s PHP code to visitors.', 'foxiz-core' ),
				],
				'debug_log'     => [
					'title' => esc_html__( 'Debug Log', 'foxiz-core' ),
					'value' => ( WP_DEBUG_LOG ) ? 'Enabled' : 'Disabled',
				],
				'theme_name'    => [
					'title' => esc_html__( 'Theme Name', 'foxiz-core' ),
					'value' => wp_get_theme()->Name,
				],
				'theme_version' => [
					'title' => esc_html__( 'Theme Version', 'foxiz-core' ),
					'value' => wp_get_theme()->Version,
				],
				'theme_author'  => [
					'title' => esc_html__( 'Theme Author', 'foxiz-core' ),
					'value' => '<a target="_blank" href="//1.envato.market/6bEx7Q">Theme-Ruby</a>',
				],
			];
		}

		public function get_slug() {

			return 'admin/system-info/template';
		}

		public function get_name() {

			return false;
		}
	}
}
