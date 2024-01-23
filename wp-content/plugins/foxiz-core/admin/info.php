<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Foxiz_Update_Settings_Info' ) ) {
	class Foxiz_Update_Settings_Info {

		private static $instance;

		public static function get_instance() {

			if ( self::$instance === null ) {
				return new self();
			}

			return self::$instance;
		}

		public function __construct() {

			self::$instance = $this;
			add_action( 'admin_notices', [ $this, 'settings_warn' ], 20 );
		}

		public function settings_warn() {

			$current_screen = get_current_screen();

			if ( ! $current_screen || $current_screen->id !== 'toplevel_page_ruby-options' ) {
				return false;
			}

			$settings  = get_option( 'foxiz_theme_options' );
			$demo_host = 'themeruby.com';
			$localhost = 'localhost';
			$parts     = parse_url( get_site_url() );
			if ( ! is_array( $settings ) || empty( $parts['host'] ) || $demo_host === $parts['host'] || false !== strpos( $parts['host'], $localhost ) ) {
				return false;
			}
			$buffer = $this->get_settings_warning( $settings, $parts['host'], $demo_host );

			if ( empty( $buffer ) ) {
				return false;
			}

			echo '<div class="notice notice-warning rb-setting-warning is-dismissible">';
			echo '<h4>' . esc_html__( 'Important: Please update images after import a demo', 'foxiz-core' ) . '</h4>';
			echo '<p>' . esc_html__( 'These are the settings that need to be updated:', 'foxiz-core' ) . '</p><ul>' . $buffer . '</ul>';
			echo '</div>';
		}

		public function get_settings_warning( $settings, $host, $demo ) {

			$output = '';
			foreach ( $settings as $id => $setting ) {
				if ( ! empty( $setting['url'] ) ) {
					if ( ! strpos( $setting['url'], $host ) || strpos( $setting['url'], $demo ) !== false ) {
						$output .= '<li><strong>' . $this->info( $id ) . ': </strong><span class="url-info">' . $setting['url'] . '</span></li>';
					}
				}
			}

			return $output;
		}

		function info( $id ) {

			$data = [
				'logo'                       => esc_html__( 'Logo > Default Logos > Main Logo', 'foxiz-core' ),
				'dark_logo'                  => esc_html__( 'Logo > Default Logos > Dark Mode - Main Logo', 'foxiz-core' ),
				'retina_logo'                => esc_html__( 'Logo > Default Logos > Retina Main Logo', 'foxiz-core' ),
				'dark_retina_logo'           => esc_html__( 'Logo > Default Logos > Dark Mode - Retina Main Logo', 'foxiz-core' ),
				'mobile_logo'                => esc_html__( 'Logo > Mobile Logos > Mobile Logo', 'foxiz-core' ),
				'dark_mobile_logo'           => esc_html__( 'Logo > Mobile Logos > Dark Mode - Mobile Logo', 'foxiz-core' ),
				'transparent_logo'           => esc_html__( 'Logo > Transparent Logos > Transparent Logo', 'foxiz-core' ),
				'transparent_retina_logo'    => esc_html__( 'Logo > Transparent Logos > Dark Mode - Transparent Logo', 'foxiz-core' ),
				'icon_touch_apple'           => esc_html__( 'Logo > Bookmarklet > iOS Bookmarklet Icon', 'foxiz-core' ),
				'icon_touch_metro'           => esc_html__( 'Logo > Bookmarklet > Metro UI Bookmarklet Icon', 'foxiz-core' ),
				'ad_top_image'               => esc_html__( 'Ads & Slide Up > Top Site > Ad Image', 'foxiz-core' ),
				'ad_top_dark_image'          => esc_html__( 'Ads & Slide Up > Top Site > Dark Mode - Ad Image', 'foxiz-core' ),
				'ad_single_image'            => esc_html__( 'Ads & Slide Up > Inline Single Content > Ad Image', 'foxiz-core' ),
				'ad_single_dark_image'       => esc_html__( 'Ads & Slide Up > Inline Single Content > Dark Mode - Ad Image', 'foxiz-core' ),
				'amp_footer_logo'            => esc_html__( 'AMP > General > AMP Footer Logo', 'foxiz-core' ),
				'page_404_featured'          => esc_html__( '404 Page > Header Image', 'foxiz-core' ),
				'page_404_dark_featured'     => esc_html__( '404 Page > Dark Mode - Header Image', 'foxiz-core' ),
				'saved_image'                => esc_html__( 'Personalize > Reading List Header > Description Image', 'foxiz-core' ),
				'saved_image_dark'           => esc_html__( 'Personalize > Reading List Header > Dark Mode - Description Image', 'foxiz-core' ),
				'interest_image'             => esc_html__( 'Personalize > User Interests > Categories > Description Image', 'foxiz-core' ),
				'interest_image_dark'        => esc_html__( 'Personalize > User Interests > Categories > Dark Mode - Description Image', 'foxiz-core' ),
				'interest_author_image'      => esc_html__( 'Personalize > User Interests > Authors > Description Image', 'foxiz-core' ),
				'interest_author_image_dark' => esc_html__( 'Personalize > User Interests > Authors > Dark Mode - Description Image', 'foxiz-core' ),
				'footer_logo'                => esc_html__( 'Footer > Footer Logo', 'foxiz-core' ),
				'dark_footer_logo'           => esc_html__( 'Footer > Dark Mode - Footer Logo', 'foxiz-core' ),
				'header_search_custom_icon'  => esc_html__( 'Header > Header Search > Custom Search SVG', 'foxiz-core' ),
				'notification_custom_icon'   => esc_html__( 'Header > Header Search > Custom Notification SVG', 'foxiz-core' ),
				'login_custom_icon'          => esc_html__( 'Header > Popup Sign In > Custom Login SVG', 'foxiz-core' ),
				'cart_custom_icon'           => esc_html__( 'Header > Mini Cart > Custom Cart SVG Icon', 'foxiz-core' ),
				'header_login_logo'          => esc_html__( 'Login > Popup Sign In > Form Logo', 'foxiz-core' ),
				'header_login_dark_logo'     => esc_html__( 'Login > Popup Sign In > Dark Mode - Form Logo', 'foxiz-core' ),
				'login_screen_logo'          => esc_html__( 'Login > Login Screen Layout > Login Logo', 'foxiz-core' ),
				'newsletter_cover'           => esc_html__( 'Popup Newsletter > Cover Image', 'foxiz-core' ),
				'facebook_default_img'       => esc_html__( 'SEO Optimized > Fallback Share Image', 'foxiz-core' ),
				'single_post_review_image'   => esc_html__( 'Single Post > Review & Rating > Default Review Image', 'foxiz-core' ),
				'podcast_custom_icon'        => esc_html__( 'Podcast > General > Custom Podcast SVG', 'foxiz-core' ),
			];

			if ( ! empty( $data[ $id ] ) ) {
				return $data[ $id ];
			}

			return esc_html__( 'External link', 'foxiz' );
		}

	}
}

/** init */
Foxiz_Update_Settings_Info::get_instance();
