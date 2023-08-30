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

PPBSPopup = function(id, dom_parent) {
	var self = this;
	self.dom_parent = "body";
	self.id = id;

	self._createOverlay = function () {
		$(self.dom_parent).prepend("<div id='ppbs-popup-wrapper'><span id='ppbs-popup-close'><i class='material-icons'>clear</i></span><div class='ppbs-popup-content'></div></div>");
		$(self.dom_parent).prepend("<div id='ppbs-overlay'></div>");
	};

	self._positionSubPanels = function () {
		$("#ppbs-popup-wrapper .ppbs-popup-content .subpanel").each(function() {
			var x = $("#ppbs-popup-wrapper").width();
			var height = $("#ppbs-popup-wrapper").height();
			$(this).css("left", x + "px");
			$(this).css("width", x + "px");
			$(this).css("height", height + "px");
		});
	};

	self.showSubPanel = function(id) {
		$("#" + self.id).find("#" + id).animate({
			left: "0px",
		}, 250, function () {
		});
	};

	self.hideSubPanel = function (id) {
		var x = $("#ppbs-popup-wrapper").width();
		$("#" + self.id).find("#" + id).animate({
			left: x + "px",
			}, 250, function () {
			}
		);
	};


	self.loadContent = function(url, data) {
		$("#ppbs-popup-wrapper").load(url,
			function () {
				$("#ppbs-overlay").fadeIn();
				self._positionSubPanels();
			}
		);
	};

	self.show = function() {
		self._createOverlay();
	};

	self.showContent = function(url, data, callback_function) {
		self._createOverlay();
		$(".ppbs-popup-content").load(url,
			function () {
				$("#ppbs-overlay").fadeIn();
				$(".ppbs-popup-content").attr('id', self.id);
				self._positionSubPanels();

				if (typeof(callback_function) === 'function')
					callback_function();
			}
		);
	};

	self.close = function() {
		$("#ppbs-popup-wrapper").remove();
		$("#ppbs-overlay").remove();
	};

	/* events */
	$("body").on("click", "#ppbs-popup-close", function () {
		self.close();
	});

	self.init = function(dom_parent) {
		if (typeof dom_parent !== 'undefined')
			self.dom_parent = dom_parent;
	}
	self.init(dom_parent);
}