<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

add_action( 'after_switch_theme', 'foxiz_get_tos_defaults', 1 );
add_action( 'after_switch_theme', 'foxiz_redirect_plugin_activation', 2 );
add_action( 'after_setup_theme', [ 'Foxiz_Register_Options', 'get_instance' ], 0 );
add_action( 'tgmpa_register', 'foxiz_register_required_plugins' );
add_action( 'admin_enqueue_scripts', 'foxiz_enqueue_admin' );
add_action( 'enqueue_block_editor_assets', 'foxiz_enqueue_editor', 90 );
add_filter( 'ruby_taxonomies', 'foxiz_register_term_settings', 10, 1 );
add_filter( 'ruby_default_taxonomy', 'foxiz_register_default_term_settings', 10, 1 );

/** register admin scripts */
if ( ! function_exists( 'foxiz_enqueue_admin' ) ) {
	function foxiz_enqueue_admin( $hook ) {

		if ( $hook === 'post.php' || $hook === 'post-new.php' || 'widgets.php' === $hook || 'nav-menus.php' === $hook || 'term.php' === $hook ) {

			wp_register_style( 'foxiz-admin-style', get_theme_file_uri( 'assets/admin/admin.css' ), [], FOXIZ_THEME_VERSION, 'all' );
			wp_enqueue_style( 'foxiz-admin-style' );

			wp_register_script( 'foxiz-admin', get_theme_file_uri( 'assets/admin/admin.js' ), [ 'jquery' ], FOXIZ_THEME_VERSION, true );
			wp_enqueue_script( 'foxiz-admin' );
		}
	}
}

/** register editor scripts */
if ( ! function_exists( 'foxiz_enqueue_editor' ) ) {
	function foxiz_enqueue_editor() {

		$uri = 'assets/admin/editor.css';
		if ( is_rtl() ) {
			$uri = 'assets/admin/editor-rtl.css';
		}

		wp_enqueue_style( 'foxiz-google-font-editor', esc_url_raw( Foxiz_Font::get_instance()->get_font_url() ), [], FOXIZ_THEME_VERSION, 'all' );
		wp_enqueue_style( 'foxiz-editor-style', get_theme_file_uri( $uri ), [ 'foxiz-google-font-editor' ], FOXIZ_THEME_VERSION, 'all' );
	}
}

if ( ! function_exists( 'foxiz_get_tos_defaults' ) ) {
	/**
	 * @return false
	 */
	function foxiz_get_tos_defaults() {

		/** disable default elementor schemes */
		update_option( 'elementor_disable_color_schemes', 'yes' );
		update_option( 'elementor_disable_typography_schemes', 'yes' );

		$current = get_option( FOXIZ_TOS_ID, false );
		$file    = get_theme_file_path( 'assets/admin/defaults.json' );
		if ( ! is_file( $file ) || is_array( $current ) ) {
			return false;
		}

		ob_start();
		include $file;
		$response = ob_get_clean();
		$data     = json_decode( $response, true );
		if ( is_array( $data ) ) {
			set_transient( '_ruby_old_settings', $current, 30 * 86400 );
			update_option( FOXIZ_TOS_ID, $data );
		}

		return false;
	}
}

if ( ! function_exists( 'foxiz_redirect_plugin_activation' ) ) {
	/**
	 * redirect to activate plugin
	 */
	function foxiz_redirect_plugin_activation() {

		global $pagenow;

		if ( is_admin() && ! is_network_admin() && 'themes.php' === $pagenow && isset( $_GET['activated'] ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=foxiz-plugins' ) );
		}
	}
}
