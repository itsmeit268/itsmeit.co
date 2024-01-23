<?php
/**
 * Plugin Name:    Ruby OpenAI Assistant
 * Plugin URI:     https://themeforest.net/user/theme-ruby/
 * Description:    A powerful tool designed to streamline content creation for bloggers, writers, and website owners.
 * Version:        1.0
 * Domain Path:    /languages/
 * Author:         Theme-Ruby
 * Author URI:     https://themeforest.net/user/theme-ruby/
 *
 * @package        ruby-openai
 */
defined( 'ABSPATH' ) || exit;
define( 'RB_OPENAI_VER', '1.0' );
define( 'RB_OPENAI_URL', plugin_dir_url( __FILE__ ) );
define( 'RB_OPENAI_PATH', plugin_dir_path( __FILE__ ) );

include_once RB_OPENAI_PATH . 'helpers.php';
include_once RB_OPENAI_PATH . 'edit-template.php';

if ( ! class_exists( 'RB_OPENAI_ASSISTANT', false ) ) {
	class RB_OPENAI_ASSISTANT {

		protected static $instance = null;
		public $capability = 'manage_options';
		public $menu_id;

		public static function get_instance() {

			if ( self::$instance === null ) {
				return new self();
			}

			return self::$instance;
		}

		public function __construct() {

			self::$instance = $this;
			add_action( 'admin_menu', [ $this, 'add_admin_menu' ], 20 );
			add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ], 20 );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_editor_assets' ], 999 );
			add_action( 'wp_ajax_rb_openai_save', [ $this, 'save' ] );
			add_action( 'wp_ajax_rb_openai_create_content', [ $this, 'create_content' ] );
			add_filter( 'rb_single_metaboxes', [ $this, 'meta_configs' ] );
		}

		function register_assets() {

			$script_version = filemtime( RB_OPENAI_PATH . 'assets/js/panel.js' );
			$editor_version = filemtime( RB_OPENAI_PATH . 'assets/js/editor.js' );

			wp_register_script( 'rb-openai-panel', RB_OPENAI_URL . 'assets/js/panel.js', [ 'jquery' ], $script_version, true );
			wp_register_script( 'rb-openai-editor', RB_OPENAI_URL . 'assets/js/editor.js', [ 'jquery' ], $editor_version, true );
		}

		public function load_assets() {

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_panel_assets' ], 80 );
		}

		public function enqueue_panel_assets() {

			wp_enqueue_style( 'rb-admin-style' );
			wp_enqueue_script( 'rb-openai-panel' );
		}

		public function enqueue_editor_assets( $hook ) {

			if ( $hook === 'post.php' || $hook === 'post-new.php' ) {
				wp_enqueue_script( 'rb-openai-editor' );
			}
		}

		public function add_admin_menu() {

			$this->menu_id = add_submenu_page(
				'foxiz-admin',
				esc_html__( 'OpenAI Assistant', 'rb-openai' ),
				esc_html__( 'OpenAI Assistant', 'rb-openai' ),
				$this->capability,
				'ruby-openai',
				[ $this, 'settings_interface' ], 60
			);
			add_action( 'load-' . $this->menu_id, [ $this, 'load_assets' ] );
		}

		function save() {

			// Check the nonce for security
			if ( ! check_ajax_referer( 'rb-openai', '_wpnonce_ruby' ) ) {
				wp_send_json_error( 'Invalid nonce verification' );
			}

			// Process the form data
			$rb_openai     = sanitize_text_field( $_POST['rb_openai'] );
			$api_key       = sanitize_text_field( $_POST['rb_openai_api_key'] );
			$max_token     = absint( sanitize_text_field( $_POST['rb_openai_max_tokens'] ) );
			$temperature   = floatval( sanitize_text_field( $_POST['rb_openai_temperature'] ) );
			$writing_style = sanitize_text_field( $_POST['rb_openai_writing_style'] );
			$language      = sanitize_text_field( $_POST['rb_openai_language'] );

			update_option( 'rb_openai', $rb_openai );
			update_option( 'rb_openai_api_key', trim( $api_key ) );
			update_option( 'rb_openai_max_tokens', $max_token );
			update_option( 'rb_openai_temperature', $temperature );
			update_option( 'rb_openai_writing_style', $writing_style );
			update_option( 'rb_openai_language', $language );

			wp_send_json_success( 'Save successfully!' );
		}

		public function settings_interface() {

			$rb_openai               = get_option( 'rb_openai' ) ? get_option( 'rb_openai' ) : false;
			$rb_openai_api_key       = get_option( 'rb_openai_api_key' ) ? get_option( 'rb_openai_api_key' ) : '';
			$rb_openai_max_tokens    = get_option( 'rb_openai_max_tokens' ) ? get_option( 'rb_openai_max_tokens' ) : 2000;
			$rb_openai_temperature   = get_option( 'rb_openai_temperature' ) ? get_option( 'rb_openai_temperature' ) : 0.8;
			$rb_openai_writing_style = get_option( 'rb_openai_writing_style' ) ? get_option( 'rb_openai_writing_style' ) : 'creative';
			$rb_openai_language      = get_option( 'rb_openai_language' ) ? get_option( 'rb_openai_language' ) : 'english';
			?>
			<div class="rb-panel-wrap">
				<div class="rb-panel-header">
					<div class="rb-panel-heading">
						<h1>
							<i class="dashicons dashicons-image-filter" aria-label="hidden"></i><?php echo esc_html__( 'Ruby OpenAI Assistant', 'foxiz-core' ); ?>
						</h1>
						<p class="sub-heading"><?php echo esc_html__( 'Powered by the cutting-edge OpenAI technology, Ruby OpenAI Assistant tool (GTP 3.5 Turbo) is designed to make content creation an effortless and inspiring experience.', 'foxiz-core' ); ?></p>
						<p class="token-info"><?php printf( esc_html__( 'To begin using OpenAI, Please generate your API key by following this %s', 'foxiz-core' ), '<a href="//platform.openai.com/account/api-keys" target="_blank" >link</a>' ); ?></p>
					</div>
				</div>
				<div class="rb-panel">
					<form id="rb-openai" name="rb-openai" method="post" action="">
						<?php wp_nonce_field( 'rb-openai', '_wpnonce_ruby' ); ?>
						<div class="rb-form-item">
							<span class="rb-form-inline-label"><?php esc_html_e( 'OpenAI Assistant', 'rb-openai' ); ?></span>
							<label for="ai-assist" class="rb-switch">
								<input id="ai-assist" type="checkbox" class="rb-switch-input" name="rb_openai" <?php if ( $rb_openai ) {
									echo 'checked';
								} ?>>
								<span class="rb-switch-slider">
							</label>
						</div>
						<div class="rb-form-item">
							<label for="api-key"><?php esc_html_e( 'OpenAI API Key', 'rb-openai' ); ?>
								<span class="rb-form-tip"><i class="dashicons dashicons-info-outline"></i><span class="rb-form-tip-content"><?php printf( __( 'Generate your API key by following this <a href=%1$s target="_blank" >link</a>', 'foxiz-core' ), '//platform.openai.com/account/api-keys' ); ?></span></span>
							</label>
							<input id="api-key" type="text" name="rb_openai_api_key" placeholder="sk-xxxx..........................." value="<?php echo esc_attr( $rb_openai_api_key ); ?>">
						</div>
						<div class="rb-form-item">
							<label for="max-tokens"><?php esc_html_e( 'Max Token', 'rb-openai' ); ?>
								<span class="rb-form-tip"><i class="dashicons dashicons-info-outline"></i><span class="rb-form-tip-content"><?php esc_html_e( 'Maximum number of tokens in the generated output. It controls the length of the generated text. ', 'rb-openai' ); ?></span></span>
							</label>
							<input id="max-tokens" type="text" name="rb_openai_max_tokens" placeholder="200" value="<?php echo esc_attr( $rb_openai_max_tokens ); ?>">
						</div>
						<div class="rb-form-item">
							<label for="temperature"><?php esc_html_e( 'Temperature', 'rb-openai' ); ?>
								<span class="rb-form-tip"><i class="dashicons dashicons-info-outline"></i><span class="rb-form-tip-content"><?php esc_html_e( 'Controls the randomness of the output. Higher values (e.g., 1.2) make the output more random, while lower values (e.g., 0.8) make it more focused and deterministic.', 'rb-openai' ); ?></span></span>
							</label>
							<input id="temperature" type="text" name="rb_openai_temperature" placeholder="0.8" value="<?php echo esc_attr( $rb_openai_temperature ); ?>">
						</div>
						<div class="rb-form-item">
							<label for="writing-style"><?php esc_html_e( 'Writing Style', 'rb-openai' ); ?>
								<span class="rb-form-tip"><i class="dashicons dashicons-info-outline"></i><span class="rb-form-tip-content"><?php esc_html_e( 'Choose a writing style that aligns with the type of content you intend to publish and resonates with your target audience.', 'rb-openai' ); ?></span></span>
							</label>
							<?php $writing_styles = rb_openai_writing_style_selection(); ?>
							<select id="writing-style" name="rb_openai_writing_style">
								<?php foreach ( $writing_styles as $key => $label ): ?>
									<?php $selected = ( $key === $rb_openai_writing_style ) ? 'selected="selected"' : ''; ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php echo $selected; ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="rb-form-item">
							<label for="writing-style"><?php esc_html_e( 'Writing Language', 'rb-openai' ); ?>
								<span class="rb-form-tip"><i class="dashicons dashicons-info-outline"></i><span class="rb-form-tip-content"><?php esc_html_e( 'Choose a writing language to create content.', 'rb-openai' ); ?></span></span>
							</label>
							<?php $languages = rb_openai_languages_selection(); ?>
							<select id="writing-style" name="rb_openai_language">
								<?php foreach ( $languages as $key => $label ): ?>
									<?php $selected = ( $key === $rb_openai_language ) ? 'selected="selected"' : ''; ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php echo $selected; ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<button type="submit" name="action" class="rb-panel-button" id="rb-submit-api" value="update"><?php echo esc_html__( 'Save Changes', 'rb-openai' ); ?></button>
					</form>
					<div id="rb-form-response"></div>
				</div>
			</div>
			<?php
		}

		public function meta_configs( $configs ) {

			$configs['tabs'][] = [
				'id'     => 'section-openai',
				'title'  => esc_html__( 'OpenAI Assistant', 'foxiz' ),
				'icon'   => 'dashicons-image-filter',
				'fields' => [
					[
						'id'       => 'ai_assistant',
						'name'     => esc_html__( 'OpenAI Assistant', 'foxiz' ),
						'type'     => 'html_template',
						'callback' => 'rb_single_openai_template',
					],
				],
			];

			return $configs;
		}

		public function create_content() {

			if ( empty( $_POST['prompt'] ) ) {
				wp_send_json_error( 'Invalid prompt' );
			}

			$prompt = sanitize_text_field( $_POST['prompt'] );
			if ( ! empty( $_POST['type'] ) ) {
				$content_type = sanitize_text_field( $_POST['type'] );
			}

			$settings = [
				'prompt' => trim( $prompt ),
			];

			if ( ! empty( $content_type ) && 'content' === $content_type ) {
				$max_tokens = get_option( 'rb_openai_max_tokens' );
				if ( intval( $max_tokens ) < 2000 ) {
					$settings['max_tokens'] = 2000;
				} else {
					$settings['max_tokens'] = $max_tokens;
				}
			}

			$response = rb_openai_content_generator( $settings );
			wp_send_json( $response );
		}
	}
}

/** init */
RB_OPENAI_ASSISTANT::get_instance();