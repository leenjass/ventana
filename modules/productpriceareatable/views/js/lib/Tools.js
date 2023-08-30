/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Musaffar Patel <musaffar.patel@gmail.com>
*  @copyright  2015-2017 Musaffar
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Property of Musaffar Patel
*/

MPTools = {

	errors: [],

	waitStart: function() {
		$("body").append("<div class='mp-wait-wrapper'><svg class='circular'><circle class='path' cx='50' cy='50' r='20' fill='none' stroke-width='2' stroke-miterlimit='10'/></svg></div>");
	},

	waitEnd: function () {
		$(".mp-wait-wrapper").remove();
	},

	highlightElement: function (element) {
		$(element).parent().closest('div').addClass("has-danger");
	},

	/**
	 * remove all fields highlighted red and hied alert div
	 */
	resetValidation : function(form) {
		$(form).find("div.has-danger").removeClass("has-danger");
		$(form).find(".mp-errors").hide();
	},

	addError: function(element, validation_message) {
		error = {
			'element' : element,
			'validation_message' : validation_message
		};
		MPTools.errors.push(error);
	},

	displayErrors: function(form) {
		var message = 'Please complete all fields marked in red below<br>';

		$(form).find(".mp-errors").fadeIn();

		if (MPTools.errors.length > 0) {
			for (i=0; i <= MPTools.errors.length - 1; i++) {
				error = MPTools.errors[i];
				MPTools.highlightElement('#' + error.element);
				message = message + error.validation_message + '<br>';
			}
		}

		$(form).find(".mp-errors").html(message);

	},

	validateForm: function(form) {

		MPTools.resetValidation(form);

		var has_errors = false;

		$(form).find('input').each(function () {
			var tag = $(this).get(0).tagName;

			if (tag == 'INPUT' && $(this).attr("type") == 'text') {
				if ($(this).attr("data-required") == 'required' && $(this).val() == '') {
					MPTools.highlightElement(this);
					has_errors = true;
				}
			}

			if (tag == 'INPUT' && $(this).attr("type") == 'hidden') {
				if ($(this).attr("data-required") == 'required' && $(this).val() == '') {
					MPTools.addError($(this).attr("id"), $(this).attr("data-validation-message"));
					has_errors = true;
				}
			}

		});

		if (has_errors)
			MPTools.displayErrors(form);

		return !has_errors;
	},

    /**
     * Merge a url wioth extra param string
     * @param url
     * @param param_string
     * @returns {string}
     */
    joinUrl: function (url, param_string) {
        var return_url = '';

        if (url.indexOf('?') > 0) {
            return_url = url + '&' + param_string;
        } else {
            return_url = url + '?' + param_string;
        }
        return return_url;
    },

    /**
     * display message in the admin (usually after an action has ben executed such as submitting form)
     * @param wrapper
     * @param status_code
     * @param message
     */
    adminMessage: function (wrapper, status_code, message) {
        let icon = '';
        if (status_code === 'success') {
            icon = 'done';
        }

        if (status_code === 'error') {
            icon = 'close';
        }

        let html = '<div class="mp-admin-message ' + status_code + '"><div class="icon"><i class="material-icons">' + icon + '</i></div><span>' + message + '</span></div>';
        $(wrapper).prepend(html);
        var new_position = $(wrapper).find(".mp-admin-message").offset();
        $('html, body').stop().animate({scrollTop: new_position.top - 200}, 500);

        $(wrapper).find(".mp-admin-message").delay(5000).fadeOut('slow', function () {
            $(this).remove();
        });
    }
};