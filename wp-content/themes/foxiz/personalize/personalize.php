<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Foxiz_Personalize', false ) ) {
	class Foxiz_Personalize {

		private static $instance;
		private $notification;
		private $enabled;
		private $enable_when;

		public static function get_instance() {

			if ( self::$instance === null ) {
				return new self();
			}

			return self::$instance;
		}

		/**
		 * Foxiz_Personalize constructor.
		 */
		public function __construct() {

			self::$instance = $this;

			$this->get_settings();

			if ( ! $this->enabled ) {
				return false;
			}

			add_action( 'body_class', [ $this, 'add_classes' ] );
			add_action( 'wp_footer', 'foxiz_bookmark_info_template' );

			/** for guest */
			if ( empty( $this->enable_when ) ) {
				add_action( 'wp_ajax_nopriv_rbbookmark', [ $this, 'bookmark_toggle' ] );
				add_action( 'wp_ajax_nopriv_rb_follow_category', [ $this, 'category_follow_toggle' ] );
				add_action( 'wp_ajax_nopriv_rb_follow_writer', [ $this, 'writer_follow_toggle' ] );
			}

			add_action( 'wp_ajax_rbbookmark', [ $this, 'bookmark_toggle' ] );
			add_action( 'wp_ajax_rb_follow_category', [ $this, 'category_follow_toggle' ] );
			add_action( 'wp_ajax_rb_follow_writer', [ $this, 'writer_follow_toggle' ] );
			add_action( 'wp_ajax_rbpersonalizedata', [ $this, 'sync_data' ] );
			add_action( 'wp_ajax_nopriv_rbpersonalizedata', [ $this, 'sync_data' ] );

			add_action( 'wp_ajax_nopriv_rbreadinglist', [ $this, 'reading_list' ] );
			add_action( 'wp_ajax_rbreadinglist', [ $this, 'reading_list' ] );

			add_action( 'wp_ajax_nopriv_rbcollect', [ $this, 'add_history' ] );
			add_action( 'wp_ajax_rbcollect', [ $this, 'add_history' ] );
		}

		public function get_settings() {

			$this->notification = foxiz_get_option( 'bookmark_notification' );
			$this->enable_when  = foxiz_get_option( 'bookmark_enable_when' );
			$this->enabled      = foxiz_get_option( 'bookmark_system' );
		}

		function add_classes( $classes ) {

			switch ( $this->enable_when ) {
				case 'ask_login':
					$classes[] = 'personalized-ask-login';
					break;
				case 'logged' :
					$classes[] = 'personalized-logged-only';
					break;
				default:
					$classes[] = 'personalized-all';
			}

			return $classes;
		}

		/**
		 * empty check
		 *
		 * @param $data
		 *
		 * @return bool
		 */
		public function is_empty( $data ) {

			if ( ! empty( $data ) && is_array( $data ) && count( $data ) ) {
				return false;
			}

			return true;
		}

		/**
		 * add bookmark
		 */
		public function bookmark_toggle() {

			if ( empty( $_POST['pid'] ) || ! class_exists( 'Foxiz_Personalize_Helper' ) ) {
				wp_send_json( [] );
			}

			$post_id  = intval( $_POST['pid'] );
			$response = [];

			if ( empty( $_POST['type'] ) || 'save' == $_POST['type'] ) {
				$response['action']      = 'saved';
				$response['description'] = foxiz_html__( 'This article has been added to reading list', 'foxiz' );
				Foxiz_Personalize_Helper::get_instance()->save_bookmark( $post_id );
			} else {
				$response['action']      = 'removed';
				$response['description'] = foxiz_html__( 'This article was removed from your bookmark', 'foxiz' );
				Foxiz_Personalize_Helper::get_instance()->delete_bookmark( $post_id );
			}

			if ( $this->notification ) {
				$response['title'] = get_the_title( $post_id );
				$response['image'] = get_the_post_thumbnail( $post_id, 'thumbnail' );
			}

			wp_send_json( $response );
		}

		/**
		 * @return array
		 */
		function category_follow_toggle() {

			if ( empty( $_POST['cid'] ) || ! class_exists( 'Foxiz_Personalize_Helper' ) ) {
				wp_send_json( [] );
			}

			$category_id = intval( $_POST['cid'] );
			$response    = [
				'action'      => 'saved',
				'description' => foxiz_html__( 'You are now following', 'foxiz' ),
			];

			if ( empty( $_POST['type'] ) || 'follow' == $_POST['type'] ) {
				Foxiz_Personalize_Helper::get_instance()->save_category( $category_id );
			} else {
				$response['action']      = 'removed';
				$response['description'] = foxiz_html__( 'You are no longer following', 'foxiz' );
				Foxiz_Personalize_Helper::get_instance()->delete_category( $category_id );
			}

			wp_send_json( $response );
		}

		function writer_follow_toggle() {

			if ( empty( $_POST['uid'] ) || ! class_exists( 'Foxiz_Personalize_Helper' ) ) {
				wp_send_json( [] );
			}

			$writer_id = intval( $_POST['uid'] );
			$response  = [
				'action'      => 'saved',
				'description' => foxiz_html__( 'You are now following', 'foxiz' ),
			];

			if ( empty( $_POST['type'] ) || 'follow' == $_POST['type'] ) {
				Foxiz_Personalize_Helper::get_instance()->save_writer( $writer_id );
			} else {
				$response['action']      = 'removed';
				$response['description'] = foxiz_html__( 'You are no longer following', 'foxiz' );
				Foxiz_Personalize_Helper::get_instance()->delete_writer( $writer_id );
			}

			wp_send_json( $response );
		}

		public function sync_data() {

			if ( ! is_user_logged_in() || ! class_exists( 'Foxiz_Personalize_Helper' ) ) {
				wp_send_json( [] );
				wp_die();
			}

			$data = [
				'bookmarks'  => Foxiz_Personalize_Helper::get_instance()->get_bookmarks(),
				'categories' => Foxiz_Personalize_Helper::get_instance()->get_categories_followed(),
				'writers'    => Foxiz_Personalize_Helper::get_instance()->get_writers_followed(),
			];

			wp_send_json( $data );
		}

		public function get_categories_followed() {

			if ( ! class_exists( 'Foxiz_Personalize_Helper' ) ) {
				return [];
			}

			/** restricted users */
			if ( ! is_user_logged_in() && ! empty( foxiz_get_option( 'bookmark_enable_when' ) ) ) {
				return [];
			}

			return Foxiz_Personalize_Helper::get_instance()->get_categories_followed();
		}

		public function get_writers_followed() {

			if ( ! class_exists( 'Foxiz_Personalize_Helper' ) ) {
				return [];
			}

			/** restricted guest */
			if ( ! is_user_logged_in() && ! empty( foxiz_get_option( 'bookmark_enable_when' ) ) ) {
				return [];
			}

			return Foxiz_Personalize_Helper::get_instance()->get_writers_followed();
		}

		/**
		 * @return array
		 * get categories with fallback
		 */
		public function get_my_categories() {

			$categories = $this->get_categories_followed();

			if ( ! $this->is_empty( $categories ) ) {
				return $categories;
			}

			$categories = get_categories( [
				'orderby' => 'count',
				'order'   => 'DESC',
				'number'  => 4,
			] );

			return wp_list_pluck( $categories, 'term_id' );
		}

		/**
		 * @return array
		 * get writers with fallback
		 */
		public function get_my_writers() {

			$writers = $this->get_writers_followed();

			if ( ! $this->is_empty( $writers ) ) {
				return $writers;
			}

			$role = [ 'author', 'editor' ];
			if ( foxiz_get_option( 'bookmark_author_admin' ) ) {
				$role[] = 'administrator';
			}

			$writers = get_users( [
				'blog_id'  => $GLOBALS['blog_id'],
				'orderby'  => 'post_count',
				'order'    => 'DESC',
				'number'   => 4,
				'role__in' => $role,
			] );

			return wp_list_pluck( $writers, 'ID' );
		}

		/**
		 * get saved post query
		 *
		 * @param array $settings
		 *
		 * @return false|WP_Query
		 */
		public function saved_posts_query( $settings = [] ) {

			if ( ! class_exists( 'Foxiz_Personalize_Helper' ) ) {
				return false;
			}

			$data = Foxiz_Personalize_Helper::get_instance()->get_bookmarks();

			if ( $this->is_empty( $data ) ) {
				return false;
			}

			if ( ! empty( $settings['posts_per_page'] ) ) {
				$posts_per_page = $settings['posts_per_page'];
			} else {
				$posts_per_page = - 1;
			}

			if ( ! empty( $settings['offset'] ) ) {
				$offset = absint( $settings['offset'] );
			} else {
				$offset = 0;
			}

			if ( ! empty( $settings['paged'] ) && ( $settings['paged'] > 1 ) ) {
				$offset = $offset + ( absint( $settings['paged'] ) - 1 ) * absint( $settings['posts_per_page'] );
			}

			$_query = new WP_Query( [
				'post_type'           => 'any',
				'post__in'            => $data,
				'orderby'             => 'post__in',
				'offset'              => $offset,
				'posts_per_page'      => $posts_per_page,
				'ignore_sticky_posts' => 1,
			] );

			/** update if posts was deleted */
			if ( isset( $GLOBALS['foxiz_queried_ids'] ) && ! empty( $_query->posts ) ) {
				$post_ids = wp_list_pluck( $_query->posts, 'ID' );
				if ( is_array( $post_ids ) ) {
					$GLOBALS['foxiz_queried_ids'] = array_unique( array_merge( $GLOBALS['foxiz_queried_ids'], $post_ids ) );
				}
			}

			if ( ! empty( $_query ) ) {
				$_query->set( 'content_source', 'saved' );
			}

			return $_query;
		}

		/**
		 * recommended_pre_query
		 *
		 * @param array $settings
		 *
		 * @return mixed|WP_Query|null
		 */
		public function recommended_pre_query( $settings = [] ) {

			if ( empty( $settings['paged'] ) ) {
				$settings['paged'] = 0;
			}

			if ( ! empty( $settings['offset'] ) ) {
				$offset = absint( $settings['offset'] );
			} else {
				$offset = 0;
			}

			if ( empty( $settings['posts_per_page'] ) ) {
				$settings['posts_per_page'] = foxiz_get_option( 'recommended_posts_per_page', get_option( 'posts_per_page' ) );
			}

			$categories = $this->get_categories_followed();
			$writers    = $this->get_writers_followed();

			if ( $this->is_empty( $categories ) && $this->is_empty( $writers ) ) {
				return foxiz_query( [
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'posts_per_page' => $settings['posts_per_page'],
					'offset'         => $offset,
					'order'          => 'popular_m',
					'post_not_in'    => ( ! empty( $settings['post_not_in'] ) ) ? $settings['post_not_in'] : '',
				], $settings['paged'] );
			}

			if ( $this->is_empty( $categories ) ) {
				return foxiz_query( [
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'author_in'      => $writers,
					'posts_per_page' => $settings['posts_per_page'],
					'post_not_in'    => ( ! empty( $settings['post_not_in'] ) ) ? $settings['post_not_in'] : '',
					'offset'         => $offset,
					'order'          => 'date_post',
				], $settings['paged'] );
			}

			if ( $this->is_empty( $writers ) ) {
				return foxiz_query( [
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'categories'     => $categories,
					'posts_per_page' => $settings['posts_per_page'],
					'post_not_in'    => ( ! empty( $settings['post_not_in'] ) ) ? $settings['post_not_in'] : '',
					'offset'         => $offset,
					'order'          => 'date_post',
				], $settings['paged'] );
			}

			$categories = implode( ',', $categories );
			$writers    = implode( ',', $writers );

			if ( ! empty( $settings['paged'] ) && ( $settings['paged'] > 1 ) ) {
				$offset = $offset + ( absint( $settings['paged'] ) - 1 ) * absint( $settings['posts_per_page'] );
			}

			global $wpdb;

			$data = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS wposts.ID FROM {$wpdb->posts} AS wposts
            LEFT JOIN {$wpdb->term_relationships} as wterm_relationships ON (wposts.ID = wterm_relationships.object_id)
            WHERE wterm_relationships.term_taxonomy_id IN ({$categories}) OR wposts.post_author IN ({$writers})
            AND wposts.post_type = 'post' AND wposts.post_status = 'publish' GROUP BY wposts.ID
            ORDER BY wposts.post_date DESC LIMIT {$offset} , {$settings['posts_per_page']}" );

			if ( ! empty( $data ) && is_array( $data ) ) {
				$found_posts   = (int) $wpdb->get_var( 'SELECT FOUND_ROWS();' );
				$max_num_pages = ceil( $found_posts / $settings['posts_per_page'] );

				$post_ins = wp_list_pluck( $data, 'ID' );
				if ( ! empty( $settings['post_not_in'] ) ) {
					$post_not_ins = explode( ',', $settings['post_not_in'] );
					$post_ins     = array_diff( $post_ins, $post_not_ins );
				}

				$_query = new WP_Query( [
						'post_type'      => 'post',
						'post_status'    => 'publish',
						'no_found_row'   => true,
						'post__in'       => $post_ins,
						'posts_per_page' => $settings['posts_per_page'],
						'order'          => 'date_post',
					]
				);

				if ( isset( $GLOBALS['foxiz_queried_ids'] ) && ! empty( $_query->posts ) ) {
					$post_ids = wp_list_pluck( $_query->posts, 'ID' );
					if ( is_array( $post_ids ) ) {
						$GLOBALS['foxiz_queried_ids'] = array_unique( array_merge( $GLOBALS['foxiz_queried_ids'], $post_ids ) );
					}
				}

				$_query->found_posts   = $found_posts;
				$_query->max_num_pages = $max_num_pages;

				return $_query;
			}

			return null;
		}

		/**
		 * @param array $settings
		 *
		 * @return mixed|WP_Query|null
		 */
		public function recommended_query( $settings = [] ) {

			$_query = $this->recommended_pre_query( $settings );

			if ( ! empty( $_query ) ) {
				$_query->set( 'content_source', 'recommended' );
			}

			return $_query;
		}

		function reading_list() {

			$response = [
				'saved'       => foxiz_my_saved_listing(),
				'categories'  => foxiz_my_categories_listing(),
				'writers'     => foxiz_my_writers_listing(),
				'recommended' => foxiz_my_recommended_listing(),
			];
			wp_send_json( $response );
		}

		/**
		 * add user history
		 */
		public function add_history() {

			if ( empty( $_GET['id'] ) || ! class_exists( 'Foxiz_Personalize_Helper' ) ) {
				wp_send_json_error( '' );
			}
			$post_id = intval( $_GET['id'] );
			Foxiz_Personalize_Helper::get_instance()->save_history( $post_id );

			wp_send_json_success( $post_id );
		}

		/**
		 * @param array $settings
		 *
		 * @return false|WP_Query
		 */
		public function reading_history_query( $settings = [] ) {

			if ( ! class_exists( 'Foxiz_Personalize_Helper' ) ) {
				return false;
			}

			$data = Foxiz_Personalize_Helper::get_instance()->get_history();

			if ( $this->is_empty( $data ) ) {
				return false;
			}

			if ( ! empty( $settings['posts_per_page'] ) ) {
				$posts_per_page = $settings['posts_per_page'];
			} else {
				$posts_per_page = - 1;
			}

			if ( ! empty( $settings['offset'] ) ) {
				$offset = absint( $settings['offset'] );
			} else {
				$offset = 0;
			}

			if ( ! empty( $settings['paged'] ) && ( $settings['paged'] > 1 ) ) {
				$offset = $offset + ( absint( $settings['paged'] ) - 1 ) * absint( $settings['posts_per_page'] );
			}

			$_query = new WP_Query( [
				'post_type'           => 'any',
				'post__in'            => $data,
				'orderby'             => 'post__in',
				'offset'              => $offset,
				'posts_per_page'      => $posts_per_page,
				'ignore_sticky_posts' => 1,
			] );

			if ( isset( $GLOBALS['foxiz_queried_ids'] ) && ! empty( $_query->posts ) ) {
				$post_ids = wp_list_pluck( $_query->posts, 'ID' );
				if ( is_array( $post_ids ) ) {
					$GLOBALS['foxiz_queried_ids'] = array_unique( array_merge( $GLOBALS['foxiz_queried_ids'], $post_ids ) );
				}
			}

			if ( ! empty( $_query ) ) {
				$_query->set( 'content_source', 'history' );
			}

			return $_query;
		}

	}
}

