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
*  @copyright  2015-2021 Musaffar
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Property of Musaffar Patel
*/

PPATFrontCartController = function() {
	var self = this;

	self.module_folder = 'productpriceareatable';

	/**
	 * Adds customization text, qty and delete to product customization lines in cart
	 */
	self.addCustomizations = function() {
		$cart = $(".cart-overview");

		$cart.find('.product-line-grid-body').each(function(i, obj) {
            if ($(obj).find(".customization-modal").length == 0 || $(obj).find(".module-customization-line").length > 0) {
                return;
            }
            $modal = $(obj).find(".customization-modal");

			if (typeof $modal.attr("id") === 'undefined') {
				return;  // continue next each loop
			}

			id_product = $(obj).parents(".cart-item").find(".remove-from-cart").attr("data-id-product");
			id_customization = $modal.attr("id").replace('product-customizations-modal-', '');
			label = $modal.find(".value").html().trim();

			var customization_line_html = '';

			$modal.find(".product-customization-line").each(function(index, item) {
				label = $(item).find(".value").html().trim();
				customization_line_html += '<div class="ppbs_customization_line module-customization-line" data-id_product="' + id_product + '" data-id_customization="' + id_customization + '">';
				customization_line_html += label;
				customization_line_html += '</div>';
			});
			$(obj).find("a[data-toggle='modal']").hide();
			$(customization_line_html).insertAfter($(obj).find("a[data-toggle='modal']"));
		});
	};

	self.init = function() {
		self.addCustomizations();
	};
	self.init();

	/**
	 * On Cart Updated
	 */
	prestashop.on('updatedCart', function (event) {
		self.addCustomizations();
	});
};