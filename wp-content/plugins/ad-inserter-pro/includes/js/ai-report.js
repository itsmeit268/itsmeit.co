jQuery(document).ready(function($) {

  var dateFormat = "yy-mm-dd";
  var default_range = true;

  function b64e (str) {
    // first we use encodeURIComponent to get percent-encoded UTF-8,
    // then we convert the percent encodings into raw bytes which
    // can be fed into btoa.
    return btoa (encodeURIComponent (str).replace (/%([0-9A-F]{2})/g,
      function toSolidBytes (match, p1) {
        return String.fromCharCode ('0x' + p1);
    }));
  }

  function b64d (str) {
    // Going backwards: from bytestream, to percent-encoding, to original string.
    return decodeURIComponent (atob (str).split ('').map (function(c) {
      return '%' + ('00' + c.charCodeAt (0).toString (16)).slice (-2);
    }).join (''));
  }

// http://www.myersdaily.org/joseph/javascript/md5.js

  function md5cycle (x, k) {
    var a = x[0],
      b = x[1],
      c = x[2],
      d = x[3];
    a = ff(a, b, c, d, k[0], 7, -680876936);
    d = ff(d, a, b, c, k[1], 12, -389564586);
    c = ff(c, d, a, b, k[2], 17, 606105819);
    b = ff(b, c, d, a, k[3], 22, -1044525330);
    a = ff(a, b, c, d, k[4], 7, -176418897);
    d = ff(d, a, b, c, k[5], 12, 1200080426);
    c = ff(c, d, a, b, k[6], 17, -1473231341);
    b = ff(b, c, d, a, k[7], 22, -45705983);
    a = ff(a, b, c, d, k[8], 7, 1770035416);
    d = ff(d, a, b, c, k[9], 12, -1958414417);
    c = ff(c, d, a, b, k[10], 17, -42063);
    b = ff(b, c, d, a, k[11], 22, -1990404162);
    a = ff(a, b, c, d, k[12], 7, 1804603682);
    d = ff(d, a, b, c, k[13], 12, -40341101);
    c = ff(c, d, a, b, k[14], 17, -1502002290);
    b = ff(b, c, d, a, k[15], 22, 1236535329);
    a = gg(a, b, c, d, k[1], 5, -165796510);
    d = gg(d, a, b, c, k[6], 9, -1069501632);
    c = gg(c, d, a, b, k[11], 14, 643717713);
    b = gg(b, c, d, a, k[0], 20, -373897302);
    a = gg(a, b, c, d, k[5], 5, -701558691);
    d = gg(d, a, b, c, k[10], 9, 38016083);
    c = gg(c, d, a, b, k[15], 14, -660478335);
    b = gg(b, c, d, a, k[4], 20, -405537848);
    a = gg(a, b, c, d, k[9], 5, 568446438);
    d = gg(d, a, b, c, k[14], 9, -1019803690);
    c = gg(c, d, a, b, k[3], 14, -187363961);
    b = gg(b, c, d, a, k[8], 20, 1163531501);
    a = gg(a, b, c, d, k[13], 5, -1444681467);
    d = gg(d, a, b, c, k[2], 9, -51403784);
    c = gg(c, d, a, b, k[7], 14, 1735328473);
    b = gg(b, c, d, a, k[12], 20, -1926607734);
    a = hh(a, b, c, d, k[5], 4, -378558);
    d = hh(d, a, b, c, k[8], 11, -2022574463);
    c = hh(c, d, a, b, k[11], 16, 1839030562);
    b = hh(b, c, d, a, k[14], 23, -35309556);
    a = hh(a, b, c, d, k[1], 4, -1530992060);
    d = hh(d, a, b, c, k[4], 11, 1272893353);
    c = hh(c, d, a, b, k[7], 16, -155497632);
    b = hh(b, c, d, a, k[10], 23, -1094730640);
    a = hh(a, b, c, d, k[13], 4, 681279174);
    d = hh(d, a, b, c, k[0], 11, -358537222);
    c = hh(c, d, a, b, k[3], 16, -722521979);
    b = hh(b, c, d, a, k[6], 23, 76029189);
    a = hh(a, b, c, d, k[9], 4, -640364487);
    d = hh(d, a, b, c, k[12], 11, -421815835);
    c = hh(c, d, a, b, k[15], 16, 530742520);
    b = hh(b, c, d, a, k[2], 23, -995338651);
    a = ii(a, b, c, d, k[0], 6, -198630844);
    d = ii(d, a, b, c, k[7], 10, 1126891415);
    c = ii(c, d, a, b, k[14], 15, -1416354905);
    b = ii(b, c, d, a, k[5], 21, -57434055);
    a = ii(a, b, c, d, k[12], 6, 1700485571);
    d = ii(d, a, b, c, k[3], 10, -1894986606);
    c = ii(c, d, a, b, k[10], 15, -1051523);
    b = ii(b, c, d, a, k[1], 21, -2054922799);
    a = ii(a, b, c, d, k[8], 6, 1873313359);
    d = ii(d, a, b, c, k[15], 10, -30611744);
    c = ii(c, d, a, b, k[6], 15, -1560198380);
    b = ii(b, c, d, a, k[13], 21, 1309151649);
    a = ii(a, b, c, d, k[4], 6, -145523070);
    d = ii(d, a, b, c, k[11], 10, -1120210379);
    c = ii(c, d, a, b, k[2], 15, 718787259);
    b = ii(b, c, d, a, k[9], 21, -343485551);
    x[0] = add32(a, x[0]);
    x[1] = add32(b, x[1]);
    x[2] = add32(c, x[2]);
    x[3] = add32(d, x[3]);
  }
  function cmn(q, a, b, x, s, t) {
    a = add32(add32(a, q), add32(x, t));
    return add32((a << s) | (a >>> (32 - s)), b);
  }
  function ff(a, b, c, d, x, s, t) {
    return cmn((b & c) | ((~b) & d), a, b, x, s, t);
  }
  function gg(a, b, c, d, x, s, t) {
    return cmn((b & d) | (c & (~d)), a, b, x, s, t);
  }
  function hh(a, b, c, d, x, s, t) {
    return cmn(b ^ c ^ d, a, b, x, s, t);
  }
  function ii(a, b, c, d, x, s, t) {
    return cmn(c ^ (b | (~d)), a, b, x, s, t);
  }
  function md51(s) {
    txt = '';
    var n = s.length,
      state = [1732584193, -271733879, -1732584194, 271733878],
      i;
    for (i = 64; i <= s.length; i += 64) {
      md5cycle(state, md5blk(s.substring(i - 64, i)));
    }
    s = s.substring(i - 64);
    var tail = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    for (i = 0; i < s.length; i++)
      tail[i >> 2] |= s.charCodeAt(i) << ((i % 4) << 3);
    tail[i >> 2] |= 0x80 << ((i % 4) << 3);
    if (i > 55) {
      md5cycle(state, tail);
      for (i = 0; i < 16; i++) tail[i] = 0;
    }
    tail[14] = n * 8;
    md5cycle(state, tail);
    return state;
  }
  /* there needs to be support for Unicode here,
   * unless we pretend that we can redefine the MD-5
   * algorithm for multi-byte characters (perhaps
   * by adding every four 16-bit characters and
   * shortening the sum to 32 bits). Otherwise
   * I suggest performing MD-5 as if every character
   * was two bytes--e.g., 0040 0025 = @%--but then
   * how will an ordinary MD-5 sum be matched?
   * There is no way to standardize text to something
   * like UTF-8 before transformation; speed cost is
   * utterly prohibitive. The JavaScript standard
   * itself needs to look at this: it should start
   * providing access to strings as preformed UTF-8
   * 8-bit unsigned value arrays.
   */
  function md5blk(s) { /* I figured global was faster.   */
    var md5blks = [],
      i; /* Andy King said do it this way. */
    for (i = 0; i < 64; i += 4) {
      md5blks[i >> 2] = s.charCodeAt(i) +
        (s.charCodeAt(i + 1) << 8) +
        (s.charCodeAt(i + 2) << 16) +
        (s.charCodeAt(i + 3) << 24);
    }
    return md5blks;
  }
  var hex_chr = '0123456789abcdef'.split('');
  function rhex(n) {
    var s = '',
      j = 0;
    for (; j < 4; j++)
      s += hex_chr[(n >> (j * 8 + 4)) & 0x0F] +
      hex_chr[(n >> (j * 8)) & 0x0F];
    return s;
  }
  function hex(x) {
    for (var i = 0; i < x.length; i++)
      x[i] = rhex(x[i]);
    return x.join('');
  }
  function md5(s) {
    return hex(md51(s));
  }
  /* this function is much faster,
  so if possible we use it. Some IEs
  are the only ones I know of that
  need the idiotic second function,
  generated by an if clause.  */
  function add32(a, b) {
    return (a + b) & 0xFFFFFFFF;
  }
  if (md5('hello') != '5d41402abc4b2a76b9719d911017c592') {
    function add32(x, y) {
      var lsw = (x & 0xFFFF) + (y & 0xFFFF),
        msw = (x >> 16) + (y >> 16) + (lsw >> 16);
      return (msw << 16) | (lsw & 0xFFFF);
    }
  }

  function configure_elycharts () {
    $.elycharts.templates['ai'] = {
      type : "line",
      margins : [10, 38, 20, 38],
      defaultSeries : {
        fill: true,
        fillProps: {
          opacity: .15
        },
        plotProps : {
          "stroke-width" : 1,
        },
      },
      series : {
        serie1 : {
          color : "#66f",
          rounded : 0.8,
        },
        serie2 : {
          color : "#888",
          axis : "r",
          fillProps: {
            opacity: .1
          },
        }
      },
      defaultAxis : {
        labels : true,
        min: 0,
      },
      features : {
        grid : {
          draw : true,
          forceBorder : true,
          ny: 5,
          ticks : {
            active : [true, true, true],
            size : [4, 0],
            props : {
              stroke: '#ccc',
            }
          }
        },
      },
      interactive: false
    }

    $.elycharts.templates['ai-clicks'] = {
      template: 'ai',
      series : {
        serie1 : {
          color : "#0a0",
          fillProps: {
            opacity: .2
          },
        },
        serie2 : {
          color : "#888",
        }
      },
    }

    $.elycharts.templates['ai-impressions'] = {
      template: 'ai',
      series : {
        serie1 : {
          color : "#66f",
        },
        serie2 : {
          color : "#888",
        }
      },
    }

    $.elycharts.templates['ai-ctr'] = {
      template: 'ai',
      series : {
        serie1 : {
          color : "#e22",
        },
        serie2 : {
          color : "#888",
        }
      },
    }

    $.elycharts.templates['ai-versions'] = {
      type : "line",
      margins : [10, 38, 20, 38],
      defaultSeries: {
        color: "#0a0",
        fillProps: {
          opacity: .2
        },
        plotProps : {
          "stroke-width" : 2,
        },
        tooltip : {
          frameProps : {
           opacity : 0.8
          }
        },
        rounded : 0.8,
      },
      series: {
        serie1: {
          color : "#aaa",
          axis : "l",
        },
        serie2 : {
          color : "#0a0",
          axis : "r",
        },
        serie3 : {
          color: "#33f",
        },
        serie4 : {
          color : "#e22",
        },
        serie5 : {
          color : "#e2f",
        },
        serie6 : {
          color : "#ec6400",
        },
        serie7 : {
          color : "#00a3b5",
        },
        serie8 : {
          color : "#7000ff",
        },
        serie9 : {
          color : "#000",
        },
        serie10 : {
          color : "#000",   // Used also for BLOCKED
        },
      },
      defaultAxis : {
        labels : true,
        min: 0,
      },
      features : {
        grid: {
          draw: true,
          forceBorder : true,
          ny: 5,
          ticks : {
            active : [true, true, true],
            size : [4, 0],
            props : {
              stroke: '#ccc',
            }
          }
        },
      },
      interactive: true,
    }

    $.elycharts.templates['ai-versions-legend'] = {
      template: 'ai-versions',
      margins : [10, 38, 10, 38],
      defaultSeries : {
        fill: true,
        fillProps: {
          opacity: 0
        },
        plotProps : {
          "stroke-width" : 0,
        },
      },
      defaultAxis : {
        labels : false,
      },
      features: {
        grid: {
          draw: false,
          props: {
            stroke: "transparent",
          },
          ticks : {
            active : false,
          }
        },
        legend: {
          horizontal : true,
          x : 20, // X | auto, (auto solo per horizontal = true)
          y : 0,
          width : 540, // X | auto, (auto solo per horizontal = true)
          height : 20,
          itemWidth : "auto", // fixed | auto, solo per horizontal = true
          borderProps: { fill : "white", stroke: "black", "stroke-width": 0},
        },
      },
    }

    $.elycharts.templates['ai-pie'] = {
      template: 'ai-versions',
      type: "pie",
      rPerc: 100,
      startAngle: 270,
      clockwise: true,
      margins : [0, 0, 0, 0],
      defaultSeries : {
        tooltip: {
          height: 55,
          width: 120,
          padding: [5, 5],
          offset: [-15, -10],
          frameProps: {
              opacity: 0.95,
              /* fill: "white", */
              stroke: "#000"

          }
        },
        plotProps : {
         stroke : "white",
         "stroke-width" : 0,
         opacity : 1
        },
        values : [{
         plotProps : {
          fill : "#aaa"
         }
        }, {
         plotProps : {
          fill : "#0a0"
         }
        }, {
         plotProps : {
          fill : "#33f"
         }
        }, {
         plotProps : {
          fill : "#e22"
         }
        }, {
         plotProps : {
          fill : "#e2f"
         }
        }, {
         plotProps : {
          fill : "#ec6400"
         }
        }, {
         plotProps : {
          fill : "#00a3b5"
         }
        }, {
         plotProps : {
          fill : "#7000ff"
         }
        }, {
         plotProps : {
          fill : "#000"
         }
        }, {
         plotProps : {
          fill : "#000"   // Used also for BLOCKED
         }
        }]
      }
    }

    $.elycharts.templates['ai-bar'] = {
      template: 'ai-pie',
      type: "line",
      margins : [5, 0, 5, 45],
      barMargins : 1,
      defaultSeries : {
        type: "bar",
        axis: "l",
        tooltip: {
          height: 38,
        }
      },
      features: {
        grid: {
          draw: [false, false],
          props : {stroke: '#e0e0e0', "stroke-width": 0},
          ticks : {
            props : {stroke: '#e0e0e0', "stroke-width": 0},
          }
        },
      },
    }

  }

  function getDate (element) {
    var date;
    try {
      date = $.datepicker.parseDate (dateFormat, element.val ());
    } catch (error) {
      date = null;
    }

    return date;
  }

  function process_chart_dates () {
    var start_date_picker = $("input#chart-start-date");
    var end_date_picker   = $("input#chart-end-date");
    var start_date = getDate (start_date_picker);
    var end_date   = getDate (end_date_picker);

    start_date_picker.attr ('title', '');
    start_date_picker.css ("border-color", "rgb(221, 221, 221)");
    end_date_picker.attr ('title', '');
    end_date_picker.css ("border-color", "rgb(221, 221, 221)");

    if (start_date == null) {
      end_date_picker.attr ('title', '');
    } else
    if (end_date == null) {
      end_date_picker.attr ('title', '');
    } else
    if (end_date > start_date) {
      var now = new Date();
      var today_date = new Date (now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0, 0);
      if (today_date - start_date > 366 * 24 * 3600 * 1000) {
        start_date_picker.attr ('title', ai_admin.invalid_start_date);
        start_date_picker.css ("border-color", "#d00");
      }
      if (end_date - start_date > 366 * 24 * 3600 * 1000) {
        end_date_picker.attr ('title', ai_admin.invalid_date_range);
        end_date_picker.css ("border-color", "#d00");
      }
    } else {
        end_date_picker.attr ('title', ai_admin.invalid_end_date);
        end_date_picker.css ("border-color", "#d00");
      }
  }

  function configure_chart (container) {
    var ai_adb_flag_blocked = 0x80;

    if (!$(container).hasClass ('not-configured')) return;
    var template = $(container).data ('template');

    if (typeof template != 'undefined') {
      var new_colors = [];
      var color_indexes = $(container).data ('colors');
      if (typeof color_indexes != 'undefined') {
        var colors = $.elycharts.templates['ai-pie'].defaultSeries.values;
        color_indexes.forEach (function (element) {
          if (element == ai_adb_flag_blocked )
            new_colors.push (colors [9]); else
              new_colors.push (colors [element]);
        });
      }

      var values = $(container).data ('values-1');
      if (values == null) values = $(container).data ('values-2');
      if (values == null) values = $(container).data ('values-3');
      if (values == null) values = $(container).data ('values-4');
      if (values == null) values = $(container).data ('values-5');
      if (values == null) values = $(container).data ('values-6');
      if (values == null) values = $(container).data ('values-7');
      if (values == null) values = $(container).data ('values-8');
      if (values == null) values = $(container).data ('values-9');

      var legend = $(container).data ('legend');
      if (typeof legend != 'undefined' && typeof legend ['serie' + (ai_adb_flag_blocked + 1)] != 'undefined') {
        var new_legend = {};
        for (var legend_item in legend) {
          if (legend_item == 'serie' + (ai_adb_flag_blocked + 1))
            new_legend ['serie10'] = legend [legend_item]; else
              new_legend [legend_item] = legend [legend_item];
        }
        legend = new_legend;
      }

      $(container).chart({
        template: template,
        labels:   $(container).data ('labels'),
        values: {
          serie1: values,
          serie2: $(container).data ('values-2'),
          serie3: $(container).data ('values-3'),
          serie4: $(container).data ('values-4'),
          serie5: $(container).data ('values-5'),
          serie6: $(container).data ('values-6'),
          serie7: $(container).data ('values-7'),
          serie8: $(container).data ('values-8'),
          serie9: $(container).data ('values-9'),
          serie10: $(container).data ('values-' + (ai_adb_flag_blocked + 1)),  // BLOCKED
        },
        legend: legend,
        tooltips: {serie1: $(container).data ('tooltips')},
        defaultSeries: {values: new_colors, tooltip: {height: $(container).data ('tooltip-height')}},
        defaultAxis : {
          max: $(container).data ('max'),
        },
        features: {
          grid: {
            draw: values == null ? true : values.length < 50,
          }
        }
      });

      $(container).removeClass ('not-configured');
      $(container).parent().find ('div.ai-chart-label').show ();
      $(container).parent().find ('div.ai-chart-version-label').show ();
    }
  }

  function configure_charts (container) {
    $(container).find ('.ai-chart.not-configured').each (function() {
      if (!$(this).hasClass ('hidden')) {
        $(this).attr ('style', '');
        configure_chart (this);
      }
    });
  }

  function configure_report () {
    $("input#load-custom-range").click (function () {
      $('#ai-loading').show ();

      var label = $(this).next ().find ('.checkbox-icon');
      label.addClass ('on');

      var block = $("#statistics-container").attr('data-block');
      var adb = $("#statistics-container").attr('data-adb');
      var range = $("#statistics-container").attr('data-range');
      var version = $("#statistics-container").attr('data-version');
      var nonce = $("#statistics-container").attr('data-nonce');

      var start_date = $("input#chart-start-date").attr('value');
      var end_date = $("input#chart-end-date").attr('value');

      var container = $("div#statistics-elements");

      var version_charts_container = $("div#ai-version-charts-" + block);
      var version_charts_container_visible = version_charts_container.is (':visible');

      var block_string = block + "";
      while (block_string.length < 2) block_string = "0" + block_string;

      if (version == '') version = '---';
      var version_string = version + "";
      while (version_string.length < 3) version_string = "0" + version_string;

      var report = start_date + end_date + block_string + '0' + adb + range + version_string;
      var report_id = b64e (report).replace ('+', '.').replace ('/', '_').replace ('=', '-');

      container.load (ajaxurl+"?action=ai_ajax&ai-report-data=" + nonce + md5 (report).substring (0, 2) + report_id, function (response, status, xhr) {
        label.removeClass ('on');
        $('#ai-loading').hide ();
        if (status == "error" ) {
          var message = "Error downloading data: " + xhr.status + " " + xhr.statusText ;
          $("div#load-error").html (message);
          if (debug) console.log (message);
        } else {
            $("#custom-range-controls").show ();
            container.find ('span.ai-statistics-export-data.ai-public-report').remove ();

            $('#ai-header-info .ai-header-desc').text ($('.ai-statistics-export-data.ai-date-range-text', container).text ());

            $( "div#load-error").html ('');
            if (debug) console.log ("Report loaded:", start_date, end_date, block, adb);
            configure_charts (container);

            container.find ("label.ai-version-charts-button.not-configured").click (function () {
              var no_delay_version_charts = $(this).hasClass ('no-version-charts-delay');

              $(this).removeClass ('not-configured');
              var version_charts_container = $(this).closest (".ai-charts").find ('div.ai-version-charts');
              version_charts_container.toggle ();

              var not_configured_charts = version_charts_container.find ('.ai-chart.not-configured.hidden');
              if (not_configured_charts.length) {
                not_configured_charts.each (function() {
                  $(this).removeClass ('hidden');
                });
                if (no_delay_version_charts) {
                  configure_charts (version_charts_container);
                } else setTimeout (function() {configure_charts (version_charts_container);}, 10);
              }
            });

            if (version_charts_container_visible) {
              container.find ("label.ai-version-charts-button.not-configured").addClass ('no-version-charts-delay').click ();
            }

            $("input#chart-start-date").css ('color', '#32373c');
            $("input#chart-end-date").css ('color', '#32373c');
          }
      });
    });

    $("input#chart-start-date").datepicker ({dateFormat: dateFormat, autoSize: true});
    $("input#chart-end-date").datepicker ({dateFormat: dateFormat, autoSize: true});

    $("input#chart-start-date").change (function() {
      $('#ai-header-title-desc .ai-header-desc .ai-header-desc-details').text ('');
      $("#statistics-container").attr ('data-range', '----');
      default_range = false;

      var custom_range_controls = $("div#custom-range-controls");
      custom_range_controls.find ('.data-range').removeClass ('selected');

      $(this).css ('color', 'red');
      var block = $("#statistics-container").attr('data-block');
      process_chart_dates (block);
      $(this).attr ("value", $(this).val ());

    });

    $("input#chart-end-date").change (function() {
      $('#ai-header-title-desc .ai-header-desc .ai-header-desc-details').text ('');
      $("#statistics-container").attr ('data-range', '----');
      default_range = false;

      var custom_range_controls = $("div#custom-range-controls");
      custom_range_controls.find ('.data-range').removeClass ('selected');

      $(this).css ('color', 'red');
      var block = $("#statistics-container").attr('data-block');
      process_chart_dates (block);
      $(this).attr ("value", $(this).val ());
    });

    $("div#custom-range-controls"+" span.data-range").click (function () {
      $("input#chart-start-date").attr ("value", $(this).data ("start-date"));
      $("input#chart-start-date").val ($(this).data ("start-date"));
      $("input#chart-end-date").attr ("value", $(this).data ("end-date"));
      $("input#chart-end-date").val ($(this).data ("end-date"));

      var custom_range_controls = $("div#custom-range-controls");
      custom_range_controls.find ('.data-range').removeClass ('selected');
      $(this).addClass ('selected');
      $('#ai-header-title-desc .ai-header-desc .ai-header-desc-details').text ('');
      $("#statistics-container").attr ('data-range', $(this).data ("range-name"));
      default_range = false;

      var block = $("#statistics-container").attr('data-block');
      process_chart_dates (block);
      $("input#load-custom-range").click ();
    });
  }

  function reload_report () {
    if (default_range) {
      $("input#load-custom-range").click ();
      setTimeout (function() {reload_report ();}, 3600 * 1000);
    }
  }

  var ajaxurl = $("#statistics-container").attr('data-ajaxurl');
  var debug = $("#statistics-container").attr('data-debug');

  configure_elycharts ();
  configure_report ();

  $("div#statistics-container").show ();
  reload_report ();
});
