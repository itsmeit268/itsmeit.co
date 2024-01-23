<?php

/**
 *
 * @package    Email_Maketting
 * @subpackage Email_Maketting/admin
 * @author     itsmeit.co <buivanloi.2010@gmail.com>
 */

class Email_Maketting_Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;

        add_action('admin_menu', array($this, 'add_email_maketting_admin_menu'), 9);
        add_action('admin_init', array($this, 'register_and_build_fields'), 10);

        add_action('plugin_action_links_' . EMAIL_MAKETTING_PLUGIN_BASE, array($this, 'add_plugin_action_link'), 20);
    }

    public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/email-maketting-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/email-maketting-admin.js', array( 'jquery' ), $this->version, false );
    }

    public function add_plugin_action_link($links){
        $setting_link = '<a href="' . esc_url(get_admin_url()) . 'admin.php?page=email-maketting-settings">' . __('Settings', 'Email Maketting') . '</a>';
        $donate_link = '<a href="//itsmeit.co/donate.html" title="' . __('Donate', 'email-maketting') . '" target="_blank" style="font-weight:bold">' . __('Donate', 'email-maketting') . '</a>';
        array_unshift($links, $donate_link);
        array_unshift($links, $setting_link);
        return $links;
    }

    public function admin_user_email_table() {
        require_once plugin_dir_path(__FILE__) . 'partials/email-maketting-admin-user-table.php';
        $table = new Email_Maketting_Admin_User_Table();
        $table->prepare_items();
        $table->display();
    }

    public function admin_subscribe_verify() {
        require_once plugin_dir_path(__FILE__) . 'partials/email-maketting-admin-subscribe.php';
        $table = new Email_Maketting_Admin_Subscribe();
        $table->prepare_items();
        $table->display();
    }

    public function admin_subscribe_contact() {
        require_once plugin_dir_path(__FILE__) . 'partials/email-maketting-admin-contact.php';
        $table = new Email_Maketting_Admin_Contact();
        $table->prepare_items();
        $table->display();
    }

    public function add_email_maketting_admin_menu(){
        $menu_title = 'User Email';
        $submenu_title = 'Verify Email';

        // Thêm menu chính
        add_menu_page(
            $this->plugin_name,
            $menu_title,
            'manage_options',
            $this->plugin_name,
            array($this, 'admin_user_email_table'),
            'dashicons-email',
            6
        );

        // Thêm submenu
        add_submenu_page(
            $this->plugin_name,
            'Verify Email',
            $submenu_title,
            'manage_options',
            'verify-email',
            array($this, 'admin_subscribe_verify')
        );

        // Thêm submenu
        add_submenu_page(
            $this->plugin_name,
            'Contact Email',
            'Contact Email',
            'manage_options',
            'contact-email',
            array($this, 'admin_subscribe_contact')
        );

        // Thêm submenu
        add_submenu_page(
            $this->plugin_name,
            'Settings',
            'Settings',
            'manage_options',
            'email_marketing_general_settings',
            array($this, 'email_marketing_settings_callback')
        );

        // Đổi tên menu chính
        global $menu;
        foreach ($menu as $key => $item) {
            if ($item[2] === $this->plugin_name) {
                $menu[$key][0] = 'Email Marketting';
                break;
            }
        }
    }

    public function register_and_build_fields() {
        add_settings_section(
            'email_marketing_general_section',
            '',
            array($this, 'email_marketing_display_general'),
            'email_marketing_general_settings'
        );

        add_settings_field(
            'email_marketing_enable',
            __('Enable/Disable', 'email-marketing' ),
            array($this, 'enable_plugin_callback'),
            'email_marketing_general_settings',
            'email_marketing_general_section',
            array(
                1 => 'Enabled',
                0 => 'Disabled',
            )
        );

        add_settings_field(
            'disable_sendmail',
            __('Disable Sendmail', 'email-marketing' ),
            array($this, 'disable_sendmail_callback'),
            'email_marketing_general_settings',
            'email_marketing_general_section',
            array(
                1 => 'Yes',
                0 => 'No',
            )
        );

        add_settings_field(
            'disable_popup_click',
            __('Disable Click Popup', 'email-marketing' ),
            array($this, 'disable_popup_callback'),
            'email_marketing_general_settings',
            'email_marketing_general_section',
            array(
                1 => 'Yes',
                0 => 'No',
            )
        );

        register_setting(
                'email_marketing_general_settings',
                'email_marketing_settings'
        );
    }

    public function email_marketing_display_general()
    {
        ?>
        <div class="email-marketing-admin-settings">
            <h3>These settings are applicable to all email marketing functionalities.</h3>
            <span>Author  : itsmeit.biz@gmail.com</span> |
            <span>Website : <a href="//itsmeit.co" target="_blank">itsmeit.co</a> | <a href="//itsmeit.biz"
                                                                                       target="_blank">itsmeit.biz</a></span>
            |
            <span>Link download/update: <a href="https://itsmeit.co/" target="_blank">Email marketing plugin</a></span>
        </div>
        <?php
    }

    public function email_marketing_settings_callback() {
        echo '<div class="wrap"><h1>' . __('General Settings', 'email-marketing') . '</h1>';
        settings_errors();
        echo '<form method="post" action="options.php">';
        settings_fields('email_marketing_general_settings');
        do_settings_sections('email_marketing_general_settings');
        submit_button();
        echo '</form></div>';
    }

    public function enable_plugin_callback($args) {
        $settings = get_option('email_marketing_settings', array());
        $selected = isset($settings['email_marketing_enable']) ? $settings['email_marketing_enable'] : '1';
        $html = '<select id="email_marketing_enable" name="email_marketing_settings[email_marketing_enable]" class="email_marketing_enable">';
        foreach ($args as $value => $label) {
            $html .= sprintf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label);
        }
        $html .= '</select>';
        $html .= '<p class="description">Enable or disable plugin (The email marketing will be ready when enabled).</p>';
        echo $html;
    }

    public function disable_sendmail_callback($args) {
        $settings = get_option('email_marketing_settings', array());
        $selected = isset($settings['disable_sendmail']) ? $settings['disable_sendmail'] : '0';
        $html = '<select id="disable_sendmail" name="email_marketing_settings[disable_sendmail]" class="disable_sendmail">';
        foreach ($args as $value => $label) {
            $html .= sprintf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label);
        }
        $html .= '</select>';
        $html .= '<p class="description">Disable email sending function, still collect registered users.</p>';
        echo $html;
    }

    public function disable_popup_callback($args) {
        $settings = get_option('email_marketing_settings', array());
        $selected = isset($settings['disable_click_popup']) ? $settings['disable_click_popup'] : '0';
        $html = '<select id="disable_click_popup" name="email_marketing_settings[disable_click_popup]" class="disable_click_popup">';
        foreach ($args as $value => $label) {
            $html .= sprintf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label);
        }
        $html .= '</select>';
        $html .= '<p class="description">Disable popup click function download button.</p>';
        echo $html;
    }
}
