<?php

/** Loai bo cache sitemap*/
add_filter('rank_math/sitemap/enable_caching', '__return_false');
//add_filter( 'rank_math_clear_data_on_uninstall', '__return_true' );

include_once get_theme_file_path('include/ezoic_cookie.php');
include_once get_theme_file_path('include/robots.php');

add_filter('wp_mail_from_name', 'change_wp_mail_from_name');
function change_wp_mail_from_name($email){
    $email = "ItsmeIT Team";
    return $email;
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

add_filter('rest_authentication_errors', function($result) {
    if (true === $result || is_wp_error($result)) {
        return $result;
    }

    $current_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    if (strpos($current_url, 'wp-json/nextend-social-login/')) {
        return $result;
    }

    if (!is_user_logged_in()) {
        return new WP_Error('rest_not_logged_in', __('You are not currently logged in.'), array('status' => 401));
    }

    return $result;
});

add_action('wp_login', 'send_admin_login_email', 10, 2);

function send_admin_login_email($user_login, $user) {
    if (user_can($user, 'manage_options')) {
        $admin_email = get_option('admin_email');
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $user_browser = $_SERVER['HTTP_USER_AGENT'];

        $subject = 'Thông báo: Admin đăng nhập vào trang web';
        $message = 'Admin ' . $user_login . ' đã đăng nhập vào trang web vào lúc ' . date('Y-m-d H:i:s') . '. Địa chỉ IP: ' . $user_ip . '. Trình duyệt: ' . $user_browser;
        wp_mail($admin_email, $subject, $message);
    }
}

//Disable the new user notification sent to the site admin
function disable_new_user_notifications() {
    //Remove original use created emails
    remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
    remove_action( 'edit_user_created_user', 'wp_send_new_user_notifications', 10, 2 );

    //Add new function to take over email creation
    add_action( 'register_new_user', 'send_new_email_notifications' );
    add_action( 'edit_user_created_user', 'send_new_email_notifications', 10, 2 );
}
function send_new_email_notifications( $user_id, $notify = 'user' ) {
    if ( empty($notify) || $notify == 'admin' ) {
        return;
    } elseif( $notify == 'both' ){
        $notify = 'user';
    }

    $blogname = 'ItsmeIT';
    $subject = sprintf( '[%s] New User Registration', $blogname );
    wp_new_user_notification( $user_id, $notify, $subject );
}
add_action( 'init', 'disable_new_user_notifications' );
