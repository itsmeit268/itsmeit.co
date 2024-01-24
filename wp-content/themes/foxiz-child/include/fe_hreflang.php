<?php

add_filter( 'rank_math/schema/language', function() {
    return 'en';
} );

add_filter( "rank_math/opengraph/facebook/og_locale", function( $locale ) {
    return 'en';
});

function polylang_language_selector($args = array()) {
    $languages = pll_the_languages($args);

    if (empty($languages)) {
        return;
    }

    echo '<div class="language-selector">';
    echo '<button class="language-selector-dropdown">';
    echo '<img class="flag-icon" src="' . $languages[pll_current_language('slug')]['flag'] . '" width="20" height="20" alt="' . $languages[pll_current_language('slug')]['name'] . ' Language"> ' . $languages[pll_current_language('slug')]['name'];
    echo '<i class="fas fa-caret-down"></i>';
    echo '</button>';

    echo '<div class="dropdown-menu">';

    foreach ($languages as $language) {
        echo '<button value="' . esc_url($language['url']) . '" class="dropdown-item">';
        echo '<img class="flag-icon" src="' . $language['flag'] . '" width="20" height="20" alt="' . $language['name'] . ' Language"> ' . $language['name'];
        echo '</button>';
    }

    echo '</div>';
    echo '</div>';
}

add_filter( 'pll_rel_hreflang_attributes', function( $hreflangs ) {
    $hreflangs['x-default'] = $hreflangs['en'];
    return $hreflangs;
} );