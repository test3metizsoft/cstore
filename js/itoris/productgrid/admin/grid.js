if (typeof Itoris == 'undefined') {
	Itoris = {};
}

Itoris.ProductGrid = Class.create({
	classPrefix : 'itoris_productgrid',
	dragElm : null,
	dragElmNum : 0,
	dragStarted : false,
	dragBlock : null,
	dragPositions : [],
	dragPosition : 0,
	defaultConfig : [],
	dragColNumStart : false,
	dragColNumEnd : false,
	currentInlineEditCeil : null,
	currentGalleryProductId : null,
	isAjaxRequest : false,
	inlineEditCeils : [],
	gridSortByCode : null,
	gridFilters : [],
	initialize : function(config, defaultConfig) {
		this.config = config;
		this.defaultConfig = defaultConfig;
		$$('body')[0].appendChild($(this.getConfigWindow()));
		this.addEvents();
		this.origGridInitCallback = function(num){return num;}(productGridJsObject.preInitCallback);
		productGridJsObject.preInitCallback = this.initGrid.bind(this);
		this.fixOptionsNbsp();
	},
	addEvents : function() {
		Event.observe($$('.' + this.classPrefix + '_close')[0], 'click', this.closeConfigWindow.bind(this));
		Event.observe($$('.' + this.classPrefix + '_gallery_close')[0], 'click', this.closeImageEditForm.bind(this));
		Event.observe($$('.' + this.classPrefix + '_gallery_buttons .close')[0], 'click', this.closeImageEditForm.bind(this));
		Event.observe($$('.' + this.classPrefix + '_gallery_buttons .save')[0], 'click', this.saveImageEditForm.bind(this));
		Event.observe($$('.' + this.classPrefix + '_config_buttons .close')[0], 'click', this.closeConfigWindow.bind(this));
		Event.observe($$('.' + this.classPrefix + '_config_buttons .reset')[0], 'click', this.resetToDefault.bind(this));
		Event.observe($$('#mass_apply_changes .discard-changes')[0], 'click', this.hideMassInlineEditForms.bind(this));
		Event.observe($$('#mass_apply_changes .apply-changes')[0], 'click', this.massSaveInlineEdit.bind(this));
		Event.observe(document, 'click', this.clickDocument.bind(this));
		if (this.config.allow_reorder) {
			Event.observe($$('body')[0], 'mousemove', this.dragAndDrop.bind(this));
			Event.observe(document, 'mouseup', this.afterDragAndDrop.bind(this));
		}
		this.initGrid();
	},
	initGrid : function() {
		if (this.origGridInitCallback instanceof Function) {
			this.origGridInitCallback();
		}
		this.hideAndDisableAllBulkAttributesValues();
		if (this.config.allow_reorder) {
			var tableCols = $$('#productGrid_table thead .headings th');
			this.dragColNumStart = false;
			this.dragColNumEnd = false;
			for (var i = 0; i < tableCols.length; i++) {
				if (tableCols[i].select('.itoris-attribute-code').length) {
					if (this.dragColNumStart === false) {
						this.dragColNumStart = function(num){return num;}(i);
					}
					tableCols[i].addClassName('itoris-draggable-ceil-header');
					if (tableCols[i].select('a').length) {
						tableCols[i].title = this.config.message_drag_click;
						tableCols[i].select('a')[0].origTitle = tableCols[i].select('a')[0].title;
						tableCols[i].select('a')[0].title = this.config.message_drag_click;
						Event.observe(tableCols[i].select('a')[0], 'click', this.changeLinkTitle.bind(this, tableCols[i].select('a')[0]));
						tableCols[i].select('.itoris-ceil-title')[0].title = this.config.message_click_sort;
					} else {
						tableCols[i].title = this.config.message_drag;
					}
					Event.observe(tableCols[i], 'mousedown', this.beforeDragAndDrop.bind(this, tableCols[i]));
					this.dragColNumEnd = function(num){return num;}(i);
				}
			}
		}
		var ceils = $$('#productGrid_table tbody td');
		if (this.config.allow_inline_edit) {
			var headers = $$('#productGrid_table thead .headings th');
			for (var i = 0; i < ceils.length; i++) {
				if (ceils[i].select('.itoris-inline-edit-ceil').length) {
					if (ceils[i].select('.not-editable').length) {
						ceils[i].addClassName('not-editable-ceil');
						Event.observe(ceils[i], 'click', function(e){e.stop();});
					} else {
						Event.observe(ceils[i], 'click', this.showInlineEditForm.bind(this, ceils[i]));
						ceils[i].addClassName('itoris-inline-edit-ceil-allowed');
						ceils[i].title = headers[i - parseInt(i / headers.length) * headers.length].select('.itoris-inline-edit-ceil-title')[0].innerHTML;
						Event.observe(ceils[i].select('.itoris-save-icon')[0], 'click', this.saveInlineEditForm.bind(this, ceils[i], false));
						var valueElm = ceils[i].select('.itoris-inline-edit-ceil-value')[0];
						if (this.isMassInlineEdit()) {
							Event.observe(valueElm, 'keyup', function(e){
								this.toogleMassInlineEditButtons();
								var keyCode = e.charCode || e.keyCode;
								if (keyCode == 13) {
									this.openNextCeil(null);
								}
							}.bind(this));
							Event.observe(valueElm, 'change', this.toogleMassInlineEditButtons.bind(this));
							ceils[i].select('.itoris-icons')[0].hide();
						} else {
							if ((valueElm.tagName == 'INPUT' && !valueElm.hasClassName('ceil-date'))
								|| (valueElm.tagName == 'SELECT' && !valueElm.multiple)
							) {
								ceils[i].select('.itoris-icons')[0].hide();
								Event.observe(valueElm, 'blur', this.blurInlineEditForm.bind(this, ceils[i]));
								Event.observe(valueElm, 'change', this.blurInlineEditForm.bind(this, ceils[i]));
								Event.observe(valueElm, 'keypress', this.keypressInlineEditFrom.bind(this, ceils[i]));
							}
						}
						Event.observe(valueElm, 'keyup', function(e){
							var keyCode = e.charCode || e.keyCode;
							if (keyCode == 27) {
								this.hideInlineEditForm(this.currentInlineEditCeil);
							}
						}.bind(this));
						ceils[i].select('textarea').each(function(elm){elm.rows = 7;elm.style.width = '100%';});
						ceils[i].select('select[multiple]').each(function(elm){elm.size = 7;});
						Event.observe(ceils[i].select('.itoris-cancel-icon')[0], 'click', this.hideInlineEditForm.bind(this, ceils[i]));
						if (ceils[i].select('.use-default-scope-link')[0]) {
							Event.observe(ceils[i].select('.use-default-scope-link')[0], 'click', this.saveInlineEditForm.bind(this, ceils[i], true));
						}
					}
				} else {
					if (!ceils[i].select('a,input[type=checkbox]').length) {
						Event.observe(ceils[i], 'click', function(e){e.stop();});
					}
				}
			}
		} else {
			var allCodes = $$('.itoris-attribute-code');
			var hasActionColumn  = false;
			for (var j = 0; j < allCodes.length; j++) {
				if (allCodes[j].innerHTML == 'action_column_order') {
					hasActionColumn = true;
					break;
				}
			}
			if (hasActionColumn) {
				for (var i = 0; i < ceils.length; i++) {
						ceils[i].addClassName('no-pointer');
						if (!ceils[i].select('input[type=checkbox], a').length) {
							Event.observe(ceils[i], 'click', function(e){e.stop();});
						}

				}
			}
		}
		if ($$('#productGrid_massaction-form button')[0]) {
			Event.observe($$('#productGrid_massaction-form button')[0], 'click', this.validateBulkAttribute.bind(this));
		}
		if ($('productGrid_massaction-select')) {
			Event.observe($('productGrid_massaction-select'), 'click', this.changeMassactionDesign.bind(this));
		}
		this.fixOptionsNbsp();

		$('reload_grid_notice').hide();
		var sortElement = $$('.sort-arrow-desc .itoris-attribute-code, .sort-arrow-asc .itoris-attribute-code')[0];
		if (sortElement) {
			this.gridSortByCode = sortElement.innerHTML;
		}
		this.gridFilters = [];
		var filterElements = $$('#productGrid_table thead tr.filter input, #productGrid_table thead tr.filter select');
		for (var i = 0; i < filterElements.length; i++) {
			if (filterElements[i].name && filterElements[i].getValue()) {
				this.gridFilters.push(filterElements[i].name.replace(/\[.*\]/, ''));
			}
		}
	},
	openNextCeil : function(ceil) {
		ceil = ceil || this.currentInlineEditCeil;
		if (ceil) {
			var currentRow = ceil.up('tr');
			var cols = currentRow.select('td');
			var ceilNum = 0;
			for (var i = 0; i < cols.length; i++) {
				if (cols[i] == ceil) {
					ceilNum = i;
					break;
				}
			}
			if (currentRow.next()) {
				var nextCeil = currentRow.next().select('td')[ceilNum];
				if (nextCeil) {
					if (nextCeil.hasClassName('itoris-inline-edit-ceil-allowed')) {
						this.showInlineEditForm(nextCeil, null);
					} else {
						this.openNextCeil(nextCeil);
					}
				}
			}
		}
	},
	isMassInlineEdit : function() {
		return 	this.config.is_mass_inline_edit;
	},
	toogleMassInlineEditButtons : function() {
		if (this.isMassInlineEdit()) {
			var isChanged = false;
			for (var i = 0; i < this.inlineEditCeils.length; i++) {
				if (this.isFieldChanged(this.inlineEditCeils[i])) {
					this.inlineEditCeils[i].select('.itoris-icons')[0].show();
					isChanged = true;
				} else {
					this.inlineEditCeils[i].select('.itoris-icons')[0].hide();
				}
			}
			if (isChanged) {
				this.showMassInlineEditButtons();
			} else {
				this.hideMassInlineEditButtons();
			}
		}
	},
	showMassInlineEditButtons : function() {
		$('mass_apply_changes').show();
	},
	hideMassInlineEditButtons : function() {
		$('mass_apply_changes').hide();
	},
	changeMassactionDesign : function() {
		var elms = $$('#productGrid_massaction-form-additional span.field-row');
		elms.push($$('#productGrid_massaction-form .field-row button')[0].up());
		elms.push($('productGrid_massaction-select').up());
		if ($('productGrid_massaction-form-additional')) {
			elms.push($('productGrid_massaction-form-additional'));
		}
		if ($('productGrid_massaction-select').value == 'edit_attribute') {
			elms.each(function(elm){elm.addClassName('clear-both');});
		} else {
			elms.each(function(elm){elm.removeClassName('clear-both');});
		}
	},
	changeLinkTitle : function(link) {
		link.title = link.origTitle;
		return this;
	},
	showInlineEditForm : function(ceil, e) {
		if (e) {
			e.stop();
		}
		if (this.currentInlineEditCeil == ceil) {
			return;
		}
		if (this.isMassInlineEdit() && this.inlineEditCeils.indexOf(ceil) != -1) {
			return;
		}
		if (ceil.select('.itoris-media-image').length) {
			this.showImageEditForm(ceil);
			return;
		}
		if (ceil.select('.itoris-ceil-loading')[0] && ceil.select('.itoris-ceil-loading')[0].visible()) {
			return;
		}
		ceil.select('.itoris-inline-edit-ceil')[0].show();
		ceil.select('.itoris-inline-edit-value')[0].hide();
		var valueElm = ceil.select('.itoris-inline-edit-ceil-value')[0];
		if (typeof valueElm.editDefaultValue == 'undefined') {
			valueElm.editDefaultValue = valueElm.getValue();
		} else if (valueElm.tagName == 'INPUT') {
			valueElm.value = valueElm.editDefaultValue;
		}
		valueElm.focus();
		if (valueElm.tagName == 'INPUT') {
			valueElm.select();
		}

		if (this.currentInlineEditCeil && this.currentInlineEditCeil != ceil) {
			if (this.isMassInlineEdit()) {
				if (!this.isFieldChanged(this.currentInlineEditCeil)) {
					this.inlineEditCeils = this.inlineEditCeils.without(this.currentInlineEditCeil);
					this.hideInlineEditForm(this.currentInlineEditCeil);
				}
			} else {
				this.hideInlineEditForm(this.currentInlineEditCeil);
			}
		}

		this.currentInlineEditCeil = ceil;
		if (this.isMassInlineEdit()) {
			this.inlineEditCeils.push(ceil);
		}
	},
	clickDocument: function(e) {
		if (this.currentInlineEditCeil) {
			if (!e) {
				e = window.event;
			}
			var targetElement = e.target || e.srcElement;
			if (!(targetElement == this.currentInlineEditCeil || (targetElement.up('.itoris-inline-edit-ceil') == this.currentInlineEditCeil))) {
				if (this.isMassInlineEdit() && this.isFieldChanged(this.currentInlineEditCeil)) {
					return;
				}
				this.hideInlineEditForm(this.currentInlineEditCeil);
			}
		}
	},
	hideInlineEditForm : function(ceil, e) {
		if (e) {
			e.stop();
		}
		try {
			ceil.select('.itoris-inline-edit-ceil')[0].hide();
			ceil.select('.itoris-inline-edit-value')[0].show();
			var valueElm = ceil.select('.itoris-inline-edit-ceil-value')[0];
			if (valueElm.multiple) {
				var options = valueElm.select('option');
				for (var i = 0; i < options.length; i++) {
					options[i].selected = valueElm.editDefaultValue.indexOf(options[i].value) != -1;
				}
			} else {
				valueElm.value = valueElm.editDefaultValue;
			}
			if (this.currentInlineEditCeil) {
				this.currentInlineEditCeil = null;
			}
			if (this.isMassInlineEdit()) {
				this.inlineEditCeils = this.inlineEditCeils.without(ceil);
				this.toogleMassInlineEditButtons();
			}
		} catch(e) {/* prevent ie erorrs after saving */}
	},
	hideMassInlineEditForms : function() {
		this.inlineEditCeils.each(function(ceil){
			this.hideInlineEditForm(ceil);
		}.bind(this));
	},
	isFieldChanged: function(ceil) {
		var valueElm = ceil.select('.itoris-inline-edit-ceil-value')[0];
		var elmValue = valueElm.getValue();
		if (elmValue instanceof Array) {
			for (var i = 0; i < elmValue.length; i++) {
				if (valueElm.editDefaultValue.indexOf(elmValue[i]) == -1) {
					return true;
				}
			}
			return elmValue.length != valueElm.editDefaultValue.length;
		} else {
			return valueElm.getValue() != valueElm.editDefaultValue;
		}
	},
	keypressInlineEditFrom : function(ceil, e) {
		var keyCode = e.charCode || e.keyCode;
		if (keyCode == 13) {
			this.saveInlineEditForm(ceil, false);
			this.openNextCeil(ceil);
		} else if (keyCode == 27) {
			this.hideInlineEditForm(ceil);
		}
	},
	blurInlineEditForm : function(ceil) {
		if (ceil.select('.itoris-inline-edit-ceil')[0].visible() && this.isFieldChanged(ceil)) {
			this.saveInlineEditForm(ceil, false);
		}
	},
	saveInlineEditForm : function(ceil, useDefault) {
		if (this.isAjaxRequest) {
			return;
		}
		if (useDefault || Validation.validate(ceil.select('.itoris-inline-edit-ceil-value')[0])) {
			if (!productGridJsObject.reloadParams) {
				var params = {form_key: FORM_KEY};
			} else {
				var params = Object.clone(productGridJsObject.reloadParams);
			}
			params.product_id = ceil.select('.itoris-product-id')[0].innerHTML;
			params.attr_code = ceil.select('.itoris-inline-edit-ceil-value')[0].name;
			if (useDefault) {
				params['use_default'] = 1;
			} else {
				var oldValue = ceil.select('.itoris-inline-edit-value')[0].innerHTML;
				var value = ceil.select('.itoris-inline-edit-ceil-value')[0].getValue();
				var displayValue = value;
				if (ceil.select('.itoris-inline-edit-ceil-value')[0].tagName == 'SELECT') {
					var valueLabels = [];
					var options = ceil.select('.itoris-inline-edit-ceil-value')[0].select('option');
					for (var i = 0; i < options.length; i++) {
						if (options[i].selected) {
							valueLabels.push(options[i].innerHTML.replace(/&nbsp;/g, ''));
						}
					}
					displayValue = valueLabels.join(', ');
				}
				if (value instanceof Array) {
					params['attr_value[]'] = value;
				} else {
					if (value == ceil.select('.itoris-inline-edit-ceil-value')[0].editDefaultValue) {
						this.hideInlineEditForm(ceil);
						return;
					}
					params['attr_value'] = value;
				}
			}
			var obj = this;
			var url = this.config.save_inline_edit_url;
			this.isAjaxRequest = true;
			this.showCeilLoader(ceil);
			if (!useDefault) {
				ceil.select('.itoris-inline-edit-ceil-value')[0].editDefaultValue = value;
				ceil.select('.itoris-inline-edit-value')[0].update(displayValue);
			}
			this.hideInlineEditForm(ceil);
			new Ajax.Request(url + (url.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true' ), {
				method: 'post',
				parameters: params,
				evalScripts: true,
				loaderArea:false,
				onFailure : function() {
					obj.isAjaxRequest = false;
				},
				onComplete: function(transport) {
					obj.isAjaxRequest = false;
					obj.hideCeilLoader(ceil);
					var resObj = transport.responseText.evalJSON();
					if (resObj.ok) {
						if (useDefault) {
							ceil.select('.use-default-scope-link')[0].remove();
							obj.updateCeilValue(ceil, resObj.default_value);
						}
						obj.showReloadGridNotice(params.attr_code);
					} else if (resObj.error) {
						ceil.select('.itoris-inline-edit-value')[0].update(oldValue);
						alert(resObj.error);
						ceil.click();
					}
					//obj.updateGridByResponse(transport);
				}
			});
		}
	},
	updateCeilValue: function(ceil, value) {
		var valueElm = ceil.select('.itoris-inline-edit-ceil-value')[0];
		var displayValue = value;
		if (valueElm.tagName == 'SELECT') {
			var valueLabels = [];
			var options =valueElm.select('option');
			for (var i = 0; i < options.length; i++) {
				if (options[i].value == value) {
					valueLabels.push(options[i].innerHTML.replace(/&nbsp;/g, ''));
					options[i].selected = false;
				} else if (options[i].selected) {
					options[i].selected = false;
				}
			}
			displayValue = valueLabels.join(', ');
		} else {
			valueElm.value = value;
		}
		valueElm.editDefaultValue = value;
		ceil.select('.itoris-inline-edit-value')[0].update(displayValue);
	},
	showReloadGridNotice: function(code) {
		if (!this.isMassInlineEdit()) {
			if (this.gridSortByCode == code || this.gridFilters.indexOf(code) != -1) {
				$('reload_grid_notice').show();
			}
		}
	},
	showCeilLoader : function(ceil) {
		ceil.select('.itoris-ceil-loading')[0].show();
	},
	hideCeilLoader : function(ceil) {
		ceil.select('.itoris-ceil-loading')[0].hide();
	},
	massSaveInlineEdit : function() {
		if (this.isAjaxRequest) {
			return;
		}
		if (!productGridJsObject.reloadParams) {
			var params = {form_key: FORM_KEY};
		} else {
			var params = Object.clone(productGridJsObject.reloadParams);
		}
		var canSave = false;
		var validationError = false;
		for (var i = 0; i < this.inlineEditCeils.length; i++) {
			var ceil = this.inlineEditCeils[i];
			if (this.isFieldChanged(ceil)) {
				if (Validation.validate(ceil.select('.itoris-inline-edit-ceil-value')[0])) {
					if (!validationError) {
						var productId = ceil.select('.itoris-product-id')[0].innerHTML;
						var attrCode = ceil.select('.itoris-inline-edit-ceil-value')[0].name;
						var paramName = 'products[' + productId + '][' + attrCode + ']';
						var value = ceil.select('.itoris-inline-edit-ceil-value')[0].getValue();
						if (value instanceof Array) {
							params[paramName + '[]'] = value;
						} else {
							params[paramName] = value;
						}
						canSave = true;
					}
				} else {
					validationError = true;
				}
			}
		}
		if (canSave && !validationError) {
			this.inlineEditCeils = [];
			this.toogleMassInlineEditButtons();
			var url = this.config.mass_save_inline_edit_url;
			this.isAjaxRequest = true;
			new Ajax.Request(url + (url.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true' ), {
				method: 'post',
				parameters: params,
				evalScripts: true,
				onFailure : function() {
					this.isAjaxRequest = false;
				}.bind(this),
				onComplete: function(transport) {
					this.isAjaxRequest = false;
					this.updateGridByResponse(transport);
				}.bind(this)
			});
		}
	},
	showImageEditForm : function(ceil) {
		var params = {
			product_id: ceil.select('.itoris-product-id')[0].innerHTML
		};
		var obj = this;
		new Ajax.Request(this.config.get_gallery_config_url, {
			method: 'post',
			parameters: params,
			onComplete: function(transport) {
				var res = transport.responseText.evalJSON();
				if (res.error) {
					alert(res.error);
				} else {
					obj.showImageEditFormAfter(ceil, res);
				}
			}
		});
	},
	showImageEditFormAfter : function(ceil, config) {
		this.currentGalleryProductId = ceil.select('.itoris-product-id')[0].innerHTML;
		var uploader = this.getGalleryObj().uploader;
		if (uploader.files) {
			var fileIds = [];
			for (var i = 0; i < uploader.files.length; i++) {
				fileIds.push(uploader.files[i].id);
			}
			for (var i = 0; i < fileIds.length; i++) {
				uploader.removeFile(fileIds[i]);
			}
		}
		this.getGalleryObj().images = [];
		var imagesRows = $$('#itoris_productgrid_gallery_list tr');
		for (var i = 2; i < imagesRows.length; i++) {
			imagesRows[i].remove();
		}
		this.getGalleryObj().setProductImages('no_selection');
		this.getGalleryObj().images = config.images.evalJSON();
		this.getGalleryObj().imagesValues = config.images_values.evalJSON();
		this.getGalleryObj().getElement('save_image').value = config.images_values;
		this.getGalleryObj().updateImages();
		this.openPopupWindow(this.getImageEditForm());
	},
	saveImageEditForm : function() {
		if (!productGridJsObject.reloadParams) {
			var params = {form_key: FORM_KEY};
		} else {
			var params = Object.clone(productGridJsObject.reloadParams);
		}
		params.product_id = this.currentGalleryProductId;
		params.images = this.getGalleryObj().getElement('save').value;
		params.images_values = this.getGalleryObj().getElement('save_image').value;
		params.ajax = true;
		var obj = this;
		var url = this.config.save_gallery_config_url;
		new Ajax.Request(url + (url.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true' ), {
			method: 'post',
			parameters: params,
			evalScripts: true,
			onComplete: function(transport) {
				obj.updateGridByResponse(transport);
			}
		});
	},
	updateGridByResponse : function(transport) {
		try {
			var responseText = transport.responseText.replace(/>\s+</g, '><');

			if (transport.responseText.isJSON()) {
				var response = transport.responseText.evalJSON()
				if (response.error) {
					alert(response.error);
				}
				if(response.ajaxExpired && response.ajaxRedirect) {
					setLocation(response.ajaxRedirect);
				}
			} else {
				var divId = $(productGridJsObject.containerId);
				if (divId.id == productGridJsObject.containerId) {
					divId.update(responseText);
				} else {
					$$('div[id="'+productGridJsObject.containerId+'"]')[0].update(responseText);
				}
			}
		} catch (e) {
			var divId = $(productGridJsObject.containerId);
			if (divId.id == productGridJsObject.containerId) {
				divId.update(responseText);
			} else {
				$$('div[id="'+productGridJsObject.containerId+'"]')[0].update(responseText);
			}
		}
		productGridJsObject.initGridAjax();
		this.fixOptionsNbsp();
		this.closeImageEditForm();
	},
	closeImageEditForm : function() {
		this.closePopupWindow(this.getImageEditForm());
	},
	getImageEditForm : function() {
		return $(this.classPrefix + '_gallery');
	},
	getGalleryObj : function() {
		return itoris_productgrid_galleryJsObject;
	},
	resetToDefault : function() {
		this.getConfigWindow().select('input[type=checkbox]').each(function(elm){
			elm.checked = false;
		});
		for (var i = 0; i < this.defaultConfig.length; i++) {
			var elm = $(this.defaultConfig[i].id);
			if (!elm) continue;
			if (elm.tagName == 'SELECT') {
				var selectedValue = this.defaultConfig[i].value;
				if (isNaN(selectedValue)) {
					selectedValue = selectedValue.toLowerCase();
				}
				var optionExists = false;
				elm.select('option').each(function(opt){
					if 	(opt.value == selectedValue) {
						optionExists = true;
					}
				});
				if (optionExists) {
					elm.value = selectedValue;
				}
			} else {
				elm.checked = parseInt(this.defaultConfig[i].value);
				elm.disabled = false;
			}
		}
	},
	beforeDragAndDrop : function(elm,e) {
		document.onselectstart = function() {return false;} // ie
		document.onmousedown = function() {return false;}
		this.dragElm = elm;
		var tableCols = $$('#productGrid_table thead .headings th');
		var num = 0;
		for (var i = this.dragColNumStart; i <= this.dragColNumEnd; i++) {
			if (elm == tableCols[i]) {
				num = i;
				break;
			}
		}
		this.dragElmNum = num;
		this.dragStarted = false;
		this.calculateColsPositions();
	},
	calculateColsPositions : function() {
		var ths = $$('#productGrid_table thead .headings th');
		this.dragPositions = [];
		for (var i = this.dragColNumStart; i <= this.dragColNumEnd; i++) {
			this.dragPositions.push({
				left: ths[i].viewportOffset().left,
				width: ths[i].getWidth()
			});
		}
	},
	dragAndDrop : function(ev) {
		if (this.dragElm) {
			if (!this.dragStarted) {
				var dragBlock = this.createDragTable();
				this.dragStarted = true;
				this.dragBlock = dragBlock;
			}
			this.dragBlock.setStyle({
				top: (ev.pageY - 20 - document.viewport.getScrollOffsets()[1]) + 'px',
				left: (ev.pageX - 20) + 'px'
			});
			this.calculateDragPosition(ev.pageX);
		}
	},
	calculateDragPosition : function(currentX) {
		for (var i = 0; i < this.dragPositions.length; i++) {
			if ((this.dragPositions[i].left > currentX)
				|| (this.dragPositions[i].left < currentX && (this.dragPositions[i].width + this.dragPositions[i].left > currentX))
			) {
				this.dragPosition = i + this.dragColNumStart;
				break;
			}
			//right position
			if (i + 1 == this.dragPositions.length) {
				this.dragPosition = i + 1 + this.dragColNumStart;
			}
		}

		var ths = this.clearHeadersStyles();
		if (ths[this.dragPosition]) {
			ths[this.dragPosition].setStyle({borderLeftColor: 'red', borderLeftWidth:'1px'});
		} else {
			ths[this.dragColNumEnd].setStyle({borderRightColor: 'red', borderRightWidth:'1px'});
		}
	},
	clearHeadersStyles : function() {
		var ths = $$('#productGrid_table thead .headings th');
		ths.each(function(elm){
			elm.removeAttribute('style');
		});

		return ths;
	},
	afterDragAndDrop : function() {
		document.onselectstart = null; // ie
		document.onmousedown = null;
		this.dragStarted = false;
		this.dragElm = null;
		if (this.dragBlock) {
			this.replaceCols();
			this.dragBlock.remove();
			this.dragBlock = null;
		}
		this.clearHeadersStyles();
	},
	replaceCols : function() {
		if (this.dragElmNum != this.dragPosition) {
			var cols = $$('#productGrid_table col');
			if (this.dragPosition == cols.length) {
				cols[this.dragPosition - 1].insert({after: cols[this.dragElmNum]});
			} else {
				cols[this.dragPosition].insert({before: cols[this.dragElmNum]});
			}
			var rows = $$('#productGrid_table tr');
			for (var i = 0; i < rows.length; i++) {
				var ceils = rows[i].select('td, th');
				if (this.dragPosition == ceils.length) {
					ceils[this.dragPosition - 1].insert({after: ceils[this.dragElmNum]});
				} else {
					ceils[this.dragPosition].insert({before: ceils[this.dragElmNum]});
				}
			}
			// +1 - array first index is 0, -this.dragColNumStart - maybe starts from 2 or more
			/*var position = (this.dragPosition == cols.length ? this.dragPosition - 1 : this.dragPosition) - this.dragColNumStart + 1;*/
			/*var position = this.dragPosition - this.dragColNumStart + 1;
			if (this.dragPosition > this.dragElmNum) {
				position--;
			}*/
			var attributeCodes = [];
			$$('#productGrid_table .itoris-attribute-code').each(function(elm){
				attributeCodes.push(elm.innerHTML);
			});
			this.saveColumnOrder(attributeCodes.join(','));
		}
		return this;
	},
	saveColumnOrder : function(columns) {
		new Ajax.Request(this.config.save_column_order_url, {
			method: 'post',
			parameters: {
				columns: columns,
				form_key: this.config.form_key
			},
			onComplete: function() {}
		});
	},
	createDragTable : function() {
		var dragTable = document.createElement('table');
		Element.extend(dragTable);
		dragTable.addClassName('itoris-draggable-table');
		var tbody = document.createElement('tbody');
		var rows = $$('#productGrid_table tr');
		for (var i = 0; i < rows.length; i++) {
			var row = document.createElement('tr');
			var col = document.createElement('td');
			col.innerHTML = rows[i].select('td, th')[this.dragElmNum].innerHTML;
			row.appendChild(col);
			tbody.appendChild(row);
		}
		dragTable.appendChild(tbody);
		$$('body')[0].appendChild(dragTable);
		return dragTable;
	},
	openConfigWindow : function() {
		this.openPopupWindow(this.getConfigWindow());
	},
	closeConfigWindow : function() {
		this.closePopupWindow(this.getConfigWindow());
	},
	openPopupWindow : function(window) {
		this.getMask().show();
		window.show();
		var configWindowHeight = window.getHeight();
		var viewportHeight = document.viewport.getHeight();
		var top = document.viewport.getScrollOffsets()[1];
		if (viewportHeight > configWindowHeight) {
			top += (viewportHeight - configWindowHeight) / 2;
		}
		window.setStyle({top: top + 'px'});
	},
	closePopupWindow : function(window) {
		this.getMask().hide();
		window.hide();
	},
	getMask : function() {
		return $(this.classPrefix + '_mask');
	},
	getConfigWindow : function() {
		return $(this.classPrefix + '_config');
	},
	showAttributeValuesForBulk : function(elm) {
		this.hideAndDisableAllBulkAttributesValues();
		var value = elm.value;
		var attribute = this.getAttributeConfig(value);
		if (attribute) {
			var type = attribute.type;
			var valueElm = type.indexOf('select') != -1 ? $('attr_' + value) : $('attr_' + type);
			this.bulkAttributeElm = valueElm;
			if (type.indexOf('select') != -1) {
				var isRequired = parseInt(attribute.required);
				if (valueElm.hasClassName('required-entry') && !isRequired) {
						valueElm.removeClassName('required-entry');
				} else if (isRequired) {
					valueElm.addClassName('required-entry');
				}
			}
			if (valueElm) {
				valueElm.up().show();
				valueElm.disabled = false;
				if (attribute.required) valueElm.addClassName('required-entry'); else valueElm.removeClassName('required-entry');
			}
			var applyAsElement = $('attr_multiselect_action');
			if (type == 'multiselect') {
				applyAsElement.up().show();
				applyAsElement.disabled = false;
			} else {
				applyAsElement.up().hide();
				applyAsElement.disabled = true;
			}
		}
	},
	validateBulkAttribute : function() {
		if (this.bulkAttributeElm && this.bulkAttributeElm.up('#productGrid_massaction-form-additional') && !Validation.validate(this.bulkAttributeElm)) {
			alert(this.config.message_attribute_blank);
		}
	},
	getAttributeConfig : function(id) {
		for (var i = 0; i < itorisProductGridAttributesConfig.length; i++) {
			if (itorisProductGridAttributesConfig[i].value == id) {
				return itorisProductGridAttributesConfig[i];
			}
		}
		return null;
	},
	hideAndDisableAllBulkAttributesValues : function() {
		$$('.itoris-bulk-attribute-value').each(function(elm) {
			if (elm.tagName == 'SELECT') {
				if (elm.select('option')) {
					elm.select('option').each(function(opt){
						opt.innerHTML = opt.innerHTML.sub('&amp;', '&', 100);
					});
				}
			}
			elm.disabled = true;
			elm.up().hide();
			if (elm.multiple) {
				elm.size = 7;
			} else if (elm.tagName == 'TEXTAREA') {
				elm.rows = 7;
				elm.style.width = '100%';
			}
		});
	},
	fixOptionsNbsp: function() {
		if ($('product_filter_categories')) {
			$('product_filter_categories').select('option').each(function(option){
				option.innerHTML = option.innerHTML.replace(/&amp;nbsp;/g, '&nbsp;');
			});
		}
	},
	changeColumnOrder : function() {}
});
window.ondragstart = function() { return false; };
window.ondrag = function() { return false; };