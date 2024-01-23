<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'foxiz_render_single_standard_2' ) ) {
	function foxiz_render_single_standard_2() {

		$classes          = array();
		$classes[]        = 'single-standard-2';
		$sidebar_name     = foxiz_get_single_setting( 'sidebar_name' );
		$line_length      = foxiz_get_option( 'single_post_line_length' );
		$sidebar_position = foxiz_get_single_sidebar_position();
		$crop_size        = foxiz_get_single_crop_size( 'foxiz_crop_o2' );

		if ( ! has_post_thumbnail( get_the_ID() ) ) {
			$classes[] = 'no-sfeat';
		}
		if ( 'none' === $sidebar_position ) {
			$sidebar_name = false;
		}
		if ( empty( $sidebar_name ) || ! is_active_sidebar( $sidebar_name ) ) {
			$classes[] = 'without-sidebar';
		} else {
			$classes[] = 'is-sidebar-' . esc_attr( $sidebar_position );
		}
		if ( foxiz_get_single_sticky_sidebar() ) {
			$classes[] = 'sticky-sidebar';
		}
		if ( ! empty( $line_length ) ) {
			$classes[] = 'optimal-line-length';
		} ?>
        <div class="<?php echo join( ' ', $classes ); ?>">
			<?php foxiz_single_open_tag(); ?>
            <header class="single-header">
                <div class="single-header-inner">
                    <div class="rb-container edge-padding overlay-text">
						<?php
						foxiz_single_breadcrumb();
						foxiz_single_entry_category();
						foxiz_single_title( 'fw-headline' );
						foxiz_single_tagline( 'fw-tagline' );
						foxiz_single_header_meta();
						?>
						<?php foxiz_single_standard_featured( $crop_size ); ?>
                    </div>
                </div>
				<?php if ( foxiz_get_single_featured_caption() ) : ?>
                    <div class="single-caption-outer rb-container edge-padding">
						<?php foxiz_single_featured_caption(); ?>
                    </div>
				<?php endif; ?>
            </header>
            <div class="rb-container edge-padding">
                <div class="grid-container">
                    <div class="s-ct">
						<?php
						foxiz_single_content();
						foxiz_single_author_box();
						foxiz_single_next_prev();
						foxiz_single_comment();
						?>
                    </div>
					<?php foxiz_single_sidebar( $sidebar_name ); ?>
                </div>
            </div>
			<?php foxiz_single_close_tag(); ?>
            <div class="rb-container edge-padding single-footer">
				<?php foxiz_single_footer(); ?>
            </div>
        </div>
		<?php
	}
}