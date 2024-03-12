<?php
/*
	Shortcode to show membership account information
*/
function pmpro_shortcode_account($atts, $content=null, $code="")
{
	global $wpdb, $pmpro_msg, $pmpro_msgt, $pmpro_levels, $current_user, $levels;

	// $atts    ::= array of attributes
	// $content ::= text within enclosing form of shortcode element
	// $code    ::= the shortcode found, when == callback name
	// examples: [pmpro_account] [pmpro_account sections="membership,profile"/]

	extract(shortcode_atts(array(
		'section' => '',
		'sections' => 'membership,profile,invoices,links',
		'title' => null,
	), $atts));

	//did they use 'section' instead of 'sections'?
	if(!empty($section))
		$sections = $section;

	//Extract the user-defined sections for the shortcode
	$sections = array_map('trim',explode(",",$sections));
	ob_start();

	// If multiple sections are being shown, set title to null.
	// Titles can only be changed from the default if only one section is being shown.
	if ( count( $sections ) > 1 ) {
		$title = null;
	}

	//if a member is logged in, show them some info here (1. past invoices. 2. billing information with button to update.)
	$order = new MemberOrder();
	$order->getLastMemberOrder();
	$mylevels = pmpro_getMembershipLevelsForUser();
	$pmpro_levels = pmpro_getAllLevels(false, true); // just to be sure - include only the ones that allow signups
	$invoices = $wpdb->get_results("SELECT *, UNIX_TIMESTAMP(CONVERT_TZ(timestamp, '+00:00', @@global.time_zone)) as timestamp FROM $wpdb->pmpro_membership_orders WHERE user_id = '$current_user->ID' AND status NOT IN('review', 'token', 'error') ORDER BY timestamp DESC LIMIT 6");
	?>
	<div id="pmpro_account">

		<?php if(in_array('profile', $sections)) { ?>
			<div id="pmpro_account-profile" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_box', 'pmpro_account-profile' ) ); ?>">
				<?php
				if ( '' !== $title ) { // Check if title is being forced to not show.
					// If a custom title was not set, use the default. Otherwise, show the custom title.
					?>
					<h2><?php echo esc_html( null === $title ? __( 'My Account', 'paid-memberships-pro' ) : $title ); ?></h2>
					<?php
				}
				wp_get_current_user();
				?>
				<div>
                    <?php
                    $user_name = $current_user->user_login;
                    $user = get_user_by('login', $user_name);

                    if (!empty($user_name)) : ?>
                        <?php
                        $avatar_id = get_user_meta($user->ID, 'wp_user_avatar', true);
                        $user_bio = get_user_meta($user->ID, 'user_bio', true);
                        $avatar = !empty($avatar_id) ? wp_get_attachment_image_src($avatar_id, 'thumbnail')[0] : get_avatar_url($user->ID);
                        $interest = get_user_meta($user->ID, 'interest', true);
                        $location = get_user_meta($user->ID, 'location', true);
                        $website = get_user_meta($user->ID, 'website', true);
                        ?>
                        <div class="card">
                            <div class="profile-content">
                                <div class="profile">
                                    <img class="avatar" src="<?= esc_url($avatar)?>" alt="avatar" width="100" height="100">
                                    <div class="content">
                                        <h2><?= $user->display_name ?></h2>
                                        <h3 class="bio"><?= !empty($interest)? $interest: '<a href="https://itsmeit.co/my-account/profile.html" rel="nofollow">Not provided yet</a>' ?></h3>
                                        <div class="other">
                                            <div class="other-content">
                                                <i class="fa-solid fa-location-dot" style="color: #565a5a;margin-right: 5px;"></i>
                                                <?php $r_location = !empty($location) ? 'https://www.google.com/maps/place/'.$location : 'https://itsmeit.co/my-account/profile.html'; ?>
                                                <span><a href="<?= $r_location?>" rel="nofollow" target="_blank"><?= !empty($location) ? $location: 'Not provided yet' ?></a></span>
                                            </div>
                                            <div class="other-content">
                                                <i class="fa-solid fa-link" style="color: #565a5a;margin-right: 5px;"></i>
                                                <?php $r_website = !empty($website) ? $website : 'https://itsmeit.co/my-account/profile.html'; ?>
                                                <span><a href="<?= esc_url($r_website)?>" rel="nofollow" target="_blank"><?= !empty($website)? esc_url($website): 'Not provided yet'?></a></span>
                                            </div>
                                        </div>
                                        <div class="other">
                                            <div class="other-content">
                                                <i class="fa-solid fa-user-group" style="color: #565a5a;margin-right: 5px;"></i>
                                                <span>Level: <a href="https://itsmeit.co/my-account/levels.html" rel="nofollow"><strong><?= get_level_name()?></strong></a></span>
                                            </div>
                                            <div class="other-content">
                                                <i class="fa-solid fa-star" style="color: #565a5a;margin-right: 5px;"></i>
                                                <span>Point: 110000</span>
                                            </div>
                                        </div>
                                        <div class="buttons">
                                            <div class="btn p-edit-profile"><a href="https://itsmeit.co/my-account/profile.html" rel="nofollow">Edit Profile</a></div>
                                            <div class="btn follow"><a href="https://itsmeit.co/my-account/levels.html">My Memberships</a></div>
                                        </div>
                                    </div>
                                    <div class="public-info" style="position: absolute; bottom: 0; border: 1px solid #ddd5d5; border-radius: 5px; padding: 0 10px; background: #f5efef;">
                                        <a href="<?= get_bloginfo('url')?>/user/<?=$current_user->user_login?>" class="pubic-user" rel="nofollow"><i class="fa-solid fa-share" style="color: #565a5a;margin-right: 5px;"></i><?= get_bloginfo('url')?>/user/<?=$current_user->user_login?></a>
                                    </div>
                                    <div class="r-content">
                                        <h3 style="margin-bottom: 5px">Biography</h3>
                                        <?= !empty($user_bio)? $user_bio : '<a href="https://itsmeit.co/my-account/profile.html" rel="nofollow">Not provided yet</a>';?>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" class="edit">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
			</div> <!-- end pmpro_account-profile -->
		<?php } ?>

		<?php if(in_array('invoices', $sections) && !empty($invoices)) { ?>
		<div id="pmpro_account-invoices" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_box', 'pmpro_account-invoices' ) ); ?>">
			<?php
			if ( '' !== $title ) { // Check if title is being forced to not show.
				// If a custom title was not set, use the default. Otherwise, show the custom title.
				?>
				<h2><?php echo esc_html( null === $title ? __( 'Past Invoices', 'paid-memberships-pro' ) : $title ); ?></h2>
				<?php
			}
			?>
			<table class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_table' ) ); ?>" width="100%" cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<th><?php esc_html_e("Date", 'paid-memberships-pro' ); ?></th>
						<th><?php esc_html_e("Level", 'paid-memberships-pro' ); ?></th>
						<th><?php esc_html_e("Amount", 'paid-memberships-pro' ); ?></th>
						<th><?php esc_html_e("Status", 'paid-memberships-pro'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
					$count = 0;
					foreach($invoices as $invoice)
					{
						if($count++ > 4)
							break;

						//get an member order object
						$invoice_id = $invoice->id;
						$invoice = new MemberOrder;
						$invoice->getMemberOrderByID($invoice_id);
						$invoice->getMembershipLevel();

						if ( in_array( $invoice->status, array( '', 'success', 'cancelled' ) ) ) {
						    $display_status = esc_html__( 'Paid', 'paid-memberships-pro' );
						} elseif ( $invoice->status == 'pending' ) {
						    // Some Add Ons set status to pending.
						    $display_status = esc_html__( 'Pending', 'paid-memberships-pro' );
						} elseif ( $invoice->status == 'refunded' ) {
						    $display_status = esc_html__( 'Refunded', 'paid-memberships-pro' );
						}
						?>
						<tr id="pmpro_account-invoice-<?php echo esc_attr( $invoice->code ); ?>">
							<td><a href="<?php echo esc_url( pmpro_url( "invoice", "?invoice=" . $invoice->code ) ) ?>"><?php echo esc_html( date_i18n(get_option("date_format"), $invoice->getTimestamp()) )?></a></td>
							<td><?php if(!empty($invoice->membership_level)) echo esc_html( $invoice->membership_level->name ); else echo esc_html__("N/A", 'paid-memberships-pro' );?></td>
							<td><?php
								//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo pmpro_escape_price( pmpro_formatPrice($invoice->total) ); ?></td>
							<td><?php echo esc_html( $display_status ); ?></td>
						</tr>
						<?php
					}
				?>
				</tbody>
			</table>
			<?php if($count == 6) { ?>
				<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_actionlinks' ) ); ?>"><a id="pmpro_actionlink-invoices" href="<?php echo esc_url( pmpro_url( "invoice" ) ); ?>"><?php esc_html_e("View All Invoices", 'paid-memberships-pro' );?></a></div>
			<?php } ?>
		</div> <!-- end pmpro_account-invoices -->
		<?php } ?>

		<?php if(in_array('links', $sections) && (has_filter('pmpro_member_links_top') || has_filter('pmpro_member_links_bottom'))) { ?>
		<div id="pmpro_account-links" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_box', 'pmpro_account-links' ) ); ?>">
			<?php
			if ( '' !== $title ) { // Check if title is being forced to not show.
				// If a custom title was not set, use the default. Otherwise, show the custom title.
				?>
				<h2><?php echo esc_html( null === $title ? __( 'Member Links', 'paid-memberships-pro' ) : $title ); ?></h2>
				<?php
			}
			?>
			<ul>
				<?php
					do_action("pmpro_member_links_top");
				?>

				<?php
					do_action("pmpro_member_links_bottom");
				?>
			</ul>
		</div> <!-- end pmpro_account-links -->
		<?php } ?>
	</div> <!-- end pmpro_account -->
	<?php

	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}
add_shortcode('pmpro_account', 'pmpro_shortcode_account');
