// ***
//jQuery (function ($) {

if (typeof ai_ip != 'undefined') {

  function getParameterByName (name, url) {
    if (!url) {
      url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return "";
    return decodeURIComponent(results[2].replace(/\+/g, " "));
  }

  function ai_random_parameter () {
    var current_time = new Date ().getTime ();
    return current_time + '-' + Math.round (Math.random () * 100000);
  }

  function process_ip_data (ai_ip_data_blocks) {
    var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//    var ai_debug = false;

    // ***
//    ai_ip_data_blocks.removeClass ('ai-ip-data');
    ai_ip_data_blocks.forEach ((element, i) => {
      element.classList.remove ('ai-ip-data');
    });

    var enable_block = false;

    if (ai_debug) console.log ('');
    if (ai_debug) console.log ("AI IP DATA:", ai_ip_data);

    if (ai_ip_data == '') {
      if (ai_debug) console.log ('AI IP DATA EMPTY');
      return;
    }
    try {
      var ip_data_array = JSON.parse (ai_ip_data);

      var ip_address  = ip_data_array [0];
      var country     = ip_data_array [1];
      var subdivision = ip_data_array [2];
      var city        = ip_data_array [3];
    } catch (error) {
        if (ai_debug) console.log ('AI IP DATA JSON ERROR');
        return;
      }

    var cfp_blocked = false;

    if (ip_address.indexOf ('#') != - 1 ) {
      cfp_blocked = true;
      ip_address = ip_address.replace ('#', '');

      if (ai_debug) console.log ("AI LISTS ip address CFP BLOCKED");
    }

    var ip_data_text = '';

    if (cfp_blocked) {
      ip_data_text = 'CFP BLOCKED, ';
    }

    ip_data_text = ip_data_text + ip_address + ', ' + country;

    if (subdivision != null && city != null) {
      ip_data_text = ip_data_text + ':' + subdivision + ':' + city;
    }

    if (subdivision == null) subdivision = '';
    if (city == null) city = '';

    // ***
//    if (ip_data_array != null) ai_ip_data_blocks.each (function () {
    if (ip_data_array != null) ai_ip_data_blocks.forEach ((el, i) => {

    // ***
//      var block_wrapping_div = $(this).closest ('div.AI_FUNCT_GET_BLOCK_CLASS_NAME');
      var block_wrapping_div = el.closest ('div.' + ai_block_class_def);

    // ***
//      if (ai_debug) console.log ('AI LISTS BLOCK', block_wrapping_div.attr ('class'));
      if (ai_debug) console.log ('AI LISTS BLOCK', block_wrapping_div != null && block_wrapping_div.hasAttribute ("class") ? block_wrapping_div.getAttribute ('class') : '');
//
      enable_block = true;
      var ip_addresses_processed = false;

      // ***
//      var ip_addresses_list = $(this).attr ("ip-addresses");
//      if (typeof ip_addresses_list != "undefined") {
      if (el.hasAttribute ("ip-addresses")) {
        var ip_addresses_list = el.getAttribute ("ip-addresses");

        var ip_address_array      = ip_addresses_list.split (",");
        // ***
//        var ip_address_list_type  = $(this).attr ("ip-address-list");
        var ip_address_list_type  = el.getAttribute ("ip-address-list");

        if (ai_debug) console.log ("AI LISTS ip address:     ", ip_address);
        if (ai_debug) console.log ("AI LISTS ip address list:", ip_addresses_list, ip_address_list_type);

        var found = false;

        // ***
//        $.each (ip_address_array, function (index, list_ip_address) {
        ip_address_array.every ((list_ip_address, index) => {

          if (list_ip_address.charAt (0) == "*") {
            if (list_ip_address.charAt (list_ip_address.length - 1) == "*") {
              list_ip_address = list_ip_address.substr (1, list_ip_address.length - 2);
              if (ip_address.indexOf (list_ip_address) != - 1) {
                found = true;
                return false;
              }
            } else {
                list_ip_address = list_ip_address.substr (1);
                if (ip_address.substr (- list_ip_address.length) == list_ip_address) {
                  found = true;
                  return false;
                }
              }
          }
          else if (list_ip_address.charAt (list_ip_address.length - 1) == "*") {
            list_ip_address = list_ip_address.substr (0, list_ip_address.length - 1);
            if (ip_address.indexOf (list_ip_address) == 0) {
              found = true;
              return false;
            }
          }
          else if (list_ip_address == "#") {
            if (ip_address == "") {
              found = true;
              return false;
            }
          }
          else if (list_ip_address.toUpperCase () == "CFP") {
            if (cfp_blocked) {
              found = true;
              return false;
            }
          }
          else if (list_ip_address == ip_address) {
            found = true;
            return false;
          }

          return true;
        });

        switch (ip_address_list_type) {
          case "B":
            if (found) enable_block = false;
            break;
          case "W":
            if (!found) enable_block = false;
            break;
        }

        if (ai_debug) console.log ("AI LISTS list found", found);
        if (ai_debug) console.log ("AI LISTS list pass", enable_block);
        ip_addresses_processed = true;
      }

      if (enable_block) {
        // ***
//        var countries_list = $(this).attr ("countries");
//        if (typeof countries_list != "undefined") {
        if (el.hasAttribute ("countries")) {
          var countries_list = el.getAttribute ("countries");

          var country_array     = countries_list.split (",");
          // ***
//          var country_list_type = $(this).attr ("country-list");
          var country_list_type = el.getAttribute ("country-list");

            if (ai_debug && ip_addresses_processed) console.log ('');
            if (ai_debug) console.log ("AI LISTS country:     ", country + ':' + subdivision + ':' + city);
            if (ai_debug) console.log ("AI LISTS country list:", countries_list, country_list_type);

          var found = false;
          // ***
//          $.each (country_array, function (index, list_country) {
          country_array.every ((list_country, index) => {

            var list_country_data = list_country.trim ().split (":");
            if (list_country_data [1] == null || subdivision == '') list_country_data [1] = '';
            if (list_country_data [2] == null || city == '') list_country_data [2] = '';
            var list_country_expaneded = list_country_data.join (':').toUpperCase ();

            var country_expaned = (country + ':' + (list_country_data [1] == '' ? '' : subdivision) + ':' + (list_country_data [2] == '' ? '' : city)).toUpperCase ();

            if (ai_debug) console.log ("AI LISTS country to check: ", country_expaned);
            if (ai_debug) console.log ("AI LISTS country list item:", list_country_expaneded);

            if (list_country_expaneded == country_expaned) {
              found = true;
              return false;
            }

            return true;
          });
          switch (country_list_type) {
            case "B":
              if (found) enable_block = false;
              break;
            case "W":
              if (!found) enable_block = false;
              break;
          }

          if (ai_debug) console.log ("AI LISTS list found", found);
          if (ai_debug) console.log ("AI LISTS list pass", enable_block);
        }
      }

      // ***
//      $(this).css ({"visibility": "", "position": "", "width": "", "height": "", "z-index": ""});
      el.style.visibility = '';
      el.style.position = '';
      el.style.width = '';
      el.style.height = '';
      el.style.zIndex = '';

      // ***
//      var debug_bar = $(this).prev ('.ai-debug-bar');
//      debug_bar.find ('.ai-debug-name.ai-ip-country').text (ip_data_text);
//      debug_bar.find ('.ai-debug-name.ai-ip-status').text (enable_block ? ai_front.visible : ai_front.hidden);
      var debug_bar = el.previousElementSibling;
      while (debug_bar) {
        if (debug_bar.matches ('.ai-debug-bar')) break;
        debug_bar = debug_bar.previousElementSibling;
      }
      if (debug_bar != null) {
        var debug_bar_data = debug_bar.querySelector (".ai-debug-name.ai-ip-country");
        if (debug_bar_data != null) {
          debug_bar_data.textContent = ip_data_text;
        }
        debug_bar_data = debug_bar.querySelector (".ai-debug-name.ai-ip-status");
        if (debug_bar_data != null) {
          debug_bar_data.textContent = enable_block ? ai_front.visible : ai_front.hidden;
        }
      }

      if (!enable_block) {
        // ***
//        $(this).hide (); // .ai-list-data
        el.style.display = 'none';

        // ***
//        if (block_wrapping_div.length) {
        if (block_wrapping_div != null) {
          // ***
//          block_wrapping_div.removeAttr ('data-ai').removeClass ('ai-track');
          block_wrapping_div.removeAttribute ('data-ai');
          block_wrapping_div.classList.remove ('ai-track');

          // ***
//          if (block_wrapping_div.find ('.ai-debug-block').length) {
          if (block_wrapping_div.querySelector (".ai-debug-block") != null) {

            // ***
//            block_wrapping_div.css ({"visibility": ""}).removeClass ('ai-close');
            block_wrapping_div.style.visibility = '';
            block_wrapping_div.classList.remove ('ai-close');

            // ***
//            if (block_wrapping_div.hasClass ('ai-remove-position')) {
            if (block_wrapping_div.classList.contains ('ai-remove-position')) {
              // ***
//              block_wrapping_div.css ({"position": ""});
              block_wrapping_div.style.position = '';
            }

            // In case client-side insert is used and lists will not be processed
            // ***
//            if (typeof $(this).data ('code') != 'undefined') {
            if (el.hasAttribute ('data-code')) {

              // Remove ai-list-block to show debug info
              // ***
//              block_wrapping_div.removeClass ('ai-list-block');
//              block_wrapping_div.removeClass ('ai-list-block-filter');
              block_wrapping_div.classList.remove ('ai-list-block');
              block_wrapping_div.classList.remove ('ai-list-block-filter');

              // Remove also 'NOT LOADED' bar if it is there
              // ***
//              if (block_wrapping_div.prev ().hasClass ('ai-debug-info')) {
              if (block_wrapping_div.previousElementSibling != null && block_wrapping_div.previousElementSibling.classList.contains ('ai-debug-info')) {
                // ***
//                block_wrapping_div.prev ().remove ();
                block_wrapping_div.previousElementSibling.remove ();
              }
            }

          } else
          // ***
//          if (block_wrapping_div [0].hasAttribute ('style') && block_wrapping_div.attr ('style').indexOf ('height:') == - 1) {
          if (block_wrapping_div.hasAttribute ('style') && block_wrapping_div.getAttribute ('style').indexOf ('height:') == - 1) {
            // ***
//            block_wrapping_div.hide ();
            block_wrapping_div.style.display = 'none';
          }
        }
      } else {
          if (block_wrapping_div != null) {
            // ***
  //          block_wrapping_div.css ({"visibility": ""});
            block_wrapping_div.style.visibility = '';

            // ***
  //          if (block_wrapping_div.hasClass ('ai-remove-position')) {
            if (block_wrapping_div.classList.contains ('ai-remove-position')) {
            // ***
  //            block_wrapping_div.css ({"position": ""});
              block_wrapping_div.style.position = '';
            }
          }

          // ***
//          if (typeof $(this).data ('code') != 'undefined') {
          if (el.hasAttribute ('data-code')) {

            // ***
//            var block_code = b64d ($(this).data ('code'));
            var block_code = b64d (el.dataset.code);

            var range = document.createRange ();
            var fragment_ok = true;
            try {
              var fragment = range.createContextualFragment (block_code);
            }
            catch (err) {
              var fragment_ok = false;
              if (ai_debug) console.log ('AI IP', 'range.createContextualFragment ERROR:', err);
            }

            if (fragment_ok) {
              // ***
  //            if ($(this).closest ('head').length != 0) {
              if (el.closest ('head') != null) {
                // ***
  //              $(this).after (block_code);
                el.parentNode.insertBefore (fragment, el.nextSibling);

                // ***
  //              if (!ai_debug) $(this).remove ();
                if (!ai_debug) el.remove ();
              // ***
  //            } else $(this).append (block_code);
              } else el.append (fragment);
            }

//                if (!ai_debug)
            // ***
//            $(this).attr ('data-code', '');
            el.removeAttribute ('data-code');

            // ***
//            if (ai_debug) console.log ('AI INSERT CODE', $(block_wrapping_div).attr ('class'));
            if (ai_debug) console.log ('AI INSERT CODE', block_wrapping_div != null && block_wrapping_div.hasAttribute ("class") ? block_wrapping_div.getAttribute ('class') : '');
            if (ai_debug) console.log ('');

            // ***
//            ai_process_element (this);
            ai_process_element (el);
          }
        }

      // ***
//      block_wrapping_div.removeClass ('ai-list-block-ip');
      if (block_wrapping_div != null) {
        block_wrapping_div.classList.remove ('ai-list-block-ip');
      }
    });
  }

  ai_process_ip_addresses = function (ai_ip_data_blocks) {

    var ai_debug = typeof ai_debugging !== 'undefined'; // 2
//    var ai_debug = false;

    if (ai_ip_data_blocks == null) {
      // ***
//      ai_ip_data_blocks = $("div.ai-ip-data, meta.ai-ip-data");
      ai_ip_data_blocks = document.querySelectorAll ("div.ai-ip-data, meta.ai-ip-data");
    } else {
        // Temp fix for jQuery elements
        // ***
        if (window.jQuery && window.jQuery.fn && ai_ip_data_blocks instanceof jQuery) {
          // Convert jQuery object to array
          ai_ip_data_blocks = Array.prototype.slice.call (ai_ip_data_blocks);
        }

        // ***
//        ai_ip_data_blocks = ai_ip_data_blocks.filter ('.ai-ip-data');
        var filtered_elements = [];
        ai_ip_data_blocks.forEach ((element, i) => {
          if (element.matches ('.ai-ip-data')) {
            filtered_elements.push (element);
          } else {
              var list_data_elements = element.querySelectorAll ('.ai-ip-data');
              if (list_data_elements.length) {
                list_data_elements.forEach ((list_element, i2) => {
                  filtered_elements.push (list_element);
                });
              }
            }
        });
        ai_ip_data_blocks = filtered_elements;

      }

    if (!ai_ip_data_blocks.length) return;

    if (ai_debug) console.log ("AI PROCESSING IP ADDRESSES:", ai_ip_data_blocks.length, "blocks");

    if (typeof ai_ip_data != 'undefined') {
      if (ai_debug) console.log ("SAVED IP DATA:", ai_ip_data);
      process_ip_data (ai_ip_data_blocks);
      return;
    }

    if (typeof ai_ip_data_requested != 'undefined') {
      if (ai_debug) console.log ("IP DATA ALREADY REQUESTED, STILL WAITING...");
      return;
    }

    if (ai_debug) console.log ("REQUESTING IP DATA");

    ai_ip_data_requested = true;

//    var site_url = "AI_SITE_URL";
//    var page = site_url+"/wp-admin/admin-ajax.php?action=ai_ajax&ip-data=ip-address-country-city";
    var page = ai_ajax_url + "?action=ai_ajax&ip-data=ip-address-country-city";

    var debug_ip_address = getParameterByName ("ai-debug-ip-address");
    if (debug_ip_address != null) page += "&ai-debug-ip-address=" + debug_ip_address;
    var debug_ip_address = getParameterByName ("ai-debug-country");
    if (debug_ip_address != null) page += "&ai-debug-country=" + debug_ip_address;

      // ***
//    $.get (page, function (ip_data) {
//    $.ajax ({
//        url: page,
//        type: "post",
//        data: {
//          ai_check: ai_data_id,
//          ai_version: ai_random_parameter ()
//        },
//        async: true
//    }).done (function (ip_data) {

    var url_data = {
      ai_check: ai_data_id,
      version: ai_random_parameter ()
    };

    var formBody = [];
    for (var property in url_data) {
      var encodedKey = encodeURIComponent (property);
      var encodedValue = encodeURIComponent (url_data [property]);
      formBody.push (encodedKey + "=" + encodedValue);
    }
    formBody = formBody.join ("&");

    async function ai_get_ip_data () {
      const response = await fetch (page, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: formBody
      });

      const text = await response.text ();

      return text;
    }

    ai_get_ip_data ().then (ip_data => {
      ai_ip_data = ip_data;

      if (ip_data == '') {
        var error_message = 'Ajax request returned empty data, geo-targeting disabled';
        console.error (error_message);

        if (typeof ai_js_errors != 'undefined') {
          ai_js_errors.push ([error_message, page, 0]);
        }
      } else {
          try {
            var ip_data_test = JSON.parse (ip_data);
          } catch (error) {
            var error_message = 'Ajax call returned invalid data, geo-targeting disabled';
            console.error (error_message, ip_data);

            if (typeof ai_js_errors != 'undefined') {
              ai_js_errors.push ([error_message, page, 0]);
            }
          }
        }

      if (ai_debug) console.log ('');
      if (ai_debug) console.log ("AI IP RETURNED DATA:", ai_ip_data);

      // Check blocks again - some blocks might get inserted after the IP data was requested
      // ***
//      ai_ip_data_blocks = $("div.ai-ip-data, meta.ai-ip-data");
      ai_ip_data_blocks = document.querySelectorAll ("div.ai-ip-data, meta.ai-ip-data");

      if (ai_debug) console.log ("AI IP DATA BLOCKS:", ai_ip_data_blocks.length);

      if (!ai_ip_data_blocks.length) return;

      process_ip_data (ai_ip_data_blocks);
    // ***
//    }).fail (function(jqXHR, status, err) {
    })
    .catch ((error) => {
//      console.error (e.message); // "oh, no!"
      // ***
//      if (ai_debug) console.log ("Ajax call failed, Status: " + status + ", Error: " + err);
      if (ai_debug) console.error ("Ajax call failed, error:", error);
      // ***
//      $("div.ai-ip-data").each (function () {
      document.querySelectorAll ('div.ai-ip-data').forEach ((el, index) => {

        // ***
//        $(this).css ({"display": "none", "visibility": "", "position": "", "width": "", "height": "", "z-index": ""}).removeClass ('ai-ip-data').hide ();
        el.style.display = 'none';
        el.style.visibility = '';
        el.style.position = '';
        el.style.width = '';
        el.style.height = '';
        el.style.zIndex = '';

        el.classList.remove ('ai-ip-data');
      });
    });
  }


function ai_ready (fn) {
  if (document.readyState === 'complete' || (document.readyState !== 'loading' && !document.documentElement.doScroll)) {
    fn ();
  } else {
     document.addEventListener ('DOMContentLoaded', fn);
  }
}

  // ***
//  $(document).ready (function($) {
//    setTimeout (function () {ai_process_ip_addresses ()}, 5);
//  });

function ai_check_ip_addresses () {
  setTimeout (function () {ai_process_ip_addresses ()}, 5);
}

ai_ready (ai_check_ip_addresses);


//});

function ai_process_element (element) {
  setTimeout (function() {
    if (typeof ai_process_rotations_in_element == 'function') {
      ai_process_rotations_in_element (element);
    }

    if (typeof ai_process_lists == 'function') {
//      ai_process_lists (jQuery (".ai-list-data", element));
      ai_process_lists ();
    }

    if (typeof ai_process_ip_addresses == 'function') {
//      ai_process_ip_addresses (jQuery (".ai-ip-data", element));
      ai_process_ip_addresses ();
    }

    if (typeof ai_process_filter_hooks == 'function') {
//      ai_process_filter_hooks (jQuery (".ai-filter-check", element));
      ai_process_filter_hooks ();
    }

    if (typeof ai_adb_process_blocks == 'function') {
      ai_adb_process_blocks (element);
    }

    if (typeof ai_process_impressions == 'function' && ai_tracking_finished == true) {
      ai_process_impressions ();
    }
    if (typeof ai_install_click_trackers == 'function' && ai_tracking_finished == true) {
      ai_install_click_trackers ();
    }

    if (typeof ai_install_close_buttons == 'function') {
      ai_install_close_buttons (document);
    }
  }, 5);
}
}
