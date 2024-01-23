<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'foxiz_render_single_standard_7' ) ) {
	function foxiz_render_single_standard_7() {

		$classes       = array();
		$classes[]     = 'single-standard-7';
		$sidebar_name     = foxiz_get_single_setting( 'sidebar_name' );
		$line_length      = foxiz_get_option( 'single_post_line_length' );
		$sidebar_position = foxiz_get_single_sidebar_position();
		$crop_size        = foxiz_get_single_crop_size( 'foxiz_crop_o2' );

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
                <div class="rb-small-container edge-padding">
					<?php
					foxiz_single_breadcrumb();
					foxiz_single_entry_category();
					foxiz_single_title();
					foxiz_single_tagline();
					foxiz_single_header_meta();
					?>
                </div>
                <div class="rb-wide-container edge-padding">
	                <?php foxiz_single_standard_featured( $crop_size );
	                if ( foxiz_get_single_featured_caption() ) : ?>
		                <div class="single-caption-outer rb-container edge-padding">
			                <?php foxiz_single_featured_caption(); ?>
		                </div>
	                <?php endif; ?>
                </div>
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
            <div class="rb-container edge-padding">
	            <?php foxiz_single_footer(); ?>
            </div>
        </div>
		<?php
	}
}