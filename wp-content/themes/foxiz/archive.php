<?php
/** header */
get_header();

/** custom taxonomy support */
if ( is_tax() ) {
	$foxiz_settings = foxiz_get_tax_page_settings();
} else {
	$foxiz_settings = foxiz_get_archive_page_settings();
}

foxiz_archive_page_header( $foxiz_settings );
if ( have_posts() ) {
	foxiz_the_blog( $foxiz_settings );
} else {
	foxiz_blog_empty();
}
/** footer */
get_footer();