<?php

/** Loai bo cache sitemap*/
add_filter('rank_math/sitemap/enable_caching', '__return_false');
//add_filter( 'rank_math_clear_data_on_uninstall', '__return_true' );

include_once get_theme_file_path('include/ezoic_cookie.php');
include_once get_theme_file_path('include/robots.php');

add_filter('wp_mail_from_name', 'change_wp_mail_from_name');
function change_wp_mail_from_name($original_email_from){
    $new_email_from_name = get_bloginfo('name');
    return $new_email_from_name;
}

if (!is_admin()) {
    include_once get_theme_file_path('include/custom_style.php');
    include_once get_theme_file_path('include/fe_hreflang.php');
    include_once get_theme_file_path('include/google_analytics.php');
    include_once get_theme_file_path('include/optimization.php');
    include_once get_theme_file_path('include/post_ads_shortcode.php');
} else {
    add_filter('rank_math/researches/tests', function ($tests, $type) {
        unset($tests['hasContentAI']);
        return $tests;
    }, 10, 2);
}

add_action('init', 'redirect_user_not_admin');

function redirect_user_not_admin() {
    if (!is_user_logged_in()) {
        return;
    }

    $manager = current_user_can('manage_options');
    $current_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    // Kiểm tra xem URL có chứa "/wp-admin/admin-ajax.php" hay không
    if (!$manager && strpos($current_url, 'wp-admin') && !strpos($current_url, 'admin-ajax.php')) {
        wp_redirect(get_bloginfo('url') . '/my-account.html');
        exit;
    }
}

// Vô hiệu hóa REST API
add_filter('rest_enabled', '_return_false');
add_filter('rest_jsonp_enabled', '_return_false');

function _return_false() {
    return false;
}

// Loại trừ URL cụ thể từ REST API
add_filter('rest_endpoints', 'exclude_specific_rest_endpoint');

function exclude_specific_rest_endpoint($endpoints) {
    // Đường dẫn URL cần loại trừ
    $excluded_url = '/nextend-social-login/v1/tiktok/redirect_uri';

    foreach ($endpoints as $route => $endpoint) {
        if (strpos($route, $excluded_url) !== false) {
            // Nếu đường dẫn URL khớp với đường dẫn cần loại trừ
            // Thì loại bỏ nó khỏi danh sách endpoint
            unset($endpoints[$route]);
        }
    }

    return $endpoints;
}
