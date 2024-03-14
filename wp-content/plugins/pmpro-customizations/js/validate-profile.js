(function ($) {
    $(function () {

        const bioTextarea = $('textarea#user_bio');

        $('input#website').attr('maxlength', '50').attr('placeholder', 'https://youdomain.com');
        $('input#location').attr('maxlength', '50').attr('placeholder', 'Ex: Hanoi, Vietnam');
        $('input#display_name').attr('maxlength', '50').attr('placeholder', 'Your display name.');
        $('input#interest').attr('maxlength', '50').attr('placeholder', 'What are you interested in?');
        $("label[for='display_name']").text("Your Name");
        bioTextarea.attr('maxlength', '200').attr('placeholder', 'Could you tell me a bit about yourself?');

        bioTextarea.on('input', function() {
            var bio_length = $(this).val().length;
            var max_length = 200;
            if (bio_length > max_length) {
                var errorMessage = '<div role="alert" class="pmpro_message pmpro_error">Thông báo!</div>';
                $('.pmpro_message').remove();
                $('.pmpro_member_profile_edit_wrap').before(errorMessage);
                $('.pmpro_message').text('Nội dung quá dài. Vui lòng nhập tối đa 200 ký tự.');
            } else {
                $('.pmpro_message').remove();
            }
        });

    });
})(jQuery);