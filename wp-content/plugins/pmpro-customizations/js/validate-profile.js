(function ($) {
    $(function () {

        const max_length = 350;
        const bioTextarea = $('textarea#user_bio');

        function validate_input() {
            $('input#website').attr('maxlength', '50').attr('placeholder', 'https://youdomain.com');
            $('input#location').attr('maxlength', '50').attr('placeholder', 'Ex: Hanoi, Vietnam');
            $('input#display_name').attr('maxlength', '50').attr('placeholder', 'Your display name.');
            $('input#interest').attr('maxlength', '50').attr('placeholder', 'What are you interested in?');
            $("label[for='display_name']").text("Your Name");
            bioTextarea.attr('maxlength', '500').attr('placeholder', 'Could you tell me a bit about yourself?');
        }

        bioTextarea.on('input', function() {
            var textLength = bioTextarea.val().length;
            if (textLength > max_length) {
                checkTextLength();
                $('.btn-pmpro_submit').find('input').attr('disabled', 'disabled');
            } else {
                $('.pmpro_message').remove();
                $('.btn-pmpro_submit').find('input').removeAttr('disabled');
            }
        });

        bioTextarea.on('paste', function() {
            var self = this;
            setTimeout(function() {
                var pastedText = $(self).val();
                if (pastedText.length > max_length) {
                    checkTextLength();
                    $(self).val(pastedText.substring(0, max_length));
                    $('.btn-pmpro_submit').find('input').attr('disabled', 'disabled');
                } else {
                    $('.pmpro_message').remove();
                    $('.btn-pmpro_submit').find('input').removeAttr('disabled');
                }
            }, 100);
        });

        function checkTextLength() {
            var textLength = bioTextarea.val().length;
            if (textLength > max_length) {
                var errorMessage = '<div role="alert" class="pmpro_message pmpro_error"></div>';
                $('.pmpro_message').remove();
                $('.pmpro_member_profile_edit_wrap').before(errorMessage);
                $('.pmpro_message').text('Content is too long. Please enter a maximum of ' + max_length + ' characters.');
            } else {
                $('.pmpro_message').remove();
            }
        }

        $('.pmpro_btn-submit').on('click', function (e) {
            var textLength = bioTextarea.val().length;
            if (textLength > max_length) {
                e.preventDefault();
                checkTextLength();
                $('.btn-pmpro_submit').find('input').attr('disabled', 'disabled');
            } else {
                $('.pmpro_message').remove();
                $('.btn-pmpro_submit').find('input').removeAttr('disabled');
            }
            return true;
        });

        validate_input();
    });
})(jQuery);