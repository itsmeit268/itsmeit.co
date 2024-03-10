<?php
/*
Plugin Name: Simple Google Tag Manager & Analytics 4
Plugin URI: https://themeruby.com/simple-gtm-ga4
Description: Fast and simple integration of Google Tag Manager or Analytics 4 tracking code into your WordPress site with zero performance impact.
Version: 1.1
Requires at least: 6.0
Requires PHP: 5.2
Author: Theme Ruby
Author URI: https://themeruby.com
License: GPLv2 or later
Text Domain: simple-gtm-ga4
*/
defined( 'ABSPATH' ) || exit;
define( 'SIMPLE_GTM_GA4_VERSION', '1.1' );
define( 'SIMPLE_GTM_GA4_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! class_exists( 'Ruby_GTM_Integration', false ) ) {

	class Ruby_GTM_Integration {

		protected static $instance = null;
		public $capability = 'manage_options';
		public $menu_id;
		static $tag_added = false;

		public function __construct() {

			self::$instance = $this;
			add_action( 'admin_menu', [ $this, 'add_admin_menu' ], 10 );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 20 );
			add_action( 'wp_ajax_simple_gtm_save', [ $this, 'save' ] );
			add_action( 'wp_head', [ $this, 'add_script_tag' ] );
			add_action( 'wp_body_open', [ $this, 'add_noscript_tag' ], 1 );
			add_action( 'wp_body_open', [ $this, 'add_amp_tag' ], 15 );
			add_action( 'wp_footer', [ $this, 'add_noscript_tag' ] );
		}

		function enqueue_scripts( $hook_suffix ) {

			if ( $hook_suffix != $this->menu_id ) {
				return;
			}
			wp_enqueue_style( 'rb-admin-style' );
			wp_enqueue_script( 'simple-gtm-ga4-script', SIMPLE_GTM_GA4_PLUGIN_URL . 'assets/js/simple-gtm-ga4.js', [ 'jquery' ], SIMPLE_GTM_GA4_VERSION, true );
		}

		function save() {

			if ( ! check_ajax_referer( 'simple-gtm-ga4-nonce', 'nonce', false ) ) {
				wp_send_json_error( 'Invalid nonce verification' );
			}

			$gtm_id  = sanitize_text_field( $_POST['simple_gtm_id'] );
			$gtag_id = sanitize_text_field( $_POST['simple_gtag_id'] );

			update_option( 'simple_gtm_id', $gtm_id );
			update_option( 'simple_gtag_id', $gtag_id );

			if ( empty( $gtag_id ) && empty( $gtm_id ) ) {
				wp_send_json_error( 'Tag Removed!' );
			}

			wp_send_json_success( 'Save successfully!' );
		}

		public function add_admin_menu() {

			if ( class_exists( 'RB_ADMIN_CORE' ) ) {
				$this->menu_id = add_submenu_page(
					'foxiz-admin',
					esc_html__( 'Google Tag Manager & Analytics 4', 'simple-gtm-ga4' ),
					esc_html__( 'GTM & Analytics 4', 'simple-gtm-ga4' ),
					$this->capability,
					'ruby-gmt-integration',
					[ $this, 'settings_interface' ], 40
				);
			} else {
				$this->menu_id = add_management_page(
					esc_html__( 'Google Tag Manager & Analytics 4', 'simple-gtm-ga4' ),
					esc_html__( 'Google Tag Manager & Analytics 4', 'simple-gtm-ga4' ),
					$this->capability,
					'ruby-gmt-integration',
					[ $this, 'settings_interface' ]
				);
			}
		}

		public function settings_interface() {

			$gtm_id  = get_option( 'simple_gtm_id' );
			$gtag_id = get_option( 'simple_gtag_id' );
			?>
			<div class="rb-panel-wrap">
				<div class="rb-panel-header">
					<div class="rb-panel-heading">
						<h1>
							<i class="dashicons dashicons-chart-bar" aria-label="hidden"></i><?php esc_html_e( 'Google Tag Manager & Analytics 4', 'simple-gtm-ga4' ); ?>
						</h1>
						<p class="sub-heading"><?php esc_html_e( 'You can choose to input either the Google Tag Manager Container ID or the Gtag Measurement ID. If both are provided, Google Tag Manager will take priority.', 'simple-gtm-ga4' ); ?></p>
					</div>
				</div>
				<div id="simple-gtm-ga4" class="simple-gtm-wrap">
					<form id="simple-gtm-form" method="post" action="">
						<?php wp_nonce_field( 'simple-gtm-ga4-nonce', 'simple-gtm-ga4-nonce' ); ?>
						<div class="rb-form-item">
							<label for="gtm_id"><?php echo esc_html__( 'Google Tag Manager Container ID', 'simple-gtm-ga4' ); ?>
								<span class="rb-form-tip"><i class="dashicons dashicons-info-outline"></i><span class="rb-form-tip-content"><?php
										esc_html_e( 'Formatted as GTM-XXXXXX. You can find your container ID in the Google Tag Manager interface.', 'simple-gtm-ga4' ); ?></span></span></label>
							<input type="text" name="simple_gtm_id" id="gtm_id" value="<?php echo esc_attr( $gtm_id ); ?>" placeholder="GTM-A1KEAZD"/>
						</div>
						<div class="rb-form-item">
							<label for="gtag_id"><?php echo esc_html__( 'or Gtag Measurement ID', 'simple-gtm-ga4' ); ?>
								<span class="rb-form-tip"><i class="dashicons dashicons-info-outline"></i><span class="rb-form-tip-content"><?php
										esc_html_e( 'A Measurement ID is an identifier (e.g., G-12345) for a web data stream. You can find this ID in the Google Analytics interface.', 'simple-gtm-ga4' ); ?></span></span></label>
							<input type="text" name="simple_gtag_id" id="simple_gtag_id" value="<?php echo esc_attr( $gtag_id ); ?>" placeholder="G-KV4C5NT2Z1"/>
						</div>
						<input class="simple-gtm-submit" type="submit" name="submit" value="<?php echo esc_html__( 'Save Changes', 'simple-gtm-ga4' ); ?>"/>
					</form>
					<div id="simple-gtm-ga4-response"></div>
					<?php echo '<p class="rb-form-infomation">' . sprintf( __( 'Are you enjoying this plugin? <a href="%s">Find out our premium WordPress themes here</a>.', 'simple-gtm-ga4' ), 'https://themeruby.com/themes/' ) . '</p>'; ?>
				</div>
			</div>
			<?php
		}

		public function add_script_tag() {

			if ( foxiz_is_amp() ) {
				return;
			}

			$gtm_id  = get_option( 'simple_gtm_id' );
			$gtag_id = get_option( 'simple_gtag_id' );

			if ( empty( $gtm_id ) && empty( $gtag_id ) ) {
				return;
			}

			if ( ! empty( $gtm_id ) ) : ?>
				<!-- Google Tag Manager -->
				<script>(function (w, d, s, l, i) {
                        w[l] = w[l] || [];
                        w[l].push({
                            'gtm.start':
                                new Date().getTime(), event: 'gtm.js'
                        });
                        var f = d.getElementsByTagName(s)[0],
                            j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                        j.async = true;
                        j.src =
                            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                        f.parentNode.insertBefore(j, f);
                    })(window, document, 'script', 'dataLayer', '<?php echo esc_attr( $gtm_id ); ?>');</script><!-- End Google Tag Manager -->
			<?php else: ?>
				<!-- Google tag (gtag.js) -->
				<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $gtag_id ); ?>"></script>
				<script> window.dataLayer = window.dataLayer || [];

                    function gtag() {
                        dataLayer.push(arguments);
                    }

                    gtag('js', new Date());
                    gtag('config', '<?php echo esc_attr( $gtag_id ); ?>');
				</script>
			<?php endif;
		}

		public function add_amp_tag() {

			if ( ! foxiz_is_amp() ) {
				return;
			}

			$gtm_id  = get_option( 'simple_gtm_id' );
			$gtag_id = get_option( 'simple_gtag_id' );

			if ( empty( $gtm_id ) && empty( $gtag_id ) ) {
				return;
			}

			if ( ! empty( $gtm_id ) ) : ?>
				<!-- Google Tag Manager -->
				<amp-analytics config="https://www.googletagmanager.com/amp.json?id=<?php echo esc_attr( $gtm_id ); ?>" data-credentials="include"></amp-analytics>
			<?php else: ?>
				<!-- Google tag (gtag.js) -->
				<amp-analytics type="gtag" data-credentials="include">
					<script type="application/json">
						{
							"vars" : {
								"gtag_id": "<?php echo esc_attr( $gtag_id ); ?>",
								"config" : {
									"<?php echo esc_attr( $gtag_id ); ?>": { "groups": "default" }
								}
							}
						}

					</script>
				</amp-analytics>
			<?php endif;
		}

		public function add_noscript_tag() {

			$gtm_id = get_option( 'simple_gtm_id' );
			if ( empty( $gtm_id ) || self::$tag_added ) {
				return;
			}
			?>
			<!-- Google Tag Manager (noscript) -->
			<noscript>
				<iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( $gtm_id ); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe>
			</noscript><!-- End Google Tag Manager (noscript) -->
			<?php
			self::$tag_added = true;
		}

		static function get_instance() {

			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
}

/** load */
Ruby_GTM_Integration::get_instance();

