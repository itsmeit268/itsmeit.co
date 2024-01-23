<?php

function apa_enol_f_generate_html(){

    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
?>
<div class="wrap">
	<h2>Email Notifification on Login</h2>
	<div id="main-container" class="postbox-container metabox-holder" style="width:75%;">
    	<div style="margin:0 8px;">
            <div class="postbox">
                <h3 style="cursor:default;"><span>Email Notification on Login - <?php _e('Settings', 'email-notification-on-login'); ?></span></h3>
                <div class="inside">
                    <p><?php _e( 'This plugin sends an email to the WordPress System email (Settings / General / Email Address) or any other configured email address each time somebody logs into WordPress. This is handy if there are not many logins each day or week to keep track of all of them and being able to detect non authorized logins.', 'email-notification-on-login' ); ?></p>
                    <p><?php _e( 'Here you can configure the email address that will receive the login notifications.', 'email-notification-on-login' ); ?></p>
					<form method="post" action="options.php">
						<?php settings_fields( 'email-notification-on-login-settings-group' ); ?>
						<table class="form-table">
							<tr valign="top">
								<th scope="row"><?php _e( 'Email to send notifications', 'email-notification-on-login' ); ?></th>
								<td>
									<p><label><?php _e('Email', 'email-notification-on-login'); ?>: <input type="text" id="apa_enol_admin_email" size="50" name="apa_enol_admin_email" value="<?php echo get_option( 'apa_enol_admin_email' ); ?>" /></label></p>
									<p class="description"><?php _e('The default email to send notifications is the WordPress System Email Address (Settings / General / Email Address)', 'email-notification-on-login'); ?>. <?php _e('In this installation the WordPress System Email is', 'email-notification-on-login'); ?>: <strong><?php echo ( get_option('admin_email') ); ?></strong></p>
                                    <p class="description"><strong><?php _e('ATTENTION', 'email-notification-on-login'); ?>: </strong><?php _e('If the email address is not valid, the emails will be sent to the SYSTEM EMAIL.', 'email-notification-on-login'); ?> <strong><?php if ( is_email( get_option( 'apa_enol_admin_email' ) ) ) { _e('Email looks ok', 'email-notification-on-login'); } else { echo '<span style="color: #f00;">'; _e('Email doesn\'t look correct. Please check it.', 'email-notification-on-login'); echo '</span>'; }  ?></strong></p>
								</td>
							</tr>
							</table>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'email-notification-on-login' ); ?>" />
						</p>
					</form>
				</div> <!-- .inside -->
            </div> <!-- .postbox -->
		</div> <!-- style margin -->
	</div> <!-- #main-container -->
	<div id="side-container" class="postbox-container metabox-holder" style="width:24%;">
    	<div style="margin:0 8px;">
            <div class="postbox">
                <h3 style="cursor:default;"><span><?php _e('Do you like this Plugin?', 'email-notification-on-login'); ?></span></h3>
                <div class="inside">
                    <p><?php _e('We also need volunteers to translate this and our other plugins into more languages.', 'email-notification-on-login'); ?></p>
                    <p><?php _e('If you wish to help then use our', 'email-notification-on-login'); echo ' <a href="https://apasionados.es/contacto/index.php?desde=wordpress-org-contactform7sdomtracking-administracionplugin" target="_blank">'; _e('contact form', 'email-notification-on-login'); echo '</a> '; _e('or contact us on Twitter:', 'email-notification-on-login'); echo ' <a href="https://twitter.com/apasionados" target="_blank">@Apasionados</a>.'; ?></p>
                    <h4 align="right"><img src="<?php echo (plugin_dir_url(__FILE__) . 'love_bw.png'); ?>" /> <span style="color:#b5b5b5;"><?php _e('Developed with love by:', 'email-notification-on-login'); ?></span> <a href="https://apasionados.es/" target="_blank">Apasionados.es</a></h4>
                </div> <!-- .inside -->
            </div> <!-- .postbox -->
		</div> <!-- style margin -->
	</div> <!-- #side-container -->
</div> <!-- wrap -->


<?php
}
apa_enol_f_generate_html();
?>