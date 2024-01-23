<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://itsmeit.co
 * @since      1.0.0
 *
 * @package    Prevent_Spam_Register
 * @subpackage Prevent_Spam_Register/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Prevent_Spam_Register
 * @subpackage Prevent_Spam_Register/public
 * @author     itsmeit.co <itsmeit.biz@gmail.com>
 */
class Prevent_Spam_Register_Public {

    /**
     * @var $plugin_name
     */
	private $plugin_name;

    /**
     * @var $version
     */
	private $version;

    /**
     * Prevent_Spam_Register_Public constructor.
     * @param $plugin_name
     * @param $version
     */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
        add_action( 'register_form', [$this, 'prevent_spam_form_register'], 10, 2 );
	}

	public function enqueue_styles() {}
	public function enqueue_scripts() {}

    public function prevent_spam_form_register(){
        $curent_url = parse_url( home_url( $_SERVER['REQUEST_URI'] ), PHP_URL_QUERY );
        parse_str( $curent_url, $params );
        if (isset( $params['action'] ) && $params['action'] === 'register' ) {
            $options = get_option('prevent_spam_settings', array());
            wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/prevent-spam-register.css', [], $this->version, 'all' );
            wp_enqueue_script( 'prevent_spam_form_register', plugin_dir_url( __FILE__ ) . 'js/prevent-spam-register.js', ['jquery'], $this->version, true );
            wp_localize_script( 'prevent_spam_form_register', 'prevent_spam_vars', [
                'block_list' => !empty($options['block_list']) ? $options['block_list']: '',
                'exclude_list' => !empty($options['exclude_list']) ? $options['exclude_list']: '',
                'redirect' => !empty($options['redirect']) ? $options['redirect']: '0',
                'redirect_url' => !empty($options['redirect_url']) ? $options['redirect_url']: get_bloginfo('url')
            ] );
        }
    }
}
