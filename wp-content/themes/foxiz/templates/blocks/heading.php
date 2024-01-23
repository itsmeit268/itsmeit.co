<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'foxiz_get_heading' ) ) {
	/**
	 * @param $attrs
	 *
	 * @return string
	 * heading
	 */
	function foxiz_get_heading( $attrs = [] ) {

		$settings = wp_parse_args( $attrs, [
			'uuid'          => '',
			'layout'        => '',
			'title'         => '',
			'link'          => [],
			'html_tag'      => '',
			'tagline'       => '',
			'tagline_arrow' => '',
			'classes'       => '',
		] );

		if ( empty( $settings['title'] ) ) {
			return false;
		}

		/** dynamic title */
		if ( is_search() ) {
			$settings['title'] = str_replace( [
				'{search}',
				'{archive}',
			], get_search_query( 's' ), $settings['title'] );
		} elseif ( is_author() ) {
			$settings['title'] = str_replace( [
				'{author}',
				'{archive}',
			], get_queried_object()->display_name, $settings['title'] );
		} elseif ( is_archive() ) {
			$settings['title'] = str_replace( [
				'{archive}',
				'{category}',
				'{tag}',
				'{taxonomy}',
			], get_queried_object()->name, $settings['title'] );
		}

		$class_name = 'block-h';
		if ( empty( $settings['layout'] ) ) {
			$settings['layout'] = foxiz_get_option( 'heading_layout', 1 );
		}
		$class_name .= ' heading-layout-' . $settings['layout'];
		if ( ! empty( $settings['color_scheme'] ) ) {
			$class_name .= ' light-scheme';
		}

		$title_class_name = 'heading-title';
		if ( ! empty( $settings['classes'] ) ) {
			$title_class_name .= ' ' . $settings['classes'];
		}
		if ( empty( $settings['html_tag'] ) ) {
			$settings['html_tag'] = 'h3';
		}

		$output = '<div';
		if ( ! empty( $settings['uuid'] ) ) {
			$output .= ' id="' . esc_attr( $settings['uuid'] ) . '"';
		}
		$output .= ' class="' . esc_attr( $class_name ) . '">';
		$output .= '<div class="heading-inner">';
		$output .= '<' . $settings['html_tag'] . ' class="' . esc_attr( $title_class_name ) . '">';
		if ( ! empty( $settings['link']['url'] ) ) {
			$output .= foxiz_render_elementor_link( $settings['link'], wp_kses( $settings['title'], 'foxiz' ) );
		} else {
			$output .= '<span>' . wp_kses( $settings['title'], 'foxiz' ) . '</span>';
		}
		$output .= '</' . $settings['html_tag'] . '>';

		if ( ! empty( $settings['tagline'] ) ) {
			$output .= '<div class="heading-tagline h6">';
			if ( ! empty( $settings['link']['url'] ) ) {
				$output .= foxiz_render_elementor_link( $settings['link'], esc_html( $settings['tagline'] ), 'heading-tagline-label' );
			} else {
				$output .= '<span class="heading-tagline-label">' . esc_html( $settings['tagline'] ) . '</span>';
			}
			if ( ! empty( $settings['tagline_arrow'] ) ) {
				$output .= '<i class="rbi rbi-cright heading-tagline-icon" aria-hidden="true"></i>';
			}
			$output .= '</div>';
		}

		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}
}

if ( ! function_exists( 'foxiz_get_start_widget_heading' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return string
	 */
	function foxiz_get_start_widget_heading( $settings = [] ) {

		$settings = wp_parse_args( $settings, [
			'layout'   => '',
			'html_tag' => '',
		] );

		$class_name = 'block-h widget-heading';
		$class_name .= ' heading-layout-' . $settings['layout'];
		if ( empty( $settings['html_tag'] ) ) {
			$settings['html_tag'] = 'h4';
		}

		$output = '<div';
		$output .= ' class="' . esc_attr( $class_name ) . '">';
		$output .= '<div class="heading-inner">';
		$output .= '<' . $settings['html_tag'] . ' class="heading-title"><span>';

		return $output;
	}
}

if ( ! function_exists( 'foxiz_get_end_widget_heading' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return string
	 */
	function foxiz_get_end_widget_heading( $settings = [] ) {

		if ( empty( $settings['html_tag'] ) ) {
			$settings['html_tag'] = 'h4';
		}
		$output = '</span></' . $settings['html_tag'] . '>';
		$output .= '</div></div>';

		return $output;
	}
}