<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! current_user_can( 'install_plugins' ) ) {
	wp_die( '<div class="notice notice-warning"><p>'.esc_html__( 'We\'re sorry, you are not authorized to install demos on this site.', 'foxiz-core' ).'</p></div>' );
}

$nonce = wp_create_nonce( 'rb-core' );
?>
<div class="rb-demos-wrap">
	<div class="importer-header">
		<div class="importer-headline">
            <h2><i class="dashicons dashicons-laptop" aria-hidden="true"></i><?php esc_html_e( 'Ruby Demo Importer', 'rb-importer' ); ?></h2>
            <a href="#" id="rb-importer-reload"><i class="dashicons dashicons-download"></i><?php esc_html_e( 'Update Demos', 'rb-importer' ); ?></a>
        </div>
		<div class="importer-desc">
			<p>Importing the theme demo will enable you to make quick edits without having to create content from scratch. Please refrain from navigating away from this page while the importer processes the data, as this may take between 5 ~ 7 minutes, depending on server speed.</p>
            <p>We are unable to include certain demo images in the content due to copyright restrictions. As a result, the images may appear different from the demo. However, the demo structures will remain intact, and you can replace the images with your own if desired.</p>
		</div>
		<div class="importer-tips">
			<p><strong>Import Tips:</strong></p>
			<p>- If the import process takes longer than 5 minutes, please refresh this page and try importing again.</p>
			<p>- If your website already has existing data, you have the option to selectively import Pages, Widgets, and Theme Options.</p>
            <p>- You are <strong>NOT</strong> required to install or activate the recommended and optional plugins if you choose not to use them.</p>
			<p>- If you intend to set up a shop, install and activate the WooCommerce plugin before initiating the import process.</p>
			<p>- Refer to the online documentation available: <a href="https://help.themeruby.com/foxiz" target="_blank">HERE</a></p>
		</div>
	</div>
	<div class="rb-demos">
		<?php
		if ( ! empty( $demos ) && is_array( $demos ) ) :
			foreach ( $demos as $directory => $demo ) :
			if ( ! empty( $demo['imported'] ) && is_array( $demo['imported'] ) ) {
				$imported       = true;
				$item_classes   = 'rb-demo-item active is-imported';
				$import_message = esc_html__( 'Already Imported', 'foxiz-core' );
			} else {
				$item_classes   = 'rb-demo-item not-imported';
				$imported       = false;
				$import_message = esc_html__( 'Import Demo', 'foxiz-core' );
			} ?>
			<div class="<?php echo esc_attr( $item_classes ); ?>" data-directory="<?php echo $directory; ?>" data-nonce="<?php echo $nonce ?>" data-action="rb_importer">
				<div class="inner-item">
					<div class="demo-preview">
						<div class="demo-process-bar"><span class="process-percent"></span></div>
						<img class="demo-image" src="<?php echo esc_html( $demo['preview'] ); ?>" alt="<?php esc_attr( $demo['name'] ); ?>"/>
						<span class="demo-status"><?php echo esc_html( $import_message ); ?></span>
						<span class="process-count">0%</span>
					</div>
					<div class="demo-content">
						<div class="demo-name">
							<h3><?php echo $demo['name']; ?></h3>
							<?php if ( ! empty( $demo['demo_url'] ) ) : ?>
								<a href="<?php echo $demo['demo_url']; ?>" target="_blank" rel="nofollow"><i class="dashicons-laptop dashicons"></i><?php esc_html_e( 'Live Preview', 'foxiz-core' ) ?></a>
							<?php endif ?>
						</div>
						<?php if ( is_array( $demo['plugins'] ) ) : ?>
							<div class="demo-plugins">
								<h4><?php esc_html_e( 'Plugins Recommended', 'foxiz-core' ); ?></h4>
								<?php rb_importer_plugins_form( $demo['plugins'], $nonce ); ?>
							</div>
						<?php endif;
						rb_importer_select_form( $directory );
						?>
						<div class="import-actions">
							<?php if ( ! $imported ) : ?>
								<div class="rb-importer-btn-wrap">
									<span class="rb-wait"><span class="rb-loading"><i class="dashicons dashicons-update" aria-hidden="true"></i></span><span><?php esc_html_e( 'Importing...', 'foxiz-core' ); ?></span></span>
									<span class="rb-do-import rb-importer-btn rb-disabled"><?php esc_html_e( 'Import Demo', 'foxiz-core' ) ?></span>
									<span class="rb-importer-completed"><?php esc_html_e( 'Import Complete', 'foxiz-core' ); ?></span>
								</div>
							<?php else : ?>
								<div class="rb-importer-btn-wrap">
									<span class="rb-wait"><span class="rb-loading"><i class="dashicons dashicons-update" aria-hidden="true"></i></span><span><?php esc_html_e( 'Importing...', 'foxiz-core' ); ?></span></span>
									<span class="rb-do-reimport rb-importer-btn rb-disabled"><?php esc_html_e( 'Re-Import', 'foxiz-core' ); ?></span>
									<span class="rb-importer-completed"><?php esc_html_e( 'Import Complete', 'foxiz-core' ); ?></span>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		<?php
		endforeach;
		else: ?>
			<p>Something was wrong. Please click the "Update Demos" button to reload the data or contact the support team for assistance.</p>
		<?php endif; ?>
	</div>
</div>
