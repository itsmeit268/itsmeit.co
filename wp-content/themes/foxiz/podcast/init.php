<?php
/** Don't load directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once get_theme_file_path( 'podcast/helpers.php' );
include_once get_theme_file_path( 'podcast/configs/settings.php' );
include_once get_theme_file_path( 'podcast/configs/metaboxes.php' );
include_once get_theme_file_path( 'podcast/configs/taxonomy.php' );
include_once get_theme_file_path( 'podcast/parts.php' );
include_once get_theme_file_path( 'podcast/modules.php' );
include_once get_theme_file_path( 'podcast/single-layout.php' );
include_once get_theme_file_path( 'podcast/blocks/grid-flex-1.php' );
include_once get_theme_file_path( 'podcast/blocks/list-flex-1.php' );
include_once get_theme_file_path( 'podcast/blocks/overlay-flex-1.php' );

if ( ! class_exists( 'Foxiz_Podcast' ) ) {
	class Foxiz_Podcast {

		protected static $instance = null;
		private $settings = array();

		static function get_instance() {

			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function __construct() {

			self::$instance = $this;
			$this->get_settings();

			if ( empty( $this->settings['podcast_supported'] ) ) {
				return;
			}

			add_filter( 'rb_meta_boxes', array( $this, 'register_meta_boxes' ) );
			add_action( 'pre_get_posts', array( $this, 'include_query' ), 99, 1 );
			add_filter( 'template_include', array( $this, 'template_include' ), 25 );
			add_filter( 'foxiz_read_more', array( $this, 'readmore_label' ) );
			add_action( 'foxiz_featured_image', 'foxiz_podcast_icon', 10 );
			add_action( 'foxiz_featured_image', 'foxiz_podcast_socials_overlay', 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		}

		function register_meta_boxes( $metaboxes = array() ) {

			$metaboxes[] = foxiz_podcast_single_metaboxes();

			return $metaboxes;
		}

		public function get_settings() {

			$this->settings = get_option( FOXIZ_TOS_ID, array() );
		}

		public function include_query( $query ) {

			if ( is_admin() || ! $query->is_main_query() ) {
				return false;
			}

			if ( $query->is_home() ) {
				if ( ! empty( $this->settings['podcast_blog_included'] ) ) {
					$query->set( 'post_type', array( 'post', 'podcast' ) );
				}
			} elseif ( $query->is_author() ) {
				if ( ! empty( $this->settings['podcast_author_included'] ) ) {
					$query->set( 'post_type', array( 'post', 'podcast' ) );
				}
			} elseif ( $query->is_tag() ) {
				if ( ! empty( $this->settings['podcast_tag_included'] ) ) {
					$query->set( 'post_type', array( 'post', 'podcast' ) );
				}
			} elseif ( $query->is_tax( 'series' ) ) {

				$query->set( 'post_status', 'publish' );

				$data = rb_get_term_meta( 'foxiz_category_meta', get_queried_object_id() );
				if ( ! empty( $data['posts_per_page'] ) ) {
					$posts_per_page = $data['posts_per_page'];
				} else {
					$posts_per_page = foxiz_get_option( 'series_posts_per_page' );
				}

				if ( ! empty( $posts_per_page ) ) {
					$query->set( 'posts_per_page', absint( $posts_per_page ) );
				}
				if ( ! empty( $data['order_by'] ) ) {
					$order_by = $data['order_by'];
				} else {
					$order_by = foxiz_get_option( 'series_order_by' );
				}
				if ( ! empty( $order_by ) ) {
					switch ( $order_by ) {
						case 'post_index' :
							$query->set( 'meta_key', 'ruby_index' );
							$query->set( 'orderby', 'meta_value' );
							$query->set( 'order', 'ASC' );
							break;
						case 'post_index_desc' :
							$query->set( 'meta_key', 'ruby_index' );
							$query->set( 'orderby', 'meta_value' );
							$query->set( 'order', 'DECS' );
							break;
					}
				}

				if ( ! empty( $data['tag_not_in'] ) ) {
					$tags    = explode( ',', $data['tag_not_in'] );
					$tags    = array_unique( $tags );
					$tag_ids = array();
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
			} elseif ( $query->is_post_type_archive( 'podcast' ) ) {
				$posts_per_page = foxiz_get_option( 'podcast_archive_posts_per_page' );

				if ( ! empty( $posts_per_page ) ) {
					$query->set( 'posts_per_page', absint( $posts_per_page ) );
				}
			}

			return false;
		}

		public function template_include( $template ) {

			$file_name = '';
			if ( is_singular( 'podcast' ) ) {
				$file_name = 'single-podcast.php';
			} elseif ( is_tax( 'series' ) ) {
				$file_name = 'category-podcast.php';
			} elseif ( is_post_type_archive( 'podcast' ) ) {
				$file_name = 'archive-podcast.php';
			}

			if ( empty( $file_name ) ) {
				return $template;
			}

			$priority_template = locate_template( $file_name );
			if ( ! empty( $priority_template ) ) {
				return $priority_template;
			}
			$file = get_theme_file_path( '/podcast/' . $file_name );
			if ( file_exists( $file ) ) {
				return $file;
			}

			return $template;
		}

		public function readmore_label( $label ) {

			if ( 'podcast' == get_post_type() && ! empty( $this->settings['podcast_readmore_label'] ) ) {
				return $this->settings['podcast_readmore_label'];
			}

			return $label;
		}

		function enqueue() {

			if ( ! foxiz_is_amp() && ! is_admin() ) {
				wp_register_script( 'foxiz-player', get_theme_file_uri( 'assets/js/media-element.min.js' ), array( 'jquery' ), FOXIZ_THEME_VERSION, true );
			}
		}
	}
}

/** load */
Foxiz_Podcast::get_instance();
