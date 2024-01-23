<?php

add_filter('style_loader_src', 'remove_query_version_css_js', 10, 2);
add_filter('script_loader_src', 'remove_query_version_css_js', 10, 2);
add_action('wp_print_scripts', 'remove_unused_script', 100);
add_action('wp_enqueue_scripts', 'remove_unused_script', 101);

function remove_query_version_css_js($src){
    if (strpos($src, '?ver='))
        $src = remove_query_arg('ver', $src);
    return $src;
}


function remove_print_scripts(){
    wp_dequeue_script('foxiz-personalize-js');
    if (!is_singular('post')) {
        wp_dequeue_script('fixedtoc-js');
        wp_dequeue_script('fixedtoc-js-js-extra');
        wp_dequeue_script('enlighterjs');
        wp_dequeue_style('enlighterjs');
    }
}


function remove_unused_script(){
    wp_dequeue_script('foxiz-personalize-js');
    if (!is_singular('post')) {
        wp_dequeue_script('fixedtoc-js');
        wp_dequeue_script('fixedtoc-js-js-extra');
        wp_dequeue_script('enlighterjs');
        wp_dequeue_style('enlighterjs');
    }
}

/**
 * Does not use passive listeners to improve scrolling performance (Lighthouse Report)
 * https://stackoverflow.com/questions/60357083/does-not-use-passive-listeners-to-improve-scrolling-performance-lighthouse-repo */
add_action('wp_head', function () {
    ?>
    <script>!function(e){"function"==typeof define&&define.amd?define(e):e()}(function(){var e,t=["scroll","wheel","touchstart","touchmove","touchenter","touchend","touchleave","mouseout","mouseleave","mouseup","mousedown","mousemove","mouseenter","mousewheel","mouseover"];if(function(){var e=!1;try{var t=Object.defineProperty({},"passive",{get:function(){e=!0}});window.addEventListener("test",null,t),window.removeEventListener("test",null,t)}catch(e){}return e}()){var n=EventTarget.prototype.addEventListener;e=n,EventTarget.prototype.addEventListener=function(n,o,r){var i,s="object"==typeof r&&null!==r,u=s?r.capture:r;(r=s?function(e){var t=Object.getOwnPropertyDescriptor(e,"passive");return t&&!0!==t.writable&&void 0===t.set?Object.assign({},e):e}(r):{}).passive=void 0!==(i=r.passive)?i:-1!==t.indexOf(n)&&!0,r.capture=void 0!==u&&u,e.call(this,n,o,r)},EventTarget.prototype.addEventListener._original=e}});</script>
    <?php
});
