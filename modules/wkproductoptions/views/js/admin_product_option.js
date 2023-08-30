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
    if ($('.wk_product_append').length <= 0) {
        $('#option_selected_product').hide();
    }
    $('.wk-calender-from').click(function() {
        $('#option_from').trigger('focus');
    })
    $('.wk-calender-to').click(function() {
        $('#option_to').trigger('focus');
    })

    $(function() {
        $("#option_to, #option_from").datetimepicker({
            showSecond: true,
            dateFormat: "yy-mm-dd",
            timeFormat: "hh:mm:ss",
            currentText: wkcurrentText,
            closeText: wkcloseText,
            timeSuffix: '',
            timeOnlyTitle: wktimeOnlyTitle,
            timeText: wktimeText,
            hourText: wkhourText,
            minuteText: wkminuteText,
            secondText: wksecondText,
            millisecText: wkmillisecText,
            microsecText: wkmicrosecText,
            timezoneText: wktimezoneText,
            container: ".wkoptiondate",
            beforeShow: function(input, inst) {
                $(inst.dpDiv).removeClass("modaldatetime_height");
            },
        });
    });
    // manage multiple products
    $(document).on(
        "click",
        "#submitAddwk_product_options_configAndStay,#submitAddwk_product_options_config",
        function() {
            $(this).addClass("wk_option_disabled");
        }
    );
    setTimeout(function() {
        $(
            "#submitAddwk_product_options_configAndStay, #submitAddwk_product_options_config"
        ).removeClass("wk_option_disabled");
    }, 1000);
    // End- manage multiple products
    // ajax process for product searching
    $(document).on("keyup", "#wk_option_product_selection", function(event) {
        idProducts = [];
        $('#wk_product_options_config_form input[name="idProducts[]"]').each(
            function() {
                idProducts.push($(this).val());
            }
        );

        $("#wk_option_product_selection_value").html("");
        var xhr = "";
        if (xhr) {
            xhr.abort();
        }

        var search = $.trim($(this).val());
        if (search.length >= 1) {
            xhr = $.ajax({
                url: option_product_ajax,
                data: {
                    idProducts: idProducts,
                    productname: search,
                    action: "SearchProductsByName",
                    ajax: true,
                },
                method: "POST",
                dataType: "JSON",
                async: true,
                success: function(data) {
                    if (data.length > 0) {
                        $.each(data, function(key, value) {
                            $("#wk_option_product_selection_value")
                                .show()
                                .append(
                                    "<li data-id-product='" +
                                    value.id_product +
                                    "' data-product-img='" +
                                    value.img_path +
                                    "' " +
                                    "' data-product-link='" +
                                    value.product_link +
                                    "' class='product_value wk-dropdown-li'>" +
                                    value.name +
                                    "</li>"
                                );
                        });
                    } else {
                        $("#wk_option_product_selection_value").html("");
                        $("#wk_option_product_selection_value")
                            .show()
                            .append(
                                "<li class='wk-dropdown-li wk-disabled'>" +
                                wk_no_product +
                                "</li>"
                            );
                    }
                },
            });
        }
    });
    //for tag (options) setup
    $.each(wk_languages, function(key, value) {
        $("#option_values_" + value.id_lang).tagify({
            delimiters: [13, 44],
            addTagPrompt: wk_addtag,
        });
    });
    $(
        "#submitAddwk_product_options_configAndStay, #submitAddwk_product_options_config"
    ).on("click", function() {
        $.each(wk_languages, function(key, value) {
            $("#option_values_" + value.id_lang).tagify("serialize");
        });
    });
    var id_option = $("#option_type").val();
    if (typeof id_option != "undefined") {
        if (id_option == wk_drop_down) {
            $(".wk_drop_option").show();
            $(".wk_price_impact_row").hide();
            $(".wk_user_input").hide();
            $(".wk_option_multi_select").show();
            $('#wk_option_placeholder').hide();
            $('#wk_option_char_limit').hide();
        } else if (id_option == wk_checkbox) {
            $(".wk_drop_option").show();
            $(".wk_price_impact_row").hide();
            $(".wk_user_input").hide();
            $(".wk_option_multi_select").hide();
            $('#wk_option_placeholder').hide();
            $('#wk_option_char_limit').hide();
        } else if (id_option == wk_radio) {
            $(".wk_drop_option").show();
            $(".wk_price_impact_row").hide();
            $(".wk_user_input").hide();
            $(".wk_option_multi_select").hide();
            $('#wk_option_placeholder').hide();
            $('#wk_option_char_limit').hide();
        } else if (id_option == wk_image_file) {
            $(".wk_drop_option").hide();
            $(".wk_price_impact_row").show();
            $(".wk_user_input").hide();
            $(".wk_option_multi_select").hide();
            $('#wk_option_placeholder').hide();
            $('#wk_option_char_limit').hide();
        } else if (id_option == wk_option_textarea) {
            $(".wk_drop_option").hide();
            $(".wk_user_input").hide();
            $(".wk_price_impact_row").show();
            $(".wk_option_multi_select").hide();
            $('#wk_option_placeholder').show();
            $('#wk_option_char_limit').show();
        } else if (
            id_option == date_format ||
            id_option == time_format ||
            id_option == datetimeformat
        ) {
            $(".wk_drop_option").hide();
            $(".wk_user_input").hide();
            $(".wk_price_impact_row").show();
            $(".wk_option_multi_select").hide();
            $('#wk_option_placeholder').hide();
            $('#wk_option_char_limit').hide();
        } else if (id_option == wk_option_text) {
            $(".wk_drop_option").hide();
            $(".wk_price_impact_row").show();
            $(".wk_user_input").show();
            $(".wk_option_multi_select").hide();
            $('#wk_option_char_limit').show();
            showHideCharLimitAndPlaceholder();
        } else {
            $(".wk_drop_option").hide();
            $(".wk_price_impact_row").show();
            $(".wk_user_input").show();
            $(".wk_option_multi_select").hide();
            $('#wk_option_placeholder').show();
            $('#wk_option_char_limit').show();
        }
    }
    $(document).on("click", "li.product_value", function(event) {
        $("#wk_option_product_selection_value").hide().html("");
        $("#wk_option_product_selection").val("");
    });
    $(document).on("change", "#option_type", function(event) {
        var option_type = $(this).val();
        if (option_type == wk_drop_down) {
            $(".wk_drop_option").show();
            $(".wk_price_impact_row").hide();
            $(".wk_user_input").hide();
            $(".wk_option_multi_select").show();
            $('#wk_option_placeholder').hide();
            $('#wk_option_char_limit').hide();
        } else if (option_type == wk_checkbox) {
            $(".wk_drop_option").show();
            $(".wk_price_impact_row").hide();
            $(".wk_user_input").hide();
            $('#wk_option_placeholder').hide();
            $(".wk_option_multi_select").hide();
            $('#wk_option_char_limit').hide();
        } else if (option_type == wk_radio) {
            $(".wk_drop_option").show();
            $(".wk_price_impact_row").hide();
            $(".wk_user_input").hide();
            $(".wk_option_multi_select").hide();
            $('#wk_option_placeholder').hide();
            $('#wk_option_char_limit').hide();
        } else if (option_type == wk_image_file) {
            $(".wk_drop_option").hide();
            $(".wk_price_impact_row").show();
            $(".wk_user_input").hide();
            $(".wk_option_multi_select").hide();
            $('#wk_option_placeholder').hide();
            $('#wk_option_char_limit').hide();
        } else if (option_type == wk_option_textarea) {
            $(".wk_drop_option").hide();
            $(".wk_user_input").hide();
            $(".wk_price_impact_row").show();
            $(".wk_option_multi_select").hide();
            $('#wk_option_placeholder').show();
            $('#wk_option_char_limit').show();
        } else if (
            option_type == date_format ||
            option_type == time_format ||
            option_type == datetimeformat
        ) {
            $(".wk_drop_option").hide();
            $(".wk_user_input").hide();
            $(".wk_price_impact_row").show();
            $(".wk_option_multi_select").hide();
            $('#wk_option_placeholder').hide();
            $('#wk_option_char_limit').hide();
        } else if (id_option == wk_option_text) {
            $(".wk_drop_option").hide();
            $(".wk_user_input").show();
            $(".wk_price_impact_row").show();
            $(".wk_option_multi_select").hide();
            $('#wk_option_char_limit').show();
            showHideCharLimitAndPlaceholder();
        } else {
            $(".wk_drop_option").hide();
            $(".wk_user_input").show();
            $(".wk_price_impact_row").show();
            $(".wk_option_multi_select").hide();
            $('#wk_option_placeholder').show();
            $('#wk_option_char_limit').show();
        }
    });
    var productDatatable = dataTableRecord();
    $(document).on("click", "li.product_value", function() {
        var productId = $(this).attr("data-id-product");
        var productImg = $(this).attr("data-product-img");
        var productLink = $(this).attr("data-product-link");
        var productName = $(this).html();
        productDatatable.destroy();
        $("#productImageTobeAppend")
            .show()
            .append(
                "<tr class='productImageInfo'><td><img class='img-responsive img-thumbnail wk-custom-image-size' src='" +
                productImg +
                "'></td><td><a href='" +
                productLink +
                "' target='blank'>" +
                productName +
                "</a></td><td><span class='btn btn-sm btn-default deleteRow'><i class='material-icons '>delete_forever</i></span></td></tr>"
            );
        $("#productImageTobeAdd")
            .show()
            .append(
                "<input type='hidden' name='idProducts[]' class='wk_product_append' value='" + productId + "'>"
            );
        $('#option_selected_product').show();
        showSuccessMessage(product_add_success);
        productDatatable = dataTableRecord();
    });

    $(document).on("click", ".deleteRow", function(event) {
        if (confirm(wk_del_confirm)) {
            productDatatable.destroy();
            $(this).closest("tr").remove();
            showSuccessMessage(product_delete_success);
            productDatatable = dataTableRecord();
            if ($('.wk_product_append').length <= 0) {
                $('#option_selected_product').hide();
            }
        }
    });
    $(document).on("keyup", "#wkoptioncustomer", function(e) {
        var field = e.target.value;
        if (field != "" && field.length > 2) {
            $.ajax({
                url: option_product_ajax,
                type: "POST",
                async: true,
                cache: false,
                dataType: "json",
                data: {
                    cust_search: "1",
                    keywords: field,
                    ajax: true,
                    action: "SearchCustomer",
                },
                success: function(result) {
                    if (result.found) {
                        var html = `<ul class="list-unstyled" style=text-align:'left'>`;
                        $.each(result.customers, function() {
                            html +=
                                '<li style="margin-bottom:5px;">' +
                                this.firstname +
                                " " +
                                this.lastname;
                            html += " - " + this.email;
                            html +=
                                '<a onclick="selectcustomer(' +
                                this.id_customer +
                                ", '" +
                                this.firstname +
                                " " +
                                this.lastname +
                                '\'); return false" href="#" class="btn btn-default btn-sm"> ' +
                                Choose +
                                "</a></li>";
                        });
                        html += "</ul>";
                    } else
                        html =
                        '<div class="alert alert-warning col-lg-10">' +
                        no_customers_found +
                        "</div>";
                    $("#customers").html(html);
                },
            });
        } else {
            $("#customers").html('');
        }
    });
    $(document).on("change", "#option_price_type", function() {
        var priceType = $(this).val();
        if (priceType == 1) {
            $(".group_wk_option-per").removeClass("hide");
            $(".group_wk_option-currency").addClass("hide");
            $("#option_tax_type").closest("div").hide();
        } else if (priceType == 2) {
            $(".group_wk_option-currency").removeClass("hide");
            $(".group_wk_option-per").addClass("hide");
            $("#option_tax_type").closest("div").show();
        }
    });
    $(document).on('focusout', '#option_price', function() {
        var reg = /^(\d*\.)?\d+$/;
        var priceImpact = $(this).val();
        if (!reg.test(priceImpact)) {
            $(this).val("0.00");
        }
    })
    $(document).on('focusout', '.option_value_price', function() {
        var priceType = $(this).closest('.wk_option_container').find('.option_value_price_type').val();
        if (priceType == 2) {
            var reg = /^(\d*\.)?\d+$/;
            var priceImpact = $(this).val();
            if (!reg.test(priceImpact)) {
                $(this).val("0.00");
            }
        } else if (priceType == 1) {
            var reg = /^(\d*\.)?\d+$/;
            var priceImpact = $(this).val();
            if (!reg.test(priceImpact)) {
                $(this).val("0.00");
            } else if (priceImpact <= 0 || priceImpact > 100) {
                $(this).val("0.00");
                showErrorMessage(wk_invalid_price_range);
            }
        }
    })
    $(document).on("change", ".option_value_price_type", function() {
        var priceType = $(this).val();
        if (priceType == 1) {
            $(this).closest('.form-group').find(".group_wk_option-per").removeClass("hide");
            $(this).closest('.form-group').find(".group_wk_option-currency").addClass("hide");
            $(this).closest('.form-group').find(".option_value_tax_type").closest("div").hide();
            $(this).closest('.form-group').find(".option_value_tax_type").closest("div").addClass('hide');
        } else if (priceType == 2) {
            $(this).closest('.form-group').find(".group_wk_option-currency").removeClass("hide");
            $(this).closest('.form-group').find(".group_wk_option-per").addClass("hide");
            $(this).closest('.form-group').find(".option_value_tax_type").closest("div").show();
            $(this).closest('.form-group').find(".option_value_tax_type").closest("div").removeClass('hide');
        }
    });
    var id_option = $("#option_type").val();
    if (typeof id_option != "undefined") {
        if (id_option != wk_drop_down &&
            id_option != wk_checkbox &&
            id_option != wk_radio
        ) {
            var priceType = $("#option_price_type").val();
            if (typeof priceType != "undefined") {
                if (priceType == 1) {
                    $(".group_wk_option-per").removeClass("hide");
                    $(".group_wk_option-currency").addClass("hide");
                    $("#option_tax_type").closest("div").hide();
                } else if (priceType == 2) {
                    $(".group_wk_option-currency").removeClass("hide");
                    $(".group_wk_option-per").addClass("hide");
                    $("#option_tax_type").closest("div").show();
                }
            }
        }
    } else {
        var priceType = $("#option_price_type").val();
        if (typeof priceType != "undefined") {
            if (priceType == 1) {
                $(".group_wk_option-per").removeClass("hide");
                $(".group_wk_option-currency").addClass("hide");
                $("#option_tax_type").closest("div").hide();
            } else if (priceType == 2) {
                $(".group_wk_option-currency").removeClass("hide");
                $(".group_wk_option-per").addClass("hide");
                $("#option_tax_type").closest("div").show();
            }
        }
    }
    $(document).on("click", "#add_another_dropdownvalue", function(e) {
        e.preventDefault();
        var count_options = $("#count_options").val();
        count_options = parseInt(count_options);
        if (count_options > 0) {
            i = count_options + 1;
        } else {
            i = 1;
        }
        var max_options = $("#max_options").val();
        var useSame = 0;
        if ($('#use_same_as_above').prop('checked') == true) {
            useSame = 1;
        }
        var lastPrice = $("#dropdown_label_info").find('.wk_option_container').last().find('.option_value_price').val();
        var lastPriceType = $("#dropdown_label_info").find('.wk_option_container').last().find('.option_value_price_type').val();
        var lastTaxType = $("#dropdown_label_info").find('.wk_option_container').last().find('.option_value_tax_type').val();
        $.ajax({
            url: option_product_ajax,
            data: {
                id_option: i,
                action: "AddNewOption",
                ajax: true,
                use_same: useSame,
                last_price: lastPrice,
                last_price_type: lastPriceType,
                last_tax_type: lastTaxType
            },
            method: "POST",
            dataType: "JSON",
            async: true,
            success: function(response) {
                $("#dropdown_label_info").find('.wk_option_container').last().after(response.data);
                max_options += "," + i;
                if (max_options.charAt(0) === ',') {
                    max_options = max_options.substring(1);
                }
                $("#max_options").val(max_options);
                var count_options = $("#count_options").val();
                count_options = parseInt(count_options) + 1;
                $("#count_options").val(count_options);
            }
        })
    });

    // Remove dropdown value option
    $(document).on("click", ".remove_dropdownvalue", function(e) {
        if (confirm(wk_del_confirm)) {
            var id_option_value = parseInt($(this).data('id_option_val'));
            e.preventDefault();
            $(this)
                .closest('.wk_option_container')
                .remove();
            var new_max_options = '';
            $('.remove_dropdownvalue').each(function() {
                var value = $(this).data('remove-id');
                new_max_options += "," + value;
            })
            if (new_max_options.charAt(0) === ',') {
                new_max_options = new_max_options.substring(1);
            }
            $("#max_options").val(new_max_options);
            if (id_option_value > 0) {
                $.ajax({
                    url: option_product_ajax,
                    data: {
                        id_option_value: id_option_value,
                        action: "deleteOptionValue",
                        ajax: true,
                    },
                    method: "POST",
                    dataType: "JSON",
                    async: true,
                    success: function(response) {}
                });
            }
            var count_options = $("#count_options").val();
            count_options = parseInt(count_options) - 1;
            $("#count_options").val(count_options);
            showSuccessMessage(wk_delete_success);
        }
    });

    $(document).on('change', "input[name='user_input']", function() {
        var display_char_limit = $(this).val();
        if (display_char_limit == 1) {
            $('#wk_option_placeholder').slideDown();
            $('#wk_option_char_limit').slideDown();
        } else {
            $('#wk_option_placeholder').slideUp();
            $('#wk_option_char_limit').slideUp();
        }
    });
});

function showHideCharLimitAndPlaceholder() {
    var display_char_limit = $("input[name='user_input']:checked").val();
    if (display_char_limit === 'undefined') {
        display_char_limit = 0;
    }
    if (display_char_limit == 1) {
        $('#wk_option_placeholder').slideDown();
        $('#wk_option_char_limit').slideDown();
    } else {
        $('#wk_option_placeholder').slideUp();
        $('#wk_option_char_limit').slideUp();
    }
}

function selectcustomer(id_customer, name) {
    $("#id_customer").val(id_customer);
    $("#wkoptioncustomer").val(name);
    $("#customers").empty();
}

function dataTableRecord() {
    return $("#productImageTobeAdd").DataTable({
        order: [],
        aoColumnDefs: [
            { 'orderable': false, 'targets': 0 },
            { 'orderable': false, 'targets': 2 }
        ],
        columnDefs: [{
            targets: "no-sort",
            orderable: false,
        }, ],
        oLanguage: {
            sEmptyTable: wkoptiondatatableMessegeEmpty,
            sZeroRecords: wkoptiondatatableNoMatching,
            sLengthMenu: wkoptiondatatableDropdownPrefix +
                " _MENU_ " +
                wkoptiondatatableDropdownSuffix,
            sInfo: wkoptiondatatableInfoPrefix +
                " _START_ " +
                wkoptiondatatableInfoTo +
                " _END_ " +
                wkoptiondatatableInfoOf +
                " _TOTAL_ " +
                wkoptiondatatableInfoSuffix,
            sInfoEmpty: wkoptiondatatableInfoPrefix +
                " 0 " +
                wkoptiondatatableInfoTo +
                " 0 " +
                wkoptiondatatableInfoOf +
                " 0 " +
                wkoptiondatatableInfoSuffix,
            sSearch: wkoptiondatatableSearch,
            oPaginate: {
                sFirst: wkoptiondatatableFirstPage,
                sPrevious: wkoptiondatatablePrevious,
                sNext: wkoptiondatatableNext,
                sLast: wkoptiondatatableLastPage,
            },
        },
    });
}

/* for multilang input */
function showExtraLangField(lang_iso_code, id_lang) {
    $(".default_value_lang_btn").html(
        lang_iso_code + ' <span class="caret"></span>'
    );
    $(".default_value_all").hide();
    $("#option_description_" + id_lang).show();
    $("#option_name_" + id_lang).show();
    $("#display_name_" + id_lang).show();
    $("#placeholder_" + id_lang).show();
    $(".option_values_" + id_lang).show();
    $(".dropdown_value_lang_btn").html(
        lang_iso_code + ' <span class="caret"></span>'
    );
    $(".dropdown_value_all").hide();
    $(".dropdown_value_" + id_lang).show();
}