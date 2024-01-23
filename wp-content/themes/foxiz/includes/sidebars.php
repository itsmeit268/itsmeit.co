<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'foxiz_register_all_sidebars' ) ) {
	function foxiz_register_all_sidebars() {

		$settings = get_option( FOXIZ_TOS_ID );

		$heading = array(
			'layout'   => '1',
			'html_tag' => 'h4'
		);

		$footer_heading = array(
			'layout'   => '10',
			'html_tag' => 'h4'
		);

		$more_heading = array(
			'layout'   => '10',
			'html_tag' => 'h5'
		);

		if ( ! empty( $settings['widget_heading_tag'] ) ) {
			$heading['html_tag']        = $settings['widget_heading_tag'];
			$footer_heading['html_tag'] = $settings['widget_heading_tag'];
		}
		if ( ! empty( $settings['widget_heading_layout'] ) ) {
			$heading['layout'] = $settings['widget_heading_layout'];
		} elseif ( ! empty( $settings['heading_layout'] ) ) {
			$heading['layout'] = $settings['heading_layout'];
		}
		if ( ! empty( $settings['footer_widget_heading_layout'] ) ) {
			$footer_heading['layout'] = $settings['footer_widget_heading_layout'];
		} elseif ( ! empty( $settings['heading_layout'] ) ) {
			$footer_heading['layout'] = $settings['heading_layout'];
		}

		if ( ! empty( $settings['multi_sidebars'] ) && is_array( $settings['multi_sidebars'] ) ) {
			$data_sidebar = array();
			foreach ( $settings['multi_sidebars'] as $sidebar ) {
				if ( ! empty( $sidebar ) ) {
					array_push( $data_sidebar, array(
						'id'   => 'foxiz_ms_' . foxiz_convert_to_id( trim( $sidebar ) ),
						'name' => strip_tags( $sidebar ),
					) );
				}
			}

			foreach ( $data_sidebar as $sidebar ) {
				if ( ! empty( $sidebar['id'] ) && ! empty( $sidebar['name'] ) ) {
					register_sidebar( array(
						'id'            => $sidebar['id'],
						'name'          => $sidebar['name'],
						'description'   => esc_html__( 'A sidebar section of your site.', 'foxiz' ),
						'before_widget' => '<div id="%1$s" class="widget rb-section w-sidebar clearfix %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => foxiz_get_start_widget_heading( $heading ),
						'after_title'   => foxiz_get_end_widget_heading( $heading ),
					) );
				}
			};
		}

		register_sidebar( array(
			'id'            => 'foxiz_sidebar_default',
			'name'          => esc_html__( 'Standard Sidebar', 'foxiz' ),
			'description'   => esc_html__( 'The default sidebar of your site', 'foxiz' ),
			'before_widget' => '<div id="%1$s" class="widget rb-section w-sidebar clearfix %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => foxiz_get_start_widget_heading( $heading ),
			'after_title'   => foxiz_get_end_widget_heading( $heading ),
		) );
		register_sidebar( array(
			'id'            => 'foxiz_header_ad',
			'name'          => esc_html__( 'Header Advertising', 'foxiz' ),
			'description'   => esc_html__( 'Display widget ad at the bottom of the website header.', 'foxiz' ),
			'before_widget' => '<div id="%1$s" class="widget header-ad-widget rb-section %2$s">',
			'after_widget'  => '</div>',
		) );
		register_sidebar( array(
			'id'            => 'foxiz_sidebar_more',
			'name'          => esc_html__( 'More Menu Section', 'foxiz' ),
			'description'   => esc_html__( 'The submenu section when hovering on the more button.', 'foxiz' ),
			'before_widget' => '<div class="more-col"><div id="%1$s" class="rb-section clearfix %2$s">',
			'after_widget'  => '</div></div>',
			'before_title'  => foxiz_get_start_widget_heading( $more_heading ),
			'after_title'   => foxiz_get_end_widget_heading( $more_heading ),
		) );
		if ( 'shortcode' !== foxiz_get_option( 'footer_layout', false ) ) {
			register_sidebar( array(
				'id'            => 'foxiz_sidebar_fw_footer',
				'name'          => esc_html__( 'Footer - Top Full Width', 'foxiz' ),
				'description'   => esc_html__( 'The full width section at the top of the footer.', 'foxiz' ),
				'before_widget' => '<div id="%1$s" class="widget w-fw-footer rb-section clearfix %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => foxiz_get_start_widget_heading( $heading ),
				'after_title'   => foxiz_get_end_widget_heading( $heading ),
			) );
			register_sidebar( array(
				'id'            => 'foxiz_sidebar_footer_1',
				'name'          => esc_html__( 'Footer - Column 1', 'foxiz' ),
				'description'   => esc_html__( 'one of the columns of the footer area.', 'foxiz' ),
				'before_widget' => '<div id="%1$s" class="widget w-sidebar rb-section clearfix %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => foxiz_get_start_widget_heading( $footer_heading ),
				'after_title'   => foxiz_get_end_widget_heading( $footer_heading ),
			) );
			register_sidebar( array(
				'id'            => 'foxiz_sidebar_footer_2',
				'name'          => esc_html__( 'Footer - Column 2', 'foxiz' ),
				'description'   => esc_html__( 'one of the columns of the footer area.', 'foxiz' ),
				'before_widget' => '<div id="%1$s" class="widget w-sidebar rb-section clearfix %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => foxiz_get_start_widget_heading( $footer_heading ),
				'after_title'   => foxiz_get_end_widget_heading( $footer_heading ),
			) );
			register_sidebar( array(
				'id'            => 'foxiz_sidebar_footer_3',
				'name'          => esc_html__( 'Footer - Column 3', 'foxiz' ),
				'description'   => esc_html__( 'one of the columns of the footer area.', 'foxiz' ),
				'before_widget' => '<div id="%1$s" class="widget w-sidebar rb-section clearfix %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => foxiz_get_start_widget_heading( $footer_heading ),
				'after_title'   => foxiz_get_end_widget_heading( $footer_heading ),
			) );
			if ( empty( $settings['footer_layout'] ) || '3' !== (string) $settings['footer_layout'] ) {
				register_sidebar( array(
					'id'            => 'foxiz_sidebar_footer_4',
					'name'          => esc_html__( 'Footer - Column 4', 'foxiz' ),
					'description'   => esc_html__( 'one of the columns of the footer area.', 'foxiz' ),
					'before_widget' => '<div id="%1$s" class="widget w-sidebar rb-section clearfix %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => foxiz_get_start_widget_heading( $footer_heading ),
					'after_title'   => foxiz_get_end_widget_heading( $footer_heading ),
				) );
			}
			if ( ! empty( $settings['footer_layout'] ) && ( '5' === (string) $settings['footer_layout'] || '51' === (string) $settings['footer_layout'] ) ) {
				register_sidebar( array(
					'id'            => 'foxiz_sidebar_footer_5',
					'name'          => esc_html__( 'Footer - Column 5', 'foxiz' ),
					'description'   => esc_html__( 'one of the columns of the footer area.', 'foxiz' ),
					'before_widget' => '<div id="%1$s" class="widget w-sidebar rb-section clearfix %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => foxiz_get_start_widget_heading( $footer_heading ),
					'after_title'   => foxiz_get_end_widget_heading( $footer_heading ),
				) );
			}
		}
		register_sidebar( array(
			'id'            => 'foxiz_entry_top',
			'name'          => esc_html__( 'Single Content - Top Area', 'foxiz' ),
			'description'   => esc_html__( 'The section at the top of the single post content. It usually uses to display adverts', 'foxiz' ),
			'before_widget' => '<div id="%1$s" class="widget entry-widget clearfix %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => foxiz_get_start_widget_heading( $heading ),
			'after_title'   => foxiz_get_end_widget_heading( $heading ),
		) );
		register_sidebar( array(
			'id'            => 'foxiz_entry_bottom',
			'name'          => esc_html__( 'Single Content - Bottom Area', 'foxiz' ),
			'description'   => esc_html__( 'The section at the bottom of the single post content. It usually uses to display adverts or the post related shortcode.', 'foxiz' ),
			'before_widget' => '<div id="%1$s" class="widget entry-widget clearfix %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => foxiz_get_start_widget_heading( $heading ),
			'after_title'   => foxiz_get_end_widget_heading( $heading ),
		) );
	}
}