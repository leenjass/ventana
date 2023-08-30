/*
 * Smartsupp Live Chat integration module.
 * 
 * @package   Smartsupp
 * @author    Smartsupp <vladimir@smartsupp.com>
 * @link      http://www.smartsupp.com
 * @copyright 2016 Smartsupp.com
 * @license   GPL-2.0+
 *
 * Plugin Name:       Smartsupp Live Chat
 * Plugin URI:        http://www.smartsupp.com
 * Description:       Adds Smartsupp Live Chat code to PrestaShop.
 * Version:           2.1.10
 * Author:            Smartsupp
 * Author URI:        http://www.smartsupp.com
 * Text Domain:       smartsupp
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/

jQuery(document).ready( function($) {
    var ssKey = $('input#smartsupp_key').val(),
        pageCreate = $('#smartsupp_page_create'),
        pageConnect = $('#smartsupp_page_connect'),
        pageConfig = $('#smartsupp_page_config'),
        
        formConfig = $('#configuration_form.smartsupp'),
        formConfigInput = $('#configuration_form.smartsupp #SMARTSUPP_OPTIONAL_API'),
        formConfigControl = $('#SMARTSUPP_OPTIONAL_API').next(),
        formConfigText = formConfigControl.html(),
        formConfigReplace = '<a href="https://developers.smartsupp.com/?utm_source=Prestashop&utm_medium=integration&utm_campaign=link" target="_blank">Smartsupp API</a>',
        formBtnCreate = $('#form_btn_create'),
        formBtnConnect = $('#form_btn_connect'),

        btnPageCreate = $('#btn_page_create'),
        btnPageConnect = $('#btn_page_connect'),
        btnDeactivate = $('#btn_deactivate')

    formConfigInput.height('117px')
    formConfigControl.html(formConfigText.replace('#', formConfigReplace))

    if (ssKey != '') {
        showPage('config', false)
    }

    function showPage(type, msg, key, email) {
        pageCreate.hide()
        pageConnect.hide()
        pageConfig.hide()
        formConfig.hide()

        if (key) {
            $("input#smartsupp_key").val(key)
        } else {
            $("input#smartsupp_key").val()
        }
        
        if (email) {
            $("#smartsupp_page_config .header-user__email").html(email)
        } else {
            $("#smartsupp_page_config .header-user__email").html('')
        }

        // Show Create page
        if (type === 'create') {
            if (msg) {
                $("#smartsupp_page_create .alerts").show()
                $("#smartsupp_page_create .alerts .alert").html(msg)
            } else {
                $("#smartsupp_page_create .alerts").hide()
            }

            pageCreate.show()
        }

        // Show Connect page
        if (type === 'connect') {
            if (msg) {
                $("#smartsupp_page_connect .alerts").show()
                $("#smartsupp_page_connect .alerts .alert").html(msg)
            } else {
                $("#smartsupp_page_connect .alerts").hide()
            }

            pageConnect.show()
        }

        // Show Config page
        if (type === 'config') {
            pageConfig.show()
            formConfig.show()
        }
    }

    btnPageConnect.click(function() {
        showPage('connect', false, false, false)
    })

    btnPageCreate.click(function() {
        showPage('create', false, false, false)
    })

    formBtnConnect.click(function() {
        $.ajax({
            url: ajax_controller_url,
            async: false,
            type: 'POST',
            data: {
                action: 'login', 
                email: $( "#smartsupp_page_connect #SMARTSUPP_EMAIL" ).val(), 
                password: $( "#smartsupp_page_connect #SMARTSUPP_PASSWORD" ).val()
            },
            dataType: 'json',
            headers: { "cache-control": "no-cache" },
            success: function(data) {
                if (data.error === false) {
                    showPage('config', false, data.key, data.email)
                } else {
                    showPage('connect', data.message, false, false)
                }
            }
        });
    });

    formBtnCreate.click(function() {
        $.ajax({
            url: ajax_controller_url,
            async: false,
            type: 'POST',
            data: {
                action: 'create', 
                email: $( "#smartsupp_page_create #SMARTSUPP_EMAIL" ).val(), 
                password: $( "#smartsupp_page_create #SMARTSUPP_PASSWORD" ).val()
            },
            dataType: 'json',
            headers: { "cache-control": "no-cache" },
            success: function(data) {
                if (data.error === false) {
                    showPage('config', false, data.key, data.email)
                } else {
                    showPage('create', data.message, false, false)
                }
            }
        });
    });

    btnDeactivate.click(function() {
        $.ajax({
            url: ajax_controller_url,
            async: false,
            type: 'POST',
            data: {
                action: 'deactivate'
            },
            dataType: 'json',
            headers: { "cache-control": "no-cache" },
            success: function(data) {
                showPage('connect', false, false, false);
            }
        });
    });
});    