(function ($) {
    $(function () {
        var $point_number = $('.point-number');

        function level_name(user_point){
            var level_name = 'FREE';
            if (user_point > 10 && user_point < 50000) {
                level_name = 'GOLD';
            } else if (user_point >= 50000 && user_point < 100000) {
                level_name = 'PREMIUM';
            } else if(user_point >= 100000) {
                level_name = 'VIP';
            }
            return level_name;
        }

        function sendAjaxRequest() {
            $.ajax({
                url: update_point._ajax_url,
                type: 'post',
                data: {
                    action: 'update_user_points',
                },
                success: function(response) {
                    var currentPoints = parseFloat($point_number.text().replace('.', '')) || 0;
                    var newPoints = currentPoints + 50;
                    var formattedPoints = newPoints % 1 === 0 ? newPoints.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.') : newPoints.toFixed(3).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    $point_number.text(formattedPoints);
                    $point_number.css('color', '#095813');
                    $('.show-level').text(' (' + level_name(newPoints) + ')');
                    $('.level-mb').text(level_name(newPoints));
                    var levelColor;
                    switch (level_name(newPoints)) {
                        case 'PREMIUM':
                            levelColor = '#0c8b1d';
                            break;
                        case 'VIP':
                            levelColor = '#ff0000';
                            break;
                        case 'GOLD':
                            levelColor = '#ed8300';
                            break;
                        default:
                            levelColor = '#00b38f';
                    }
                    $('.level-mb, .show-level').css('color', levelColor);
                }
            });
        }

        setInterval(sendAjaxRequest, 5 * 60 * 1000);
    });
})(jQuery);