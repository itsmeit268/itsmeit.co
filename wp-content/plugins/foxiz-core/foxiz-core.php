<?php
/**
 * Plugin Name:    Foxiz Core
 * Plugin URI:     https://themeforest.net/user/theme-ruby/
 * Description:    Features for Foxiz, this is required plugin (important) for this theme.
 * Version:        2.2.3
 * Requires at least: 6.0
 * Requires PHP:   5.6
 * Text Domain:    foxiz-core
 * Domain Path:    /languages/
 * Author:         Theme-Ruby
 * Author URI:     https://themeforest.net/user/theme-ruby/
 *
 * @package        foxiz-core
 */
defined( 'ABSPATH' ) || exit;

if ( ! defined( 'FOXIZ_CORE_VERSION' ) ) {
	define( 'FOXIZ_CORE_VERSION', '2.2.3' );
}

if ( ! defined( 'FOXIZ_TOS_ID' ) ) {
	define( 'FOXIZ_TOS_ID', 'foxiz_theme_options' );
}

define( 'FOXIZ_CORE_URL', plugin_dir_url( __FILE__ ) );
define( 'FOXIZ_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'FOXIZ_REL_PATH', dirname( plugin_basename( __FILE__ ) ) );

if ( ! function_exists( 'is_plugin_active' ) ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

include_once FOXIZ_CORE_PATH . 'include-files.php';

if ( ! is_plugin_active( 'redux-framework/redux-framework.php' ) ) {
	include_once FOXIZ_CORE_PATH . 'lib/redux-framework/framework.php';
}

if ( ! class_exists( 'FOXIZ_CORE', false ) ) {
	class FOXIZ_CORE {

		private static $instance;

		public static function get_instance() {

			if ( self::$instance === null ) {
				return new self();
			}

			return self::$instance;
		}

		public function __construct() {

			self::$instance = $this;
			Foxiz_Upgrade_Db::get_instance();
			register_activation_hook( __FILE__, [ $this, 'activated' ] );
			add_action( 'plugins_loaded', [ $this, 'plugins_support' ], 0 );
			add_action( 'init', [ $this, 'load_components' ], 2 );
			add_action( 'wp_enqueue_scripts', [ $this, 'core_enqueue' ], 1 );
			add_action( 'widgets_init', [ $this, 'register_widgets' ] );
			add_filter( 'user_contactmethods', [ $this, 'user_contactmethods' ] );
			add_action( 'redux/page/' . FOXIZ_TOS_ID . '/enqueue', [ $this, 'enqueue_theme_options' ], 99 );
		}

		function plugins_support() {

			$this->translation();
			RB_ADMIN_CORE::get_instance();
			Foxiz_AMP::get_instance();

			if ( class_exists( 'SimpleWpMembership' ) ) {
				Foxiz_Membership::get_instance();
			}
			if ( class_exists( 'Foxiz_Register_Podcast' ) ) {
				Foxiz_Register_Podcast::get_instance();
			}

			if ( class_exists( 'Foxiz_Register_Podcast' ) ) {
				Foxiz_Register_Podcast::get_instance();
			}
		}

		function load_components() {

			Ruby_Reaction::get_instance();
			Foxiz_Optimized::get_instance();
			Foxiz_Table_Contents::get_instance();
		}

		function translation() {

			$loaded = load_plugin_textdomain( 'foxiz-core', false, FOXIZ_CORE_PATH . 'languages/' );
			if ( ! $loaded ) {
				$locale = apply_filters( 'plugin_locale', get_locale(), 'foxiz-core' );
				$mofile = FOXIZ_CORE_PATH . 'languages/foxiz-core-' . $locale . '.mo';
				load_textdomain( 'foxiz-core', $mofile );
			}
		}

		function enqueue_theme_options() {

			wp_dequeue_script( 'redux-rtl-css' );
			wp_register_style( 'foxiz-panel', FOXIZ_CORE_URL . 'admin/assets/theme-options.css', [ 'redux-admin-css' ], FOXIZ_CORE_VERSION, 'all' );
			wp_enqueue_style( 'foxiz-panel' );
		}

		function core_enqueue() {

			if ( is_admin() || foxiz_is_amp() ) {
				return false;
			}

			$deps = [ 'jquery' ];
			wp_register_script( 'foxiz-core', FOXIZ_CORE_URL . 'assets/core.js', $deps, FOXIZ_CORE_VERSION, true );
			$js_params     = [
				'ajaxurl'      => admin_url( 'admin-ajax.php' ),
				'darkModeID'   => $this->get_dark_mode_id(),
				'cookieDomain' => defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '',
				'cookiePath'   => defined( 'COOKIEPATH' ) ? COOKIEPATH : '/',
			];
			$multi_site_id = $this->get_multisite_subfolder();
			if ( $multi_site_id ) {
				$js_params['mSiteID'] = $multi_site_id;
			}
			wp_localize_script( 'foxiz-core', 'foxizCoreParams', $js_params );
			wp_enqueue_script( 'foxiz-core' );
		}

		public function get_dark_mode_id() {

			if ( is_multisite() ) {
				return 'D_' . trim( str_replace( '/', '_', preg_replace( '/https?:\/\/(www\.)?/', '', get_site_url() ) ) );
			}

			return 'RubyDarkMode';
		}

		public function get_multisite_subfolder() {

			if ( is_multisite() ) {
				$site_info = get_blog_details( get_current_blog_id() );
				$path      = $site_info->path;

				if ( ! empty( $path ) && '/' !== $path ) {
					return trim( str_replace( '/', '', $path ) );
				} else {
					return false;
				}
			}

			return false;
		}

		/**
		 * @return false
		 */
		public function register_widgets() {

			$widgets = [
				'Foxiz_W_Post',
				'Foxiz_W_Follower',
				'Foxiz_W_Weather',
				'Foxiz_Fw_Instagram',
				'Foxiz_W_Social_Icon',
				'Foxiz_W_Youtube_Subscribe',
				'Foxiz_W_Flickr',
				'Foxiz_W_Address',
				'Foxiz_W_Instagram',
				'Foxiz_Fw_Mc',
				'Foxiz_Ad_Image',
				'Foxiz_FW_Banner',
				'Foxiz_W_Facebook',
				'Foxiz_Ad_Script',
				'Foxiz_W_Ruby_Template',
			];

			foreach ( $widgets as $widget ) {
				if ( class_exists( $widget ) ) {
					register_widget( $widget );
				}
			}

			return false;
		}

		function user_contactmethods( $user ) {

			if ( ! is_array( $user ) ) {
				$user = [];
			}

			$data = [
				'job'         => esc_html__( 'Job Name', 'foxiz-core' ),
				'facebook'    => esc_html__( 'Facebook profile URL', 'foxiz-core' ),
				'twitter_url' => esc_html__( 'Twitter profile URL', 'foxiz-core' ),
				'instagram'   => esc_html__( 'Instagram profile URL', 'foxiz-core' ),
				'pinterest'   => esc_html__( 'Pinterest profile URL', 'foxiz-core' ),
				'linkedin'    => esc_html__( 'LinkedIn profile URL', 'foxiz-core' ),
				'tumblr'      => esc_html__( 'Tumblr profile URL', 'foxiz-core' ),
				'flickr'      => esc_html__( 'Flickr profile URL', 'foxiz-core' ),
				'skype'       => esc_html__( 'Skype profile URL', 'foxiz-core' ),
				'snapchat'    => esc_html__( 'Snapchat profile URL', 'foxiz-core' ),
				'myspace'     => esc_html__( 'Myspace profile URL', 'foxiz-core' ),
				'youtube'     => esc_html__( 'Youtube profile URL', 'foxiz-core' ),
				'bloglovin'   => esc_html__( 'Bloglovin profile URL', 'foxiz-core' ),
				'digg'        => esc_html__( 'Digg profile URL', 'foxiz-core' ),
				'dribbble'    => esc_html__( 'Dribbble profile URL', 'foxiz-core' ),
				'soundcloud'  => esc_html__( 'Soundcloud profile URL', 'foxiz-core' ),
				'vimeo'       => esc_html__( 'Vimeo profile URL', 'foxiz-core' ),
				'reddit'      => esc_html__( 'Reddit profile URL', 'foxiz-core' ),
				'vkontakte'   => esc_html__( 'Vkontakte profile URL', 'foxiz-core' ),
				'telegram'    => esc_html__( 'Telegram profile URL', 'foxiz-core' ),
				'whatsapp'    => esc_html__( 'Whatsapp profile URL', 'foxiz-core' ),
				'rss'         => esc_html__( 'Rss', 'foxiz-core' ),
			];

			return array_merge( $user, $data );
		}

		function activated() {

			if ( ! is_network_admin() ) {
				set_transient( '_rb_welcome_page_redirect', true, 30 );
			}
		}
	}
}

/** init */
FOXIZ_CORE::get_instance();