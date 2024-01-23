<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

?>
<div class="rb-panel-wrap">
	<div class="rb-panel-header">
		<div class="rb-panel-heading">
			<h1><i class="dashicons dashicons-awards"></i><?php echo esc_html__( 'Welcome to', 'foxiz-core' ) . ' ' . '<strong>' . wp_get_theme()->Name . ' </strong>'; ?></h1>
			<p class="sub-heading"><?php echo sprintf( esc_html__( 'Thank you for your awesome taste and choosing %s!', 'foxiz-core' ), wp_get_theme()->Name ) ?></p>
			<?php echo rb_admin_get_template_part( 'admin/templates/links' ); ?>
		</div>
	</div>
	<div class="rb-panel rb-panel-activate">
		<div class="rb-inner">
			<?php if ( empty( $is_activated ) ) : ?>
				<div class="rb-activate-header">
					<h2><i class="dashicons-before dashicons-admin-network" aria-hidden="true"></i><?php echo sprintf( esc_html__( 'Activate %s', 'foxiz-core' ), wp_get_theme()->Name ) ?></h2>
					<p><?php echo sprintf( esc_html__( 'Please activate %s to unlock the full features of the theme and get access to premium dedicated support.', 'foxiz-core' ), wp_get_theme()->Name ); ?></p>
				</div>
				<div class="rb-activate-form">
					<form method="post" action="" id="rb-register-theme-form">
						<?php wp_nonce_field( 'rb-core', 'rb-core-nonce' ); ?>
						<div class="rb-panel-input">
							<label class="rb-panel-label" for="purchase_code"><?php esc_html_e( 'Purchase Code', 'foxiz-core' ); ?></label>
							<input type="text" name="purchase_code" class="rb-panel-input-text" placeholder="<?php esc_html_e( 'Input purchase code...', 'foxiz-core' ); ?>" required>
							<span class="rb-error-info is-hidden"><i class="dashicons-info-outline dashicons-before" aria-hidden="true"></i></span>
						</div>
						<div class="rb-panel-input">
							<label class="rb-panel-label" for="email"><?php esc_html_e( 'Support Email', 'foxiz-core' ); ?></label>
							<input type="email" name="email" class="rb-panel-input-text" placeholder="<?php esc_html_e( 'Input your email...', 'foxiz-core' ); ?>" required>
							<span class="rb-error-info is-hidden"><i class="dashicons-info-outline dashicons-before" aria-hidden="true"></i></span>
						</div>
						<div class="rb-panel-submit">
							<input id="rb-register-theme-btn" type="submit" class="rb-panel-button" name="register-theme" value="<?php esc_attr_e( 'Activate Theme', 'foxiz-core' ); ?>">
							<span class="rb-loading is-hidden"><i class="dashicons dashicons-update" aria-hidden="true"></i></span>
							<span class="rb-purchase-code-info"><a href="#"><?php esc_html_e( 'How to find your purchase code?', 'foxiz-core' ) ?></a></span>
						</div>
						<div class="rb-response-info is-hidden"></div>
					</form>
				</div>
			<?php else : ?>
				<div class="rb-activate-header is-activated">
					<h2>
						<i class="dashicons-before dashicons-admin-network" aria-hidden="true"></i><?php echo sprintf( esc_html__( '%s is Activated', 'foxiz-core' ), wp_get_theme()->Name ) ?>
					</h2>
					<p><?php echo sprintf( esc_html__( 'Thank you for choosing %s. Should you have any other concerns while using this theme, please feel free to contact us at any time via the above link.', 'foxiz-core' ), wp_get_theme()->Name ); ?></p>
				</div>
				<div class="rb-activate-form is-activated">
					<form method="post" action="" id="rb-deregister-theme-form">
						<?php wp_nonce_field( 'rb-core', 'rb-core-nonce' ); ?>
						<div class="rb-panel-input">
							<label class="rb-panel-label" for="purchase_code"><?php esc_html_e( 'Purchase Code', 'foxiz-core' ); ?></label>
							<input type="text" value="<?php echo rb_admin_hide_code( $purchase_code ); ?>" name="activated_purchase_code" class="rb-panel-input-text" readonly>
						</div>
						<div class="rb-panel-submit">
							<input type="submit" class="rb-panel-button deregister-button" value="<?php esc_attr_e( 'Deactivate Theme', 'foxiz-core' ); ?>" name="deregister-theme" id="rb-deregister-theme-btn"/>
							<span class="rb-loading is-hidden"><i class="dashicons dashicons-update" aria-hidden="true"></i></span>
						</div>
						<div class="rb-response-info is-hidden"></div>
					</form>
					<p class="reactivation-info"><i class="dashicons dashicons-info-outline"></i><?php esc_html_e('For theme reactivation, kindly deactivate the license before utilizing the purchase code once again!', 'foxiz-core'); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="rb-panel rb-panel-system-info">
		<div class="rb-inner">
			<div class="rb-info-header"><h2><?php esc_html_e( 'PHP Information', 'foxiz-core' ); ?></h2></div>
			<div class="rb-info-content">
				<?php if ( ! empty( $system_info ) && is_array( $system_info ) ) :
					foreach ( $system_info as $info => $val ) :
						$class_name = 'info-el';
						if ( isset( $val['passed'] ) && ! $val['passed'] ) {
							$class_name .= ' is-warning';
						} ?>
						<div class="<?php echo esc_attr( $class_name ); ?>">
							<div class="info-content">
								<span class="info-label"><?php echo esc_attr( $val['title'] ) ?></span>
								<span class="info-value"><?php echo esc_attr( $val['value'] ) ?></span>
							</div>
							<?php if ( isset( $val['passed'] ) && ! $val['passed'] ) : ?>
								<span class="info-warning"><?php echo esc_attr( $val['warning'] ) ?></span>
							<?php endif; ?>
						</div>
					<?php endforeach;
				endif; ?>
			</div>
		</div>
	</div>
</div>