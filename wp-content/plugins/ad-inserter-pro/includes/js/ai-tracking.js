if (typeof ai_internal_tracking !== 'undefined') {

ai_viewport_names = JSON.parse (b64d (ai_viewport_names_string));

function matchRuleShort (str, rule) {
  var escapeRegex = (str) => str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
  return new RegExp("^" + rule.split("*").map(escapeRegex).join(".*") + "$").test(str);
}

function ai_addEventListener (el, eventName, eventHandler, selector) {
  if (selector) {
    const wrappedHandler = (e) => {
      if (e.target && e.target.matches(selector)) {
        eventHandler(e);
      }
    };
    el.addEventListener (eventName, wrappedHandler);
    return wrappedHandler;
  } else {
    el.addEventListener (eventName, eventHandler);
    return eventHandler;
  }
}

// ***
//(function($) {
  // Tracking handler manager
  // ***
//  $.fn.iframeTracker = function(handler) {
  installIframeTracker = function (handler, target) {
    // Building handler object from handler function
    if (typeof handler == "function") {
      handler = {
        blurCallback: handler
      };
    }

    // ***
//    var target = this.get();
    if (handler === null || handler === false) {
//      $.iframeTracker.untrack(target);
      ai_iframeTracker.untrack (target);
    } else if (typeof handler == "object") {
//      $.iframeTracker.track(target, handler);
      ai_iframeTracker.track (target, handler);
    } else {
      throw new Error ("Wrong handler type (must be an object, or null|false to untrack)");
    }
    return this;
  };

  // ***
  var ai_mouseoverHander = function (handler, event){
    event.data = {'handler': handler};
    ai_iframeTracker.mouseoverListener (event);
  }
  var ai_mouseoutHander = function (handler, event){
    event.data = {'handler': handler};
    ai_iframeTracker.mouseoutListener (event);
  }

  // Iframe tracker common object
  // ***
//  $.iframeTracker = {
  ai_iframeTracker = {
    // State
    focusRetriever: null,  // Element used for restoring focus on window (element)
    focusRetrieved: false, // Says if the focus was retrieved on the current page (bool)
    handlersList: [],      // Store a list of every trakers (created by calling $(selector).iframeTracker...)
    isIE8AndOlder: false,  // true for Internet Explorer 8 and older

    // Init (called once on document ready)
    init: function () {
    // ***
      // Determine browser version (IE8-)
        try {
          // ### AI
          // To prevent replacement of regexp pattern with CDN url (CDN code bug)
          var msie_regexp = new RegExp ('(msie) ([\\w.]+)', 'i');

//          var matches = navigator.userAgent.match(/(msie) ([\w.]+)/i);
          var matches = navigator.userAgent.match (msie_regexp);
          // ### /AI

          if (matches [2] < 9) {
            this.isIE8AndOlder = true;
          }
        } catch (ex2) {}

      // Listening window blur
      // ***
//      $(window).focus();
      window.focus ();

      // ***
//      $(window).blur(function(e) {
      window.addEventListener ('blur', (event) => {
        // ***
//        $.iframeTracker.windowLoseFocus (e);
        ai_iframeTracker.windowLoseFocus (event);
      });

      // Focus retriever (get the focus back to the page, on mouse move)
      // ### AI
      // ### added label for tools like https://web.dev/measure/
      // ***
//      $("body").append('<div style="position:fixed; top:0; left:0; overflow:hidden;"><input style="position:absolute; left:-300px;" type="text" value="" id="focus_retriever" readonly="true" /><label for="focus_retriever">&nbsp;</label></div>');
//      document.querySelector ('body').innerHTML += '<div style="position:fixed; top:0; left:0; overflow:hidden;"><input style="position:absolute; left:-300px;" type="text" value="" id=" focus_retriever" readonly="true" /><label for="focus_retriever">&nbsp;</label></div>';

      var focus_retriever_holder = document.createElement ('div');
      focus_retriever_holder.style = 'position:fixed; top:0; left:0; overflow:hidden;';
      focus_retriever_holder.innerHTML = '<input style="position:absolute; left:-300px;" type="text" value="" id="focus_retriever" readonly="true" /><label for="focus_retriever">&nbsp;</label>';
      document.querySelector ('body').append (focus_retriever_holder);

      // ### /AI
      // ***
//      this.focusRetriever = $("#focus_retriever");
      this.focusRetriever = document.querySelector ("#focus_retriever");
      this.focusRetrieved = false;

      // Special processing to make it work with my old friend IE8 (and older) ;)
      if (this.isIE8AndOlder) {
        // Blur doesn't works correctly on IE8-, so we need to trigger it manually

        this.focusRetriever.blur (function (e) {
          e.stopPropagation ();
          e.preventDefault ();
          // ***
//          $.iframeTracker.windowLoseFocus(e);
          ai_iframeTracker.windowLoseFocus (e);

        });

        // Keep focus on window (fix bug IE8-, focusable elements)
        // ***
//        $("body").click(function(e) {
        document.querySelector ('body').addEventListener ('click', (e) => {
          // ***
//          $(window).focus();
          window.focus ();
        });
        // ***
//        $("form").click(function(e) {
        document.querySelector ('form').addEventListener ('click', (e) => {
          e.stopPropagation ();
        });

        // Same thing for "post-DOMready" created forms (issue #6)
        try {
          // ***
//          $("body").on("click", "form", function(e) {
          ai_addEventListener (document.querySelector ('body'), 'click', (e) => {e.stopPropagation();}, 'form');
        } catch (ex) {
          // ***
//          console.log("[iframeTracker] Please update jQuery to 1.7 or newer. (exception: " + ex.message + ")");
          console.log ("[iframeTracker] error (exception: " + ex.message + ")");
        }
      }
    },

    // Add tracker to target using handler (bind boundary listener + register handler)
    // target: Array of target elements (native DOM elements)
    // handler: User handler object
    track: function (target, handler) {
      // Adding target elements references into handler
      handler.target = target;

      // Storing the new handler into handler list
      // ***
//      $.iframeTracker.handlersList.push(handler);
      ai_iframeTracker.handlersList.push (handler);

      // Binding boundary listener
      // ***
//      $(target)
//        .bind("mouseover", { handler: handler }, $.iframeTracker.mouseoverListener)
//        .bind("mouseout",  { handler: handler }, $.iframeTracker.mouseoutListener);

      target.addEventListener ('mouseover', ai_mouseoverHander.bind (event, handler), false);
      target.addEventListener ('mouseout', ai_mouseoutHander.bind (event, handler), false);
    },

    // Remove tracking on target elements
    // target: target element
    untrack: function (target) {
      if (typeof Array.prototype.filter != "function") {
        console.log ("Your browser doesn't support Array filter, untrack disabled");
        return;
      }

      // Unbinding boundary listener
      // ***
//      $(target).each(function(index) {
      target.forEach ((el, i) => {
//        $(this)
//          .unbind("mouseover", $.iframeTracker.mouseoverListener)
//          .unbind("mouseout", $.iframeTracker.mouseoutListener);

        el.removeEventListener ('mouseover', ai_mouseoverHander, false);
        el.removeEventListener ('mouseout',  ai_mouseoutHander,  false);
      });

      // Handler garbage collector
      var nullFilter = function(value) {
        return value === null ? false : true;
      };
      for (var i in this.handlersList) {
        // Prune target
        for (var j in this.handlersList [i].target) {
          if ($.inArray (this.handlersList [i].target [j], target) !== -1) {
            this.handlersList [i].target [j] = null;
          }
        }
        this.handlersList [i].target = this.handlersList[i].target.filter (nullFilter);

        // Delete handler if unused
        if (this.handlersList [i].target.length === 0) {
          this.handlersList [i] = null;
        }
      }
      this.handlersList = this.handlersList.filter (nullFilter);
    },

    // Target mouseover event listener
    mouseoverListener: function(e) {
      e.data.handler.over = true;
      // ***
//      $.iframeTracker.retrieveFocus();
      ai_iframeTracker.retrieveFocus ();
      try {
        // ***
//        e.data.handler.overCallback(this, e);
        e.data.handler.overCallback (e.data.handler.target, e);
      } catch (ex) {}
    },

    // Target mouseout event listener
    mouseoutListener: function(e) {
      e.data.handler.over = false;
      // ***
//      $.iframeTracker.retrieveFocus();
      ai_iframeTracker.retrieveFocus ();
      try {
        // ***
//        e.data.handler.outCallback(this, e);
        e.data.handler.outCallback (e.data.handler.target, e);
      } catch (ex) {}
    },

    // Give back focus from an iframe to parent page
    retrieveFocus: function() {
      if (document.activeElement && document.activeElement.tagName === "IFRAME") {
        var process_iframe = true;

        // Do not process listed iframes
        if (document.activeElement.hasAttribute ('id') && typeof ai_ignore_iframe_ids !== "undefined" && ai_ignore_iframe_ids.constructor === Array) {
          var iframe_id = document.activeElement.id;
          ai_ignore_iframe_ids.forEach (function (ignored_id) {if (matchRuleShort (iframe_id, ignored_id)) process_iframe = false});
        }

        if (process_iframe && document.activeElement.hasAttribute ('class') && typeof ai_ignore_iframe_classes !== "undefined" && ai_ignore_iframe_classes.constructor === Array) {
          var iframe_class = document.activeElement.className;
          ai_ignore_iframe_classes.forEach (function (ignored_class) {if (matchRuleShort (iframe_class, ignored_class)) process_iframe = false});
        }

        if (process_iframe) {
          // ***
//          $.iframeTracker.focusRetriever.focus();
          ai_iframeTracker.focusRetriever.focus ();
          // ***
//          $.iframeTracker.focusRetrieved = true;
          ai_iframeTracker.focusRetrieved = true;
        }
      }
    },

    // Calls blurCallback for every handler with over=true on window blur
    windowLoseFocus: function (e) {
      for (var i in this.handlersList) {
        if (this.handlersList [i].over === true) {
          try {
            this.handlersList [i].blurCallback (e);
          } catch (ex) {}
        }
      }
    }
  };

function ai_ready (fn) {
  if (document.readyState === 'complete' || (document.readyState !== 'loading' && !document.documentElement.doScroll)) {
    fn ();
  } else {
     document.addEventListener ('DOMContentLoaded', fn);
  }
}

  // Init the iframeTracker on document ready
    // ***
//  $(document).ready(function() {
    // ***
//    $.iframeTracker.init();
function ai_init_IframeTracker () {
  ai_iframeTracker.init ();
}

ai_ready (ai_init_IframeTracker);

// ***
//})(jQuery);

// ***
//}));


ai_tracking_finished = false;

// ***
//jQuery(document).ready(function($) {
function ai_tracking () {

//  var ai_internal_tracking = AI_INTERNAL_TRACKING;
//  var ai_external_tracking = AI_EXTERNAL_TRACKING;

//  var ai_external_tracking_category  = "AI_EXT_CATEGORY";
//  var ai_external_tracking_action    = "AI_EXT_ACTION";
//  var ai_external_tracking_label     = "AI_EXT_LABEL";
//  var ai_external_tracking_username  = "WP_USERNAME";

//  var ai_track_pageviews = AI_TRACK_PAGEVIEWS;
//  var ai_advanced_click_detection = AI_ADVANCED_CLICK_DETECTION;
//  var ai_viewport_widths = AI_VIEWPORT_WIDTHS;
//  var ai_viewport_indexes = AI_VIEWPORT_INDEXES;
//  var ai_viewport_names = JSON.parse (b64d ("AI_VIEWPORT_NAMES"));
//  var ai_data_id = "AI_NONCE";
//  var ai_ajax_url = "AI_SITE_URL/wp-admin/admin-ajax.php";
//  var ai_debug_tracking = AI_DEBUG_TRACKING;

  if (ai_debug_tracking) {
    ai_ajax_url = ai_ajax_url + '?ai-debug-tracking=1';
  }

  Number.isInteger = Number.isInteger || function (value) {
    return typeof value === "number" &&
           isFinite (value) &&
           Math.floor (value) === value;
  };

  function replace_tags (text, event, block, block_name, block_counter, version, version_name) {
    text = text.replace ('[EVENT]',                 event);
    text = text.replace ('[BLOCK_NUMBER]',          block);
    text = text.replace ('[BLOCK_NAME]',            block_name);
    text = text.replace ('[BLOCK_COUNTER]',         block_counter);
    text = text.replace ('[VERSION_NUMBER]',        version);
    text = text.replace ('[VERSION_NAME]',          version_name);
    text = text.replace ('[BLOCK_VERSION_NUMBER]',  block + (version == 0 ? '' : ' - ' + version));
    text = text.replace ('[BLOCK_VERSION_NAME]',    block_name + (version_name == '' ? '' : ' - ' + version_name));
    text = text.replace ('[WP_USERNAME]',           ai_external_tracking_username);

    return (text);
  }

  function external_tracking (event, block, block_name, block_counter, version, version_name, non_interaction) {

    var category = replace_tags (ai_external_tracking_category, event, block, block_name, block_counter, version, version_name);
    var action   = replace_tags (ai_external_tracking_action,   event, block, block_name, block_counter, version, version_name);
    var label    = replace_tags (ai_external_tracking_label,    event, block, block_name, block_counter, version, version_name);

    var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//    var ai_debug = false;

    if (ai_debug) console.log ("AI TRACKING EXTERNAL", event, block, '["' + category + '", "' + action + '", "' + label + '"]');

    if (typeof ai_external_tracking_event == 'function') {
      if (ai_debug) console.log ('AI TRACKING ai_external_tracking_event (' + block + ', ' + event + ', ' + category + ', ' + action + ', ' + label + ', ' + non_interaction + ')');

      var event_data = {'event': event, 'block': block, 'block_name': block_name, 'block_counter': block_counter, 'version': version, 'version_name': version_name};

      var result = ai_external_tracking_event (event_data, category, action, label, non_interaction);

      if (ai_debug) console.log ('AI TRACKING ai_external_tracking_event ():', result);

      if (result == 0) return;
    }

//        Google Analytics
    if (typeof window.ga == 'function') {
      var ga_command = 'send';

      if (typeof ai_ga_tracker_name == 'string') {
        ga_command = ai_ga_tracker_name + '.' + ga_command;

        if (ai_debug) console.log ("AI TRACKING ai_ga_tracker_name:", ai_ga_tracker_name);
      } else {
          var trackers = ga.getAll();

          if (trackers.length != 0) {
            var tracker_name = trackers [0].get ('name');
            if (tracker_name != 't0') {
              ga_command = tracker_name + '.' + ga_command;

              if (ai_debug) console.log ("AI TRACKING ga tracker name:", tracker_name);
            }
          } else {
              if (ai_debug) console.log ("AI TRACKING no ga tracker");
            }
        }

      ga (ga_command, 'event', {
        eventCategory: category,
        eventAction: action,
        eventLabel: label,
        nonInteraction: non_interaction
      });

      if (ai_debug) console.log ("AI TRACKING Google Universal Analytics:", non_interaction);
    }
//    else

    if (typeof window.gtag == 'function') {
      gtag ('event', 'impression', {
        'event_category': category,
        'event_action': action,
        'event_label': label,
        'non_interaction': non_interaction
      });

      if (ai_debug) console.log ("AI TRACKING Global Site Tag:", non_interaction);
    }
//    else

    if (typeof window.__gaTracker == 'function') {
      __gaTracker ('send', 'event', {
        eventCategory: category,
        eventAction: action,
        eventLabel: label,
        nonInteraction: non_interaction
      });

      if (ai_debug) console.log ("AI TRACKING Google Universal Analytics by MonsterInsights:", non_interaction);
    }
//    else

    if (typeof _gaq == 'object') {
//      _gaq.push (['_trackEvent', category, action, label]);
      _gaq.push (['_trackEvent', category, action, label, undefined, non_interaction]);

      if (ai_debug) console.log ("AI TRACKING Google Legacy Analytics:", non_interaction);
    }

//        Matomo (Piwik)
    if (typeof _paq == 'object') {
      _paq.push (['trackEvent', category, action, label]);

      if (ai_debug) console.log ("AI TRACKING Matomo");
    }
  }

  function ai_click (data, click_type) {

    var ai_debug = typeof ai_debugging !== 'undefined'; //2
//    var ai_debug = false;

    var block         = data [0];
    var code_version  = data [1];

    if (Number.isInteger (code_version)) {

      if (typeof ai_check_data == 'undefined' && typeof ai_check_data_timeout == 'undefined') {
        if (ai_debug) console.log ('AI CHECK CLICK - DATA NOT SET YET');

        ai_check_data_timeout = true;
        setTimeout (function() {if (ai_debug) console.log (''); if (ai_debug) console.log ('AI CHECK CLICK TIMEOUT'); ai_click (data, click_type);}, 2500);
        return;
      }

      if (ai_debug) console.log ('AI CHECK CLICK block', block);
      if (ai_debug) console.log ('AI CHECK CLICK data', ai_check_data);

      ai_cookie = ai_load_cookie ();

      for (var cookie_block in ai_cookie) {

        if (parseInt (block) != parseInt (cookie_block)) continue;

        for (var cookie_block_property in ai_cookie [cookie_block]) {
          if (cookie_block_property == 'c') {
            if (ai_debug) console.log ('AI CHECK CLICKS block:', cookie_block);

            var clicks = ai_cookie [cookie_block][cookie_block_property];
            if (clicks > 0) {
              if (ai_debug) console.log ('AI CLICK, block', cookie_block, 'remaining', clicks - 1, 'clicks');

              ai_set_cookie (cookie_block, 'c', clicks - 1);

              if (clicks == 1) {
                if (ai_debug) console.log ('AI CLICKS #1, closing block', block, '- no more clicks');

                // ***
//                var cfp_time = $('span[data-ai-block=' + block + ']').data ('ai-cfp-time');
                var cfp_time = document.querySelector ('span[data-ai-block="' + block + '"]').dataset.aiCfpTime;
                var date = new Date();
                var timestamp = Math.round (date.getTime() / 1000);

                var closed_until = timestamp + 7 * 24 * 3600;
                ai_set_cookie (cookie_block, 'c', - closed_until);

                // ***
//                setTimeout (function() {$('span[data-ai-block=' + block + ']').closest ("div[data-ai]").remove ();}, 50);
                setTimeout (function() {
                  document.querySelectorAll ('span[data-ai-block="' + block + '"]').forEach ((el, index) => {
                    var closest = el.closest ("div[data-ai]");
                    if (closest) {
                      closest.remove ();
                    }
                  });
                }, 50);
              } else ai_set_cookie (cookie_block, 'c', clicks - 1);
            }
          } else

          if (cookie_block_property == 'cpt') {
            if (ai_debug) console.log ('AI CHECK CLICKS PER TIME PERIOD block:', cookie_block);

            var clicks = ai_cookie [cookie_block][cookie_block_property];
            if (clicks > 0) {
              if (ai_debug) console.log ('AI CLICKS, block', cookie_block, 'remaining', clicks - 1, 'clicks per time period');

              ai_set_cookie (cookie_block, 'cpt', clicks - 1);

              if (clicks == 1) {
                if (ai_debug) console.log ('AI CLICKS, closing block', block, '- no more clicks per time period');

                // ***
//                var cfp_time = $('span[data-ai-block=' + block + ']').data ('ai-cfp-time');
                var cfp_time = document.querySelector ('span[data-ai-block="' + block + '"]').dataset.aiCfpTime;

                var date = new Date();
                var timestamp = Math.round (date.getTime() / 1000);

                var closed_until = ai_cookie [cookie_block]['ct'];
                ai_set_cookie (cookie_block, 'x', closed_until);

                if (ai_debug) console.log ('AI CLICKS, closing block', block, 'for', closed_until - timestamp, 's');

                // ***
//                var block_to_close = $('span[data-ai-block=' + block + ']').closest ("div[data-ai]");
//                setTimeout (function() {
//                  block_to_close.closest ("div[data-ai]").remove ();
//                }, 75); // Remove after CFP check
                setTimeout (function() {
                  document.querySelectorAll ('span[data-ai-block="' + block + '"]').forEach ((el, index) => {
                    var closest = el.closest ("div[data-ai]");
                    if (closest) {
                      closest.remove ();
                    }
                  });
                }, 75); // After CFP is processed

                if (typeof cfp_time != 'undefined') {
                  if (ai_debug) console.log ('AI CLICKS CFP, closing block', block, 'for', cfp_time, 'days');

                  var closed_until = timestamp + cfp_time * 24 * 3600;

//                  if (ai_debug) console.log ('AI COOKIE x 3 block', block, 'closed_until', closed_until);
                  ai_set_cookie (block, 'x', closed_until);

                  // ***
//                  $('span.ai-cfp').each (function (index) {
                  document.querySelectorAll ('span.ai-cfp').forEach ((el, index) => {
                    // ***
//                    var cfp_block = $(this).data ('ai-block');
                    var cfp_block = el.dataset.aiBlock;

                    if (ai_debug) console.log ('AI CLICKS CFP, closing block', cfp_block, 'for', cfp_time, 'days');

                    // ***
//                    var block_to_close = $(this);
                    var block_to_close = el;

                    setTimeout (function() {
//                      block_to_close.closest ("div[data-ai]").remove ();
                      var closest = block_to_close.closest ("div[data-ai]");
                      if (closest) {
                        closest.remove ();
                      }
                    }, 50);

//                  if (ai_debug) console.log ('AI COOKIE x 4 block', cfp_block, 'closed_until', closed_until);
                    ai_set_cookie (cfp_block, 'x', closed_until);
                  });
                }
              }
            } else {
                if (ai_check_data.hasOwnProperty (cookie_block) && ai_check_data [cookie_block].hasOwnProperty ('cpt') && ai_check_data [cookie_block].hasOwnProperty ('ct')) {
                  if (ai_cookie.hasOwnProperty (cookie_block) && ai_cookie [cookie_block].hasOwnProperty ('ct')) {
                    var date = new Date();
                    var closed_for = ai_cookie [cookie_block]['ct'] - Math.round (date.getTime() / 1000);
                    if (closed_for <= 0) {
                      if (ai_debug) console.log ('AI CLICKS, block', cookie_block, 'set max clicks period (', ai_check_data [cookie_block]['ct'], 'days =', ai_check_data [cookie_block]['ct'] * 24 * 3600, 's)');

                      var timestamp = Math.round (date.getTime() / 1000);

                      ai_set_cookie (cookie_block, 'cpt', ai_check_data [cookie_block]['cpt'] - 1);
                      ai_set_cookie (cookie_block, 'ct', Math.round (timestamp + ai_check_data [cookie_block]['ct'] * 24 * 3600));
                    }
                  }
                } else {
                    if (ai_cookie.hasOwnProperty (cookie_block) && ai_cookie [cookie_block].hasOwnProperty ('cpt')) {
                      if (ai_debug) console.log ('AI CLICKS, block', cookie_block, 'removing cpt');

                      ai_set_cookie (cookie_block, 'cpt', '');
                    }
                    if (ai_cookie.hasOwnProperty (cookie_block) && ai_cookie [cookie_block].hasOwnProperty ('ct')) {
                      if (ai_debug) console.log ('AI CLICKS, block', cookie_block, 'removing ct');

                      ai_set_cookie (cookie_block, 'ct', '');
                    }
                  }
              }
          }
        }
      }

      if (ai_cookie.hasOwnProperty ('G') && ai_cookie ['G'].hasOwnProperty ('cpt')) {
        if (ai_debug) console.log ('AI CHECK GLOBAL CLICKS PER TIME PERIOD');

        var clicks = ai_cookie ['G']['cpt'];
        if (clicks > 0) {
          if (ai_debug) console.log ('AI CLICKS, GLOBAL remaining', clicks - 1, 'clicks per time period');

          ai_set_cookie ('G', 'cpt', clicks - 1);

          if (clicks == 1) {
            if (ai_debug) console.log ('AI CLICKS, closing block', block, '- no more global clicks per time period');

            // ***
//            var cfp_time = $('span[data-ai-block=' + block + ']').data ('ai-cfp-time');
            var cfp_time = document.querySelector ('span[data-ai-block="' + block + '"]').dataset.aiCfpTime;
            var date = new Date();
            var timestamp = Math.round (date.getTime() / 1000);

            var closed_until = ai_cookie ['G']['ct'];
            ai_set_cookie (block, 'x', closed_until);

            if (ai_debug) console.log ('AI CLICKS, closing block', block, 'for', closed_until - timestamp, 's');

            // ***
//            var block_to_close = $('span[data-ai-block=' + block + ']').closest ("div[data-ai]");
            setTimeout (function() {
              document.querySelectorAll ('span[data-ai-block="' + block + '"]').forEach ((el, index) => {
                var closest = el.closest ("div[data-ai]");
                if (closest) {
                  closest.remove ();
                }
              });
            }, 75); // After CFP is processed

            if (ai_debug) console.log ('AI CLICKS GLOBAL block', block, 'cfp_time', cfp_time);

            if (typeof cfp_time != 'undefined') {
              if (ai_debug) console.log ('AI CLICKS GLOBAL CFP, closing block', block, 'for', cfp_time, 'days');

              var closed_until = timestamp + cfp_time * 24 * 3600;

//                if (ai_debug) console.log ('AI COOKIE x 3 block', block, 'closed_until', closed_until);
              ai_set_cookie (block, 'x', closed_until);

              // ***
//              $('span.ai-cfp').each (function (index) {
              document.querySelectorAll ('span.ai-cfp').forEach ((el, index) => {
                // ***
//                var cfp_block = $(this).data ('ai-block');
                var cfp_block = el.dataset.aiBlock;
                if (ai_debug) console.log ('AI CLICKS GLOBAL CFP, closing block', cfp_block, 'for', cfp_time, 'days');

                // ***
//                var block_to_close = $(this);
                var block_to_close = el;
                setTimeout (function() {
                  block_to_close.closest ("div[data-ai]").remove ();
                }, 50);

//                if (ai_debug) console.log ('AI COOKIE x 4 block', cfp_block, 'closed_until', closed_until);
                ai_set_cookie (cfp_block, 'x', closed_until);
              });
            }
          }
        } else {
            if (ai_check_data.hasOwnProperty ('G') && ai_check_data ['G'].hasOwnProperty ('cpt') && ai_check_data ['G'].hasOwnProperty ('ct')) {
              if (ai_cookie.hasOwnProperty ('G') && ai_cookie ['G'].hasOwnProperty ('ct')) {
                var date = new Date();
                var closed_for = ai_cookie ['G']['ct'] - Math.round (date.getTime() / 1000);
                if (closed_for <= 0) {
                  if (ai_debug) console.log ('AI CLICKS GLOBAL set max clicks period (', ai_check_data ['G']['ct'], 'days =', ai_check_data ['G']['ct'] * 24 * 3600, 's)');

                  var timestamp = Math.round (date.getTime() / 1000);

                  ai_set_cookie ('G', 'cpt', ai_check_data ['G']['cpt'] - 1);
                  ai_set_cookie ('G', 'ct', Math.round (timestamp + ai_check_data ['G']['ct'] * 24 * 3600));
                }
              }
            } else {
                if (ai_cookie.hasOwnProperty ('G') && ai_cookie ['G'].hasOwnProperty ('cpt')) {
                  if (ai_debug) console.log ('AI CLICKS GLOBAL removing cpt');

                  ai_set_cookie ('G', 'cpt', '');
                }
                if (ai_cookie.hasOwnProperty ('G') && ai_cookie ['G'].hasOwnProperty ('ct')) {
                  if (ai_debug) console.log ('AI CLICKS GLOBAL removing ct');

                  ai_set_cookie ('G', 'ct', '');
                }
              }
          }
      }


      if (ai_debug) console.log ("AI CLICK: ", data, click_type);

      if (ai_internal_tracking) {
        if (typeof ai_internal_tracking_no_clicks === 'undefined') {
              // ***
//          $.ajax ({
//              url: ai_ajax_url,
//              type: "post",
//              data: {
//                action: "ai_ajax",
//                ai_check: ai_data_id,
//                click: block,
//                version: code_version,
//                type: click_type,
//              },
//              async: true
//          }).done (function (data) {

          var url_data = {
            action: "ai_ajax",
            ai_check: ai_data_id,
            click: block,
            version: code_version,
            type: click_type,
          };

          var formBody = [];
          for (var property in url_data) {
            var encodedKey = encodeURIComponent (property);
            var encodedValue = encodeURIComponent (url_data [property]);
            formBody.push (encodedKey + "=" + encodedValue);
          }
          formBody = formBody.join ("&");

          async function ai_post_clicks () {
            const response = await fetch (ai_ajax_url, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
              },
              body: formBody
            });

            const text = await response.text ();

            return text;
          }

          ai_post_clicks ().then (data => {

              data = data.trim ();
              if (data != "") {
                var db_records = JSON.parse (data);

                if (ai_debug) {
                  console.log ("AI DB RECORDS: ", db_records);
                }

                if (typeof db_records ['#'] != 'undefined' && db_records ['#'] == block) {
                  // Reload cookie data
                  ai_cookie = ai_load_cookie ();

                  var date = new Date();
                  var closed_until = Math.round (date.getTime() / 1000) + 12 * 3600;

                  if (ai_debug) console.log ("AI SERVERSIDE LIMITED BLOCK:", block);

                  if (!ai_cookie.hasOwnProperty (block) || !ai_cookie [block].hasOwnProperty ('x')) {
                    if (ai_debug) console.log ("AI SERVERSIDE LIMITED BLOCK:", block, ' not closed - closing for 12 hours');

                    ai_set_cookie (block, 'x', closed_until);
                  }

//                  setTimeout (function() {$('span[data-ai-block=' + block + ']').closest ("div[data-ai]").remove ();}, 50);
                  // ***
                  setTimeout (function () {
                    document.querySelectorAll ('span[data-ai-block="' + block + '"]').forEach ((el, index) => {
                      var closest = el.closest ("div[data-ai]");
                      if (closest) {
                        closest.remove ();
                      }
                    });
                  }, 50);
                }

                if (ai_debug) {
                  var db_record = db_records ['='];

                  if (typeof db_record != "undefined") {
                    if (typeof db_record == "string")
                      console.log ("AI CLICK " + block, code_version == 0 ? "" : "[" + code_version + "]", "(" + db_record + ")"); else
                        console.log ("AI CLICK " + block, code_version == 0 ? "" : "[" + code_version + "]", "(Views: " + db_record [4] + ", Clicks: " + db_record [5] + (click_type == "" ? "" : ", " + click_type) + ")");
                  }
                }
              } else if (ai_debug) console.log ("AI CLICK " + block, code_version == 0 ? "" : "[" + code_version + "]", "(NO DATA" + (click_type == "" ? "" : ", " + click_type) + ")");

              if (ai_debug) console.log ('');
          });
        } else {
            if (ai_debug) console.log ("AI CLICK INTERNAL TRACKING DISABLED");
          }
      }

      if (ai_external_tracking) {
        if (typeof ai_external_tracking_no_clicks === 'undefined') {
          var block_name         = data [2];
          var code_version_name  = data [3];
          var block_counter      = data [4];

          external_tracking ("click", block, block_name, block_counter, code_version, code_version_name, false);
        } else {
            if (ai_debug) console.log ("AI CLICK EXTERNAL TRACKING DISABLED");
          }
      }

      if (typeof ai_click_action == 'function') {
        if (ai_debug) console.log ('AI CLICK ai_click_action (' + block + ') CALLED');

        ai_click_action (block, block_name, code_version, code_version_name);
      }
    }
  }

  ai_install_standard_click_trackers = function (block_wrapper) {
    var ai_debug = typeof ai_debugging !== 'undefined'; // 3
//    var ai_debug = false;

    if (typeof block_wrapper == 'undefined') {
      // ***
//      block_wrapper = $('body');
      block_wrapper = document.querySelector ('body');
    }

//    var elements = $("div.ai-track[data-ai]:visible a", block_wrapper);
    // ***
//    var elements = $("div.ai-track[data-ai]:visible", block_wrapper);
    var elements = block_wrapper.querySelectorAll ("div.ai-track[data-ai]");

    // ***
//    var filtered_elements = $();
    var filtered_elements = [];
    // ***
//    elements.each (function () {
    elements.forEach ((element, i) => {
      if (!!(element.offsetWidth || element.offsetHeight || element.getClientRects ().length)) {
      // ### Excludes element also when class is found in rotation option
//      var ai_lazy_loading = $(this).find ('div.ai-lazy');
//      var ai_manual_loading = $(this).find ('div.ai-manual');
//      var ai_manual_loading_list = $(this).find ('div.ai-list-manual');
//      var ai_manual_loading_auto = $(this).find ('div.ai-manual-auto');
//      if (ai_lazy_loading.length == 0 && ai_manual_loading.length == 0 && ai_manual_loading_list.length == 0 && ai_manual_loading_auto.length == 0) filtered_elements = filtered_elements.add ($(this));

      // ***
//      if ($(this).find ('div.ai-lazy, div.ai-manual, div.ai-list-manual, div.ai-manual-auto, div.ai-delayed').length == 0) filtered_elements = filtered_elements.add ($(this));
//      if (!element.querySelectorAll ('div.ai-lazy, div.ai-manual, div.ai-list-manual, div.ai-manual-auto, div.ai-delayed').length) filtered_elements.push (element);
      if (!element.querySelectorAll ('div.ai-lazy, div.ai-wait-for-interaction, div.ai-manual, div.ai-list-manual, div.ai-manual-auto, div.ai-delayed').length) filtered_elements.push (element);
      // ***
      }
    });

    elements = filtered_elements;


    // Mark as tracked
    // ***
//    elements.removeClass ('ai-track');
//    elements = elements.find ('a');
    var processed_elements = [];
    elements.forEach ((element, i) => {
      element.classList.remove ('ai-track');
      processed_elements.push.apply (processed_elements, element.querySelectorAll ('a'));
    });

    // ***
    elements = processed_elements;

    if (elements.length != 0) {
      if (ai_advanced_click_detection) {
        // ***
//        elements.click (function () {
        elements.forEach ((element, i) => {
          element.addEventListener ('click', () => {
            // ***
  //          var wrapper = $(this).closest ("div[data-ai]");
            var wrapper = element.closest ("div[data-ai]");
            // ***
  //          while (typeof wrapper.attr ("data-ai") != "undefined") {
            while (wrapper !== null && wrapper.hasAttribute ("data-ai")) {
              // ***
  //            var data = JSON.parse (b64d (wrapper.attr ("data-ai")));
              var data = JSON.parse (b64d (wrapper.getAttribute ("data-ai")));
              if (typeof data !== "undefined" && data.constructor === Array) {
                if (Number.isInteger (data [1])) {
                  // ***
  //                if (!wrapper.hasClass ("clicked")) {
                  if (!wrapper.classList.contains ("clicked")) {
                    // ***
  //                  wrapper.addClass ("clicked");
                    wrapper.classList.add ("clicked");
                    ai_click (data, "a.click");
                  }
                }
              }
              // ***
  //            wrapper = wrapper.parent ().closest ("div[data-ai]");
              wrapper = wrapper.parentElement.closest ("div[data-ai]");
            }
          });
        // ***
        });

        if (ai_debug) {
          // ***
//          elements.each (function (){
          elements.forEach ((element, i) => {
            // ***
//            var wrapper = $(this).closest ("div[data-ai]");
            var wrapper = element.closest ("div[data-ai]");
            // ***
//            if (typeof wrapper.attr ("data-ai") != "undefined") {
            if (wrapper !== null && wrapper.hasAttribute ("data-ai")) {
              // ***
//              var data = JSON.parse (b64d (wrapper.data ("ai")));
              var data = JSON.parse (b64d (wrapper.dataset.ai));
              if (typeof data !== "undefined" && data.constructor === Array) {
                if (Number.isInteger (data [1])) {
                  // ***
//                  if (!wrapper.hasClass ("clicked")) {
                  if (!wrapper.classList.contains ("clicked")) {
                    console.log ("AI STANDARD CLICK TRACKER for link installed on block", data [0]);
                  } else console.log ("AI STANDARD CLICK TRACKER for link NOT installed on block", data [0], "- has class clicked");
                } else console.log ("AI STANDARD CLICK TRACKER for link NOT installed on block", data [0], "- version not set");

              }
            }
          });
        }
      } else {
          // ***
//          elements.click (function () {
          elements.forEach ((element, i) => {
            element.addEventListener ('click', () => {
              // ***
  //            var wrapper = $(this).closest ("div[data-ai]");
              var wrapper = element.closest ("div[data-ai]");
              // ***
  //            while (typeof wrapper.attr ("data-ai") != "undefined") {
              while (wrapper !== null && wrapper.hasAttribute ("data-ai")) {
                // ***
  //              var data = JSON.parse (b64d (wrapper.attr ("data-ai")));
                var data = JSON.parse (b64d (wrapper.getAttribute ("data-ai")));
                if (typeof data !== "undefined" && data.constructor === Array) {
                  if (Number.isInteger (data [1])) {
                    ai_click (data, "a.click");
                    clicked = true;
                  }
                }
                // ***
  //              wrapper = wrapper.parent ().closest ("div[data-ai]");
                wrapper = wrapper.parentElement.closest ("div[data-ai]");
              }
            });
            // ***
          });

          if (ai_debug) {
            // ***
//            elements.each (function (){
            elements.forEach ((element, i) => {
              // ***
//              var wrapper = $(this).closest ("div[data-ai]");
              var wrapper = element.closest ("div[data-ai]");
              // ***
//              if (typeof wrapper.attr ("data-ai") != "undefined") {
              if (wrapper !== null && wrapper.hasAttribute ("data-ai")) {
                // ***
//                var data = JSON.parse (b64d (wrapper.attr ("data-ai")));
                var data = JSON.parse (b64d (wrapper.getAttribute ("data-ai")));

                if (typeof data !== "undefined" && data.constructor === Array) {
                  if (Number.isInteger (data [1])) {
                    console.log ("AI STANDARD CLICK TRACKER installed on block", data [0]);
                  } else console.log ("AI STANDARD CLICK TRACKER NOT installed on block", data [0], "- version not set");

                }
              }
            });
          }
        }
    }
  }

  ai_install_click_trackers = function (block_wrapper) {

    var ai_debug = typeof ai_debugging !== 'undefined'; // 4
//    var ai_debug = false;

    if (typeof block_wrapper == 'undefined') {
      // ***
//      block_wrapper = $('body');
      block_wrapper = document.querySelector ('body');
      if (ai_debug) console.log ("AI INSTALL CLICK TRACKERS");
    // ***
//    }  else if (ai_debug) console.log ("AI INSTALL CLICK TRACKERS:", block_wrapper.prop ("tagName"), block_wrapper.attr ('class'));
    }  else if (ai_debug) console.log ("AI INSTALL CLICK TRACKERS:", block_wrapper.tagName, block_wrapper.getAttribute ('class'));


    if (ai_advanced_click_detection) {
                                                       // timed rotation options that may contain blocks for tracking (block shortcodes) - only currently active option is visible
      // ***
//      var elements = $("div.ai-track[data-ai]:visible, div.ai-rotate[data-info]:visible div.ai-track[data-ai]", block_wrapper);
      var elements = block_wrapper.querySelectorAll ("div.ai-track[data-ai], div.ai-rotate[data-info] div.ai-track[data-ai]");

      var all_elements = [];
      elements.forEach ((element, i) => {
        // Install iframe click tracker only for visible blocks
        if (!!(element.offsetWidth || element.offsetHeight || element.getClientRects ().length)) {
          all_elements.push (element);
        }
      });

      // ***
//      if (typeof block_wrapper.attr ("data-ai") != "undefined" && $(block_wrapper).hasClass ('ai-track') && $(block_wrapper).is (':visible')) {
      if (block_wrapper.hasAttribute ("data-ai") && block_wrapper.classList.contains ('ai-track') && !!(block_wrapper.offsetWidth || block_wrapper.offsetHeight || block_wrapper.getClientRects ().length)) {
        // ***
//        elements = elements.add (block_wrapper);
        all_elements.push (block_wrapper);
      }

      // ***
//      var filtered_elements = $();
      var filtered_elements = [];
//      elements.each (function () {
      all_elements.forEach ((element, i) => {

        // ### Excludes element also when class is found in rotation option
//        var ai_lazy_loading = $(this).find ('div.ai-lazy');
//        var ai_manual_loading = $(this).find ('div.ai-manual');
//        var ai_manual_loading_auto = $(this).find ('div.ai-manual-auto');
//        if (ai_lazy_loading.length == 0 && ai_manual_loading.length == 0 && ai_manual_loading_auto.length == 0) filtered_elements = filtered_elements.add ($(this));

        // ***
//        if ($(this).find ('div.ai-lazy, div.ai-manual, div.ai-list-manual, div.ai-manual-auto, div.ai-delayed').length == 0) filtered_elements = filtered_elements.add ($(this));
//        if (!element.querySelectorAll ('div.ai-lazy, div.ai-manual, div.ai-list-manual, div.ai-manual-auto, div.ai-delayed').length) filtered_elements.push (element);
        if (!element.querySelectorAll ('div.ai-lazy, div.ai-wait-for-interaction, div.ai-manual, div.ai-list-manual, div.ai-manual-auto, div.ai-delayed').length) filtered_elements.push (element);
      });

      elements = filtered_elements;

    // Mark as tracked - prevents ai_install_standard_click_trackers
      // ***
//      elements.removeClass ('ai-track');

//      var processed_elements = [];
//      elements.forEach ((element, i) => {
//        element.classList.remove ('ai-track');
//        processed_elements.push (processed_elements);
//      });
//      elements = processed_elements;

    // Will be marked in ai_install_standard_click_trackers

      if (elements.length != 0) {
        // ***
//        elements.iframeTracker ({
        elements.forEach ((element, i) => {
          installIframeTracker ({

          blurCallback: function(){
            if (this.ai_data != null && wrapper != null) {
              if (ai_debug) console.log ("AI blurCallback for block: " + this.ai_data [0]);
              // ***
//              if (!wrapper.hasClass ("clicked")) {
              if (!wrapper.classList.contains ("clicked")) {
                // ***
//                wrapper.addClass ("clicked");
                wrapper.classList.add ("clicked");
                ai_click (this.ai_data, "blurCallback");

                // ***
//                var inner_wrapper = wrapper.find ("div[data-ai]:visible");
                var inner_wrapper = wrapper.querySelector ("div[data-ai]");
                // ***
//                while (typeof inner_wrapper.attr ("data-ai") != "undefined") {
                while (inner_wrapper != null && !!(inner_wrapper.offsetWidth || inner_wrapper.offsetHeight || inner_wrapper.getClientRects ().length) && inner_wrapper.hasAttribute ("data-ai")) {
                  // ***
//                  var data = JSON.parse (b64d (inner_wrapper.attr ("data-ai")));
                  var data = JSON.parse (b64d (inner_wrapper.getAttribute ("data-ai")));
                  if (typeof data !== "undefined" && data.constructor === Array && Number.isInteger (data [1])) {
                    ai_click (data, "blurCallback INNER");
                  }
                  // ***
//                  inner_wrapper = inner_wrapper.find ("div[data-ai]:visible");
                  inner_wrapper = inner_wrapper.querySelector ("div[data-ai]");
                }
              }
            }
          },
          overCallback: function(element){
            // ***
//            var closest = $(element).closest ("div[data-ai]");
            var closest = element.closest ("div[data-ai]");
            // ***
//            if (typeof closest.attr ("data-ai") != "undefined") {
            if (closest.hasAttribute ("data-ai")) {
              // ***
//              var data = JSON.parse (b64d (closest.attr ("data-ai")));
              var data = JSON.parse (b64d (closest.getAttribute ("data-ai")));
              if (typeof data !== "undefined" && data.constructor === Array && Number.isInteger (data [1])) {
                wrapper = closest;
                this.ai_data = data;
                if (ai_debug) console.log ("AI overCallback for block: " + this.ai_data [0]);
              } else {
                  // ***
//                  if (wrapper != null) wrapper.removeClass ("clicked");
                  if (wrapper != null) wrapper.classList.remove ("clicked");
                  wrapper        = null;
                  this.ai_data  = null;
                }
            }
          },
          outCallback: function (element){
            if (ai_debug && this.ai_data != null) console.log ("AI outCallback for block: " + this.ai_data [0]);
            // ***
//            if (wrapper != null) wrapper.removeClass ("clicked");
            if (wrapper != null) wrapper.classList.remove ("clicked");
            wrapper = null;
            this.ai_data = null;
          },
          focusCallback: function(element){
            if (this.ai_data != null && wrapper != null) {
              if (ai_debug) console.log ("AI focusCallback for block: " + this.ai_data [0]);
              // ***
//              if (!wrapper.hasClass ("clicked")) {
              if (!wrapper.classList.contains ("clicked")) {
                // ***
//                wrapper.addClass ("clicked");
                wrapper.classList.add ("clicked");
                ai_click (this.ai_data, "focusCallback");

//                var inner_wrapper = wrapper.find ("div[data-ai]:visible");
                var inner_wrapper = wrapper.querySelector ("div[data-ai]");

                // ***
//                while (typeof inner_wrapper.attr ("data-ai") != "undefined") {
                while (inner_wrapper != null && !!(inner_wrapper.offsetWidth || inner_wrapper.offsetHeight || inner_wrapper.getClientRects ().length) && inner_wrapper.hasAttribute ("data-ai")) {
                  // ***
//                  var data = JSON.parse (b64d (inner_wrapper.attr ("data-ai")));
                  var data = JSON.parse (b64d (inner_wrapper.getAttribute ("data-ai")));
                  if (typeof data !== "undefined" && data.constructor === Array && Number.isInteger (data [1])) {
                    ai_click (data, "focusCallback INNER");
                  }
                  // ***
//                  inner_wrapper = inner_wrapper.find ("div[data-ai]:visible");
                  inner_wrapper = inner_wrapper.querySelector ("div[data-ai]");
                }
              }
            }
          },
          wrapper:  null,
          ai_data: null,
          block:   null,
          version: null
        // ***
//        });
        }
        , element
        );
        // ***
        });

        if (ai_debug) {
          // ***
//          elements.each (function (){
          elements.forEach ((element, i) => {
            // ***
//            var closest = $(this).closest ("div[data-ai]");
            var closest = element.closest ("div[data-ai]");
            // ***
//            if (typeof closest.attr ("data-ai") != "undefined") {
            if (closest.hasAttribute ("data-ai")) {
            // ***
//              var data = JSON.parse (b64d (closest.attr ("data-ai")));
              var data = JSON.parse (b64d (closest.getAttribute ("data-ai")));
              if (typeof data !== "undefined" && data.constructor === Array) {
                console.log ("AI ADVANCED CLICK TRACKER installed on block", data [0]);
              }
            }
          });
        }
      }
    }


    ai_install_standard_click_trackers (block_wrapper);
  }

  var pageview_data = [];

  ai_process_impressions = function (block_wrapper) {

    var ai_debug = typeof ai_debugging !== 'undefined'; // 5
//    var ai_debug = false;

    if (typeof block_wrapper == 'undefined') {
      // ***
//      block_wrapper = $('body');
      block_wrapper = document.querySelector ('body');
      if (ai_debug) console.log ("AI PROCESS IMPRESSIONS");
    // ***
//    }  else if (ai_debug) console.log ("AI PROCESS IMPRESSIONS:", block_wrapper.prop ("tagName"), block_wrapper.attr ('class'));
    } else if (ai_debug) console.log ("AI PROCESS IMPRESSIONS:", block_wrapper.tagName, block_wrapper.hasAttribute ('class') ? block_wrapper.getAttribute ('class') : '');

    var blocks = [];
    var versions = [];
    var block_names = [];
    var version_names = [];
    var block_counters = [];

    if (pageview_data.length != 0) {
      if (ai_debug) console.log ('AI PROCESS IMPRESSIONS - SENDING ALSO PAGEVIEW DATA', pageview_data);

      blocks.push          (pageview_data [0]);
      versions.push        (pageview_data [1]);
      block_names.push     ('Pageviews');
      block_counters.push  (0);
      version_names.push   ('');
    }

                                                                // timed rotation options that may contain blocks for tracking (block shortcodes) - only currently active option is visible
    // ***
//    var blocks_for_tracking = $("div.ai-track[data-ai]:visible, div.ai-rotate[data-info]:visible div.ai-track[data-ai]", block_wrapper);
    var blocks_for_tracking = block_wrapper.querySelectorAll ("div.ai-track[data-ai], div.ai-rotate[data-info] div.ai-track[data-ai]");
    var visible_elements = [];
    blocks_for_tracking.forEach ((element, i) => {
      if (!!(element.offsetWidth || element.offsetHeight || element.getClientRects ().length) && !element.classList.contains ('ai-no-pageview')) {
        visible_elements.push (element);
      }
    });

    // ***
//    if (typeof $(block_wrapper).attr ("data-ai") != "undefined" && $(block_wrapper).hasClass ('ai-track') && $(block_wrapper).is (':visible')) {
    if (block_wrapper !== null && block_wrapper.hasAttribute ("data-ai") && block_wrapper.classList.contains ('ai-track') && !block_wrapper.classList.contains ('ai-no-pageview') && !!(block_wrapper.offsetWidth || block_wrapper.offsetHeight || block_wrapper.getClientRects ().length)) {
      visible_elements.push (block_wrapper);
    }
    blocks_for_tracking = visible_elements;;

    // ***
//    if (ai_debug) console.log ("AI BLOCKS FOR TRACKING:", blocks_for_tracking.each (function () {return $(this).attr ('class')}).get ());
    if (ai_debug) {
      console.log ("AI BLOCKS FOR TRACKING:");
      blocks_for_tracking.forEach ((element, i) => {console.log ('  ', element.getAttribute ('class'))});
    }

    if (blocks_for_tracking.length != 0) {
      if (ai_debug) console.log ("");

      // ***
//      $(blocks_for_tracking).each (function (){
      blocks_for_tracking.forEach ((element, i) => {

        // ***
//        if (typeof $(this).attr ("data-ai") != "undefined") {
        if (element.hasAttribute ("data-ai")) {


          // Check for fallback tracking
          var new_tracking_data = '';

          if (ai_debug && element.hasAttribute ('data-ai-1')) console.log ('AI TRACKING CHECKING BLOCK', element.getAttribute ('class'));

          for (var fallback_level = 1; fallback_level <= 9; fallback_level ++) {
            if (element.hasAttribute ('data-ai-' + fallback_level)) {
              new_tracking_data = element.getAttribute ('data-ai-' + fallback_level);

              if (ai_debug) console.log ('  FALLBACK LEVEL', fallback_level);
            } else break;
          }

          if (new_tracking_data != '') {
            element.setAttribute ('data-ai', new_tracking_data);
            if (ai_debug) console.log ('  TRACKING DATA UPDATED TO', b64d (element.getAttribute ('data-ai')));
          }

          // ***
//          var data = JSON.parse (b64d ($(this).attr ("data-ai")));
          var data = JSON.parse (b64d (element.getAttribute ("data-ai")));

          if (typeof data !== "undefined" && data.constructor === Array) {
            if (ai_debug) console.log ("AI TRACKING DATA:", data);

            var timed_rotation_count = 0;
            // ***
//            var ai_rotation_info = $(this).find ('div.ai-rotate[data-info]');
            var ai_rotation_info = element.querySelectorAll ('div.ai-rotate[data-info]');
            if (ai_rotation_info.length == 1) {
              // ***
//              var block_rotation_info = JSON.parse (b64d (ai_rotation_info.data ('info')));
              var block_rotation_info = JSON.parse (b64d (ai_rotation_info [0].dataset.info));

              if (ai_debug) console.log ("AI TIMED ROTATION DATA:", block_rotation_info);

              timed_rotation_count = block_rotation_info [1];
            }

            if (Number.isInteger (data [0]) && data [0] != 0) {
              if (Number.isInteger (data [1])) {

                var adb_flag = 0;
                // Deprecated
                // ***
//                var no_tracking = $(this).hasClass ('ai-no-tracking');
                var no_tracking = element.classList.contains ('ai-no-tracking');

                // ***
//                var ai_masking_data = jQuery(b64d ("Ym9keQ==")).attr (AI_ADB_ATTR_NAME);
                var ai_masking_data = document.querySelector (b64d ("Ym9keQ==")).getAttribute (b64d (ai_adb_attribute));
                if (typeof ai_masking_data === "string") {
                  var ai_masking = ai_masking_data == b64d ("bWFzaw==");
                }

                if (typeof ai_masking_data === "string" && typeof ai_masking === "boolean") {
                  // ***
//                  var outer_height = $(this).outerHeight ();
                  var outer_height = element.offsetHeight;

                  // ***
//                  var ai_attributes = $(this).find ('.ai-attributes');
                  var ai_attributes = element.querySelectorAll ('.ai-attributes');
                  if (ai_attributes.length) {
//                    ai_attributes.each (function (){
                    // ***
                    ai_attributes.forEach ((el, i) => {
                      // ***
//                      if (outer_height >= $(this).outerHeight ()) {
                      if (outer_height >= element.offsetHeight) {
                        // ***
//                        outer_height -= $(this).outerHeight ();
                        outer_height -= element.offsetHeight;
                      }
                    });
                  }

                  // ***
//                  var ai_code = $(this).find ('.ai-code');
                  var ai_code = element.querySelectorAll ('.ai-code');
                  outer_height = 0;
                  if (ai_code.length) {
                    // ***
//                    ai_code.each (function (){
                    ai_code.forEach ((element, i) => {
                      // ***
//                      outer_height += $(this).outerHeight ();
                      outer_height += element.offsetHeight;
                    });
                  }

  //                no_tracking = $(this).hasClass ('ai-no-tracking');
                  // ***
//                  if (ai_debug) console.log ('AI ad blocking:', ai_masking, " outerHeight:", outer_height, 'no tracking:', no_tracking);
                  if (ai_debug) console.log ('AI ad blocking:', ai_masking, " offsetHeight:", outer_height, 'no tracking:', no_tracking);
                  if (ai_masking && outer_height === 0) {
                    adb_flag = 0x80;
                  }
                }

//                var ai_lazy_loading = $(this).find ('div.ai-lazy');
//                var ai_manual_loading = $(this).find ('div.ai-manual');
//                var ai_manual_loading_list = $(this).find ('div.ai-list-manual');
//                var ai_manual_loading_auto = $(this).find ('div.ai-manual-auto');

//                if (ai_lazy_loading.length != 0 || ai_manual_loading.length != 0 || ai_manual_loading_list.length != 0 || ai_manual_loading_auto.length != 0) {

                // ***
//                if ($(this).find ('div.ai-lazy, div.ai-manual, div.ai-list-manual, div.ai-manual-auto, div.ai-delayed').length != 0) {
//                if (element.querySelectorAll ('div.ai-lazy, div.ai-manual, div.ai-list-manual, div.ai-manual-auto, div.ai-delayed').length != 0) {
                if (element.querySelectorAll ('div.ai-lazy, div.ai-wait-for-interaction, div.ai-manual, div.ai-list-manual, div.ai-manual-auto, div.ai-delayed').length != 0) {
                  no_tracking = true;

                  if (ai_debug) {
                    // ***
//                    if ($(this).find ('div.ai-lazy').length   != 0) console.log ("AI TRACKING block", data [0], "is set for lazy loading");
//                    if ($(this).find ('div.ai-manual').length != 0) console.log ("AI TRACKING block", data [0], "is set for manual loading");
//                    if ($(this).find ('div.ai-list-manual').length != 0) console.log ("AI TRACKING block", data [0], "is set for manual loading AUTO list");
//                    if ($(this).find ('div.ai-manual-auto').length != 0) console.log ("AI TRACKING block", data [0], "is set for manual loading AUTO");
//                    if ($(this).find ('div.ai-delayed').length != 0) console.log ("AI TRACKING block", data [0], "is set for delayed loading");

                    if (element.querySelectorAll ('div.ai-lazy').length   != 0) console.log ("AI TRACKING block", data [0], "is set for lazy loading");
                    if (element.querySelectorAll ('div.ai-wait-for-interaction').length   != 0) console.log ("AI TRACKING block", data [0], "is waiting for interaction");
                    if (element.querySelectorAll ('div.ai-manual').length != 0) console.log ("AI TRACKING block", data [0], "is set for manual loading");
                    if (element.querySelectorAll ('div.ai-list-manual').length != 0) console.log ("AI TRACKING block", data [0], "is set for manual loading AUTO list");
                    if (element.querySelectorAll ('div.ai-manual-auto').length != 0) console.log ("AI TRACKING block", data [0], "is set for manual loading AUTO");
                    if (element.querySelectorAll ('div.ai-delayed').length != 0) console.log ("AI TRACKING block", data [0], "is set for delayed loading");
                  }
                }

                if (!no_tracking) {
                  if (timed_rotation_count == 0) {
                    blocks.push (data [0]);
                    versions.push (data [1] | adb_flag);
                    block_names.push (data [2]);
                    version_names.push (data [3]);
                    block_counters.push (data [4]);
                  } else {
                      // Timed rotation
                      for (var option = 1; option <= timed_rotation_count; option ++) {
                        blocks.push (data [0]);
                        versions.push (option | adb_flag);
                        block_names.push (data [2]);
                        version_names.push (data [3]);
                        block_counters.push (data [4]);
                      }
                    }

                } else if (ai_debug) console.log ("AI TRACKING block", data [0], "DISABLED");

              // ***
//              } else if (ai_debug) console.log ("AI TRACKING block", data [0], "- version not set", $(this).find ('div.ai-lazy').length != 0 ? 'LAZY LOADING' : '', ($(this).find ('div.ai-manual').length + $(this).find ('div.ai-list-manual').length + $(this).find ('div.ai-manual-auto').length) != 0 ? 'MANUAL LOADING' : '');
//              } else if (ai_debug) console.log ("AI TRACKING block", data [0], "- version not set", element.querySelectorAll ('div.ai-lazy').length != 0 ? 'LAZY LOADING' : '', (element.querySelectorAll ('div.ai-manual').length + element.querySelectorAll ('div.ai-list-manual').length + element.querySelectorAll ('div.ai-manual-auto').length) != 0 ? 'MANUAL LOADING' : '');
              } else if (ai_debug) console.log ("AI TRACKING block", data [0], "- version not set", element.querySelectorAll ('div.ai-lazy').length != 0 ? 'LAZY LOADING' : '', element.querySelectorAll ('div.ai-wait-for-interaction').length != 0 ? 'WAITING FOR INTERACTION' : '', (element.querySelectorAll ('div.ai-manual').length + element.querySelectorAll ('div.ai-list-manual').length + element.querySelectorAll ('div.ai-manual-auto').length) != 0 ? 'MANUAL LOADING' : '');
            } else if (ai_debug) console.log ("AI TRACKING DISABLED");
          }
        }
      });
    }

    if (ai_debug) console.log ('AI CHECK IMPRESSIONS blocks', blocks);
    if (ai_debug) console.log ('AI CHECK IMPRESSIONS data', ai_check_data);

    ai_cookie = ai_load_cookie ();

    for (var cookie_block in ai_cookie) {

      if (!blocks.includes (parseInt (cookie_block))) continue;

      for (var cookie_block_property in ai_cookie [cookie_block]) {
        if (cookie_block_property == 'i') {
          if (ai_debug) console.log ('AI CHECK IMPRESSIONS block:', cookie_block);

          var impressions = ai_cookie [cookie_block][cookie_block_property];
          if (impressions > 0) {
            if (ai_debug) console.log ('AI IMPRESSION, block', cookie_block, 'remaining', impressions - 1, 'impressions');

            if (impressions == 1) {
              var date = new Date();
                var closed_until = Math.round (date.getTime() / 1000) + 7 * 24 * 3600;
//              // TEST
//              var closed_until = Math.round (date.getTime() / 1000) + 36;
              ai_set_cookie (cookie_block, 'i', - closed_until);
            } else ai_set_cookie (cookie_block, 'i', impressions - 1);
          }
        } else

        if (cookie_block_property == 'ipt') {
          if (ai_debug) console.log ('AI CHECK IMPRESSIONS PER TIME PERIOD block:', cookie_block);

          var impressions = ai_cookie [cookie_block][cookie_block_property];
          if (impressions > 0) {
            if (ai_debug) console.log ('AI IMPRESSIONS, block', cookie_block, 'remaining', impressions - 1, 'impressions per time period');

            ai_set_cookie (cookie_block, 'ipt', impressions - 1);
          } else {
              if (ai_check_data.hasOwnProperty (cookie_block) && ai_check_data [cookie_block].hasOwnProperty ('ipt') && ai_check_data [cookie_block].hasOwnProperty ('it')) {
                if (ai_cookie.hasOwnProperty (cookie_block) && ai_cookie [cookie_block].hasOwnProperty ('it')) {
                  var date = new Date();
                  var closed_for = ai_cookie [cookie_block]['it'] - Math.round (date.getTime() / 1000);
                  if (closed_for <= 0) {
                    if (ai_debug) console.log ('AI IMPRESSIONS, block', cookie_block, 'set max impressions period (' + ai_check_data [cookie_block]['it'], 'days =', ai_check_data [cookie_block]['it'] * 24 * 3600, 's)');

                    var timestamp = Math.round (date.getTime() / 1000);

                    ai_set_cookie (cookie_block, 'ipt', ai_check_data [cookie_block]['ipt']);
                    ai_set_cookie (cookie_block, 'it', Math.round (timestamp + ai_check_data [cookie_block]['it'] * 24 * 3600));
                  }
                }
              } else {
                  if (ai_cookie.hasOwnProperty (cookie_block) && ai_cookie [cookie_block].hasOwnProperty ('ipt')) {
                    if (ai_debug) console.log ('AI IMPRESSIONS, block', cookie_block, 'removing ipt');

                    ai_set_cookie (cookie_block, 'ipt', '');
                  }
                  if (ai_cookie.hasOwnProperty (cookie_block) && ai_cookie [cookie_block].hasOwnProperty ('it')) {
                    if (ai_debug) console.log ('AI IMPRESSIONS, block', cookie_block, 'removing it');

                    ai_set_cookie (cookie_block, 'it', '');
                  }
                }
            }
        }
      }
    }

    if (blocks.length) {
      if (ai_debug) {
        console.log ("AI IMPRESSION blocks:", blocks);
        console.log ("            versions:", versions);
      }

      if (ai_internal_tracking) {
        if (typeof ai_internal_tracking_no_impressions === 'undefined') {

          // Mark as sent
          pageview_data = [];

          // ***
//          $.ajax ({
//              url: ai_ajax_url,
//              type: "post",
//              data: {
//                action: "ai_ajax",
//                ai_check: ai_data_id,
//                views: blocks,
//                versions: versions,
//              },
//              async: true
//          }).done (function (data) {

          var url_data = {
            action: "ai_ajax",
            ai_check: ai_data_id,
          };

          var formBody = [];
          for (var property in url_data) {
            var encodedKey = encodeURIComponent (property);
            var encodedValue = encodeURIComponent (url_data [property]);
            formBody.push (encodedKey + "=" + encodedValue);
          }

          for (var index in blocks) {
            var encodedKey = encodeURIComponent ('views[]');
            var encodedValue = encodeURIComponent (blocks [index]);
            formBody.push (encodedKey + "=" + encodedValue);
          }

          for (var index in versions) {
            var encodedKey = encodeURIComponent ('versions[]');
            var encodedValue = encodeURIComponent (versions [index]);
            formBody.push (encodedKey + "=" + encodedValue);
          }

          formBody = formBody.join ("&");

          async function ai_post_views () {
            const response = await fetch (ai_ajax_url, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
              },
              body: formBody
            });

            const text = await response.text ();
            return text;
          }

          ai_post_views ().then (data => {

              data = data.trim ();
              if (data != "") {
                var db_records = JSON.parse (data);

                if (ai_debug) console.log ("AI DB RECORDS: ", db_records);

                if (typeof db_records ['#'] != 'undefined') {
                  // Reload cookie data
                  ai_cookie = ai_load_cookie ();

                  var date = new Date();
                  var closed_until = Math.round (date.getTime() / 1000) + 12 * 3600;

                  var blocks_to_remove = new Array();
                  for (var limited_block_index in db_records ['#']) {
                    if (ai_debug) console.log ("AI SERVERSIDE LIMITED BLOCK:", db_records ['#'][limited_block_index]);

                    // Not needed as they will remain closed from the next page load
//                    blocks_to_remove.push (db_records ['#'][limited_block_index]);

                    if (!ai_cookie.hasOwnProperty (db_records ['#'][limited_block_index]) || !ai_cookie [db_records ['#'][limited_block_index]].hasOwnProperty ('x')) {
                      if (ai_debug) console.log ("AI SERVERSIDE LIMITED BLOCK:", db_records ['#'][limited_block_index], ' not closed - closing for 12 hours');

                      ai_set_cookie (db_records ['#'][limited_block_index], 'x', closed_until);
                    }
                  }

                  setTimeout (function () {
                    for (index = 0; index < blocks_to_remove.length; ++index) {
                      // ***
//                      $('span[data-ai-block=' + blocks_to_remove [index] + ']').closest ("div[data-ai]").remove ();
                      document.querySelectorAll ('span[data-ai-block="' + blocks_to_remove [index] + '"]').forEach ((el, index) => {
                        var closest = el.closest ("div[data-ai]");
                        if (closest) {
                          closest.remove ();
                        }
                      });
                    }
                  }, 50);
                }

                if (ai_debug) console.log ('');
              }

          });
        } else {
            if (ai_debug) console.log ("AI PROCESS IMPRESSIONS INTERNAL TRACKING DISABLED");
          }
      }

      if (ai_external_tracking) {
        if (typeof ai_external_tracking_no_impressions === 'undefined') {
          for (var i = 0; i < blocks.length; i++) {
            // Skip pageview data
            if (blocks [i] != 0) {
              external_tracking ("impression", blocks [i],  block_names [i], block_counters [i], versions [i], version_names [i], true);
            }
          }
        } else {
            if (ai_debug) console.log ("AI PROCESS IMPRESSIONS EXTERNAL TRACKING DISABLED");
          }
      }
    }
  }

  function ai_process_pageview_checks () {

    var ai_debug = typeof ai_debugging !== 'undefined'; // 6
//    var ai_debug = false;

    ai_check_data = {};

    if (typeof ai_iframe != 'undefined') return;

    if (ai_debug) console.log ('AI PROCESS PAGEVIEW CHECKS');

    ai_cookie = ai_load_cookie ();

    // ***
//    $('.ai-check-block').each (function () {
    document.querySelectorAll ('.ai-check-block').forEach ((element, i) => {

      // ***
//      var block = $(this).data ('ai-block');
//      var delay_pv = $(this).data ('ai-delay-pv');
//      var every_pv = $(this).data ('ai-every-pv');

//      var code_hash             = $(this).data ('ai-hash');
//      var max_imp               = $(this).data ('ai-max-imp');
//      var limit_imp_per_time    = $(this).data ('ai-limit-imp-per-time');
//      var limit_imp_time        = $(this).data ('ai-limit-imp-time');
//      var max_clicks            = $(this).data ('ai-max-clicks');
//      var limit_clicks_per_time = $(this).data ('ai-limit-clicks-per-time');
//      var limit_clicks_time     = $(this).data ('ai-limit-clicks-time');

//      var global_limit_clicks_per_time = $(this).data ('ai-global-limit-clicks-per-time');
//      var global_limit_clicks_time     = $(this).data ('ai-global-limit-clicks-time');


      var block = element.dataset.aiBlock;
      var delay_pv = element.dataset.aiDelayPv;
      var every_pv = element.dataset.aiEveryPv;

      var code_hash             = element.dataset.aiHash;
      var max_imp               = element.dataset.aiMaxImp;
      var limit_imp_per_time    = element.dataset.aiLimitImpPerTime;
      var limit_imp_time        = element.dataset.aiLimitImpTime;
      var max_clicks            = element.dataset.aiMaxClicks;
      var limit_clicks_per_time = element.dataset.aiLimitClicksPerTime;
      var limit_clicks_time     = element.dataset.aiLimitClicksTime;


      var global_limit_clicks_per_time = element.dataset.aiGlobalLimitClicksPerTime;
      var global_limit_clicks_time     = element.dataset.aiGlobalLimitClicksTime;

      if (ai_debug) console.log ('AI CHECK INITIAL DATA, block:', block);

      if (typeof delay_pv != 'undefined' && delay_pv > 0) {
        if (!ai_check_data.hasOwnProperty (block)) {
          ai_check_data [block] = {};
        }
        ai_check_data [block]['d'] = delay_pv;

        var cookie_delay_pv = '';
        if (ai_cookie.hasOwnProperty (block)) {
          if (ai_cookie [block].hasOwnProperty ('d')) {
            cookie_delay_pv = ai_cookie [block]['d'];
          }
        }

        if (cookie_delay_pv === '') {
          if (ai_debug) console.log ('AI CHECK PAGEVIEWS, block:', block, 'delay:', delay_pv);

          ai_set_cookie (block, 'd', delay_pv - 1);
        }
      }

      if (typeof every_pv != 'undefined' && every_pv >= 2) {
        if (!ai_check_data.hasOwnProperty (block)) {
          ai_check_data [block] = {};
        }

        if (typeof ai_delay_showing_pageviews === 'undefined' && (!ai_cookie.hasOwnProperty (block) || !ai_cookie [block].hasOwnProperty ('d'))) {
          // Set d to process e
          if (!ai_cookie.hasOwnProperty (block)) {
            ai_cookie [block] = {};
          }
          ai_cookie [block]['d'] = 0;
        }

        ai_check_data [block]['e'] = every_pv;
      }

      if (typeof max_imp != 'undefined' && max_imp > 0) {
        if (!ai_check_data.hasOwnProperty (block)) {
          ai_check_data [block] = {};
        }
        ai_check_data [block]['i'] = max_imp;
        ai_check_data [block]['h'] = code_hash;

        var cookie_code_hash = '';
        var cookie_max_imp = '';
        if (ai_cookie.hasOwnProperty (block)) {
          if (ai_cookie [block].hasOwnProperty ('i')) {
            cookie_max_imp = ai_cookie [block]['i'];
          }
          if (ai_cookie [block].hasOwnProperty ('h')) {
            cookie_code_hash = ai_cookie [block]['h'];
          }
        }

        if (cookie_max_imp === '' || cookie_code_hash != code_hash) {
          if (ai_debug) console.log ('AI CHECK IMPRESSIONS, block:', block, 'max', max_imp, 'impressions', 'hash', code_hash);

          ai_set_cookie (block, 'i', max_imp);
          ai_set_cookie (block, 'h', code_hash);
        }
      } else {
          if (ai_cookie.hasOwnProperty (block) && ai_cookie [block].hasOwnProperty ('i')) {
            if (ai_debug) console.log ('AI IMPRESSIONS, block', block, 'removing i');

            ai_set_cookie (block, 'i', '');
            if (!ai_cookie [block].hasOwnProperty ('c') && !ai_cookie [block].hasOwnProperty ('x')) {
              ai_set_cookie (block, 'h', '');
            }
          }
        }

      if (typeof limit_imp_per_time != 'undefined' && limit_imp_per_time > 0 && typeof limit_imp_time != 'undefined' && limit_imp_time > 0) {
        if (!ai_check_data.hasOwnProperty (block)) {
          ai_check_data [block] = {};
        }
        ai_check_data [block]['ipt'] = limit_imp_per_time;
        ai_check_data [block]['it']  = limit_imp_time;

        var cookie_limit_imp_per_time = '';
        var cookie_limit_imp_time = '';
        if (ai_cookie.hasOwnProperty (block)) {
          if (ai_cookie [block].hasOwnProperty ('ipt')) {
            cookie_limit_imp_per_time = ai_cookie [block]['ipt'];
          }
          if (ai_cookie [block].hasOwnProperty ('it')) {
            cookie_limit_imp_time = ai_cookie [block]['it'];
          }
        }

        if (cookie_limit_imp_per_time === '' || cookie_limit_imp_time === '') {
          if (ai_debug) console.log ('AI CHECK IMPRESSIONS, block:', block, 'max', limit_imp_per_time, 'impresssions per', limit_imp_time, 'days (' + (limit_imp_time * 24 * 3600), 's)');

          ai_set_cookie (block, 'ipt', limit_imp_per_time);

          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);

          ai_set_cookie (block, 'it', Math.round (timestamp + limit_imp_time * 24 * 3600));
        }
        if (cookie_limit_imp_time > 0) {
          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);

          if (cookie_limit_imp_time <= timestamp) {
            if (ai_debug) console.log ('AI CHECK IMPRESSIONS, block:', block, 'reset max', limit_imp_per_time, 'impresssions per', limit_imp_time, 'days (' + (limit_imp_time * 24 * 3600), 's)');

            ai_set_cookie (block, 'ipt', limit_imp_per_time);
            ai_set_cookie (block, 'it', Math.round (timestamp + limit_imp_time * 24 * 3600));
          }
        }
      } else {
          if (ai_cookie.hasOwnProperty (block)) {
            if (ai_cookie [block].hasOwnProperty ('ipt')) ai_set_cookie (block, 'ipt', '');
            if (ai_cookie [block].hasOwnProperty ('it'))  ai_set_cookie (block, 'it',  '');
          }
        }

      if (typeof max_clicks != 'undefined' && max_clicks > 0) {
        if (!ai_check_data.hasOwnProperty (block)) {
          ai_check_data [block] = {};
        }
        ai_check_data [block]['c'] = max_clicks;
        ai_check_data [block]['h'] = code_hash;

        var cookie_code_hash = '';
        var cookie_max_clicks = '';
        if (ai_cookie.hasOwnProperty (block)) {
          if (ai_cookie [block].hasOwnProperty ('c')) {
            cookie_max_clicks = ai_cookie [block]['c'];
          }
          if (ai_cookie [block].hasOwnProperty ('h')) {
            cookie_code_hash = ai_cookie [block]['h'];
          }
        }

        if (cookie_max_clicks === '' || cookie_code_hash != code_hash) {
          if (ai_debug) console.log ('AI CHECK CLICKS, block:', block, 'max', max_clicks, 'clicks', 'hash', code_hash);

          ai_set_cookie (block, 'c', max_clicks);
          ai_set_cookie (block, 'h', code_hash);
        }
      } else {
          if (ai_cookie.hasOwnProperty (block) && ai_cookie [block].hasOwnProperty ('c')) {
            if (ai_debug) console.log ('AI CLICKS, block', block, 'removing c');

            ai_set_cookie (block, 'c', '');
            if (!ai_cookie [block].hasOwnProperty ('i') && !ai_cookie [block].hasOwnProperty ('x')) {
              ai_set_cookie (block, 'h', '');
            }
          }
        }

      if (typeof limit_clicks_per_time != 'undefined' && limit_clicks_per_time > 0 && typeof limit_clicks_time != 'undefined' && limit_clicks_time > 0) {
        if (!ai_check_data.hasOwnProperty (block)) {
          ai_check_data [block] = {};
        }
        ai_check_data [block]['cpt'] = limit_clicks_per_time;
        ai_check_data [block]['ct']  = limit_clicks_time;

        var cookie_limit_clicks_per_time = '';
        var cookie_limit_clicks_time = '';

        if (ai_cookie.hasOwnProperty (block)) {
          if (ai_cookie [block].hasOwnProperty ('cpt')) {
            cookie_limit_clicks_per_time = ai_cookie [block]['cpt'];
          }
          if (ai_cookie [block].hasOwnProperty ('ct')) {
            cookie_limit_clicks_time = ai_cookie [block]['ct'];
          }
        }

        if (cookie_limit_clicks_per_time === '' || cookie_limit_clicks_time === '') {
          if (ai_debug) console.log ('AI CHECK CLICKS, block:', block, 'max', limit_clicks_per_time, 'clicks per', limit_clicks_time, 'days (' + (limit_clicks_time * 24 * 3600), 's)');

          ai_set_cookie (block, 'cpt', limit_clicks_per_time);

          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);

          ai_set_cookie (block, 'ct', Math.round (timestamp + limit_clicks_time * 24 * 3600));
        }

        if (cookie_limit_clicks_time > 0) {
          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);

          if (cookie_limit_clicks_time <= timestamp) {
            if (ai_debug) console.log ('AI CHECK CLICKS, block:', block, 'reset max', limit_clicks_per_time, 'clicks per', limit_clicks_time, 'days (' + (limit_clicks_time * 24 * 3600), 's)');

            ai_set_cookie (block, 'cpt', limit_clicks_per_time);
            ai_set_cookie (block, 'ct', Math.round (timestamp + limit_clicks_time * 24 * 3600));
          }
        }
      } else {
          if (ai_cookie.hasOwnProperty (block)) {
            if (ai_cookie [block].hasOwnProperty ('cpt')) ai_set_cookie (block, 'cpt', '');
            if (ai_cookie [block].hasOwnProperty ('ct'))  ai_set_cookie (block, 'ct', '');
          }
        }

      if (typeof global_limit_clicks_per_time != 'undefined' && global_limit_clicks_per_time > 0 && typeof global_limit_clicks_time != 'undefined' && global_limit_clicks_time > 0) {
        if (!ai_check_data.hasOwnProperty ('G')) {
          ai_check_data ['G'] = {};
        }
        ai_check_data ['G']['cpt'] = global_limit_clicks_per_time;
        ai_check_data ['G']['ct']  = global_limit_clicks_time;

        var global_cookie_limit_clicks_per_time = '';
        var global_cookie_limit_clicks_time = '';

        if (ai_cookie.hasOwnProperty ('G')) {
          if (ai_cookie ['G'].hasOwnProperty ('cpt')) {
            global_cookie_limit_clicks_per_time = ai_cookie ['G']['cpt'];
          }
          if (ai_cookie ['G'].hasOwnProperty ('ct')) {
            global_cookie_limit_clicks_time = ai_cookie ['G']['ct'];
          }
        }

        if (global_cookie_limit_clicks_per_time === '' || global_cookie_limit_clicks_time === '') {
          if (ai_debug) console.log ('AI CHECK CLICKS GLOBAL: max', global_limit_clicks_per_time, 'clicks per', global_limit_clicks_time, 'days (' + (global_limit_clicks_time * 24 * 3600), 's)');

          ai_set_cookie ('G', 'cpt', global_limit_clicks_per_time);

          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);

          ai_set_cookie ('G', 'ct', Math.round (timestamp + global_limit_clicks_time * 24 * 3600));
        }

        if (global_cookie_limit_clicks_time > 0) {
          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);

          if (global_cookie_limit_clicks_time <= timestamp) {
            if (ai_debug) console.log ('AI CHECK CLICKS GLOBAL: reset max', global_limit_clicks_per_time, 'clicks per', global_limit_clicks_time, 'days (' + (global_limit_clicks_time * 24 * 3600), 's)');

            ai_set_cookie ('G', 'cpt', global_limit_clicks_per_time);
            ai_set_cookie ('G', 'ct', Math.round (timestamp + global_limit_clicks_time * 24 * 3600));
          }
        }
      } else {
          if (ai_cookie.hasOwnProperty ('G')) {
            if (ai_cookie ['G'].hasOwnProperty ('cpt')) ai_set_cookie ('G', 'cpt', '');
            if (ai_cookie ['G'].hasOwnProperty ('ct'))  ai_set_cookie ('G', 'ct', '');
          }
        }
    });

    // Remove check class so it's not processed again when tracking is called
    // ***
//    $('.ai-check-block'). removeClass ('ai-check-block');
    document.querySelectorAll ('.ai-check-block').forEach ((element, i) => {
      element.classList.remove ('ai-check-block');
    });


    if (ai_debug) console.log ('');
    if (ai_debug) console.log ('AI PROCESS CHECKS', ai_check_data);


    if (ai_debug) console.log ('AI CHECK PAGEVIEWS');

    for (var cookie_block in ai_cookie) {
      for (var cookie_block_property in ai_cookie [cookie_block]) {
        if (cookie_block_property == 'd') {
          if (ai_debug) console.log ('AI CHECK PAGEVIEWS block:', cookie_block);

          var delay = ai_cookie [cookie_block][cookie_block_property];
          if (delay > 0) {
            if (ai_debug) console.log ('AI PAGEVIEW, block', cookie_block, 'delayed for', delay - 1, 'pageviews');

            ai_set_cookie (cookie_block, 'd', delay - 1);
          } else {
              if (ai_check_data.hasOwnProperty (cookie_block) && ai_check_data [cookie_block].hasOwnProperty ('e')) {
                if (ai_debug) console.log ('AI PAGEVIEW, block', cookie_block, 'show every', ai_check_data [cookie_block]['e'], 'pageviews, delayed for', ai_check_data [cookie_block]['e'] - 1, 'pageviews');

                ai_set_cookie (cookie_block, 'd', ai_check_data [cookie_block]['e'] - 1);
              } else {
                  if (!ai_check_data.hasOwnProperty (cookie_block) || !ai_check_data [cookie_block].hasOwnProperty ('d')) {
                    if (ai_debug) console.log ('AI PAGEVIEW, block', cookie_block, 'removing d');

                    ai_set_cookie (cookie_block, 'd', '');
                  }
                }
            }
        }
      }
    }
  }

  function ai_log_impressions () {

    var ai_debug = typeof ai_debugging !== 'undefined'; // 7
//    var ai_debug = false;

    if (ai_debug) console.log ('');
    if (ai_debug) console.log ('AI TRACKING');

    // Move to ai_process_impressions ()
//    Array.prototype.forEach.call (document.querySelectorAll ('[data-ai]'), function (block_wrapping_div) {
//      var new_tracking_data = '';

//      if (ai_debug && block_wrapping_div.hasAttribute ('data-ai-1')) console.log ('AI TRACKING CHECKING BLOCK', block_wrapping_div.getAttribute ('class'));

//      for (var fallback_level = 1; fallback_level <= 9; fallback_level ++) {
//        if (block_wrapping_div.hasAttribute ('data-ai-' + fallback_level)) {
//          new_tracking_data = block_wrapping_div.getAttribute ('data-ai-' + fallback_level);

//          if (ai_debug) console.log ('  FALLBACK LEVEL', fallback_level);
//        } else break;
//      }

//      if (new_tracking_data != '') {
//        block_wrapping_div.setAttribute ('data-ai', new_tracking_data);
//      }

//      if (ai_debug) console.log ('  TRACKING DATA UPDATED TO', b64d (block_wrapping_div.getAttribute ('data-ai')));
//    });

    if (ai_track_pageviews) {
      var client_width = document.documentElement.clientWidth, inner_width =  window.innerWidth;
      var viewport_width = client_width < inner_width ? inner_width : client_width;

      var version = 0;
      var name = '?';
      // ***
//      $.each (ai_viewport_widths, function (index, width) {
      ai_viewport_widths.every ((width, index) => {
        if (viewport_width >= width) {
          version = ai_viewport_indexes [index];
          name = ai_viewport_names [index];
          return (false);
        }
        return (true);
      });

      if (ai_debug) console.log ('AI TRACKING PAGEVIEW, viewport width:', viewport_width, '=>', name);

      // ***
//      var ai_masking_data = jQuery(b64d ("Ym9keQ==")).attr (AI_ADB_ATTR_NAME);
      var ai_masking_data = document.querySelector (b64d ("Ym9keQ==")).getAttribute (b64d (ai_adb_attribute));
      if (typeof ai_masking_data === "string") {
        var ai_masking = ai_masking_data == b64d ("bWFzaw==");
      }

      if (typeof ai_masking_data === "string" && typeof ai_masking === "boolean" && ai_masking) {
        if (ai_external_tracking) {
          external_tracking ("ad blocking", 0, ai_viewport_names [version - 1], 0, 0, '', true);
        }
        version |= 0x80;
      }

      pageview_data = [0, version];
    }

    ai_process_pageview_checks ();

    ai_process_impressions ();

    // Pageview data was not sent with block impressions
    if (pageview_data.length != 0) {
      if (ai_debug) console.log ('AI PROCESS IMPRESSIONS - SENDING PAGEVIEW DATA', pageview_data);

      if (ai_internal_tracking) {
        // ***
//        $.ajax ({
//            url: ai_ajax_url,
//            type: "post",
//            data: {
//              action: "ai_ajax",
//              ai_check: ai_data_id,
//              views: [0],
//              versions: [version],
//            },
//            async: true
//        }).done (function (data) {



        var url_data = {
          action: "ai_ajax",
          ai_check: ai_data_id,
        };

        var formBody = [];
        for (var property in url_data) {
          var encodedKey = encodeURIComponent (property);
          var encodedValue = encodeURIComponent (url_data [property]);
          formBody.push (encodedKey + "=" + encodedValue);
        }

        var encodedKey = encodeURIComponent ('views[]');
        var encodedValue = encodeURIComponent (0);
        formBody.push (encodedKey + "=" + encodedValue);

        var encodedKey = encodeURIComponent ('versions[]');
        var encodedValue = encodeURIComponent (version);
        formBody.push (encodedKey + "=" + encodedValue);

        formBody = formBody.join ("&");

        async function ai_post_pageview () {
          const response = await fetch (ai_ajax_url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: formBody
          });

          const text = await response.text ();
          return text;
        }

        ai_post_pageview ().then (data => {
          if (ai_debug) {
            data = data.trim ();
            if (data != "") {
              var db_records = JSON.parse (data);
              console.log ("AI DB RECORDS: ", db_records);
            }
          }
        });
      }
    }

    ai_tracking_finished = true;
  }

  // ***
//  jQuery (window).on ('load', function () {
  window.addEventListener ('load', (event) => {
    if (typeof ai_delay_tracking == 'undefined') {
      ai_delay_tracking = 0;
    }

    setTimeout (ai_log_impressions, ai_delay_tracking + 1400);
    setTimeout (ai_install_click_trackers, ai_delay_tracking + 1500);
  });
// ***
//});
}

ai_ready (ai_tracking);

}
