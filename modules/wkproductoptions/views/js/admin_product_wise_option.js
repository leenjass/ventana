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
    var is_native_allowed = $("input[name='wk_disable_native_customization']:checked").val();
    if (is_native_allowed === 'undefined') {
        is_native_allowed = 0;
    }
    displayAssociatedOptionDiv(is_native_allowed);

    $(document).on('change', "input[name='wk_disable_native_customization']", function() {
        var is_native_allowed = $(this).val();
        displayAssociatedOptionDiv(is_native_allowed);
    });

    $(document).on("click", "#wk_select_option_all", function() {
        $(".wk_select_option").prop("checked", $(this).prop("checked"));
    });
    $(document).on("click", ".wk_select_option", function() {
        var idComb = [];
        $(".wk_select_option").each(function() {
            if ($(this).prop("checked")) {
                idComb.push(1);
            }
        });
        if (idComb.length == 0) {
            $("#wk_select_option_all").prop("checked", false);
        }
    })

    $(document).on('click', '.wk_option_bulk_apply', function(e) {
        e.preventDefault();
        var idComb = [];
        $(".wk_select_option").each(function() {
            if ($(this).prop("checked")) {
                idComb.push($(this).val());
            }
        });
        if (idComb.length === 0) {
            showErrorMessage(wk_select_comb);
        }
        if (typeof is_new_product_page != 'undefined' && is_new_product_page == 1) {
            var form = document.getElementsByName("product")[0];
        } else {
            var form = document.getElementById("form");
        }
        var formData = new FormData(form);
        formData.append("wk_comb", idComb);
        formData.append("id_product", $('#id_ps_product').val());
        formData.append("action", 'OptionBulkAction');
        $.ajax({
            url: wk_product_option_controller,
            type: "post",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(response) {
                if (response) {
                    $('#module_wkproductoptions').html(response.data);
                    $('[data-toggle="popover"]').popover();
                    showSuccessMessage(response.msg);
                }
            }
        });
    })
})

function displayAssociatedOptionDiv(is_native_allowed) {
    if (is_native_allowed == 1) {
        $('.wk_product_option_container').slideDown();
        $('#custom_fields').hide();
        $('#custom_fields').after('<div id="wk_customization_msg" class="alert alert-info">' + wk_customization_msg + '</div>');
    } else {
        $('.wk_product_option_container').slideUp();
        $('#custom_fields').show();
        $('#wk_customization_msg').remove();
    }
}