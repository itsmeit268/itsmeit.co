<?php get_header(); ?>
<?php
$user_name = get_username_from_url();
$user = get_user_by('login', $user_name);

if (!empty($user_name)) : ?>
    <?php
        $my_user = get_userdata($user->ID);
        $avatar_id = get_user_meta($user->ID, 'wp_user_avatar', true);
        $user_bio = get_user_meta($user->ID, 'user_bio', true);
        $avatar = !empty($avatar_id) ? wp_get_attachment_image_src($avatar_id, 'thumbnail')[0] : get_avatar_url($user->ID);
        $interest = get_user_meta($user->ID, 'interest', true);
        $location = get_user_meta($user->ID, 'location', true);
        $website = get_user_meta($user->ID, 'website', true);
        $point = get_user_meta($user->ID, 'wp_user_point', true);
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
                            <span>Level: <a href="https://itsmeit.co/my-account/levels.html" rel="nofollow"><strong style="color: #ee0004"><?= get_level_name()?></strong></a></span>
                        </div>
                        <div class="other-content">
                            <i class="fa-solid fa-star" style="color: #565a5a;margin-right: 5px;"></i>
                            <span>Point: <?= number_format($point?:0, 0, ',', '.');?></span>
                        </div>
                    </div>
                    <?php
                    $curent_user = get_userdata(get_current_user_id());
                    if (is_user_logged_in() && $user_name === $curent_user->user_login):?>
                    <div class="buttons">
                        <div class="btn p-edit-profile"><a href="https://itsmeit.co/my-account/profile.html" rel="nofollow"><?php esc_html_e( 'Edit Profile', 'pmpro-approvals' ); ?></a></div>
                        <div class="btn follow"><a href="https://itsmeit.co/my-account/levels.html"><?= __( 'My Memberships', 'paid-memberships-pro' )?></a></div>
                    </div>
                    <?php endif;?>
                </div>
                <div class="r-content">
                    <h3 style="margin-bottom: 5px">Biography</h3>
                    <p class="biography"><?= !empty($user_bio)? $user_bio : '<a href="https://itsmeit.co/my-account/profile.html" rel="nofollow">Not provided yet</a>';?></p>
                </div>
                <script>
                    var content = document.getElementsByClassName('biography')[0].innerHTML;
                    content = content.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>');
                    document.getElementsByClassName('biography')[0].innerHTML = content;
                </script>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" class="edit">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php get_footer();?>