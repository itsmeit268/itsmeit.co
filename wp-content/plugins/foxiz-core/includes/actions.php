<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

add_action( 'pre_get_posts', 'foxiz_get_query_settings', 10 );
add_action( 'init', 'foxiz_remove_plugin_hooks' );
add_action( 'save_post', 'foxiz_init_word_count', 100, 1 );
add_action( 'comment_post', 'foxiz_add_user_rating', 1 );
add_action( 'wp_head', 'foxiz_bookmarklet_icons', 1 );
add_action( 'wp_head', 'foxiz_pingback_supported', 5 );
add_action( 'wp_head', 'foxiz_embed_dark_mode', 100 );
add_action( 'wp_footer', 'foxiz_footer_inline_script', 0 );
add_filter( 'upload_mimes', 'foxiz_svg_upload_supported', 10, 1 );
add_filter( 'wp_get_attachment_image_src', 'foxiz_gif_supported', 10, 4 );
add_filter( 'edit_comment_misc_actions', 'foxiz_edit_comment_review_form', 10, 2 );
add_filter( 'the_content', 'foxiz_filter_load_next_content', PHP_INT_MAX );
add_filter( 'pvc_post_views_html', 'foxiz_remove_pvc_post_views', 999 );
add_filter( 'bcn_pick_post_term', 'foxiz_bcn_primary_category', 10, 4 );
add_filter( 'wpcf7_autop_or_not', '__return_false', 999 );
add_filter( 'single_product_archive_thumbnail_size', 'foxiz_product_archive_thumbnail_size', 10, 1 );
add_filter( 'the_content', 'foxiz_add_elements_to_content', 999 );
add_filter( 'get_the_archive_title_prefix', 'foxiz_archive_title_prefix', 10 );
add_filter( 'wp_kses_allowed_html', 'foxiz_kses_allowed_html', 10, 2 );
add_filter( 'the_excerpt_rss', 'foxiz_rss_tagline_supported', 0, 1 );
add_action( 'rss2_ns', 'foxiz_podcast_rss2_ns', 0 );
add_action( 'rss2_head', 'foxiz_podcast_rss2_head', 0 );
add_action( 'rss2_item', 'foxiz_podcast_rss2_item', 0 );
remove_filter( 'pre_term_description', 'wp_filter_kses' );

if ( ! function_exists( 'foxiz_pingback_supported' ) ):
	function foxiz_pingback_supported() {

		if ( is_singular() && pings_open() ) : ?>
			<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>"/>
		<?php endif;
	}
endif;

if ( ! function_exists( 'foxiz_bookmarklet_icons' ) ) :
	/**
	 * foxiz_bookmarklet_icon
	 */
	function foxiz_bookmarklet_icons() {

		$apple_icon = foxiz_get_option( 'icon_touch_apple' );
		$metro_icon = foxiz_get_option( 'icon_touch_metro' );

		if ( ! empty( $apple_icon['url'] ) ) : ?>
			<link rel="apple-touch-icon" href="<?php echo esc_url( $apple_icon['url'] ); ?>"/>
		<?php endif;

		if ( ! empty( $metro_icon['url'] ) ) : ?>
			<meta name="msapplication-TileColor" content="#ffffff">
			<meta name="msapplication-TileImage" content="<?php echo esc_url( $metro_icon['url'] ); ?>"/>
		<?php endif;
	}
endif;

if ( ! function_exists( 'foxiz_svg_upload_supported' ) ) {
	/**
	 * @param $mime_types
	 *
	 * @return mixed
	 */
	function foxiz_svg_upload_supported( $mime_types = [] ) {

		if ( foxiz_get_option( 'svg_supported' ) ) {
			$mime_types['svg']  = 'image/svg+xml';
			$mime_types['svgz'] = 'image/svg+xml';
		}

		return $mime_types;
	}
}

if ( ! function_exists( 'foxiz_gif_supported' ) ) {
	/**
	 * @param $image
	 * @param $attachment_id
	 * @param $size
	 * @param $icon
	 *
	 * @return array|false
	 * gif support
	 */
	function foxiz_gif_supported( $image, $attachment_id, $size, $icon ) {

		if ( foxiz_get_option( 'gif_supported' ) && ! empty( $image[0] ) ) {
			$format = wp_check_filetype( $image[0] );

			if ( ! empty( $format ) && 'gif' === $format['ext'] && 'full' !== $size ) {
				return wp_get_attachment_image_src( $attachment_id, $size = 'full', $icon );
			}
		}

		return $image;
	}
}

if ( ! function_exists( 'foxiz_remove_plugin_hooks' ) ) {
	/**
	 * remove multiple authors
	 */
	function foxiz_remove_plugin_hooks() {

		global $multiple_authors_addon;
		if ( ! empty( $multiple_authors_addon ) ) {
			remove_filter( 'the_content', [ $multiple_authors_addon, 'filter_the_content' ] );
		}
	}
}

if ( ! function_exists( 'foxiz_init_word_count' ) ) {
	function foxiz_init_word_count( $post_id = '' ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return false;
		}
		delete_post_meta( $post_id, 'foxiz_content_total_word' );
		foxiz_update_word_count( $post_id );
	}
}

if ( ! function_exists( 'foxiz_update_word_count' ) ) {
	/**
	 * @param string $post_id
	 *
	 * @return false|int|string[]
	 */
	function foxiz_update_word_count( $post_id = '' ) {

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$post_type = get_post_type( $post_id );

		if ( 'nav_menu_item' === $post_type || 'product' === $post_type || 'comment' == $post_type ) {
			return false;
		}

		$content = get_post_field( 'post_content', $post_id );
		$count   = foxiz_count_content( $content );
		update_post_meta( $post_id, 'foxiz_content_total_word', $count );

		return $count;
	}
}

if ( ! function_exists( 'foxiz_remove_pvc_post_views' ) ) {
	/**
	 * @param $html
	 *
	 * @return false
	 */
	function foxiz_remove_pvc_post_views( $html ) {

		if ( is_single() ) {
			return false;
		} else {
			return $html;
		}
	}
}

if ( ! function_exists( 'foxiz_add_user_rating' ) ) {
	/**
	 * @param $comment_id
	 *
	 * @return false|void
	 */
	function foxiz_add_user_rating( $comment_id ) {

		if ( ! empty( $_POST['comment_parent'] ) ) {
			return false;
		}
		if ( isset( $_POST['rbrating'] ) && isset( $_POST['comment_post_ID'] ) && 'post' === get_post_type( absint( $_POST['comment_post_ID'] ) ) ) {

			if ( $_POST['rbrating'] > 5 || $_POST['rbrating'] < 0 ) {
				return;
			}
			update_comment_meta( $comment_id, 'rbrating', intval( $_POST['rbrating'] ), true );
			foxiz_calc_average_rating( absint( $_POST['comment_post_ID'] ) );
		}

		return false;
	}
}

if ( ! function_exists( 'foxiz_edit_comment_review_form' ) ) {
	/**
	 * @param $output
	 * @param $comment
	 *
	 * @return mixed|string
	 */
	function foxiz_edit_comment_review_form( $output, $comment ) {

		$rating = get_comment_meta( $comment->comment_ID, 'rbrating', true );
		if ( empty( $rating ) ) {
			return $output;
		}

		$output .= '<div class="misc-pub-section rb-form-rating">';
		$output .= '<label id="rating-' . $comment->comment_ID . '">' . foxiz_html__( 'Your Rating', 'foxiz-core' ) . '</label>';
		$output .= '<select name="comment_meta[rbrating]" id="rating-' . get_the_ID() . '" class="rb-rating-selection">';
		for ( $i = 1; $i <= 5; $i ++ ) {
			if ( $i === intval( $rating ) ) {
				$output .= '<option value="' . $i . '" selected>' . $i . '</option>';
			} else {
				$output .= '<option value="' . $i . '">' . $i . '</option>';
			};
		}
		$output .= '</select></div>';

		return $output;
	}
}

if ( ! function_exists( 'foxiz_filter_load_next_content' ) ) {
	/**
	 * @param $content
	 *
	 * @return array|mixed|string|string[]
	 */
	function foxiz_filter_load_next_content( $content ) {

		if ( is_singular( 'post' ) && get_query_var( 'rbsnp' ) ) {
			$content = str_replace( "(adsbygoogle = window.adsbygoogle || []).push({});", '', $content );
		}

		return $content;
	}
}

if ( ! function_exists( 'foxiz_get_query_settings' ) ) {
	function foxiz_get_query_settings( $query ) {

		if ( is_admin() || ! $query->is_main_query() ) {
			return false;
		}

		if ( $query->is_home() ) {
			$blog_posts_per_page = foxiz_get_option( 'blog_posts_per_page' );
			if ( ! empty( $blog_posts_per_page ) ) {
				$query->set( 'posts_per_page', intval( $blog_posts_per_page ) );
			}
		} elseif ( $query->is_search() ) {
			$posts_per_page = foxiz_get_option( 'search_posts_per_page' );

			if ( foxiz_get_option( 'search_only_post' ) ) {
				$post_type = [ 'post' => 'post' ];
			} else {

				$post_type = get_post_types( [ 'exclude_from_search' => false ] );
				unset( $post_type['e-landing-page'] );

				if ( foxiz_get_option( 'search_exclude_page' ) ) {
					unset( $post_type['page'] );
				}
				if ( foxiz_get_option( 'search_exclude_attachment' ) ) {
					unset( $post_type['attachment'] );
				}
			}

			if ( ! foxiz_get_option( 'podcast_search_included' ) ) {
				unset( $post_type['podcast'] );
			}

			$query->set( 'post_type', $post_type );

			if ( ! empty( $posts_per_page ) ) {
				$query->set( 'posts_per_page', absint( $posts_per_page ) );
			}
		} elseif ( $query->is_category() ) {

			$query->set( 'post_status', 'publish' );
			$data = rb_get_term_meta( 'foxiz_category_meta', get_queried_object_id() );

			$posts_per_page = ! empty( $data['posts_per_page'] ) ? $data['posts_per_page'] : foxiz_get_option( 'category_posts_per_page' );
			if ( ! empty( $posts_per_page ) ) {
				$query->set( 'posts_per_page', absint( $posts_per_page ) );
			}

			if ( ! empty( $data['tag_not_in'] ) ) {
				$tags    = explode( ',', $data['tag_not_in'] );
				$tags    = array_unique( $tags );
				$tag_ids = [];
				foreach ( $tags as $tag ) {
					$tag = get_term_by( 'slug', trim( $tag ), 'post_tag' );
					if ( ! empty( $tag->term_id ) ) {
						array_push( $tag_ids, $tag->term_id );
					}
				}
				if ( count( $tag_ids ) ) {
					$query->set( 'tag__not_in', $tag_ids );
				}
			}
		} elseif ( $query->is_author() ) {
			$posts_per_page = foxiz_get_option( 'author_posts_per_page' );
			if ( ! empty( $posts_per_page ) ) {
				$query->set( 'posts_per_page', intval( $posts_per_page ) );
			}
		} elseif ( is_tag() ) {

			$data           = rb_get_term_meta( 'foxiz_category_meta', get_queried_object_id() );
			$posts_per_page = ! empty( $data['posts_per_page'] ) ? $data['posts_per_page'] : foxiz_get_option( 'tag_posts_per_page', foxiz_get_option( 'archive_posts_per_page' ) );
			if ( ! empty( $posts_per_page ) ) {
				$query->set( 'posts_per_page', absint( $posts_per_page ) );
			}
		} elseif ( $query->is_tax() ) {
			$data           = rb_get_term_meta( 'foxiz_category_meta', get_queried_object_id() );
			$posts_per_page = ! empty( $data['posts_per_page'] ) ? $data['posts_per_page'] : foxiz_get_option( 'tax_posts_per_page', foxiz_get_option( 'archive_posts_per_page' ) );
			if ( ! empty( $posts_per_page ) ) {
				$query->set( 'posts_per_page', absint( $posts_per_page ) );
			}
		} elseif ( $query->is_archive() ) {

			$query->set( 'post_status', 'publish' );

			if ( is_post_type_archive( 'product' ) || ( function_exists( 'is_shop' ) && is_shop() ) ) {
				$posts_per_page = foxiz_get_option( 'wc_shop_posts_per_page' );
			} elseif ( is_tax( 'product_cat' ) ) {
				$posts_per_page = foxiz_get_option( 'wc_archive_posts_per_page' );
			} else {
				$posts_per_page = foxiz_get_option( 'archive_posts_per_page' );
			}
			if ( ! empty( $posts_per_page ) ) {
				$query->set( 'posts_per_page', absint( $posts_per_page ) );
			}
		}

		return false;
	}
}

if ( ! function_exists( 'foxiz_bcn_primary_category' ) ) {
	/**
	 * @param $terms
	 * @param $id
	 * @param $type
	 * @param $taxonomy
	 *
	 * @return array|false|WP_Error|WP_Term|null
	 */
	function foxiz_bcn_primary_category( $terms, $id, $type, $taxonomy ) {

		if ( 'post' === $type ) {
			$primary_category = rb_get_meta( 'primary_category', $id );

			if ( empty( $primary_category ) ) {
				return $terms;
			};

			return get_term_by( 'id', $primary_category, $taxonomy );
		}

		return $terms;
	}
}

if ( ! function_exists( 'foxiz_dark_mode_inline_script' ) ) {
	function foxiz_dark_mode_inline_script() {

		if ( '1' !== (string) foxiz_get_option( 'dark_mode' ) ) {
			return false;
		}

		$optimized_load   = (string) foxiz_get_option( 'dark_mode_cookie' );
		$cookie_mode      = ( $optimized_load === '1' );
		$first_visit_mode = foxiz_get_option( 'first_visit_mode' );
		$type_attr        = current_theme_supports( 'html5', 'script' ) ? '' : " type='text/javascript'";
		?>
		<script<?php echo $type_attr; ?>>
            (function () {
                let currentMode = null;
                const darkModeID = '<?php echo FOXIZ_CORE::get_instance()->get_dark_mode_id() ?>';
				<?php if( $cookie_mode ) : ?>
                currentMode = document.body.getAttribute('data-theme');
                if (currentMode === 'browser' && window.matchMedia) {
                    currentMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default';
                    document.body.setAttribute('data-theme', currentMode);
                }
				<?php else: ?>
                currentMode = navigator.cookieEnabled ? localStorage.getItem(darkModeID) || null : 'default';
                if (!currentMode) {
					<?php if( 'browser' === $first_visit_mode ) : ?>
                    if (window.matchMedia && navigator.cookieEnabled) {
                        currentMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default';
                        localStorage.setItem(darkModeID, currentMode);
                    }
					<?php else : ?>
                    currentMode = '<?php echo strip_tags( $first_visit_mode ); ?>';
                    localStorage.setItem(darkModeID, '<?php echo strip_tags( $first_visit_mode ); ?>');
					<?php endif; ?>
                }
                document.body.setAttribute('data-theme', currentMode === 'dark' ? 'dark' : 'default');
				<?php endif; ?>
            })();
		</script>
		<?php
	}
}

if ( ! function_exists( 'foxiz_dark_mode_prefers_scheme_script' ) ) {
	function foxiz_dark_mode_prefers_scheme_script() {

		$type_attr = current_theme_supports( 'html5', 'script' ) ? '' : " type='text/javascript'";
		?>
		<script<?php echo $type_attr; ?>>
            (function () {
                document.body.setAttribute('data-theme', window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default');
            })();
		</script>
		<?php
	}
}

if ( ! function_exists( 'foxiz_footer_inline_script' ) ) {
	function foxiz_footer_inline_script() {

		if ( foxiz_is_amp() ) {
			return false;
		}

		if ( ! empty( $GLOBALS['foxiz_yes_recommended'] ) && ! empty( $GLOBALS['foxiz_queried_ids'] ) && is_array( $GLOBALS['foxiz_queried_ids'] ) ) {
			wp_localize_script( 'foxiz-core', 'foxizQueriedIDs', [ 'data' => implode( ',', $GLOBALS['foxiz_queried_ids'] ) ] );
		}

		$optimized_load = (string) foxiz_get_option( 'dark_mode_cookie' );
		$dark_mode      = (string) foxiz_get_option( 'dark_mode' );
		$cookie_mode    = ( $optimized_load === '1' );
		$type_attr      = current_theme_supports( 'html5', 'script' ) ? '' : " type='text/javascript'";
		?>
		<script<?php echo $type_attr; ?>>
            (function () {
				<?php if ( '1' === $dark_mode ) : ?>
                const darkModeID = '<?php echo FOXIZ_CORE::get_instance()->get_dark_mode_id() ?>';
                const currentMode = <?php echo ! empty( $cookie_mode ) ? "document.body.getAttribute('data-theme')" : "navigator.cookieEnabled ? (localStorage.getItem(darkModeID) || 'default') : 'default'" ?>;
                const selector = currentMode === 'dark' ? '.mode-icon-dark' : '.mode-icon-default';
                const icons = document.querySelectorAll(selector);
                if (icons.length) {
                    icons.forEach(icon => icon.classList.add('activated'));
                }
				<?php endif; ?>

				<?php if (foxiz_get_option( 'privacy_bar' ) && foxiz_get_option( 'privacy_text' )) : ?>
                const privacyBox = document.getElementById('rb-privacy');
                const currentPrivacy = navigator.cookieEnabled ? localStorage.getItem('RubyPrivacyAllowed') || '' : '1';
                if (!currentPrivacy && privacyBox?.classList) {
                    privacyBox.classList.add('activated');
                }
				<?php endif; ?>
                const readingSize = navigator.cookieEnabled ? sessionStorage.getItem('rubyResizerStep') || '' : '1';
                if (readingSize) {
                    const body = document.querySelector('body');
                    switch (readingSize) {
                        case '2':
                            body.classList.add('medium-entry-size');
                            break;
                        case '3':
                            body.classList.add('big-entry-size');
                            break;
                    }
                }
            })();
		</script>
		<?php
	}
}

if ( ! function_exists( 'foxiz_product_archive_thumbnail_size' ) ) {
	function foxiz_product_archive_thumbnail_size( $size ) {

		if ( ! empty( $GLOBALS['foxiz_product_thumb_size'] ) ) {
			return $GLOBALS['foxiz_product_thumb_size'];
		} else {
			return $size;
		}
	}
}

if ( ! function_exists( 'foxiz_add_elements_to_content_fallback' ) ) {
	/**
	 * @param $content
	 * @param $data
	 *
	 * @return string
	 */
	function foxiz_add_elements_to_content_fallback( $content, $data ) {

		$tag     = '</p>';
		$content = explode( $tag, $content );

		foreach ( $content as $index => $paragraph ) {
			if ( trim( $paragraph ) ) {
				$content[ $index ] .= $tag;
			}
		}

		foreach ( $data as $element ) {
			if ( empty( $element['positions'] ) || ! is_array( $element['positions'] ) || empty( trim( $element['render'] ) ) ) {
				continue;
			}
			$element['positions'] = array_unique( $element['positions'] );
			foreach ( $element['positions'] as $pos ) {
				$index = $pos - 1;
				if ( ! empty( $content[ $index ] ) ) {
					$content[ $index ] .= $element['render'];
				}
			}
		}

		return implode( '', $content );
	}
}

if ( ! function_exists( 'foxiz_add_elements_to_content' ) ) {
	/**
	 * @param $content
	 *
	 * @return string|string[]|null
	 */
	function foxiz_add_elements_to_content( $content ) {

		$data = apply_filters( 'ruby_content_elements', [], $content );

		if ( empty( $data ) || ! is_array( $data ) || ! is_main_query() ) {
			return $content;
		}

		if ( foxiz_is_elementor_active() && ! foxiz_amp_suppressed_elementor() ) {
			$document = \Elementor\Plugin::$instance->documents->get( get_the_ID() );
			if ( $document && $document->is_built_with_elementor() ) {
				return foxiz_add_elements_to_content_fallback( $content, $data );
			}
		}

		if ( ! class_exists( 'DOMDocument' ) ) {
			return foxiz_add_elements_to_content_fallback( $content, $data );
		}

		$doc    = new DOMDocument();
		$buffer = new DOMDocument();

		libxml_use_internal_errors( true );
		@$doc->loadHTML( '<?xml encoding="' . get_bloginfo( 'charset' ) . '" ?>' . $content );
		libxml_use_internal_errors( false );

		$xpath          = new DOMXPath( $doc );
		$rootParagraphs = $xpath->query( '//p[parent::body]' );

		foreach ( $data as $element ) {
			if ( empty( $element['positions'] ) || ! is_array( $element['positions'] ) || empty( trim( $element['render'] ) ) ) {
				continue;
			}

			$element['positions'] = array_unique( $element['positions'] );
			@$buffer->loadHTML( '<?xml encoding="' . get_bloginfo( 'charset' ) . '" ?>' . $element['render'], LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED );

			if ( $buffer->hasChildNodes() ) {
				foreach ( $element['positions'] as $pos ) {
					$index = $pos - 1;
					if ( $index >= 0 && $index < $rootParagraphs->length ) {
						$importedNode = @$doc->importNode( $buffer->documentElement, true );
						$rootParagraphs->item( $index )->parentNode->insertBefore( $importedNode, $rootParagraphs->item( $index )->nextSibling );
					}
				}
			}
		}

		$content = preg_replace( '#^.*?<body.*?>(.*)</body>.*?$#si', '$1', $doc->saveHTML( $doc->documentElement ) );

		return $content;
	}
}

/**
 * @return false
 */
if ( ! function_exists( 'foxiz_embed_dark_mode' ) ) {
	function foxiz_embed_dark_mode() {

		if ( foxiz_is_amp() ) {
			return false;
		}

		$dark_mode     = (string) foxiz_get_option( 'dark_mode' );
		$optimize_load = (string) foxiz_get_option( 'dark_mode_cookie' );

		if ( 'browser' === $dark_mode ) {
			add_action( 'wp_body_open', 'foxiz_dark_mode_prefers_scheme_script', 0 );
		}

		if ( '2' === $optimize_load ) {
			add_action( 'wp_body_open', 'foxiz_dark_mode_inline_script', 0 );
		} else {
			add_action( 'wp_footer', 'foxiz_dark_mode_inline_script', 0 );
		}
	}
}

if ( ! function_exists( 'foxiz_archive_title_prefix' ) ) {
	function foxiz_archive_title_prefix( $prefix ) {

		if ( foxiz_get_option( 'archive_no_prefix' ) ) {
			return false;
		}

		return $prefix;
	}
}

if ( ! function_exists( 'foxiz_kses_allowed_html' ) ) {
	/**
	 * @param $tags
	 * @param $context
	 *
	 * @return array
	 */
	function foxiz_kses_allowed_html( $tags, $context ) {

		switch ( $context ) {
			case 'foxiz':
				$tags = [
					'a'      => [
						'href'   => [],
						'title'  => [],
						'target' => [],
					],
					'br'     => [],
					'em'     => [],
					'strong' => [],
					'i'      => [
						'class' => [],
					],
					'p'      => [],
					'span'   => [],
					'div'    => [
						'class' => [],
					],
					'img'    => [
						'loading'  => [],
						'height'   => [],
						'width'    => [],
						'decoding' => [],
						'src'      => [],
						'class'    => [],
						'alt'      => '',
					],
				];

				return $tags;
			default:
				return $tags;
		}
	}
}

if ( ! function_exists( 'foxiz_rss_tagline_supported' ) ) {
	function foxiz_rss_tagline_supported( $output ) {

		$tagline = rb_get_meta( 'tagline' );
		if ( ! empty( $tagline ) ) {
			return $tagline;
		}

		return $output;
	}
}

/** support RSS feed for show */
if ( ! function_exists( 'foxiz_podcast_rss2_ns' ) ) {
	function foxiz_podcast_rss2_ns() {

		if ( is_tax( 'series' ) ) {
			echo PHP_EOL . "\t" . 'xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"';
			echo PHP_EOL . "\t";
		}
	}
}

if ( ! function_exists( 'foxiz_podcast_rss2_head' ) ) {
	function foxiz_podcast_rss2_head() {

		if ( is_tax( 'series' ) ) {
			$tax_id   = get_queried_object_id();
			$settings = rb_get_term_meta( 'foxiz_category_meta', $tax_id );

			if ( ! empty( $settings['featured_rss'][0] ) ) {
				echo "\t\t<itunes:image href=\"" . wp_get_attachment_image_url( $settings['featured_rss'][0], 'full' ) . "\" />" . PHP_EOL;
			}
			if ( ! empty( $settings['itunes_category'] ) ) {
				$categories = explode( ',', $settings['itunes_category'] );
				foreach ( $categories as $category ) {
					echo "\t\t\t<itunes:category text=\"" . htmlspecialchars( trim( strip_tags( $category ) ), ENT_QUOTES ) . "\" />" . PHP_EOL;
				}
			} else {
				echo "\t\t\t<itunes:category text=\"News\" />" . PHP_EOL;
			}
			echo "\t\t<itunes:type>serial</itunes:type>" . PHP_EOL;
			echo "\t\t<itunes:explicit>false</itunes:explicit>" . PHP_EOL;
		}
	}
}

if ( ! function_exists( 'foxiz_podcast_rss2_item' ) ) {
	function foxiz_podcast_rss2_item() {

		global $post;

		if ( empty( $post ) || 'podcast' !== get_post_type( $post ) ) {
			return;
		}

		/** only support RSS for hosted audio */
		$file_id = rb_get_meta( 'audio_hosted', $post->ID );
		if ( empty( $file_id ) ) {
			return;
		}

		global $post;

		$file_url  = wp_get_attachment_url( $file_id );
		$file_path = get_attached_file( $file_id );

		if ( ! empty( $file_path ) ) {
			$file_size = filesize( $file_path );
		} else {
			$file_size = 1;
		}

		$summary      = rb_get_meta( 'tagline', $post->ID );
		$duration     = rb_get_meta( 'duration', $post->ID );
		$episode_type = rb_get_meta( 'episode_type', $post->ID );
		if ( empty( $episode_type ) ) {
			$episode_type = 'full';
		}
		if ( function_exists( 'mime_content_type' ) && ! empty( $file_path ) ) {
			$type = mime_content_type( $file_path );
		} else {
			$type = 'audio/mpeg';
		}
		if ( empty( $summary ) ) {
			$summary = get_the_excerpt();
		}

		echo "\t\t<enclosure url=\"" . $file_url . "\" type=\"" . $type . "\" length=\"" . $file_size . "\" />" . PHP_EOL;
		echo "\t\t<itunes:episodeType>" . $episode_type . "</itunes:episodeType>" . PHP_EOL;
		echo "\t\t<itunes:author>" . get_the_author_meta( 'display_name', $post->post_author ) . "</itunes:author>" . PHP_EOL;
		echo "\t\t<itunes:summary>" . strip_tags( $summary ) . "</itunes:summary>" . PHP_EOL;
		echo "\t\t<itunes:image href=\"" . get_the_post_thumbnail_url( $post->ID, 'full' ) . "\" />" . PHP_EOL;
		echo "\t\t<itunes:explicit>false</itunes:explicit>" . PHP_EOL;
		echo "\t\t<itunes:duration>" . strip_tags( $duration ) . "</itunes:duration>" . PHP_EOL;
	}
}