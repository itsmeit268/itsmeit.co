<?php
/** FOXIZ functions */
define( 'FOXIZ_THEME_VERSION', '2.2.2' );

if ( ! defined( 'FOXIZ_TOS_ID' ) ) {
	define( 'FOXIZ_TOS_ID', 'foxiz_theme_options' );
}

include_once get_theme_file_path( 'include-files.php' );

add_action( 'after_setup_theme', 'foxiz_theme_setup', 10 );
add_action( 'wp_enqueue_scripts', 'foxiz_register_script_frontend', 990 );
add_action( 'widgets_init', 'foxiz_register_all_sidebars' );

if ( function_exists( 'load_theme_textdomain' ) ) {
	load_theme_textdomain( 'foxiz', get_theme_file_path( 'languages' ) );
}

/** setup */
if ( ! function_exists( 'foxiz_theme_setup' ) ) {
	function foxiz_theme_setup() {

		if ( ! isset( $GLOBALS['content_width'] ) ) {
			$GLOBALS['content_width'] = 1170;
		}

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', [
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
		] );
		add_theme_support( 'post-formats', [ 'gallery', 'video', 'audio' ] );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'editor-style' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'woocommerce', [
			'gallery_thumbnail_image_width' => 110,
			'thumbnail_image_width'         => 300,
			'single_image_width'            => 760,
		] );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		if ( empty( $settings['widget_block_editor'] ) ) {
			remove_theme_support( 'widgets-block-editor' );
		}
		register_nav_menus( [
			'foxiz_main'         => esc_html__( 'Main Menu', 'foxiz' ),
			'foxiz_mobile'       => esc_html__( 'Mobile Menu', 'foxiz' ),
			'foxiz_mobile_quick' => esc_html__( 'Mobile Quick Access', 'foxiz' ),
		] );

		$crop_data = foxiz_calc_crop_sizes();
		foreach ( $crop_data as $crop_id => $size ) {
			add_image_size( $crop_id, $size[0], $size[1], $size[2] );
		}

		/** load */
		add_action( 'admin_init', 'Foxiz_Register_Metaboxes::get_instance' );
		if ( class_exists( 'Foxiz_Personalize' ) ) {
			add_action( 'init', 'Foxiz_Personalize::get_instance', 1 );
		}
		add_action( 'init', 'Foxiz_Ajax::get_instance', 2 );
	}
}

/* register scripts */
if ( ! function_exists( 'foxiz_register_script_frontend' ) ) {
	function foxiz_register_script_frontend() {

		$style_deps = [
			'foxiz-font',
			'foxiz-main',
		];

		$script_deps = [
			'jquery',
			'jquery-waypoints',
			'rbswiper',
			'jquery-magnific-popup',
		];

		$main_filename        = 'main';
		$woocommerce_filename = 'woocommerce';
		$podcast_filename     = 'podcast';

		if ( is_rtl() ) {
			$main_filename        = 'rtl';
			$woocommerce_filename = 'woocommerce-rtl';
			$podcast_filename     = 'podcast-rtl';
		}
		wp_register_style( 'foxiz-font', esc_url_raw( Foxiz_Font::get_instance()->get_font_url() ), [], FOXIZ_THEME_VERSION, 'all' );
		if ( foxiz_get_option( 'font_awesome' ) ) {
			array_unshift( $style_deps, 'font-awesome' );
			wp_deregister_style( 'font-awesome' );
			wp_register_style( 'font-awesome', get_theme_file_uri( 'assets/css/font-awesome.css' ), [], '6.1.1', 'all' );
		}

		wp_register_style( 'foxiz-main', get_theme_file_uri( 'assets/css/' . $main_filename . '.css' ), [], FOXIZ_THEME_VERSION, 'all' );

		if ( foxiz_get_option( 'podcast_supported' ) ) {
			$style_deps[] = 'foxiz-podcast';
			wp_register_style( 'foxiz-podcast', get_theme_file_uri( 'assets/css/' . $podcast_filename . '.css' ), [], FOXIZ_THEME_VERSION, 'all' );
		}

		if ( ! foxiz_is_amp() ) {
			$style_deps[] = 'foxiz-print';
			wp_register_style( 'foxiz-print', get_theme_file_uri( 'assets/css/print.css' ), [], FOXIZ_THEME_VERSION, 'all' );
		}

		if ( class_exists( 'WooCommerce' ) && ! foxiz_is_amp() ) {
			wp_deregister_style( 'yith-wcwl-font-awesome' );
			$style_deps[] = 'foxiz-woocommerce';
			wp_register_style( 'foxiz-woocommerce', get_theme_file_uri( 'assets/css/' . $woocommerce_filename . '.css' ), [], FOXIZ_THEME_VERSION, 'all' );
		}

		wp_register_style( 'foxiz-style', get_stylesheet_uri(), $style_deps, FOXIZ_THEME_VERSION, 'all' );
		wp_enqueue_style( 'foxiz-style' );

		if ( ! foxiz_is_amp() ) {

			wp_register_script( 'html5', get_theme_file_uri( 'assets/js/html5shiv.min.js' ), [], '3.7.3' );
			wp_script_add_data( 'html5', 'conditional', 'lt IE 9' );
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}
			wp_register_script( 'jquery-waypoints', get_theme_file_uri( 'assets/js/jquery.waypoints.min.js' ), [ 'jquery' ], '3.1.1', true );
			wp_register_script( 'rbswiper', get_theme_file_uri( 'assets/js/rbswiper.min.js' ), [], '6.5.8', true );
			wp_register_script( 'jquery-magnific-popup', get_theme_file_uri( 'assets/js/jquery.mp.min.js' ), [ 'jquery' ], '1.1.0', true );

			if ( foxiz_get_option( 'site_tooltips' ) && ! foxiz_is_wc_pages() ) {
				$script_deps[] = 'rb-tipsy';
				wp_register_script( 'rb-tipsy', get_theme_file_uri( 'assets/js/jquery.tipsy.min.js' ), [ 'jquery' ], '1.0', true );
			}
			if ( foxiz_get_option( 'single_post_highlight_shares' ) ) {
				$script_deps[] = 'highlight-share';
				wp_register_script( 'highlight-share', get_theme_file_uri( 'assets/js/highlight-share.js' ), '1.1.0', true );
			}
			if ( foxiz_get_option( 'back_top' ) ) {
				$script_deps[] = 'jquery-uitotop';
				wp_register_script( 'jquery-uitotop', get_theme_file_uri( 'assets/js/jquery.ui.totop.min.js' ), [ 'jquery' ], 'v1.2', true );
			}

			if ( class_exists( 'FOXIZ_CORE' ) ) {
				$script_deps[] = 'foxiz-core';
				if ( foxiz_get_option( 'bookmark_system' ) ) {
					$script_deps[] = 'foxiz-personalize';
					wp_register_script( 'foxiz-personalize', get_theme_file_uri( 'assets/js/personalized.js' ), [
						'jquery',
						'foxiz-core',
					], FOXIZ_THEME_VERSION, true );
				}
			}

			wp_register_script( 'foxiz-global', get_theme_file_uri( 'assets/js/global.js' ), $script_deps, FOXIZ_THEME_VERSION, true );
			wp_localize_script( 'foxiz-global', 'foxizParams', foxiz_get_js_settings() );

			wp_enqueue_script( 'foxiz-global' );
		}
	}
}