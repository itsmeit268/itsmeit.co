<?php
/** Don't load directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** header */
get_header();
$foxiz_series_settings = foxiz_get_series_settings();
foxiz_series_page_header( $foxiz_series_settings );
if ( have_posts() ) {
	foxiz_podcast_blog( $foxiz_series_settings );
} else {
	foxiz_blog_empty();
}
/** footer */
get_footer();