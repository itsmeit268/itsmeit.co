<?php
/** Don't load directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$foxiz_placeholder = foxiz_get_option( 'search_placeholder' );
if ( empty( $foxiz_placeholder ) ) {
	$foxiz_placeholder = foxiz_html__( 'Search Headlines, News...', 'foxiz' );
} ?>
<form role="search" method="get" class="search-form wp-block-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <div class="search-form-icon"><i class="rbi rbi-search" aria-hidden="true"></i></div>
    <label class="search-form-input">
        <span class="screen-reader-text"><?php foxiz_html_e( 'Search for:', 'foxiz' ) ?></span>
        <input type="search" class="search-field" placeholder="<?php echo esc_attr( $foxiz_placeholder ); ?>" value="<?php echo get_search_query(); ?>" name="s">
    </label>
    <div class="search-form-submit">
        <input type="submit" value="<?php foxiz_html_e( 'Search', 'foxiz' ); ?>">
    </div>
</form>