<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'foxiz_register_options_page' ) ) {
	/**
	 * @return array
	 */
	function foxiz_register_options_page() {

		$prefix = 'page_';

		return [
			'id'     => 'foxiz_config_section_page',
			'title'  => esc_html__( 'Single Page', 'foxiz' ),
			'icon'   => 'el el-list-alt',
			'desc'   => esc_html__( 'Customize the styles and layout of the single page.', 'foxiz' ),
			'fields' => [
				[
					'id'     => 'section_start_page_header',
					'type'   => 'section',
					'class'  => 'ruby-section-start',
					'title'  => esc_html__( 'General', 'foxiz' ),
					'indent' => true,
				],
				[
					'id'       => $prefix . 'page_header_style',
					'type'     => 'select',
					'title'    => esc_html__( ' Page Header Style', 'foxiz' ),
					'subtitle' => esc_html__( 'Select a header style for the single page.', 'foxiz' ),
					'options'  => foxiz_config_page_header_dropdown( false ),
					'default'  => '1',
				],
				[
					'id'       => $prefix . 'header_width',
					'type'     => 'select',
					'title'    => esc_html__( 'Max Width of Page Header', 'foxiz' ),
					'subtitle' => esc_html__( 'Select a max-width (edge padding included) for the page header content.', 'foxiz' ),
					'options'  => [
						'small' => esc_html__( 'Small - 860px', 'foxiz' ),
						'0'     => esc_html__( 'Full Width - 1280px', 'foxiz' ),
					],
					'default'  => '0',
				],
				[
					'id'     => 'section_end_page_header',
					'type'   => 'section',
					'class'  => 'ruby-section-end',
					'indent' => false,
				],
				[
					'id'     => 'section_start_page_sidebar',
					'type'   => 'section',
					'class'  => 'ruby-section-start',
					'title'  => esc_html__( 'Sidebar Area', 'foxiz' ),
					'indent' => true,
				],
				[
					'id'       => $prefix . 'sidebar_position',
					'type'     => 'image_select',
					'title'    => esc_html__( 'Sidebar Position', 'foxiz' ),
					'subtitle' => esc_html__( 'Select a sidebar position or disable it for the single page.', 'foxiz' ),
					'options'  => foxiz_config_sidebar_position(),
					'default'  => 'none',
				],
				[
					'id'       => $prefix . 'sidebar_name',
					'type'     => 'select',
					'title'    => esc_html__( 'Assign a Sidebar', 'foxiz' ),
					'subtitle' => esc_html__( 'Assign a widget section for the sidebar for the single page if it is enabled.', 'foxiz' ),
					'options'  => foxiz_config_sidebar_name( false ),
					'default'  => 'foxiz_sidebar_default',
				],
				[
					'id'       => $prefix . 'sticky_sidebar',
					'type'     => 'select',
					'title'    => esc_html__( 'Sticky Sidebar', 'foxiz' ),
					'subtitle' => esc_html__( 'Enable or disable sticky sidebar feature for the single page.', 'foxiz' ),
					'options'  => [
						'default' => esc_html__( 'Use Global Setting', 'foxiz' ),
						'1'       => esc_html__( 'Enable', 'foxiz' ),
						'-1'      => esc_html__( 'Disable', 'foxiz' ),
					],
					'default'  => 'default',
				],
				[
					'id'          => $prefix . 'width_wo_sb',
					'type'        => 'select',
					'title'       => esc_html__( 'Max Width Content without Sidebar', 'foxiz' ),
					'subtitle'    => esc_html__( 'Select a max-width (edge padding included) for the page content.', 'foxiz' ),
					'description' => esc_html__( 'This setting will apply only to pages without sidebar.', 'foxiz' ),
					'options'     => [
						'small' => esc_html__( 'Small - 860px', 'foxiz' ),
						'0'     => esc_html__( 'Full Width - 1280px', 'foxiz' ),
					],
					'default'     => 'small',
				],
				[
					'id'     => 'section_end_page_sidebar',
					'type'   => 'section',
					'class'  => 'ruby-section-end no-border',
					'indent' => false,
				],
			],
		];
	}
}