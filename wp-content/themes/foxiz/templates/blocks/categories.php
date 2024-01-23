<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'foxiz_get_categories_1' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false|string
	 */
	function foxiz_get_categories_1( $settings = [] ) {

		$settings = wp_parse_args( $settings, [
			'uuid'       => '',
			'name'       => 'categories_1',
			'categories' => [],
		] );

		if ( empty( $settings['followed'] ) || '1' !== (string) $settings['followed'] ) {
			$settings['display_mode'] = 'direct';
		}

		$settings['classes'] = 'block-categories block-categories-1';
		if ( empty( $settings['columns'] ) ) {
			$settings['columns'] = 4;
		}
		if ( empty( $settings['column_gap'] ) ) {
			$settings['column_gap'] = 10;
		}

		$params = foxiz_get_category_block_params( $settings );
		if ( empty( $settings['display_mode'] ) ) {
			$settings['classes'] .= ' is-ajax-categories';
			foxiz_categories_localize_script( $params );
		}

		ob_start();
		foxiz_block_open_tag( $settings );
		if ( foxiz_is_elementor_active() && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			foxiz_live_get_categories_1( $params );
		} else {
			if ( empty( $settings['display_mode'] ) ) {
				echo '<div class="block-loader">' . foxiz_get_svg( 'loading', '', 'animation' ) . '</div>';
			} else {
				foxiz_live_get_categories_1( $params );
			}
		}
		foxiz_block_close_tag();

		return ob_get_clean();
	}
}

if ( ! function_exists( 'foxiz_live_get_categories_1' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false|string
	 */
	function foxiz_live_get_categories_1( $settings = [] ) {

		$category_ids = foxiz_merge_saved_categories( $settings );
		?>
		<div class="block-inner">
			<?php foreach ( $category_ids as $category_id ) :
				$settings['cid'] = $category_id;
				foxiz_category_item_1( $settings );
			endforeach; ?>
		</div>
	<?php }
}

if ( ! function_exists( 'foxiz_get_categories_2' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false|string
	 */
	function foxiz_get_categories_2( $settings = [] ) {

		$settings = wp_parse_args( $settings, [
			'uuid'       => '',
			'name'       => 'categories_2',
			'categories' => [],
		] );

		if ( empty( $settings['followed'] ) || '1' !== (string) $settings['followed'] ) {
			$settings['display_mode'] = 'direct';
		}

		$settings['classes'] = 'block-categories block-categories-2';

		if ( ! empty( $settings['gradient'] ) && '-1' === (string) $settings['gradient'] ) {
			$settings['classes'] .= ' no-gradient';
		}
		if ( empty( $settings['columns'] ) ) {
			$settings['columns'] = 4;
		}
		if ( empty( $settings['column_gap'] ) ) {
			$settings['column_gap'] = 5;
		}

		$params = foxiz_get_category_block_params( $settings );
		if ( empty( $settings['display_mode'] ) ) {
			$settings['classes'] .= ' is-ajax-categories';
			foxiz_categories_localize_script( $params );
		}

		ob_start();
		foxiz_block_open_tag( $settings );
		if ( class_exists( 'Elementor\\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			foxiz_live_get_categories_2( $params );
		} else {
			if ( empty( $settings['display_mode'] ) ) {
				echo '<div class="block-loader">' . foxiz_get_svg( 'loading', '', 'animation' ) . '</div>';
			} else {
				foxiz_live_get_categories_2( $params );
			}
		}
		foxiz_block_close_tag();

		return ob_get_clean();
	}
}

if ( ! function_exists( 'foxiz_live_get_categories_2' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false|string
	 */
	function foxiz_live_get_categories_2( $settings = [] ) {

		$category_ids = foxiz_merge_saved_categories( $settings );
		?>
		<div class="block-inner">
			<?php foreach ( $category_ids as $category_id ) :
				$settings['cid'] = $category_id;
				foxiz_category_item_2( $settings );
			endforeach; ?>
		</div>
	<?php }
}

if ( ! function_exists( 'foxiz_get_categories_3' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false|string
	 */
	function foxiz_get_categories_3( $settings = [] ) {

		$settings = wp_parse_args( $settings, [
			'uuid'       => '',
			'name'       => 'categories_3',
			'categories' => [],
		] );

		if ( empty( $settings['followed'] ) || '1' !== (string) $settings['followed'] ) {
			$settings['display_mode'] = 'direct';
		}

		$settings['classes'] = 'block-categories block-categories-3';
		if ( ! empty( $settings['gradient'] ) && '-1' === (string) $settings['gradient'] ) {
			$settings['classes'] .= ' no-gradient';
		}
		if ( empty( $settings['columns'] ) ) {
			$settings['columns'] = 4;
		}
		if ( empty( $settings['column_gap'] ) ) {
			$settings['column_gap'] = 5;
		}

		$params = foxiz_get_category_block_params( $settings );
		if ( empty( $settings['display_mode'] ) ) {
			$settings['classes'] .= ' is-ajax-categories';
			foxiz_categories_localize_script( $params );
		}

		ob_start();
		foxiz_block_open_tag( $settings );
		if ( class_exists( 'Elementor\\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			foxiz_live_get_categories_3( $params );
		} else {
			if ( empty( $settings['display_mode'] ) ) {
				echo '<div class="block-loader">' . foxiz_get_svg( 'loading', '', 'animation' ) . '</div>';
			} else {
				foxiz_live_get_categories_3( $params );
			}
		}
		foxiz_block_close_tag();

		return ob_get_clean();
	}
}

if ( ! function_exists( 'foxiz_live_get_categories_3' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false|string
	 */
	function foxiz_live_get_categories_3( $settings = [] ) {

		$category_ids = foxiz_merge_saved_categories( $settings );
		?>
		<div class="block-inner">
			<?php foreach ( $category_ids as $category_id ) :
				$settings['cid'] = $category_id;
				foxiz_category_item_3( $settings );
			endforeach; ?>
		</div>
	<?php }
}

if ( ! function_exists( 'foxiz_get_categories_4' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false|string
	 */
	function foxiz_get_categories_4( $settings = [] ) {

		$settings = wp_parse_args( $settings, [
			'uuid'       => '',
			'name'       => 'categories_4',
			'categories' => [],
		] );

		if ( empty( $settings['followed'] ) || '1' !== (string) $settings['followed'] ) {
			$settings['display_mode'] = 'direct';
		}

		$settings['classes'] = 'block-categories block-categories-4';
		if ( empty( $settings['columns'] ) ) {
			$settings['columns'] = 4;
		}
		if ( empty( $settings['column_gap'] ) ) {
			$settings['column_gap'] = 10;
		}

		$params = foxiz_get_category_block_params( $settings );
		if ( empty( $settings['display_mode'] ) ) {
			$settings['classes'] .= ' is-ajax-categories';
			foxiz_categories_localize_script( $params );
		}

		ob_start();
		foxiz_block_open_tag( $settings );
		if ( class_exists( 'Elementor\\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			foxiz_live_get_categories_4( $params );
		} else {
			if ( empty( $settings['display_mode'] ) ) {
				echo '<div class="block-loader">' . foxiz_get_svg( 'loading', '', 'animation' ) . '</div>';
			} else {
				foxiz_live_get_categories_4( $params );
			}
		}
		foxiz_block_close_tag();

		return ob_get_clean();
	}
}

if ( ! function_exists( 'foxiz_live_get_categories_4' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false|string
	 */
	function foxiz_live_get_categories_4( $settings = [] ) {

		$category_ids = foxiz_merge_saved_categories( $settings );
		?>
		<div class="block-inner">
			<?php foreach ( $category_ids as $category_id ) :
				$settings['cid'] = $category_id;
				foxiz_category_item_4( $settings );
			endforeach; ?>
		</div>
	<?php }
}

if ( ! function_exists( 'foxiz_get_categories_5' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false|string
	 */
	function foxiz_get_categories_5( $settings = [] ) {

		$settings = wp_parse_args( $settings, [
			'uuid'       => '',
			'name'       => 'categories_1',
			'categories' => [],
		] );

		if ( empty( $settings['followed'] ) || '1' !== (string) $settings['followed'] ) {
			$settings['display_mode'] = 'direct';
		}

		$settings['classes'] = 'block-categories block-categories-5';
		if ( empty( $settings['columns'] ) ) {
			$settings['columns'] = 4;
		}
		if ( empty( $settings['column_gap'] ) ) {
			$settings['column_gap'] = 10;
		}

		$params = foxiz_get_category_block_params( $settings );
		if ( empty( $settings['display_mode'] ) ) {
			$settings['classes'] .= ' is-ajax-categories';
			foxiz_categories_localize_script( $params );
		}

		ob_start();
		foxiz_block_open_tag( $settings );
		if ( class_exists( 'Elementor\\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			foxiz_live_get_categories_5( $params );
		} else {
			if ( empty( $settings['display_mode'] ) ) {
				echo '<div class="block-loader">' . foxiz_get_svg( 'loading', '', 'animation' ) . '</div>';
			} else {
				foxiz_live_get_categories_5( $params );
			}
		}
		foxiz_block_close_tag();

		return ob_get_clean();
	}
}

if ( ! function_exists( 'foxiz_live_get_categories_5' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false|string
	 */
	function foxiz_live_get_categories_5( $settings = [] ) {

		$category_ids = foxiz_merge_saved_categories( $settings );
		?>
		<div class="block-inner">
			<?php foreach ( $category_ids as $category_id ) :
				$settings['cid'] = $category_id;
				foxiz_category_item_5( $settings );
			endforeach; ?>
		</div>
	<?php }
}