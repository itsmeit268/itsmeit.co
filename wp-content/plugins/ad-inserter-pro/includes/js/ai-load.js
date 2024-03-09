if (typeof ai_recaptcha_site_key != 'undefined') {

/**
 * Based on yall - Yet Another Lazy loader
 * https://github.com/malchata/yall.js
 **/

const alLoad = function (element, env) {

  if (element.tagName === "DIV") {
    // ***
//    if (typeof element.dataset.code != 'undefined') {
    if (element.hasAttribute ('data-code')) {
      var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//      var ai_debug = false;

      // Using jQuery to properly load AdSense
      // ***
//      jQuery (element).prepend (b64d (element.dataset.code));

      var range = document.createRange ();
      var fragment_ok = true;
      try {
        var fragment = range.createContextualFragment (b64d (element.dataset.code));
      }
      catch (err) {
        var fragment_ok = false;
        if (ai_debug) console.log ('AI LOADING ', 'range.createContextualFragment ERROR:', err);
      }

      if (fragment_ok) {
        element.insertBefore (fragment, element.firstChild);
      }


      element.removeAttribute ("data-code");

      var classes = '';
      var wrapper = element.closest ('.' + b64d (element.dataset.class));

      if (ai_debug) {
        console.log ('');

        if (wrapper != null) {
          classes = wrapper.className;
        }
        if (element.getAttribute ("class").includes ('ai-wait-for-interaction')) {
          console.log ('AI LOADING ON INTERACTION', classes);
        } else
        if (element.getAttribute ("class").includes ('ai-check-recaptcha-score')) {
          console.log ('AI LOADING ON RECAPTCHA SCORE', classes);
        } else
        if (element.getAttribute ("class").includes ('ai-delayed')) {
          console.log ('AI DELAYED LOADING', classes);
        }
        else console.log ('AI LAZY LOADING', classes);
      }

      element.removeAttribute("data-class");
      element.removeAttribute("class");

      if (typeof ai_process_lists == 'function') {
        // ***
//        ai_process_lists        (jQuery(".ai-list-data", element)); // Doesn't process rotations
        ai_process_lists (); // Doesn't process rotations
      }
      if (typeof ai_process_ip_addresses == 'function') {
        // ***
//        ai_process_ip_addresses (jQuery(".ai-ip-data",   element));
        ai_process_ip_addresses ();
      }
      if (typeof ai_process_filter_hooks == 'function') {
        // ***
//        ai_process_filter_hooks (jQuery (".ai-filter-check", element));
        ai_process_filter_hooks ();
      }
      if (typeof ai_process_rotations_in_element == 'function') {
        ai_process_rotations_in_element (element);
      }
      if (typeof ai_adb_process_blocks == 'function') {
        // ***
//        ai_adb_process_blocks (jQuery (element));
        ai_adb_process_blocks ();
      }
//      console.log (typeof ai_process_impressions == 'function', wrapper != null, ai_tracking_finished == true);
      if (typeof ai_process_impressions == 'function' && wrapper != null && ai_tracking_finished == true) {
//        ai_process_impressions ();
        setTimeout (ai_process_impressions, 1400);
      }
      if (typeof ai_install_click_trackers == 'function' && wrapper != null && ai_tracking_finished == true) {
//        ai_install_click_trackers ();
        setTimeout (ai_install_click_trackers, 1500);
      }
      if (typeof ai_install_close_buttons == 'function' && wrapper != null) {
        ai_install_close_buttons (wrapper);
      }

      ai_process_wait_for_interaction ();

      ai_process_delayed_blocks ();
    }
  }
};

const aiLazyLoading = function (userOptions) {
  const env = {
    intersectionObserverSupport: "IntersectionObserver" in window && "IntersectionObserverEntry" in window && "intersectionRatio" in window.IntersectionObserverEntry.prototype,
    mutationObserverSupport: "MutationObserver" in window,
    idleCallbackSupport: "requestIdleCallback" in window,
    eventsToBind: [
      [document, "scroll"],
      [document, "touchmove"],
      [window, "resize"],
      [window, "orientationchange"]
    ]
  };

  const options = {
    lazyClass: "ai-lazy",
    lazyElement: null,
    throttleTime: 200,
    idlyLoad: false,
    idleLoadTimeout: 100,
    threshold: ai_lazy_loading_offset,
    observeChanges: false,
    observeRootSelector: "body",
    mutationObserverOptions: {
      childList: true
    }
//    ,
//    ...userOptions
  };

  //  ... replacement
  Object.assign (options, userOptions);

  const selectorString = `div.${options.lazyClass}`;
  const idleCallbackOptions = {
    timeout: options.idleLoadTimeout
  };

  if (options.lazyElement == null) {
    var lazyElements = [].slice.call(document.querySelectorAll(selectorString));
  } else {
      var lazyElements = [].push (options.lazyElement);
    }

  if (env.intersectionObserverSupport === true) {
//    var intersectionListener = new IntersectionObserver((entries, observer) => {
    var intersectionListener = new IntersectionObserver (function (entries, observer) {
//      entries.forEach((entry) => {
      entries.forEach (function (entry) {
//        let element = entry.target;
        var element = entry.target;

        if (entry.isIntersecting === true) {
          if (options.idlyLoad === true && env.idleCallbackSupport === true) {
//            requestIdleCallback(() => {
            requestIdleCallback (function () {
              alLoad(element, env);
            }, idleCallbackOptions);
          } else {
            alLoad(element, env);
          }

          element.classList.remove(options.lazyClass);
          observer.unobserve(element);

//          lazyElements = lazyElements.filter((lazyElement) => {
          lazyElements = lazyElements.filter (function (lazyElement) {
            return lazyElement !== element;
          });
        }
      });
    }, {
      rootMargin: `${options.threshold}px 0%`
    });

//    lazyElements.forEach((lazyElement) => intersectionListener.observe(lazyElement));
    lazyElements.forEach (function (lazyElement) {intersectionListener.observe (lazyElement)});
  } else {
//    var lazyloadBack = () => {
    var lazyloadBack = function () {
//      let active = false;
      var active = false;

      if (active === false && lazyElements.length > 0) {
        active = true;

//        setTimeout(() => {
        setTimeout (function () {
//          lazyElements.forEach((lazyElement) => {
          lazyElements.forEach (function (lazyElement) {
            if (lazyElement.getBoundingClientRect().top <= (window.innerHeight + options.threshold) && lazyElement.getBoundingClientRect().bottom >= -(options.threshold) && getComputedStyle(lazyElement).display !== "none") {
              if (options.idlyLoad === true && env.idleCallbackSupport === true) {
//                requestIdleCallback(() => {
                requestIdleCallback (function () {
                  alLoad(lazyElement, env);
                }, idleCallbackOptions);
              } else {
                alLoad(lazyElement, env);
              }

              lazyElement.classList.remove(options.lazyClass);

//              lazyElements = lazyElements.filter((element) => {
              lazyElements = lazyElements.filter (function (element) {
                return element !== lazyElement;
              });
            }
          });

          active = false;

          if (lazyElements.length === 0 && options.observeChanges === false) {
//            env.eventsToBind.forEach((eventPair) => eventPair[0].removeEventListener(eventPair[1], lazyloadBack));
            env.eventsToBind.forEach (function (eventPair) {eventPair[0].removeEventListener(eventPair[1], lazyloadBack)});
          }
        }, options.throttleTime);
      }
    };

//    env.eventsToBind.forEach((eventPair) => eventPair[0].addEventListener(eventPair[1], lazyloadBack));
    env.eventsToBind.forEach (function (eventPair) {eventPair[0].addEventListener(eventPair[1], lazyloadBack)});

    lazyloadBack();
  }

  if (env.mutationObserverSupport === true && options.observeChanges === true) {
//    const mutationListener = new MutationObserver((mutations) => {
    const mutationListener = new MutationObserver (function (mutations) {
//      mutations.forEach((mutation) => {
      mutations.forEach (function (mutation) {
//        [].slice.call(document.querySelectorAll(selectorString)).forEach((newElement) => {
        [].slice.call(document.querySelectorAll(selectorString)).forEach (function (newElement) {
          if (lazyElements.indexOf(newElement) === -1) {
            lazyElements.push(newElement);

            if (env.intersectionObserverSupport === true) {
              intersectionListener.observe(newElement);
            } else {
              lazyloadBack();
            }
          }
        });
      });
    });

    mutationListener.observe(document.querySelector(options.observeRootSelector), options.mutationObserverOptions);
  }
};

function ai_ready (fn) {
  if (document.readyState === 'complete' || (document.readyState !== 'loading' && !document.documentElement.doScroll)) {
    fn ();
  } else {
     document.addEventListener ('DOMContentLoaded', fn);
  }
}

// ***
//jQuery (function ($) {
//  $(document).ready(function($) {
function ai_trigger_lazy_loading () {
    setTimeout (function() {aiLazyLoading ({
      lazyClass: 'ai-lazy',
//      lazySelector: "div.ai-lazy",
      observeChanges: true,
      mutationObserverOptions: {
        childList: true,
        attributes: true,
        subtree: true
      }
    });}, 5);
}
//  });
//});

ai_ready (ai_trigger_lazy_loading);

ai_load_blocks = function (block) {
  if (Number.isInteger (block)) {
    var loading_class = 'ai-manual-' + block;
  } else var loading_class = 'ai-manual';

  aiLazyLoading ({
    lazyClass: loading_class,
    threshold: 99999,
    observeChanges: true,
    mutationObserverOptions: {
      childList: true,
      attributes: true,
      subtree: true
    }
  });

  if (typeof ai_process_lists == 'function') {
  // ***
//    ai_process_lists (jQuery ("div.ai-list-manual, meta.ai-list-manual"));
    ai_process_lists ();
  }
}


ai_process_wait_for_interaction = function () {
  var ai_debug = typeof ai_debugging !== 'undefined'; // 2
//  var ai_debug = false;

  const ai_user_interaction_events = [
    "mouseover",
    "keydown",
    "touchmove",
    "touchstart"
  ];

  function ai_trigger_script_loader () {
    if (ai_debug) console.log ('AI WAIT FOR INTERACTION TRIGGER')

    if (typeof ai_load_scripts_timer != 'undefined') {
      clearTimeout (ai_load_scripts_timer);
    }

    ai_user_interaction = true;

    ai_load_interaction (false);
  }

  function ai_load_interaction (timeout) {
    if (ai_debug) {
      if (timeout) console.log ('AI WAIT FOR INTERACTION TIMEOUT')
      console.log ('AI WAIT FOR INTERACTION LOADING')
    }

    ai_user_interaction_events.forEach (function (event) {
      window.removeEventListener (event, ai_trigger_script_loader, {passive: true});
    });

    var loading_class = 'ai-wait-for-interaction';

    aiLazyLoading ({
      lazyClass: loading_class,
      threshold: 99999,
      observeChanges: true,
      mutationObserverOptions: {
        childList: true,
        attributes: true,
        subtree: true
      }
    });
  }

  var ai_wait_for_interaction_blocks = document.getElementsByClassName ("ai-wait-for-interaction").length;

  if (ai_wait_for_interaction_blocks != 0) {
    if (ai_debug) console.log ('AI WAIT FOR INTERACTION BLOCKS: ', ai_wait_for_interaction_blocks);

    if (typeof ai_interaction_timeout == 'undefined') {
      ai_interaction_timeout = 4000;
    }

    if (ai_debug) console.log ('AI WAIT FOR INTERACTION TIMEOUT:', ai_interaction_timeout > 0 ? ai_interaction_timeout + ' ms' : 'DISABLED');

    if (typeof ai_delay_tracking == 'undefined') {
      ai_delay_tracking = 0;
    }

    if (ai_interaction_timeout > 0) {
      ai_delay_tracking += ai_interaction_timeout;

      var ai_load_scripts_timer = setTimeout (ai_load_interaction, ai_interaction_timeout, true);
    }

    ai_user_interaction_events.forEach (function (event) {
      window.addEventListener (event, ai_trigger_script_loader, {passive: true});
    });
  }
}

setTimeout (ai_process_wait_for_interaction, 3);



ai_process_check_recaptcha_score = function () {
  var ai_debug = typeof ai_debugging !== 'undefined'; // 3
//  var ai_debug = false;

  if (typeof grecaptcha != 'undefined' && ai_recaptcha_site_key != '') {
    grecaptcha.ready (function () {
      grecaptcha.execute (ai_recaptcha_site_key, {action: 'submit'}).then(function(token) {
        var xhttp = new XMLHttpRequest ();
        var data = "ai_check=AI_NONCE&recaptcha=" + token;
        xhttp.open ("POST", ai_ajax_url +"?action=ai_ajax", true);
        xhttp.setRequestHeader ('Content-type', 'application/x-www-form-urlencoded');
        xhttp.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
            let response = JSON.parse (this.responseText);

            // TEST
//            response.score = 0.1;

            if (ai_debug) console.log ('AI RECAPTCHA RESPONSE: ', response);
            if (ai_debug) console.log ('AI RECAPTCHA SCORE: ', response.score, '['+parseFloat (ai_recaptcha_threshold)+']');

            if (response && response.success) {
              ai_recaptcha_score = response.score;
              const recaptcha_blocks = document.getElementsByClassName ("ai-check-recaptcha-score");

              if (response.score < (1000 * parseFloat (ai_recaptcha_threshold)) / 1000) {
                // bad user
                if (ai_debug) console.log ('AI RECAPTCHA RESULT: VERY LIKELY A BAD INTERACTION');

                for (let i = 0; i < recaptcha_blocks.length; i++) {
                  const trackign_block = recaptcha_blocks [i].closest ('.ai-track');
                  if (trackign_block != null) {
                    trackign_block.classList.remove ("ai-track");
                  }

                  var block_class = recaptcha_blocks [i].dataset.class;
                  if (typeof block_class != 'undefined') {
                    block_class = b64d (block_class);
                    const wrapping_div = recaptcha_blocks [i].closest ('.' + block_class);
                    if (wrapping_div != null) {
                      wrapping_div.classList.remove ('ai-list-block');
                      wrapping_div.classList.remove ('ai-list-block-ip');

                      var debug_label = wrapping_div.getElementsByClassName ('ai-recaptcha-score');
                      if (debug_label.length != 0) {
                        debug_label [0].innerHTML = response.score;
                      }

                      debug_label = wrapping_div.getElementsByClassName ('ai-recaptcha-result');
                      if (debug_label.length != 0) {
                        debug_label [0].innerHTML = ai_front.hidden;
                      }
                    }
                  }
                }
              } else {
                  // good user
                  if (ai_debug) console.log ('AI RECAPTCHA RESULT: VERY LIKELY A GOOD INTERACTION');

                  var loading_class = 'ai-check-recaptcha-score';

                  aiLazyLoading ({
                    lazyClass: loading_class,
                    threshold: 99999,
                    observeChanges: true,
                    mutationObserverOptions: {
                      childList: true,
                      attributes: true,
                      subtree: true
                    }
                  });

                for (let i = 0; i < recaptcha_blocks.length; i++) {
                  var block_class = recaptcha_blocks [i].dataset.class;
                  if (typeof block_class != 'undefined') {
                    block_class = b64d (block_class);
                    const wrapping_div = recaptcha_blocks [i].closest ('.' + block_class);
                    if (wrapping_div != null) {
                      var debug_label = wrapping_div.getElementsByClassName ('ai-recaptcha-score');
                      if (debug_label.length != 0) {
                        debug_label [0].innerHTML = response.score;
                      }

                      debug_label = wrapping_div.getElementsByClassName ('ai-recaptcha-result');
                      if (debug_label.length != 0) {
                        debug_label [0].innerHTML = ai_front.visible;
                      }
                    }
                  }
                }

                }

            } else {
                if (ai_debug) console.log ('AI RECAPTCHA AJAX RESPONSE ERROR');
              }

          }
        };
        xhttp.send (data);

      });
    });
  }
}

setTimeout (ai_process_check_recaptcha_score, 2);


ai_process_delayed_blocks = function () {
  var ai_delayed_block_elements = document.getElementsByClassName ("ai-delayed-unprocessed");

  if (ai_delayed_block_elements.length != 0) {
    var ai_debug = typeof ai_debugging !== 'undefined'; // 4
//    var ai_debug = false;

    if (ai_debug) console.log ('AI DELAYED BLOCK ELEMENTS: ', ai_delayed_block_elements);

    function ai_delayed_load (block) {
      if (ai_debug) console.log ('AI DELAYED LOADING BLOCK', block)

      var loading_class = 'ai-delayed-' + block;

      aiLazyLoading ({
        lazyClass: loading_class,
        threshold: 99999,
        observeChanges: true,
        mutationObserverOptions: {
          childList: true,
          attributes: true,
          subtree: true
        }
      });
    }

    if (typeof ai_delay_tracking != 'undefined') {
      if (ai_debug) console.log ('ai_delay_tracking:', ai_delay_tracking);
    } else {
        ai_delay_tracking = 0;
      }

    var ai_delayed_block_numbers = Array ();

    for (var el = 0; el < ai_delayed_block_elements.length; el ++) {
      var element = ai_delayed_block_elements [el];
      var ai_block = parseInt (element.getAttribute ('data-block'));
      ai_delayed_block_numbers.push (ai_block);
    }
    const ai_delayed_blocks = [...new Set (ai_delayed_block_numbers)]

    if (ai_debug) console.log ('AI DELAYED BLOCKS', ai_delayed_blocks);


    for (var index = 0; index < ai_delayed_blocks.length; index ++) {
      var ai_block = ai_delayed_blocks [index];
      var delayed_blocks = document.getElementsByClassName ("ai-delayed-" + ai_block);
      var ai_delay = parseInt (delayed_blocks [0].getAttribute ('data-delay'));

      for (var i = delayed_blocks.length - 1; i >= 0; i --) {
        var delayed_block = delayed_blocks [i];
        delayed_block.classList.remove ('ai-delayed-unprocessed');

        if (ai_debug) console.log ('AI DELAYED BLOCK PROCESSED', delayed_block.getAttribute ('class'));
      }

      if (ai_debug) console.log ('AI DELAYED BLOCK', ai_block, 'for', ai_delay, 'ms');

      ai_delay_tracking += ai_delay;

      setTimeout (ai_delayed_load, ai_delay, ai_block);
    }
  }
}

//ai_process_delayed_blocks ();
setTimeout (ai_process_delayed_blocks, 1);

}

