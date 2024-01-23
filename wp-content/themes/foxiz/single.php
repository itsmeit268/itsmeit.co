<?php
/** The template for displaying single page */
get_header();

if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		foxiz_single_post();
	endwhile;
endif;

/** get footer */
get_footer();