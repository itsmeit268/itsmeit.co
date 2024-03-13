<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Intelligent Link Custom
 * Plugin URI:        https://itsmeit.co/
 * Description:       Customize the Intelligent Link plugin.
 * Version:           1.0.0
 * Author:            itsmeit.co
 * Author URI:        https://itsmeit.co/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       intelligent-link
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

// Đảm bảo rằng hàm is_plugin_active không tồn tại trước khi thêm nó
if ( ! function_exists('is_plugin_active') ) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// Kiểm tra xem plugin chính có được kích hoạt hay không
if ( ! is_plugin_active('intelligent-link/intelligent-link.php') ) {
    return;
}

add_action('link_field_meta_box_before', 'link_field_meta_box_before');
add_action('intelligent_link_save_field_meta_box', 'intelligent_link_save_field_meta_box');
add_action('intelligent_link_delete_field_meta_box', 'intelligent_link_delete_field_meta_box');
add_filter('ilgl_href_vars', 'ilgl_href_vars');
add_filter('ilgl_prep_template_vars', 'ilgl_prep_template_vars');
add_filter('intelligent_link_template', 'intelligent_link_template');
add_filter('prep_display_mode', 'prep_display_mode');

function link_field_meta_box_before($post){ ?>
    <h2 class="list-h3-title">Point to download</h2>
    <div class="point_download">
        <?php $point = get_post_meta($post->ID, 'point_download', true); ?>
        <label for="point-to-download">Point:</label>
        <input type="number" id="point" name="point_download" placeholder="1000" min="0" max="50000" value="<?= esc_attr($point ? : false) ?>"/>
    </div>
<?php }

function intelligent_link_save_field_meta_box($post_id) {
    if (isset($_POST['point_download'])) {
        $point = intval(sanitize_text_field($_POST['point_download']));
        $point = max(0, min($point, 50000));
        update_post_meta($post_id, 'point_download', $point);
    }
}

function intelligent_link_delete_field_meta_box($post_id) {
    $post_type = get_post_type($post_id);
    if (metadata_exists('post', $post_id, 'point_download') &&
        in_array($post_type, array('post', 'product'))) {
        delete_post_meta($post_id, 'point_download');
    }
}

function ilgl_href_vars($href_vars) {
    $settings = get_option('email_marketing_settings', []);
    $href_vars['is_user_logged_in'] = is_user_logged_in();
    if (is_user_logged_in()) {
        $href_vars['count_down'] = 3;
        $href_vars['display_mode'] = 'progress';
    }
    if (!empty($settings)) {
        $href_vars['is_popup'] = isset($settings['disable_click_popup']) ? $settings['disable_click_popup'] : '0';
        $href_vars['logged_in_notice'] = __('Would you like to log in to skip the waiting time?', 'intelligent-link');
    }
    return $href_vars;
}

function ilgl_prep_template_vars($prep_template) {
    return $prep_template;
}

function intelligent_link_template($template) {
    $custom_template = dirname(__FILE__) . '/templates/custom_template.php';
    if (file_exists($custom_template)) {
        return $custom_template;
    }
    return $template;
}
