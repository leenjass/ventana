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

PPATAdminConfigEditUnitController = function(wrapper) {
	var self = this;
	self.wrapper = wrapper;
	self.$wrapper = $(wrapper);

	/* function render main form into the tab canvas */
	self.render = function(id_unit) {
		var url = module_config_url + '&route=ppatadminconfiggeneralcontroller&action=rendereditform';
		breadcrumb.add('Add / Edit Unit', url);

		MPTools.waitStart();
		$.ajax({
			type: 'POST',
			url: url,
			async: true,
			cache: false,
			data: {
				'id_unit' : id_unit
			},
			success: function (html_result) {
				self.$wrapper.html(html_result);
				MPTools.waitEnd();
			}
		});
	};

	self.processForm = function() {
		var url = module_config_url + '&route=ppatadminconfiggeneralcontroller&action=processeditform';

		var form_data = self.$wrapper.find("#form-ppat-unit-edit :input").serialize();

		MPTools.waitStart();
		$.ajax({
			type: 'POST',
			url: url,
			async: true,
			cache: false,
			data: form_data,
			success: function (result) {
				MPTools.waitEnd();
				breadcrumb.cancel();
			}
		});
	};

	self.init = function() {
	};
	self.init();

	/**
	 * Events
 	 */

	/**
	 * Cancel edit unit button click
	 */
	$("body").on("click", self.wrapper + ' #btn-ppat-edit-save', function () {
		self.processForm();
	});



	/**
	 * Cancel edit unit button click
	 */
	$("body").on("click", self.wrapper + ' #btn-ppat-edit-cancel', function () {
		console.log('clicked');
		breadcrumb.cancel();
	});


};