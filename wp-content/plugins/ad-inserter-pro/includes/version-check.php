<?php

//ini_set ('display_errors', 1);
//error_reporting (E_ALL);

global $wp_version;

$check   = get_transient (AI_TRANSIENT_VERSION_CHECK);
$version = get_transient (AI_TRANSIENT_PHP_CHECK);

if ($version == AD_INSERTER_VERSION && is_numeric ($check) && time () - $check > 40 * 24 * 3600) {

//  if (version_compare (phpversion (), "7.4", "<")) {
//    echo '<div class="notice notice-warning" style="margin: 5px 15px 2px 0px;">';
//    echo '<p>' . __('The latest PHP version: ', 'ad-inserter') . WP_LATEST_PHP_VERSION . '</p>';
//    echo '</div>';
//  }

  $wp_check = get_transient (AI_TRANSIENT_WP_CHECK);

  if ($wp_check !== false) {
    $wp_version_check = explode ('.', $wp_version);
    $wp_version_last  = explode ('.', $wp_check);

    if (isset ($wp_version_check [0]) && is_numeric ($wp_version_check [0]) &&
        isset ($wp_version_check [1]) && is_numeric ($wp_version_check [1]) &&
        isset ($wp_version_last  [0]) && is_numeric ($wp_version_last  [0]) &&
        isset ($wp_version_last  [1]) && is_numeric ($wp_version_last  [1])) {
      $wp_check = 100 * $wp_version_check [0] + $wp_version_check [1];
      $wp_last  = 100 * $wp_version_last  [0] + $wp_version_last  [1];

      echo $wp_check, ' ', $wp_last;

      if ($wp_check - $wp_last >= 2) {
        echo '<div class="notice notice-warning" style="margin: 5px 15px 2px 0px;">';
        echo '<p>' . __('Warning: Ad Inserter Pro plugin is outdated - it has not been tested with WordPress version', 'ad-inserter') . ' ' . $wp_version . '</p>';
        echo '</div>';
      }
    }
  }
}

if ($check === false) {
  set_transient (AI_TRANSIENT_VERSION_CHECK, time ());
  set_transient (AI_TRANSIENT_PHP_CHECK, AD_INSERTER_VERSION);
  set_transient (AI_TRANSIENT_WP_CHECK, $wp_version);
}
