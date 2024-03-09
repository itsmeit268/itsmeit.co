jQuery(document).ready(function($) {
  var ai_debug = typeof ai_debugging !== 'undefined';
//  var ai_debug = false;

  function ai_process_close_buttons (element) {
    var ai_close_button = $(element).find ('.ai-close-button.ai-close-unprocessed');
    if (ai_close_button.length) {
      if ($(element).outerHeight () !== 0) {
        if (!$(element).find ('.ai-parallax').length) {
          $(element).css ('width', '').addClass ('ai-close-fit');
        }
        $(element).find ('.ai-close-button').fadeIn (50);

        if (ai_debug) console.log ('AI CLOSE BUTTON', $(element).attr ('class'));
      } else {
          if (ai_debug) console.log ('AI CLOSE BUTTON outerHeight 0', $(element).attr ('class'));

          var ai_close_button = $(element);
          setTimeout (function() {
            if (ai_debug) console.log ('');

            if (ai_close_button.outerHeight () !== 0) {
              if (!ai_close_button.find ('.ai-parallax').length) {
                ai_close_button.css ('width', '').addClass ('ai-close-fit');
              }
              ai_close_button.find ('.ai-close-button').fadeIn (50);

              if (ai_debug) console.log ('AI DELAYED CLOSE BUTTON ', ai_close_button.attr ('class'));
            } else if (ai_debug) console.log ('AI DELAYED CLOSE BUTTON outerHeight 0', ai_close_button.attr ('class'));
          }, 4000);
        }

      $(ai_close_button).removeClass ('ai-close-unprocessed');
    }
  }

  ai_close_block = function (button) {
    var block_wrapper = $(button).closest ('.ai-close');
    var block = $(button).data ('ai-block');
    if (typeof block_wrapper != 'undefined') {
      var hash = block_wrapper.find ('.ai-attributes [data-ai-hash]').data ('ai-hash');
      var closed = $(button).data ('ai-closed-time');
      if (typeof closed != 'undefined') {
        if (ai_debug) console.log ('AI CLOSED block', block, 'for', closed, 'days');

        var date = new Date();
        var timestamp = Math.round (date.getTime() / 1000);

        // TODO: stay closed for session
        ai_set_cookie (block, 'x', Math.round (timestamp + closed * 24 * 3600));
        ai_set_cookie (block, 'h', hash);
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
    setTimeout (function() {
      $('.ai-close-button.ai-close-unprocessed', element).click (function () {
        ai_close_block (this);

        if (typeof ai_close_button_action == 'function') {
          var block = this.dataset.aiBlock;

          if (ai_debug) console.log ('AI CLOSE BUTTON ai_close_button_action (' + block + ') CALLED');

          ai_close_button_action (block);
        }
      });
    }, 1800);

    if (typeof ai_preview === 'undefined') {
      setTimeout (function() {
        $('.ai-close-button.ai-close-unprocessed', element).each (function () {

          var button = $(this);
          var timeout = button.data ('ai-close-timeout');

          if (timeout > 0) {
            if (ai_debug) console.log ('AI CLOSE TIME', timeout, 's,', typeof button.closest ('.ai-close').attr ('class') != 'undefined' ? button.closest ('.ai-close').attr ('class') : '');

            // Compensate for delayed timeout
            if (timeout > 2) timeout = timeout - 2; else timeout = 0;

            setTimeout (function() {
              if (ai_debug) console.log ('');
              if (ai_debug) console.log ('AI CLOSE TIMEOUT', typeof button.closest ('.ai-close').attr ('class') != 'undefined' ? button.closest ('.ai-close').attr ('class') : '');

              ai_close_block (button);
            }, timeout * 1000 + 1);
          }
        });
      }, 2000);
    }

    setTimeout (function() {
      if (ai_debug) console.log ('');
      if (ai_debug) console.log ('AI CLOSE BUTTON INSTALL', typeof $(element).attr ('class') != 'undefined' ? $(element).attr ('class') : '');

      if ($(element).hasClass ('ai-close')) ai_process_close_buttons (element); else
        $('.ai-close', element).each (function() {
          ai_process_close_buttons (this);
        });
     }, ai_close_button_delay);
  }

  if (typeof ai_close_button_delay == 'undefined') {
    ai_close_button_delay = 2200;
  }

  ai_install_close_buttons (document);
});
