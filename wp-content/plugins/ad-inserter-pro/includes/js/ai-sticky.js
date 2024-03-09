if (typeof ai_process_sticky_elements_on_ready != 'undefined') {

if (typeof ai_sticky_delay != 'number') {
  ai_sticky_delay = 200;
}
//*
//ai_process_sticky_elements = function ($) {
ai_process_sticky_elements = function () {

  // ***
//  $('[data-ai-position-pc]').each (function() {
//    var scroll_height = $('body').height () - document.documentElement.clientHeight;
//    if (scroll_height <= 0) return true;
//    $(this).css ('top', scroll_height * $(this).data ('ai-position-pc'));
  var scroll_height = document.querySelector ('body').clientHeight - document.documentElement.clientHeight;
  document.querySelectorAll ('[data-ai-position-pc]').forEach ((el, i) => {
    if (scroll_height > 0) {
      el.style.top = scroll_height * el.dataset.aiPositionPc + 'px';
    }
  });

  var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//  var ai_debug = false;

  // Must be global variable to prevent optimization
  ai_main_content_element = ai_main_content_element.trim ();

  var client_width = document.documentElement.clientWidth;
  // ***
//  var main_element = element = $('.ai-content').first ();
  var main_element = element = document.querySelector ('.ai-content');
  var default_margin = 0;
  // ***
//  var sticky_content = $('.ai-sticky-content');
  var sticky_content = document.querySelectorAll ('.ai-sticky-content');
  // ***
//  var sticky_background = $('.ai-sticky-background');
  var sticky_background = document.querySelectorAll ('.ai-sticky-background');

  if (ai_debug) console.log ('');
  if (ai_debug) console.log ("AI STICKY CLIENT WIDTH:", client_width, 'px');
  if (ai_debug) console.log ("AI STICKY CONTENT:   ", sticky_content.length, 'elements');
  if (ai_debug) console.log ("AI STICKY BACKGROUND:", sticky_background.length, 'elements');

  var main_width = 0;
  if (sticky_content.length != 0 || sticky_background.length != 0) {
    // ***
//    if (ai_main_content_element == '' || $('body').hasClass ('ai-preview')) {
    if (ai_main_content_element == '' || document.querySelector ('body').classList.contains ('ai-preview')) {
      // ***
//      if (ai_debug) console.log ("AI STICKY CONTENT:", $('.ai-content').length, 'markers');
      if (ai_debug) console.log ("AI STICKY CONTENT:   ", document.querySelectorAll ('.ai-content').length, 'markers');

      // ***
//      if (element.length != 0) {
      if (element != null) {

        if (ai_debug) console.log ("AI STICKY CONTENT ELEMENT: TRYING FIRST MARKER");

        // ***
//        while (element.prop ("tagName") != "BODY") {
        while (element.tagName != "BODY") {
          // ***
//          var outer_width = element.outerWidth ();
          var outer_width = element.offsetWidth;

          if (ai_debug) {
            // ***
//            var element_class = main_element.attr ("class");
            var element_class = main_element.getAttribute ("class");
            if (typeof element_class == 'string') {
              element_class = '.' + element_class.trim ().split (" ").join ('.');
            } else element_class = '';
            // ***
//            console.log ("AI STICKY CONTENT ELEMENT:", main_element.prop ("tagName"), '#' + main_element.attr ("id"), element_class, outer_width, 'px');
            console.log ("AI STICKY CONTENT ELEMENT:", main_element.tagName, main_element.hasAttribute ("id") ? '#' + main_element.getAttribute ("id") : '', element_class, outer_width, 'px');
          }
                                                                                // allow some rounding - outerWidth () does not return decimal value
          if (outer_width != 0 && outer_width <= client_width && outer_width >= (main_width - 1)) {
            main_element = element;
            main_width = outer_width;
          }

          // ***
//          element = element.parent ();
          element = element.parentElement;
        }
      }

      if (main_width == 0) {

        if (ai_debug) console.log ("AI STICKY CONTENT ELEMENT: TRYING LAST MARKER");

        // ***
//        main_element = element = $('.ai-content').last ();
        element = document.querySelectorAll ('.ai-content');
        if (element.length != 0) {
          main_element = element = element [element.length - 1];
          // ***
  //        while (element.prop ("tagName") != "BODY") {
          while (element.tagName != "BODY") {
            // ***
//            var outer_width = element.outerWidth ();
            var outer_width = element.offsetWidth;

            if (ai_debug) {
              // ***
//              var element_class = main_element.attr ("class");
              var element_class = main_element.getAttribute ("class");
              if (typeof element_class == 'string') {
                element_class = '.' + element_class.trim ().split (" ").join ('.');
              } else element_class = '';
              // ***
//              console.log ("AI STICKY CONTENT ELEMENT:", main_element.prop ("tagName"), '#' + main_element.attr ("id"), element_class, outer_width, 'px');
              console.log ("AI STICKY CONTENT ELEMENT:", main_element.tagName, main_element.hasAttribute ("id") ? '#' + main_element.getAttribute ("id") : '', element_class, outer_width, 'px');
            }
                                                                                  // allow some rounding - outerWidth () does not return decimal value
            if (outer_width != 0 && outer_width <= client_width && outer_width >= (main_width - 1)) {
              main_element = element;
              main_width = outer_width;
            }

            // ***
//            element = element.parent ();
            element = element.parentElement;
          }
        }
      }
    } else {
        // numeric main content element is handled server-side
        if (parseInt (ai_main_content_element) != ai_main_content_element) {
          //
//          main_element = $(ai_main_content_element);
          main_element = document.querySelector (ai_main_content_element);

          if (ai_debug) console.log ("AI STICKY CUSTOM MAIN CONTENT ELEMENT:", ai_main_content_element);

          // ***
//          if (typeof main_element.prop ("tagName") != 'undefined') {
//            var outer_width = main_element.outerWidth ();
          if (typeof main_element.tagName != 'undefined') {
            var outer_width = main_element.offsetWidth;

            if (ai_debug) {
              // ***
//              var element_class = main_element.attr ("class");
              var element_class = main_element.getAttribute ("class");
              if (typeof element_class == 'string') {
                element_class = '.' + element_class.trim ().split (" ").join ('.');
              } else element_class = '';
              // ***
//              console.log ("AI STICKY CUSTOM MAIN CONTENT ELEMENT:", main_element.prop ("tagName"), '#' + main_element.attr ("id"), element_class, outer_width, 'px');
              console.log ("AI STICKY CUSTOM MAIN CONTENT ELEMENT:", main_element.tagName, main_element.hasAttribute ("id") ? '#' + main_element.getAttribute ("id") : '', element_class, outer_width, 'px');
            }

            if (outer_width != 0 && outer_width <= client_width && outer_width >= main_width) {
              main_width = outer_width;
            }
          }
        }
      }
  }

  if (main_width != 0) {
    if (ai_debug) {
      // ***
//      var element_class = main_element.attr ("class");
      var element_class = main_element.getAttribute ("class");
      if (typeof element_class == 'string') {
        element_class = '.' + element_class.trim ().split (" ").join ('.');
      } else element_class = '';
      // ***
//      console.log ("AI STICKY MAIN CONTENT ELEMENT:", main_element.prop ("tagName"), '#' + main_element.attr ("id"), element_class, outer_width, 'px');
      console.log ("AI STICKY MAIN CONTENT ELEMENT:", main_element.tagName, main_element.hasAttribute ("id") ? '#' + main_element.getAttribute ("id") : '', element_class, outer_width, 'px');
    }

    var shift = Math.floor (main_width / 2) + default_margin;
    if (ai_debug) console.log ('AI STICKY shift:', shift, 'px');

    //
//    sticky_content.each (function () {
    sticky_content.forEach ((el, i) => {
      if (ai_debug) console.log ('');

      if (main_width != 0) {
        // ***
//        var block_width = $(this).width ();
//        var block_height = $(this).height ();
        // Element should not be hidden while measuring
        el_style_display = el.style.display;
        el.style.display = 'block';
        var block_width  = Math.max (el.clientWidth,  el.offsetWidth,  el.scrollWidth);
        var block_height = Math.max (el.clientHeight, el.offsetHeight, el.scrollHeight);
        el.style.display = el_style_display;

        if (ai_debug) console.log ('AI STICKY BLOCK:', block_width, 'x', block_height);

        // ***
//        var sticky_background = $(this).hasClass ('ai-sticky-background');
//        $(this).removeClass ('ai-sticky-background');
        var sticky_background = el.classList.contains ('ai-sticky-background');
        el.classList.remove ('ai-sticky-background');

        if (sticky_background) {
          //
//          $(this).removeClass ('ai-sticky-background').removeAttr ('data-aos');
          el.classList.remove ('ai-sticky-background');
          el.removeAttribute ('data-aos');
          if (typeof ai_preview === 'undefined') {
            // ***
//            $(this).find ('.ai-close-button').removeAttr ('class');
            var button = el.querySelector ('.ai-close-button');
            if (button != null) {
              button.removeAttribute ('class');
            }
          }
        }

        if (ai_debug) console.log ('AI STICKY BACKGROUND:', sticky_background);

        // ***
//        if ($(this).hasClass ('ai-sticky-left')) {
        if (el.classList.contains ('ai-sticky-left')) {
          // ***
//          var margin = parseInt ($(this).css ('margin-right'));
          var margin = parseInt (el.style.marginRight);

          // ***
//          if (ai_debug) console.log ('AI STICKY left  ', $(this).attr ("class"), '=> SPACE LEFT: ', main_element.offset().left - margin - block_width, 'px');
          if (ai_debug) console.log ('AI STICKY left  ', el.hasAttribute ("class") ? el.getAttribute ("class") : '', '=> SPACE LEFT: ', main_element.offsetLeft - margin - block_width, 'px');

          // ***
//          if (sticky_background || main_element.offset().left - margin - block_width >= - block_width / 2) {
          if (sticky_background || main_element.offsetLeft - margin - block_width >= - block_width / 2) {
            // ***
//            $(this).css ('right', 'calc(50% + ' + shift + 'px)');
//            $(this).show ();
            el.style.right = 'calc(50% + ' + shift + 'px)';
            el.style.display = 'block';
//          } else $(this).removeClass ('ai-sticky-scroll'); // prevent showing if it has sticky scroll class
          } else el.classList.remove ('ai-sticky-scroll'); // prevent showing if it has sticky scroll class

        } else
        // ***
//        if ($(this).hasClass ('ai-sticky-right')) {
        if (el.classList.contains ('ai-sticky-right')) {
          // ***
//          var margin = parseInt ($(this).css ('margin-left'));
          var margin = parseInt (el.style.marginLeft);

          // ***
//          if (ai_debug) console.log ('AI STICKY right ', $(this).attr ("class"), '=> SPACE RIGHT: ', client_width - (main_element.offset().left + main_width + margin + block_width), 'px');
          if (ai_debug) console.log ('AI STICKY right ', el.hasAttribute ("class") ? el.getAttribute ("class") : '', '=> SPACE RIGHT: ', client_width - (main_element.offsetLeft + main_width + margin + block_width), 'px');

          // ***
//          if (sticky_background || main_element.offset().left + main_width + margin + block_width <= client_width + block_width / 2) {
          if (sticky_background || main_element.offsetLeft + main_width + margin + block_width <= client_width + block_width / 2) {
            // ***
//            $(this).css ('right', '').css ('left', 'calc(50% + ' + shift + 'px)');
//            $(this).show ();
            el.style.right = '';
            el.style.left = 'calc(50% + ' + shift + 'px)';
            el.style.display = 'block';
            // ***
//          } else $(this).removeClass ('ai-sticky-scroll'); // prevent showing if it has sticky scroll class
          } else el.classList.remove ('ai-sticky-scroll'); // prevent showing if it has sticky scroll class
        }

        // ***
//        if ($(this).hasClass ('ai-sticky-scroll')) {
        if (el.classList.contains ('ai-sticky-scroll')) {

          // ***
//          if (ai_debug) console.log ('AI STICKY scroll', $(this).attr ("class"), '=> MARGIN BOTTOM:', - block_height, 'px');
          if (ai_debug) console.log ('AI STICKY scroll', el.hasAttribute ("class") ? el.getAttribute ("class") : '', '=> MARGIN BOTTOM:', - block_height, 'px');

          // ***
//          $(this).css ('margin-bottom', - block_height).show ();
          el.style.marginBottom = - block_height;
          el.style.display = 'block';
        }
      }
    });

    // ***
//    var sticky_background = $('.ai-sticky-background');
    var sticky_background = document.querySelectorAll ('.ai-sticky-background');
    // ***
//    sticky_background.each (function () {
    sticky_background.forEach ((el, i) => {
      if (ai_debug) console.log ('');

      if (main_width != 0) {

//        var block_width = $(this).width ();
//        var block_height = $(this).height ();
        var block_width = el.clientWidth;
        var block_height = el.clientHeight;

        if (ai_debug) console.log ('AI STICKY BLOCK:', block_width, 'x', block_height);

        // ***
//        $(this).removeClass ('ai-sticky-background').removeAttr ('data-aos');
        el.classList.remove ('ai-sticky-background');
        el.removeAttribute ('data-aos');
        if (typeof ai_preview === 'undefined') {
          // ***
//          $(this).find ('.ai-close-button').removeAttr ('class');
          var button = el.querySelector ('.ai-close-button');
          if (button != null) {
            button.removeAttribute ('class');
          }
        }

        // ***
//        if ($(this).hasClass ('ai-sticky-left')) {
        if (el.classList.contains ('ai-sticky-left')) {
          // ***
//          var background_width = main_element.offset().left;
          var background_width = main_element.offsetLeft;

          if (ai_debug) console.log ('AI STICKY BACKGROUND left:', background_width, 'px');

          // ***
//          $(this).css ('width', background_width + 'px').css ('overflow', 'hidden');
//          $(this).show ();
          el.style.width = background_width + 'px';
          el.style.overflow = 'hidden';
          el.style.display = 'block';
        } else
        // ***
//        if ($(this).hasClass ('ai-sticky-right')) {
        if (el.classList.contains ('ai-sticky-right')) {
          // ***
//          var background_width = client_width - (main_element.offset().left + main_width);
          var background_width = client_width - (main_element.offsetLeft + main_width);

          if (ai_debug) console.log ('AI STICKY BACKGROUND right:', background_width, 'px');

          // ***
//          $(this).css ('width', background_width + 'px').css ('overflow', 'hidden').css ('display', 'flex');
          el.style.width = background_width + 'px';
          el.style.overflow = 'hidden';
          el.style.display = 'flex';
        }

        // ***
//        if ($(this).hasClass ('ai-sticky-scroll')) {
        if (el.classList.contains ('ai-sticky-scroll')) {

          // ***
//          if (ai_debug) console.log ('AI STICKY scroll', $(this).attr ("class"), '=> MARGIN BOTTOM:', - block_height, 'px');
          if (ai_debug) console.log ('AI STICKY scroll', el.hasAttribute ("class") ? el.getAttribute ("class") : '', '=> MARGIN BOTTOM:', - block_height, 'px');

          // ***
//          $(this).css ('margin-bottom', - block_height).show ();
          el.style.marginBottom = - block_height;
          el.style.display = 'block';
        }
      }
    });
  }

  if (ai_debug && main_width == 0) console.log ("AI STICKY CONTENT NOT SET: MAIN WIDTH 0");

}

function ai_ready (fn) {
  if (document.readyState === 'complete' || (document.readyState !== 'loading' && !document.documentElement.doScroll)) {
    fn ();
  } else {
     document.addEventListener ('DOMContentLoaded', fn);
  }
}

//jQuery(document).ready(function($) {
function ai_init_sticky_elements () {
    // ***
//    setTimeout (function() {ai_process_sticky_elements (jQuery);}, ai_sticky_delay);
    setTimeout (function() {ai_process_sticky_elements ();}, ai_sticky_delay);
    if (typeof AOS != 'undefined' && typeof ai_no_aos_init == 'undefined') {
      setTimeout (function() {AOS.init();}, ai_sticky_delay + 10);
    }
//});
}

if (ai_process_sticky_elements_on_ready) {
  ai_ready (ai_init_sticky_elements);
}

}
