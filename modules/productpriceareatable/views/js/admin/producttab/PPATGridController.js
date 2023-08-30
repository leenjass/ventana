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

PPATGridController = function() {
    var self = this;
    self.$grid;
    self.$formAddCol = $('#form-add-col');
    self.selectedColumn = -1;
    self.in_delete = false;
    self.grid_first_load_executed = false;

	/**
	 * Insert a new column into the grid
	 */
    self.insertColumn = function () {
        var $form = $(".form-add-new");
        var row = parseFloat($form.find("input[name='row']").val());
        var col = parseFloat($form.find("input[name='col']").val());
        var price = parseFloat($form.find("input[name='price']").val());
        var col_index = -1;

        var i = 0;
        $.each(data_columns, function (index, value) {
            if (parseFloat(value.title) == col) col_index = i;
            i++;
        });

        if (col_index == -1) {
            var CM = self.$grid.pqGrid("option", "colModel");
            CM[1].colModel.push({title: col, align: 'right', width: 60});
			self.loadGrid();
		}
    };

	/**
	 * Insert a new row into the grid
 	 */
	self.addRow = function () {
		var new_row = [];
		var DM = self.$grid.pqGrid("option", "dataModel");

		if (DM.data == null || DM.data.length == 0) {
			data_grid = [];
			for (i = 0; i <= data_columns.length; i++) {
				new_row[i] = '0.00';
			}
			data_grid.push(new_row);
		}
		else {
			var last_row = DM.data[DM.data.length - 1];
			for (i = 0; i <= last_row.length - 1; i++) {
				new_row[i] = '0.00';
			}
			DM.data.push(new_row);
		}
		self.$grid.pqGrid("refreshDataAndView");
		self.loadGrid();
	};

	/**
	 * delete column from the grid
 	 * @returns {boolean}
	 */
    self.deleteCol = function() {
        if (self.selectedColumn < 1) return false;
        if (!confirm('Are you sure you want to delete the entire column and associated data?')) return false;

        data_columns.splice(self.selectedColumn-1, 1);

        var i = 0;
        $.each(data_columns, function( index, value ){
            data_columns[i].dataIndx = i+1;
            i++;
        });

        $.each(data_grid, function( index, value ){
            value.splice(self.selectedColumn, 1);
        });
        self.$grid.pqGrid( "refreshDataAndView" );
        self.loadGrid();
    };

	/**
	 * Delete row
	 * @returns {boolean}
	 */
    self.deleteRow = function() {
        self.in_delete = true;
        var rowIndx = self.getRowIndx();
        var DM = self.$grid.pqGrid("option", "dataModel");
        DM.data.splice(rowIndx, 1);


        self.$grid.pqGrid("refreshDataAndView");
        self.$grid.pqGrid("setSelection", { rowIndx: rowIndx});
        return false;
    };

	/**
	 * Get active grid row index
 	 * @returns {*}
	 */
    self.getRowIndx = function() {
        var arr = self.$grid.pqGrid("selection", { type: 'row', method: 'getSelection' });
        if (arr && arr.length > 0) {
            var rowIndx = arr[0].rowIndx;
            return rowIndx;
        }
        else {
            alert("Select a row.");
            return null;
        }
    };


	/**
	 * Display import CSV Form as a jquery dialog popup
 	 */
	self.importPopupShow = function() {
        $("#ppat-import").dialog({ title: "Import CSV", buttons: {
            Add: function () {
                var url = MPTools.joinUrl(module_ajax_url_ppat, 'section=adminproducttab&route=ppatadminproducttabpricescontroller&action=processimportcsv');
				var form_data = new FormData();

				form_data.append('file', $('#ppat-csv')[0].files[0]);
				form_data.append('id_product', id_product);
				form_data.append('id_option', id_option);

				$.ajax({
					   url : url,
					   type : 'POST',
					   data : form_data,
					   processData: false,
					   contentType: false,
					   success : function(data) {
						   $("#ppat-import").dialog("destroy").remove();
						   ppat_admin_producttab_prices_controller.onOptionSelect(id_option);  //refresh
					   }
				});
            },
            Cancel: function () {
				$("#ppat-import").dialog("destroy").remove();
            }
        }
        });
        //self.$formAddCol.dialog("open");
	};


	/**
     * Show the add column jquery UI Dialog
      */
    self.addColumnFormShow = function() {
        self.$formAddCol.dialog({ title: "Insert Column",
			buttons: {
				Add: function () {
					self.insertColumn();
					self.$formAddCol.dialog("destroy").remove();
				},
				Cancel: function () {
					self.$formAddCol.dialog("destroy").remove();
				}
        	},
			close: function (event, ui) {
				self.$formAddCol.dialog("destroy").remove();
			}
        });
    };

	/**
	 * Create the toolbar for the grid
	 */
	self.createToolbar = function () {
		$("#grid_array").on("pqgridrender", function (evt, obj) {
			var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-crud'></div>").appendTo($(".pq-grid-top", this));

			$("<span class='button btn-new-column'><i class='material-icons'></i>New Column</span>").appendTo($toolbar).button({icons: {primary: "material-icons"}}).click(function (evt) {
				self.addColumnFormShow();
			});

			$("<span class='button btn-new-row'><i class='material-icons'></i>Insert Row</span>").appendTo($toolbar).button({icons: {primary: "ui-icon-circle-plus"}}).click(function (evt) {
				self.addRow();
			});

			$("<span class='button btn-delete-row'><i class='material-icons'></i>Delete Row</span>").appendTo($toolbar).button({icons: {primary: "ui-icon-circle-plus"}}).click(function (evt) {
				self.deleteRow();
			});

			$("<span class='button btn-delete-col' id='delete-col'><i class='material-icons'></i>Delete Selected Column</span>").appendTo($toolbar).button({icons: {primary: "ui-icon-circle-minus"}}).click(function () {
				self.deleteCol();
			});

			$("<span class='button btn-import-csv' id='import'><i class='material-icons'></i>Import CSV</span>").appendTo($toolbar).button({icons: {primary: "ui-icon-circle-plus"}}).click(function () {
				self.importPopupShow();
			});

			$toolbar.disableSelection();
		});
	};

	/**
	 * Load grid and data
 	 */
	self.loadGrid = function() {

        self.createToolbar();

        var obj = {};
        obj.width = '100%';
        obj.height = 400;
        obj.colModel = [
            { title: row_title, width: 140 },
            { title: col_title, align: "center", colModel:
                data_columns
            }
        ];
        obj.dataModel = {
            data:data_grid
        };

        obj.quitEditMode = function (event, ui) {
            if (!self.in_delete)
                self.$grid.pqGrid("saveEditCell");
            self.in_delete = false;
        };

       	self.$grid = $("#grid_array").pqGrid(obj);

		var colM = $("#grid_array").pqGrid( "option" , "colModel" );
        var dataType = colM[0].editable;
        //self.loadForms();
    };

	/**
	 * update the grid data
 	 */
	self.processSaveGrid = function(data, columns) {
		MPTools.waitStart();
        var url = MPTools.joinUrl(module_ajax_url_ppat, 'section=adminproducttab&route=ppatadminproducttabpricescontroller&action=processpricetable');

		id_option = $("#ppat-grid-wrapper").find("input#id_option").val();

		var post_data = {
			'id_option': id_option,
			'grid_json_columns' : columns,
			'grid_json_data' : data
		};
		$.ajax({
			type: 'POST',
			url: url,
			async: true,
			cache: false,
			data: post_data,
			success: function (result) {
				MPTools.waitEnd();
			}
		});
	};

	self.importCSV = function() {
	};

	self.init = function() {
        self.loadGrid();
	};
	self.init();

    /*$(document).on('mousemove', 'body', function() {
        if (!self.grid_first_load_executed) {
            self.loadGrid();
            $("#delete-col").hide();
        }
        self.grid_first_load_executed = true;
    });*/

	/**
	 * On grid save button cluick
 	 */

    $(document).off('click', '#ppat-btn-grid-save').on('click', '#ppat-btn-grid-save', function() {
        var data = self.$grid.pqGrid( "option" , "dataModel.data");
        var columns = self.$grid.pqGrid( "option" , "colModel");

        data = JSON.stringify(data);
        columns = JSON.stringify(columns);

        $("input[name='grid_json_data']").val(data);
        $("input[name='grid_json_columns']").val(columns);
		self.processSaveGrid(data, columns);
        return false;
    });

	/**
	 * On a column header click
 	 */
    $(document).off('click', '.pq-grid-col').on('click', '.pq-grid-col', function() {
        self.selectedColumn = $(this).attr('pq-grid-col-indx');
        if (self.selectedColumn > 0) $("#delete-col").show();
    });


};