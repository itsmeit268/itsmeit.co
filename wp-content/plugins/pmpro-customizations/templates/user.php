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
    ?>
    <style>
        .profile, .designs {
            padding-left: 30px;
            padding-right: 30px;
        }

        .r-content {
            margin-left: 30px;
        }
       
        @media (max-width: 750px) {

            .profile {
                flex-direction: column;
            }

            .view {
                display: none !important;
            }

            .card {
                min-width: auto !important;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .category p {
                margin-right: 1.5rem !important;
            }
        }

        @media (max-width: 535px) {

            .profile {
                padding: 1rem;
                align-items: center;
            }


            .category p:nth-child(2), .category p:nth-child(3) {
                display: none !important;
            }

            .category p {
                margin: 0 !important;
            }

            .edit {
                display: none !important;
            }

            .other-content svg, .footer {
                display: none !important;
            }

            .other-content:first-child {
                display: none !important;
            }

            .buttons {
                flex-direction: column !important;
                width: auto !important;
                padding: 0.5rem !important;
            }

            .p-edit-profile {
                margin-right: 0 !important;
            }

            .follow {
                margin-top: 1rem !important;
            }
        }

        .card {
            margin: 0 auto;
            max-width: 1280px;
        }

        .edit {
            transition: 100ms cubic-bezier(0.19, 1, 0.22, 1);
            position: absolute;
            right: 0;
            padding: 0.5rem;
            border-radius: 50%;
            animation-delay: 400ms;
            animation-duration: 1s;
            animation-name: slideInRight;
            animation-fill-mode: forwards;
        }

        .profile {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
            position: relative;
            margin-top: 50px;
            width: 100%;
        }

        .content {
            width: 45%;
            margin-left: 10px;
        }

        .r-content {
            width: 65%;
        }
        .avatar {
            border-radius: 50%;
            margin-right: 1.5rem;
        }

        .bio {
            font-size: medium;
        }

        .other {
            display: flex;
            white-space: nowrap;
        }

        .other-content {
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgb(70, 70, 70);
        }

        .other-content svg {
            margin-right: 0.3rem;
            animation-name: slideIn;
            animation-fill-mode: forwards;
            animation-delay: 400ms;
            animation-duration: 1s;
            animation-timing-function: cubic-bezier(0.075, 0.82, 0.165, 1);
        }

        .other-content:first-of-type {
            margin-right: 2rem;
        }

        .buttons {
            margin-top: 10px;
            display: flex;
        }

        .btn {
            padding: 0px 10px;
            border-radius: 0.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-weight: 600;
        }

        .p-edit-profile {
            background: #2731e2;
            color: whitesmoke;
            margin-right: 0.8rem;
        }

        .follow {
            color: #2731e2;
            border: solid 3px #2731e2;
            background: whitesmoke;
        }

        .designs {
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .category {
            display: flex;
        }

        .category p {
            margin-right: 0.8rem;
        }

        .category p:first-child {
            text-decoration: underline;
            text-decoration-color: #2731e2;
            -moz-text-decoration-color: #2731e2;
            text-decoration-thickness: 0.2rem;
        }

        .view {
            display: flex;
            position: absolute;
            right: 0;
            top: 0;
        }

        .view p {
            margin-right: 0.8rem;
        }

        .filter {
            margin-right: 0.2rem;
        }


        .edit:hover {
            background: #2730e21f;
        }

        .p-edit-profile:hover {
            background: #2129b9;
        }

        .follow:hover {
            background: #2731e2;
            color: whitesmoke;
        }

        /* ANIMATION */
        @keyframes slideIn {
            from {
                transform: translateX(-500vw);
            }

            to {
                transform: translateX(0px);
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(200vw);
            }

            to {
                transform: translateX(0px);
            }
        }

    </style>
    <div class="card">
        <div class="profile-content">
            <div class="profile">
                <img class="avatar" src="<?= esc_url($avatar)?>" alt="avatar" width="100" height="100">
                <div class="content">
                    <h2><?= $user->display_name ?></h2>
                    <h3 class="bio"><?= !empty($interest)? $interest: 'Not provided yet' ?></h3>
                    <div class="other">
                        <div class="other-content">
                            <i class="fa-solid fa-location-dot" style="color: #565a5a;margin-right: 5px;"></i>
                            <span><a href="https://www.google.com/maps/place/<?=$location?>" rel="nofollow" target="_blank"><?= !empty($location) ? $location: 'Not provided yet' ?></a></span>
                        </div>
                        <div class="other-content">
                            <i class="fa-solid fa-link" style="color: #565a5a;margin-right: 5px;"></i>
                            <span><a href="<?= esc_url($website)?>" rel="nofollow" target="_blank"><?= !empty($website)? esc_url($website): 'Not provided yet'?></a></span>
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
                <div class="r-content">
                    <h3 style="margin-bottom: 5px">Biography</h3>
                    <?= !empty($user_bio)? $user_bio : 'Not provided yet';?>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" class="edit">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </div>
<!--            <div class="designs">-->
<!--                <div class="category">-->
<!--                    <p>Posts</p>-->
<!--                    <p>Albums</p>-->
<!--                    <p>Stats</p>-->
<!--                </div>-->
<!--            </div>-->
        </div>
    </div>
<?php endif; ?>

<?php get_footer();?>