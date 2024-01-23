<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

?><div class="rb-panel-wrap rb-panel-redirect">
	<div class="rb-panel-header">
		<div class="rb-panel-heading">
			<h1><?php echo esc_html__( 'Activate', 'foxiz-core' ) . ' ' . wp_get_theme()->Name . ''; ?></h1>
			<p class="sub-heading"><?php echo esc_html__( 'Please activate your copy of the theme in order to get access to Import Demos panel and Theme Options.', 'foxiz-core' ); ?></p>
			<?php echo rb_admin_get_template_part('admin/templates/links'); ?>
		</div>
	</div>
	<div class="rb-redirect-content">
		<a class="rb-panel-button" href="<?php menu_page_url( RB_ADMIN_CORE::get_instance()->panel_menu_slug ); ?>"><?php esc_html_e('Go to Activate Page', 'foxiz-core'); ?></a>
	</div>
</div>