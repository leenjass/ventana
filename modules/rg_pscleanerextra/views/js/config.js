/**
 * SEO Performance (PageSpeed, Lighthouse, Lazy Load)
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

$(document).ready(function() {
    $('.products-marketing-list').slick({
        arrows: false,
        dots: true,
        autoplay: true,
        infinite: false,
        speed: 800,
        slidesToShow: 4,
        slidesToScroll: 4,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true
                }
            }, {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            }, {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    $('input[type="radio"]').on('change', function() {
        var name = $(this).attr('name'),
            status = Boolean(parseInt($('input[name="' + name + '"]:checked').val()));
        $('.' + name).toggle(status);
    }).change();

    if ($('code.command').length) {
        $('#configuration_form input').on('change', function() {
            var type = $(this).attr('type'),
                name = $(this).attr('name'),
                val = $(this).val();

            if (type === 'text') {
                if (!Number(val) || !val) {
                    $(this).val(window[name]);
                } else {
                    $(this).val(Math.abs(parseInt(val)));
                }
            }

            $('code.command').html(cron_base_url + '&' + $('#configuration_form input:not([type=hidden])').serialize());
        }).change();
    }
});
