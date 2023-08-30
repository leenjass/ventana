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
    afterPageLoadScript();
    // hide customization block for option on order-summary checkout page
    $('.wk-customization-option-modal').closest('.order-line').find('.customizations').css('display', 'none');
    if (typeof wk_ps_version != 'undefined' && wk_ps_version >= '8.1.0') {
        // Issue due to this PR : 29016
        prestashop.on("handleError", function(event) {
            if (event.eventType == 'addProductToCart') {
                var response = event.resp;
                if (response.hasError) {
                    totalErrors = response.errors;
                    var ajaxError = "";
                    $.each(totalErrors, (index, item) => {
                        ajaxError += item;
                        ajaxError += "<br>";
                    });
                    $(".wk-product-add-to-cart .ajax-error").html('<div class="alert alert-danger">' + ajaxError + '</div>').show();
                    $(".wk_ajax-error .alert").html(ajaxError);
                    $(".wk_ajax-error").show();
                }
            }
        })
    }
    // save option data on add to cart event
    prestashop.on("updateCart", function(event) {
        var form = document.getElementById("add-to-cart-or-refresh");
        var formData = new FormData(form);
        if (typeof event.resp != "undefined") {
            var response = event.resp;
            formData.append("action", "addProductOption");
            formData.append("id_product", response.id_product);
            formData.append("id_product_attribute", response.id_product_attribute);
            formData.append("id_customization", response.id_customization);
            if (response.success) {
                $.ajax({
                    url: wk_product_option_ajax,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function(data) {
                        $(".product-add-to-cart .ajax-error").hide();
                        $(".wk-product-add-to-cart .ajax-error").hide();
                        form.reset();
                        $(".wk_select_dropdown_multiple").select2('destroy');
                        $(".wk_select_dropdown_multiple").select2({ placeholder: wk_multi_select_placeholder });
                        $('.wk_option_file_contain').val('0');
                        $('.js-file-name').html(wk_no_file);
                        $('.wk_quickview').modal('hide');
                    },
                });
            } else if (response.hasError) {
                totalErrors = response.errors;
                var ajaxError = "";
                $.each(totalErrors, (index, item) => {
                    ajaxError += item;
                    ajaxError += "<br>";
                });
                $(".product-add-to-cart .ajax-error").html(ajaxError).show();
                $(".wk_ajax-error .alert").html(ajaxError);
                $(".wk_ajax-error").show();
            }
        }
    });
    const currentURL = window.location.href;
    const pageTitle = document.title;
    prestashop.on("updateProduct", function(event) {
        $('.wk_quickview').addClass('quickview');
        $('.quickview').removeClass('wk_quickview');
        setTimeout(function() {
            $("#wk_id_product_attribute_form").val($("#wk_id_product_attribute").val());
            $.ajax({
                url: wk_product_option_ajax,
                type: "post",
                async: true,
                dataType: "json",
                data: {
                    ajax: true,
                    action: "changeVariantTemplate",
                    token: secure_key,
                    id_product: $('#product_page_product_id').val(),
                    id_product_attribute: $("#wk_id_product_attribute").val(),
                },
                success: function(response) {
                    $(".wk_custom_variant").html(response.data);
                    afterPageLoadScript();
                },
            });
        }, 1000)
    });

    prestashop.on("updatedProduct", function(event) {
        if ($('.modal.wk_quickview.in').length > 0) {
            window.history.pushState(
                '',
                pageTitle,
                currentURL,
            );
        }
        updatePsPrice();
        $('.quickview').addClass('wk_quickview');
        $('.wk_quickview').removeClass('quickview');
    });
    prestashop.on("clickQuickView", function(event) {
        setTimeout(function() {
            $('.quickview').addClass('wk_quickview');
            $('.wk_quickview').removeClass('quickview');
            updatePsPrice();
            afterPageLoadScript(true);
        }, 1000)
    });
    $(document).on("change", '.js-file-input', function() {
        var imageId = $(this).attr("id");
        if (typeof this.files[0] != "undefined") {
            if (this.files[0].size > maxSizeAllowed * 1000000) {
                $("#" + imageId + "_contain").val(2);
            } else {
                if (this.files[0].size > 0) {
                    var form = document.getElementById("add-to-cart-or-refresh");
                    var formData = new FormData(form);
                    formData.append("action", "validateImage");
                    var fieldName = imageId;
                    formData.append("field_name", fieldName);
                    $.ajax({
                        url: wk_product_option_ajax,
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        dataType: "json",
                        success: function(data) {
                            if (data.status == 'fail') {
                                $("#" + imageId + "_contain").val(3);
                            } else {
                                $("#" + imageId + "_contain").val(1);
                            }
                        },
                    });
                }
            }
        }
    });
    $(document).on("click", ".wk_image_upload", function(e) {
        e.preventDefault();
        var idImage = $(this).data("id_image");
        $("#" + idImage).trigger("click");
    });
    $(document).on("click", ".wk_option_checkbox_parent", function(e) {
        updatePsPrice();
    });
    $(document).on('change', '.wk_select_dropdown_single', function(e) {
        var idField = $(this).data('id_field');
        var idValue = $(this).find(':selected').data('id-option-val');
        $('#wk_option_dropdown_value_id_' + idField).val(idValue);
        if ($(this).closest('.wk-product-variants-item').find('.wk_option_checkbox_parent').prop('checked') == true) {
            updatePsPrice();
        }
    })
    $(document).on('click', '.wk_checkbox_selected', function(e) {
        var idField = $(this).data('id_field');
        var selectedValue = $('#wk_option_checkbox_value_id_' + idField).val();
        var itemValue = $(this).data('id-check-val');
        if ($(this).prop('checked') == true) {
            selectedValue += "," + itemValue;
        } else {
            selectedValue = selectedValue.split(',');
            selectedValue = selectedValue.filter(function(item) {
                return item != itemValue
            })
            selectedValue = selectedValue.toString();
        }
        $('#wk_option_checkbox_value_id_' + idField).val(selectedValue);
        if ($(this).closest('.wk-product-variants-item').find('.wk_option_checkbox_parent').prop('checked') == true) {
            updatePsPrice()
        }
    })
    $('.wk_select_dropdown_multiple').on('change', function() {
        var idField = $(this).data('id_field');
        var selectedValue = $(this).val();
        $('#wk_option_dropdown_value_id_multi_' + idField).val(selectedValue.toString());
        if ($(this).closest('.wk-product-variants-item').find('.wk_option_checkbox_parent').prop('checked') == true) {
            updatePsPrice(); // vary price based on selected checkbox value
        }
    });
    $(document).on('click', '.wk_radio_selected', function(e) {
        var idField = $(this).data('id_field');
        var idValue = $(this).data('id-radio-val');
        $('#wk_option_radio_value_id_' + idField).val(idValue);
        if ($(this).closest('.wk-product-variants-item').find('.wk_option_checkbox_parent').prop('checked') == true) {
            updatePsPrice();
        }
    })
    $(document).on('click', '.wk_custom_addon', function() {
        $(this).closest('.input-group').find('input').focus();
    })
});

function afterPageLoadScript(is_quick_view = false) {
    $('.wk_product_opt_container [data-toggle="tooltip"]').tooltip();
    if (typeof wk_controller != 'undefined' && wk_controller == 'product') {
        updatePsPrice();
    }
    $(".wk_select_dropdown_multiple").select2({ placeholder: wk_multi_select_placeholder });
    $("#wk_id_product_attribute_form").val($("#wk_id_product_attribute").val());
    $(".wk_option_info_icon").mouseover(function() {
        var id = $(this).attr("data-id_info");
        $("#wk-hovercard" + id + "")
            .removeClass("wk-hovercard")
            .addClass("wk-hovercard_hover");
    });
    $(".wk_option_info_icon").mouseout(function() {
        var id = $(this).attr("data-id_info");
        $("#wk-hovercard" + id + "")
            .removeClass("wk-hovercard_hover")
            .addClass("wk-hovercard");
    });
    $(".wk_color_input").change(function() {
        var idParent = $(this).data("id_parent");
        $("#" + idParent).css("color", $(this).val());
    });
    $('.wk_product_option_date').datepicker({
        dateFormat: "yy-mm-dd",
        closeText: wkcloseText,
        prevText: wkprevText,
        nextText: wknextText,
        startDate: '1970-01-01',
        currentText: wkcurrentText,
        monthNames: wkMonthNameFull,
        monthNamesShort: wkMonthNameShort,
        dayNamesMin: wkDaysOfWeek,
    }).attr('readonly', 'readonly');
    $('.wk_product_option_time').timepicker({
        timeText: wktimeText,
        hourText: wkhourText,
        minuteText: wkminuteText,
        showSecond: true,
        secondText: wksecondText,
        timeFormat: 'hh:mm:ss',
        currentText: wkcurrentTimeText,
        closeText: wkcloseText,
        timeOnlyTitle: wktimeOnlyTitle,
    }).attr('readonly', 'readonly');
    $('.wk_product_option_datetime').datetimepicker({
        dateFormat: "yy-mm-dd",
        timeFormat: 'hh:mm:ss',
        timeText: wktimeText,
        hourText: wkhourText,
        closeText: wkcloseText,
        prevText: wkprevText,
        nextText: wknextText,
        currentText: wkcurrentText,
        monthNames: wkMonthNameFull,
        monthNamesShort: wkMonthNameShort,
        dayNamesMin: wkDaysOfWeek,
        showSecond: true,
        secondText: wksecondText,
    }).attr('readonly', 'readonly');
    if (is_quick_view) {
        setTimeout(function() {
            $('#ui-datepicker-div').css('z-index', '10000 !important');
            $('#ui-datepicker-div').addClass('wk_custom_index')
        }, 400)
    }
}

function updatePsPrice() {
    var options = [];
    var idValues = [];
    $(".wk_option_checkbox_parent:checked").each(function() {
        var id_option = $(this).val();
        var dropValueMulti = $('#wk_option_dropdown_value_id_multi_' + id_option).val();
        if (dropValueMulti) {
            dropValueMulti = dropValueMulti.split(',');
            $.each(dropValueMulti, function(index, dval) {
                idValues.push(dval);
            })
        }
        var dropcheckBox = $('#wk_option_checkbox_value_id_' + id_option).val();
        if (dropcheckBox) {
            dropcheckBox = dropcheckBox.split(',');
            $.each(dropcheckBox, function(index, cval) {
                idValues.push(cval);
            })
        }
        var dropValue = $('#wk_option_dropdown_value_id_' + id_option).val();
        if (dropValue) {
            idValues.push(dropValue);
        }
        var radioValue = $('#wk_option_radio_value_id_' + id_option).val();
        if (radioValue) {
            idValues.push(radioValue);
        }
        options.push(id_option);
    });
    var idProduct = $("#product_page_product_id").val();
    var idProductAttribute = $("#wk_id_product_attribute").val();
    $.ajax({
        url: wk_product_option_ajax,
        type: "post",
        async: true,
        dataType: "json",
        data: {
            ajax: true,
            action: "changeCatalogPrice",
            token: secure_key,
            selectedOption: options,
            id_product: idProduct,
            id_product_attribute: idProductAttribute,
            id_values: idValues
        },
        success: function(response) {
            $(".wk_price_option").html(response.price_tpl);
        },
    });
}