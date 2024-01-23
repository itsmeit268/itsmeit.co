<?php

add_action('wp_enqueue_scripts', 'itsmeit_customstyle');

function itsmeit_customstyle() {
    $user = wp_get_current_user();
    wp_enqueue_style('itsmeit-theme', get_theme_file_uri('/itsmeit/css/itsmeit-theme.css'), array(), FOXIZ_THEME_VERSION, 'all');
    wp_enqueue_script('itsmeit-theme', get_theme_file_uri('/itsmeit/js/theme-itsmeit.js'), array('jquery'), FOXIZ_THEME_VERSION, true);
    wp_localize_script('itsmeit-theme', 'ims', array(
        'ajax_url' => admin_url( 'admin-ajax.php'),
        'display_name' => isset($user->display_name) ? $user->display_name: ''
    ));
    if (is_singular('post')) {
        if (get_the_ID() == '140') {
            wp_enqueue_script('asciinema-player', get_theme_file_uri('/itsmeit/js/asciinema-player.js'), array('jquery'), FOXIZ_THEME_VERSION, true);
            wp_enqueue_style('asciinema-player', get_theme_file_uri('/itsmeit/css/asciinema-player.css'), array(), FOXIZ_THEME_VERSION, 'all');
        }
        wp_enqueue_style('single-post', get_theme_file_uri('/itsmeit/css/single-post.css'), array(), FOXIZ_THEME_VERSION, 'all');
        wp_enqueue_script('single-post', get_theme_file_uri('/itsmeit/js/single-post.js'), array('jquery'), FOXIZ_THEME_VERSION, true);

        if ((!vip_level() || !standard_level() || !pro_level())) {
            wp_enqueue_script('ads-check', get_theme_file_uri('/itsmeit/js/ads_itsmeit.js'), array('jquery'), FOXIZ_THEME_VERSION, true);
        }
        wp_enqueue_script('dmca', get_theme_file_uri('/itsmeit/js/DMCABadgeHelper.min.js'), array('jquery'), FOXIZ_THEME_VERSION, true);
    }
}