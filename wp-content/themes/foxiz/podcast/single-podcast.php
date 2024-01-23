<?php
/** The template for displaying the single podcast */
get_header();

if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		foxiz_podcast_render_single();
	endwhile;
endif;

/** get footer */
get_footer();