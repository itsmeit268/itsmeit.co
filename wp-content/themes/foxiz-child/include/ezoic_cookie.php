<?php

add_action('init', function () {
    $cookieName = 'x-ez-wp-noads';
    if (!is_allow_show_ads()) {
        if (!isset($_COOKIE[$cookieName])) {
            setcookie('x-ez-wp-noads', '1', 0);
        }
    } else {
        if (isset($_COOKIE[$cookieName])) {
            setcookie($cookieName, '0', 1);
        }
    }
});

if (is_admin()) {
    /**
     * Clear cache ezoic after save post */
    add_action('save_post', 'clear_cache_after_save_post');
    function clear_cache_after_save_post($post_id){
        // Check post type
        $post_type = get_post_type($post_id);
        if ($post_type !== 'post') {
            return;
        }

        // Prepare data for API request
        $data = array(
            'url' => get_permalink($post_id),
        );

        // Send API request
        wp_remote_post(
            'https://api-gateway.ezoic.com/gateway/cdnservices/clearcache?developerKey=54dd98302a3c1a44bab508a73faeb3ee',
            array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                ),
                'body' => wp_json_encode($data),
            )
        );
    }
}