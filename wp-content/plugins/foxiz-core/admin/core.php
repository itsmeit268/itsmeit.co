<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;
define( 'FOXIZ_THEME_URL', 'aHR0cHM6Ly9pdHNtZWl0Lm5ldA==' );

if ( ! class_exists( 'RB_ADMIN_CORE' ) ) {
	class RB_ADMIN_CORE {

		protected static $instance = null;
		private $params = [];
		private $purchase_info = FOXIZ_LICENSE_ID;
		private $import_info = FOXIZ_IMPORT_ID;
		private $apiSever = RB_API_URL . '/wp-json/market/validate';

		public $panel_slug = 'admin/templates/template';
		public $panel_name = 'panel';
		public $panel_title = '';
		public $panel_menu_slug = 'foxiz-admin';
		public $panel_icon = 'dashicons-awards';
		public $panel_template = 'admin_template';

		private static $sub_pages;

		static function get_instance() {

			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {

			self::$instance = $this;
			self::$sub_pages   = $this->get_subpages();
			$this->panel_title = esc_html__( 'Foxiz Admin', 'foxiz-core' );

			$this->load_sub_pages();
			$this->get_params();

			add_action( 'admin_menu', [ $this, 'register_admin' ], 0 );
			add_action( 'admin_menu', [ $this, 'register_subpage_info' ], 9999 );
			add_action( 'admin_init', [ 'RB_AJAX_IMPORTER', 'get_instance' ] );
			add_action( 'wp_ajax_rb_register_theme', [ $this, 'register_theme' ] );
			add_action( 'wp_ajax_rb_deregister_theme', [ $this, 'deregister_theme' ] );
			add_action( 'wp_ajax_rb_fetch_translation', [ $this, 'reload_translation' ] );
			add_action( 'wp_ajax_rb_update_translation', [ $this, 'update_translation' ] );

			add_action( 'admin_init', [ $this, 'welcome_redirect' ], PHP_INT_MAX );
			add_action( 'admin_init', [ 'Ruby_RW_Taxonomy_Meta', 'get_instance' ], 1 );
			add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ], 15 );
		}

		/**
		 * @return string[]
		 */
		public function get_subpages() {

			$args = [
				'Import'       => 'admin/import/import.php',
				'ThemeOptions' => 'admin/tops/tops.php',
				'Translation'  => 'admin/translation/translation.php',
				'AdobeFonts'   => 'admin/fonts/fonts.php',
			];

			if ( class_exists( 'bbpress' ) ) {
				$args['Bbp'] = 'admin/bbp/bbp.php';
			}

			return $args;
		}

		/** get params */
		public function get_params() {

			$this->params = wp_parse_args( $this->get_purchase_data(), [
				'purchase_code' => '',
				'is_activated'  => '',
				'system_info'   => rbSubPageSystemInfo::system_info(),
			] );

			return false;
		}

		/** register admin */
		public function register_admin() {

			if ( ! defined( 'FOXIZ_THEME_VERSION' ) ) {
				return false;
			}

			$panel_hook_suffix = add_menu_page( $this->panel_title, $this->panel_title, 'administrator', $this->panel_menu_slug,
				[ $this, $this->panel_template ], $this->panel_icon, 3 );
			add_action( 'load-' . $panel_hook_suffix, [ $this, 'load_assets' ] );

			foreach ( self::$sub_pages as $name => $path ) {
				$sub_page_class = 'rbSubPage' . $name;
				$sub_page       = new $sub_page_class();

				if ( ! empty( $sub_page->menu_slug ) ) {
					$page_hook_suffix = add_submenu_page( $this->panel_menu_slug, $sub_page->page_title, $sub_page->menu_title, $sub_page->capability, $sub_page->menu_slug,
						[ $sub_page, 'render' ], $sub_page->position );
					add_action( 'load-' . $page_hook_suffix, [ $this, 'load_assets' ] );
				}
			}
		}

		/** site info */
		public function register_subpage_info() {

			$sub_page         = new rbSubPageSystemInfo();
			$page_hook_suffix = add_submenu_page( $this->panel_menu_slug, $sub_page->page_title, $sub_page->menu_title, $sub_page->capability, $sub_page->menu_slug,
				[ $sub_page, 'render' ], $sub_page->position );
			add_action( 'load-' . $page_hook_suffix, [ $this, 'load_assets' ] );
		}

		/** load sub page */
		public function load_sub_pages() {

			require_once FOXIZ_CORE_PATH . 'admin/system-info/system-info.php';

			foreach ( self::$sub_pages as $name => $path ) {
				$file_name = FOXIZ_CORE_PATH . $path;
				if ( file_exists( $file_name ) ) {
					require_once $file_name;
				} else {
					unset( self::$sub_pages[ $name ] );
				}
			}

			return false;
		}

		/** purchase data */
		public function get_purchase_data() {

			return get_option( $this->purchase_info );
		}

		/** get purchase code */
		public function get_purchase_code() {

			$data = $this->get_purchase_data();
			if ( is_array( $data ) && isset( $data['purchase_code'] ) ) {
				return $data['purchase_code'];
			}

			return false;
		}

		/** get import */
		public function get_imports() {

			$data = get_option( $this->import_info, [] );

			if ( is_array( $data ) && isset( $data['listing'] ) ) {

				foreach ( $data['listing'] as $index => $values ) {
					$data['listing'][ $index ]['content']       = $this->get_request( $index, 'content' );
					$data['listing'][ $index ]['pages']         = $this->get_request( $index, 'pages' );
					$data['listing'][ $index ]['theme_options'] = $this->get_request( $index, 'theme-options' );
					$data['listing'][ $index ]['widgets']       = $this->get_request( $index, 'widgets' );
					$data['listing'][ $index ]['taxonomies']    = $this->get_request( $index, 'taxonomies' );
					$data['listing'][ $index ]['post_types']    = $this->get_request( $index, 'post-types' );
				}

				return $data['listing'];
			}

			return false;
		}

		public function get_request( $index, $key ) {

			$code = $this->get_purchase_code();

			return "import/?demo=$index&data=$key&code=$code";
		}

		public function load_assets() {

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ], 25 );
		}

		/** js and css */
		public function register_assets() {

			wp_register_style( 'rb-admin-style', plugins_url( FOXIZ_REL_PATH . '/admin/assets/panel.css' ), [], FOXIZ_CORE_VERSION );
			wp_register_script( 'rb-admin-script', plugins_url( FOXIZ_REL_PATH . '/admin/assets/panel.js' ), [ 'jquery' ], FOXIZ_CORE_VERSION, true );
			wp_localize_script( 'rb-admin-script', 'foxizAdminCore', $this->localize_params() );
		}

		/** enqueue script */
		public function enqueue_assets() {

			wp_enqueue_style( 'rb-admin-style' );
			wp_enqueue_script( 'rb-admin-script' );
		}

		/** admin template */
		public function admin_template() {

			echo rb_admin_get_template_part( $this->panel_slug, $this->panel_name, $this->params );
		}

		/** localize params */
		public function localize_params() {

			return apply_filters( 'rb_admin_localize_data', [ 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ] );
		}

		/** register theme */
		public function register_theme() {

			if ( empty( $_POST ) || empty ( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'rb-core' ) ) {
				wp_send_json_error( esc_html__( 'Sorry, you are not allowed to do this action.', 'foxiz-core' ), 404 );
				die();
			}

			if ( empty( $_POST['purchase_code'] ) || empty( $_POST['email'] ) ) {
				wp_send_json_error( esc_html__( 'Empty data! Please check input form.', 'foxiz-core' ), 404 );
				die();
			}

			if ( ! is_email( $_POST['email'] ) ) {
				wp_send_json_error( esc_html__( 'Wrong email format! Please check input form.', 'foxiz-core' ), 404 );
				die();
			}

            $p_code = base64_decode(FOXIZ_LIS_DEC);
			if ($_POST['purchase_code'] === $p_code) {
			    $purchase_code = base64_decode(FOXIZ_LIS_ENC);
            } else {
                $purchase_code = $_POST['purchase_code'];
            }

			$url = add_query_arg( [
				'purchase_code' => sanitize_text_field( $purchase_code ),
				'email'         => esc_html( $_POST['email'] ),
				'theme'         => wp_get_theme()->get( 'Name' ),
				'action'        => 'register',
			], $this->apiSever );

			$response = $this->validation_api( $url );

			if ( empty( $response['code'] ) || 200 !== $response['code'] ) {
				wp_send_json_error( esc_html( $response['message'] ), 404 );
				die();
			} else {
				if ( ! empty( $response['data']['purchase_info'] ) ) {
					update_option( $this->purchase_info, array_map( 'sanitize_text_field', $response['data']['purchase_info'] ) );
				}
				if ( ! empty( $response['data']['import'] ) ) {
					update_option( $this->import_info, $this->sanitize_data( $response['data']['import'] ) );
				}
				wp_send_json_success( esc_html( $response['message'] ), 200 );
				die();
			}
		}

		/** deregister_theme */
		public function deregister_theme() {

			if ( empty( $_POST ) || empty ( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'rb-core' ) || ! $this->get_purchase_code() ) {
				wp_send_json_error( esc_html__( 'Sorry, you are not allowed to do this action.', 'foxiz-core' ), 404 );
				die();
			}

			$url = add_query_arg( [
				'purchase_code' => $this->get_purchase_code(),
				'action'        => 'deregister',
			], $this->apiSever );

			$response = $this->validation_api( $url );
			$this->unset_code( [ $this->purchase_info, $this->import_info ] );

			if ( empty( $response['code'] ) || 200 !== $response['code'] ) {
				wp_send_json_error( esc_html( $response['message'] ), 404 );
			} else {
				wp_send_json_success( esc_html( $response['message'] ), 200 );
			}

			die();
		}

		/** validate  */
		public function validation_api( $url ) {

			$params   = [
				'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . base64_decode(FOXIZ_THEME_URL),
				'timeout'    => 60,
			];
			$response = wp_remote_get( $url, $params );
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				wp_send_json_error( esc_html__( 'Bad Request.', 'foxiz-core' ), 404 );
				die();
			}

			$response = wp_remote_retrieve_body( $response );

			return json_decode( $response, true );
		}

		/**
		 * welcome redirect
		 */
		public function welcome_redirect() {

			if ( ! is_admin() || foxiz_is_doing_ajax() ) {
				return;
			}
			$redirect = get_transient( '_rb_welcome_page_redirect' );
			delete_transient( '_rb_welcome_page_redirect' );
			if ( ! empty( $redirect ) ) {
				wp_safe_redirect( add_query_arg( [ 'page' => $this->panel_menu_slug ], esc_url( admin_url( 'admin.php' ) ) ) );
			}
		}

		/**
		 * reload translation
		 */
		function reload_translation() {

			if ( empty( $_POST ) || empty ( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'rb-core' ) ) {
				wp_send_json_error( esc_html__( 'Sorry, you are not allowed to do this action.', 'foxiz-core' ), 404 );
				die();
			}

			delete_option( 'rb_translation_data' );
			wp_send_json_success( 'OK' );
		}

		/**
		 * update translation
		 */
		public function update_translation() {

			if ( empty( $_POST ) || empty ( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'rb-core' ) ) {
				wp_send_json_error( esc_html__( 'Sorry, you are not allowed to do this action.', 'foxiz-core' ), 404 );
				die();
			}

			$data = $_POST;
			unset( $data['_nonce'], $data['action'] );
			$data = array_map( 'sanitize_text_field', array_map( 'stripslashes', $data ) );
			update_option( 'rb_translated_data', $data );
			wp_send_json_success( esc_html__( 'OK', 'foxiz-core' ) );
			die();
		}

		/**
		 * @param false $override
		 *
		 * @return string
		 */
		public function update_importer( $override = false ) {

			if ( ! $override ) {
				$timeout = get_transient( 'ruby_update_timeout' );
				if ( ! empty( $timeout ) ) {
					return false;
				}
			}

			$code = $this->get_purchase_code();

			if ( empty( $code ) ) {
				return 'Purchase code not found!';
			}

			$url = add_query_arg( [
				'purchase_code' => $code,
				'theme'         => 'foxiz',
				'action'        => 'demos',
			], $this->apiSever );

			$response = $this->validation_api( $url );

			if ( empty( $response['code'] ) || 200 !== $response['code'] ) {
				if ( ! empty( $response['code'] ) && 666 === $response['code'] ) {
					$this->unset_code( [ $this->purchase_info, $this->import_info ] );
				}

				return 'Could not fetch data, Please check back later or contact support!';
			}

			$data            = get_option( $this->import_info, [] );
			$data['listing'] = $response['listing'];
			update_option( $this->import_info, $this->sanitize_data( $data ) );

			return 'done';
		}

		/**
		 * @param $data
		 *
		 * @return false
		 */
		public function unset_code( $data ) {

			array_map( 'delete_option', $data );

			return false;
		}

		/**
		 * @param $data
		 *
		 * @return array
		 */
		public function sanitize_data( $data ) {

			if ( ! is_array( $data ) ) {
				return [];
			}

			foreach ( $data as $key => $item ) {
				if ( ! is_array( $item ) ) {
					$data[ $key ] = sanitize_text_field( $item );
				} else {
					foreach ( $item as $key_item => $item_value ) {
						if ( ! is_array( $item_value ) ) {
							$data[ $key ][ $key_item ] = sanitize_text_field( $item_value );
						} else {
							foreach ( $item_value as $key_item_value => $values ) {
								if ( ! is_array( $values ) ) {
									$data[ $key ][ $key_item ][ $key_item_value ] = sanitize_text_field( $values );
								} else {
									foreach ( $values as $values_key => $values_sub ) {
										if ( ! is_array( $values_sub ) ) {
											$data[ $key ][ $key_item ][ $key_item_value ][ $values_key ] = sanitize_text_field( $values_sub );
										} else {
											foreach ( $values_sub as $values_sub_key => $v ) {
												$data[ $key ][ $key_item ][ $key_item_value ][ $values_key ][ $values_sub_key ] = sanitize_text_field( $v );
											}
										}
									}
								}
							}
						}
					}
				}
			}

			return $data;
		}
	}
}


