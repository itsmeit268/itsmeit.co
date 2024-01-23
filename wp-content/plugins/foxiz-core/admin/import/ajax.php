<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RB_AJAX_IMPORTER', false ) ) {
	/**
	 * Class RB_INIT_IMPORTER
	 * init importer
	 */
	class RB_AJAX_IMPORTER {

		private static $instance;

		public static function get_instance() {

			if ( self::$instance === null ) {
				return new self();
			}

			return self::$instance;
		}

		public function __construct() {

			self::$instance = $this;

			/** ajax importer */
			add_action( 'wp_ajax_rb_importer', [ $this, 'importer' ] );
			add_action( 'wp_ajax_rb_update_import', [ $this, 'update_import' ] );
			add_action( 'wp_ajax_rb_check_progress', [ $this, 'get_progress' ] );
			add_action( 'rb_importer_before_content', [ $this, 'duplicate_menu' ], 10 );
			add_action( 'rb_importer_before_content', [ $this, 'register_post_type' ], 20 );
			add_action( 'rb_importer_before_widgets', [ $this, 'register_demo_widgets' ] );
			add_action( 'rb_importer_content_settings', [ $this, 'after_import_content' ] );
			add_action( 'rb_importer_after_tos', [ $this, 'remove_dynamic_style' ] );
		}

		/** load file */
		public function load() {

			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			require_once plugin_dir_path( __FILE__ ) . 'radium-importer.php';
			require_once plugin_dir_path( __FILE__ ) . 'init.php';
		}

		/** get demos */
		function get_demos() {

			return RB_ADMIN_CORE::get_instance()->get_imports();
		}

		/**
		 * @return string
		 */
		function register_tos_id() {

			if ( ! defined( 'FOXIZ_TOS_ID' ) ) {
				return 'RUBY_OPTIONS';
			}

			return FOXIZ_TOS_ID;
		}

		/** install external plugins */
		public function install_package() {

			if ( ! current_user_can( 'install_plugins' ) ) {
				die( 0 );
			}
			if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'rb-core' ) || ! isset ( $_REQUEST['slug'] ) || ! isset ( $_REQUEST['package'] ) ) {
				die( 0 );
			}

			$package = base64_decode( $_REQUEST['package'] );

			if ( ! class_exists( 'Plugin_Upgrader', false ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			}

			$url = add_query_arg(
				[
					'action' => 'upload-plugin',
					'plugin' => urlencode( sanitize_text_field( $_REQUEST['slug'] ) ),
				],
				'update.php'
			);

			$skin_args = [
				'type'  => 'upload',
				'title' => '',
				'url'   => esc_url_raw( $url ),
			];

			$skin     = new Plugin_Installer_Skin( $skin_args );
			$upgrader = new Plugin_Upgrader( $skin );
			$upgrader->install( $package );

			die();
		}

		public function update_import() {

			wp_send_json( RB_ADMIN_CORE::get_instance()->update_importer( true ), 200 );
			die();
		}

		/** importer */
		public function importer() {

			$this->load();

			if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'rb-core' ) || ! isset ( $_REQUEST['directory'] ) ) {
				die( 0 );
			}

			$demos          = $this->get_demos();
			$directory      = sanitize_text_field( $_REQUEST['directory'] );
			$import_all     = sanitize_text_field( $_REQUEST['import_all'] );
			$import_content = sanitize_text_field( $_REQUEST['import_content'] );
			$import_pages   = sanitize_text_field( $_REQUEST['import_pages'] );
			$import_opts    = sanitize_text_field( $_REQUEST['import_opts'] );
			$import_widgets = sanitize_text_field( $_REQUEST['import_widgets'] );

			if ( ! isset( $demos[ $directory ] ) ) {
				wp_die( esc_html__( 'Not found!', 'foxiz-core' ) );
			}

			$demo = $demos[ $directory ];

			$params = [
				'directory'         => $directory,
				'theme_option_name' => $this->register_tos_id(),
				'import_all'        => $import_all,
				'import_content'    => $import_content,
				'import_pages'      => $import_pages,
				'import_opts'       => $import_opts,
				'import_widgets'    => $import_widgets,
			];

			$params['theme_options'] = isset( $demo['theme_options'] ) ? $demo['theme_options'] : null;
			$params['taxonomies']    = isset( $demo['taxonomies'] ) ? $demo['taxonomies'] : null;
			$params['post_types']    = isset( $demo['post_types'] ) ? $demo['post_types'] : null;
			$params['content']       = isset( $demo['content'] ) ? $demo['content'] : null;
			$params['pages']         = isset( $demo['pages'] ) ? $demo['pages'] : null;
			$params['widgets']       = isset( $demo['widgets'] ) ? $demo['widgets'] : null;

			$this->before_import();
			new RB_INIT_IMPORTER( $params );
		}

		/** import progress */
		public function before_import() {

			delete_option( 'rb_import_progress' );
			add_action( 'wp_import_posts', [ $this, 'import_progress_setup' ] );
			add_action( 'add_attachment', [ $this, 'update_progress' ] );
			add_action( 'edit_attachment', [ $this, 'update_progress' ] );
			add_action( 'wp_insert_post', [ $this, 'update_progress' ] );
			add_filter( 'wp_import_post_data_raw', [ $this, 'check_post' ] );
		}

		public function import_progress_setup( $posts ) {

			$progress_array = [
				'total_post'     => count( $posts ),
				'imported_count' => 0,
				'remaining'      => count( $posts ),
			];
			update_option( 'rb_import_progress', $progress_array );

			return $posts;
		}

		/** update progress */
		public function update_progress() {

			$post_count = get_option( 'rb_import_progress' );
			if ( is_array( $post_count ) ) {
				if ( $post_count['remaining'] > 0 ) {
					$post_count['remaining']      = $post_count['remaining'] - 1;
					$post_count['imported_count'] = $post_count['imported_count'] + 1;
					update_option( 'rb_import_progress', $post_count );
				} else {
					$post_count['remaining']      = 0;
					$post_count['imported_count'] = $post_count['total_post'];
					update_option( 'rb_import_progress', $post_count );
				}
			}
		}

		/** check posts */
		public function check_post( $post ) {

			if ( ! post_type_exists( $post['post_type'] ) ) {
				$this->update_progress();

				return $post;
			}

			if ( $post['status'] == 'auto-draft' ) {
				$this->update_progress();

				return $post;
			}

			if ( 'nav_menu_item' == $post['post_type'] ) {
				$this->update_progress();

				return $post;
			}

			$post_exists = post_exists( $post['post_title'], '', $post['post_date'] );
			if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
				$this->update_progress();

				return $post;
			}

			return $post;
		}

		/**
		 * get_progress
		 */
		public function get_progress() {

			$progress = get_option( 'rb_import_progress' );
			wp_send_json( $progress );
			die();
		}

		/**
		 * @param string $directory
		 */
		function after_import_content( $directory = '' ) {

			$demos = $this->get_demos();

			if ( ! empty( $demos[ $directory ]['homepage'] ) ) {

				$pages = get_posts( [
						'post_type'   => 'page',
						'title'       => $demos[ $directory ]['homepage'],
						'post_status' => 'all',
						'numberposts' => 1,
						'order'       => 'ASC',
					]
				);
				if ( ! empty( $pages[0]->ID ) ) {
					update_option( 'page_on_front', $pages[0]->ID );
					update_option( 'show_on_front', 'page' );
				}

				$blog = get_posts( [
						'post_type'   => 'page',
						'title'       => 'Blog',
						'post_status' => 'all',
						'numberposts' => 1,
						'order'       => 'ASC',
					]
				);
				if ( ! empty( $blog[0]->ID ) ) {
					update_option( 'page_for_posts', $blog[0]->ID );
				}
			} else {
				update_option( 'page_on_front', 0 );
				update_option( 'show_on_front', 'posts' );
			}

			/** setup WC */
			if ( class_exists( 'WC_Install' ) ) {
				WC_Install::create_pages();
			}

			/** setup menu */
			$main_menu   = get_term_by( 'name', 'main', 'nav_menu' );
			$mobile_menu = get_term_by( 'name', 'mobile', 'nav_menu' );
			$quick_menu  = get_term_by( 'name', 'mobile-quick-access', 'nav_menu' );

			$menu_locations = [];
			if ( isset( $main_menu->term_id ) ) {
				$menu_locations['foxiz_main'] = $main_menu->term_id;
			}
			if ( isset( $mobile_menu->term_id ) ) {
				$menu_locations['foxiz_mobile'] = $mobile_menu->term_id;
			}
			if ( isset( $quick_menu->term_id ) ) {
				$menu_locations['foxiz_mobile_quick'] = $quick_menu->term_id;
			}

			set_theme_mod( 'nav_menu_locations', $menu_locations );
		}

		function register_demo_widgets() {

			/** empty sidebars */
			$sidebars_widgets['foxiz_sidebar_default']          = [];
			$sidebars_widgets['foxiz_sidebar_more']             = [];
			$sidebars_widgets['foxiz_sidebar_fw_footer']        = [];
			$sidebars_widgets['foxiz_sidebar_footer_1']         = [];
			$sidebars_widgets['foxiz_sidebar_footer_2']         = [];
			$sidebars_widgets['foxiz_sidebar_footer_3']         = [];
			$sidebars_widgets['foxiz_sidebar_footer_4']         = [];
			$sidebars_widgets['foxiz_sidebar_footer_5']         = [];
			$sidebars_widgets['foxiz_entry_top']                = [];
			$sidebars_widgets['foxiz_entry_bottom']             = [];
			$sidebars_widgets['foxiz_sidebar_multi_sb1']        = [];
			$sidebars_widgets['foxiz_sidebar_multi_sb2']        = [];
			$sidebars_widgets['foxiz_sidebar_multi_next-posts'] = [];
			$sidebars_widgets['foxiz_sidebar_multi_single']     = [];
			$sidebars_widgets['foxiz_sidebar_multi_blog']       = [];
			$sidebars_widgets['foxiz_sidebar_multi_contact']    = [];

			/** add sidebars */
			$theme_options                   = get_option( 'foxiz_theme_options' );
			$theme_options['multi_sidebars'] = [ 'sb1', 'sb2', 'next-posts', 'single', 'blog', 'contact' ];

			update_option( 'sidebars_widgets', $sidebars_widgets );
			update_option( FOXIZ_TOS_ID, $theme_options );

			/** register sidebar to import */
			register_sidebar( [
				'name'          => 'More Menu Section',
				'id'            => 'foxiz_sidebar_more',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			] );

			register_sidebar( [
				'name'          => 'sb1',
				'id'            => 'foxiz_sidebar_multi_sb1',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			] );

			register_sidebar( [
				'name'          => 'sb2',
				'id'            => 'foxiz_sidebar_multi_sb2',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			] );
			register_sidebar( [
				'name'          => 'next-posts',
				'id'            => 'foxiz_sidebar_multi_next-posts',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			] );

			register_sidebar( [
				'name'          => 'single',
				'id'            => 'foxiz_sidebar_multi_single',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			] );

			register_sidebar( [
				'name'          => 'contact',
				'id'            => 'foxiz_sidebar_multi_contact',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			] );

			register_sidebar( [
				'name'          => 'blog',
				'id'            => 'foxiz_sidebar_multi_blog',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			] );

			return false;
		}

		/** register podcast for import */
		function register_post_type() {

			if ( class_exists( 'Foxiz_Register_Podcast' ) ) {
				Foxiz_Register_Podcast::get_instance()->register();
			}
		}

		/** remove duplicate menu */
		function duplicate_menu() {

			$deleted_menus = [
				'main',
				'mobile',
				'mobile-quick-access',
				'more-1',
				'more-2',
				'more-3',
				'footer-1',
				'footer-2',
				'footer-3',
				'footer-4',
				'footer-5',
				'quick-link',
				'top-categories',
				'advertise',
				'footer-copyright',
				'logged',
			];

			foreach ( $deleted_menus as $menu ) {
				wp_delete_nav_menu( $menu );
			}

			return false;
		}

		/**
		 * remove cache
		 */
		public function remove_dynamic_style() {

			delete_option( 'foxiz_style_cache' );
		}
	}
}


