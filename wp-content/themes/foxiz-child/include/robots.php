<?php

add_filter( 'rank_math/frontend/robots', function( $robots ) {
    $url = home_url( $_SERVER['REQUEST_URI'] );
    if(strpos($url,"/search/") !== false ||
        strpos($url,"?s=") !== false ||
        substr($url, -9) === "/download" ||
        strpos($url,"404.php") !== false || is_404()
    )  {
        $robots["follow"] = 'nofollow';
    }
    if (strpos($url, "/comment-page-") !== false ||
        strpos($url, "#comments") !== false ||
        strpos($url,"/tag/") !== false
    ) {
        $robots = [
            "follow" => "nofollow",
            "index" => "noindex",
            "snippet" => "nosnippet",
            "archive" => "noarchive"
        ];
    }
    return $robots;
});

add_filter('wp_robots', function($robots) {
    if (is_admin() || strpos($_SERVER['REQUEST_URI'], wp_login_url()) !== false
        || strpos($_SERVER['REQUEST_URI'], '/admin_ma2405') !== false) {
        $robots = array('noindex' => true, 'nofollow' => true, 'nosnippet' => true, 'noarchive' => true);
    }
    return $robots;
});
