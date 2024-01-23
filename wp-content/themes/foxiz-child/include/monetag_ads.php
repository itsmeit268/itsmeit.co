<?php
add_action('wp_footer', 'monetag_ads');
function monetag_ads()
{
    if (!current_user_can('manage_options')) {
        if (!is_front_page()) {
            wp_enqueue_script('native-banner', get_theme_file_uri('/itsmeit/js/monetag/native_banner_and_check_adblock.js'), array('jquery'), FOXIZ_THEME_VERSION, true);
        }
        if (!wp_is_mobile() && !is_front_page()) {
            wp_enqueue_script('onclick', get_theme_file_uri('/itsmeit/js/monetag/onclick.js'), array('jquery'), FOXIZ_THEME_VERSION, true);
        }
    }
}