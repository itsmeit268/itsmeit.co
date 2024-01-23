<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'foxiz_protocol' ) ) {
	/**
	 * get protocol
	 */
	function foxiz_protocol() {

		if ( is_ssl() ) {
			return 'https';
		}

		return 'http';
	}
}

if ( ! function_exists( 'rb_get_meta' ) ) {
	/**
	 * @param      $id
	 * @param null $post_id
	 *
	 * @return false|mixed
	 * get meta
	 */
	function rb_get_meta( $id, $post_id = null ) {

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		if ( empty( $post_id ) ) {
			return false;
		}

		$rb_meta = get_post_meta( $post_id, 'rb_global_meta', true );
		if ( ! empty( $rb_meta[ $id ] ) ) {

			if ( is_array( $rb_meta[ $id ] ) && isset( $rb_meta[ $id ]['placebo'] ) ) {
				unset( $rb_meta[ $id ]['placebo'] );
			}

			return $rb_meta[ $id ];
		}

		return false;
	}
}

if ( ! function_exists( 'rb_get_term_meta' ) ) {
	/**
	 * @param      $key
	 * @param null $term_id
	 *
	 * @return array|false
	 */
	function rb_get_term_meta( $key, $term_id = null ) {

		if ( empty( $term_id ) ) {
			$term_id = get_queried_object_id();
		}

		// get meta fields from option table
		$metas = get_metadata( 'term', $term_id, $key, true );

		/** fallback */
		if ( empty( $metas ) ) {
			$metas = get_option( $key );
			$metas = isset( $metas[ $term_id ] ) ? $metas[ $term_id ] : [];
		}

		if ( empty( $metas ) || ! is_array( $metas ) ) {
			return [];
		}

		return $metas;
	}
}

if ( ! function_exists( 'rb_get_nav_item_meta' ) ) {
	/**
	 * @param      $key
	 * @param      $nav_item_id
	 * @param null $menu_id
	 *
	 * @return array|false
	 */
	function rb_get_nav_item_meta( $key, $nav_item_id, $menu_id = null ) {

		$metas = get_metadata( 'post', $nav_item_id, $key, true );

		/** fallback */
		if ( empty( $metas ) ) {
			$metas = get_option( 'rb_menu_settings_' . $menu_id, [] );
			$metas = isset( $metas[ $nav_item_id ] ) ? $metas[ $nav_item_id ] : [];
		}

		if ( empty( $metas ) || ! is_array( $metas ) ) {
			return [];
		}

		return $metas;
	}
}

/**
 * @param        $text
 * @param string $domain
 *
 * @return mixed|string|void
 * foxiz html
 */
if ( ! function_exists( 'foxiz_html__' ) ) {
	function foxiz_html__( $text, $domain = 'foxiz-core' ) {

		$translated = esc_html( translate( $text, $domain ) );
		$id         = foxiz_convert_to_id( $text );
		$data       = get_option( 'rb_translated_data', [] );

		if ( ! empty( $data[ $id ] ) ) {
			$translated = $data[ $id ];
		}

		return $translated;
	}
}

if ( ! function_exists( 'foxiz_attr__' ) ) {
	/**
	 * @param        $text
	 * @param string $domain
	 *
	 * @return mixed|string|void
	 * foxiz translate
	 */
	function foxiz_attr__( $text, $domain = 'foxiz-core' ) {

		$translated = esc_attr( translate( $text, $domain ) );
		$id         = foxiz_convert_to_id( $text );
		$data       = get_option( 'rb_translated_data', [] );

		if ( ! empty( $data[ $id ] ) ) {
			$translated = $data[ $id ];
		}

		return $translated;
	}
}

if ( ! function_exists( 'foxiz_html_e' ) ) {
	/**
	 * @param        $text
	 * @param string $domain
	 * foxiz html e
	 */
	function foxiz_html_e( $text, $domain = 'foxiz-core' ) {

		echo foxiz_html__( $text, $domain );
	}
}

if ( ! function_exists( 'foxiz_attr_e' ) ) {
	/**
	 * @param        $text
	 * @param string $domain
	 * foxiz attr e
	 */
	function foxiz_attr_e( $text, $domain = 'foxiz-core' ) {

		echo foxiz_attr__( $text, $domain );
	}
}

if ( ! function_exists( 'foxiz_get_option' ) ) {
	/**
	 * @param string $option_name
	 * @param false  $default
	 *
	 * @return false|mixed|void
	 */
	function foxiz_get_option( $option_name = '', $default = false ) {

		$settings = get_option( FOXIZ_TOS_ID, [] );

		if ( empty( $option_name ) ) {
			return $settings;
		}

		if ( ! empty( $settings[ $option_name ] ) ) {
			return $settings[ $option_name ];
		}

		return $default;
	}
}

if ( ! function_exists( 'foxiz_is_amp' ) ) {
	/**
	 * @return bool
	 */
	function foxiz_is_amp() {

		return function_exists( 'amp_is_request' ) && amp_is_request();
	}
}

if ( ! function_exists( 'foxiz_pretty_number' ) ) {
	/**
	 * @param $number
	 *
	 * @return int|string
	 * pretty number
	 */
	function foxiz_pretty_number( $number ) {

		$number = intval( $number );
		if ( $number > 999999 ) {
			$number = str_replace( '.00', '', number_format( ( $number / 1000000 ), 2 ) ) . foxiz_attr__( 'M' );
		} elseif ( $number > 999 ) {
			$number = str_replace( '.0', '', number_format( ( $number / 1000 ), 1 ) ) . foxiz_attr__( 'k' );
		}

		return $number;
	}
}

if ( ! function_exists( 'foxiz_render_svg' ) ) {
	/**
	 * @param string $svg_name
	 * @param string $color
	 * @param string $ui
	 * render svg
	 */
	function foxiz_render_svg( $svg_name = '', $color = '', $ui = '' ) {

		echo foxiz_get_svg( $svg_name, $color, $ui );
	}
}

if ( ! function_exists( 'foxiz_get_svg' ) ) {
	/**
	 * @param string $svg_name
	 * @param string $color
	 * @param string $ui
	 *
	 * @return false
	 * get svg icon
	 */
	function foxiz_get_svg( $svg_name = '', $color = '', $ui = '' ) {

		return false;
	}
}

if ( ! function_exists( 'wp_body_open' ) ) {
	/** ensuring backward compatibility with versions of WordPress older than 5.2. */
	function wp_body_open() {

		do_action( 'wp_body_open' );
	}
}

if ( ! function_exists( 'foxiz_get_breadcrumb' ) ) {
	/**
	 * @param string $classes
	 *
	 * @return false
	 */
	function foxiz_get_breadcrumb( $classes = '' ) {

		return false;
	}
}

if ( ! function_exists( 'foxiz_render_breadcrumb' ) ) {
	/**
	 * @param string $classes
	 */
	function foxiz_render_breadcrumb( $classes = '' ) {

		echo foxiz_get_breadcrumb( $classes );
	}
}

if ( ! function_exists( 'foxiz_is_svg' ) ) {
	/**
	 * @param string $attachment
	 *
	 * @return bool
	 */
	function foxiz_is_svg( $attachment = '' ) {

		if ( substr( $attachment, - 4, 4 ) === '.svg' ) {

			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'foxiz_navigation_fallback' ) ) {
	/**
	 * @param array $settings
	 *
	 * @return false
	 */
	function foxiz_navigation_fallback( $settings = [] ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$menu_name = '';
		if ( isset( $settings['fallback_name'] ) ) {
			$menu_name = $settings['fallback_name'];
		} ?>
		<div class="rb-error">
			<p><?php printf( esc_html__( 'Please assign a menu to the "%s" location under ', 'foxiz' ), $menu_name ) ?>
				<a href="<?php echo get_admin_url( get_current_blog_id(), 'nav-menus.php?action=locations' ); ?>"><?php esc_html_e( 'Manage Locations', 'foxiz' ); ?></a>
			</p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'foxiz_get_twitter_name' ) ) {
	/**
	 * @return array|string|string[]|void
	 */
	function foxiz_get_twitter_name() {

		if ( is_single() ) {
			global $post;
			$name = get_the_author_meta( 'twitter_url', $post->post_author );
		} else {
			$name = foxiz_get_option( 'twitter' );
		}

		if ( empty( $name ) ) {
			$name = get_bloginfo( 'name' );
		}

		$name = parse_url( $name, PHP_URL_PATH );

		$name = str_replace( '/', '', (string) $name );

		return $name;
	}
}

if ( ! function_exists( 'foxiz_get_image_size' ) ) {
	/**
	 * @param $filename
	 *
	 * @return array|false
	 */
	function foxiz_get_image_size( $filename ) {

		if ( is_string( $filename ) ) {
			return @getimagesize( $filename );
		}

		return [];
	}
}

if ( ! function_exists( 'foxiz_calc_crop_sizes' ) ) {
	/**
	 * @return array[]
	 */
	function foxiz_calc_crop_sizes() {

		$settings = get_option( FOXIZ_TOS_ID );
		$crop     = true;
		if ( ! empty( $settings['crop_position'] ) && ( 'top' === $settings['crop_position'] ) ) {
			$crop = [ 'center', 'top' ];
		}

		$sizes = [
			'foxiz_crop_g1' => [ 330, 220, $crop ],
			'foxiz_crop_g2' => [ 420, 280, $crop ],
			'foxiz_crop_g3' => [ 615, 410, $crop ],
			'foxiz_crop_o1' => [ 860, 0, $crop ],
			'foxiz_crop_o2' => [ 1536, 0, $crop ],
		];

		foreach ( $sizes as $crop_id => $size ) {
			if ( empty( $settings[ $crop_id ] ) ) {
				unset( $sizes[ $crop_id ] );
			}
		}

		if ( ! empty( $settings['featured_crop_sizes'] ) && is_array( $settings['featured_crop_sizes'] ) ) {
			foreach ( $settings['featured_crop_sizes'] as $custom_size ) {
				if ( ! empty( $custom_size ) ) {
					$custom_size = preg_replace( '/\s+/', '', $custom_size );;
					$hw = explode( 'x', $custom_size );
					if ( isset( $hw[0] ) && isset( $hw[1] ) ) {
						$crop_id           = 'foxiz_crop_' . $custom_size;
						$sizes[ $crop_id ] = [ absint( $hw[0] ), absint( $hw[1] ), $crop ];
					}
				}
			}
		}

		return $sizes;
	}
}

if ( ! function_exists( 'foxiz_get_adsense' ) ) {
	function foxiz_get_adsense() {

		return false;
	}
}

if ( ! function_exists( 'foxiz_get_ad_image' ) ) {
	function foxiz_get_ad_image() {

		return false;
	}
}

if ( ! function_exists( 'foxiz_get_theme_mode' ) ) {
	/**
	 * @return string
	 */
	function foxiz_get_theme_mode() {

		return 'default';
	}
}

if ( ! function_exists( 'foxiz_get_active_plugins' ) ) {
	/**
	 * @return array
	 */
	function foxiz_get_active_plugins() {

		$active_plugins = (array) get_option( 'active_plugins', [] );
		if ( is_multisite() ) {
			$network_plugins = array_keys( get_site_option( 'active_sitewide_plugins', [] ) );
			if ( $network_plugins ) {
				$active_plugins = array_merge( $active_plugins, $network_plugins );
			}
		}

		sort( $active_plugins );

		return array_unique( $active_plugins );
	}
}

if ( ! function_exists( 'foxiz_conflict_schema' ) ) {
	/**
	 * @return bool
	 */
	function foxiz_conflict_schema() {

		$schema_conflicting_plugins = [
			'seo-by-rank-math/rank-math.php',
			'all-in-one-seo-pack/all_in_one_seo_pack.php',
		];

		$active_plugins = foxiz_get_active_plugins();
		if ( ! empty( $active_plugins ) ) {
			foreach ( $schema_conflicting_plugins as $plugin ) {
				if ( in_array( $plugin, $active_plugins, true ) ) {
					return true;
				}
			}
		}

		return false;
	}
}

if ( ! function_exists( 'foxiz_amp_suppressed_elementor' ) ) {
	function foxiz_amp_suppressed_elementor() {

		if ( foxiz_is_amp() ) {
			$amp_options        = get_option( 'amp-options' );
			$suppressed_plugins = ( ! empty( $amp_options['suppressed_plugins'] ) && is_array( $amp_options['suppressed_plugins'] ) ) ? $amp_options['suppressed_plugins'] : [];
			if ( ! empty( $suppressed_plugins['elementor'] ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'foxiz_is_elementor_active' ) ) {
	function foxiz_is_elementor_active() {

		if ( class_exists( 'Elementor\\Plugin' ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'foxiz_is_doing_ajax' ) ) {
	function foxiz_is_doing_ajax() {

		return function_exists( 'wp_doing_ajax' ) && wp_doing_ajax();
	}
}