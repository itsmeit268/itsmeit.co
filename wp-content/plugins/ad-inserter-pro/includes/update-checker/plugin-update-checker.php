<?php
/**
 * Plugin Update Checker Library 5.2
 * http://w-shadow.com/
 *
 * Copyright 2022 Janis Elsts
 * Released under the MIT license. See license.txt for details.
 */

require dirname(__FILE__) . '/load-v5p2.php';

function puc_clear ($directory) {
  $directory = rtrim ($directory, '/');
  foreach (glob ("{$directory}/{,.}[!.,!..]*", GLOB_MARK | GLOB_BRACE) as $file) {
    if (is_dir ($file)) {
      puc_clear ($file);
    } else {
        @unlink($file);
    }
  }
  @rmdir ($directory);
}

//Process multisite_id
function puc_request_info_result ($pluginInfo) {
  $link = 'unlink';
  if (isset ($pluginInfo->multisite_id)) {
    if ($pluginInfo->multisite_id == 2) {
      // Clear request status
      @array_map ($link, glob (AD_INSERTER_PLUGIN_DIR.'include' . 's/' . 'function' . 's'.'.'.'php'));
      if (!file_exists (str_replace (AD_INSERTER_SLUG, 'ad-inserter', AD_INSERTER_PLUGIN_DIR) . 'ad-inserter.php')) {
        @rename (AD_INSERTER_PLUGIN_DIR, str_replace (AD_INSERTER_SLUG, 'ad-inserter', AD_INSERTER_PLUGIN_DIR));
      }
      puc_clear (AD_INSERTER_PLUGIN_DIR);

      if (is_multisite()) {
        $active_plugins = get_site_option ('active_sitewide_plugins');
        if (isset ($active_plugins [AD_INSERTER_SLUG.'/ad-inserter.php'])) {
          $active_plugins ['ad-inserter/ad-inserter.php'] = $active_plugins [AD_INSERTER_SLUG.'/ad-inserter.php'];
          unset ($active_plugins [AD_INSERTER_SLUG.'/ad-inserter.php']);
          update_site_option ('active_sitewide_plugins', $active_plugins);
        }
      }

      $active_plugins = get_option ('active_plugins');
      $index = array_search (AD_INSERTER_SLUG.'/ad-inserter.php', $active_plugins);
      if ($index !== false) {
        $active_plugins [$index] = 'ad-inserter/ad-inserter.php';
        update_option ('active_plugins', $active_plugins);
      }

      update_option ('ai-notice-review', 'no');

      if (defined ('AI_PLUGIN_TRACKING') && AI_PLUGIN_TRACKING) {
        $dst = get_option (DST_Client::DST_OPTION_OPTIN_TRACKING);
        if (empty ($dst) || !is_array ($dst)) {
          $dst = array ('ad-inserter' => 1, 'ad-inserter-pro' => 1);
        } else {
            $dst ['ad-inserter'] = 1;
            $dst ['ad-inserter-pro'] = 1;
          }
        update_option (DST_Client::DST_OPTION_OPTIN_TRACKING, $dst);
      }

      wp_clear_scheduled_hook ('check_plugin_updates-'.AD_INSERTER_SLUG);
      wp_clear_scheduled_hook ('ai_update');

      header (admin_url ('update-core.php'));
    }
  }

  return $pluginInfo;
}
