/**
 * 2008 - 2017 Presto-Changeo
 *
 * MODULE Attribute Wizard
 *
 * @version   2.0.0
 * @author    Presto-Changeo <info@presto-changeo.com>
 * @link      http://www.presto-changeo.com
 * @copyright Copyright (c) permanent, Presto-Changeo
 * @license   Addons PrestaShop license limitation
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
 */
    var aw_tiny = false;
$( window ).resize(function() {
    recomputeColumnsHeight();
});


$(document).ready(function () {
    if ($("#uploadBtn").length > 0) {
        document.getElementById("uploadBtn").onchange = function () {
            document.getElementById("uploadFile").value = this.value;
        };

    }
    /* Fixing height and width for left / right column */
    recomputeColumnsHeight();
    
    $(document).on("click", "#main_menu .menu_item", function() {

        /* Add style to selected menu item */
        var $this = $(this);

        $this
            .siblings(".menu_item.selected").removeClass("selected")
            .end()
            .addClass("selected");
        
        /* Show / hide secondary menu */
        var secondaryMenu = $(this).attr('data-left-menu');        
        $('#secondary_menu .menu').hide();        
        $('#secondary_menu #' + secondaryMenu).show();
        
        var noOfVisibleMenu = false;
        $('#left_menu #secondary_menu .menu').each(function(){
            if ($(this).is(":visible"))
                noOfVisibleMenu = true;
        });

        var $secondaryMenu = $('#left_menu #secondary_menu');
        if (noOfVisibleMenu) {
            $('#left_menu #secondary_menu').css({
                "padding-top" : 0,
                "border" : "1px solid #d7dbde",
                "margin-top" : 30
            });
        } else {
            $('#left_menu #secondary_menu').css({
                "padding-top" : 15,
                "border" : "0px solid #d7dbde",
                "margin-top" : 0
            });
            
            var contentId = $(this).attr('data-content');
            $('.po_main_content').hide();
            $('#' + contentId).show();
        }
        
        $('.instructions_block').hide();
        
        /* Load secondary Menu functionality */
        var secondary_menu_item = $('#secondary_menu #' + secondaryMenu).find('.secondary_menu_item').first().attr('id');
        $('#'+secondary_menu_item).click();
        
        /* Display Left Contact US */
        $('.contact_form_left_menu').hide();
        if ($(this).attr('data-contact-us') == '1')
            $('.contact_form_left_menu').show();    
            
    });
    
    $(document).on('click', '#secondary_menu .secondary_menu_item' ,function() {        
        var leftMenuItemId = $(this).attr('id');
        leftMenuItemId = leftMenuItemId.replace('secondary_menu_item', '');
        
        /* Add style to selected menu item */
        $('#secondary_menu .secondary_menu_item').removeClass('selected');
        $(this).addClass('selected');
        
        /* Hide / Show Instructions */
        $('.instructions_block').hide();
        var instructionsId = $(this).attr('data-instructions');
        $('#' + instructionsId).show();
        
        /* Hide / Show Block contents */
        var contentId = $(this).attr('data-content');
        
        $('.po_main_content').hide();
        $('#' + contentId).show();
        
        recomputeColumnsHeight();
    });
    
    $('#main_menu .menu_item').first().click();
    
    $(document).on('click', '.menu_header' ,function() {
        var classArrow = $(this).find('#left_menu_arrow').attr('class');
        if (classArrow == 'arrow_up') {
            $(this).find('span.arrow_up').attr('class', 'arrow_down');
            $(this).parent().find('.secondary_submenu').slideToggle('slow');
        } else if (classArrow == 'arrow_down') {
            $(this).find('span.arrow_down').attr('class', 'arrow_up');
            $(this).parent().find('.secondary_submenu').slideToggle('slow');
        
        }
    });

    // since MCE editor JS file loading using AJAX request, we wait while tinyMCE object will be available
    // and stop waiting after 5 sec if no editor object initialized
    var mce_wait_interval = setInterval(aw_tiny_mce_init, 300);
    var mce_wait_timeout = setTimeout(function () {
        clearInterval(mce_wait_interval);
    }, 5000);

    // initial Tiny MCE load
    function aw_tiny_mce_init() {
        if (typeof tinyMCE !== "undefined") {
            if (tinyMCE.activeEditor == null || tinyMCE.activeEditor.isHidden() != false) {
                var tiny_mce_all_on = $("#tiny_mce_all_on:checked");
                if (tiny_mce_all_on.length) {
                    tiny_mce_all_on.click();
                } else {
                    $(".tiny_mce_on:checked").click();
                }
                clearInterval(mce_wait_interval);
                clearTimeout(mce_wait_timeout);
            }
        }
    }

    // manage Tiny MCE buttons
    var presto_switch = $(".presto-switch");
    presto_switch.on("click", "input", function () {
        // off
        aw_tiny = false;
        var color = "#aab3bb";
        var suffix = "off";
        // on
        var is_on = $(this).prop("class").match(/_on$/);
        if (is_on) {
            aw_tiny = true;
            color = '#86d151';
            suffix = "on";
        }

        $(this).prop("checked", true).siblings("input").prop("checked", false);
        $(this).parent().css("background-color", color);

        // all
        if ($(this).prop("class").match(/_all/)) {
            presto_switch.find(".tiny_mce_" + suffix).click();
            return;
        }

        // enable mce for certain element
        var mce_field = $(this).closest(".column_format").find(".autoload_rte");
        if (is_on) {
            tinyMCEInit("textarea", "#" + mce_field.prop("id"));
        } else {
            tinyMCE.remove("#" + mce_field.prop("id"));
        }
    });
    
    $(document).on('click', '.display_more' ,function() {
        
        if (!$(this).hasClass('hide_more')) {            
            $(this).parent().find('.hideADN').each(function(){                
                if ($(this).hasClass('row_format'))
                    $(this).show();
            });
            
            $(this).hide();
            $(this).parent().find('.hide_more').show();
        } else {            
            $(this).parent().find('.hideADN').each(function(){               
                if ($(this).hasClass('row_format'))
                    $(this).hide();
                if ($(this).hasClass('display_more'))
                    return false;
            });
            $(this).hide();
            $(this).parent().find('.display_more').not('.hide_more').show();
        }
    });
    

     $('#open_module_upgrade').fancybox({
            helpers : {
                overlay : {
                    locked : false,
                    css : {
                        'background' : 'transparent'
                    }
                }
            },
            'padding': 0,
            'closeBtn': true,
            'autoScale': true,
            'transitionIn': 'elastic',
            'transitionOut': 'elastic',
            'speedIn': 500,
            'speedOut': 300,
            'autoDimensions': true
    }).click();
    
    $('.info_alert').fancybox({
            helpers : {
                overlay : {
                    locked : false,
                    css : {
                        'background' : 'transparent'
                    }
                }
            },
            'padding': 0,
            'closeBtn': true,
            'autoScale': true,
            'transitionIn': 'elastic',
            'transitionOut': 'elastic',
            'speedIn': 500,
            'speedOut': 300,
            'autoDimensions': true
    });
    
    /* Sorting Attribute Values */
    if ($('.attribute_values_sort').length > 0) 
        $(".attribute_values_sort").sortable();
    /* Sorting Attribute Groups */
    if ($('.attribute_groups_sort').length > 0) 
        $(".attribute_groups_sort").sortable(); 
    /* Call php to update the sorting of attributes*/
    if ($('.attribute_groups_sort').length > 0) {
        $( ".attribute_groups_sort, .attribute_values_sort" ).on( "sortstop", function( event, ui ) {

            var idsInOrder = $(this).sortable("toArray");
            var firstValue = idsInOrder[0];
            var firstValueOpt = firstValue.split('_');
            if (firstValueOpt[0] == 'row') {

                var groups = [];
                $.each(idsInOrder, function( index, value ) {
                    if (value != '') {
                        var ids = value.split('_');
                        groups.push(ids[1]);
                    }
                });

                params = 'ajaxProductsPositions=true&attribute_group_order=1&awp_random='+awp_random+'idsInOrder=' + groups;
     
            } else {
                var values = [];
                var id_group = 0;
                $.each(idsInOrder, function( index, value ) {
                    if (value != '') {
                        var ids = value.split('_');
                        values.push(ids[1]);
                        id_group = ids[0];
                    }
                });

                params = 'ajaxProductsPositions=true&attribute_value_order=1&awp_random='+awp_random+'&id_group='+id_group+'&idsInOrder=' + values + '&groupsPerPage=' + attributes_per_page + '&page=' + current_page;
            }

            $.ajax({
                type: 'POST',
                url: baseDir + 'wizard_json.php',
                async: true,
                data: params
            });


        });
    
    }
    presto_toggle_all(0);
    presto_toggle_all(1, 0);
    
});

    function recomputeColumnsHeight() {

        $(".columns").each(function(){
            $(this).find(".left_column").height('auto');
            $(this).find(".right_column").height('auto');
            var
                $this = $(this),
                $leftColumn = $this.find(".left_column"),
                $rightColumn = $this.find(".right_column"),
                heightLeftColumn = $leftColumn.height(),
                heightRightColumn = $rightColumn.height();

            
            
            $leftColumn.add($rightColumn).css("height", function () {
                return heightLeftColumn > heightRightColumn ? heightLeftColumn : heightRightColumn;
            });
            
            if ($leftColumn.find("a.info_alert").length <= 0)
                $leftColumn.css("padding-right", function () {
                    return $this.closest("#advanced_settings").length > 0 ? "0px" : "25px";
                });
        });
    }

    function presto_toggle_all(toggle, stop)
    {
        var i = 0;
        if (toggle == 0)
            recomputeColumnsHeight();
        while ($("div[data-order='"+i+"']").css('display'))
        {
            $("div[data-order='"+i+"']").each(function() {
                $(this).toggle(toggle == 1);
            });
            if (stop == i)
               return;
            i++;
        }

        var $expandAll = $(".expand_all");
        var $expandCollapse = $(".expand_collapse");

        $expandAll.find("span.expand, span.arrow_up").toggle(toggle == 1);
        $expandAll.find("span.collapse, span.arrow_down").toggle(toggle != 1);

        $expandCollapse
            .find("span.arrow_up").toggle(toggle == 1)
            .end()
            .find("span.arrow_down").toggle(toggle != 1);
    
        recomputeColumnsHeight();
    }
    
    function presto_toggle(id, toggle) 
    {
        if (typeof toggle != 'undefined') {
            $("div[data-id='"+id+"']").each(function() {
                $(this).toggle(toggle);              
            });
            $("#expand_"+id+" span.arrow_up").toggle(true);
            $("#expand_"+id+" span.arrow_down").toggle(false);   
                 
 
        } else {
            $("div[data-id='"+id+"']").each(function() {
                $(this).toggle();              
            });
            $("#expand_"+id+" span").toggle();
            
            arrow_up = $("#expand_"+id+" span.arrow_up").is(":visible"); 
            arrow_down = $("#expand_"+id+" span.arrow_down").is(":visible"); 
        } 
        
        
        recomputeColumnsHeight();
    }
    
   
