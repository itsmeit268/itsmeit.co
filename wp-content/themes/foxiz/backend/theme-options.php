<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! defined( 'FOXIZ_TOS_ID' ) ) {
	define( 'FOXIZ_TOS_ID', 'foxiz_theme_options' );
}

if ( ! class_exists( 'Foxiz_Register_Options' ) ) {
	class Foxiz_Register_Options {

		protected static $instance = null;
		protected $is_activated;
		public $sources;
		public $funcs_name;

		public function __construct() {

			self::$instance = $this;

			if ( ! class_exists( 'ReduxFramework' ) || ! class_exists( 'RB_ADMIN_CORE' ) ) {
				return false;
			}

			$this->sources = [
				'logo',
				'header',
				'header-style',
				'sidebar',
				'footer',
				'design',
				'blocks',
				'block-classic',
				'block-grid',
				'block-list',
				'heading',
				'single-post',
				'blog-pages',
				'page',
				'color',
				'typography',
				'login',
				'personalize',
				'table-contents',
				'socials',
				'breadcrumb',
				'privacy',
				'newsletter',
				'adblock',
				'dark-mode',
				'optimized',
				'ads',
				'reaction',
				'woocommerce',
				'amp',
			];

			$this->funcs_name = [
				'general',
				'logo',
				'logo_global',
				'logo_mobile',
				'logo_transparent',
				'logo_favicon',
				'header',
				'header_general',
				'header_1',
				'header_4',
				'header_5',
				'header_mobile',
				'header_more',
				'header_search',
				'header_login',
				'header_notification',
				'header_alert',
				'header_cart',
				'sidebar',
				'footer',

				'design',
				'design_category',
				'design_meta',
				'design_meta_custom',
				'design_featured',
				'design_slider',
				'design_format',
				'design_live_blog',
				'design_readmore',
				'design_hover',
				'design_border',
				'design_svg',
				'design_gif',
				'design_icons',
				'design_placeholder',
				'design_back_top',
				'design_tooltips',
				'design_input',
				'design_loader',
				'design_container',

				'blocks',
				'block_classic_1',
				'block_grid_1',
				'block_grid_2',
				'block_grid_small_1',
				'block_grid_box_1',
				'block_grid_box_2',
				'block_list_1',
				'block_list_2',
				'block_list_box_1',
				'block_list_box_2',
				'heading',
				'color',

				'single_post',
				'single_post_layout',
				'single_post_types_layout',
				'single_post_featured',
				'single_post_sections',
				'single_post_sidebar',
				'single_post_category',
				'single_post_tagline',
				'single_post_meta',
				'single_post_sponsored',
				'single_post_review',
				'single_post_comment',
				'single_post_footer',
				'single_post_ajax',
				'single_post_shares',
				'single_post_sticky',
				'single_reading_indicator',
				'single_post_highlight',
				'single_post_header',

				'page',
				'category',
				'blog',
				'author',
				'search',
				'archive',
				'page_404',

				'typo',
				'typo_body',
				'typo_h1',
				'typo_h2',
				'typo_h3',
				'typo_h4',
				'typo_h5',
				'typo_h6',
				'typo_category',
				'typo_meta',
				'typo_excerpt',
				'typo_readmore',
				'typo_input',
				'typo_breadcrumb',
				'typo_single',
				'typo_menus',
				'typo_heading',
				'typo_widget_menu',
				'typo_wc',

				'table_contents',
				'reaction',
				'bookmark',
				'bookmark_general',
				'reading_list_saved',
				'reading_list_followed',
				'reading_list_recommended',
				'user_history',

				'socials',
				'breadcrumb',
				'privacy',
				'dark_mode',
				'newsletter',
				'performance',
				'seo',
				'adblock',
				'ads',
				'ad_auto',
				'ad_top',
				'ad_single',
				'footer_slide_up',
				'login',
				'woocommerce',
				'wc_style',
				'wc_page',
				'wc_single',
				'membership',
				'podcast',
				'podcast_general',
				'podcast_design',
				'podcast_single',
				'podcast_show',
				'podcast_archive',
				'amp',
				'amp_general',
				'amp_single',
				'amp_auto_ads',
				'amp_ads',
			];

			$this->load_files();

			$is_activate = $this->activated();
			if ( empty( $is_activate ) ) {
				return false;
			}

			Redux::setArgs( FOXIZ_TOS_ID, $this->get_params() );
			$this->register_options();
		}

		/** load */
		function load_files() {

			$path = 'backend/panels';
			if ( is_array( $this->sources ) ) {
				foreach ( $this->sources as $name ) {
					$file = get_theme_file_path( $path . '/' . trim( $name ) . '.php' );
					if ( file_exists( $file ) ) {
						require_once $file;
					}
				}
			}
		}

		public function display_name() {

			return esc_html__( 'Foxiz Panel', 'foxiz' ) . '<span class="p-version">' . wp_get_theme()->get( 'Version' ) . '</span>';
		}

		public function display_version() {

			return '<span class="pdocs-info"><i class="el el-folder-open"></i><a href="https://help.themeruby.com/foxiz" target="_blank">' . esc_html__( 'Online Documentation', 'foxiz' ) . '</a></span>';
		}

		public function activated() {

			return $this->is_activated = RB_ADMIN_CORE::get_instance()->get_purchase_code();
		}

		public function get_params() {

			return [
				'opt_name'                  => FOXIZ_TOS_ID,
				'display_name'              => $this->display_name(),
				'display_version'           => $this->display_version(),
				'menu_type'                 => 'menu',
				'allow_sub_menu'            => true,
				'menu_title'                => esc_html__( 'Theme Options', 'foxiz' ),
				'page_title'                => esc_html__( 'Theme Options', 'foxiz' ),
				'google_api_key'            => '',
				'google_update_weekly'      => false,
				'async_typography'          => false,
				'admin_bar'                 => true,
				'admin_bar_icon'            => 'dashicons-admin-generic',
				'admin_bar_priority'        => 50,
				'global_variable'           => FOXIZ_TOS_ID,
				'dev_mode'                  => false,
				'update_notice'             => false,
				'customizer'                => true,
				'page_priority'             => 54,
				'page_parent'               => 'themes.php',
				'page_permissions'          => 'manage_options',
				'menu_icon'                 => '',
				'last_tab'                  => '',
				'page_icon'                 => 'icon-themes',
				'page_slug'                 => 'ruby-options',
				'show_options_object'       => false,
				'save_defaults'             => true,
				'default_show'              => false,
				'default_mark'              => '',
				'show_import_export'        => true,
				'transient_time'            => 6400,
				'use_cdn'                   => true,
				'output'                    => true,
				'output_tag'                => true,
				'disable_tracking'          => true,
				'database'                  => '',
				'disable_google_fonts_link' => true,
				'system_info'               => false,
				'search'                    => true,
			];
		}

		public function register_options() {

			if ( $this->is_activated ) {
				foreach ( $this->funcs_name as $name ) {
					$func = 'foxiz_register_options_' . $name;
					if ( function_exists( $func ) ) {
						Redux::setSection( FOXIZ_TOS_ID, call_user_func( $func ) );
					}
				}
			}
		}

		static function get_instance() {

			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
}