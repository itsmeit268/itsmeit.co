<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'foxiz_protocol' ) ) {
	/**
	 * @return string
	 * get protocol
	 */
	function foxiz_protocol() {

		if ( is_ssl() ) {
			return 'https';
		}

		return 'http';
	}
}

if ( ! function_exists( 'foxiz_is_amp' ) ) {
	/** is amp */
	function foxiz_is_amp() {

		return function_exists( 'amp_is_request' ) && amp_is_request();
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

if ( ! function_exists( 'foxiz_count_content' ) ) {
	/**
	 * @param string $content
	 *
	 * @return false|int|string[]
	 * word counter
	 */
	function foxiz_count_content( $content = '' ) {

		if ( empty( $content ) ) {
			return 0;
		}

		$content = preg_replace( '/(<\/[^>]+?>)(<[^>\/][^>]*?>)/', '$1 $2', $content );
		$content = nl2br( $content );
		$content = strip_tags( $content );
		if ( preg_match( "/[\x{4e00}-\x{9fa5}]+/u", $content ) ) {
			$count = mb_strlen( $content, get_bloginfo( 'charset' ) );
		} elseif ( preg_match( "/[А-Яа-яЁё]/u", $content ) ) {
			$count = count( preg_split( '~[^\p{L}\p{N}\']+~u', $content ) );
		} elseif ( preg_match( "/[\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7A3}]+/u", $content ) ) {
			$count = count( preg_split( '/[^\p{L}\p{N}\']+/', $content ) );
		} elseif ( preg_match( "/[\x{3040}-\x{309F}\x{30A0}-\x{30FF}]+/u", $content ) ) {
			$count = count( preg_split( '/[^\p{L}\p{N}\']+/', $content ) );
		} else {
			$count = count( preg_split( '/\s+/', $content ) );
		}

		return $count;
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
			$number = str_replace( '.00', '', number_format( ( $number / 1000000 ), 2 ) ) . foxiz_attr__( 'M', 'foxiz-core' );
		} elseif ( $number > 999 ) {
			$number = str_replace( '.0', '', number_format( ( $number / 1000 ), 1 ) ) . foxiz_attr__( 'k', 'foxiz-core' );
		}

		return $number;
	}
}

if ( ! function_exists( 'foxiz_is_ruby_template' ) ) {
	function foxiz_is_ruby_template() {

		if ( 'rb-etemplate' !== get_post_type() && is_admin() ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'foxiz_extract_number' ) ) {
	/**
	 * @param $str
	 *
	 * @return int
	 * extract number
	 */
	function foxiz_extract_number( $str ) {

		return intval( preg_replace( '/[^0-9]+/', '', $str ), 10 );
	}
}

if ( ! function_exists( 'foxiz_query_order_selection' ) ) {
	function foxiz_query_order_selection( $settings = [] ) {

		$configs = [
			'date_post'               => esc_html__( 'Last Published', 'foxiz-core' ),
			'update'                  => esc_html__( 'Last Updated', 'foxiz-core' ),
			'comment_count'           => esc_html__( 'Popular Comment', 'foxiz-core' ),
			'popular'                 => esc_html__( 'Popular (by Post Views)', 'foxiz-core' ),
			'popular_3d'              => esc_html__( 'Popular Published Last 3 Days', 'foxiz-core' ),
			'popular_w'               => esc_html__( 'Popular Published Last 7 Days', 'foxiz-core' ),
			'popular_m'               => esc_html__( 'Popular Published Last 30 Days', 'foxiz-core' ),
			'popular_3m'              => esc_html__( 'Popular Published Last 3 Months', 'foxiz-core' ),
			'popular_6m'              => esc_html__( 'Popular Published Last 6 Months', 'foxiz-core' ),
			'popular_y'               => esc_html__( 'Popular Published Last Year', 'foxiz-core' ),
			'top_review'              => esc_html__( 'Top Review', 'foxiz-core' ),
			'top_review_3d'           => esc_html__( 'Top Review Published Last 3 Days', 'foxiz-core' ),
			'top_review_w'            => esc_html__( 'Top Review Published Last 7 Days', 'foxiz-core' ),
			'top_review_m'            => esc_html__( 'Top Review Published Last 30 Days', 'foxiz-core' ),
			'top_review_3m'           => esc_html__( 'Top Review Published Last 3 Months', 'foxiz-core' ),
			'top_review_6m'           => esc_html__( 'Top Review Published Last 6 Months', 'foxiz-core' ),
			'top_review_y'            => esc_html__( 'Top Review Published Last Year', 'foxiz-core' ),
			'last_review'             => esc_html__( 'Latest Review', 'foxiz-core' ),
			'post_type'               => esc_html__( 'Post Type', 'foxiz-core' ),
			'sponsored'               => esc_html__( 'Latest Sponsored', 'foxiz-core' ),
			'rand'                    => esc_html__( 'Random', 'foxiz-core' ),
			'rand_w'                  => esc_html__( 'Random last 7 Days', 'foxiz-core' ),
			'rand_m'                  => esc_html__( 'Random last 30 Days', 'foxiz-core' ),
			'rand_3m'                 => esc_html__( 'Random last 3 Months', 'foxiz-core' ),
			'rand_6m'                 => esc_html__( 'Random last 6 Months', 'foxiz-core' ),
			'rand_y'                  => esc_html__( 'Random last Last Year', 'foxiz-core' ),
			'author'                  => esc_html__( 'Author', 'foxiz-core' ),
			'new_live'                => esc_html__( 'Last Published Live', 'foxiz-core' ),
			'update_live'             => esc_html__( 'Last Updated Live', 'foxiz-core' ),
			'new_flive'               => esc_html__( 'Last Live (Archived Included)', 'foxiz-core' ),
			'update_flive'            => esc_html__( 'Last Updated Live (Archived Included)', 'foxiz-core' ),
			'alphabetical_order_decs' => esc_html__( 'Title DECS', 'foxiz-core' ),
			'alphabetical_order_asc'  => esc_html__( 'Title ACS', 'foxiz-core' ),
			'by_input'                => esc_html__( 'by input IDs Data (Post IDs filter)', 'foxiz-core' ),
		];

		if ( is_array( $settings ) && count( $settings ) ) {
			$configs = array_merge( $configs, $settings );
		}

		return $configs;
	}
}

if ( ! function_exists( 'foxiz_create_widget_heading_field' ) ) {
	/**
	 * @param array $param
	 * create text field
	 */
	function foxiz_create_widget_heading_field( $param = [] ) {

		$param = wp_parse_args( $param, [
			'id'          => '',
			'title'       => '',
			'name'        => '',
			'description' => '',
		] );
		?>
		<div class="rb-w-input rb-wheading">
			<h3><?php echo esc_html( $param['title'] ); ?></h3>
			<?php echo ( $param['description'] ) ? '<p>' . html_entity_decode( esc_html( $param['description'] ) ) . '</p>' : false; ?>
			<label for="<?php echo esc_attr( $param['id'] ); ?>"></label>
		</div>
	<?php }
}

if ( ! function_exists( 'foxiz_create_widget_text_field' ) ) {
	/**
	 * @param array $param
	 * create text field
	 */
	function foxiz_create_widget_text_field( $param = [] ) {

		$param = wp_parse_args( $param, [
			'id'          => '',
			'title'       => '',
			'name'        => '',
			'value'       => '',
			'description' => '',
			'placeholder' => '',
		] );
		?>
		<div class="rb-w-input">
			<h4><?php echo esc_html( $param['title'] ); ?></h4>
			<?php echo ( $param['description'] ) ? '<p>' . html_entity_decode( esc_html( $param['description'] ) ) . '</p>' : false; ?>
			<label for="<?php echo esc_attr( $param['id'] ); ?>"></label>
			<input placeholder="<?php echo esc_attr( $param['placeholder'] ); ?>" class="widefat" id="<?php echo esc_attr( $param['id'] ); ?>" name="<?php echo esc_attr( $param['name'] ); ?>" type="text" value="<?php echo esc_html( $param['value'] ); ?>"/>
		</div>
	<?php }
}

if ( ! function_exists( 'foxiz_create_widget_select_field' ) ) {
	/**
	 * @param array $param
	 * create select field
	 */
	function foxiz_create_widget_select_field( $param = [] ) {

		$param = wp_parse_args( $param, [
			'id'          => '',
			'title'       => '',
			'name'        => '',
			'options'     => [],
			'data'        => '',
			'value'       => '',
			'description' => '',
		] );

		if ( ! empty( $param['data'] ) ) {
			switch ( $param['data'] ) {
				case 'menu' :
					$param['options'] = foxiz_menu_selection();
					break;
				case 'menu_locations' :
					$param['options'] = foxiz_menu_location_selection();
					break;
				case 'page' :
					$param['options'] = foxiz_page_selection();
					break;
				case 'sidebar' :
					$param['options'] = foxiz_sidebar_selection();
					break;
				case 'user' :
					$param['options'] = foxiz_user_selection();
					break;
				case 'template' :
					$param['options'] = foxiz_ruby_template_selection();
					break;
				case 'category' :
					$param['options'] = foxiz_config_cat_selection();
					break;
				case 'on-off' :
					$param['options'] = [
						'on'  => esc_html__( 'Enable', 'foxiz-core' ),
						'off' => esc_html__( 'Disable', 'foxiz-core' ),
					];
					break;
			}
		}
		?>
		<div class="rb-w-input">
			<h4><?php echo esc_html( $param['title'] ); ?></h4>
			<?php echo ( $param['description'] ) ? '<p>' . html_entity_decode( esc_html( $param['description'] ) ) . '</p>' : false; ?>
			<label for="<?php echo esc_attr( $param['id'] ); ?>"></label>
			<select class="widefat" id="<?php echo esc_attr( $param['id'] ); ?>" name="<?php echo esc_attr( $param['name'] ); ?>">
				<?php foreach ( $param['options'] as $option_value => $option_title ) :
					if ( (string) $param['value'] === (string) $option_value ) : ?>
						<option value="<?php echo esc_html( $option_value ); ?>" selected><?php echo esc_html( $option_title ); ?></option>
					<?php else: ?>
						<option value="<?php echo esc_html( $option_value ); ?>"><?php echo esc_html( $option_title ); ?></option>
					<?php endif;
				endforeach; ?>
			</select>
		</div>
	<?php }
}

if ( ! function_exists( 'foxiz_create_widget_textarea_field' ) ) {
	/**
	 * @param array $param
	 * create textarea field
	 */
	function foxiz_create_widget_textarea_field( $param = [] ) {

		$param = wp_parse_args( $param, [
			'id'          => '',
			'title'       => '',
			'name'        => '',
			'value'       => '',
			'description' => '',
			'row'         => '',
			'placeholder' => '',
		] );

		if ( empty( $param['row'] ) ) {
			$param['row'] = 4;
		}
		?>
		<div class="rb-w-input">
			<h4><?php echo esc_html( $param['title'] ); ?></h4>
			<?php echo ( $param['description'] ) ? '<p>' . html_entity_decode( esc_html( $param['description'] ) ) . '</p>' : false; ?>
			<label for="<?php echo esc_attr( $param['id'] ); ?>"></label>
			<textarea placeholder="<?php echo esc_attr( $param['placeholder'] ); ?>" rows="<?php echo esc_attr( $param['row'] ); ?>>" cols="10" id="<?php echo esc_attr( $param['name'] ); ?>" name="<?php echo esc_attr( $param['name'] ); ?>" class="widefat"><?php echo esc_html( $param['value'] ); ?></textarea>
		</div>
		<?php
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

		$translated = esc_html__( $text, $domain );
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

		$translated = esc_attr__( $text, $domain );
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

if ( ! function_exists( 'foxiz_menu_location_selection' ) ) {
	/**
	 * @return array
	 * get nav selection
	 */
	function foxiz_menu_location_selection() {

		$data = [];
		global $_wp_registered_nav_menus;

		if ( is_array( $_wp_registered_nav_menus ) ) {
			foreach ( $_wp_registered_nav_menus as $key => $value ) {
				$data[ $key ] = $value;
			}
		}

		return $data;
	}
}

if ( ! function_exists( 'foxiz_page_selection' ) ) {
	/**
	 * @return array
	 * get page select
	 */
	function foxiz_page_selection() {

		$data                   = [];
		$args['posts_per_page'] = - 1;
		$pages                  = get_pages( $args );

		if ( ! empty ( $pages ) ) {
			foreach ( $pages as $page ) {
				$data[ $page->ID ] = $page->post_title;
			}
		}

		return $data;
	}
}

if ( ! function_exists( 'foxiz_menu_selection' ) ) {
	/**
	 * @return array
	 * menu selection
	 */
	function foxiz_menu_selection() {

		$data  = [];
		$menus = wp_get_nav_menus();
		if ( ! empty ( $menus ) ) {
			foreach ( $menus as $item ) {
				$data[ $item->term_id ] = $item->name;
			}
		}

		return $data;
	}
}

if ( ! function_exists( 'foxiz_config_cat_selection' ) ) {
	/**
	 * @param false  $dynamic
	 * @param string $post_type
	 *
	 * @return array
	 */
	function foxiz_config_cat_selection( $dynamic = false, $post_type = 'post' ) {

		$data = [
			'0' => esc_html__( '-- All categories --', 'foxiz-core' ),
		];

		if ( $dynamic ) {
			$data['dynamic'] = esc_html__( 'Dynamic Query', 'foxiz-core' );
		}

		$categories = get_categories( [
			'hide_empty' => 0,
			'type'       => $post_type,
			'parent'     => '0',
		] );

		$pos = 1;
		foreach ( $categories as $index => $item ) {
			$children = get_categories( [
				'hide_empty' => 0,
				'type'       => $post_type,
				'child_of'   => $item->term_id,
			] );
			if ( ! empty( $children ) ) {
				array_splice( $categories, $pos + $index, 0, $children );
				$pos += count( $children );
			}
		}

		foreach ( $categories as $item ) {
			$deep = '';
			if ( ! empty( $item->parent ) ) {
				$deep = '--';
			}
			$data[ $item->term_id ] = $deep . ' ' . esc_attr( $item->name ) . ' - [ID: ' . esc_attr( $item->term_id ) . ' / Posts: ' . foxiz_count_posts_category( $item ) . ']';
		}

		return $data;
	}
}

if ( ! function_exists( 'foxiz_count_posts_category' ) ) {
	/**
	 * @param $item
	 *
	 * @return int
	 */
	function foxiz_count_posts_category( $item ) {

		$count     = $item->category_count;
		$tax_terms = get_terms( 'category', [
			'child_of' => $item->term_id,
		] );
		foreach ( $tax_terms as $tax_term ) {
			$count += $tax_term->count;
		}

		return intval( $count );
	}
}

if ( ! function_exists( 'foxiz_ruby_template_selection' ) ) {
	/**
	 * @return array
	 * get page select
	 */
	function foxiz_ruby_template_selection() {

		$data                   = [];
		$args['posts_per_page'] = - 1;
		$args['post_type']      = 'rb-etemplate';
		$templates              = get_posts( $args );
		$data[0]                = esc_html__( '- Select a Template -', 'foxiz-core' );

		if ( ! empty ( $templates ) ) {
			foreach ( $templates as $template ) {
				$data[ $template->ID ] = $template->post_title . ' - [Ruby_E_Template id="' . $template->ID . '"]';
			}
		}

		return $data;
	}
}

if ( ! function_exists( 'foxiz_sidebar_selection' ) ) {
	/**
	 * @return array
	 * sidebar selection
	 */
	function foxiz_sidebar_selection() {

		$data = [];

		global $wp_registered_sidebars;

		foreach ( $wp_registered_sidebars as $key => $value ) {
			$data[ $key ] = $value['name'];
		}

		return $data;
	}
}

if ( ! function_exists( 'foxiz_user_selection' ) ) {
	/**
	 * @return array
	 * user selection
	 */
	function foxiz_user_selection() {

		$data  = [];
		$users = get_users();
		if ( ! empty ( $users ) ) {
			foreach ( $users as $user ) {
				$data[ $user->ID ] = $user->display_name;
			}
		}

		return $data;
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

if ( ! class_exists( 'Rb_Category_Select_Walker', false ) ) {
	/**
	 * Class Rb_Category_Select_Walker
	 */
	class Rb_Category_Select_Walker extends Walker {

		var $tree_type = 'category';
		var $cat_array = [];
		var $db_fields = [
			'id'     => 'term_id',
			'parent' => 'parent',
		];

		public function start_lvl( &$output, $depth = 0, $args = [] ) {
		}

		public function end_lvl( &$output, $depth = 0, $args = [] ) {
		}

		public function start_el( &$output, $object, $depth = 0, $args = [], $current_object_id = 0 ) {

			$this->cat_array[ str_repeat( ' - ', $depth ) . $object->name . ' - [ ID: ' . $object->term_id . ' / Posts: ' . $object->category_count . ' ]' ] = $object->term_id;
		}

		public function end_el( &$output, $object, $depth = 0, $args = [] ) {
		}
	}
}

if ( ! function_exists( 'foxiz_get_breadcrumb' ) ) {
	function foxiz_get_breadcrumb( $classes = '' ) {

		if ( ! foxiz_get_option( 'breadcrumb' ) ) {
			return false;
		}

		ob_start();
		$class_name = 'breadcrumb-wrap';
		if ( foxiz_get_option( 'breadcrumb_style' ) ) {
			$class_name .= ' breadcrumb-line-wrap';
		}

		if ( function_exists( 'bcn_display' ) ) :
			$class_name .= ' breadcrumb-navxt';
			if ( ! empty( $classes ) ) {
				$class_name .= ' ' . $classes;
			} ?>
			<aside class="<?php echo esc_attr( $class_name ); ?>">
				<div class="breadcrumb-inner" vocab="<?php echo foxiz_protocol(); ?>://schema.org/" typeof="BreadcrumbList"><?php bcn_display(); ?></div>
			</aside>
		<?php
		else :
			if ( function_exists( 'yoast_breadcrumb' ) ) {
				$class_name .= ' breadcrumb-yoast';
				if ( ! empty( $classes ) ) {
					$class_name .= ' ' . $classes;
				}
				yoast_breadcrumb( '<aside class="' . esc_attr( $class_name ) . '"><div class="' . esc_attr( $class_name ) . '">', '</div></aside>' );
			} elseif ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
				$class_name .= ' rank-math-breadcrumb';
				if ( ! empty( $classes ) ) {
					$class_name .= ' ' . $classes;
				}
				rank_math_the_breadcrumbs( [
					'wrap_before' => '<nav aria-label="breadcrumbs" class="' . esc_attr( $class_name ) . '"><p class="breadcrumb-inner">',
					'wrap_after'  => '</p></nav>',
				] );
			}

		endif;

		return ob_get_clean();
	}
}

if ( ! function_exists( 'foxiz_calc_average_rating' ) ) {
	/**
	 * @param $post_id
	 *
	 * @return false
	 */
	function foxiz_calc_average_rating( $post_id ) {

		global $wpdb;

		$data         = [];
		$total_review = [];
		$raw_total    = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_value, COUNT( * ) as meta_value_count FROM $wpdb->commentmeta
			LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
			WHERE meta_key = 'rbrating'
			AND comment_post_ID = %d
			AND comment_approved = '1'
			AND meta_value > 0
			GROUP BY meta_value",
				$post_id
			)
		);

		foreach ( $raw_total as $count ) {
			$total_review[] = absint( $count->meta_value_count );
		}

		$data['count'] = array_sum( $total_review );

		$ratings = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM(meta_value) FROM $wpdb->commentmeta
				LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
				WHERE meta_key = 'rbrating'
				AND comment_post_ID = %d
				AND comment_approved = '1'
				AND meta_value > 0",
				$post_id
			)
		);

		if ( ! empty( $data['count'] ) && ! empty( $ratings ) ) {
			$data['average'] = number_format( $ratings / $data['count'], 1, '.', '' );
		}

		update_post_meta( $post_id, 'foxiz_user_rating', $data );

		return false;
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

if ( ! function_exists( 'foxiz_get_theme_mode' ) ) {
	/**
	 * @return string
	 */
	function foxiz_get_theme_mode() {

		$dark_mode = foxiz_get_option( 'dark_mode' );

		if ( empty( $dark_mode ) || 'browser' === $dark_mode ) {
			return 'default';
		} elseif ( 'dark' === $dark_mode ) {
			return 'dark';
		}

		$is_cookie_mode = (string) foxiz_get_option( 'dark_mode_cookie' );
		if ( '1' === $is_cookie_mode ) {
			$id = FOXIZ_CORE::get_instance()->get_dark_mode_id();
			if ( ! empty( $_COOKIE[ $id ] ) ) {
				return $_COOKIE[ $id ];
			}
		}

		$first_visit_mode = foxiz_get_option( 'first_visit_mode' );
		if ( empty( $first_visit_mode ) ) {
			$first_visit_mode = 'default';
		}

		return $first_visit_mode;
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

if ( ! function_exists( 'foxiz_ajax_localize_script' ) ) {
	/**
	 * @param $id
	 * @param $js_settings
	 *
	 * @return false
	 */
	function foxiz_ajax_localize_script( $id, $js_settings ) {

		if ( empty( $id ) ) {
			return false;
		}

		if ( ! empty( $js_settings['live_block'] ) ) {
			if ( ! empty( $js_settings['paged'] ) ) {
				$js_settings['paged'] = 0;
			}
			if ( empty( $js_settings['page_max'] ) ) {
				$js_settings['page_max'] = 2;
			}
			$output = '<script>';
			$output .= esc_attr( $id ) . '.paged = ' . $js_settings['paged'] . ';';
			$output .= esc_attr( $id ) . '.page_max = ' . $js_settings['page_max'] . ';';
			$output .= '</script>';
			echo $output;
		} else {
			echo '<script> var ' . esc_attr( $id ) . ' = ' . wp_json_encode( $js_settings ) . '</script>';
		}
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

if ( ! function_exists( 'foxiz_wc_strip_wrapper' ) ) {
	function foxiz_wc_strip_wrapper( $html ) {

		if ( empty( $html ) || ! class_exists( 'DOMDocument', false ) ) {
			return false;
		}

		$output = '';
		libxml_use_internal_errors( true );
		$dom = new DOMDocument();
		@$dom->loadHTML( '<?xml encoding="' . get_bloginfo( 'charset' ) . '" ?>' . $html );
		libxml_clear_errors();
		$xpath = new DomXPath( $dom );
		$nodes = $xpath->query( "//*[contains(@class, 'products ')]" );
		if ( $nodes->item( 0 ) ) {
			foreach ( $nodes->item( 0 )->childNodes as $node ) {
				$output .= $dom->saveHTML( $node );
			}
		}

		return $output;
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

if ( ! function_exists( 'foxiz_get_twitter_name' ) ) {
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