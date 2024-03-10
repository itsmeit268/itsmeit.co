<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'foxiz_register_required_plugins' ) ) {
	function foxiz_register_required_plugins() {

		if ( ! function_exists( 'tgmpa' ) ) {
			return false;
		}

		$plugins = [
			[
				'name'               => esc_html__( 'Foxiz Core', 'foxiz' ),
				'slug'               => 'foxiz-core',
				'source'             => get_theme_file_path( 'plugins/foxiz-core.zip' ),
				'required'           => true,
				'version'            => '2.3',
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
				'is_callable'        => '',
			],
			[
				'name'     => esc_html__( 'Elementor Website Builder', 'foxiz' ),
				'slug'     => 'elementor',
				'required' => true,
			],
			[
				'name'               => 'Envato Market',
				'slug'               => 'envato-market',
				'source'             => get_theme_file_path( 'plugins/envato-market.zip' ),
				'force_activation'   => false,
				'force_deactivation' => false,
				'version'            => '2.0.11',
				'external_url'       => '',
			],
			[
				'name'     => esc_html__( 'Breadcrumb NavXT', 'foxiz' ),
				'slug'     => 'breadcrumb-navxt',
				'required' => false,
			],
			[
				'name'     => esc_html__( 'Post Views Counter', 'foxiz' ),
				'slug'     => 'post-views-counter',
				'required' => false,
			],
			[
				'name'     => esc_html__( 'HubSpot – CRM, Email Marketing, Live Chat...', 'foxiz' ),
				'slug'     => 'leadin',
				'required' => false,
			],
		];

		$foxiz_config = [
			'id'           => 'foxiz',
			'default_path' => '',
			'menu'         => 'foxiz-plugins',
			'parent_slug'  => 'themes.php',
			'capability'   => 'edit_theme_options',
			'has_notices'  => true,
			'dismissable'  => true,
			'dismiss_msg'  => '',
			'is_automatic' => false,
			'message'      => '',
			'strings'      => [
				'page_title'                      => esc_html__( 'Install Required Plugins', 'foxiz' ),
				'menu_title'                      => esc_html__( 'Install Plugins', 'foxiz' ),
				'installing'                      => esc_html__( 'Installing Plugin: %s', 'foxiz' ),
				'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'foxiz' ),
				'notice_can_install_required'     => _n_noop( 'Foxiz the following plugin: %1$s.', 'Foxiz requires the following plugins: %1$s.', 'foxiz' ),
				'notice_can_install_recommended'  => _n_noop( 'Foxiz recommends the following plugin: %1$s.', 'Foxiz recommends the following plugins: %1$s.', 'foxiz' ),
				'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'foxiz' ),
				'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'foxiz' ),
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'foxiz' ),
				'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'foxiz' ),
				'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with Foxiz: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with Foxiz: %1$s.', 'foxiz' ),
				'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'foxiz' ),
				'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'foxiz' ),
				'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'foxiz' ),
				'return'                          => esc_html__( 'Return to Required Plugins Installer', 'foxiz' ),
				'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'foxiz' ),
				'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'foxiz' ),
				'nag_type'                        => 'updated',
			],
		];

		tgmpa( $plugins, $foxiz_config );
	}
}