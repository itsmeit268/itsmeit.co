setTimeout (function() {
  var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//  var ai_debug = false;

  try {
    if (ai_debug) console.log ("AI AD BLOCKING EXTRA CODE TIMEOUT 1");

    fetch (new Request (b64d ("aHR0cHM6Ly9wYWdlYWQyLmdvb2dsZXN5bmRpY2F0aW9uLmNvbS9wYWdlYWQvanMvYWRzYnlnb29nbGUuanM="), {method: b64d ("SEVBRA=="), mode: b64d ("bm8tY29ycw==")})).then (function (response) {
      if (ai_debug) console.log ("AI AD BLOCKING EXTRA CODE FETCH OK");
    }).catch (function (error) {
      if (ai_debug) console.log ("AI AD BLOCKING EXTRA CODE FETCH ERROR", error);

      setTimeout (function() {

        if (typeof jQuery == 'function') {
          if (ai_debug) console.log ("AI AD BLOCKING EXTRA CODE TIMEOUT 2");

          var element = jQuery(b64d ("Ym9keQ=="));
          var ai_masking_data = element.attr (AI_ADB_ATTR_NAME);
          if (typeof ai_masking_data !== "string") {
            var body_children = element.children ();
            body_children.eq (Math.floor (Math.random() * body_children.length)).after (AI_ADB_OVERLAY_WINDOW);
            body_children.eq (Math.floor (Math.random() * body_children.length)).after (AI_ADB_MESSAGE_WINDOW);

            if (ai_debug) console.log ("AI AD BLOCKING EXTRA CODE INSERTED");
          }
        }
      }, 5432);
    });
  } catch (error) {
    if (ai_debug) console.log ("AI AD BLOCKING EXTRA CODE TRY ERROR", error);
  }
}, 1);

