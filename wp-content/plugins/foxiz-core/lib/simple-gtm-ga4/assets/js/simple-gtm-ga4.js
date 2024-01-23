var RB_GTM_PANEL = (function (Module, $) {
    'use strict';

    Module.init = function () {
        var infoWrap = $('#simple-gtm-ga4-response');
        var simpleGtmForm = $('#simple-gtm-form');
        var submitButton = $('.simple-gtm-submit');
        var isSubmitting = false;

        simpleGtmForm.on('submit', function (e) {
            e.preventDefault();

            if (isSubmitting) {
                return;
            }

            isSubmitting = true;
            submitButton.prop('disabled', true);

            var spinner = $('<span class="rb-loading"><i class="dashicons dashicons-update"></i></span>').insertAfter(submitButton);
            var formData = $(this).serialize() + '&nonce=' + $('#simple-gtm-ga4-nonce').val() + '&action=simple_gtm_save';

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                success: function (response) {
                    var infoClass = response.success ? 'notice-success' : 'notice-error';
                    infoWrap.html('<div class="simple-gtm-info ' + infoClass + '"><p>' + response.data + '</p></div>').show().delay(5000).fadeOut();
                    spinner.remove();
                    isSubmitting = false;
                    submitButton.prop('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    infoWrap.html('<div class="simple-gtm-info notice-error"><p><i class="dashicons-warning dashicons"></i>Form submission failed. Please try again!</p></div>').show().delay(5000).fadeOut();
                    spinner.remove();
                    isSubmitting = false;
                    submitButton.prop('disabled', false);
                }
            });
        });

        infoWrap.hover(function () {
            $(this).show();
        }, function () {
            $(this).fadeOut();
        });
    }

    return Module;

}(RB_GTM_PANEL || {}, jQuery));

/** init */
jQuery(document).ready(function ($) {
    RB_GTM_PANEL.init();
});
