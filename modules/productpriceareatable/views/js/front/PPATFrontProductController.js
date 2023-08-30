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

PPATFrontProductController = function(wrapper, after_element) {
	var self = this;
	self.wrapper = wrapper;
	self.$wrapper = $(wrapper);
	self.module_folder = 'productpriceareatable';
	self.product_info = [];
	self.route = 'ppatfrontproductcontroller';
    self.pco_event = {
        price: 0,
        price_impact: 0,
    };
    self.status = 0;
    self.debug = false;

	/**
	 * Get the product ID
 	 */
	 self.getProductID = function() {
		return $("#add-to-cart-or-refresh input[name='id_product']").val()
	};


	/**
	 * Round up float to 2 decimal places
	 * @param value
	 * @returns {number|*}
	 */
	self.toNearestPenny = function (value) {
		value = value * 100;
		value = Math.ceil(value) / 100;
		return value;
	};

    self.customRound = function(num, places) {
        places = Math.pow(10, places);
        return Math.ceil(num * places) / places;
	};
	
	/**
	 * removes alphabetical characters to return a decimal value only (price)
	 */
	self.removeFormatting = function (number) {
		var number = number.toString();

		if (number.indexOf('.') > 0)
			number = number.replace(",", "");
		else
			number = number.replace(",", ".");
		number = number.replace(/[^\d\.-]/g, '');
		return (number);
	};

	/**
	 * Apply extra discounts
	 * @param value
	 * @returns {*}
	 */
	self.applyExtraDiscounts = function (value) {
		self.product_info.group_reduction = parseFloat(self.product_info.group_reduction);
		if (self.product_info.group_reduction > 0) {
			value = value - (value * (self.product_info.group_reduction / 100));
		}
		return value;
	};

	/**
	 * Get the information about a table option
 	 * @param id_option
	 */
	self.getTableOptionInfo = function(id_option) {
		for (i=0; i< ppat_table_options.length; i++) {
			if (ppat_table_options[i].id_option == id_option) {
				return ppat_table_options[i];
			}
		}
		return false;
	};

	/**
	 * Attempt to match price in preloaded price matrix
 	 * @param id_option
	 * @param col
	 * @param row
	 * @returns {*|boolean}
	 */
	self.getPriceFromMatrix = function(id_option, col, row) {
		col = parseFloat(col).toFixed(2);
		row = parseFloat(row).toFixed(2);

		if (typeof(ppat_price_matrix[id_option]) !== 'undefined' &&
			typeof(ppat_price_matrix[id_option][row]) !== 'undefined' &&
			typeof(ppat_price_matrix[id_option][row][col]) !== 'undefined') {
            price = ppat_price_matrix[id_option][row][col];
        } else {
            price = false;
        }
		return price;
	};

	/**
	 * Query an array for closest index based on rounding mode
 	 * @param array
	 * @param search_value
	 * @param price_rounding_mode
	 * @returns {number}
	 */
	self.queryArray = function(array, search_value, price_rounding_mode) {
		var index_found = -1;
		if (price_rounding_mode == 'up') {
			for (i = 0; i < array.length; i++) {
				index = parseFloat(array[i]);
				if (index >= search_value) {
					index_found = index;
					break;
				}
			}
			if (index_found == -1) {
				index_found = array[array.length - 1];
			}
		}

		if (price_rounding_mode == 'down') {
			array.reverse();
			for (i = 0; i < array.length; i++) {
				index = parseFloat(array[i]);
				if (search_value >= index) {
					index_found = index;
					break;
				}
			}
			if (index_found == -1) {
				index_found = array[array.length - 1];
			}
		}
		return index_found;
	};

	/**#
	 * Get closest matching price from the price table based on price rounding mode
 	 * @param id_option
	 * @param row
	 * @param col
	 * @param price_rounding_mode
	 * @returns {*}
	 */
	self.getClosestPrice = function(id_option, row, col, price_rounding_mode) {
		var col = parseFloat(col).toFixed(2);
		var row = parseFloat(row).toFixed(2);
		var rows = [];
		var row_index = -1;
		var cols = [];
		var col_index = -1;

		if (typeof ppat_price_matrix[id_option] === 'undefined') {
			return 0;
		}

		$.each(ppat_price_matrix[id_option], function (index, price) {
			index = parseFloat(index);
			rows.push(index);
		});

		price_rounding_mode = 'up';

		row_index = self.queryArray(rows, row, price_rounding_mode);

		if (row_index == -1) {
			return 0;
		}
		row_index = row_index.toFixed(2);

		$.each(ppat_price_matrix[id_option][row_index], function (index, price) {
			index = parseFloat(index);
			cols.push(index);
		});

		col_index = self.queryArray(cols, col, price_rounding_mode);
		col_index = col_index.toFixed(2);

		if (col_index > -1 && row_index > -1) {
			return parseFloat(ppat_price_matrix[id_option][row_index][col_index]);
		} else {
			return 0;
		}

	};

	/**
	 * Get the price based on product, option and dimensions
 	 */
    self.getPrice = function()
    {
        var id_option = self.$wrapper.find("select[name='ppat_id_option']").val();
        var col = self.$wrapper.find('.ppat-unit-entry.col').val();
        var row = self.$wrapper.find('.ppat-unit-entry.row').val();
		var price_impact = '';

		if (typeof(col) !== 'string' || typeof(row) !== 'string') {
			return false;
		}

		col = self.removeFormatting(col);
		row = self.removeFormatting(row);
		price = self.getPriceFromMatrix(id_option, col, row);

		if (price !== false) {
			if (price.indexOf('+') == 0) price_impact = '+';
			if (price.indexOf('-') == 0) price_impact = '-';

			price = price.replace('+', '');
			price = price.replace('-', '');

			if (price_impact == '+') {
				price = parseFloat(price) + parseFloat(self.product_info.base_price_exc_tax);
			}

			if (price_impact == '-') {
				price = parseFloat(price) - parseFloat(self.product_info.base_price_exc_tax);
			}
			price = self.toNearestPenny(parseFloat(price));
			return price;
		}

        /* for text based inputs round up to nearest int before fetching price */
        if (self.$wrapper.find('.ppat-unit-entry.col').prop('tagName') == "INPUT")
            col = self.customRound(col, 1);
        if (self.$wrapper.find('.ppat-unit-entry.row').prop('tagName') == "INPUT")
            row = self.customRound(row, 1);

        ppat_table_option = self.getTableOptionInfo(id_option);
        if (ppat_table_option != false) {
			return self.removeFormatting(self.getClosestPrice(id_option, row, col, ppat_table_option.lookup_rounding_mode));
		}
    };

	/**
	 * Format the price to relevant formatting
 	 * @param price
	 * @param callback
	 * @returns {*}
	 */
	self.formatPrice = function(price, callback) {
        return $.ajax({
            type: 'POST',
            url: ppat_module_ajax_url,
            async: true,
            cache: false,
            data: {
                section: 'front_ajax',
                route: self.route,
                action: 'formatprice',
                price: price
            },
            //dataType: 'json',
            success: function (resp) {
                callback(resp);
            }
        });
    };

	/**
	 *
	 * @param price (final price)
	 * @param regular_price (original price without discount)
	 */
	self.displayPrice = function(price, regular_price) {
        self.formatPrice(parseFloat(price), function (formatted_price) {
            $span = $(".current-price span[itemprop='price']");

            if ($span.length == 0) {
                $span = $(".current-price .current-price-value");
            }

            $span.html(formatted_price);
            $span.attr("content", formatted_price);
        });

        if (regular_price > price) {
            self.formatPrice(parseFloat(regular_price), function (formatted_price) {
                $(".product-discount .regular-price").html(formatted_price);
            });
        }
	};

	/**
	 * Main function to call for recalculating final price and displaying it
 	 */
	self.calculateFinalPrice = function() {
		var final_price = 0.00;
		var regular_price = 0.00;  //price without any discounts

		$.when(self.getPrice()).then(function(final_price) {
			if (final_price === false) {
				return false;
			}

			if (typeof final_price === 'object') {
				final_price = parseFloat(final_price.price);
			}

			final_price = parseFloat(final_price);
			final_price = final_price + parseFloat(self.product_info.attribute_price);

            if (self.pco_event.price_impact > 0) {
                final_price = final_price + self.pco_event.price_impact;
            }

            if (self.product_info.price_display == 0) {
				final_price = final_price * (1 + (self.product_info.rate / 100)); //add tax
			}
			final_price = self.applyExtraDiscounts(final_price);

			// add any specific price discounts
			if (self.product_info.specific_prices != null) {
				if (self.product_info.specific_prices.reduction_type == 'percentage') {
					regular_price = final_price;
					reduction = parseFloat(self.product_info.specific_prices.reduction);
					final_price = final_price - (final_price * reduction);
				}

				if (self.product_info.specific_prices.reduction_type == 'amount') {
					regular_price = final_price;
					reduction = parseFloat(self.product_info.specific_prices.reduction);

					if (self.product_info.specific_prices.reduction_tax == '0')
						reduction = reduction * (1 + (self.product_info.rate / 100)); //add tax to reduction

					final_price = final_price - reduction;
				}
			}

            self.displayPrice(final_price, regular_price);
		});
	};

	/**
	 * Update Product Information such as product price, attribute price tax etc.  This information will be used to calculate dynamic price
	 */
	self.getProductInfo = function () {
		var query = $("#add-to-cart-or-refresh").serialize();
		return $.ajax({
			type: 'POST',
			url: ppat_module_ajax_url + '?' + query,
			async: true,
			cache: false,
			data: {
                section: 'front_ajax',
                route: self.route,
                action: 'getproductinfo',
                id_product: self.getProductID()
            },
			dataType: 'json',
			success: function (resp) {
				self.product_info = resp;
			}
		});
	};


	/**
	 * Add rows and cols to the dropdowns via ajax based on selected option
 	 */
    self.updateUnitValues = function() {
        if (self.$wrapper.find('select.ppat-unit-entry').length > 0) {
            $('select.ppat-unit-entry').find('option').remove();
            $(self.$wrapper.find('select.ppat-unit-entry')).each(function (i, obj) {
                var name = $(obj).attr("name");
                $.ajax({
                    type: 'POST',
                    headers: {"cache-control": "no-cache"},
                    url: ppat_module_ajax_url,
                    cache: false,
                    dataType: "json",
                    data: 'section=front_ajax&route=ppatfrontproductcontroller&action=getUnitEntryValues&name=' + name + '&id_option=' + self.$wrapper.find('select[name="ppat_id_option"]').val(),
                    success: function (jsonData, textStatus, jqXHR) {
                        $.each(jsonData, function (key, value) {
                            $(obj).append('<option value="' + eval('value.' + name) + '">' + eval('value.' + name) + '</option>');
                        });
                        self.setDefaultTextBoxValues();
                        self.calculateFinalPrice();
                    }
                });
            });
        } else {
            self.setDefaultTextBoxValues();
        }
    };

	/**
	 * set the default row / col values for textboxes based on selected option
	 */
	self.setDefaultTextBoxValues = function() {
		if (typeof ppat_table_options !== "undefined" && ppat_table_options.length > 0) {
			if ($("select[name='ppat_id_option']").length > 0) {
				var id_option = $("select[name='ppat_id_option']").val();
			} else {
				var id_option = ppat_table_options[0].id_option;
			}

			for (var i=0; i <= ppat_table_options.length-1; i++) {
				if (ppat_table_options[i].id_option == id_option) {
					default_row = ppat_table_options[i].default_row;
					default_col = ppat_table_options[i].default_col;
					$(".ppat-unit-entry-wrapper [name='col']").val(default_col);
					$(".ppat-unit-entry-wrapper [name='row']").val(default_row);
					self.getPrice();
				}
			}
		}
	};


	/**
	 * Bind widgets controls to any events (which need to be done after widget is rendered via ajax)
 	 */
	self.bindWidgetEvents = function() {
		self.$wrapper = $(self.wrapper);

		$('input.ppat-unit-entry').typeWatch({
			callback: function () {
				self.calculateFinalPrice();
			},
			wait: 500,
			highlight: false,
			captureLength: 0
		});
	};

	/**
	 * Display the widget via ajax
	 */
	self.renderWidget = function() {
        return $.ajax({
            type: 'POST',
            url: ppat_module_ajax_url,
            async: true,
            cache: false,
            data: {
                section: 'front_ajax',
                route: self.route,
                action: 'renderwidget',
                rand: new Date().getTime(),
                id_product: self.getProductID()
            },
            success: function(html) {
            	if ($("div.product-variants").length > 0) {
					$(html).insertBefore("div.product-variants");
				} else {
            		$(".product-actions").first().prepend(html);
				}
                self.bindWidgetEvents();
                self.updateUnitValues();
                self.validate(false);
                self.status = 1;
            }
        });
	};

	/**
	 * Validate dimensions entered by customer
 	 * @param $sender
     * @param show_error
	 * @returns {boolean}
	 */
    self.validateEntry = function($sender, show_error) {
        var error = false;
        var name = $sender.attr('name');
        var min = parseFloat(ppat_product['min_' + name]);
        var max = parseFloat(ppat_product['max_' + name]);
        var $error_div = $sender.parents('.ppat-unit-entry-wrapper').find('.ppat-error');
        var input_val = parseFloat($sender.val());

        if (min > 0 && input_val < min) error = true;
        if (max > 0 && input_val > max) error = true;
        if (isNaN(input_val)) {
            error = true;
        }

        if (error && show_error) {
            $error_div.html(min + ' - ' + max);
            $error_div.fadeIn();
            $sender.addClass('ppat-error-unit');
        } else {
            $error_div.fadeOut();
            $sender.removeClass('ppat-error-unit');
        }
        return !error;
    };

	/**
	 * Validate the customer values entered
 	 */
    self.validate = function(show_error) {
    	var has_error = false;
    	$(".ppat-unit-entry").each(function(i, obj) {
			if (!self.validateEntry($(obj), show_error)) {
				has_error = true;
			}
		});

    	if (has_error) {
			$(".product-add-to-cart button.add-to-cart").prop("disabled", true);
		} else {
			$(".product-add-to-cart button.add-to-cart").prop("disabled", false);
		}
	};

	self.init = function() {
		$.when(self.renderWidget()).then(self.getProductInfo).then(self.calculateFinalPrice);
	};
	self.init();


	/** Events **/

	/**
	 * On Attributes changed
	 */
	prestashop.on('updatedProduct', function (event) {
		self.getProductInfo().then(function () {
			self.calculateFinalPrice();
		});
	});

	$(document).on('keyup', self.wrapper + ' input.ppat-unit-entry', function() {
		self.validate(true);
    });

	$(document).on('change', self.wrapper + ' select.ppat-unit-entry', function() {
		self.validate(true);
		self.calculateFinalPrice();
    });

	/**
	 * On Table option changed
 	 */
	$(document).on('change', self.wrapper + ' select[name="ppat_id_option"]', function() {
		self.setDefaultTextBoxValues();
		self.updateUnitValues();
		self.validate(true);
		self.calculateFinalPrice();
    });

    /**
     * on product custom options price
     */
    prestashop.on('productPriceUpdated', function (event) {
        if (self.status === 0) {
            setTimeout(function () {
                prestashop.emit('productPriceUpdated', event);
            }, 500);
            return false;
        }
        self.pco_event = event;
        self.calculateFinalPrice();
    });
};