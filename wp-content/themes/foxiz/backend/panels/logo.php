<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'foxiz_register_options_logo' ) ) {
	function foxiz_register_options_logo() {

		return [
			'id'    => 'foxiz_config_section_site_logo',
			'title' => esc_html__( 'Logo', 'foxiz' ),
			'icon'  => 'el el-barcode',
		];
	}
}

if ( ! function_exists( 'foxiz_register_options_logo_global' ) ) {
	function foxiz_register_options_logo_global() {

		return [
			'id'         => 'foxiz_config_section_global_logo',
			'title'      => esc_html__( 'Default Logos', 'foxiz' ),
			'desc'       => esc_html__( 'Upload logos for you website.', 'foxiz' ),
			'icon'       => 'el el-laptop',
			'subsection' => true,
			'fields'     => [
				[
					'id'    => 'info_add_favico',
					'type'  => 'info',
					'style' => 'info',
					'desc'  => esc_html__( 'Navigate to "Appearance > Customize > Site Identity > Site Icon" to add the favico icon.', 'foxiz' ),
				],
				[
					'id'    => 'info_add_logo_dark',
					'type'  => 'info',
					'style' => 'warning',
					'desc'  => esc_html__( 'Ensure dark mode logos are set if you enable dark mode for your site.', 'foxiz' ),
				],
				[
					'id'          => 'logo',
					'type'        => 'media',
					'url'         => true,
					'preview'     => true,
					'title'       => esc_html__( 'Main Logo', 'foxiz' ),
					'subtitle'    => esc_html__( 'Select or upload a logo for your site.', 'foxiz' ),
					'description' => esc_html__( 'The recommended height value is 60px.', 'foxiz' ),
				],
				[
					'id'          => 'dark_logo',
					'type'        => 'media',
					'url'         => true,
					'preview'     => true,
					'title'       => esc_html__( 'Dark Mode - Main Logo', 'foxiz' ),
					'subtitle'    => esc_html__( 'Select or upload a logo for your site in dark mode.', 'foxiz' ),
					'description' => esc_html__( 'The image sizes should be the same as the main logo.', 'foxiz' ),
				],
				[
					'id'          => 'retina_logo',
					'type'        => 'media',
					'url'         => true,
					'preview'     => true,
					'title'       => esc_html__( 'Retina Main Logo', 'foxiz' ),
					'subtitle'    => esc_html__( 'Select or upload a retina logo (x2 size).', 'foxiz' ),
					'description' => esc_html__( 'The recommended height value is 120px.', 'foxiz' ),
				],
				[
					'id'          => 'dark_retina_logo',
					'type'        => 'media',
					'url'         => true,
					'preview'     => true,
					'title'       => esc_html__( 'Dark Mode - Retina Main Logo', 'foxiz' ),
					'subtitle'    => esc_html__( 'Select or upload a retina logo in dark mode.', 'foxiz' ),
					'description' => esc_html__( 'The image sizes should be the same as the retina main logo.', 'foxiz' ),
				],
			],
		];
	}
}

if ( ! function_exists( 'foxiz_register_options_logo_mobile' ) ) {
	/**
	 * @return array
	 */
	function foxiz_register_options_logo_mobile() {

		return [
			'id'         => 'foxiz_config_section_mobile_logo',
			'title'      => esc_html__( 'Mobile Logos', 'foxiz' ),
			'desc'       => esc_html__( 'Customize the mobile logos.', 'foxiz' ),
			'icon'       => 'el el-iphone-home',
			'subsection' => true,
			'fields'     => [
				[
					'id'          => 'mobile_logo',
					'type'        => 'media',
					'url'         => true,
					'preview'     => true,
					'title'       => esc_html__( 'Mobile Logo', 'foxiz' ),
					'subtitle'    => esc_html__( 'Upload a retina logo for displaying on mobile devices.', 'foxiz' ),
					'description' => esc_html__( 'The recommended height value is 84px.', 'foxiz' ),
				],
				[
					'id'          => 'dark_mobile_logo',
					'type'        => 'media',
					'url'         => true,
					'preview'     => true,
					'title'       => esc_html__( 'Dark Mode - Mobile Logo', 'foxiz' ),
					'subtitle'    => esc_html__( 'Upload a retina logo for displaying on mobile devices in dark mode.', 'foxiz' ),
					'description' => esc_html__( 'The image sizes should be the same as the mobile logo.', 'foxiz' ),
				],
			],
		];
	}
}

if ( ! function_exists( 'foxiz_register_options_logo_transparent' ) ) {
	function foxiz_register_options_logo_transparent() {

		return [
			'id'         => 'foxiz_config_section_transparent_logo',
			'title'      => esc_html__( 'Transparent Logos', 'foxiz' ),
			'desc'       => esc_html__( 'Manage logos for the transprent headers.', 'foxiz' ),
			'icon'       => 'el el-photo',
			'subsection' => true,
			'fields'     => [
				[
					'id'          => 'transparent_logo',
					'type'        => 'media',
					'url'         => true,
					'preview'     => true,
					'title'       => esc_html__( 'Transparent Logo', 'foxiz' ),
					'subtitle'    => esc_html__( 'Upload a logo for the transparent headers.', 'foxiz' ),
					'description' => esc_html__( 'We recommended that it is being a white color image (60px).', 'foxiz' ),
				],
				[
					'id'       => 'transparent_retina_logo',
					'type'     => 'media',
					'url'      => true,
					'preview'  => true,
					'title'    => esc_html__( 'Retina Transparent Logo', 'foxiz' ),
					'subtitle' => esc_html__( 'Upload a retina logo for the transparent header (x2 size).', 'foxiz' ),
				],
			],
		];
	}
}

if ( ! function_exists( 'foxiz_register_options_logo_favicon' ) ) {
	/**
	 * @return array
	 */
	function foxiz_register_options_logo_favicon() {

		return [
			'id'         => 'foxiz_config_section_logo_favicon',
			'title'      => esc_html__( 'Bookmarklet', 'foxiz' ),
			'desc'       => esc_html__( 'Select or upload bookmarklet icons for iOS and Android devices.', 'foxiz' ),
			'icon'       => 'el el-bookmark',
			'subsection' => true,
			'fields'     => [
				[
					'id'          => 'icon_touch_apple',
					'type'        => 'media',
					'url'         => true,
					'preview'     => true,
					'title'       => esc_html__( 'iOS Bookmarklet Icon', 'foxiz' ),
					'subtitle'    => esc_html__( 'Upload an icon for the Apple touch.', 'foxiz' ),
					'description' => esc_html__( 'The recommended image size is 72 x 72px', 'foxiz' ),
				],
				[
					'id'          => 'icon_touch_metro',
					'type'        => 'media',
					'url'         => true,
					'preview'     => true,
					'title'       => esc_html__( 'Metro UI Bookmarklet Icon', 'foxiz' ),
					'description' => esc_html__( 'The recommended image size is 144 x 144px', 'foxiz' ),
					'subtitle'    => esc_html__( 'Upload icon for the Metro interface.', 'foxiz' ),
				],
			],
		];
	}
}
