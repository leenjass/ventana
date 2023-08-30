/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

$(document).ready(function() {
    appendProductOptionInfoOrderLine();
    $(document).ajaxComplete(function(event, xhr, settings) {
        if (typeof settings.data !== "undefined") {
            var ajax_data = settings.data;
            var info_tab_ajax = ajax_data.match("action=editProductOnOrder");
            if (typeof info_tab_ajax !== "undefined" && info_tab_ajax !== null) {
                appendProductOptionInfoOrderLine();
            }
        }
        if (typeof wk_symfony_context !== "undefined") {
            if (typeof settings.url !== 'undefined') {
                var ajax_url = settings.url;
                var key = "/sell/orders/" + id_order + "/products/";
                var order_info_tab = ajax_url.match(key);
                if (typeof order_info_tab !== "undefined" && order_info_tab !== null) {
                    setTimeout(function() {
                        if ($('.wk_append_order_admin').length == 0) {
                            appendProductOptionInfoOrderLine();
                        }
                    }, 1000)
                }
            }
        }
    });
});

function appendProductOptionInfoOrderLine() {
    // code for symfony order controller
    if (typeof wk_symfony_context !== "undefined" && wk_symfony_context == 1) {
        if (typeof id_order !== "undefined") {
            if (id_order && contains_options) {
                $("#orderProductsPanel .cellProduct").each(function() {
                    var thisVar = $(this);
                    var identifier = $(this).attr("id");
                    identifier = identifier.match(/\d/g);
                    id_order_detail = identifier.join("");
                    if (id_order_detail) {
                        $.ajax({
                            url: product_option_controller,
                            type: "post",
                            async: true,
                            dataType: "json",
                            data: {
                                ajax: true,
                                action: "appendInProductLine",
                                id_order_detail: id_order_detail,
                                id_order: id_order,
                            },
                            success: function(response) {
                                if (response.data) {
                                    thisVar.find(".cellProductName a").after(response.data);
                                }
                            },
                        });
                    }
                });
            }
        }
    } else {
        // code for legacy order controller
        if (typeof id_order !== "undefined") {
            if (id_order && contains_options) {
                $("#orderProducts .product-line-row").each(function() {
                    var thisVar = $(this);
                    var id_order_detail = thisVar
                        .find('input[name="product_id_order_detail"]')
                        .val();
                    if (id_order_detail) {
                        $.ajax({
                            url: product_option_controller,
                            type: "post",
                            async: true,
                            dataType: "json",
                            data: {
                                ajax: true,
                                action: "appendInProductLine",
                                id_order_detail: id_order_detail,
                                id_order: id_order,
                            },
                            success: function(response) {
                                if (response.data) {
                                    thisVar
                                        .closest("tr")
                                        .find("td .productName")
                                        .parent()
                                        .after(response.data);
                                }
                            },
                        });
                    }
                });
            }
        }
    }
}