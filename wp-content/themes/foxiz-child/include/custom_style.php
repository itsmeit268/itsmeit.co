<?php

add_action('wp_enqueue_scripts', 'itsmeit_customstyle');

function itsmeit_customstyle(){
    global $post;
    $user = wp_get_current_user();
    wp_enqueue_style('itsmeit-theme', get_theme_file_uri('/itsmeit/css/itsmeit-theme.css'), array(), FOXIZ_THEME_VERSION, 'all');
    wp_enqueue_script('itsmeit-theme', get_theme_file_uri('/itsmeit/js/theme-itsmeit.js'), array('jquery'), FOXIZ_THEME_VERSION, true);
    wp_localize_script('itsmeit-theme', 'ims', array(
        'ajax_url' => admin_url( 'admin-ajax.php'),
        'display_name' => isset($user->display_name) ? $user->display_name: ''
    ));
    wp_enqueue_script('dmca', get_theme_file_uri('/itsmeit/js/DMCABadgeHelper.min.js'), array('jquery'), FOXIZ_THEME_VERSION, true);

    if (get_the_ID() == '140' || get_the_ID() == '23320') {
        wp_enqueue_script('asciinema-player', get_theme_file_uri('/itsmeit/js/asciinema-player.js'), array('jquery'), FOXIZ_THEME_VERSION, true);
        wp_enqueue_style('asciinema-playe', get_theme_file_uri('/itsmeit/css/asciinema-player.css'), array(), FOXIZ_THEME_VERSION, 'all');
    }

    if (is_single()) {
        wp_enqueue_style('single-post', get_theme_file_uri('/itsmeit/css/single-post.css'), array(), FOXIZ_THEME_VERSION, 'all');
        wp_enqueue_script('single-post', get_theme_file_uri('/itsmeit/js/single-post.js'), array('jquery'), FOXIZ_THEME_VERSION, true);
        wp_enqueue_script('ads-check', get_theme_file_uri('/itsmeit/js/ads_itsmeit.js'), array('jquery'), FOXIZ_THEME_VERSION, true);
    }

    if (is_page() || is_category() || is_search()) {
        $arr = array('10259','9622','10247','10278');
        if (isset($post->ID) && in_array($post->ID,$arr)){
            ?>
            <script>
                window.addEventListener("DOMContentLoaded", () => {
                    setTimeout(function () {
                        window.scrollBy({top: 100, left: 0, behavior : "smooth"})
                    }, 1500);
                });
            </script>
            <?php
        }
    }
}