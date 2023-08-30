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

PPATAdminProductTabPricesController = function(canvas) {

	var self = this;
	self.canvas = canvas;
	self.$canvas = $(canvas);

	self.render = function() {
		MPTools.waitStart();
        var url = MPTools.joinUrl(module_ajax_url_ppat, 'section=adminproducttab&route=ppatadminproducttabpricescontroller&action=render');

		var post_data = {
			'id_product': id_product
		};

		$.ajax({
			type: 'POST',
			url: url,
			async: true,
			cache: false,
			data: post_data,
			success: function (html_result) {
				self.$canvas.html(html_result);
				self.refreshOptionsList();
				MPTools.waitEnd();
			}
		});
	};

	/**
	 * save the product option label
	 */
	self.processUpdateLabel = function() {
		MPTools.waitStart();
        var url = MPTools.joinUrl(module_ajax_url_ppat, 'section=adminproducttab&route=ppatadminproducttabpricescontroller&action=processupdatelabel');

		$.ajax({
			type: 'POST',
			url: url,
			async: true,
			cache: false,
			data: self.$canvas.find("#form-ppat-options-label :input, select").serialize(),
			success: function (result) {
				MPTools.waitEnd();
			}
		});
	};

	/**
	 * update the positions of the options in the list
 	 */
	self.processOptionsPositions = function() {

		var positions = [];

		$("#ppat-options-list-table tbody tr").each(function () {
			if (typeof $(this).attr("data-id_option") !== 'undefined')
				positions.push($(this).attr("data-id_option"));
		});
        var url = MPTools.joinUrl(module_ajax_url_ppat, 'section=adminproducttab&route=ppatadminproducttabpricescontroller&action=processoptionspositions');

		$.ajax({
			type: 'POST',
			url: url,
			async: true,
			cache: false,
			data: {
				'ids_option' : positions
			},
			success: function (result) {
			}
		});

	};

	/**
	 * Refresh the product table options list
 	 */
	self.refreshOptionsList = function() {

		$options_list = self.$canvas.find("#ppat-options-list-wrapper");
        var url = MPTools.joinUrl(module_ajax_url_ppat, 'section=adminproducttab&route=ppatadminproducttabpricescontroller&action=refreshoptionslist');
		MPTools.waitStart();

		$.ajax({
			type: 'POST',
			url: url,
			async: true,
			cache: false,
			data: {
				id_product : id_product
			},
			success: function (html_result) {
				$options_list.html(html_result);
				MPTools.waitEnd();
				$("#ppat-options-list-table tbody").sortable({
					update: function (event, ui) {
						self.processOptionsPositions();
					}
				}).disableSelection();
			}
		});
	};

	/**
	 * Render the edit option form
 	 * @param id_option
	 */
	self.renderEditOption = function(id_option) {
        var url = MPTools.joinUrl(module_ajax_url_ppat, 'section=adminproducttab&route=ppatadminproducttabpricescontroller&action=rendereditoption');

		$.ajax({
			type: 'POST',
			url: url,
			async: true,
			cache: false,
			data: {
				id_option : id_option,
				id_product : id_product
			},
			success: function (html_result) {
				$("#ppat-option-edit-wrapper").html(html_result);
				prestaShopUiKit.init();
			}
		});
	};


	/**
	 * Add new option value
 	 */
	self.processAddOptionValue = function()	{
        var url = MPTools.joinUrl(module_ajax_url_ppat, 'section=adminproducttab&route=ppatadminproducttabpricescontroller&action=processoptionvalue');
		MPTools.waitStart();
		
		$.ajax({
			type: 'POST',
			url: url,
			async: true,
			cache: false,
			data: self.$canvas.find("#form-ppat-option-value :input, select").serialize(),
			success: function (result) {
				MPTools.waitEnd();
				self.refreshOptionsList();
			}
		});
	};

	/**
	 * Delete option
 	 * @param id_option
	 */
	self.processDeleteOption = function(id_option) {
        var url = MPTools.joinUrl(module_ajax_url_ppat, 'section=adminproducttab&route=ppatadminproducttabpricescontroller&action=processdeleteoption');

		$.ajax({
			type: 'POST',
			url: url,
			async: true,
			cache: false,
			data: {
				id_option : id_option
			},
			success: function (result) {
				self.refreshOptionsList();
			}
		});
	};

	/**
	 * Delete option
 	 * @param id_option
	 */
	self.processEditOption = function(id_option) {
        var url = MPTools.joinUrl(module_ajax_url_ppat, 'section=adminproducttab&route=ppatadminproducttabpricescontroller&action=processeditoption');
		var form_data = self.$canvas.find("#ppat-option-edit-wrapper :input").serialize();

		MPTools.waitStart();

		$.ajax({
			type: 'POST',
			url: url,
			async: true,
			cache: false,
			data: form_data,
			success: function (result) {
				MPTools.waitEnd();
			}
		});
	};


	/**
	 * on option selected
 	 * @param id_option
	 */
	self.onOptionSelect = function(id_option) {
        var url = MPTools.joinUrl(module_ajax_url_ppat, 'section=adminproducttab&route=ppatadminproducttabpricescontroller&action=renderpricetable');

		var post_data = {
			'id_option': id_option
		};

		$.ajax({
			type: 'POST',
			url: url,
			async: true,
			cache: false,
			data: post_data,
			success: function (html_result) {
				self.$canvas.find("#ppat-table-wrapper").html(html_result);
				self.renderEditOption(id_option);
			}
		});
	};


	self.init = function() {
		self.render();
	};
	self.init();


	/**
	 * Options Label Save click
 	 */
	$("body").on("click", "#ppat-btn-option-label-save", function() {
		self.processUpdateLabel();
		return false;
	});

	/**
	 * Table Option Click
 	 */
	$("body").on("click", ".ppat-table-option", function() {
		var id_option = $(this).attr("data-id_option");
		$(this).parents("table").find("tr").removeClass("selected");
		$(this).addClass("selected");
		self.onOptionSelect(id_option);
		return false;
	});


	/**
	 * Option Value Add click
 	 */
	$("body").on("click", "#ppat-btn-option-value-add", function() {
		self.processAddOptionValue();
		return false;
	});

	/**
	 * Delete option click
 	 */
	$("body").on("click", "#ppat-options-list .ppat-option-delete", function() {
		self.processDeleteOption($(this).attr("data-id_option"));
		return false;
	});

	$("body").on("click", "#ppat-button-option-edit-save", function() {
		self.processEditOption(self.$canvas.find("input#id_option").val());
		return false;
	});

};

