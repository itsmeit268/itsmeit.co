<?php
/** Don't load directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ruby_GTM_Integration' ) ) {
	require_once FOXIZ_CORE_PATH . 'lib/simple-gtm-ga4/simple-gtm-ga4.php';
}

if ( ! class_exists( 'RB_OPENAI_ASSISTANT' ) ) {
	require_once FOXIZ_CORE_PATH . 'lib/ruby-openai/ruby-openai.php';
}

if ( ! class_exists( 'Foxiz_Post_Elements' ) ) {
	require_once FOXIZ_CORE_PATH . 'lib/foxiz-elements/foxiz-elements.php';
}

require_once FOXIZ_CORE_PATH . 'admin/define.php';
require_once FOXIZ_CORE_PATH . 'lib/bbp/ruby-bbp-supported.php';
require_once FOXIZ_CORE_PATH . 'admin/core-helpers.php';
require_once FOXIZ_CORE_PATH . 'admin/taxonomy.php';
require_once FOXIZ_CORE_PATH . 'admin/sub-pages.php';
require_once FOXIZ_CORE_PATH . 'admin/import/ajax.php';
require_once FOXIZ_CORE_PATH . 'admin/core.php';
require_once FOXIZ_CORE_PATH . 'admin/rb-meta/rb-meta.php';
require_once FOXIZ_CORE_PATH . 'admin/fonts/init.php';
require_once FOXIZ_CORE_PATH . 'admin/info.php';
require_once FOXIZ_CORE_PATH . 'admin/upgrade.php';
require_once FOXIZ_CORE_PATH . 'podcast/init.php';

require_once FOXIZ_CORE_PATH . 'includes/helpers.php';
require_once FOXIZ_CORE_PATH . 'elementor/base.php';
require_once FOXIZ_CORE_PATH . 'includes/actions.php';
require_once FOXIZ_CORE_PATH . 'includes/optimized.php';
require_once FOXIZ_CORE_PATH . 'includes/login-screen.php';
require_once FOXIZ_CORE_PATH . 'includes/svg.php';
require_once FOXIZ_CORE_PATH . 'includes/table-contents.php';
require_once FOXIZ_CORE_PATH . 'includes/video-thumb.php';
require_once FOXIZ_CORE_PATH . 'includes/extras.php';
require_once FOXIZ_CORE_PATH . 'includes/shortcodes.php';
require_once FOXIZ_CORE_PATH . 'includes/amp.php';
require_once FOXIZ_CORE_PATH . 'includes/personalize-helper.php';

require_once FOXIZ_CORE_PATH . 'membership/membership.php';
require_once FOXIZ_CORE_PATH . 'membership/options.php';
require_once FOXIZ_CORE_PATH . 'e-template/init.php';
require_once FOXIZ_CORE_PATH . 'includes/shares.php';
require_once FOXIZ_CORE_PATH . 'includes/ads.php';
require_once FOXIZ_CORE_PATH . 'reaction/reaction.php';

require_once FOXIZ_CORE_PATH . 'includes/widgets.php';
require_once FOXIZ_CORE_PATH . 'includes/socials.php';
require_once FOXIZ_CORE_PATH . 'widgets/fw-instagram.php';
require_once FOXIZ_CORE_PATH . 'widgets/fw-mc.php';
require_once FOXIZ_CORE_PATH . 'widgets/banner.php';
require_once FOXIZ_CORE_PATH . 'widgets/sb-post.php';
require_once FOXIZ_CORE_PATH . 'widgets/sb-follower.php';
require_once FOXIZ_CORE_PATH . 'widgets/sb-weather.php';
require_once FOXIZ_CORE_PATH . 'widgets/sb-social-icon.php';
require_once FOXIZ_CORE_PATH . 'widgets/sb-youtube.php';
require_once FOXIZ_CORE_PATH . 'widgets/sb-flickr.php';
require_once FOXIZ_CORE_PATH . 'widgets/sb-address.php';
require_once FOXIZ_CORE_PATH . 'widgets/sb-instagram.php';
require_once FOXIZ_CORE_PATH . 'widgets/sb-ad-image.php';
require_once FOXIZ_CORE_PATH . 'widgets/sb-ad-script.php';
require_once FOXIZ_CORE_PATH . 'widgets/sb-facebook.php';
require_once FOXIZ_CORE_PATH . 'widgets/ruby-template.php';
