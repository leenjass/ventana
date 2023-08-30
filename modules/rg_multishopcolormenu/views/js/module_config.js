/**
 * Multishop Color Menu
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
        infinite: true,
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

    /*
     * Parent options
     */
    var config_prefix = rg_multishopcolormenu.config_prefix;

    $('input[type="radio"][name^="' + config_prefix + '"], select[name^="' + config_prefix + '"]').on('change', function() {
        var name = $(this).attr('name'),
            opt = false;

        if ($(this).is('input')) {
            opt = $('input[name="' + name + '"]:checked').val();
        } else {
            opt = $(this).val();
        }

        $('.' + name).toggle(Boolean(parseInt(opt)));
        $('[class*="' + name + '_"]').toggle(false);
        $('.' + name + '_' + opt).toggle(true);
    }).change();
});
