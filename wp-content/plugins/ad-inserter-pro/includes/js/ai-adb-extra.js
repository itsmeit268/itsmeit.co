setTimeout (function() {
  var ai_adb_debugging = typeof ai_debugging !== 'undefined'; // 1
//  var ai_adb_debugging = false;

  var ai_adb_devices = AI_FUNC_GET_ADB_DEVICES;

  if (typeof MobileDetect !== "undefined") {
    var md = new MobileDetect (window.navigator.userAgent);

    // ENABLED FOR_ALL_DEVICES
    if (ai_adb_devices != 6) {

      if (ai_adb_debugging) console.log ('AI AD BLOCKING DEVICES:', ai_adb_devices);
      if (ai_adb_debugging) console.log ('AI AD BLOCKING DEVICE desktop',  !md.mobile ());
      if (ai_adb_debugging) console.log ('AI AD BLOCKING DEVICE  mobile', !!md.mobile ());
      if (ai_adb_debugging) console.log ('AI AD BLOCKING DEVICE   phone', !!md.phone ());
      if (ai_adb_debugging) console.log ('AI AD BLOCKING DEVICE  tablet', !!md.tablet ());

      switch (ai_adb_devices) {
        // ENABLED FOR DESKTOP_DEVICES
        case 0:
          if (!!md.mobile ()) return false;
          break;
        // ENABLED FOR MOBILE_DEVICES
        case 1:
          if (!md.mobile ()) return false;
          break;
        // ENABLED FOR TABLET_DEVICES
        case 2:
          if (!md.tablet ()) return false;
          break;
        // ENABLED FOR PHONE_DEVICES
        case 3:
          if (!md.phone ()) return false;
          break;
        // ENABLED FOR DESKTOP_TABLET_DEVICES
        case 4:
          if (!!md.phone ()) return false;
          break;
        // ENABLED FOR DESKTOP_PHONE_DEVICES
        case 5:
          if (!!md.tablet ()) return false;
          break;
      }
    }
  }

  try {
    if (ai_adb_debugging) console.log ("AI AD BLOCKING EXTRA CODE TIMEOUT 1");

    var ok = false;
    fetch (b64d ("aHR0cHM6Ly9wYWdlYWQyLmdvb2dsZXN5bmRpY2F0aW9uLmNvbS9wYWdlYWQvanMvYWRzYnlnb29nbGUuanM="), {method: b64d ("SEVBRA==")}).then (function (response) {
      if (ai_adb_debugging) console.log ("AI AD BLOCKING EXTRA CODE FETCH OK", response.ok, response);
      if (!response.ok) check_status ();
    }).catch (function (error) {
      if (ai_adb_debugging) console.log ("AI AD BLOCKING EXTRA CODE FETCH ERROR", error);
      check_status ();
    });

    function insertAfter (newNode, referenceNode) {
      referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
    }

    function check_status () {
      setTimeout (function() {

//        if (typeof jQuery == 'function') {
          if (ai_adb_debugging) console.log ("AI AD BLOCKING EXTRA CODE TIMEOUT 2");

//          var element = jQuery(b64d ("Ym9keQ=="));
          // ***
          var element = document.querySelector (b64d ("Ym9keQ=="));
//          var ai_masking_data = element.attr (AI_ADB_ATTR_NAME);
          // ***
          var ai_masking_data = element.getAttribute (AI_ADB_ATTR_NAME);

          if (typeof ai_masking_data !== "string") {
//            var body_children = element.children ();
//            body_children.eq (Math.floor (Math.random() * body_children.length)).after (AI_ADB_OVERLAY_WINDOW);
//            body_children.eq (Math.floor (Math.random() * body_children.length)).after (AI_ADB_MESSAGE_WINDOW);
            // ***
            var body_children = element.children;
            insertAfter (AI_ADB_OVERLAY_WINDOW, body_children.item (Math.floor (Math.random () * body_children.length)));
            insertAfter (AI_ADB_MESSAGE_WINDOW, body_children.item (Math.floor (Math.random () * body_children.length)));

            if (ai_adb_debugging) console.log ("AI AD BLOCKING EXTRA CODE INSERTED");
          }
//        }
      }, 5432);
    }

  } catch (error) {
    if (ai_adb_debugging) console.log ("AI AD BLOCKING EXTRA CODE TRY ERROR", error);
  }
}, 1);

