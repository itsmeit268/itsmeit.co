window.onscroll = function() {ai_scroll_update ()};

function ai_scroll_update () {
  var blocks = document.getElementsByClassName ("ai-parallax-background");

  for (var i = 0; i < blocks.length; i ++) {
    var rect = blocks [i].getBoundingClientRect ();

    var window_height = (window.innerHeight || document.documentElement.clientHeight) + rect.height;

    visible =
      rect.top + rect.height >= 0 &&
      rect.left >= 0 &&
      rect.bottom - rect.height <= (window.innerHeight || document.documentElement.clientHeight) &&
      rect.right <= (window.innerWidth || document.documentElement.clientWidth);

    if (visible) {
      var shift = parseInt (blocks [i].dataset.shift);
      blocks[i].style.backgroundPositionY = - shift * ((rect.top + rect.height) / window_height) + 'px';

      if (blocks[i].style.backgroundSize != 'cover') {
        var window_width  = (window.innerWidth  || document.documentElement.clientWidth);
        var hor_shift = parseInt (window_width / 2 - rect.left - rect.width / 2);
        blocks[i].style.left = hor_shift + 'px';
        blocks[i].style.transform = 'translate(' + (- hor_shift) + 'px)';
      }
    }
  }
}

setTimeout (function() {ai_scroll_update ();}, 100);

