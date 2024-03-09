//jQuery (window).on ('load', function () {
// ***
window.addEventListener ('load', (event) => {

  var ai_adb_debugging = typeof ai_debugging !== 'undefined'; // 1
//  var ai_adb_debugging = false;

  if (typeof MobileDetect !== "undefined") {
    var md = new MobileDetect (window.navigator.userAgent);

    // ENABLED FOR ALL_DEVICES
    if (ai_adb_devices != 6) {

      if (ai_adb_debugging) console.log ('AI AD BLOCKING DEVICES:', ai_adb_devices);
      if (ai_adb_debugging) console.log ('AI AD BLOCKING DEVICE desktop',  !md.mobile ());
      if (ai_adb_debugging) console.log ('AI AD BLOCKING DEVICE  mobile', !!md.mobile ());
      if (ai_adb_debugging) console.log ('AI AD BLOCKING DEVICE   phone', !!md.phone ());
      if (ai_adb_debugging) console.log ('AI AD BLOCKING DEVICE  tablet', !!md.tablet ());

      switch (ai_adb_devices) {
        // ENABLED FOR DESKTOP_DEVICES
        case 0:
          if (!!md.mobile ()) return false;
          break;
        // ENABLED FOR MOBILE_DEVICES
        case 1:
          if (!md.mobile ()) return false;
          break;
        // ENABLED FOR TABLET_DEVICES
        case 2:
          if (!md.tablet ()) return false;
          break;
        // ENABLED FOR PHONE_DEVICES
        case 3:
          if (!md.phone ()) return false;
          break;
        // ENABLED FOR DESKTOP_TABLET_DEVICES
        case 4:
          if (!!md.phone ()) return false;
          break;
        // ENABLED FOR DESKTOP_PHONE_DEVICES
        case 5:
          if (!!md.tablet ()) return false;
          break;
      }
    }
  }

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
//    if (jQuery(b64d ("I2FpLWFkYi1hZHZlcnRpc2luZw==")).length) {
    // ***
    if (document.querySelector (b64d ('I2FpLWFkYi1hZHZlcnRpc2luZw==')) != null) {
      if (typeof funAdBlock === "undefined") {
        ai_adb_get_script ('advertising', ai_adb_9);
      } else ai_adb_9 ();
    }

    // FuckAdBlock (4.0.0-beta.3)
//    if (jQuery(b64d ("I2FpLWFkYi1hZHZlcnRz")).length) {
    // ***
    if (document.querySelector (b64d ('I2FpLWFkYi1hZHZlcnRz')) != null) {
      if (typeof badBlock === "undefined") {
        ai_adb_get_script ('adverts', ai_adb_10);
      } else ai_adb_10 ();
    }
  }, 1100);
});

