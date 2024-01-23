var RB_OPENAI_PANEL = (function (Module, $) {
    'use strict';

    Module.init = function () {
        var infoWrap = $('#rb-form-response');
        var rbAiAssistantForm = $('#rb-openai');
        var saveButton = $('#rb-submit-api');
        var isSubmitting = false;

        rbAiAssistantForm.on('submit', function (e) {
            e.preventDefault();

            if (isSubmitting) {
                return;
            }

            isSubmitting = true;
            saveButton.prop('disabled', true); // Disable the "Save" button

            var target = $(this);
            var spinner = $('<span class="rb-loading"><i class="dashicons dashicons-update"></i></span>').insertAfter(saveButton);
            var formData = target.serialize() + '&nonce=' + $('#rb-openai-nonce').val() + '&action=rb_openai_save';

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                success: function (response) {
                    var infoClass = response.success ? 'notice-success' : 'notice-error';
                    infoWrap
                        .html('<div class="rb-form-response ' + infoClass + '"><p>' + response.data + '</p></div>')
                        .show()
                        .delay(5000)
                        .fadeOut();
                    spinner.remove();
                    isSubmitting = false;
                    saveButton.prop('disabled', false); // Enable the "Save" button
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    infoWrap
                        .html('<div class="rb-form-response notice-error"><p><i class="dashicons-warning dashicons"></i>Form submission failed. Please try again!</p></div>')
                        .show()
                        .delay(5000)
                        .fadeOut();
                    spinner.remove();
                    isSubmitting = false;
                    saveButton.prop('disabled', false); // Enable the "Save" button
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

}(RB_OPENAI_PANEL || {}, jQuery));

/** init */
jQuery(document).ready(function ($) {
    RB_OPENAI_PANEL.init();
});
