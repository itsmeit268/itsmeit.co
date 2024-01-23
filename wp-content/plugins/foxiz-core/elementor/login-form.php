<?php

namespace foxizElementor\Widgets;
defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use function foxiz_render_login_form;
use function foxiz_render_user_login;

/**
 * Class Header_Login
 *
 * @package foxizElementor\Widgets
 */
class Login_Form extends Widget_Base {

	public function get_name() {

		return 'foxiz-login-form';
	}

	public function get_title() {

		return esc_html__( 'Foxiz - Login Form', 'foxiz-core' );
	}

	public function get_icon() {

		return 'eicon-person';
	}

	public function get_keywords() {

		return [ 'foxiz', 'ruby', 'dark', 'template', 'builder', 'user', 'register' ];
	}

	public function get_categories() {

		return [ 'foxiz_element' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'label_section', [
				'label' => esc_html__( 'Login Labels', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'lform_info',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Input custom labels for the login form. Leave the field blank to use the default labels.', 'foxiz-core' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);
		$this->add_control(
			'label_username',
			[
				'label'   => esc_html__( 'Username Label', 'foxiz-core' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 1,
				'ai'      => [ 'active' => false ],
				'default' => '',
			]
		);
		$this->add_control(
			'label_password',
			[
				'label'   => esc_html__( 'Password Label', 'foxiz-core' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 1,
				'ai'      => [ 'active' => false ],
				'default' => '',
			]
		);
		$this->add_control(
			'label_remember',
			[
				'label'   => esc_html__( 'Remember Label', 'foxiz-core' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 1,
				'ai'      => [ 'active' => false ],
				'default' => '',
			]
		);

		$this->add_control(
			'label_log_in',
			[
				'label'   => esc_html__( 'Login Button Label', 'foxiz-core' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 1,
				'ai'      => [ 'active' => false ],
				'default' => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'logged_section', [
				'label' => esc_html__( 'Logged Status', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'logged_status',
			[
				'label'       => esc_html__( 'Logged Status', 'foxiz-core' ),
				'description' => esc_html__( 'Select a layout if the user is logged in.', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'0' => esc_html__( 'None', 'foxiz-core' ),
					'1' => esc_html__( 'Status with Avatar', 'foxiz-core' ),
					'2' => esc_html__( 'Minimal', 'foxiz-core' ),
				],
				'default'     => '1',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'label_style_section', [
				'label' => esc_html__( 'Label', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'label_style',
			[
				'label'        => esc_html__( 'Label Style', 'foxiz-core' ),
				'type'         => Controls_Manager::SELECT,
				'description'  => esc_html__( 'Select a style for the login label.', 'foxiz-core' ),
				'options'      => [
					'none' => esc_html__( 'None', 'foxiz-core' ),
					'pipe' => esc_html__( 'Pipe (|)', 'foxiz-core' ),
					'dot'  => esc_html__( 'Dot (.)', 'foxiz-core' ),
				],
				'default'      => 'none',
				'prefix_class' => 'is-label-',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label'    => esc_html__( 'Label Font', 'foxiz-core' ),
				'name'     => 'label_font',
				'selector' => '{{WRAPPER}} label[for="user_login"], {{WRAPPER}} label[for="user_pass"], .logged-status-simple',
			]
		);
		$this->add_responsive_control(
			'label_spacing', [
				'label'       => esc_html__( 'Label Spacing', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => esc_html__( 'Input a custom bottom margin for the label.', 'foxiz-core' ),
				'selectors'   => [ '{{WRAPPER}}' => '--llabel-spacing: {{VALUE}}px;' ],
			]
		);
		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__( 'Text Color', 'foxiz-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}}' => '--llabel-color : {{VALUE}};' ],
			]
		);
		$this->add_control(
			'label_icon',
			[
				'label'     => esc_html__( 'Icon Color', 'foxiz-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}}' => '--licon-color : {{VALUE}};' ],
			]
		);
		$this->add_control(
			'dark_label_color',
			[
				'label'     => esc_html__( 'Dark Mode - Text Color', 'foxiz-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '[data-theme="dark"] {{WRAPPER}}' => '--llabel-color : {{VALUE}};' ],
			]
		);
		$this->add_control(
			'dark_label_icon',
			[
				'label'     => esc_html__( 'Dark Mode - Icon Color', 'foxiz-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '[data-theme="dark"] {{WRAPPER}}' => '--licon-color : {{VALUE}};' ],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'input_style_section', [
				'label' => esc_html__( 'Input Fields', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'input_spacing', [
				'label'       => esc_html__( 'Input Spacing', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => esc_html__( 'Input a custom spacing between input fields.', 'foxiz-core' ),
				'selectors'   => [ '{{WRAPPER}}' => '--linput-spacing: {{VALUE}}px;' ],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label'    => esc_html__( 'Input Font', 'foxiz-core' ),
				'name'     => 'input_font',
				'selector' => '{{WRAPPER}} input',
			]
		);
		$this->add_control(
			'input_style',
			[
				'label'        => esc_html__( 'Input Style', 'foxiz-core' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => [
					'bg'     => esc_html__( 'Gray Background', 'foxiz-core' ),
					'border' => esc_html__( 'Gray Border', 'foxiz-core' ),
				],
				'default'      => 'bg',
				'prefix_class' => 'is-input-',
			]
		);
		$this->add_responsive_control(
			'input_border', [
				'label'     => esc_html__( 'Border Radius', 'foxiz-core' ),
				'type'      => Controls_Manager::NUMBER,
				'selectors' => [ '{{WRAPPER}}' => '--round-7: {{VALUE}}px;' ],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'button_section', [
				'label' => esc_html__( 'Login Button', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label'    => esc_html__( 'Button Font', 'foxiz-core' ),
				'name'     => 'button_font',
				'selector' => '{{WRAPPER}} input[type="submit"]',
			]
		);
		$this->add_control(
			'button_width', [
				'label'     => esc_html__( 'Button Width', 'foxiz-core' ),
				'type'      => Controls_Manager::NUMBER,
				'selectors' => [ '{{WRAPPER}}' => '--lbutton-width: {{VALUE}}px;' ],
			]
		);
		$this->add_responsive_control(
			'button_border', [
				'label'     => esc_html__( 'Border Radius', 'foxiz-core' ),
				'type'      => Controls_Manager::NUMBER,
				'selectors' => [ '{{WRAPPER}}' => '--round-3: {{VALUE}}px;' ],
			]
		);
		$this->add_responsive_control(
			'button_padding',
			[
				'label'       => esc_html__( 'Inner Padding', 'foxiz-core' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'description' => esc_html__( 'Input a custom padding for the login button', 'foxiz-core' ),
				'selectors'   => [
					'{{WRAPPER}}' => '--lbutton-padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);
		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Text Color', 'foxiz-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}}' => '--awhite : {{VALUE}};' ],
			]
		);
		$this->add_control(
			'button_bg',
			[
				'label'     => esc_html__( 'Background', 'foxiz-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}}' => '--g-color : {{VALUE}}; --g-color-90: {{VALUE}}e6;' ],
			]
		);
		$this->add_control(
			'dark_button_color',
			[
				'label'     => esc_html__( 'Dark Mode - Text Color', 'foxiz-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '[data-theme="dark"] {{WRAPPER}}' => '--awhite : {{VALUE}};' ],
			]
		);
		$this->add_control(
			'dark_button_bg',
			[
				'label'     => esc_html__( 'Dark Mode - Background', 'foxiz-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '[data-theme="dark"]  {{WRAPPER}}' => '--g-color : {{VALUE}}; --g-color-90: {{VALUE}}e6;' ],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'form_meta_section', [
				'label' => esc_html__( 'Remember & Lost your password?', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label'    => esc_html__( 'Meta Font', 'foxiz-core' ),
				'name'     => 'meta_font',
				'selector' => '{{WRAPPER}} .is-meta, {{WRAPPER}} .login-remember label, {{WRAPPER}} .s-logout-link',
			]
		);
		$this->add_control(
			'meta_color',
			[
				'label'     => esc_html__( 'Meta Color', 'foxiz-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}}' => '--meta-fcolor : {{VALUE}};' ],
			]
		);
		$this->add_control(
			'dark_meta_color',
			[
				'label'     => esc_html__( 'Dark Mode - Meta Color', 'foxiz-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '[data-theme="dark"] {{WRAPPER}}' => '--meta-fcolor : {{VALUE}};' ],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'padding_section', [
				'label' => esc_html__( 'Inner Padding', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'padding_info',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'These settings below allow you to set different inner spacing for the login form and logged status. Using Advanced tab for further style settings: background, shadow and border...', 'foxiz-core' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);
		$this->add_responsive_control(
			'form_padding',
			[
				'label'       => esc_html__( 'Login Form', 'foxiz-core' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'description' => esc_html__( 'Input a custom inner padding for the login form.', 'foxiz-core' ),
				'selectors'   => [
					'{{WRAPPER}}' => '--lform-padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);
		$this->add_responsive_control(
			'logged_padding',
			[
				'label'       => esc_html__( 'Logged Status', 'foxiz-core' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'description' => esc_html__( 'Input a custom padding for the logged status.', 'foxiz-core' ),
				'selectors'   => [
					'{{WRAPPER}}' => '--lstatus-padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);
		$this->end_controls_section();
	}

	/**
	 * render layout
	 */
	protected function render() {

		$settings         = $this->get_settings();
		$settings['uuid'] = 'uid_' . $this->get_id();
		if ( Plugin::$instance->editor->is_edit_mode() ) {
			foxiz_render_login_form( $settings );
		} else {
			foxiz_render_user_login( $settings );
		}
	}
}