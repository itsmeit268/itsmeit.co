//jQuery(document).ready(function($) {
// ***
function ai_check_close_buttons () {
  var ai_debug = typeof ai_debugging !== 'undefined';
//  var ai_debug = false;

  function ai_process_close_button (element) {
//    var ai_close_button = $(element).find ('.ai-close-button.ai-close-unprocessed');
    // ***
    var ai_close_button = element.querySelector ('.ai-close-button.ai-close-unprocessed');

    if (ai_close_button != null) {
      ai_close_button.addEventListener ('click', (event) => {
        ai_close_block (ai_close_button);

        if (typeof ai_close_button_action == 'function') {
          var block = ai_close_button.dataset.aiBlock;

          if (ai_debug) console.log ('AI CLOSE BUTTON ai_close_button_action (' + block + ') CALLED');

          ai_close_button_action (block);
        }
      });


//      if ($(element).outerHeight () !== 0) {
      // ***
      if (element.offsetHeight !== 0) {
//        if (!$(element).find ('.ai-parallax').length) {
        // ***
        if (element.querySelector ('.ai-parallax') == null) {
//          $(element).css ('width', '').addClass ('ai-close-fit');
          // ***
          element.style.width = '';
          element.classList.add ('ai-close-fit');
        }
//        $(element).find ('.ai-close-button').fadeIn (50);
        // ***
        ai_fade_in (element.querySelector ('.ai-close-button'), 50);

//        if (ai_debug) console.log ('AI CLOSE BUTTON', $(element).attr ('class'));
        // ***
        if (ai_debug) console.log ('AI CLOSE BUTTON', element.hasAttribute ("class") ? element.getAttribute ('class') : '');
      } else {
//          if (ai_debug) console.log ('AI CLOSE BUTTON outerHeight 0', $(element).attr ('class'));
          // ***
          if (ai_debug) console.log ('AI CLOSE BUTTON outerHeight 0', element.hasAttribute ("class") ? element.getAttribute ('class') : '');

//          var ai_close_button = $(element);
          // ***
          var ai_close_button = element;
          setTimeout (function() {
            if (ai_debug) console.log ('');

//            if (ai_close_button.outerHeight () !== 0) {
            // ***
            if (ai_close_button.offsetHeight !== 0) {
//              if (!ai_close_button.find ('.ai-parallax').length) {
              // ***
//              if (!ai_close_button.find ('.ai-parallax').length) {
              // ***
              if (ai_close_button.querySelector ('.ai-parallax') == null) {
              // ***
//                ai_close_button.css ('width', '').addClass ('ai-close-fit');
                ai_close_button.style.width = '';
                ai_close_button.classList.add ('ai-close-fit');
              }
//              ai_close_button.find ('.ai-close-button').fadeIn (50);
              // ***
              ai_fade_in (ai_close_button.querySelector ('.ai-close-button'), 50);

//              if (ai_debug) console.log ('AI DELAYED CLOSE BUTTON ', ai_close_button.attr ('class'));
              // ***
              if (ai_debug) console.log ('AI DELAYED CLOSE BUTTON ', ai_close_button.hasAttribute ("class") ? ai_close_button.getAttribute ('class') : '');
//            } else if (ai_debug) console.log ('AI DELAYED CLOSE BUTTON outerHeight 0', ai_close_button.attr ('class'));
            // ***
            } else if (ai_debug) console.log ('AI DELAYED CLOSE BUTTON outerHeight 0', ai_close_button.hasAttribute ("class") ? ai_close_button.getAttribute ('class') : '');
          }, 4000);
        }



        if (typeof ai_preview === 'undefined') {
    //      setTimeout (function() {

    //          var button = $(this);
              // ***
              var button = ai_close_button;
    //          var timeout = button.data ('ai-close-timeout');
              // ***
              var timeout = button.dataset.aiCloseTimeout;

              if (typeof timeout != 'undefined' && timeout > 0) {
    //            if (ai_debug) console.log ('AI CLOSE TIME', timeout, 's,', typeof button.closest ('.ai-close').attr ('class') != 'undefined' ? button.closest ('.ai-close').attr ('class') : '');
                // ***
                if (ai_debug) console.log ('AI CLOSE TIME', timeout, 's,', button.closest ('.ai-close').hasAttribute ('class') ? button.closest ('.ai-close').getAttribute ('class') : '');

                // Compensate for delayed timeout
                if (timeout > 2) timeout = timeout - 2; else timeout = 0;

                setTimeout (function() {
                  if (ai_debug) console.log ('');
    //              if (ai_debug) console.log ('AI CLOSE TIMEOUT', typeof button.closest ('.ai-close').attr ('class') != 'undefined' ? button.closest ('.ai-close').attr ('class') : '');
                  // ***
                  if (ai_debug) console.log ('AI CLOSE TIMEOUT', button.closest ('.ai-close').hasAttribute ('class') ? button.closest ('.ai-close').getAttribute ('class') : '');

                  ai_close_block (button);
                }, timeout * 1000 + 1);
              }
    //      }, 2000);
        }


//      $(ai_close_button).removeClass ('ai-close-unprocessed');
      // ***
      ai_close_button.classList.remove ('ai-close-unprocessed');
    }
  }

  ai_close_block = function (button) {
//    var block_wrapper = $(button).closest ('.ai-close');
    // ***
    var block_wrapper = button.closest ('.ai-close');
//    var block = $(button).data ('ai-block');
    // ***
    var block = button.dataset.aiBlock;
//    if (typeof block_wrapper != 'undefined') {
    // ***
    if (block_wrapper != null) {
//      var hash = block_wrapper.find ('.ai-attributes [data-ai-hash]').data ('ai-hash');
      // ***
      if (block_wrapper.querySelector ('.ai-attributes [data-ai-hash]') != null && 'aiHash' in block_wrapper.querySelector ('.ai-attributes [data-ai-hash]').dataset) {
        var hash = block_wrapper.querySelector ('.ai-attributes [data-ai-hash]').dataset.aiHash;
//        var closed = $(button).data ('ai-closed-time');
//        if (typeof closed != 'undefined') {
        // ***
        if ('aiClosedTime'in button.dataset) {
          var closed = button.dataset.aiClosedTime;
          if (ai_debug) console.log ('AI CLOSED block', block, 'for', closed, 'days');

          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);

          // TODO: stay closed for session
          ai_set_cookie (block, 'x', Math.round (timestamp + closed * 24 * 3600));
          ai_set_cookie (block, 'h', hash);
        }
      } else {
          var ai_cookie = ai_set_cookie (block, 'x', '');
          if (ai_cookie.hasOwnProperty (block) && !ai_cookie [block].hasOwnProperty ('i') && !ai_cookie [block].hasOwnProperty ('c')) {
            ai_set_cookie (block, 'h', '');
          }
        }

      block_wrapper.remove ();
    } else {
        ai_set_cookie (block, 'x', '');
        if (ai_cookie.hasOwnProperty (block) && !ai_cookie [block].hasOwnProperty ('i') && !ai_cookie [block].hasOwnProperty ('c')) {
          ai_set_cookie (block, 'h', '');
        }
      }
  }

  ai_install_close_buttons = function (element) {
//    if (ai_debug) console.log ('AI CLOSE BUTTONS INSTALL');

//    setTimeout (function () {
////      $('.ai-close-button.ai-close-unprocessed', element).click (function () {
//      // ***
//      element.querySelectorAll ('.ai-close-button.ai-close-unprocessed').forEach ((el, index) => {

//        if (!el.classList.contains ('ai-close-event')) {
//          el.addEventListener ('click', (event) => {
//            ai_close_block (el);
//          });
//        }
//        el.classList.add ('ai-close-event');
//      });
//    }, 1800);

//    if (typeof ai_preview === 'undefined') {
//      setTimeout (function() {
////        $('.ai-close-button.ai-close-unprocessed', element).each (function () {
//        // ***
//        element.querySelectorAll ('.ai-close-button.ai-close-unprocessed').forEach ((el, index) => {

////          var button = $(this);
//          // ***
//          var button = el;
////          var timeout = button.data ('ai-close-timeout');
//          // ***
//          var timeout = button.dataset.aiCloseTimeout;

//          if (typeof timeout != 'undefined' && timeout > 0) {
////            if (ai_debug) console.log ('AI CLOSE TIME', timeout, 's,', typeof button.closest ('.ai-close').attr ('class') != 'undefined' ? button.closest ('.ai-close').attr ('class') : '');
//            // ***
//            if (ai_debug) console.log ('AI CLOSE TIME', timeout, 's,', button.closest ('.ai-close').hasAttribute ('class') ? button.closest ('.ai-close').getAttribute ('class') : '');

//            // Compensate for delayed timeout
//            if (timeout > 2) timeout = timeout - 2; else timeout = 0;

//            setTimeout (function() {
//              if (ai_debug) console.log ('');
////              if (ai_debug) console.log ('AI CLOSE TIMEOUT', typeof button.closest ('.ai-close').attr ('class') != 'undefined' ? button.closest ('.ai-close').attr ('class') : '');
//              // ***
//              if (ai_debug) console.log ('AI CLOSE TIMEOUT', button.closest ('.ai-close').hasAttribute ('class') ? button.closest ('.ai-close').getAttribute ('class') : '');

//              ai_close_block (button);
//            }, timeout * 1000 + 1);
//          }
//        });
//      }, 2000);
//    }

    setTimeout (function() {
      if (ai_debug) console.log ('');
//      if (ai_debug) console.log ('AI CLOSE BUTTON INSTALL', typeof $(element).attr ('class') != 'undefined' ? $(element).attr ('class') : '');
      // ***

      if (ai_debug) console.log ('AI CLOSE BUTTON INSTALL', element instanceof Element && element.hasAttribute ('class') ? element.getAttribute ('class') : '');

//      if ($(element).hasClass ('ai-close')) ai_process_close_button (element); else
      // ***
      if (element instanceof Element && element.classList.contains ('ai-close')) ai_process_close_button (element); else
//        $('.ai-close', element).each (function() {
        // ***
          document.querySelectorAll ('.ai-close').forEach ((el, index) => {
  //          ai_process_close_button (this);
            // ***
            ai_process_close_button (el);
          });
     }, ai_close_button_delay);
  }

  if (typeof ai_close_button_delay == 'undefined') {
    ai_close_button_delay = 2200;
  }

  ai_install_close_buttons (document);
//});
// ***
}


function ai_fade_in (el, time) {
  el.style.display = 'block';
  el.style.opacity = 0;

  var last = +new Date();
  var tick = function () {
    el.style.opacity = +el.style.opacity + (new Date() - last) / time;
    last = +new Date();

    if (+el.style.opacity < 1) {
      (window.requestAnimationFrame && requestAnimationFrame (tick)) || setTimeout (tick, 16);
    }
  };

  tick ();
}

function ai_ready (fn) {
  if (document.readyState === 'complete' || (document.readyState !== 'loading' && !document.documentElement.doScroll)) {
    fn ();
  } else {
     document.addEventListener ('DOMContentLoaded', fn);
  }
}

ai_ready (ai_check_close_buttons);
