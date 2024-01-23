jQuery (window).on ('load', function () {

  var ai_adb_debugging = typeof ai_debugging !== 'undefined'; // 1
//  var ai_adb_debugging = false;

  if (ai_adb_debugging) console.log ("AI AD BLOCKING window load pro");

  function ai_adb_9 () {
    if (typeof funAdBlock === "undefined") {
      if (!ai_adb_active || ai_debugging_active) ai_adb_detected (9);
    } else {
        var a9 = false;
        funAdBlock.onDetected (function () {if (!ai_adb_active || ai_debugging_active) {if (!a9) {a9 = true; ai_adb_detected (9);}}});
        funAdBlock.onNotDetected (function () {if (!a9) {a9 = true; ai_adb_undetected (9);}});
        funAdBlock.check ();
      }
  }

  function ai_adb_10 () {
    if (typeof badBlock === "undefined") {
        if (!ai_adb_active || ai_debugging_active) ai_adb_detected (10);
    } else {
        var a10 = false;
        badBlock.on (true, function () {if (!ai_adb_active || ai_debugging_active) {if (!a10) {a10 = true; ai_adb_detected (10);}}}).on (false, function () {if (!a10) {a10 = true; ai_adb_undetected (10);}});
        badBlock.check ();
    }

    badBlock = undefined;
    BadBlock = undefined;
  }

  setTimeout (function() {
    var ai_debugging_active = typeof ai_adb_fe_dbg !== 'undefined';

    // FuckAdBlock (v3.2.1)
    if (jQuery(b64d ("I2FpLWFkYi1hZHZlcnRpc2luZw==")).length) {
      if (typeof funAdBlock === "undefined") {
        ai_adb_get_script ('advertising', ai_adb_9);
      } else ai_adb_9 ();
    }

    // FuckAdBlock (4.0.0-beta.3)
    if (jQuery(b64d ("I2FpLWFkYi1hZHZlcnRz")).length) {
      if (typeof badBlock === "undefined") {
        ai_adb_get_script ('adverts', ai_adb_10);
      } else ai_adb_10 ();
    }
  }, 1100);
});

