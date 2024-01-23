<?php

/**
 * @link       https://itsmeit.co
 * @package    Prevent_Spam_Register
 * @subpackage Prevent_Spam_Register/admin
 * @author     itsmeit.co <itsmeit.biz@gmail.com>
 */
class Prevent_Spam_Register_Admin {

    private $plugin_name;
    private $version;
    private $options = 'prevent_spam_settings';

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_action( 'admin_menu', array( $this, 'prevent_spam_admin_menu' ), 9 );
        add_action( 'admin_init', array( $this, 'prevent_spam_admin_fields' ) );
        add_action( 'plugin_action_links_' . PREVENT_SPAM_REGISTER_PLUGIN_BASE, array($this, 'prevent_spam_action_link'), 20 );
        add_action( 'register_post', array( &$this, 'prevent_spam_register_drop' ), 1, 3 );
    }

    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/prevent-spam-register-admin.css', array(), $this->version, 'all' );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/prevent-spam-register-admin.js', array( 'jquery' ), $this->version, false );
    }

    public function prevent_spam_action_link($links) {
        $setting_link = '<a href="' . esc_url(get_admin_url()) . 'admin.php?page=prevent-spam-register">' . __('Settings', 'prevent-spam-register') . '</a>';
        $donate_link = '<a href="//itsmeit.co" title="' . __('Donate Now', 'prevent-spam-register') . '" target="_blank" style="font-weight:bold">' . __('Donate', 'prevent-spam-register') . '</a>';
        array_unshift($links, $donate_link);
        array_unshift($links, $setting_link);
        return $links;
    }

    public function prevent_spam_admin_menu(){
        add_submenu_page(
            'tools.php',
            __('Prevent Spam Register', 'prevent-spam-register'),
            __('Prevent Spam Register', 'prevent-spam-register'),
            'manage_options',
            'prevent-spam-register',
            [$this,'prevent_spam_settings_callback'],
        );
    }

    public function prevent_spam_settings_callback() {
        echo '<div class="wrap"><h1>' . __('General Settings', 'prevent-spam-register') . '</h1>';
        settings_errors();
        echo '<form method="post" action="options.php">';
        settings_fields('prevent_spam_general_settings');
        do_settings_sections('prevent_spam_general_settings');
        submit_button();
        echo '</form></div>';
    }

    public function prevent_spam_admin_fields() {
        add_settings_section(
            'prevent_spam_general_section',
            '',
            array($this, 'prevent_spam_display_general'),
            'prevent_spam_general_settings'
        );

        add_settings_field(
            'prevent_spam_message',
            __('Message', 'prevent-spam-register'),
            array($this, 'prevent_spam_message_callback'),
            'prevent_spam_general_settings',
            'prevent_spam_general_section');

        add_settings_field(
            'prevent_spam_redirect',
            __('Redirect Blocked Users?', 'prevent-spam-register'),
            array($this, 'prevent_spam_redirect_callback'),
            'prevent_spam_general_settings',
            'prevent_spam_general_section');

        add_settings_field(
            'prevent_spam_blocked_list',
            __('BLocked List', 'prevent-spam-register'),
            array($this, 'prevent_spam_blocked_list_callback'),
            'prevent_spam_general_settings',
            'prevent_spam_general_section');

        add_settings_field(
            'prevent_spam_exclude_list',
            __('Exclude List', 'prevent-spam-register'),
            array($this, 'prevent_spam_exclude_list_callback'),
            'prevent_spam_general_settings',
            'prevent_spam_general_section');

        add_settings_field(
            'prevent_spam_delete_all_data',
            __('Delete All Aata', 'prevent-spam-register'),
            array($this, 'prevent_spam_delete_all_data_callback'),
            'prevent_spam_general_settings',
            'prevent_spam_general_section');

        register_setting(
            'prevent_spam_general_settings',
            $this->options
        );
    }

    public function prevent_spam_display_general() {
        ?>
        <div class="prevent-spam-admin-settings">
            <h3>These settings are applicable to all prevent spam register functionalities.</h3>
            <span>Author  : admin@itsmeit.co</span> |
            <span>Website : <a href="//itsmeit.co" target="_blank">itsmeit.co</a></span>
            |
            <span>Link download/update: <a href="https://itsmeit.co/" target="_blank">Prevent Spam Register</a></span>
        </div>
        <?php
    }

    public function prevent_spam_message_callback() {
        $settings = get_option($this->options, array());
        ?>
        <p class="description">Notification message to user when blocked.</p>
        <input type="text" id="endpoint" name="prevent_spam_settings[message]" placeholder="<strong>Error:</strong> Your email has been banned from registration."
               value="<?= esc_attr(!empty($settings['message']) ? $settings['message'] : false) ?>" style="width:500px"/>
        <?php
    }

    public function prevent_spam_redirect_callback(){
        $settings = get_option($this->options, array());
        ?>
        <table class="form-table">
            <tbody>
            <tr class="prevent_spam_redirect">
                <td style="padding: 2px 0">
                    <select name="prevent_spam_settings[redirect]" id="prevent-spam-redirect" class="prevent-spam-redirect">
                        <option value="1" <?php selected(isset($settings['redirect']) && $settings['redirect'] == '1'); ?>>Yes</option>
                        <option value="0" <?php selected(isset($settings['redirect']) && $settings['redirect'] == '0'); ?>>No</option>
                    </select>
                </td>
            </tr>
            <tr class="prevent_spam_redirect_url">
                <td class="prevent-spam-redirect_url" style="padding: 2px 0">
                    <label><p><?php esc_html_e( 'If you\'d rather redirect users to a custom URL, please check the box below. If you do, the message above will not show.', 'prevent-spam-register' ); ?></p></label>
                    <input type="text" id="prevent-spam-redirect_url" name="prevent_spam_settings[redirect_url]" placeholder="<?= get_bloginfo('url') .'/'?>"
                           value="<?= isset($settings['redirect_url']) ? ($settings['redirect_url'] == 'redirect_url' ? 0 : $settings['redirect_url']) : '' ?>" style="width:500px"/>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function prevent_spam_blocked_list_callback() {
        $settings = get_option($this->options, array());
        ?>
        <p class="description">The terms below will not be allowed during the registration process. You can add an email domain to block account registration. (ex: example.com)</p>
        <textarea id="prevent_spam_block_list" cols="56" rows="5" name="prevent_spam_settings[block_list]"><?php echo isset($settings["block_list"]) ? $settings["block_list"] : false; ?></textarea>
        <?php
    }

    public function prevent_spam_exclude_list_callback() {
        $settings = get_option($this->options, array());
        ?>
        <p class="description">List of email domains you wish to exclude from verification, such as gmail.com, yahoo.com, one domain per line.</p>
        <textarea id="prevent_spam_exclude_list" cols="56" rows="5" name="prevent_spam_settings[exclude_list]"><?php echo isset($settings["exclude_list"]) ? $settings["exclude_list"] : false; ?></textarea>
        <?php
    }

    public function prevent_spam_delete_all_data_callback() {
        $settings = get_option($this->options, array());
        ?>
        <p>
            <input type="checkbox" id="prevent_spam_settings[delete_data]" name="prevent_spam_settings[delete_data]" value="yes"
                <?php checked( isset($settings['delete_data']) && $settings['delete_data'] === 'yes', true ); ?> >
            <label for="prevent_spam_settings[delete_data]"><?php esc_html_e( 'Delete all data when removing the plugin?', 'prevent-spam-register' ); ?></label>
        </p>
        <?php
    }

    public function prevent_spam_register_drop( $sanitized_user_login, $user_email, $errors ) {
        $options = get_option($this->options, array());
        if (!empty($options['block_list'])) {

            $parts = explode('@', $user_email);
            $domain = end($parts);
            $banned_lists = explode("\r\n", $options['block_list']);

            if (in_array($domain, $banned_lists)) {
                $message = !empty($options['message']) ? $options['message'] : __('<strong>ERROR</strong>: Your email has been banned from registration.');
                $redirect = !empty($options['redirect']) ? $options['redirect'] : '0';

                foreach ($banned_lists as $banned_list) {
                    $banned_list = trim($banned_list);
                    if (stripos($user_email, $banned_list) !== false) {
                        $errors->add('invalid_email', $message);
                        if ($redirect === '1') {
                            wp_safe_redirect($options['redirect_url']);
                        } else {
                            return true;
                        }
                    }
                }
            }
        }
        return true;
    }
}
