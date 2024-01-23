<?php
/** Don't load directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'foxiz_series_page_header' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false
	 */
	function foxiz_series_page_header( $settings = array() ) {

		if ( empty( $settings['category_header'] ) ) {
			foxiz_series_header_1( $settings );

			return false;
		}

		switch ( $settings['category_header'] ) {
			case '2' :
				foxiz_series_header_2( $settings );
				break;
			case '3' :
				foxiz_category_page_header_3( $settings );
				break;
			case 'none':
				break;
			default:
				foxiz_series_header_1( $settings );
		}

		return false;
	}
}
if ( ! function_exists( 'foxiz_podcast_single_breadcrumb' ) ) {
	/**
	 * @return false|string
	 */
	function foxiz_podcast_single_breadcrumb() {

		if ( ! foxiz_get_option( 'single_podcast_breadcrumb' ) ) {
			return false;
		}

		echo foxiz_get_breadcrumb( 's-breadcrumb' );

		return false;
	}
}

if ( ! function_exists( 'foxiz_podcast_icon' ) ) {
	function foxiz_podcast_icon() {

		if ( 'podcast' !== get_post_type() ) {
			return;
		}

		$icon = foxiz_get_option( 'podcast_custom_icon' );
		if ( ! empty( $icon['url'] ) ) {
			echo '<div class="entry-podcast-icon"><span class="podcast-icon-svg"></span></div>';
		} else {
			echo '<div class="entry-podcast-icon"><i class="rbi rbi-mic" aria-hidden="true"></i></div>';
		}
	}
}

if ( ! function_exists( 'foxiz_podcast_socials_overlay' ) ) {
	function foxiz_podcast_socials_overlay() {

		if ( 'podcast' !== get_post_type() || ! foxiz_get_option( 'podcast_socials_overlay' ) ) {
			return;
		}
		$socials = foxiz_get_listen_on_settings();
		if ( ! count( $socials ) ) {
			return;
		}
		$index = 1;
		?>
        <div class="podcast-social-overlay meta-bold">
			<?php foreach ( $socials as $social ) :
				if ( empty( $social['icon'] || empty( $social['url'] ) || empty( $social['label'] ) ) ) {
					continue;
				} else {
					$index ++;
				}
				if ( stripos( $social['icon'], 'rbi' ) ) {
					$icon_classes = 'rbi ' . trim( $social['icon'] );
				} else {
					$icon_classes = trim( $social['icon'] );
				}
				?>
            <a href="<?php echo esc_url( $social['url'] ); ?>" data-gravity="s" data-title="<?php echo foxiz_attr__( 'Listen on', 'foxiz' ) . ' ' . esc_attr( $social['label'] ); ?>" class="podcast-social-item" target="_blank" rel="nofollow">
                <i class="<?php echo esc_attr( $icon_classes ); ?>" aria-hidden="true"></i><span><?php echo esc_html( $social['label'] ); ?></span>
                </a><?php
				if ( $index > 2 ) {
					break;
				}
			endforeach; ?>
        </div>
		<?php
	}
}

if ( ! function_exists( 'foxiz_podcast_socials' ) ) {
	function foxiz_podcast_socials() {

		$socials = foxiz_get_listen_on_settings();
		if ( ! count( $socials ) ) {
			return;
		} ?>
        <div class="podcast-socials">
            <div class="podcast-social-label is-meta">
                <i class="rbi rbi-frequency"></i><span><?php foxiz_html_e( 'Listen on', 'foxiz' ); ?></span></div>
            <div class="podcast-social-list">
				<?php foreach ( $socials as $social ) :
					if ( empty( $social['icon'] || empty( $social['url'] ) || empty( $social['label'] ) ) ) {
						continue;
					}
					if ( stripos( $social['icon'], 'rbi' ) ) {
						$icon_classes = 'rbi ' . trim( $social['icon'] );
					} else {
						$icon_classes = trim( $social['icon'] );
					}
					?>
                    <a href="<?php echo esc_url( $social['url'] ); ?>" data-gravity="s" data-title="<?php echo foxiz_attr__( 'Listen on', 'foxiz' ) . ' ' . esc_attr( $social['label'] ); ?>" class="<?php echo 'podcast-social-item social-' . trim( $social['icon'] ); ?>" target="_blank" rel="nofollow">
                        <i class="<?php echo esc_attr( $icon_classes ); ?>"></i><span><?php echo esc_html( $social['label'] ); ?></span>
                    </a>
				<?php endforeach; ?>
            </div>
        </div>
		<?php
	}
}

if ( ! function_exists( 'foxiz_podcast_entry_meta_play' ) ) {
	function foxiz_podcast_entry_meta_play( $settings ) {

		$post_id = get_the_ID();

		if ( 'podcast' !== get_post_type( $post_id ) ) {
			return false;
		}

		$self_hosted_audio_id = rb_get_meta( 'audio_hosted', $post_id );

		$classes   = array();
		$classes[] = 'meta-el meta-play meta-bold';
		if ( ! empty( $settings['tablet_hide_meta'] ) && is_array( $settings['tablet_hide_meta'] ) && in_array( 'play', $settings['tablet_hide_meta'] ) ) {
			$classes[] = 'tablet-hide';
		}
		if ( ! empty( $settings['mobile_hide_meta'] ) && is_array( $settings['mobile_hide_meta'] ) && in_array( 'play', $settings['mobile_hide_meta'] ) ) {
			$classes[] = 'mobile-hide';
		}
		if ( empty( $settings['has_play_label'] ) ) {
			$classes[] = 'no-label';
		}
		?><span class="<?php echo implode( ' ', $classes ); ?>">
        <span class="meta-play-btn">
                <?php if ( ! empty( $self_hosted_audio_id ) ) :
	                echo foxiz_get_audio_hosted( array(
		                'src'      => wp_get_attachment_url( $self_hosted_audio_id ),
		                'autoplay' => false,
		                'preload'  => 'none',
		                'style'    => '',
		                'class'    => 'self-hosted-audio podcast-player meta-podcast-player',
	                ) );
                else : ?>
                    <a href="<?php echo get_the_permalink(); ?>" class="meta-play-redirect"><i class="rbi rbi-play"></i></a>
                <?php endif; ?>
            </span>
            <?php if ( ! empty( $settings['has_play_label'] ) ): ?>
                <span class="meta-play-label" data-pause="<?php foxiz_attr_e( 'PAUSE', 'foxiz' ); ?>" data-listen="<?php foxiz_attr_e( 'LISTEN', 'foxiz' ); ?>"><?php echo foxiz_html__( 'LISTEN', 'foxiz' ); ?></span>
            <?php endif; ?>
        </span>
		<?php
	}
}

if ( ! function_exists( 'foxiz_podcast_entry_player' ) ) {
	function foxiz_podcast_entry_player( $settings ) {

		if ( 'podcast' !== get_post_type() ) {
			return false;
		} ?>
        <div class="entry-player"><?php echo foxiz_get_audio_embed(); ?></div>
		<?php
	}
}

if ( ! function_exists( 'foxiz_podcast_featured' ) ) {
	/**
	 * @param array $settings
	 * render featured image
	 */
	function foxiz_podcast_featured( $settings = array() ) {

		$settings = wp_parse_args( $settings, array(
			'featured_classes' => '',
			'crop_size'        => '1536x1536',
			'format'           => ''
		) );

		$classes   = array();
		$classes[] = 'p-featured';
		if ( ! empty( $settings['featured_classes'] ) ) {
			$classes[] = $settings['featured_classes'];
		}
		?>
        <div class="<?php echo join( ' ', $classes ); ?>">
			<?php
			foxiz_entry_featured_image( $settings );
			if ( current_user_can( 'edit_posts' ) ) {
				if ( ! isset( $settings['edit_link'] ) ) {
					$settings['edit_link'] = foxiz_get_option( 'edit_link' );
				}
				if ( ! empty( $settings['edit_link'] ) ) {
					edit_post_link( esc_html__( 'edit', 'foxiz' ) );
				}
			} ?>
        </div>
	<?php }
}

if ( ! function_exists( 'foxiz_podcast_featured_only' ) ) {
	/**
	 * @param array $settings
	 */
	function foxiz_podcast_featured_only( $settings = array() ) {

		if ( ! empty( $settings['crop_size'] ) && foxiz_has_featured_image( $settings['crop_size'] ) ) : ?>
            <div class="feat-holder"><?php foxiz_podcast_featured( $settings ); ?></div>
		<?php endif;
	}
}

if ( ! function_exists( 'foxiz_podcast_featured_with_category' ) ) {
	/**
	 * @param array $settings
	 */
	function foxiz_podcast_featured_with_category( $settings = array() ) {

		if ( ! empty( $settings['crop_size'] ) && foxiz_has_featured_image( $settings['crop_size'] ) ) : ?>
            <div class="feat-holder">
				<?php
				foxiz_podcast_featured( $settings );
				foxiz_entry_top( $settings ); ?>
            </div>
		<?php endif;
	}
}

if ( ! function_exists( 'foxiz_podcast_title' ) ) {
	/**
	 * @param $settings
	 */
	function foxiz_podcast_title( $settings = array() ) {

		$classes = 'entry-title podcast-title';
		if ( empty( $settings['title_tag'] ) ) {
			$settings['title_tag'] = 'h3';
		}
		if ( ! empty( $settings['title_classes'] ) ) {
			$classes .= ' ' . $settings['title_classes'];
		}
		if ( ! empty( $settings['counter'] ) ) {
			$classes .= ' counter-el';
		}

		if ( ! empty( $settings['title_index'] ) && '1' === (string) $settings['title_index'] ) {
			$title_index = rb_get_meta( 'post_index' );
		}

		$classes    = apply_filters( 'foxiz_entry_title_classes', $classes, get_the_ID() );
		$post_title = get_the_title();
		$rel_attr   = 'bookmark';
		$link       = get_permalink();

		echo '<' . esc_attr( $settings['title_tag'] ) . ' class="' . esc_attr( $classes ) . '">';

		echo '';
		if ( ! empty( $settings['title_prefix'] ) ) {
			echo wp_kses( $settings['title_prefix'], 'foxiz' );
		} ?>
        <a href="<?php echo esc_url( $link ); ?>" rel="<?php echo esc_attr( $rel_attr ); ?>"<?php
		if ( ! empty( $title_index ) ) {
			echo ' data-index="' . esc_attr( $title_index . ': ' ) . '" class="p-url has-index"';
		} else {
			echo ' class="p-url"';
		}
		?>><?php if ( ! empty( $post_title ) ) {
			the_title();
		} else {
			echo get_the_date( '', get_the_ID() );
		} ?></a><?php
		echo '</' . esc_attr( $settings['title_tag'] ) . '>';
	}
}

if ( ! function_exists( 'foxiz_podcast_entry_meta' ) ) {
	/**
	 * @param array $settings
	 */
	function foxiz_podcast_entry_meta( $settings = array() ) {

		$settings['meta_label_by']   = foxiz_get_option( 'podcast_meta_author_label' );
		$settings['has_duration_label'] = foxiz_get_option( 'podcast_meta_duration_label' );
		$settings['has_play_label']     = foxiz_get_option( 'podcast_meta_play_label' );

		if ( foxiz_get_entry_meta( $settings ) ) {
			$class_name   = array();
			$class_name[] = 'p-meta';
			if ( ! empty( $settings['entry_meta']['avatar'] ) ) {
				$class_name[] = 'has-avatar';
			}
			if ( ! empty( $settings['bookmark'] ) ) {
				$class_name[] = 'has-bookmark';
			} ?>
            <div class="<?php echo join( ' ', $class_name ); ?>">
                <div class="meta-inner is-meta">
					<?php echo foxiz_get_entry_meta( $settings ); ?>
                </div>
				<?php if ( ! empty( $settings['bookmark'] ) ) {
					foxiz_bookmark_trigger( get_the_ID() );
				} ?>
            </div>
		<?php }
	}
}

if ( ! function_exists( 'foxiz_series_header_1' ) ) {
	/**
	 * @param array $settings
	 */
	function foxiz_series_header_1( $settings = array() ) {

		$class_name = 'archive-header category-header-1 series-header-1';
		if ( ! empty( $settings['pattern'] ) && '-1' !== (string) $settings['pattern'] ) {
			$class_name .= ' is-pattern pattern-' . esc_attr( $settings['pattern'] );
		} else {
			$class_name .= ' solid-bg';
		}
		?>
        <header class="<?php echo esc_attr( $class_name ); ?>">
            <div class="rb-container edge-padding">
                <div class="archive-inner">
                    <div class="archive-header-content">
						<?php if ( ! empty( $settings['breadcrumb'] ) ) {
							echo foxiz_get_breadcrumb( 'archive-breadcrumb' );
						} ?>
                        <h1 class="archive-title"><?php single_cat_title( '', true ); ?></h1>
						<?php
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
						foxiz_series_socials( $settings );
						?>
                    </div>
					<?php if ( ! empty( $settings['featured_image'] ) ) :
						if ( empty( $settings['featured_image_urls'] ) ) {
							$settings['featured_image_urls'] = array();
						} ?>
                        <div class="category-hero-wrap">
							<?php foxiz_render_category_hero( $settings['featured_image'], $settings['featured_image_urls'] );
							if ( ! empty( $settings['featured_file'][0] ) ) : ?>
                                <div class="series-intro series-intro-absolute">
									<?php echo foxiz_get_audio_hosted( array(
										'src'      => wp_get_attachment_url( $settings['featured_file'][0] ),
										'autoplay' => false,
										'preload'  => 'none',
										'style'    => '',
										'class'    => 'self-hosted-audio podcast-player meta-podcast-player',
									) ); ?>
                                </div>
							<?php endif; ?>
                        </div>
					<?php endif; ?>
                </div>
            </div>
        </header>
	<?php }
}

if ( ! function_exists( 'foxiz_series_header_2' ) ) {
	/**
	 * @param array $settings
	 */
	function foxiz_series_header_2( $settings = array() ) {

		$class_name = 'archive-header category-header-2 series-header-2';
		if ( ! empty( $settings['pattern'] ) && '-1' !== (string) $settings['pattern'] ) {
			$class_name .= ' is-pattern pattern-' . esc_attr( $settings['pattern'] );
		} else {
			$class_name .= ' solid-bg';
		} ?>
        <header class="<?php echo esc_attr( $class_name ); ?>">
            <div class="rb-container edge-padding">
                <div class="archive-inner">
                    <div class="archive-header-content light-scheme">
						<?php if ( ! empty( $settings['breadcrumb'] ) ) {
							echo foxiz_get_breadcrumb( 'archive-breadcrumb' );
						}
						if ( ! empty( $settings['featured_file'][0] ) ) : ?>
                            <div class="series-intro">
								<?php echo foxiz_get_audio_hosted( array(
									'src'      => wp_get_attachment_url( $settings['featured_file'][0] ),
									'autoplay' => false,
									'preload'  => 'none',
									'style'    => '',
									'class'    => 'self-hosted-audio podcast-player meta-podcast-player',
								) ); ?>
                            </div>
						<?php endif; ?>
                        <h1 class="archive-title"><?php single_cat_title( '', true ); ?></h1>
						<?php the_archive_description( '<div class="taxonomy-description">', '</div>' );
						foxiz_series_socials( $settings );
						?>
                    </div>
                </div>
                <div class="category-feat-overlay">
					<?php if ( ! empty( $settings['featured_image'][0] ) ) : ?>
                        <img src="<?php echo esc_url( wp_get_attachment_image_url( $settings['featured_image'][0], '2048×2048' ) ); ?>" alt="<?php echo get_post_meta( $settings['featured_image'][0], '_wp_attachment_image_alt', true ); ?>">
					<?php endif; ?>
                </div>
            </div>
        </header>
	<?php }
}

if ( ! function_exists( 'foxiz_series_socials' ) ) {
	function foxiz_series_socials( $settings = array() ) {

		$socials = array();
		if ( ! empty( $settings['listen_on_apple'] ) ) {
			$socials[] = array(
				'label' => 'Apple',
				'icon'  => 'rbi-applepodcast',
				'url'   => $settings['listen_on_apple']
			);
		}
		if ( ! empty( $settings['listen_on_spotify'] ) ) {
			$socials[] = array(
				'label' => 'Spotify',
				'icon'  => 'rbi-spotify',
				'url'   => $settings['listen_on_spotify']
			);
		}
		if ( ! empty( $settings['listen_on_soundcloud'] ) ) {
			$socials[] = array(
				'label' => 'SoundCloud',
				'icon'  => 'rbi-soundcloud',
				'url'   => $settings['listen_on_soundcloud']
			);
		}
		if ( ! empty( $settings['listen_on_google'] ) ) {
			$socials[] = array(
				'label' => 'GooglePodcast',
				'icon'  => 'rbi-googlepodcast',
				'url'   => $settings['listen_on_google']
			);
		}
		if ( foxiz_get_option( 'series_rss' ) ) {
			$socials[] = array(
				'label' => 'Subscribe',
				'icon'  => 'rbi-rss',
				'url'   => get_term_feed_link( $settings['category'], 'series' ),
			);
		}
		?>
        <div class="podcast-social-list">
			<?php foreach ( $socials as $social ) :
				if ( empty( $social['icon'] || empty( $social['url'] ) || empty( $social['label'] ) ) ) {
					continue;
				}
				if ( stripos( $social['icon'], 'rbi' ) ) {
					$icon_classes = 'rbi ' . trim( $social['icon'] );
				} else {
					$icon_classes = trim( $social['icon'] );
				}
				?>
                <a href="<?php echo esc_url( $social['url'] ); ?>" data-gravity="s" data-title="<?php echo esc_attr( $social['label'] ); ?>" class="<?php echo 'podcast-social-item social-' . trim( $social['icon'] ); ?>" target="_blank" rel="nofollow">
                    <i class="<?php echo esc_attr( $icon_classes ); ?>"></i><span><?php echo esc_html( $social['label'] ); ?></span>
                </a>
			<?php endforeach; ?>
        </div>
		<?php
	}
}

if ( ! function_exists( 'foxiz_podcast_blog' ) ) {
	/**
	 * @param array $settings
	 * @param null $_query
	 *
	 * @return false
	 */
	function foxiz_podcast_blog( $settings = array(), $_query = null ) {

		if ( ! empty( $settings['template_global'] ) ) {
			foxiz_blog_template( $settings['template_global'] );

			return false;
		}

		if ( empty( $_query ) ) {
			global $wp_query;
			$_query = $wp_query;
		}
		?>
        <div class="blog-wrap podcast-blog-wrap without-sidebar">
            <div class="rb-container edge-padding">
                <div class="grid-container">
                    <div class="blog-content">
						<?php echo foxiz_get_podcast_grid_flex_1( $settings, $_query ); ?>
                    </div>
                </div>
            </div>
        </div>
	<?php }
}



