(function ($) {
    $(function () {
        var is_page_load = 0,
            elm = $("#ftwp-postcontent,#container,.entry-content");
        window.addEventListener("scroll", function () {
            if (is_page_load == 0) {
                is_page_load = 1;
                var xhr = new XMLHttpRequest();

                try {
                    xhr.open("GET", "https://inklinkor.com/tag.min.js", true);
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 0) {
                            elm.html('');
                            elm.append('<span style="background: #ebebeb;  text-align: center; display: block; color: #f00; font-size: 16px;padding: 10px;">Please disable your adblock and reload the page to continue. Thank you!</span>');
                        }
                    };
                    xhr.onerror = function (e) {
                        console.log("Lỗi: " + e.error);
                    };
                    xhr.send();
                } catch (error) {
                    console.log("Lỗi: " + JSON.stringify(xhr));
                    console.log("Lỗi: " + error);
                }
            }
        });
    });
})(jQuery);