function awp_update_lang(lang_change)
{
    var id = $("#awp_id_lang").val();
    var name = document.wizard_form;
    for(i=0; i<name.elements.length; i++)
    {
        if (name.elements[i].type == "hidden" && name.elements[i].name.substring(0,8) == "id_group")
        {
            var gid = name.elements[i].value;

            if (lang_change)
            {
                $("#description_"+gid).val($("#description_"+gid+"_"+id).val());
                $("#group_header_"+gid).val($("#group_header_"+gid+"_"+id).val());
                $("#parent_group_name_"+gid).val($("#parent_group_name_"+gid+"_"+id).val());
                
                if (typeof tinymce != 'undefined')
                    if (typeof tinymce.get("description_"+gid) != 'undefined')
                        tinymce.get("description_"+gid).setContent($("#description_"+gid+"_"+id).val());
   
            }
            else
            {                

                $("#description_"+gid+"_"+id).val($("#description_"+gid).val());
                $("#group_header_"+gid+"_"+id).val($("#group_header_"+gid).val());
                $("#parent_group_name_"+gid+"_"+id).val($("#parent_group_name_"+gid).val());
                if (typeof tinymce != 'undefined')
                    if (typeof tinymce.get("description_"+gid) != 'undefined')
                        $("#description_"+gid+"_"+id).val(tinymce.get("description_"+gid).getContent());
                    
            }
        }
              
        if (name.elements[i].type == "hidden" && name.elements[i].name == "id_attribute")
        {
            var aid = name.elements[i].value;
            if (lang_change)
            {
                $("#attr_description_"+aid).val($("#attr_description_"+aid+"_"+id).val());   
            }
            else
            { 
                $("#attr_description_"+aid+"_"+id).val($("#attr_description_"+aid).val());                    
            }
        }
    }
}


function awp_select_lang(il)
{
    var i = 0;
    $('ul#awp_first-languages li:not(.selected_language)').unbind('mouseover');
    $('ul#awp_first-languages li:not(.selected_language)').unbind('mouseout');
    while (document.getElementById("awp_li_lang_"+i))
    {
            var id = document.getElementById("awp_li_lang_"+i).value;
            if (id != il)
            {
                    $("#awp_lang_"+id).removeClass("selected_language");
                    $("#awp_lang_"+id).css('opacity', 0.3);
            }
            else
            {
                    $("#awp_id_lang").val(id);
                    $("#awp_lang_"+id).addClass("selected_language");
                    $("#awp_lang_"+id).css('opacity', 1);
            }
            i++;
    }
    awp_update_lang(true);	
    $('ul#awp_first-languages li:not(.selected_language)').mouseover(function(){
            $(this).css('opacity', 1);});
    $('ul#awp_first-languages li:not(.selected_language)').mouseout(function(){
            $(this).css('opacity', 0.3);});
}


$(document).ready(function ()
{
    
    
    
    $('table.tableDnD').tableDnD(
            {
                onDragStart: function (table, row)
                {
                    originalOrder = $.tableDnD.serialize();
                    if (awp_psv >= 1.4)
                    {
                        reOrder = ':even';
                        if (table.tBodies[0].rows[1] && $('#' + table.tBodies[0].rows[1].id).hasClass('alt_row'))
                            reOrder = ':odd';
                    } else
                        $('#' + table.id + '#' + row.id).parent('tr').addClass('myDragClass');
                },
                dragHandle: 'dragHandle',
                onDragClass: 'myDragClass',
                onDrop: function (table, row)
                {
                    if (originalOrder != $.tableDnD.serialize())
                    {
                        var way = (originalOrder.indexOf(row.id) < $.tableDnD.serialize().indexOf(row.id)) ? 1 : 0;
                        var ids = row.id.split('_');
                        var group = ids[1];
                        var attribute = ids[2] ? ids[2] : "";
                        var tableDrag = $('#' + table.id);
                        var bak = alternate;
                        alternate = (alternate == 1 && way == 0 ? 1 : (alternate == 1 && way == 1 ? 0 : way)); // If orderWay = DESC alternate the way
                        params = 'ajaxProductsPositions=true&id_attribute=' + attribute + '&id_group=' + group + '&' + $.tableDnD.serialize();

                        $.ajax(
                                {
                                    type: 'POST',
                                    url: baseDir + 'wizard_json.php',
                                    async: true,
                                    data: params,
                                    success: function (data)
                                    {
                                        if (awp_psv >= 1.4)
                                        {
                                            if (attribute != "")
                                            {
                                                tableDrag.find('tbody tr').removeClass('alt_row');
                                                tableDrag.find('tbody tr' + reOrder).addClass('alt_row');
                                            }
                                            tableDrag.find('tr td.dragHandle a:hidden').show();
                                            if (bak)
                                            {
                                                tableDrag.find('tbody td.dragHandle:last a:odd').hide();
                                                tableDrag.find('tbody td.dragHandle:first a:even').hide();
                                            } else
                                            {
                                                tableDrag.find('tbody td.dragHandle:last a:even').hide();
                                                tableDrag.find('tbody td.dragHandle:first a:odd').hide();
                                            }
                                        } else
                                        {
                                            if (attribute != "")
                                            {
                                                tableDrag.find('tr').not('.nodrag').removeClass('alt_row');
                                                tableDrag.find('tr:not(".nodrag"):odd').addClass('alt_row');
                                            }
                                            tableDrag.find('tr td.dragHandle a:hidden').show();
                                            if (bak)
                                            {
                                                tableDrag.find('tr td.dragHandle:first a:even').hide();
                                                tableDrag.find('tr td.dragHandle:last a:odd').hide();
                                            } else
                                            {
                                                tableDrag.find('tr td.dragHandle:first a:odd').hide();
                                                tableDrag.find('tr td.dragHandle:last a:even').hide();
                                            }
                                        }
                                    }

                                });
                    }
                }
            });

    var i = 0;

        awp_update_lang(true);
        awp_select_lang($("#awp_id_lang").val());

    while (i < total_groups)
    {

        // awp_update_lang(true);
        // awp_select_lang($("#awp_id_lang").val());

        if (!$('#upload_container_' + i).html())
        {
            i++;
            continue;
        }
        new AjaxUpload('#upload_button_' + i, i, 'void', {
            action: baseDirModule + 'image_upload.php',
            name: 'userfile',
            data: {'awp_random': awp_random, 'id_group': $('#id_group_' + i).val()},
            // Submit file after selection
            autoSubmit: true,
            responseType: false,
           onSubmit: function(file, ext, i)
            {
                
                if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext)))
                {
                    alert('Error: invalid file extension');
                    return false;
                }
                $('#image_container_'+i).html('<img src="'+baseDirModule+'/views/img/loading.gif" /> <b>Please wait</b>');
            },
            onComplete: function(file, ext, response, i)
            {
                
                id = $('#id_group_'+i).val();

                presto_toggle(id, true);
               
                rand = Math.floor((Math.random() * 10000) + 1);
                $('#image_container_'+i).html('\n\
                <img src="'+baseDirModule+'views/img/id_group_'+$('#id_group_'+i).val()+'.'+ext+'?v= ' + rand + '" />\n\
                ');
                
                
                $('#upload_container_'+i).append( '<div id="edit_new_block_'+i+'" data-id="'+$('#id_group_'+i).val()+'" data-order="'+i+'"></div>' );
            
                $('#edit_new_block_'+i).attr('data-id', $('#id_group_'+i).val());
                $('#edit_new_block_'+i).attr('data-order', i);
                
                $('#image_url_'+i).detach().prependTo('#edit_new_block_'+i);
                $('#delete_button_'+i).detach().prependTo('#edit_new_block_'+i);
                $('#delete_button_'+i).removeClass('hideADN');
                $('#upload_button_'+i).detach().prependTo('#edit_new_block_'+i);
                $('#upload_button_'+i).removeClass('upload_btn');
                $('#upload_button_'+i).addClass('edit_btn');
                $('#upload_button_'+i).val(aw_change_image);

            }
        });
        $('#upload_container_' + i).css('display', 'inline');
        $('#image_upload_container_' + i).css('display', 'inline');
        i++;
    }

    if (awp_layered_image)
    {
        $('.liu').each(function () {
            var awp_att_id = $(this).attr('id').substring(18);
            var awp_group_id = $(this).attr('group');

            if ($('#upload_container_l' + awp_att_id).html())
            {
                new AjaxUpload('#upload_button_l' + awp_att_id, awp_att_id, 'void', {
                    action: baseDirModule + 'image_upload.php',
                    name: 'userfile',
                    data: {'awp_random': awp_random, id_attribute: awp_att_id, pos: awp_group_id},
                    // 	Submit file after selection
                    autoSubmit: true,
                    responseType: false,
                    onSubmit: function (file, ext, awp_att_id, jpg)
                    {
                        if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext)))
                        {
                            alert('Error: invalid file extension');
                            return false;
                        }
                        $('#image_container_l' + awp_att_id).html('<img src="' + baseDirModule + 'views/img/loading.gif" /> <b>Please wait</b>');
                    },
                    onComplete: function (file, ext, response, awp_att_id)
                    {
                        if (response != "success")
                            alert(response);
                        $('#image_container_l' + awp_att_id).html('<img width="32" height="32" src="' + baseDirModule + 'views/img/id_attribute_' + awp_att_id + '.' + ext + '" />\n');
                        $('#upload_button_l' + awp_att_id).removeClass('upload_btn').addClass('edit_btn').val(awp_edit_img);
                        // upload_button.after('<br/><br/><input id="delete_image_l' + awp_att_id + '" class="button delete_btn" value="' + awp_delete + '" type="button">');
                    }
                });
                $('#upload_container_' + awp_att_id).css('display', 'inline');
                $('#image_upload_container_l' + awp_att_id).css('display', 'inline');
            }
        });
    }



    $('ul#awp_first-languages li:not(.selected_language)').css('opacity', 0.3);
    $('ul#awp_first-languages li:not(.selected_language)').mouseover(function () {
        $(this).css('opacity', 1);
    });
    $('ul#awp_first-languages li:not(.selected_language)').mouseout(function () {
        $(this).css('opacity', 0.3);
    });


    $('.parent-group-name-container').each(function(){
        var $parent = $(this).parents('.row_format');
        if($parent.find('.group-type').val() == 'radio') {
            $(this).removeClass('hide');
        }
    });

    $('.group-type').on('change', function(){
        var $group_name = $(this).parents('.row_format').find('.parent-group-name-container');
        if($(this).val() == 'radio') {
            $group_name.removeClass('hide');
        } else {
            $group_name.addClass('hide');
        }
    });

})

function toggle_desc(id)
{
    if (document.getElementById('description_container_' + id).style.display == "none")
    {
        document.getElementById('description_container_' + id).style.display = "block";
        document.getElementById('awp_description_' + id + '_text').innerHTML = awp_hide + " " + awp_description;
    } else
    {
        document.getElementById('description_container_' + id).style.display = "none";
        document.getElementById('awp_description_' + id + '_text').innerHTML = (document.getElementById('awp_description_' + id).innerHTML != "" ? awp_edit : awp_enter) + " " + awp_description;
    }

}

function update_image_resize()
{
    $.post(baseDir + "update_resize.php", {awp_random: awp_random, resize: (document.getElementById('awp_image_resize').checked == true ? 1 : ""), width: document.getElementById('awp_image_resize_width').value});
}



function awp_toggle(i)
{
    if ($('#awp_ag_' + i).css('display') == 'block')
    {
        $('.awp_ag_display_' + i).fadeOut('slow');
        $('.awp_ag_display_' + i).css('display', 'none');
    } else
        $('.awp_ag_display_' + i).fadeIn('slow');
}

function awp_toggle_on(i)
{
    if ($('#awp_ag_' + i).css('display') != 'block')
        $('.awp_ag_display_' + i).fadeIn('slow');
}

function awp_toggle_all(toggle)
{
    var i = 0;
    while ($('.awp_ag_display_' + i).css('display'))
    {
        if (toggle == 0)
        {
            $('.awp_ag_display_' + i).fadeOut('slow');
            //$('.awp_ag_display_'+i).css('none');
        } else
            $('.awp_ag_display_' + i).fadeIn('slow');
        i++;
    }
}

function regIsDigit(fData)
{
    var reg = new RegExp(/^[0-9]+$/g);
    return (reg.test(fData));
}

function awp_copy_validation()
{
    if (!regIsDigit($("#awp_copy_src").val()))
    {
        alert(awp_copy_src);
        return;
    }
    if (!regIsDigit($("#awp_copy_tgt").val()))
    {
        alert(awp_copy_tgt);
        return;
    }
    if ($("#awp_copy_src").val() == $("#awp_copy_tgt").val() && $('#awp_copy_tgt_type').val() == "p")
    {
        alert(awp_copy_same);
        return;
    }

    if (awp_psv >= 1.5)
        awp_shops = awp_shops;
    else
        awp_shops = '';

    $.ajax({
        type: 'POST',
        url: baseDir + 'copy_combination_json.php',
        async: false,
        cache: false,
        dataType: "json",
        data: {'awp_random': awp_random, 'action': 'validate', 'id_product_src': $("#awp_copy_src").val(), 'id_product_tgt': $("#awp_copy_tgt").val(), 'type': $('#awp_copy_tgt_type').val(), 'id_lang': awp_id_lang, 'awp_shops': awp_shops},
        success: function (feed) {
            if (feed.invalid_src)
            {
                alert(awp_invalid_src);
                $("#awp_copy_confirmation").html("");
            } else if (feed.invalid_tgt)
            {
                alert(awp_invalid_tgt);
                $("#awp_copy_confirmation").html("");
            } else
            {
                $("#awp_copy_confirmation").html("<b>" + awp_are_you + " <b style=\"color:blue\">" + (typeof feed.product_src[awp_id_lang] == 'undefined' ? feed.product_src : feed.product_src[awp_id_lang]) + "</b> " + awp_to + " <b style=\"color:green\">" + (typeof feed.product_tgt[awp_id_lang] == 'undefined' ? feed.product_tgt : feed.product_tgt[awp_id_lang]) + "</b></b> <input class=\"button\" type=\"button\" value=\"" + awp_copy + "\" onclick=\"awp_copy_attributes()\" /> &nbsp; <input class=\"button\" type=\"button\" value=\"" + awp_cancel + "\" onclick=\"$('#awp_copy_confirmation').html('');\" /><br /><b style=\"color:red\">* " + awp_will_delete + "!</b>");
            }
        }
    });
}

function awp_copy_attributes()
{
    if (awp_psv >= 1.5)
        awp_shops = awp_shops;
    else
        awp_shops = '';
    $.ajax({
        type: 'POST',
        url: baseDir + 'copy_combination_json.php',
        async: false,
        cache: false,
        dataType: "json",
        data: {'awp_random': awp_random, 'action': 'copy', 'id_product_src': $("#awp_copy_src").val(), 'id_product_tgt': $("#awp_copy_tgt").val(), 'type': $('#awp_copy_tgt_type').val(), 'id_lang': awp_id_lang, 'awp_shops': awp_shops},
        success: function (feed) {
            //alert(feed.toSource());
            if (feed.complete == "1")
            {
                $("#awp_copy_confirmation").html("<b>" + awp_copied + "</b>");
            }
        }
    });
}


function awp_change_type(obj, id_attribute_group, group_color)
{
    $('#ipr_container_'+id_attribute_group).css('display',(obj.value == 'checkbox' || obj.value == 'radio' || obj.value == 'image' || obj.value == 'images' || obj.value == 'textbox' || obj.value == 'quantity'?'':'none'));
    $('#hin_container_'+id_attribute_group).css('display',(obj.value != 'dropdown' && obj.value != 'hidden'?'':'none'));
    $('#qty_zero_container_'+id_attribute_group).css('display',(obj.value == 'quantity'?'':'none'));
    $('#size_container_'+id_attribute_group).css('display',(group_color == 1 || (obj.value != 'dropdown' && obj.value != 'file' && obj.value != 'hidden')?'':'none'));
    $('#size2_container_'+id_attribute_group).css('display',(group_color == 1 || (obj.value != 'dropdown' && obj.value != 'file' && obj.value != 'hidden')?'':'none'));
    $('#resize_container_'+id_attribute_group).css('display',(group_color == 1?'':'none'));
    $('#required_container_'+id_attribute_group).css('display',(obj.value == 'image' || obj.value == 'images' || obj.value == 'dropdown' || obj.value == 'radio' || obj.value == 'textbox' || obj.value == 'textarea' || obj.value == 'file'?'':'none'));
    $('#max_limit_container_'+id_attribute_group).css('display',(obj.value == 'textbox' || obj.value == 'textarea'?'':'none'));
    $('#ext_container_'+id_attribute_group).css('display',(obj.value == 'file'?'':'none'));
    $('#il_container_'+id_attribute_group).css('display',(obj.value != 'hidden'?'':'none'));
    $('#chk_limit_container_'+id_attribute_group).css('display',(obj.value == 'checkbox'?'':'none'));
    $('#chk_limit_container_x_'+id_attribute_group).css('display',(obj.value == 'checkbox'?'':'none'));
    timeoutHide = setTimeout(function() {
        showHideExceptions($('#price_impact_per_char_' + id_attribute_group)); 
    }, 1000);
    
}
