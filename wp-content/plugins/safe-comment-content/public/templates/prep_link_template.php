<?php $link = isset($_COOKIE['pr_hr']) ? $_COOKIE['pr_hr'] : ''; ?>
<?php if ( file_exists( get_template_directory() . '/header.php' ) ) {
	get_header();
} ?>
    <div class="prep-link-comment" style="margin: 0 auto; max-width: 890px; margin-top: 30px;text-align: center;">
        <?php if ($link): ?>
        <p>Liên kết bạn đang chuyển hướng tới nằm ngoài phạm vi kiểm soát của <?= get_bloginfo( 'name' ) ?>.</p>
        <div style="margin-top:20px">
            <button id="go-to-link" onclick="window.location.href='<?php esc_html_e($link); ?>';"><?php _e( '✓ Chuyển hướng' ); ?></button>
            <button id="link-close" onclick="self.close()"><?php _e( '✘ Hủy bỏ' ); ?></button>
        </div>
        <?php else: ?>
            <p>Phiên truy cập đã hết hạn, vui lòng bấm vào <a href="<?= get_permalink(get_the_ID()) ?>">đây</a> thực hiện lại.</p>
        <?php endif;?>
    </div>
<?php if ( file_exists( get_template_directory() . '/footer.php' ) ) {
	get_footer();
} ?>