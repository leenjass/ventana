/**
 * Multishop Color Menu
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2019 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

$(document).ready(function () {
    if (multishopcolormenu.color) {
        $('#shop-list button, #header_shop .dropdown a.dropdown-toggle').first().attr('style', 'color: ' + multishopcolormenu.color + ' !important;');
        $('#shop-list button i.material-icons, #header_shop .dropdown i.material-icons').first().css('color', multishopcolormenu.color);
    }
    if (multishopcolormenu.back_color) {
        $('#shop-list, #header_shop').first().css('backgroundColor', multishopcolormenu.back_color);
    }
});
