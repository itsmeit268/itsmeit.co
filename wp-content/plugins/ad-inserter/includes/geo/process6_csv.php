<?php

//ini_set ('display_errors', 1);
//error_reporting (E_ALL);

define ("HASH_SIZE",  5);
define ("LISTS",      pow (2, HASH_SIZE));
define ("HASH_MASK",  LISTS - 1);
define ("LIST_ITEM_SIZE",  16 + 16 + 2);

function process6_csv ($file = '') {

  if ($file == '') $csv_file = 'data/IpToCountry.6R.csv'; else $csv_file = $file;

  echo "START...\n";
  echo "LISTS: ", LISTS, "\n\n";

  $lists = array ();
  for ($index = 0; $index < LISTS; $index++) {
    $lists [$index] = '';
  }

  // Open the csv file
  $fp = fopen ($csv_file, 'r');

  if ($fp === false) {
   echo 'Error opening file ', $csv_file, "\n";
   return;
  }

  $ranges = 0;
  $invalid_ranges = 0;
  while (($row = fgetcsv ($fp, 255)) !== false)
    if ($row && count ($row) > 2 && substr (trim ($row [0]), 0, 1) != '#') {
      list ($iprange, $iso) = $row;

      if (strlen ($iso) != 2 || $iso == 'ZZ') {
  //      echo 'Invalid ISO code: ', $iso, "(", implode (",", $row), ")\n";
        $invalid_ranges ++;
      } else {
        $ip_addresses = explode ("-", $iprange);

        if (!filter_var ($ip_addresses [0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
          echo 'Invalid range start address: ', $ip_addresses [0], "\n";
        }

        if (!filter_var ($ip_addresses [1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
          echo 'Invalid range end address: ', $ip_addresses [1], "\n";
        }

        if (preg_match ("/[13579bdf]:ffff:ffff:ffff:ffff:ffff:ffff:ffff/", $ip_addresses [1], $match)) {
          echo 'Range more than /16: ', $ip_addresses [0], " - ", $ip_addresses [1], "\n";
        }

  //      // Translate IP to 16-byte binary strings
        $range_start = inet_pton ($ip_addresses [0]);
        $range_end   = inet_pton ($ip_addresses [1]);

        $iprange_hash = substr ($range_start, 0, 2);
        $len = strlen ($iprange_hash);
        $hash = 0;

        for ($index = 0; $index < $len; $index ++) {
          $hash ^= ord ($iprange_hash [$index]);
        }
        $hash &= HASH_MASK;

        $list_item = $range_start . $range_end . $iso;

        $lists [$hash] .= $list_item;

        $ranges ++;

  //      echo $hash, " ", inet_ntop (pack ("A16", $range_start)), " - ", inet_ntop (pack ("A16", $range_end)), "\n";
      }
    }

  $header = pack ("L", LISTS);
  $data = '';
  $pointer = 0;
  for ($index = 0; $index < LISTS; $index++) {
    $header .= pack ("L", $pointer);
    $data .= $lists [$index];
    $pointer += strlen ($lists [$index]);

    echo "List $index: ", strlen ($lists [$index]) / LIST_ITEM_SIZE, " ranges\n";
  }
  for ($index = 0; $index < LISTS; $index++) {
    $header .= pack ("L", strlen ($lists [$index]) / LIST_ITEM_SIZE);
  }
  echo "\nTOTAL $ranges ranges\n";
  echo "TOTAL $invalid_ranges invalid ranges\n";

  if ($file == '') $dat_file = 'ip2country6.dat'; else $dat_file = dirname ($file).'/ip2country6.dat';
  if ($ranges < 1000) {
    echo "\nInvalid input file, file ip2country6.dat not generated.\n";
    @unlink ($dat_file);
  } else file_put_contents ($dat_file, $header . $data);
}

if (strpos ($_SERVER ['REQUEST_URI'], 'includes/geo/process6_csv.php') !== false) {
  process6_csv (dirname (__FILE__).'/data/IpToCountry.6R.csv');
}
