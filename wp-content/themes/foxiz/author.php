<?php
/** header */
get_header();
$foxiz_settings = foxiz_get_archive_page_settings( 'author_' );
foxiz_author_page_header( $foxiz_settings );
if ( have_posts() ) {
	foxiz_the_blog( $foxiz_settings );
} else {
	foxiz_blog_empty();
}
/** footer */
get_footer();