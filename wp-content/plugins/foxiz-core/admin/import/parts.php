<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'rb_importer_select_form' ) ) {
	function rb_importer_select_form( $directory ) {

		echo '<div class="data-select-wrap">';
		echo '<h3 class="rb-import-header">' . esc_html__( 'Choose to Import', 'foxiz-core' ) . '</h3>';
		echo '<div class="data-select">';
		echo '<div class="data-select-el data-all">';
		echo '<a href="#" id="rb-all-' . esc_attr( $directory ) . '" data-title="rb_import_all" class="rb-importer-checkbox rb_import_all" data-checked="0"><span class="import-label">' . esc_html__( 'Import All Demo', 'foxiz-core' ) . '</span></a>';
		echo '</div>';
		echo '<div class="data-select-el">';
		echo '<a href="#" id="rb-content-' . esc_attr( $directory ) . '" data-title="rb_import_content" class="rb-importer-checkbox rb_import_content" data-checked="0"><span class="import-label">' . esc_html__( 'Content', 'foxiz-core' ) . '</span></a>';
		echo '</div>';
		echo '<div class="data-select-el">';
		echo '<a href="#" id="rb-page-' . esc_attr( $directory ) . '" data-title="rb_import_pages" class="rb-importer-checkbox rb_import_pages" data-checked="0"><span class="import-label">' . esc_html__( 'Only Pages', 'foxiz-core' ) . '</span></a>';
		echo '</div>';
		echo '<div class="data-select-el">';
		echo '<a href="#" id="rb-tops-' . esc_attr( $directory ) . '" data-title="rb_import_tops" class="rb-importer-checkbox rb_import_tops" data-checked="0"><span class="import-label">' . esc_html__( 'Theme Options', 'foxiz-core' ) . '</span></a>';
		echo '</div>';
		echo '<div class="data-select-el">';
		echo '<a href="#" id="rb-widgets-' . esc_attr( $directory ) . '" data-title="rb_import_widgets" class="rb-importer-checkbox rb_import_widgets" data-checked="0"><span class="import-label">' . esc_html__( 'Widgets', 'foxiz-core' ) . '</span></a>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
}

if ( ! function_exists( 'rb_importer_plugins_form' ) ) {
	/**
	 * @param        $plugins
	 * @param string $nonce
	 */
	function rb_importer_plugins_form( $plugins, $nonce = '' ) {

		$site_plugins = get_plugins();
		$repo         = 'https://wordpress.org/plugins/';

		foreach ( $plugins as $plugin ) {

			if ( empty( $plugin['name'] ) || empty( $plugin['slug'] ) ) {
				continue;
			}

			$classname = 'plugin-el';
			if ( ! empty( $plugin['class'] ) ) {
				$classname .= ' ' . $plugin['class'];
			}

			if ( empty( $plugin['file'] ) ) {
				$plugin_plug = $plugin['slug'] . '/' . $plugin['slug'] . '.php';
			} else {
				$plugin_plug = $plugin['slug'] . '/' . $plugin['file'] . '.php';
			}

			if ( array_key_exists( $plugin_plug, $site_plugins ) ) {

				$classname     .= ' installed';
				$yes_activated = is_plugin_active( $plugin_plug );

				$info = strtolower( $plugin['info'] );
				if ( ! $yes_activated && ! empty( $plugin['info'] ) && ( 'recommended' === $info || 'required' === $info ) ) {
					$classname .= ' show-dismiss';
				}

				/** plugin installed */
				echo '<div class="' . esc_attr( $classname ) . '">';
				echo '<span class="name">' . esc_html( $plugin['name'] );
				if ( ! empty( $plugin['info'] ) ) {
					echo '<span class="info">(' . esc_html( $plugin['info'] ) . ')</span>';
				}
				echo '</span>';
				if ( $yes_activated ) {
					echo '<span class="activate-info activated">' . esc_html__( 'Activated', 'foxiz-core' ) . '</span>';
				} else {
					$active_link = wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $plugin_plug ), 'activate-plugin_' . $plugin_plug );
					echo '<a href="' . $active_link . '" class="activate-info activate rb-activate-plugin">' . esc_html__( 'Activate', 'foxiz-core' ) . '</a>';
				}
				echo '</div>';
			} else {
				/** plugin not install */
				if ( ! empty( $plugin['source'] ) ) {
					$classname = 'plugin-el';

					if ( ! empty( $plugin['class'] ) ) {
						$classname .= ' ' . $plugin['class'];
					}

					$info = strtolower( $plugin['info'] );
					if ( 'recommended' === $info || 'required' === $info ) {
						$classname .= ' show-dismiss';
					}

					echo '<div class="' . esc_attr( $classname ) . ' rb-repackage-plugin">';
					echo '<span class="name">' . esc_html( $plugin['name'] );
					if ( ! empty( $plugin['info'] ) ) {
						echo '<span class="info">(' . esc_html( $plugin['info'] ) . ')</span>';
					}
					echo '</span>';
					echo '</span>';
					echo '<a href="#" class="activate-info activate rb-install-package" data-slug="' . $plugin['slug'] . '" data-action="rb_install_package" data-package="' . base64_encode( $plugin['source'] ) . '" data-nonce="' . $nonce . '">' . esc_html__( 'Install Package', 'foxiz-importer' ) . '</a>';
					echo '</div>';
				} else {
					$install_link = wp_nonce_url(
						add_query_arg(
							[
								'action' => 'install-plugin',
								'plugin' => $plugin['slug'],
							],
							admin_url( 'update.php' )
						),
						'install-plugin' . '_' . $plugin['slug']
					);

					echo '<div class="' . esc_attr( $classname ) . ' install">';
					echo '<span class="name"><a class="plugin-link" href="' . $repo . $plugin['slug'] . '" target="_blank">' . esc_html( $plugin['name'] ) . '</a>';
					if ( ! empty( $plugin['info'] ) ) {
						echo '<span class="info">(' . esc_html( $plugin['info'] ) . ')</span>';
					}
					echo '</span>';
					echo '</span>';
					echo '<a href="' . $install_link . '" class="activate-info activate rb-activate-plugin is-install">' . esc_html__( 'Install', 'foxiz-core' ) . '</a>';
					echo '</div>';
				}
			}
		}
	}
}
