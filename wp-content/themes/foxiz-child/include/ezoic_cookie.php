<?php

add_action('template_redirect', function () {
    $IP = array('127.0.0.1', '127.0.1.1', 'localhost');
    $cookieName = 'x-ez-wp-noads';

    if (current_user_can('manage_options') ||
        in_array($_SERVER['REMOTE_ADDR'], $IP)
    ) {
        // If the cookie doesn't exist create the cookie
        // 0 means a cookie expires at the end of the session (when the browser closes)
        if (!isset($_COOKIE[$cookieName])) {
            setcookie($cookieName, '1', 0);
        }
    } else {
        // If the cookie exists delete the cookie by setting the expire date-time to 1 in UNIX time
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