(function ($) {

    $(function () {
        $('#pmpro_paypalexpress_checkout br').remove();
        $('#pmpro_paypalexpress_checkout').wrap('<div id="question-container"></div>');

        $('#question-container').prepend('<input type="text" name="answer" id="answer">');
        $('#question-container').prepend('<div id="question"></div>');

        var minimum = 1;
        var maximum = 10;
        var int1 = Math.floor(Math.random() * (maximum - minimum + 1)) + minimum;
        var int2 = Math.floor(Math.random() * (maximum - minimum + 1)) + minimum;
        $('#question').html( int1 + " " + "+" + " " + int2 + ' = ');
        var qanswer = int1 + int2;

        $('form#pmpro_form').submit( function (e) {
            e.preventDefault();
            var self = $(this);
            var answer_elm = $('#answer');
            var uanswer = parseInt(answer_elm.val());

            if (uanswer !== qanswer) {
                answer_elm.focus();
                answer_elm.css('border', '1px solid #f90000');
                jQuery('input[type=submit]').removeAttr('disabled');
                jQuery('input[type=image]').removeAttr('disabled');
                return false;
            }

            self.get(0).submit();
            // self.submit();

            return false;
        });
    });
})(jQuery);