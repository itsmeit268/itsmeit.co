<?php

//ini_set ('display_errors', 1);
//error_reporting (E_ALL);

/**
 * This class contains the capability to look up IP addresses and return the correct
 * country, based on the lookup data generated from process_csv.php.
 *
 * Since PHP cannot handle 32-bit unsigned longs, we translate the IP addresses
 * into 4-byte strings instead, which also should safeguard against future IPv6
 * lookups.
 *
 * The index file contains 12-byte records, grouped into three 32-bit dwords.
 * The first is an IP block address, the second contains the position in the data
 * file, and the third is the block length for that IP group.
 *
 * The data file contains a long list of 6-byte records, the first four form the
 * start of the IP address block and the remaining two bytes the two-letter ISO
 * country code to which that IP block is allocated.
 *
 * The lookups are done by calculating the "group number" of the IP address,
 * which is the high 13 bits, and looking that up in a hash table. This yields
 * a position into the data file, which can be either preloaded or loaded on
 * demand. The data is then searched using a binary tree search, yielding
 * effectively O(log2 n) lookups.
 *
 * Memory is used very conservatively - since we only use strings to represent
 * data, a fully preloaded database should add about 1.2 MB of internal memory.
 *
 * On a 2.3 GHz Intel Core i5 we get about 40,000 lookups per second.
 */

use GeoIp2\Database\Reader;

if (!class_exists ('Ip2Country')) {

class Ip2Country
{
    private $index = null;     // Holds the preloaded index
    private $data  = array();  // Holds the data records, one chunk per index group
    private $fp;
    private $offset;
    private $datalen;
    private $lastentry;

    /**
     * Initializes the ip to country lookup tables and preloads the index.
     */
    public function __construct($datafile = null)
    {
        if (!$datafile)
            $datafile = 'data/ip2country.dat';

        $this->fp = fopen($datafile, 'r');

        $header = fread($this->fp, 8);
        list(, $indexlen, $this->datalen) = unpack('L2', $header);
        $this->offset = 8 + $indexlen;

        $this->index = array_fill_keys(range(0, 8191), null);

        $index = str_split(fread($this->fp, $indexlen), 12);
        foreach($index as $ix)
            $this->index[self::ipgroup($ix)] .= $ix;

        $lastv = null;
        foreach($this->index as $k => &$v)
            if ($v)
                $lastv = $v;
            else
                $v = $lastv;
    }

    /**
     * Destroys the ip 2 country instance.
     */
    public function __destruct()
    {
        if ($this->fp)
            fclose($this->fp);
    }

    /**
     * Get the group of an IP address by returning the 13 high bits
     */
    public static function ipgroup($ipbin)
    {
        return (ord($ipbin[0]) << 8 | ord($ipbin[1])) >> 3;
    }

    /**
     * Preloads the entire data file into memory for faster lookups of many IP
     * addresses
     */
    public function preload()
    {
        if ($this->fp == null)
            return;

        fseek($this->fp, $this->offset);
        $data = fread($this->fp, $this->datalen);

        foreach($this->index as $index)
            foreach(str_split($index, 12) as $ix)
            {
                list(, , $pos, $len) = unpack('L3', $ix);
                if (!isset($this->data[$pos]))
                    $this->data[$pos] = substr($data, $pos, $len + 6); // Always one extra record, for upper bound
            }

        fclose($this->fp);
        $this->fp = null;
    }

    /**
     * Binary search function that searches a string with records for a match.
     * Requires that the array is sorted. Since we're operating on 32-bit
     * IPs, we match the first four bytes of every string and the
     * rest is just payload. Returns the entry "n" for which the relation
     *        32bit($data[n]) <= $str < 32bit($data[n+1])
     * is true.
     *
     * Always tries to return 4 extra bytes from the next record, for an upper
     * bound. So if $reclen is 12, it tries to return 16 bytes.
     */
    private function findRecord($data, $str, $reclen)
    {
        $l = 0;
        $count = strlen($data) / $reclen;
        $h = $count - 1;
        while ($l <= $h)
        {
            $i = ($l + $h) >> 1;

            // Get the current record (i) and compare
            $rec = substr($data, $i * $reclen, $reclen + 4);
            $c = strcmp($str, substr($rec, 0, 4));

            // Equal? I guess we found it.
            if ($c == 0)
                return $rec;
            // If it's more, then compare with the next record to see if that one is less
            else if ($c > 0)
            {
                // If this was the last record, then return it
                if ($i+1 >= $count)
                    return $rec;

                $rec1 = substr($data, ($i+1)*$reclen, $reclen + 4);
                $c1 = strcmp($str, substr($rec1, 0, 4));

                // Did we stumble upon the right one?
                if ($c1 == 0)
                    // Oops. Equal with the next one. Just return.
                    return $rec1;
                else if ($c1 < 0)
                    // Yep, this is the right one.
                    return $rec;

                $l = $i + 1;
            }
            else
                $h = $i - 1;
        }

        // Never supposed to end up here - something is wrong.
        throw new Exception('Binary find failure');
    }

    /**
     * Find the index given by the IP address.
     */
    private function findIndex($ipbin)
    {
        $group = self::ipgroup($ipbin);

        $ix = $this->index[$group];
        if (strcmp($ipbin, substr($ix, 0, 4)) < 0 && $group > 0)
            $ix = $this->index[$group-1];

        if (strlen($ix) > 12)
            $ix = $this->findRecord($ix, $ipbin, 12);

        return $ix;
    }

    /**
     * Load the specific data chunk from the main data file and save it
     * to the data cache.
     */
    private function getDataEntry($pos, $len)
    {
        // If it doesn't exist in the cache, load it
        if (!isset($this->data[$pos]))
        {
            fseek($this->fp, $pos + $this->offset);
            $this->data[$pos] = fread($this->fp, $len + 6);  // Always one extra record, for upper bound
        }

        return $this->data[$pos];
    }

    /**
     * Lookup a 4-byte binary IP string and return the two-letter ISO country.
     */
    public function lookupbin($ipbin)
    {
        if (strlen($this->lastentry) == 10 && strcmp($ipbin, substr($this->lastentry, 0, 4)) >= 0 && strcmp($ipbin, substr($this->lastentry, 6, 4)) < 0)
            return substr($this->lastentry, 4, 2);

        $unpacked = @unpack('L3', $this->findIndex($ipbin));

        if (!is_array ($unpacked)) {
          return '07';
        }

        list(, , $pos, $len) = $unpacked;
        $data = $this->getDataEntry($pos, $len);

        $this->lastentry = $this->findRecord($data, $ipbin, 6);

        return substr($this->lastentry, 4, 2);
    }

    /**
     * Lookup an IP address and return the two-letter ISO country.
     */
    public function lookup($ip)
    {
      return $this->lookupbin(@inet_pton($ip));
    }
}

class Ip2Country6 {

    private $lists;
    private $list_offsets = array();
    private $list_items   = array();
    private $fp;
    private $ranges = array();

    public function __construct ($datafile = null) {
      if (!$datafile) $datafile = 'data/ip2country6.dat';

      $this->fp = fopen ($datafile, 'r');

      $header = fread ($this->fp, 4);
      list (, $this->lists) = unpack ('L', $header);

      $header = fread ($this->fp, $this->lists * 4);
      $this->list_offsets = array_values (unpack ('L'.$this->lists, $header));

      $header = fread ($this->fp, $this->lists * 4);
      $this->list_items = array_values (unpack ('L'.$this->lists, $header));
    }

    public function __destruct () {
      if ($this->fp) fclose ($this->fp);
    }

    public function preload () {
      for ($hash = 0; $hash < $this->lists; $hash ++) {

//        echo "Loading list ", $hash, "\n";

        $items = $this->list_items [$hash];
        $list_string = fread ($this->fp, $items * 34);
        $list_items = str_split ($list_string, 34);

        $this->ranges [$hash] = array ();
        foreach ($list_items as $list_item) {
          $range_start = substr ($list_item,  0, 16);
          $range_end   = substr ($list_item, 16, 16);
          $iso_code    = substr ($list_item, 32,  2);

          $this->ranges [$hash] []= array ($range_start, $range_end, $iso_code);
        }
      }
    }

    public function lookup ($ip) {

      if (!filter_var ($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
//        throw new Exception ('Invalid IPv6 address: ', $ip);
        return '01';
      }

      // Check for IPv6 support to avoid warning
      if (defined ('AF_INET6')) {
        // PHP was compiled without --disable-ipv6 option
        $ip_bin = @inet_pton ($ip);
      } else return '02';

      $ip_hash = substr ($ip_bin, 0, 2);
      $len = strlen ($ip_hash);
      $hash = 0;

      for ($index = 0; $index < $len; $index ++) {
        $hash ^= ord ($ip_hash [$index]);
      }
      $hash &= $this->lists - 1;

      if (!isset ($this->ranges [$hash])) {
        $offset = $this->list_offsets [$hash];
        if ($offset != 0) {
          // Move to list offset
          $list = fread ($this->fp, $offset);
        }

        $items = $this->list_items [$hash];
        $list_string = fread ($this->fp, $items * 34);
        $list_items = str_split ($list_string, 34);

        $this->ranges [$hash] = array ();
        foreach ($list_items as $list_item) {
          $range_start = substr ($list_item,  0, 16);
          $range_end   = substr ($list_item, 16, 16);
          $iso_code    = substr ($list_item, 32,  2);

          $this->ranges [$hash] []= array ($range_start, $range_end, $iso_code);
        }
      }

      foreach ($this->ranges [$hash] as $range) {
        if ($ip_bin >= $range [0] && $ip_bin <= $range [1]) {
          return $range [2];
        }
      }
    }
}

function get_client_ip_address () {
  if (isset ($_GET ['ai-debug-ip-address'])) {
    if (isset ($_COOKIE ['AI_WP_DEBUGGING']))
      return strtolower ($_GET ['ai-debug-ip-address']);
  }
  elseif (isset ($_GET ['ai-check-ip-address'])) {
    if (strpos ($_SERVER ['SERVER_NAME'], 'adinserter.pro') !== false) {
      return strtolower ($_GET ['ai-check-ip-address']);
    }
  }

  if (defined ('AI_CLIENT_IP_ADDRESS')) {
    return strtolower (AI_CLIENT_IP_ADDRESS);
  }

  $server_addr = isset ($_SERVER ['SERVER_ADDR']) ? $_SERVER ['SERVER_ADDR'] : '';
  if (defined ('AI_CLIENT_IP_SERVER_VAR')) $ai_client_ip_server_var = array (AI_CLIENT_IP_SERVER_VAR); else {
    $ai_client_ip_server_var = array (
      'HTTP_CF_CONNECTING_IP',
      'HTTP_CLIENT_IP',
      'HTTP_INCAP_CLIENT_IP',
      'HTTP_X_FORWARDED_FOR',
      'HTTP_X_FORWARDED',
      'HTTP_X_CLUSTER_CLIENT_IP',
      'HTTP_FORWARDED_FOR',
      'HTTP_FORWARDED',
      'REMOTE_ADDR',
    );
  }
  foreach ($ai_client_ip_server_var as $key) {
    if (array_key_exists ($key, $_SERVER) === true) {
      foreach (explode (',', $_SERVER [$key]) as $ip) {
        $ip = str_replace ("for=", "", $ip);
        $ip = trim ($ip); // just to be safe
        switch ($key) {
          case 'HTTP_X_FORWARDED_FOR':
            if ($server_addr != '' && $ip == $server_addr) continue 3; // HTTP_X_FORWARDED_FOR may report server IP address
            break;
        }
        if (substr_count ($ip, ':', 0) == 1) continue; // IP address in HTTP_X_FORWARDED_FOR may report port - only for IPv4

        return $ip;
      }
    }
  }
 return '';
}

function ip_to_country_webnet77 ($ip) {
  $result = null;
  if (strpos ($ip, ':') === false) {
    if (file_exists (__DIR__.'/data/ip2country.dat')) {
      $ipc = new Ip2Country (__DIR__.'/data/ip2country.dat');
//      $ipc->preload();
      try {
        $result = $ipc->lookup ($ip);
      } catch (Exception $e) {
      }
      if ($result == null) return '04';
    } else return '03';
  } else {
      if (file_exists (__DIR__.'/data/ip2country6.dat')) {
        $ipc = new Ip2Country6 (__DIR__.'/data/ip2country6.dat');
//        $ipc->preload();
        try {
          $result = $ipc->lookup ($ip);
        } catch (Exception $e) {
        }
      } else return '05';
      if ($result == null) return '06';
    }

  return strtoupper ($result);
}

function ip_to_country_maxmind ($ip, $database, $details) {
  require_once __DIR__.'/maxmind/autoload.php';

  try {
    $reader = new Reader ($database);

    try {
      $record = $reader->city ($ip);
    } catch (\BadMethodCallException $e) {
      $record = $reader->country ($ip);
      $details = false;
    }
  } catch(\Exception $e) {
    return '10';
  }

  if (!$details) return $record->country->isoCode;

  $subdivision = $record->mostSpecificSubdivision->isoCode == null ? '' : $record->mostSpecificSubdivision->isoCode;
  $city = $record->city->name == null ? '' : $record->city->name;

  return array ($record->country->isoCode, $subdivision, $city);
}
                                               // Normally CloudFlare country data can be used - invalid data for direct IP checks (CFP)
function ip_to_country ($ip, $details = false, $force_database_check = false) {
  global $ai_wp_data;

  if (isset ($_GET ['ai-debug-country'])) {
    if (isset ($_COOKIE ['AI_WP_DEBUGGING'])) {
      $parts = explode (':', strtoupper ($_GET ['ai-debug-country']));
      if (count ($parts) > 1) return ($parts);

      return strtoupper ($_GET ['ai-debug-country']);
    }
  }

  $country_data = false;
  if (function_exists ('apply_filters')) {
    $country_data = apply_filters ("ai_ip_to_country", false, $ip, $details);
  }

  if ($country_data !== false) {
    return $country_data;
  }

  if (!$force_database_check && !$details && isset ($_SERVER ['HTTP_CF_IPCOUNTRY'])) {
    return strtoupper ($_SERVER ['HTTP_CF_IPCOUNTRY']);
  }

  if (!defined ('AI_IP_TO_COUNTRY')) {
    define ('AI_IP_TO_COUNTRY', 74);
  }

  if (!defined ('AI_NO_IP_TO_COUNTRY_CACHING') && $ip != '') {
    if (isset ($ai_wp_data [AI_IP_TO_COUNTRY][$ip])) {
      return $ai_wp_data [AI_IP_TO_COUNTRY][$ip];
    }
  }

  if (defined ('AI_MAXMIND_DB')) {
    $ip_to_country = ip_to_country_maxmind ($ip, AI_MAXMIND_DB, $details);
    $ai_wp_data [AI_IP_TO_COUNTRY][$ip] = $ip_to_country;
    return $ip_to_country;
  }

  $ip_to_country = ip_to_country_maxmind ($ip, dirname ( __FILE__) . '/data/ip2country.dat', false);

  $ai_wp_data [AI_IP_TO_COUNTRY][$ip] = $ip_to_country;
  return $ip_to_country;
}

function ip_to_country_internal ($ip) {
  return ip_to_country_maxmind ($ip, dirname ( __FILE__) . '/data/ip2country.dat', false);
}

function check_ip_address_list ($ip_addresses, $white_list) {

  $return = $white_list;

  if (trim ($ip_addresses) == AD_EMPTY_DATA) return true;
  $ip_addresses = explode (",", $ip_addresses);

  $client_ip_address = strtolower (get_client_ip_address ());

  foreach ($ip_addresses as $ip_address) {
    $ip_address = strtolower (trim ($ip_address));
    if ($ip_address == "") continue;

    if ($ip_address [0] == '*') {
      if ($ip_address [strlen ($ip_address) - 1] == '*') {
        $ip_address = substr ($ip_address, 1, strlen ($ip_address) - 2);
        if (strpos ($client_ip_address, $ip_address) !== false) return $return;
      } else {
          $ip_address = substr ($ip_address, 1);
          if (substr ($client_ip_address, - strlen ($ip_address)) == $ip_address) return $return;
        }
    }
    elseif ($ip_address [strlen ($ip_address) - 1] == '*') {
      $ip_address = substr ($ip_address, 0, strlen ($ip_address) - 1);
      if (strpos ($client_ip_address, $ip_address) === 0) return $return;
    }
    elseif ($ip_address == "#") {
      if ($client_ip_address == '') return $return;
    }
    elseif ($ip_address == $client_ip_address) return $return;
  }
  return !$return;
}

function check_country_list ($countries, $white_list) {

  $return = $white_list;

  if (trim ($countries) == AD_EMPTY_DATA) return true;
  $countries = explode (",", $countries);

  $country_from_ip_address = ip_to_country (get_client_ip_address (), true);

  if (!is_array ($country_from_ip_address)) {
    $country_array = array ($country_from_ip_address, '', '');
  } else $country_array = $country_from_ip_address;

  if (!isset ($country_array [1])) $country_array [1] = '';
  if (!isset ($country_array [2])) $country_array [2] = '';

  foreach ($countries as $list_country) {

    $list_country = strtoupper (trim ($list_country));
    if ($list_country == "") continue;

    $list_country_array = explode (':', $list_country);
    if (!isset ($list_country_array [1]) || $country_array [1] == '') $list_country_array [1] = '';
    if (!isset ($list_country_array [2]) || $country_array [2] == '') $list_country_array [2] = '';

    $list_country_data = implode (':', $list_country_array);

    $country_data = strtoupper ($country_array [0] . ':' . ($list_country_array [1] == '' ? '' : $country_array [1]) . ':' . ($list_country_array [2] == '' ? '' : $country_array [2]));

    if ($list_country == "#::") {
      if ($country_from_ip_address == '') return $return;
    } elseif ($list_country_data == $country_data) return $return;
  }
  return !$return;
}

if (isset ($_GET ["ip-country"])) {
  $client_ip_address = get_client_ip_address ();
  if (isset ($_GET ["maxmind"]) && file_exists ($_GET ["maxmind"])) {
    define ('AI_MAXMIND_DB', $_GET ["maxmind"]);
  }
  if ($_GET ["ip-country"] == 'ip-address') {
    echo $client_ip_address;
  }
  elseif ($_GET ["ip-country"] == 'country') {
    echo ip_to_country ($client_ip_address);
  }
  elseif ($_GET ["ip-country"] == 'country-internal') {
    echo ip_to_country_internal ($client_ip_address);
  }
  elseif ($_GET ["ip-country"] == 'ip-address-country') {
    echo json_encode (array ($client_ip_address, ip_to_country ($client_ip_address)));
  }
  elseif ($_GET ["ip-country"] == 'ip-address-country-city') {
    $ip_to_country = ip_to_country ($client_ip_address, true);
    if (is_array ($ip_to_country)) {
      echo json_encode (array_merge (array ($client_ip_address), $ip_to_country));
    } else echo json_encode (array ($client_ip_address, $ip_to_country));
  }
}

}
