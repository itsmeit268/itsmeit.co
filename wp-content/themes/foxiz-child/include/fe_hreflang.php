<?php

add_filter( 'rank_math/schema/language', function() {
    return 'vi';
} );

add_filter( "rank_math/opengraph/facebook/og_locale", function( $locale ) {
    return 'vi';
});
add_action('wp_head', 'add_hreflang_tags', 2);

function add_hreflang_tags(){
    if (is_home() || is_front_page()) {
        echo PHP_EOL;
        echo '<link rel="alternate" hreflang="vi" href="https://itsmeit.co/"/>' . PHP_EOL;
        echo '<link rel="alternate" hreflang="en" href="https://itsmeit.biz/"/>' . PHP_EOL;
        echo '<link rel="alternate" hreflang="x-default" href="https://itsmeit.biz/"/>' . PHP_EOL;
    }

    if (is_category()) {
        $category = get_queried_object();
        $en_hreflang = get_term_meta($category->term_id, '_c_hreflang', true);
        $category_url = get_category_link($category->term_id);
        echo PHP_EOL;
        echo '<link rel="alternate" hreflang="vi" href="' . esc_url($category_url) . '"/>' . PHP_EOL;
        if ($en_hreflang) {
            echo '<link rel="alternate" hreflang="en" href="' . esc_url($en_hreflang) . '"/>' . PHP_EOL;
            echo '<link rel="alternate" hreflang="x-default" href="'. esc_url($en_hreflang) .'"/>' . PHP_EOL;
        }
    }
    if (is_singular('post')) {
        $en_hreflang = get_post_meta(get_the_ID(), '_hreflang', true);
        echo PHP_EOL;
        echo '<link rel="alternate" hreflang="vi" href="' . esc_url(get_permalink(get_the_ID())) . '"/>' . PHP_EOL;
        if ($en_hreflang) {
            echo '<link rel="alternate" hreflang="en" href="' . $en_hreflang . '"/>' . PHP_EOL;
            echo '<link rel="alternate" hreflang="x-default" href="'. $en_hreflang .'"/>' . PHP_EOL;
        }
    }

    if (is_singular('page') && !is_home() && !is_front_page()) {
        $en_hreflang = get_post_meta(get_the_ID(), '_hreflang', true);
        echo PHP_EOL;
        echo '<link rel="alternate" hreflang="vi" href="' . esc_url(get_permalink(get_the_ID())) . '"/>' . PHP_EOL;
        if ($en_hreflang) {
            echo '<link rel="alternate" hreflang="en" href="' . $en_hreflang . '"/>' . PHP_EOL;
            echo '<link rel="alternate" hreflang="x-default" href="'. $en_hreflang .'"/>' . PHP_EOL;
        }
    }
}