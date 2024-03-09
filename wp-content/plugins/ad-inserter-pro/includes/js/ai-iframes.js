function ai_resize_iframe (iframe) {
  function getDocHeight (doc) {
    doc = doc || document;
    // from http://stackoverflow.com/questions/1145850/get-height-of-entire-document-with-javascript
    var body = doc.body, html = doc.documentElement;
    var height = Math.max (body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);
    return height;
  }

  function resizeIframe (iframe) {
    var doc = iframe.contentDocument ? iframe.contentDocument : iframe.contentWindow.document;
    iframe.style.visibility = "hidden";
    iframe.style.height = "10px"; // reset to minimal height ...
    // IE opt. for bing/msn needs a bit added or scrollbar appears
    iframe.style.height = getDocHeight (doc) + "px";
    iframe.style.visibility = "visible";
  }

  if (typeof ai_iframe_resize_delay == "undefined") {
    ai_iframe_resize_delay = 200;
  }

  setTimeout (function(){resizeIframe (iframe);}, ai_iframe_resize_delay);
}
