function updateCombinationsView(dataIds)
{
    id_product = $('#form_id_product').val();
    formToken = $('#form__token').val();
    /* Get all combinations and update view */
    urlCombinations = $('#form_step2_specific_price_sp_id_product_attribute').attr('data-action');

    urlCombinations = urlCombinations.replace('/combination/product-combinations/1', '/combination/product-combinations/' + id_product);

    var combinationsIds = '';
    $.ajax({
        url: urlCombinations,
        type: "GET",
        success: function (data) {

            urlCombinationsView = $('#accordion_combinations').attr('data-combinations-url');
            idsCombination = '';
            $.each(data, function (key, value) {
                idsCombination += value.id + '-';

            });
            idsCombination = idsCombination.substring(0, idsCombination.length - 1);

            if (idsCombination == '') {
                idsCombination = id_product;
                urlCombinationsView = urlCombinationsView.replace('combination/1', 'attribute/' + idsCombination);

                $.ajax({
                    url: urlCombinationsView,
                    type: "DELETE",
                    data: dataIds,
                    success: function (data) {
                        $('.combinations-list tbody').html(data);

                        $('.combinations-list tbody tr.combination.loaded').css('display', 'table-row');
                        specificPrices.refreshCombinationsList();
                        supplierCombinations.refresh();
                        warehouseCombinations.refresh();
                        displayFieldsManager.refresh();

                    },

                    error: function (XMLHttpRequest, textStatus, errorThrown)
                    {
                        console.log('Error: ' + errorThrown);
                    }
                });

            } else {
                urlCombinationsView = urlCombinationsView.replace('combination/1', 'combination/' + idsCombination);

                $.ajax({
                    url: urlCombinationsView,
                    type: "GET",
                    success: function (data) {
                        $('.combinations-list tbody').html(data);

                        $('.combinations-list tbody tr.combination.loaded').css('display', 'table-row');
                        specificPrices.refreshCombinationsList();
                        supplierCombinations.refresh();
                        warehouseCombinations.refresh();
                        displayFieldsManager.refresh();

                    },

                    error: function (XMLHttpRequest, textStatus, errorThrown)
                    {
                        console.log('Error: ' + errorThrown);
                    }
                });

            }



        },
        error: function (XMLHttpRequest, textStatus, errorThrown)
        {
            console.log('Error: ' + errorThrown);
        }
    });


}
function enableFirstDefaultAttribute(id_attr, optSelected) {

    id_group = $('#awp-attribute-' + id_attr).attr('data-group-id');

    enabledGroups = [];
    selectedDefault = [];
    preSelectedDefault = false;
    $(".js-attribute-checkbox[data-group-id='" + id_group + "']").each(function () {
        checkBox = $(this);


        id_attrEach = checkBox.attr('data-value');
        id_attr_group = checkBox.attr('data-group-id');
        if (checkBox.is(':checked') && $('.awp-attribute-default-' + id_attrEach).is(':checked')) {
            preSelectedDefault = true;
        } else if (!checkBox.is(':checked') && $('.awp-attribute-default-' + id_attrEach).is(':checked')) {
            preSelectedDefault = false;
        }

    });
    if (!preSelectedDefault) {
        if ($('#awp-attribute-' + id_attr).is(':checked'))
            $('.awp-attribute-default-' + id_attr).prop('checked', true);
    }

}

document.addEventListener('DOMContentLoaded', function(){
jQuery(function ($) {

    $('.awp_select_all').click(function () {
        id_group = $(this).attr('data-group-id');
        checked = $(this).is(':checked');
        $(".js-attribute-checkbox[data-group-id='" + id_group + "']").prop('checked', checked);
        if (checked)
            $(".awp_attribute_default[data-group-id='" + id_group + "']").show();
        else
            $(".awp_attribute_default[data-group-id='" + id_group + "']").hide();
        firstIdAttr = 0;
        $(".js-attribute-checkbox[data-group-id='" + id_group + "']").each(function () {
            firstIdAttr = $(this).attr('data-value');
            return false;
        });
        enableFirstDefaultAttribute(firstIdAttr, checked);
    });

    $('.js-attribute-checkbox').click(function () {
        checkBox = $(this);
        id_attr = checkBox.attr('data-value');
        if (checkBox.is(':checked')) {
            $('.awp-attribute-default-' + id_attr).show();
            $('.awp-attribute-default-' + id_attr).prop('checked', false);
        } else {
            $('.awp-attribute-default-' + id_attr).hide();
            $('.awp-attribute-default-' + id_attr).prop('checked', false);
        }
        enableFirstDefaultAttribute(id_attr, checkBox.is(':checked'));
    });

    $('#awp_delete_all').click(function () {
        if (confirm(awp_confirm_delete)) {
            id_product = $('#form_id_product').val();
            formToken = $('#form__token').val();
            urlDelete = $('#delete-combinations').attr('data');

            /* Get all combinations and update view */
            urlCombinations = $('#form_step2_specific_price_sp_id_product_attribute').attr('data-action');

            urlCombinations = urlCombinations.replace('/combination/product-combinations/1', '/combination/product-combinations/' + id_product);
            var combinationsIds = '';
            $.ajax({
                url: urlCombinations,
                type: "GET",
                success: function (data) {
                    dataIds = '';
                    $.each(data, function (key, value) {
                        dataIds += 'attribute-ids[]=' + value.id + '&';
                    });
                    $.ajax({
                        url: urlDelete,
                        type: "DELETE",
                        data: dataIds,
                        success: function (dataA) {

                            updateCombinationsView(dataIds);
                        },

                        error: function (XMLHttpRequest, textStatus, errorThrown)
                        {
                            console.log('Error: ' + errorThrown);
                            updateCombinationsView(dataIds);

                        }
                    });

                },
                error: function (XMLHttpRequest, textStatus, errorThrown)
                {
                    console.log('Error: ' + errorThrown);
                }
            });

        }

    });

    var
        $jsBtnSave = $(".js-btn-save");

    // Start Suren
    /*$('button.js-btn-save').unbind().click(function(e){
     e.stopImmediatePropagation();
     console.log('test 2');
     });

     $('#form').unbind().submit(function(e){
     e.preventDefault();
     e.stopImmediatePropagation();
     console.log('test');
     });*/
    // End Suren

    // this handler was never called
    // $('input#submit').unbind().click(function (event) {
    //
    //     if ($('#module_attributewizardpro').is(':visible')) {
    //         event.preventDefault();
    //         event.stopPropagation();
    //         event.stopImmediatePropagation();
    //
    //         id_product = $('#form_id_product').val();
    //         formToken = $('#form__token').val();
    //         /* Save new combinations */
    //         plainUrl = $('#form_step2_specific_price_sp_id_product_attribute').attr('data-action');
    //         urlSaveProductFirst = plainUrl.replace('/combination/product-combinations/1?', '/product/form/' + id_product + '?');
    //         noOfCombinations = 0;
    //         var productFomData = "";
    //         $.each($('#form').serializeArray(), function (i, field) {
    //             field.value = $.trim(field.value) || 0;
    //             productFomData += field.name + '=' + field.value + '&';
    //         });
    //
    //
    //         $.ajax({
    //             url: urlSaveProductFirst,
    //             type: "POST",
    //             data: productFomData,
    //             success: function (data) {
    //
    //                 updateCombinationsView();
    //             },
    //
    //             error: function (XMLHttpRequest, textStatus, errorThrown)
    //             {
    //                 console.log('Error: ' + errorThrown);
    //                 updateCombinationsView();
    //
    //             }
    //         });
    //         return true;
    //     }
    // });

    // send form with callback which handle our generator, see main.bundle.js for more info
    $('#awp-create-combinations').click(function (e) {
        $('.js-spinner').css('display', 'inline-block');
        form.send('#tab-step3', false, function () {

            $.ajax({
                type: "POST",
                url: $("#form_step3_attributes").attr("data-action"),
                data: $("#awp_attribute_list .awp_input, #form_id_product").serialize() + '&options[]=0',
                beforeSend: function () {
                    $("#awp-create-combinations, #create-combinations, #submit, .btn-submit").attr("disabled", "disabled");
                },
                success: function (t) {
                    // rewrite to generateCombinationFormAction
                    /*var e = function (e, n) {
                        $.each(n, function (t, n) {
                            var i = $('.combination[data="' + n + '"]'), s = i.find(".images"), r = i.attr("data-index");
                            0 === s.length && (s = $("#combination_" + r + "_id_image_attr"));
                            var a = "";
                            $.each(e[n], function (e, t) {
                                a += '<div class="product-combination-image ' + (t.id_image_attr ? "img-highlight" : "") + '">\n          <input type="checkbox" name="combination_' + r + '[id_image_attr][]" value="' + t.id + '" ' + (t.id_image_attr ? 'checked="checked"' : "") + '>\n          <img src="' + t.base_image_url + "-small_default." + t.format + '" alt="" />\n        </div>'
                            }), s.html(a), i.fadeIn(1e3)
                        })
                    };
                    refreshTotalCombinations(1, $(t.form).filter(".combination.loaded").length),
                        $("#accordion_combinations").append(t.form), displayFieldsManager.refresh();
                    var n = $(".js-combinations-list").attr("data-action-refresh-images").replace(/product-form-images\/\d+/, "product-form-images/" + $(".js-combinations-list").data("id-product"));
                    $.get(n).then(function (n) {
                        e(n, t.ids_product_attribute)
                    }),
                        $("input.attribute-generator").remove(),
                        $("#attributes-generator div.token").remove(), $(".js-attribute-checkbox:checked").each(function () {
                        $(this).prop("checked", !1)
                    }),
                        $("#combinations_thead").fadeIn();*/
                },
                complete: function () {
                    // $("#create-combinations, #submit, .btn-submit").removeAttr("disabled"), supplierCombinations.refresh(), warehouseCombinations.refresh()
                    // $('.js-spinner').hide();
                    window.location.reload();

                }
            })
        })
    });


$('button.js-btn-save').click(function(e){
            
                    
            var checkExist = setInterval(function() {
               if ($('.js-spinner').is(':hidden')) {
                  
                  if ($('#module_attributewizardpro').is(':visible')) {
                      window.location.reload();
                      
                  }
                  clearInterval(checkExist);
               }
            }, 100); // check every 100ms
          
         });
		 
    $('#awp_preview_combinations').on('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var combinations = [];
        var attributeGroups = [];
        var attributeValues = [];
        var groupType = [];
        var groupShared = [];

        var defaultComb = [];
        noOfCombinations = 0;
        $('.js-attribute-checkbox').each(function () {
            var
                $attrCheck = $(this);
            if ($attrCheck.is(":checked")) {
                id_attribute_group = parseInt($attrCheck.attr('data-group-id'));
                id_attribute = parseInt($attrCheck.attr('data-value'));

                /* Default attribute */
                if ($('.awp-attribute-default-' + id_attribute).is(":checked"))
                    if ($.inArray(parseInt(id_attribute), defaultComb) < 0)
                        defaultComb.push(id_attribute);

                group_type = $('#awp_group_type_' + id_attribute_group).val();
                group_shared = $('#awp_group_shared_' + id_attribute_group).is(":checked");

                attributeGroups[id_attribute_group] = id_attribute_group;
                if (typeof attributeValues[id_attribute_group] == 'undefined')
                    attributeValues[id_attribute_group] = [];
                if ($.inArray(parseInt(id_attribute), attributeValues[id_attribute_group]) < 0)
                    attributeValues[id_attribute_group].push(id_attribute);
                groupType[id_attribute_group] = group_type;
                groupShared[id_attribute_group] = group_shared;
            }
        });

        combinationsHtml = '';
        var arrSharedConnectedCombinations = [];
        var arrConnectedCombinations = [];
        var genCombs = [];
        i = 0;
        $.each(attributeGroups, function (id_attribute_group, id_group_val) {
            if (typeof id_group_val != 'undefined') {

                sel_group_type = groupType[id_group_val];
                sel_group_shared = groupShared[id_group_val];
                if (sel_group_type == 'separated') {
                    if (sel_group_shared) {
                        combinationAttr = '';
                        combinationPriceImpact = 0;
                        combinationQty = 0;
                        combinationWeight = 0;

                        idsAttrs = [];
                        $.each(attributeValues[id_group_val], function (pos, id_attribute) {
                            idsAttrs.push(id_attribute);
                            combinationAttr += $('.awp-attribute-name-' + id_attribute).html() + ', ';
                            combinationPriceImpact += parseFloat($('.awp-attribute-price-' + id_attribute).val());
                            combinationQty += parseInt($('.awp-attribute-qty-' + id_attribute).val());
                            combinationWeight += parseFloat($('.awp-attribute-weight-' + id_attribute).val());
                        });
                        genCombs.push(idsAttrs);


                        noOfCombinations += 1;
                        if (i % 2 == 0) {
                            rowclass = "awp_odd";
                        } else {
                            rowclass = "";
                        }



                        combinationsHtml += '<div class="row_preview_comb' + i + ' row_preview_comb ' + rowclass + '"><div class="preview_comb">' + combinationAttr + '</div>';
                        combinationsHtml += '<div class="preview_comb_price">' + combinationPriceImpact + '</div>';
                        combinationsHtml += '<div class="preview_comb_weight">' + combinationWeight + '</div>';


                        combinationsHtml += '<div class="preview_comb_qty ">' + combinationQty + '</div><div class="clear"></div></div>';

                        i++;
                    } else {
                        $.each(attributeValues[id_group_val], function (pos, id_attribute) {
                            combinationAttr = $('.awp-attribute-name-' + id_attribute).html();
                            combinationPriceImpact = $('.awp-attribute-price-' + id_attribute).val();
                            combinationQty = $('.awp-attribute-qty-' + id_attribute).val();
                            combinationWeight = $('.awp-attribute-weight-' + id_attribute).val();
                            if (i % 2 == 0) {
                                rowclass = "awp_odd";
                            } else {
                                rowclass = "";
                            }
                            combinationsHtml += '<div class="row_preview_comb' + i + ' row_preview_comb ' + rowclass + '"><div class="preview_comb">' + combinationAttr + '</div>';
                            combinationsHtml += '<div class="preview_comb_price">' + combinationPriceImpact + '</div>';
                            combinationsHtml += '<div class="preview_comb_weight">' + combinationWeight + '</div>';
                            noOfCombinations += 1;
                            idsAttrs = [];
                            idsAttrs.push(id_attribute);
                            genCombs.push(idsAttrs);
                            combinationsHtml += '<div class="preview_comb_qty ">' + combinationQty + '</div><div class="clear"></div></div>';
                            i++;
                        });
                    }
                } else {
                    if (sel_group_shared) {
                        $.each(attributeValues[id_group_val], function (pos, id_attribute) {
                            arrSharedConnectedCombinations.push(id_attribute);
                        });
                    } else {
                        $.each(attributeValues[id_group_val], function (pos, id_attribute) {
                            if (typeof arrConnectedCombinations[id_group_val] == 'undefined')
                                arrConnectedCombinations[id_group_val] = [];
                            arrConnectedCombinations[id_group_val].push(id_attribute);
                        });
                    }
                }

            }

        });

        newarrConnectedCombinations = [];
        for (k in arrConnectedCombinations) {
            newarrConnectedCombinations.push(arrConnectedCombinations[k]);
        }
        var getAllCombinations = function (arraysToCombine) {
            var divisors = [];
            for (var i = arraysToCombine.length - 1; i >= 0; i--) {
                divisors[i] = divisors[i + 1] ? divisors[i + 1] * arraysToCombine[i + 1].length : 1;
            }

            function getPermutation(n, arraysToCombine) {
                var result = [],
                    curArray;
                for (var i = 0; i < arraysToCombine.length; i++) {
                    curArray = arraysToCombine[i];
                    result.push(curArray[Math.floor(n / divisors[i]) % curArray.length]);
                }
                return result;
            }

            var numPerms = arraysToCombine[0].length;
            for (var i = 1; i < arraysToCombine.length; i++) {
                numPerms *= arraysToCombine[i].length;
            }

            var combinations = [];
            for (var i = 0; i < numPerms; i++) {
                combinations.push(getPermutation(i, arraysToCombine));
            }
            return combinations;
        }

        if (newarrConnectedCombinations.length > 0) {
            connectedComcinations = getAllCombinations(newarrConnectedCombinations);

            $.each(connectedComcinations, function (pos, comb) {
                $.each(arrSharedConnectedCombinations, function (id_shared, id_attribute) {
                    comb.push(id_attribute);
                });
            });

            $.each(connectedComcinations, function (pos, comb) {
                combinationAttr = '';
                combinationPriceImpact = 0;
                combinationQty = 0;
                combinationWeight = 0;
                previousIdGroup = 0;
                separatorAWP = ' , ';
                idsAttrs = [];
                $.each(comb, function (id_shared, id_attribute) {
                    currentGroupId = parseInt($('#awp-attribute-' + id_attribute).attr('data-group-id'));
                    sel_group_shared = groupShared[currentGroupId];

                    idsAttrs.push(id_attribute);

                    if (currentGroupId != previousIdGroup) {
                        separatorAWP = '| ';
                    } else {
                        separatorAWP = ' , ';
                    }

                    if (sel_group_shared) {
                        separatorAWP = ', ';
                        if (currentGroupId != previousIdGroup) {
                            separatorAWP = '| ';
                        }
                    } else {
                        separatorAWP = '| ';
                    }

                    if (id_shared == 0)
                        separatorAWP = '';

                    combinationAttr += separatorAWP + $('.awp-attribute-name-' + id_attribute).html();
                    combinationPriceImpact += parseFloat($('.awp-attribute-price-' + id_attribute).val());
                    combinationQty += parseInt($('.awp-attribute-qty-' + id_attribute).val());
                    combinationWeight += parseFloat($('.awp-attribute-weight-' + id_attribute).val());

                    previousIdGroup = currentGroupId;

                });
                if (i % 2 == 0) {
                    rowclass = "";
                } else {
                    rowclass = "awp_odd";
                }

                genCombs.push(idsAttrs);
                combinationsHtml += '<div class="row_preview_comb' + i + '  row_preview_comb ' + rowclass + '"><div class="preview_comb">' + combinationAttr + '</div>';
                combinationsHtml += '<div class="preview_comb_price">' + combinationPriceImpact + '</div>';
                combinationsHtml += '<div class="preview_comb_weight">' + combinationWeight + '</div>';
                noOfCombinations += 1;
                i++;
                combinationsHtml += '<div class="preview_comb_qty">' + combinationQty + '</div><div class="clear"></div></div>';
            });

        } else {
            connectedComcinations = [];
            comb = [];
            $.each(arrSharedConnectedCombinations, function (id_shared, id_attribute) {
                comb.push(id_attribute);
            });
            connectedComcinations.push(comb);

            $.each(connectedComcinations, function (pos, comb) {

                combinationAttr = '';
                combinationPriceImpact = 0;
                combinationQty = 0;
                combinationWeight = 0;
                previousIdGroup = 0;
                separatorAWP = ' , ';
                idsAttrs = [];
                $.each(comb, function (id_shared, id_attribute) {

                    currentGroupId = parseInt($('#awp-attribute-' + id_attribute).attr('data-group-id'));
                    sel_group_shared = groupShared[currentGroupId];

                    idsAttrs.push(id_attribute);

                    if (currentGroupId != previousIdGroup) {
                        separatorAWP = '| ';
                    } else {
                        separatorAWP = ' , ';
                    }

                    if (sel_group_shared) {
                        separatorAWP = ', ';
                        if (currentGroupId != previousIdGroup) {
                            separatorAWP = '| ';
                        }
                    } else {
                        separatorAWP = '| ';
                    }

                    if (id_shared == 0)
                        separatorAWP = '';

                    combinationAttr += separatorAWP + $('.awp-attribute-name-' + id_attribute).html();
                    combinationPriceImpact += parseFloat($('.awp-attribute-price-' + id_attribute).val());
                    combinationQty += parseInt($('.awp-attribute-qty-' + id_attribute).val());
                    combinationWeight += parseFloat($('.awp-attribute-weight-' + id_attribute).val());

                    previousIdGroup = currentGroupId;

                });
                if (i % 2 == 0) {
                    rowclass = "";
                } else {
                    rowclass = "awp_odd";
                }

                genCombs.push(idsAttrs);



                // combinationsHtml += '<div class="row_preview_comb' + i + '  row_preview_comb ' + rowclass + '"><div class="preview_comb">' + combinationAttr + '</div>';
                // combinationsHtml += '<div class="preview_comb_price">' + combinationPriceImpact + '</div>';
                // combinationsHtml += '<div class="preview_comb_weight">' + combinationWeight + '</div>';
                // noOfCombinations += 1;
                // i++;
                // combinationsHtml += '<div class="preview_comb_qty">' + combinationQty + '</div><div class="clear"></div></div>';
            });
        }

        defCombinationAttr = '';
        defCombinationPriceImpact = 0;
        defCombinationQty = 0;
        defCombinationWeight = 0;
        previousIdGroup = 0;


        function arrayContainsAnotherArray(needle, haystack) {
            for (index in needle) {

                if (haystack.indexOf(needle[index]) === -1)
                    return false;
            }
            return true;
        }

        existsDef = -1;
        if (defaultComb.length > 0)
            genCombs.forEach(function (pos, comb) {
                if (arrayContainsAnotherArray(defaultComb, genCombs[comb]) && (defaultComb.length == genCombs[comb].length)) {
                    existsDef = comb;
                }
            });



        $(defaultComb).each(function (id_shared, id_attribute) {
            currentGroupId = parseInt($('#awp-attribute-' + id_attribute).attr('data-group-id'));
            sel_group_shared = groupShared[currentGroupId];

            if (currentGroupId != previousIdGroup) {
                separatorAWP = '| ';
            } else {
                separatorAWP = ' , ';
            }

            if (sel_group_shared) {
                separatorAWP = ', ';
                if (currentGroupId != previousIdGroup) {
                    separatorAWP = '| ';
                }
            }

            if (id_shared == 0)
                separatorAWP = '';

            defCombinationAttr += separatorAWP + $('.awp-attribute-name-' + id_attribute).html();
            defCombinationPriceImpact += parseFloat($('.awp-attribute-price-' + id_attribute).val());
            defCombinationQty += parseInt($('.awp-attribute-qty-' + id_attribute).val());
            defCombinationWeight += parseFloat($('.awp-attribute-weight-' + id_attribute).val());
            previousIdGroup = currentGroupId;
        });
        defCombinationsHtml = '';
        /* If default combination does not exist */

        if (defCombinationAttr != '') {
            if (existsDef == -1) {
                noOfCombinations += 1;
            }
            defCombinationsHtml += '<div class="default_comb_preview row_preview_comb "><div class="preview_comb">' + defCombinationAttr + '</div>';
            defCombinationsHtml += '<div class="preview_comb_price">' + defCombinationPriceImpact + '</div>';
            defCombinationsHtml += '<div class="preview_comb_weight">' + defCombinationWeight + '</div>';

            defCombinationsHtml += '<div class="preview_comb_qty">' + defCombinationQty + '</div><div class="clear"></div></div>';

        }

        noOfCombinationsTxt = tableAWPHeader = '';
        if(noOfCombinations > 0)
        {
            noOfCombinationsTxt = '<div>' + noOfCombinations + ' combinations will be generated : </div>';
            tableAWPHeader = '<div class="row_preview_comb awp_odd"><div class="preview_comb">' + awp_table_col1 + '</div><div class="preview_comb_price">' + awp_table_col2 + '</div><div class="preview_comb_weight">' + awp_table_col3 + '</div><div class="preview_comb_qty">' + awp_table_col4 + '</div><div class="clear"></div></div>';
        }

        $('#awp_preview_attribute_list').html(noOfCombinationsTxt + tableAWPHeader + defCombinationsHtml + combinationsHtml);

        if (existsDef >= 0) {
            $('.row_preview_comb' + existsDef).hide();
        }
        $('.row_preview_comb').each(function () {
            heightA = $(this).height();
            $(this).find('.preview_comb').height(heightA);
            $(this).find('.preview_comb_price').height(heightA);
            $(this).find('.preview_comb_weight').height(heightA);
            $(this).find('.preview_comb_qty').height(heightA);
        });
    });

    $('.info_alert').click(function () {
        setTimeout(function () {
            $('.fancybox-opened').css('z-index', '9999');
            $('.fancybox-overlay').css('z-index', '9998');
            $('.fancybox-overlay').css('width', $(window).width());
            $('.fancybox-overlay').css('height', $(window).height());
            $('.fancybox-overlay').css('position', 'fixed');
            $('.fancybox-overlay').css('top', '0px');
            $('.fancybox-overlay').css('background', '#000');
            $('.fancybox-overlay').css('opacity', '0.5');

        }, 600);
    });

});
}, false);