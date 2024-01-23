<?php

/**
 * @link       https://itsmeit.co
 * @package    Safe_Comment_Content
 * @subpackage Safe_Comment_Content/public
 * @author     itsmeit | Technology Blogs <admin@itsmeit.co>
 */
class Safe_Comment_Content_Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('admin_menu', array($this, 'scc_admin_menu'), 9);
		add_action('admin_init', array($this, 'scc_admin_fields'));
		add_action('plugin_action_links_' . SAFE_COMMENT_CONTENT_BASE, array($this, 'css_action_link'), 20);
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/safe-comment-content-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/safe-comment-content-admin.js', array( 'jquery' ), $this->version, false );
	}

	public function css_action_link($links) {
		$setting_link = '<a href="' . esc_url(get_admin_url()) . 'admin.php?page=safe-comment-content">' . __('Settings', 'safe-comment-content') . '</a>';
		$donate_link = '<a href="//itsmeit.co" title="' . __('Donate Now', 'safe-comment-content') . '" target="_blank" style="font-weight:bold">' . __('Donate', 'safe-comment-content') . '</a>';
		array_unshift($links, $donate_link);
		array_unshift($links, $setting_link);
		return $links;
	}

    public function scc_admin_menu(){
        add_submenu_page(
            'tools.php',
            __('Safe Comment Content', 'prevent-spam-register'),
            __('Safe Comment Content', 'prevent-spam-register'),
            'manage_options',
            $this->plugin_name,
            [$this,'scc_settings_callback'],
        );
    }

	public function scc_settings_callback() {
		echo '<div class="wrap"><h1>' . __('General Settings', 'safe-comment-content') . '</h1>';
		settings_errors();
		echo '<form method="post" action="options.php">';
		settings_fields('scc_general_settings');
		do_settings_sections('scc_general_settings');
		submit_button();
		echo '</form></div>';
	}

	public function scc_admin_fields() {
		add_settings_section(
			'scc_general_section',
			'',
			array($this, 'scc_display_general'),
			'scc_general_settings'
		);

		add_settings_field(
			'scc_endpoint',
			__('Endpoint', 'safe-comment-content'),
			array($this, 'scc_endpoint_callback'),
			'scc_general_settings',
			'scc_general_section');

		add_settings_field(
			'scc_moderation',
			__('Comment Moderation', 'safe-comment-content'),
			array($this, 'scc_moderation_callback'),
			'scc_general_settings',
			'scc_general_section');

		register_setting(
			'scc_general_settings',
			'scc_settings'
		);
	}

	public function scc_display_general() {
		?>
		<div class="scc-admin-settings">
			<h3>These settings are applicable to all safe comment content functionalities.</h3>
			<span>Author  : admin@itsmeit.co</span> |
			<span>Website : <a href="//itsmeit.co" target="_blank">itsmeit.co</a></span>
			|
			<span>Link download/update: <a href="https://itsmeit.co/" target="_blank">Safe Comment Content</a></span>
		</div>
		<?php
	}

	public function scc_endpoint_callback() {
		$settings = get_option('scc_settings', array());
		?>
        <p class="description">The default endpoint for the link format is set to "1", which means that the link will be in the following format: domain.com/post/1.</p>
        <input type="text" id="endpoint" name="scc_settings[endpoint]" placeholder="1"
		       value="<?= esc_attr(!empty($settings['endpoint']) ? $settings['endpoint'] : false) ?>"/>
		<p class="description" style="color: red">If you make changes to the endpoint, it is necessary to navigate to Settings->Permalinks->Save in order to synchronize the endpoint.</p>
		<?php
	}

	public function scc_moderation_callback() {
		$settings = get_option('scc_settings', array());
		?>
        <p class="description">When a comment contains any of these words in the body, author name, URL, email, IP address, or user-agent string of the browser, the comment is encrypted. Each word or IP address should be placed on a separate line. The matching will be done for words within the content, so for instance, "crack" will be encoded as "cra**".</p>
        <textarea id="scc_moderation" cols="50" rows="5" name="scc_settings[scc_moderation]"><?php echo isset($settings["scc_moderation"]) ? $settings["scc_moderation"] : false; ?></textarea>
        <?php
	}
}
