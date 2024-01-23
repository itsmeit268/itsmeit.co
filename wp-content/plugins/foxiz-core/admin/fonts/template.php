<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

$settings     = get_option( 'rb_adobe_font_settings', [] );
$font_options = rbSubPageAdobeFonts::get_instance()->font_selection( $fonts );
?>
<div class="rb-panel-wrap rb-fonts">
	<div class="rb-panel-header">
		<div class="rb-panel-heading">
			<h1>
				<i class="dashicons dashicons-cloud" aria-label="hidden"></i><?php echo esc_html__( 'Adobe Fonts', 'foxiz-core' ); ?>
			</h1>
			<p class="sub-heading"><?php echo esc_html__( 'The theme supports Adobe (TypeKit) Fonts, enabling you to integrate custom fonts into your websites.', 'foxiz-core' ); ?></p>
		</div>
	</div>
	<div class="rb-panel">
		<div class="rb-project-id-header">
			<h2><?php esc_html_e( 'Project ID', 'foxiz-core' ); ?></h2>
			<p class="token-info"><?php printf( __( 'You can find the Project ID <a href=%1$s target="_blank" >here</a> from your Adobe(Typekit) Account.', 'foxiz-core' ), '//fonts.adobe.com/my_fonts?browse_mode=all#web_projects-section' ); ?></p>
		</div>
		<form name="rb-adobe-font" method="post" action="">
			<?php wp_nonce_field( 'rb-fonts', 'rb-fonts-nonce' ); ?>
			<?php if ( ! empty( $project_id ) ) : ?>
				<input class="rb-panel-input-text" type="text" name="rb_fonts_project_id" placeholder="<?php esc_html_e( 'Your project ID', 'foxiz-core' ); ?>" id="rb-project-id" readonly value="<?php echo esc_attr( $project_id ); ?>">
				<a href="#" id="rb-edit-project-id" class="rb-panel-button"><?php esc_html_e( 'Edit Project ID', 'foxiz-core' ); ?></a>
				<button type="submit" name="action" class="rb-panel-button" id="delete-project-id" value="delete"><?php echo esc_attr( $delete ); ?></button>
				<button type="submit" name="action" class="rb-panel-button is-hidden" id="submit-project-id" value="update"><?php echo esc_attr( $button ); ?></button>
			<?php else : ?>
				<input class="rb-panel-input-text" type="text" name="rb_fonts_project_id" id="rb-project-id" placeholder="<?php esc_html_e( 'Your project ID', 'foxiz-core' ); ?>" value="">
				<button type="submit" name="action" class="rb-panel-button" id="submit-project-id" value="update"><?php echo esc_attr( $button ); ?></button>
			<?php endif; ?>
		</form>
	</div>
	<div class="rb-panel font-details-wrap">
		<div class="rb-font-details-header">
			<h2><?php esc_html_e( 'Adobe Font Details', 'foxiz-core' ); ?></h2>
			<p><?php esc_html_e( 'The list below will display all the fonts in your Adobe Fonts account.', 'foxiz-core' ); ?></p>
		</div>
		<div class="font-details">
			<?php if ( empty( $project_id ) ) : ?>
				<p class="rb-font-notice"><?php esc_html_e( 'Fonts not found, Project ID is missing!', 'foxiz-core' ); ?></p>
			<?php elseif ( empty( $fonts ) ) : ?>
				<p class="rb-font-notice"><?php esc_html_e( 'No webfont found in your project.', 'foxiz-core' ); ?></p>
			<?php else : ?>
				<div class="rb-font-item is-top">
					<p class="rb-font-detail"><?php esc_html_e( 'Fonts', 'foxiz-core' ); ?></p>
					<p class="rb-family-detail"><?php esc_html_e( 'Font Family', 'foxiz-core' ); ?></p>
					<p class="rb-weight-detail"><?php esc_html_e( 'Weight & Style', 'foxiz-core' ); ?></p>
				</div>
				<?php foreach ( $fonts as $font ) : ?>
					<div class="rb-font-item">
						<p class="rb-font-detail"><?php echo esc_html( $font['family'] ); ?></p>
						<p class="rb-family-detail"><?php echo esc_html( $font['backup'] ); ?></p>
						<p class="rb-weight-detail"><?php echo esc_html( implode( ',', $font['variations'] ) ); ?></p>
					</div>
				<?php endforeach;
			endif; ?>
		</div>
	</div>
	<div class="rb-panel font-settings-wrap">
		<div class="rb-font-details-header">
			<h2><?php esc_html_e( 'Font Settings', 'foxiz-core' ); ?></h2>
			<p><?php esc_html_e( 'You can find your Adobe fonts and assign them to elements in ', 'foxiz-core' ); ?>
				<a href="<?php echo admin_url( 'admin.php?page=ruby-options&tab=78' ); ?>">Dashboard > Theme Options > Typography</a>
			</p>
		</div>
	</div>
</div>