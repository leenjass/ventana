/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function changeAttributeType(elem) {
    type = elem.val();
    thisElem = elem;
    thisParent = thisElem.parent();
    types = ["checkbox","radio","textbox","quantity","calculation","image","images","textarea","file","hidden","dropdown"];
    $.each( types, function( key, value ) {      
        thisParent.find('.' + value + 'Opt').hide();
    });
    
    thisParent.find('.' + type + 'Opt').show();

}
var timeoutHide;
function showHideExceptions(thisPriceImpact) {
    id_attr = thisPriceImpact.attr('data-attr');
    if ($('select[name="group_type_' + id_attr + '"]').val() == 'textarea' || $('select[name="group_type_' + id_attr + '"]').val() == 'textbox') {
        if (thisPriceImpact.is(':checked')) {
            $('#exceptions_container_' + id_attr).find('.two_columns').show();
        } else {
            $('#exceptions_container_' + id_attr).find('.two_columns').hide();
        }
    } else {
        $('#exceptions_container_' + id_attr).find('.two_columns').hide();
    }
}

$(document).ready(function () {
    $(document).on('click', '.price_impact_per_char' ,function() {    
        showHideExceptions($(this)); 
        
    });
    $('.price_impact_per_char').each(function(){
        showHideExceptions($(this));
    });
    
    $(document).on('change', '.attribute_type' ,function() {     
        changeAttributeType($(this)); 
        
    });
    
    $(document).on('click', '.column_5 .delete_btn', function() {
        
        btnIdClicked = $(this).attr('id');
        btnIdClicked = btnIdClicked.replace('delete_image_l', '');
      
        id_attribute = btnIdClicked;

        id_group_pos = $(this).parent().attr('group');
        
        $.ajax({
            type: 'POST',
            url: baseDirModule + 'image_upload.php',
            async: false,
            cache: false,
            data: {'awp_random': awp_random, 'action':'delete_layered_image','id_attribute':id_attribute,'id_group_pos':id_group_pos},
            success:function(feed) {
                
                $('#image_container_l'+btnIdClicked).html('');
                
                $('#delete_image_l'+btnIdClicked).addClass('hideADN');
                
                $('#upload_button_l'+btnIdClicked).removeClass('edit_btn');
                $('#upload_button_l'+btnIdClicked).addClass('upload_btn');
                $('#upload_button_l'+btnIdClicked).val(awp_upload_img);
                
            }
	});
    });
        
    $(document).on('click', '.column_3 .delete_btn', function() {
        
        btnIdClicked = $(this).attr('id');
        btnIdClicked = btnIdClicked.replace('delete_button_', '');
       
        id_group = $('#id_group_'+btnIdClicked).val();

        
        $.ajax({
            type: 'POST',
            url: baseDirModule + 'image_upload.php',
            async: false,
            cache: false,
            data: {'awp_random': awp_random, 'action':'delete_image','id_group':id_group},
            success:function(feed) {
                
                $('#image_container_'+btnIdClicked).html('');
                
                $('#delete_button_'+btnIdClicked).addClass('hideADN');
                
                $('#upload_button_'+btnIdClicked).removeClass('edit_btn');
                $('#upload_button_'+btnIdClicked).addClass('upload_btn');
                $('#upload_button_'+btnIdClicked).val(awp_upload_img);
                
                $('#edit_new_block_'+btnIdClicked).removeAttr('data-id');
                $('#edit_new_block_'+btnIdClicked).removeAttr('data-order');
            }
	});
    });

   // menu tabs
    if(window.location.hash) {
      var tab = window.location.hash;
      tab = tab.substr(1);
    } else {
      var tab = ''; 
    }

    if(tab != '' && $('#secondary_menu .secondary_menu_item[data-content="'+ tab +'"]').length > 0) {
        $('#secondary_menu .secondary_menu_item').removeClass('selected');
        $('#secondary_menu .secondary_menu_item[data-content="'+ tab +'"]').addClass('selected');
        $('.instructions_block').hide();
        var instructionsId = $('#secondary_menu .secondary_menu_item[data-content="'+ tab +'"]').attr('data-instructions');
        $('#' + instructionsId).show();

        $('.po_main_content').hide();
        $('#' + tab).show();
    }

    $('select[name="groups_count"]').on('change', function(){
        var groups_count = $(this).val();
        window.location.href = module_uri + '&p=1&n=' + groups_count + '#advanced_settings';
    });
});

/* Copy attributes functionality */
$(document).on('click', '#aw_copy_validate', function () {
    if (!regIsDigit($("#aw_copy_src").val()))
    {
        alert(aw_copy_src);
        return;
    }
    if (!regIsDigit($("#aw_copy_tgt").val()))
    {
        alert(aw_copy_tgt);
        return;
    }
    if ($("#aw_copy_src").val() == $("#aw_copy_tgt").val() && $('#aw_copy_tgt_type').val() == "p")
    {
        alert(aw_copy_same);
        return;
    }
    $.ajax({
        type: 'POST',
        url: baseDirModule + 'copy_combination_json.php',
        async: false,
        cache: false,
        dataType: "json",
        data: {'awp_random': awp_random, 'action': 'validate', 'id_product_src': $("#aw_copy_src").val(), 'id_product_tgt': $("#aw_copy_tgt").val(), 'type': $('#aw_copy_tgt_type').val(), 'awp_shops': awp_shops},
        success: function (feed) {
            if (feed.invalid_src)
            {
                alert(aw_invalid_src);
                $("#aw_copy_confirmation").html("");
            } else if (feed.invalid_tgt)
            {
                alert(aw_invalid_tgt);
                $("#aw_copy_confirmation").html("");
            } else
            {
                $("#aw_copy_confirmation").html('<div class="special_instructions_header"> ' + aw_are_you + " <b style=\"color:blue\">" + feed.product_src[awp_id_lang] + ' ' + aw_to + " <b style=\"color:green\">" + (feed.product_tgt[awp_id_lang].length > 1 ? feed.product_tgt[awp_id_lang] : feed.product_tgt) + "</b></b> <br/> <input class=\"submit_button\" type=\"button\" value=\"" + aw_copy + "\" onclick=\"aw_copy_attributes()\" /> &nbsp; <input class=\"submit_button\" type=\"button\" value=\"" + aw_cancel + "\" onclick=\"$('#aw_copy_confirmation').html('');\" /></div><br /><b style=\"color:red\">* " + aw_will_delete + "!</b>");
            }
        }
    });
});

function regIsDigit(fData)
{
    var reg = new RegExp(/^[0-9]+$/g);
    return (reg.test(fData));
}

function aw_copy_attributes()
{
    $.ajax({
        type: 'POST',
        url: baseDirModule + 'copy_combination_json.php',
        async: false,
        cache: false,
        dataType: "json",
        data: {'awp_random': awp_random, 'action': 'copy', 'id_product_src': $("#aw_copy_src").val(), 'id_product_tgt': $("#aw_copy_tgt").val(), 'type': $('#aw_copy_tgt_type').val(), 'awp_shops': awp_shops},
        success: function (feed) {
            //alert(feed.toSource());
            if (feed.complete == "1")
            {
                $("#aw_copy_confirmation").html('<div class="special_instructions_header">' + aw_copied + "</div>");
            }
        }
    });
}


$(document).ready(function () {

    if ($('#awp_in_page').is(':checked')) {
        $('.popup_config').fadeOut(1000);
    }
    $(document).on('click', '#awp_in_page', function () {
        $('.popup_config').fadeOut(1000);
    });
    $(document).on('click', '#awp_in_popup', function () {
        $('.popup_config').fadeIn(1000);
    });
});


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
                   
                if (typeof tinymce.get("description_"+gid) != 'undefined')
                    tinymce.get("description_"+gid).setContent($("#description_"+gid+"_"+id).val());
   
            }
            else
            {                

                $("#description_"+gid+"_"+id).val($("#description_"+gid).val());
                $("#group_header_"+gid+"_"+id).val($("#group_header_"+gid).val());
                if (typeof tinymce.get("description_"+gid) != 'undefined')
                    $("#description_"+gid+"_"+id).val(tinymce.get("description_"+gid).getContent());
                    
            }
        }
    }
}
