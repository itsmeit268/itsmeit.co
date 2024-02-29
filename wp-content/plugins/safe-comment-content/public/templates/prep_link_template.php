<?php $link = isset($_COOKIE['pr_hr']) ? $_COOKIE['pr_hr'] : ''; ?>
<?php if ( file_exists( get_template_directory() . '/header.php' ) ) get_header(); ?>
    <div class="prep-link-comment" style="margin: 0 auto; max-width: 890px; margin-top: 30px;text-align: center;">
        <?php if ($link): ?>
        <p><?= __('The link you are navigating to is beyond the control scope of', 'safe-comment-content')?>  <?= get_bloginfo( 'url' ) ?>.</p>
        <div style="margin-top:20px">
            <button id="go-to-link" onclick="window.location.href='<?php esc_html_e($link); ?>';"><?= __('✓ Redirect', 'safe-comment-content' ); ?></button>
            <button id="link-close" onclick="self.close()"><?= __('✘ Cancel', 'safe-comment-content'); ?></button>
        </div>
        <?php else: ?>
            <p><?= __('The session has expired, please click', 'safe-comment-content')?>
                <a href="<?= get_permalink(get_the_ID()) ?>"><?= __('here', 'safe-comment-content')?></a> <?= __('to refresh.', 'safe-comment-content')?></p>
        <?php endif;?>
    </div>
<?php if ( file_exists( get_template_directory() . '/footer.php' ) ) get_footer(); ?>