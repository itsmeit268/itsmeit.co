<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'foxiz_get_category_block_params' ) ) {
	function foxiz_get_category_block_params( $settings ) {

		$params = shortcode_atts( [
			'uuid'         => '',
			'name'         => '',
			'followed'     => '',
			'crop_size'    => '',
			'title_tag'    => '',
			'follow'       => '',
			'count_posts'  => '',
			'display_mode' => '',
		], $settings );

		$ids = [];
		foreach ( $settings['categories'] as $item ) {
			if ( ! empty( $item['category'] ) ) {
				$category = get_category_by_slug( $item['category'] );
				if ( $category ) {
					$ids[] = $category->term_id;
				}
			}
		}

		$params['categories'] = implode( ',', $ids );

		return $params;
	}
}

if ( ! function_exists( 'foxiz_categories_localize_script' ) ) {
	/**
	 * @param $settings
	 */
	function foxiz_categories_localize_script( $settings ) {

		$js_settings = [
			'block_type' => 'category',
		];
		$localize    = 'foxiz-global';
		foreach ( $settings as $key => $val ) {
			if ( ! empty( $val ) ) {
				$js_settings[ $key ] = $val;
			}
		}
		wp_localize_script( $localize, $settings['uuid'], $js_settings );
	}
}

/**
 * @param $settings
 *
 * @return array
 */
if ( ! function_exists( 'foxiz_merge_saved_categories' ) ) {
	/**
	 * @param $settings
	 *
	 * @return array
	 */
	function foxiz_merge_saved_categories( $settings ) {

		$category_ids = [];
		if ( ! empty( $settings['followed'] ) && '1' === (string) $settings['followed'] ) {
			$category_ids = Foxiz_Personalize::get_instance()->get_categories_followed();
		}

		if ( ! empty( $settings['categories'] ) ) {
			$category_ids = array_merge( $category_ids, explode( ',', $settings['categories'] ) );
		}

		return array_unique( $category_ids );
	}
}

if ( ! function_exists( 'foxiz_render_follow_redirect' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false
	 */
	function foxiz_render_follow_redirect( $settings = [] ) {

		if ( empty( $settings['url'] ) ) {
			return false;
		}
		?>
		<div class="follow-redirect-wrap">
			<a href="<?php echo esc_url( $settings['url'] ); ?>" class="follow-redirect">
				<i class="rbi rbi-plus" aria-hidden="true"></i><span class="meta-text"><?php foxiz_html_e( 'Add More', 'foxiz' ); ?></span>
			</a>
		</div>
		<?php
	}
}

if ( ! function_exists( 'foxiz_category_count' ) ) {
	/**
	 * @param $category
	 */
	function foxiz_category_count( $category ) {

		if ( empty( $category->category_count ) ) {
			return;
		}

		echo '<span class="cbox-count is-meta">';
		if ( 1 < $category->category_count ) {
			echo esc_attr( $category->category_count ) . ' ' . foxiz_html__( 'Articles', 'foxiz' );
		} else {
			echo esc_attr( $category->category_count ) . ' ' . foxiz_html__( 'Article', 'foxiz' );
		}
		echo '</span>';
	}
}

if ( ! function_exists( 'foxiz_category_item_1' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false
	 */
	function foxiz_category_item_1( $settings = [] ) {

		if ( ! empty( $settings['cid'] ) ) {
			$category = get_category( $settings['cid'] );
		} elseif ( ! empty( $settings['slug'] ) ) {
			$category = get_category_by_slug( $settings['slug'] );
		}

		if ( empty( $category ) ) {
			return false;
		}

		if ( empty( $settings['title_tag'] ) ) {
			$settings['title_tag'] = 'h4';
		}
		if ( empty( $settings['crop_size'] ) ) {
			$settings['crop_size'] = 'foxiz_crop_g1';
		}

		$id    = $category->term_id;
		$link  = foxiz_get_term_link( $id );
		$metas = rb_get_term_meta( 'foxiz_category_meta', $id );

		$featured_array      = [];
		$featured_urls_array = [];

		if ( ! empty( $metas['featured_image'] ) ) {
			$featured_array = $metas['featured_image'];
		}
		if ( ! empty( $metas['featured_image_urls'] ) ) {
			$featured_urls_array = $metas['featured_image_urls'];
		} ?>
		<div class="<?php echo 'cbox cbox-1 is-cbox-' . $category->term_id; ?>">
			<div class="cbox-inner">
				<a class="cbox-featured" href="<?php echo esc_url( $link ); ?>"><?php foxiz_render_category_featured( $featured_array, $featured_urls_array, $settings['crop_size'] ); ?></a>
				<div class="cbox-body">
					<div class="cbox-content">
						<?php echo '<' . esc_attr( $settings['title_tag'] ) . ' class="cbox-title">';
						echo '<a class="p-url" href="' . esc_url( $link ) . '" rel="category">' . esc_html( $category->name ) . '</a>';
						echo '</' . esc_attr( $settings['title_tag'] ) . '>';
						if ( ! empty( $settings['count_posts'] ) && '1' === (string) $settings['count_posts'] ) {
							foxiz_category_count( $category );
						} ?>
					</div>
					<?php if ( ! empty( $settings['follow'] ) && '1' === (string) $settings['follow'] ) {
						foxiz_follow_trigger( [ 'id' => $id, 'type' => 'category' ] );
					} ?>
				</div>
			</div>
		</div>
	<?php }
}

if ( ! function_exists( 'foxiz_category_item_2' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false
	 */
	function foxiz_category_item_2( $settings = [] ) {

		if ( ! empty( $settings['cid'] ) ) {
			$category = get_category( $settings['cid'] );
		} elseif ( ! empty( $settings['slug'] ) ) {
			$category = get_category_by_slug( $settings['slug'] );
		}
		if ( empty( $category ) ) {
			return false;
		}
		if ( empty( $settings['title_tag'] ) ) {
			$settings['title_tag'] = 'h3';
		}
		if ( empty( $settings['crop_size'] ) ) {
			$settings['crop_size'] = 'foxiz_crop_g1';
		}

		$id                  = $category->term_id;
		$link                = foxiz_get_term_link( $id );
		$metas               = rb_get_term_meta( 'foxiz_category_meta', $id );
		$featured_array      = [];
		$featured_urls_array = [];
		if ( ! empty( $metas['featured_image'] ) ) {
			$featured_array = $metas['featured_image'];
		}
		if ( ! empty( $metas['featured_image_urls'] ) ) {
			$featured_urls_array = $metas['featured_image_urls'];
		}
		?>
		<div class="<?php echo 'cbox cbox-2 is-cbox-' . $category->term_id; ?>">
			<div class="cbox-inner">
				<a class="cbox-featured is-overlay" href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $category->name ) ?>"><?php foxiz_render_category_featured( $featured_array, $featured_urls_array, $settings['crop_size'] ); ?></a>
				<div class="cbox-overlay overlay-wrap light-scheme">
					<div class="cbox-body">
						<div class="cbox-content">
							<?php echo '<' . esc_attr( $settings['title_tag'] ) . ' class="cbox-title">';
							echo '<a class="p-url" href="' . esc_url( $link ) . '" rel="category">' . esc_html( $category->name ) . '</a>';
							echo '</' . esc_attr( $settings['title_tag'] ) . '>';
							if ( ! empty( $settings['count_posts'] ) && '1' === (string) $settings['count_posts'] ) {
								foxiz_category_count( $category );
							}
							?>
						</div>
						<?php if ( ! empty( $settings['follow'] ) && '1' === (string) $settings['follow'] ) {
							foxiz_follow_trigger( [ 'id' => $id, 'type' => 'category', 'classes' => 'is-light' ] );
						} ?>
					</div>
				</div>
			</div>
		</div>
	<?php }
}

if ( ! function_exists( 'foxiz_category_item_3' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false
	 */
	function foxiz_category_item_3( $settings = [] ) {

		if ( ! empty( $settings['cid'] ) ) {
			$category = get_category( $settings['cid'] );
		} elseif ( ! empty( $settings['slug'] ) ) {
			$category = get_category_by_slug( $settings['slug'] );
		}
		if ( empty( $category ) ) {
			return false;
		}
		$description = true;
		if ( empty( $settings['title_tag'] ) ) {
			$settings['title_tag'] = 'h3';
		}
		if ( empty( $settings['crop_size'] ) ) {
			$settings['crop_size'] = 'foxiz_crop_g2';
		}
		if ( ! empty( $settings['description'] ) && '-1' === (string) $settings['description'] ) {
			$description = false;
		}

		$id                  = $category->term_id;
		$link                = foxiz_get_term_link( $id );
		$metas               = rb_get_term_meta( 'foxiz_category_meta', $id );
		$featured_array      = [];
		$featured_urls_array = [];
		if ( ! empty( $metas['featured_image'] ) ) {
			$featured_array = $metas['featured_image'];
		}
		if ( ! empty( $metas['featured_image_urls'] ) ) {
			$featured_urls_array = $metas['featured_image_urls'];
		}
		?>
		<div class="<?php echo 'cbox cbox-3 is-cbox-' . $category->term_id; ?>">
			<div class="cbox-inner">
				<a class="cbox-featured is-overlay" href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $category->name ) ?>"><?php foxiz_render_category_featured( $featured_array, $featured_urls_array, $settings['crop_size'] ); ?></a>
				<div class="cbox-overlay overlay-wrap light-scheme">
					<div class="cbox-body">
						<div class="cbox-top cbox-content">
							<?php
							if ( ! empty( $settings['count_posts'] ) && '1' === (string) $settings['count_posts'] ) {
								foxiz_category_count( $category );
							}
							echo '<' . esc_attr( $settings['title_tag'] ) . ' class="cbox-title">';
							echo '<a class="p-url" href="' . esc_url( $link ) . '" rel="category">' . esc_html( $category->name ) . '</a>';
							echo '</' . esc_attr( $settings['title_tag'] ) . '>';
							?>
						</div>
						<?php if ( ! empty( $category->description ) && $description ): ?>
							<div class="cbox-center cbox-description">
								<?php echo wp_trim_words( $category->description, 25 ); ?>
							</div>
						<?php endif;
						if ( ! empty( $settings['follow'] ) && '1' === (string) $settings['follow'] ) {
							echo '<div class="cbox-bottom">';
							foxiz_follow_trigger( [ 'id' => $id, 'type' => 'category', 'classes' => 'is-light' ] );
							echo '</div>';
						} ?>
					</div>
				</div>
			</div>
		</div>
	<?php }
}

if ( ! function_exists( 'foxiz_category_item_4' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false
	 */
	function foxiz_category_item_4( $settings = [] ) {

		if ( ! empty( $settings['cid'] ) ) {
			$category = get_category( $settings['cid'] );
		} elseif ( ! empty( $settings['slug'] ) ) {
			$category = get_category_by_slug( $settings['slug'] );
		}

		if ( empty( $category ) ) {
			return false;
		}

		if ( empty( $settings['title_tag'] ) ) {
			$settings['title_tag'] = 'h3';
		}
		if ( empty( $settings['crop_size'] ) ) {
			$settings['crop_size'] = 'foxiz_crop_g1';
		}
		$id                  = $category->term_id;
		$link                = foxiz_get_term_link( $id );
		$metas               = rb_get_term_meta( 'foxiz_category_meta', $id );
		$featured_array      = [];
		$featured_urls_array = [];

		if ( ! empty( $metas['featured_image'] ) ) {
			$featured_array = $metas['featured_image'];
		}
		if ( ! empty( $metas['featured_image_urls'] ) ) {
			$featured_urls_array = $metas['featured_image_urls'];
		} ?>
		<div class="<?php echo 'cbox cbox-4 is-cbox-' . $category->term_id; ?>">
			<div class="cbox-inner">
				<?php if ( ! empty( $settings['follow'] ) && '1' === (string) $settings['follow'] ) {
					foxiz_follow_trigger( [ 'id' => $id, 'type' => 'category', 'classes' => 'is-light' ] );
				} ?>
				<a class="cbox-featured" href="<?php echo esc_url( $link ); ?>"><?php foxiz_render_category_featured( $featured_array, $featured_urls_array, $settings['crop_size'] ); ?></a>
				<div class="cbox-body">
					<div class="cbox-content">
						<?php
						if ( ! empty( $settings['count_posts'] ) && '1' === (string) $settings['count_posts'] ) {
							foxiz_category_count( $category );
						}
						echo '<' . esc_attr( $settings['title_tag'] ) . ' class="cbox-title">';
						echo '<a class="p-url" href="' . esc_url( $link ) . '" rel="category">' . esc_html( $category->name ) . '</a>';
						echo '</' . esc_attr( $settings['title_tag'] ) . '>';
						?>
					</div>
				</div>
			</div>
		</div>
	<?php }
}

if ( ! function_exists( 'foxiz_category_item_5' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false
	 */
	function foxiz_category_item_5( $settings = [] ) {

		if ( ! empty( $settings['cid'] ) ) {
			$category = get_category( $settings['cid'] );
		} elseif ( ! empty( $settings['slug'] ) ) {
			$category = get_category_by_slug( $settings['slug'] );
		}

		if ( empty( $category ) ) {
			return false;
		}

		if ( empty( $settings['title_tag'] ) ) {
			$settings['title_tag'] = 'h4';
		}
		if ( empty( $settings['crop_size'] ) ) {
			$settings['crop_size'] = 'foxiz_crop_g1';
		}
		$id                  = $category->term_id;
		$link                = foxiz_get_term_link( $id );
		$metas               = rb_get_term_meta( 'foxiz_category_meta', $id );
		$featured_array      = [];
		$featured_urls_array = [];

		if ( ! empty( $metas['featured_image'] ) ) {
			$featured_array = $metas['featured_image'];
		}
		if ( ! empty( $metas['featured_image_urls'] ) ) {
			$featured_urls_array = $metas['featured_image_urls'];
		} ?>
		<div class="<?php echo 'cbox cbox-5 is-cbox-' . $category->term_id; ?>">
			<div class="cbox-featured-holder">
				<?php if ( ! empty( $settings['follow'] ) && '1' === (string) $settings['follow'] ) : ?>
					<span class="cbox-featured"><?php foxiz_render_category_featured( $featured_array, $featured_urls_array, $settings['crop_size'] ); ?></span>
					<?php foxiz_follow_trigger( [
						'id'      => $id,
						'type'    => 'category',
						'classes' => 'is-light',
					] );
				else : ?>
					<a class="cbox-featured" href="<?php echo esc_url( $link ); ?>"><?php foxiz_render_category_featured( $featured_array, $featured_urls_array, $settings['crop_size'] ); ?></a>
				<?php endif; ?>
			</div>
			<div class="cbox-content">
				<?php echo '<' . esc_attr( $settings['title_tag'] ) . ' class="cbox-title">';
				echo '<a class="p-url" href="' . esc_url( $link ) . '" rel="category">' . esc_html( $category->name ) . '</a>';
				echo '</' . esc_attr( $settings['title_tag'] ) . '>';
				if ( ! empty( $settings['count_posts'] ) && '1' === (string) $settings['count_posts'] ) {
					foxiz_category_count( $category );
				} ?>
			</div>
		</div>
	<?php }
}

if ( ! function_exists( 'foxiz_category_item_6' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false
	 */
	function foxiz_category_item_6( $settings = [] ) {

		if ( empty( $settings['cid'] ) ) {

			return false;
		}

		$featured_array = [];
		$category       = get_category( $settings['cid'] );

		if ( empty( $settings['title_tag'] ) ) {
			$settings['title_tag'] = 'h4';
		}
		if ( empty( $settings['crop_size'] ) ) {
			$settings['crop_size'] = 'foxiz_crop_g1';
		}
		$id    = $category->term_id;
		$link  = foxiz_get_term_link( $id );
		$metas = rb_get_term_meta( 'foxiz_category_meta', $id );

		if ( ! empty( $metas['featured_image'] ) ) {
			$featured_array = $metas['featured_image'];
		} ?>
		<div class="<?php echo 'cbox cbox-6 is-cbox-' . $category->term_id; ?>">
			<div class="cbox-featured-holder">
				<a class="cbox-featured" href="<?php echo esc_url( $link ); ?>"><?php foxiz_render_category_featured( $featured_array, [], $settings['crop_size'] ); ?></a>
			</div>
			<div class="cbox-content">
				<?php echo '<' . esc_attr( $settings['title_tag'] ) . ' class="cbox-title">';
				echo '<a class="p-url" href="' . esc_url( $link ) . '" rel="category">' . esc_html( $category->name ) . '</a>';
				echo '</' . esc_attr( $settings['title_tag'] ) . '>';
				if ( ! empty( $settings['count_posts'] ) && '1' === (string) $settings['count_posts'] ) {
					foxiz_category_count( $category );
				} ?>
			</div>
			<?php
			if ( ! empty( $settings['follow'] ) ) {
				foxiz_follow_trigger( [ 'id' => $id, 'type' => 'category' ] );
			}
			?>
		</div>
	<?php }
}
