<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'foxiz_get_user_socials' ) ) {
	/**
	 * @param string $author_id
	 *
	 * @return array|false
	 */
	function foxiz_get_user_socials( $author_id = '' ) {

		if ( empty( $author_id ) ) {
			return false;
		}

		$data               = [];
		$data['website']    = get_the_author_meta( 'user_url', $author_id );
		$data['facebook']   = get_the_author_meta( 'facebook', $author_id );
		$data['twitter']    = get_the_author_meta( 'twitter_url', $author_id );
		$data['instagram']  = get_the_author_meta( 'instagram', $author_id );
		$data['pinterest']  = get_the_author_meta( 'pinterest', $author_id );
		$data['linkedin']   = get_the_author_meta( 'linkedin', $author_id );
		$data['tumblr']     = get_the_author_meta( 'tumblr', $author_id );
		$data['flickr']     = get_the_author_meta( 'flickr', $author_id );
		$data['skype']      = get_the_author_meta( 'skype', $author_id );
		$data['snapchat']   = get_the_author_meta( 'snapchat', $author_id );
		$data['myspace']    = get_the_author_meta( 'myspace', $author_id );
		$data['youtube']    = get_the_author_meta( 'youtube', $author_id );
		$data['bloglovin']  = get_the_author_meta( 'bloglovin', $author_id );
		$data['digg']       = get_the_author_meta( 'digg', $author_id );
		$data['dribbble']   = get_the_author_meta( 'dribbble', $author_id );
		$data['soundcloud'] = get_the_author_meta( 'soundcloud', $author_id );
		$data['vimeo']      = get_the_author_meta( 'vimeo', $author_id );
		$data['reddit']     = get_the_author_meta( 'reddit', $author_id );
		$data['vkontakte']  = get_the_author_meta( 'vkontakte', $author_id );
		$data['telegram']   = get_the_author_meta( 'telegram', $author_id );
		$data['whatsapp']   = get_the_author_meta( 'whatsapp', $author_id );
		$data['rss']        = get_the_author_meta( 'rss', $author_id );

		return $data;
	}
}

if ( ! function_exists( 'foxiz_is_wc_pages' ) ) {
	function foxiz_is_wc_pages() {

		if ( class_exists( 'WooCommerce' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'foxiz_is_template_preview' ) ) {
	/**
	 * @return bool
	 */
	function foxiz_is_template_preview() {

		if ( ( foxiz_is_elementor_active() && Elementor\Plugin::$instance->editor->is_edit_mode() ) || is_singular( 'rb-etemplate' ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'foxiz_get_video_mine_type' ) ) {
	/**
	 * @param $url
	 *
	 * @return string
	 */
	function foxiz_get_video_mine_type( $url ) {

		/** set default */
		if ( empty( $url ) ) {
			return 'video/mp4';
		}

		if ( false !== strpos( $url, '.webm' ) ) {
			return 'video/webm';
		} elseif ( false !== strpos( $url, '.ogv' ) ) {
			return 'video/ogg';
		} elseif ( false !== strpos( $url, '.avi' ) ) {
			return 'video/avi';
		} elseif ( false !== strpos( $url, '.mpeg' ) || false !== strpos( $url, '.mpg' ) || false !== strpos( $url, '.mpe' ) ) {
			return 'video/mpeg';
		}

		return 'video/mp4';
	}
}

if ( ! function_exists( 'foxiz_get_current_permalink' ) ) {
	/**
	 * @return string|void
	 */
	function foxiz_get_current_permalink() {

		global $wp;

		return home_url( add_query_arg( [], $wp->request ) );
	}
}

/**
 * @param        $id
 * @param string $taxonomy
 *
 * @return array|false|int|string|WP_Error|WP_Term|null
 */
if ( ! function_exists( 'foxiz_get_term_link' ) ) {
	function foxiz_get_term_link( $id, $taxonomy = '' ) {

		if ( ! is_object( $id ) ) {
			$id = (int) $id;
		}

		$link = get_term_link( $id, $taxonomy );

		if ( is_wp_error( $link ) ) {
			return '';
		}

		return $link;
	}
}

if ( ! function_exists( 'foxiz_get_post_likes' ) ) {
	/**
	 * @param string $id
	 *
	 * @return false|string
	 */
	function foxiz_get_post_likes( $id = '' ) {

		$count = get_post_meta( $id, 'rb_total_like', true );
		if ( ! empty( $count ) ) {
			return foxiz_pretty_number( $count );
		}

		return '';
	}
}

if ( ! function_exists( 'foxiz_get_post_dislikes' ) ) {
	/**
	 * @param string $id
	 *
	 * @return false|string
	 */
	function foxiz_get_post_dislikes( $id = '' ) {

		$count = get_post_meta( $id, 'rb_total_dislike', true );
		if ( ! empty( $count ) ) {
			return foxiz_pretty_number( $count );
		}

		return '';
	}
}

/**
 * @param array $settings
 *
 * @return array
 */
if ( ! function_exists( 'foxiz_get_top_post_ids' ) ) {
	function foxiz_get_top_post_ids( $settings = [] ) {

		if ( ! defined( 'JETPACK__VERSION' ) || ! class_exists( 'Automattic\Jetpack\Stats\WPCOM_Stats' ) ) {
			return [];
		}

		$count = 10;
		$days  = 2;

		if ( ! empty( $settings['posts_per_page'] ) ) {
			$count = absint( $settings['posts_per_page'] );
		}

		if ( ! empty( $settings['jetpack_days'] ) ) {
			$days = intval( $settings['jetpack_days'] );
		}

		if ( function_exists( 'wpl_get_blogs_most_liked_posts' ) && ! empty( $settings['jetpack_sort_order'] ) && 'likes' === $settings['jetpack_sort_order'] ) {
			$post_ids = wpl_get_blogs_most_liked_posts();
			if ( ! $post_ids ) {
				return [];
			}

			return array_keys( $post_ids );
		}

		if ( defined( 'IS_WPCOM' ) && IS_WPCOM && function_exists( 'stats_get_daily_history' ) ) {
			$post_views = wp_cache_get( "get_top_posts_$count", 'stats' );
			if ( false === $post_views ) {
				$stats_get_daily_history = stats_get_daily_history(
					false,
					get_current_blog_id(),
					'postviews',
					'post_id',
					false,
					2,
					'',
					$count * 2 + 10,
					true
				);
				$post_views              = array_shift( $stats_get_daily_history );
				unset( $post_views[0] );
				wp_cache_add( "get_top_posts_$count", $post_views, 'stats', 1200 );
			}

			return array_keys( $post_views );
		}

		$args = [
			'max'       => 11,
			'summarize' => 1,
			'num'       => $days,
		];

		$data = foxiz_convert_stats_array_to_object( ( new Automattic\Jetpack\Stats\WPCOM_Stats() )->get_top_posts( $args ) );

		if ( ! isset( $data->summary ) || empty( $data->summary->postviews ) ) {
			return [];
		}

		$post_ids = array_filter( wp_list_pluck( $data->summary->postviews, 'id' ) );
		if ( ! $post_ids ) {
			return [];
		}

		return $post_ids;
	}
}

/**
 * @param $stats_array
 *
 * @return mixed|WP_Error
 */
if ( ! function_exists( 'foxiz_convert_stats_array_to_object' ) ) {
	function foxiz_convert_stats_array_to_object( $stats_array ) {

		if ( is_wp_error( $stats_array ) ) {
			return $stats_array;
		}
		$encoded_array = wp_json_encode( $stats_array );
		if ( ! $encoded_array ) {
			return new WP_Error( 'stats_encoding_error', 'Failed to encode stats array' );
		}

		return json_decode( $encoded_array );
	}
}