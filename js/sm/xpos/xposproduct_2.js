/*
 * Author here?
 *
 * TODO - huypq 2015-03-17
 * - SHOULD do add mode: -v verbose. Let's it talks much more to console
 * -- function prelog(thing) {if(verbose){//console.log(thing);}}
 * - SHOULD do remove some quick & dirty code
 * -- scroller
 * -- hover
 * -- inline CSS style??? >.<
 */
(function (jQuery) {
    jQuery(document).ready(function () {
        productData = prepareProductData();

        if (jQuery('#online_search_product').val() == '1') {
            varienGlobalEvents.attachEventHandler('xPos_loaded_product_data', function () {
                jQuery('#status').text(' | ' + 100 + '% of ' + Object.keys(productData).length + ' products (saved ' + Object.keys(productData).length + ')');
            });
        }

        //get all orders
        var orders = $.jStorage.get("orders");

        if (orders != null) {
            jQuery('#count_pending_orders').html(orders.length);
        }

        //search base jquery autocomplete
        jQuery('#search-box').keyup(function (event) {
            /*
             * XPOS-2324 Easy Keyboard
             */
            var keyCode = event.keyCode || event.which;

            if (keyCode == 37 || keyCode == 38 || keyCode == 39 || keyCode == 40 || keyCode == 13 || keyCode == 192) {
                event.preventDefault();
                return false;
            }

            delay(function () {
                var alert_box_stt = jQuery('#alert_box_stt').val();
                if (alert_box_stt == 1) {
                    jQuery('#alert_box_stt').val('0');
                    return;
                }
                if (event.ctrlKey) return;
                var term = jQuery('#search-box').val();
                if (term == '') {
                    //var default_cate = jQuery("#cate_default").val();
                    var default_cate = $.jStorage.get("cate_default");
                    var cate_default_name = $.jStorage.get("cate_default_name");
                    if (default_cate >= 0) {
                        displayProduct(default_cate, 0);
                        //initScroll("#product-info");
                        // alert(cate_default_name);
                        jQuery('#category-selected').text(cate_default_name);
                        jQuery('#category-selected').append('<i>icon</i>');
                        jQuery('#category-list').slideUp('slow');
                        jQuery('#category-selected').removeClass('show').addClass('hide');

                    }
                    else {
                        jQuery('#category-selected').text("Select category");
                        jQuery('#category-selected').append('<i>icon</i>');
                    }
                }
                else {
                    jQuery("#product-info").empty();
                }
                var lucky_search = parseInt(jQuery('#lucky_search').val());
                var last_key_search = jQuery('#last_key_search').val();

                //jQuery('#search-box').val('');
                if (offlineSearch && term != "") {
                    jQuery('#last_key_search').val(term);
                    //var data = search(term, productData);
                    term = term.toLowerCase();
                    var result = [];
                    var number_result = jQuery("#result_number_search").val();
                    jQuery.each(productData, function (i, item) {
                        if (item['name'] == null || item['sku'] == null) {
                            return true;
                        }
                        if (result.length < number_result &&
                            (item['searchString'] != null && item['searchString'].toLowerCase().match(term))) {
                            result.push(item);
                        }
                    });

                    if (result.length == 1 && lucky_search == 1) {
                        addToCart(result[0].id);
                    }

                    var data = result;
                    if (data.length == 1)
                        $.jStorage.set('searchData', data);
                    else $.jStorage.set('searchData', null);
                    if (data.length == 0) {
                        jQuery('#category-selected').text("No search result for : " + term);
                        jQuery('#category-selected').append('<i>icon</i>');
                        if (event.which != 13) {
                            return;
                        }
                        //else
                        //document.getElementById("search-box").select();
                        return;
                    }

                    var count_rs = 0;
                    jQuery.each(data, function (i, item) {
                        displayProductItem(item, true);
                        count_rs++;
                    });
                    var result_string = "result";
                    if (count_rs > 1) {
                        result_string = "results";
                    }
                    jQuery('#category-selected').text(count_rs + ' ' + result_string + ' for: ' + term);
                    jQuery('#category-selected').append('<i>icon</i>');
                    //initScroll("#product-info");

                    /*
                     * XPOS-2359: Lucky search does not work
                     * TODO: Remove lucky search
                     */
                    if (!!document.getElementById('product-info').children[0]) {
                        window.CURRENT_PRODUCT_ITEM = document.getElementById('product-info').children[0];
                        document.getElementById('product-info').children[0].className = 'active';
                        document.getElementById('product-info').children[0].querySelector('a[id^="product"]').focus();
                    }

                    if (data.length > 1) {
                        if (event.which != 13) {
                            return;
                        }
                        else
                        //document.getElementById("search-box").select();
                            return;
                    }
                } else {
                    if (isOnline() && term != "") {
                        iLog('Online Search');
                        jQuery('#last_key_search').val(term);
                        jQuery('#category-selected').text("Searching online result for : " + term);
                        jQuery('#category-selected').append('<i>icon</i>');
                        //>setCountryId($address->getCountryId())
                        //    ->setRegionId($address->getRegionId())
                        //    ->setPostcode($address->getPostcode())
                        var billingCountryId = jQuery('#order-billing_address_address_country_id option:selected').val();
                        var billingRegionId = jQuery('#order-billing_address_address_region_id option:selected').val();//order-billing_address_address_region_id
                        var billingPostCode = jQuery('#order-billing_address_address_postcode').val();
                        var shippingCountryId = jQuery('#order-shipping_address_address_country_id option:selected').val();
                        var shippingRegionId = jQuery('#order-shipping_address_address_region_id option:selected').val();//order-billing_address_address_region_id
                        var shippingPostCode = jQuery('#order-shipping_address_address_postcode').val();
                        var customerId = jQuery('#order_customer_id').val();
                        var url1 = searchProductUrl + "?query=" + term + "&billingCountryId=" + billingCountryId + "&billingRegionId=" + billingRegionId + "&billingPostCode=" + billingPostCode + "&shippingCountryId=" + shippingCountryId + '&shippingRegionId=' + shippingRegionId + "&shippingPostCode=" + shippingPostCode + "&customerId=" + customerId;
                        var url = url1.replace("?___SID=U", "");
                        jQuery.getJSON(url, function (json) {
                            var data_search = json;
                            var productInfo = data_search['productInfo'];
                            var lstProductId = [];
                            /*Set product to ProductData even it already existed*/
                            if (productInfo != null) {
                                jQuery.each(productInfo, function (i, item) {
                                    lstProductId.push(item.id);
                                    productData[item.id] = item;
                                });
                                $.jStorage.set('productData', productData)
                            }
                            //$.jStorage.set('productData', productData);
                            var count = 0;
                            for (jsonObj in productInfo) {
                                if (productInfo.hasOwnProperty(jsonObj)) {
                                    count++;
                                }
                            }
                            var data = [];
                            jQuery("#product-info").empty();
                            if (count > 0)
                                for (var i = 0; i < lstProductId.length; i++) {
                                    data.push(productData[lstProductId[i]]);
                                }
                            if (data.length == 1)
                                $.jStorage.set('searchData', data);
                            else $.jStorage.set('searchData', null);
                            if (data.length == 0) {
                                jQuery('#category-selected').text("No search result for : " + term);
                                jQuery('#category-selected').append('<i>icon</i>');
                                if (event.which != 13) {
                                    return;
                                }
                                //else
                                //document.getElementById("search-box").select();
                                return;
                            }

                            var count_rs = 0;
                            jQuery.each(data, function (i, item) {
                                displayProductItem(item, true);
                                count_rs++;
                            });

                            var result_string = "result";
                            if (count_rs > 1) {
                                result_string = "results";
                            }
                            jQuery('#category-selected').text(count_rs + ' ' + result_string + ' for: ' + term);
                            jQuery('#category-selected').append('<i>icon</i>');
                            //initScroll("#product-info");

                            if (data.length == 1) {
                                if (event.which != 13) {
                                    return;
                                }
                                else {
                                    if (lucky_search == 1) {
                                        addToCart(data[0].id);
                                        //window.product_cart_id = data[0].id;
                                    }
                                    //document.getElementById("search-box").select();
                                }

                            }
                            if (data.length > 1) {
                                if (event.which != 13) {
                                    return;
                                }
                                //else
                                //document.getElementById("search-box").select();
                            }
                        })
                    }
                }
            }, 1500);

//            if ( event.which != 13 ) {
//                return;
//            }

        });

        //keyup please
        jQuery('body').on("keydown", '.item-qty', function (event) {
            if (jQuery(this).attr('class').indexOf('qty_decimal') == -1 && event.keyCode == 190)
                event.preventDefault();

            if (jQuery(this).attr('class').indexOf('qty_decimal') == -1 && event.keyCode == 188)
                event.preventDefault();
        });

        jQuery('body').on("keydown", '.item-price', function (event) {
            if (event.shiftKey == true) {
                // event.preventDefault(); allow to enter % char
            }

            if ((event.keyCode >= 48 && event.keyCode <= 57) ||
                (event.keyCode >= 96 && event.keyCode <= 105) ||
                event.keyCode == 16 ||
                event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
                event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190 || event.keyCode == 110 || event.keyCode == 188 ||
                event.key == "%"
            ) {
                if (event.key == "!" || event.key == "@" || event.key == "#" || event.key == "$" || event.key == "^" || event.key == "&" || event.key == "*" || event.key == "(" || event.key == ")") {
                    event.preventDefault();
                }
            } else {
                event.preventDefault();
            }
//            if((jQuery(this).attr('class').indexOf('qty_decimal') != -1 && jQuery(this).attr('class').indexOf('item-qty') != -1) && event.keyCode == 190 )
//                event.preventDefault();
            if ((jQuery(this).val().indexOf('.') !== -1 || jQuery(this).val().indexOf(',') !== -1) && event.keyCode == 190)
                event.preventDefault();

            if ((jQuery(this).val().indexOf(',') !== -1 || jQuery(this).val().indexOf('.') !== -1 ) && event.keyCode == 188)
                event.preventDefault();

            if ((jQuery(this).val().indexOf(',') !== -1 || jQuery(this).val().indexOf('.') !== -1 ) && event.keyCode == 110)
                event.preventDefault();

//            if(jQuery(this).val().indexOf('.') !== -1 && event.keyCode == 110)
//                event.preventDefault();
        });

    });
})(jQuery);

var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

var data;
var selectedProductOptions = new Array();

firstShowCategoryDefault = false;
function showCategoryDefault(checkFirst) {
    if (!!checkFirst) {
        if (firstShowCategoryDefault) {
            return;
        }
        firstShowCategoryDefault = true;
    }
    productData = prepareProductData();
    //show product default category in xpos, should be place after prepare productdata
    var default_cate = jQuery("#cate_default").val();
    var cate_default_name = jQuery("#cate_default_name").val();
    if (default_cate != undefined) {
        $.jStorage.set("cate_default", default_cate);
        $.jStorage.set("cate_default_name", cate_default_name);
        displayProduct(default_cate, 0);
    }
}

(function (window, undefined) {
    var ProductPage = Class.create();
    ProductPage.prototype = {
        initialize: function () {
            this.key = 'product_page_storage';
        },
        resetPage: function () {
            $.jStorage.set('product_page_storage', 1);
            return 1;
        },
        getCurrentPage: function () {
            return $.jStorage.get(this.key, false) || this.resetPage();
        },
        increment: function () {
            var nextPage = this.getCurrentPage() + 1;
            //console.log('set next page = ' + nextPage);
            $.jStorage.set('product_page_storage', nextPage);
            var afterSet = $.jStorage.get('product_page_storage', false);
            //console.log('next page = ' + afterSet);
            //console.log($.jStorage.get('product_page_storage'));
            return afterSet;
        }
    };

    window.ProductPage = new ProductPage();
})(window, undefined);


function getData() {
    if (offlineSearch == false) {
        iLog('Setting Search Only Only.');
        varienGlobalEvents.fireEvent('xPos_loaded_product_data');
        //Only access online data in X-POS
        return true;
    }
    if (offlineSearch == false) {
        varienGlobalEvents.fireEvent('xPos_loaded_product_data');
        //Only access online data in X-POS
        return true;
    }
    var check_new_interval = jQuery('#check_new_interval').val() * 1000;
    checkXPosProductData(function () {
        var currentPage = ProductPage.getCurrentPage();
        iLog('true call back with currentPage = ', currentPage);
        getDataFromServer(currentPage);
    }, function () {
        setTimeout(getData, check_new_interval);
    });

}

var hasProductCachKey = '';
var hasNewData = 0;
var productUpdated = 0;

function getDataFromServer(page) {
    setStatusXposWorking(' | >>Loading Product ');
    var cache_key = 'xpos_data_cache_key';
    var warehouseId = $.jStorage.get('xpos_warehouse');
    var append = (loadProductUrl.indexOf('?') > -1) ? '&page=' + page : '?page=' + page;
    var data_load_interval = jQuery('#data_load_interval').val() * 1000; //fix load interval
    var check_new_interval = jQuery('#check_new_interval').val() * 1000;
    if (warehouseId != null) {
        append += '&warehouse=' + warehouseId.warehouse_id;
    }
    append += '&storeId=' + window.multiStoreView.getCurrentStoreId();
    jQuery.getJSON(loadProductUrl + append, function (json) {

        data = json;
        var totalLoad = data['totalLoad'];
        var totalProduct = data['totalProduct'];
        var totalSaved;
        productUpdated += totalLoad;
        iLog('ProductUpdated', productUpdated);
        saveData(data, page);
        var next = page + 1;
        $.jStorage.set('product_page_storage', next);
        var afterin = $.jStorage.get('product_page_storage');
        iLog('next page to Load', afterin);


        _plural = totalProduct > 1 ? ' products' : ' product';
        totalSaved = Object.keys(productData).length;
        var _updatedPercentage = (totalSaved / totalProduct * 100).toFixed(0);
        if (hasNewData == 1) {
            _updatedPercentage = (productUpdated / totalProduct * 100).toFixed(0);
        }
        var _status = ' | ' + _updatedPercentage + '% of ' + totalProduct + _plural + ' (saved ' + totalSaved + ')';

        jQuery('#status').text(_status);
        if (_updatedPercentage > 99) {
            showCategoryDefault(true);
        }

        if (totalLoad == 0) {
            setStatusXposWorking('Almost finished loading products ');
            iLog('total Load == 0');
            if (data.hasOwnProperty('xpos_cache_key')) {
                iLog('set cache key');
                $.jStorage.set('xpos_data_cache_key', data['xpos_cache_key']);
                iLog('get cache key after set =', $.jStorage.get(cache_key, data['xpos_cache_key']));
                hasProductCachKey = data['xpos_cache_key'];
                hasNewData = 0;
                productUpdated = 0;
            }
            varienGlobalEvents.fireEvent('xPos_loaded_product_data');
            setTimeout(function () {
                setStatusXposWorking('');
                getData();
            }, check_new_interval);
        } else {
            hasProductCachKey = 0;
            setTimeout(function () {
                getDataFromServer(afterin);
            }, data_load_interval);
        }
    });
}


var firstCheckXPosProductData = true;
function checkXPosProductData(whenTrueCallback, whenFalseCallback) {
    // backup firstCheckXPosProductData prevent early return
    var firstCheck = firstCheckXPosProductData;
    firstCheckXPosProductData = false;
    var data_load_interval = jQuery('#data_load_interval').val() * 1000;

    var cache_key = 'xpos_data_cache_key';

    var append = '';

    var xpos_data_cache_key = $.jStorage.get('xpos_data_cache_key', '0');
    if (!xpos_data_cache_key && !!hasProductCachKey) {
        xpos_data_cache_key = hasProductCachKey;
    }
    if (xpos_data_cache_key != '0' || hasProductCachKey == 1) {
        iLog('cache key exist');
        //if cache existed
        append = (xPosCheckProductUrl.indexOf('?') > -1) ? '&cache_key=' + xpos_data_cache_key : '?cache_key=' + xpos_data_cache_key;
        append += '&storeId=' + window.multiStoreView.getCurrentStoreId();
        jQuery.getJSON(xPosCheckProductUrl + append, function (json) {
            if (json.hasOwnProperty('has_new_data') && !!json.has_new_data) {
                //verbose log
                iLog('Has New Data');
                hasNewData = 1;
                productUpdated = 0;
                $.jStorage.set('product_page_storage', 1);
                $.jStorage.deleteKey(cache_key);
                return whenTrueCallback();
            } else {
                if (window.show_cate_default == null)
                    window.show_cate_default = 1;
                if (window.show_cate_default == 1) {
                    showCategoryDefault(true);
                    window.show_cate_default = 0;
                }
                if (firstCheck) {
                    varienGlobalEvents.fireEvent('xPos_loaded_product_data');
                }
                whenFalseCallback();
            }
        });
    } else {
        //if cache not existed
        iLog('cache not exist');
        whenTrueCallback();
    }
}

function saveData(data, page, callback) {
    var productInfo = data['productInfo'];
    jQuery.each(productInfo, function (i, item) {
        productData[item.id] = item;
    });
    $.jStorage.set('productData', productData);

    if (typeof callback === "function") {
        callback();
    }
}

/**
 * huypq
 * 31/03/205
 * Update product data loaded progress status
 */
var isCountAllProductsUrlAppendedWarehouse = false;
function updateProductsLoadedStatus() {
    var warehouseId = $.jStorage.get('xpos_warehouse');
    var appendWarehouse = '';
    if (warehouseId != null && isCountAllProductsUrlAppendedWarehouse) {
        appendWarehouse = '?warehouse=' + warehouseId.warehouse_id;
        isCountAllProductsUrlAppendedWarehouse = true;
    }

    //countAllProductsUrl += appendWarehouse;
    countAllProductsUrl += appendWarehouse;

    jQuery.getJSON(countAllProductsUrl, function (_data) {
        _totalProduct = _data.number;
        _totalLoaded = Object.keys(productData).length;

        _updatedPercentage = (_totalLoaded / _totalProduct * 100).toFixed(0);
        _plural = _data.number > 1 ? ' products' : ' product';

        _status = ' | ' + _updatedPercentage + '% of ' + _totalProduct + _plural + ' (saved ' + _totalLoaded + ')';
        jQuery('#status').text(_status);
    });
}


/**
 * Author here?
 * @return empty || Array of productData
 */
function prepareProductData() {

    //console.log('Preparing Product Data---');

    var data = {};

    /** Get productData from jStorage
     *
     */

    var storageList = $.jStorage.get('productData');

    if (storageList != null && storageList != undefined) {
        data = storageList;
    }

    return data;

    /*
     if (jQuery.isEmptyObject($.jStorage.get('origProductData'))) {
     var storageList = $.jStorage.get('productData');
     //original data is not set. set it
     console.log('%cSet orignal product data', 'color: blue');
     $.jStorage.set('origProductData', storageList);
     } else {
     //revoke original data
     console.log('%cRevoke Original Data', 'color: green; font-weight:bold');
     var storageList = $.jStorage.get('origProductData');
     }


     if (storageList !=null && storageList != undefined){
     data = storageList;
     }

     return data;
     */
}

function search(term, productData) {
    term = term.toLowerCase();
    var result = [];
    jQuery.each(productData, function (i, item) {
        if (result.length < 51 &&
            (item['name'].toLowerCase().match(term) || item['sku'].toLowerCase().match(term) ||
            (item['searchString'] != null && item['searchString'].toLowerCase().match(term)))) {
            result.push(item);
        }
    })

    if (result.length == 1) {
        addToCart(result[0].id);
    }
    return result;
}

var productTemplate = '<li onmouseover="productItemHovered(event, this);">'
    + '<div class="product-hover-info">'
    + '<div class="literal">'
    + '<h6 class="pro-name">{name1} {name2}</h6>'
    + '<div class="pro-pair"><span>SKU</span><span>: </span><span>{sku}</span></div>'
    + '<div class="pro-pair"><span>Type: </span><span style="text-transform: capitalize;"><em>{type}</em></span></div>'
    + '<div class="pro-pair"><span>Original Price</span><span>: </span><span><strong>{price}</strong></span></div>'
    + '{qtyText}'
    + '{additionalInformation}'
    + '</div>'
    + '<div class="decorate"></div>'
    + '</div>'
    + '<div class="product-wrapper">'
    + '<span class="{type}"></span><a class="product" id="product-{id}" href="#" onclick="addToCart({id})"><img src="{small_image}"><div class="price"><span>{finalPrice}<span></div><div class="out-stock"><span>{outStock}<span></div><span style="margin-top: 4px; margin-bottom: -14px;" class="name" >{name1}</span><span style="-o-text-overflow: ellipsis;text-overflow:ellipsis; white-space:nowrap;overflow:hidden;margin-bottom:-14px; " class="name">{name2}</span></a>'
    + '</div></li>';
var productSearchTemplate = '<li onmouseover="productItemHovered(event, this);">'
    + '<div class="product-hover-info">'
    + '<div class="literal">'
    + '<h6 class="pro-name">{name1} {name2}</h6>'
    + '<div class="pro-pair"><span>SKU</span><span>: </span><span>{sku}</span></div>'
    + '<div class="pro-pair"><span>Type: </span><span style="text-transform: capitalize;"><em>{type}</em></span></div>'
    + '<div class="pro-pair"><span>Original Price</span><span>: </span><span><strong>{price}</strong></span></div>'
    + '{qtyText}'
    + '{additionalInformation}'
    + '</div>'
    + '<div class="decorate"></div>'
    + '</div>'
    + '<div class="product-wrapper">' +
    '<span class="{type}"></span><a class="product" id="product-{id}" href="#" onclick="addToCart({id})"><img src="{small_image}"><div class="price"><span>{finalPrice}<span></div><div class="out-stock"><span>{outStock}<span></div><span style="margin-top: 4px; margin-bottom: -14px;" class="name" >{name1}</span><span style="-o-text-overflow: ellipsis;text-overflow:ellipsis; white-space:nowrap;overflow:hidden;margin-bottom:-14px; " class="name">{name2}</span></a>'
    + '</div>'
    + '</li>';// the last div : <span class="name">{sku} - {name}</span></a>

/**
 * huypq
 */
function productItemHovered(event, elem) {
    var scrollPanel = elem.parentNode;
    var infoItem = elem.firstChild;
    var pos = getPos(elem);

    pos.x -= infoItem.offsetWidth;
    //5px to release hover
    pos.x -= 5;
    pos.y -= scrollPanel.scrollTop;

    infoItem.style.top = pos.y + 'px';
    infoItem.style.left = pos.x + 'px';

    //console.log(scrollPanel.scrollTop);
}

/**
 * huypq
 * http://stackoverflow.com/questions/288699/get-the-position-of-a-div-span-tag#288708
 *
 * Get the x, y position of the element relative to the page/
 * - Loop up through all the element's parents and add their offsets together.
 */
function getPos(el) {
    for (var lx = 0, ly = 0;
         el != null;
         lx += el.offsetLeft, ly += el.offsetTop, el = el.offsetParent);
    return {x: lx, y: ly};
}

/*
 * Author here?
 * @param category
 * @param page
 * @return
 */
function displayProduct(category, page) {
    var productList = getProductByCategory(category, page);
    jQuery("#product-info").attr('data', category);
    jQuery("#product-info").attr('page', page);
    jQuery("#product-info").empty();
    jQuery.each(productList, function (i, item) {
        displayProductItem(item, false);
    });
    //initScroll("#product-info");

    jQuery('#category-list li').removeClass('active');
    var element = jQuery('#category-list li').find('[data="category-' + category + '"]')[0];
    element.parentNode.parentNode.addClassName('active');
    jQuery('#category-selected').text(element.text);
    jQuery('#category-selected').append('<i>icon</i>');
    jQuery('#category-list').hide();
    jQuery('#category-selected').removeClass('show').addClass('hide');

}

function getProductByCategory(category, page) {
    var result = [];
    var p = categoryData[category];
    for (var i = 0; i < p.length; i++) {
        result.push(productData[p[i]]);
    }
    return result;
}
/*
 * using nano template to append product,
 * isSearch = true to show in search mode,
 * false in normal mode
 */
function displayProductItem(item, isSearch) {
    //verbose console
    prelog('-- Displaying Product Item...');

    if (item == null || item == undefined || item.sku == null || item.price == null) {/*watched-XPOS-2274*/
        return;
    }
    //convert name
    var pro_name = item.name;

    var length = pro_name.length;
    if (length > 10) {
        var name1 = pro_name.substring(0, 10);
        var next_char = pro_name.substring(10, 11);
        if (next_char != ' ') {
            var index_last_space = name1.search(/ [^ ]*$/);
            name1 = pro_name.substring(0, index_last_space);
            var name2 = pro_name.substring(index_last_space, length);

        } else {
            var name2 = pro_name.substring(10, length);
        }
    }
    else name1 = pro_name;

    var productItem = {};
    productItem.type = item.type;
    productItem.id = item.id;
    productItem.small_image = item.small_image;
    productItem.name1 = name1;
    productItem.name2 = name2;

    productItem.price = item.price;
    productItem.qty = parseInt(item.qty);
    if(item.hasOwnProperty('ms') && item.ms == 0){
        productItem.qtyText = '';
    }else{
    productItem.qtyText = (productItem.type == 'configurable') ? '' : '<div class="pro-pair"><span>Stock</span><span>: </span><span id="view-product-qty-{id}">' + productItem.qty + '</span></div>';
    }
    //productItem.name = item.name;
    productItem.sku = item.sku;
    productItem.finalPrice = Number(item.priceInclTax).toFixed(2);
    /*watched-XPOS-2274*/

    productItem.additionalInformation = (!!item.additional_info) ? '<div class="pro-plus"><h6 class="title">Additional information</h6>' : null;
    for (_key in item.additional_info) {
        var _additionInfo = item.additional_info[_key];
        _additionInfo = _additionInfo.replace('_', ' ');
        _key = _key.replace('_', ' ');
        productItem.additionalInformation += '<div class="item"><span class="title">' + _key + '</span>: <span>' + _additionInfo + '</span></div>';
    }
    if (!!productItem.additionalInformation) {
        productItem.additionalInformation += '</div>';
    }

    if (displayTaxInCatalog) {
        if (productItem.type != "giftcard")
            productItem.finalPrice = Number(item.priceInclTax).toFixed(2);
        /*watched-XPOS-2274*/
    }
    if (isSearch) {
        jQuery("#product-info").append(nano(productSearchTemplate, productItem).replace('<img src="">', placeholder));
    } else {
        jQuery("#product-info").append(nano(productTemplate, productItem).replace('<img src="">', placeholder));
    }
}

var firstCheckOrder = true;
var isCheckOrderAction = false;
var numOfCurItemInOrder = 0;
var tempOldOrderItem = {};
function saveOldOrderItem(con) {
    if (con == true) {
        iLog('go to save Old Order - user not update item');
        tempOldOrderItem = currentOrder;
    } else {
        iLog('go to save Old Order - user  updated item');
        console.log('current Oder');
        console.log(currentOrder);
        var tempKeyCurrentOrder = Object.keys(currentOrder);
        console.log(tempKeyCurrentOrder);
        iLog('lengt tempKey', tempKeyCurrentOrder.length - 1);
        var lastKey = tempKeyCurrentOrder[tempKeyCurrentOrder.length - 1];
        console.log(lastKey);
        iLog('Key to add tempOldOrder', lastKey);
        tempOldOrderItem[lastKey] = currentOrder[lastKey];
    }
}

function addToCart(itemId) {
    var item = productData[itemId];
    var pId = 'p' + itemId;
    /*
     * Temporary approach
     * Get track of current product is being added. Thus, can obtain it's tax percent
     */
    window.PRODUCT_BEING_ADDED = item;
    var tax_percent = 0;
    //console.log('current item options:' + item.options);
    var hasExistedItemOption = false;
    if (window.isBrSpSt == true && typeof(window.dataProOp[pId]) != "undefined") {
        //iLog('hasExistedItemOption');
        hasExistedItemOption = true;
    }
    if ((item.h || item.type != 'simple') && window.ultraBootLoad === '1' && hasExistedItemOption == false) {
        var getProductOp = urlGetOp;
        var url = getProductOp + "?productId=" + itemId;
        //iLog('here');
        //displayLoadingMask();
        jQuery.getJSON(url, function (json) {
            hideLoadingMask();
            var dataProductOp = json;
            if (dataProductOp.options == '0') {
                iLog('get Option = null', null, 5);
            } else {
                if (window.isBrSpSt == true) {
                    //iLog('set OP->to dataProOp');
                    window.dataProOp[pId] = dataProductOp.options;
                    localStorage.setItem("dataProOp", JSON.stringify(window.dataProOp));
                }
                /*not save to jstorage*/
                //productData[itemId].options = dataProductOp.options;
                item.options = dataProductOp.options;
                if (item.tax != '') {
                    tax_percent = item.tax;
                }

                //show config panel
                optionsPrice = new Product.OptionsPrice({
                    "productId": itemId,
                    "priceFormat": priceFormat,
                    "showBothPrices": true,
                    "productPrice": item.price,
                    "productOldPrice": item.price,
                    "priceInclTax": item.priceInclTax,
                    "priceExclTax": item.price,
                    "skipCalculate": 0,
                    "defaultTax": tax_percent,
                    "currentTax": tax_percent,
                    "idSuffix": "_clone",
                    "oldPlusDisposition": 0,
                    "plusDisposition": 0,
                    "plusDispositionTax": 0,
                    "oldMinusDisposition": 0,
                    "minusDisposition": 0,
                    "tierPrices": [],
                    "tierPricesInclTax": [],
                    "includeTax": (item.includeTax == '1') ? true : false
                });

                if (item.type == 'grouped') {
                    optionsPrice.productPrice = 0;
                    optionsPrice.productOldPrice = 0;
                    optionsPrice.priceInclTax = 0;
                    optionsPrice.priceExclTax = 0;
                }
                jQuery('#product-option').empty();
                jQuery('#product-option').append('<div class="price-box">Price: <span id="price-excluding-tax-' + itemId + '" class="regular-price giftcard_input_price">' + item.price + '</span><span class="including-tax"> Incl. Tax: </span><span id="price-including-tax-' + itemId + '" class="regular-price giftcard_input_price">' + item.priceInclTax + '</span></div>');
                jQuery('#product-option').append(item.options);
                jQuery('#product-option').append('<div class="action"><button id="cancel" class="button cancel" type="button"><span>Cancel</span></button><button id="ok" class="ok" type="submit" ><span>OK</span></button></div>');
                jQuery('#product-option-form').bPopup({closeClass: 'button'});
                var productCompositeConfigureForm = new varienForm('product-option');

                /*
                 * Temporary
                 * XPOS 2344
                 */
                optionInputElements = document.getElementById('product-option').querySelectorAll('input, select');
                if (optionInputElements[0] != undefined) {
                    for (var _i = 0; _i < optionInputElements.length; _i++) {
                        if (optionInputElements[_i].disabled == false && optionInputElements[_i].type != 'hidden') {
                            optionInputElements[_i].focus();
                            break;
                        }
                    }
                }

                jQuery("#product-option").unbind();

                if (item.type == 'bundle') {
                    ProductConfigure.bundleControl.reloadPrice(); // config price at the first time if user don't select option
                }

                jQuery("#product-option").submit(function (event) {
                    if (productCompositeConfigureForm.validate()) {
                        jQuery('#product-option-form').bPopup().close();
                        addConfigurableProduct(itemId);
                        var term = jQuery('#search-box').val();
                        //if (term.length > 0) document.getElementById("search-box").select();
                    }
                    event.preventDefault();
                });
                if (item.type != 'giftcard')
                    optionsPrice.reload();
            }
            /*need to run*/
            if (item.type == 'grouped') {
                groupProductReloadPrice(itemId);
            }
            auto_select_field();
            jQuery('#product-info li').removeClass('active');
            jQuery('#product-' + itemId).parent().parent().addClass('active');

            //console.log("Add to cart " + itemId);

            // refresh scroller
            //initScroll("#items_area");

            var term = jQuery('#search-box').val();
            //if (term.length > 0) document.getElementById("search-box").select();
            if (firstCheckOrder == true) {
                saveOldOrderItem(true);
            } else {
                console.log('after go to save OldOrder');
                console.log(currentOrder);
                saveOldOrderItem(false);
            }


        });

    } else {
        if (hasExistedItemOption == true) {
            iLog('has existed current item options', null, 1);
            item.options = window.dataProOp[pId];
        }
        if (item.options) {
            if (item.tax != '') {
                tax_percent = item.tax;
            }

            //show config panel
            optionsPrice = new Product.OptionsPrice({
                "productId": itemId,
                "priceFormat": priceFormat,
                "showBothPrices": true,
                "productPrice": item.price,
                "productOldPrice": item.price,
                "priceInclTax": item.priceInclTax,
                "priceExclTax": item.price,
                "skipCalculate": 0,
                "defaultTax": tax_percent,
                "currentTax": tax_percent,
                "idSuffix": "_clone",
                "oldPlusDisposition": 0,
                "plusDisposition": 0,
                "plusDispositionTax": 0,
                "oldMinusDisposition": 0,
                "minusDisposition": 0,
                "tierPrices": [],
                "tierPricesInclTax": [],
                "includeTax": window.INCLUDE_TAX == '1'
            });

            if (item.type == 'grouped') {
                optionsPrice.productPrice = 0;
                optionsPrice.productOldPrice = 0;
                optionsPrice.priceInclTax = 0;
                optionsPrice.priceExclTax = 0;
            }
            jQuery('#product-option').empty();
            jQuery('#product-option').append('<div class="price-box">Price: <span id="price-excluding-tax-' + itemId + '" class="regular-price giftcard_input_price">' + item.price + '</span><span class="including-tax"> Incl. Tax: </span><span id="price-including-tax-' + itemId + '" class="regular-price giftcard_input_price">' + item.priceInclTax + '</span></div>');
            jQuery('#product-option').append(item.options);
            jQuery('#product-option').append('<div class="action"><button id="cancel" class="button cancel" type="button"><span>Cancel</span></button><button id="ok" class="ok" type="submit" ><span>OK</span></button></div>');
            jQuery('#product-option-form').bPopup({closeClass: 'button'});
            var productCompositeConfigureForm = new varienForm('product-option');

            /*
             * Temporary
             * XPOS 2344
             */
            optionInputElements = document.getElementById('product-option').querySelectorAll('input, select');
            if (optionInputElements[0] != undefined) {
                for (var _i = 0; _i < optionInputElements.length; _i++) {
                    if (optionInputElements[_i].disabled == false && optionInputElements[_i].type != 'hidden') {
                        optionInputElements[_i].focus();
                        break;
                    }
                }
            }

            jQuery("#product-option").unbind();

            if (item.type == 'bundle') {
                ProductConfigure.bundleControl.reloadPrice(); // config price at the first time if user don't select option
            }

            jQuery("#product-option").submit(function (event) {
                if (productCompositeConfigureForm.validate()) {
                    jQuery('#product-option-form').bPopup().close();
                    addConfigurableProduct(itemId);
                    var term = jQuery('#search-box').val();
                    //if (term.length > 0) document.getElementById("search-box").select();
                }
                event.preventDefault();
            });
            if (item.type != 'giftcard')
                optionsPrice.reload();
        } else {
            //add to abstract
            addOrder(itemId, []);

            //add to visual. MUST do add to abstract first
            displayOrder(currentOrder, true);
        }
        if (item.type == 'grouped') {
            groupProductReloadPrice(itemId);
        }
        auto_select_field();
        jQuery('#product-info li').removeClass('active');
        jQuery('#product-' + itemId).parent().parent().addClass('active');

        //console.log("Add to cart " + itemId);

        // refresh scroller
        //initScroll("#items_area");

        var term = jQuery('#search-box').val();
        //if (term.length > 0) document.getElementById("search-box").select();
        if (firstCheckOrder == true) {
            saveOldOrderItem(true);
        } else {
            console.log('after go to save OldOrder');
            console.log(currentOrder);
            saveOldOrderItem(false);
        }
    }
}

//change product bundle
function changeProductBundle(itemId, id_row, child_id) {

    var item = productData[itemId];
    if (item.options) {
        //show config panel
        optionsPrice = new Product.OptionsPrice({
            "productId": itemId,
            "priceFormat": priceFormat,
            "showBothPrices": true,
            "productPrice": item.price,
            "productOldPrice": item.price,
            "priceInclTax": item.price,
            "priceExclTax": item.price,
            "skipCalculate": 0,
            "defaultTax": 8.25,
            "currentTax": 8.25,
            "idSuffix": "_clone",
            "oldPlusDisposition": 0,
            "plusDisposition": 0,
            "plusDispositionTax": 0,
            "oldMinusDisposition": 0,
            "minusDisposition": 0,
            "tierPrices": [],
            "tierPricesInclTax": []
        });
        if (item.type == 'grouped') {
            optionsPrice.productPrice = 0;
            optionsPrice.productOldPrice = 0;
            optionsPrice.priceInclTax = 0;
            optionsPrice.priceExclTax = 0;
        }

        jQuery('#product-option').empty();
        jQuery('#product-option').append('<div class="price-box">Price: <span id="price-excluding-tax-' + itemId + '" class="regular-price">' + item.price + '</span><span class="including-tax"> Incl. Tax: </span><span id="price-including-tax-' + itemId + '" class="regular-price">' + item.price + '</span></div>');
        jQuery('#product-option').append(item.options);
        jQuery('#product-option').append('<div class="action"><button id="cancel" class="button cancel" type="button"><span>Cancel</span></button><button id="ok" class="ok" type="submit" ><span>OK</span></button></div>');

        /**
         * huypq
         * 03/04/2015
         * XPOS-1591 Remember setting for configurable
         */
        if (typeof productData[child_id] != 'undefined') {
            //console.log('product data not found. It ');
            jQuery('#product-option select').each(function (_index, _select) {
                var _attribute_code = jQuery(_select).data('attribute-code');

                [].forEach.call(_select.options, function (_value, _index) {
                    if (_value.value == productData[child_id][_attribute_code]) {
                        _value.setAttribute('selected', true);
                        evt = document.createEvent("HTMLEvents");
                        evt.initEvent("change", false, true);
                        _select.dispatchEvent(evt);
                    } else {
                        _value.removeAttribute('selected');
                    }
                });
            });
        }
        /***************/

        jQuery('#product-option-form').bPopup({closeClass: 'button'});
        var productCompositeConfigureForm = new varienForm('product-option');
        jQuery("#product-option").unbind();
        jQuery("#product-option").submit(function (event) {
            if (productCompositeConfigureForm.validate()) {
                removeFromCat(id_row);
                jQuery('#product-option-form').bPopup().close();
                addConfigurableProduct(itemId);
            }
            event.preventDefault();
        });
        optionsPrice.reload();
    } else {
        addOrder(itemId, []);
        displayOrder(currentOrder, true);
    }
    auto_select_field();
    jQuery('#product-info li').removeClass('active');
    jQuery('#product-' + itemId).parent().parent().addClass('active');
    //console.log("add to cart " + itemId);
    //initScroll("#items_area");
}
//end change product bundle

function addConfigurableProduct(productId) {
    var options = jQuery('#product-option').serializeArray();
    addOrder(productId, options);
    displayOrder(currentOrder, true);
}

/**
 * create order item from itemId,
 * options: auto create new if null
 */
function addOrder(productId, options) {
    /*function call from addToCart for simple product*/
    var product = productData[productId];
    var origProduct = product;

    if (product.type == 'giftcard') {
        if (options[0]['name'] == 'giftcard_amount' || options[0]['name'] == 'custom_giftcard_amount') {
            if (options[0]['value'] != 'custom') {
                productData[productId].price = parseFloat(options[0]['value']);
                productData[productId].priceInclTax = parseFloat(options[0]['value']);
                //productData[productId].finalPrice = parseFloat(options[0]['value']);
            }
            else {
                if (options[1]['name'] == 'custom_giftcard_amount') {
                    productData[productId].price = parseFloat(options[1]['value']);
                    productData[productId].priceInclTax = parseFloat(options[1]['value']);
                    //productData[productId].finalPrice = parseFloat(options[1]['value']);
                }
            }
        }

    }

    //var product = productData[productId];
    var product = origProduct;

    var preparedSku = null;
    var childProductId = null;

    if (product.type == 'configurable') {
        /*
         * Display child products SKU after selecting configurable product options
         */

        filterChildProducts = new Array();

        //init child products
        Object.keys(product.list_bundle).forEach(function (_value, _index) {
            //SHOUD be window.productData. Change later
            filterChildProducts.push(productData[_value]);
        });

        //go through options
        jQuery('#product-option select').each(function (_index, _value) {

            _attributeCode = jQuery(_value).data('attribute-code');
            _attributeValue = jQuery(_value).val();

            filterChildProducts.forEach(function (_value, _index) {
                if (_value !== undefined && _value[_attributeCode] != _attributeValue) {
                    filterChildProducts[_index] = false;
                }
            });

        });

        filterChildProducts.forEach(function (_value, _index) {
            if (_value) {
                preparedSku = "<span style='opacity:.6'>" + product.sku + "</span> &rsaquo; " + _value.sku;
                childProductId = _value.id;
            }
        });
    }

    var oldOrderItem = null;
    //get array of enumerable properties (keys)
    var tempOrder = Object.keys(currentOrder);

    //go though keys
    for (var i = 0; i < tempOrder.length; i++) {
        var tempOrderItem = currentOrder[tempOrder[i]];

        if (tempOrderItem.productId == productId) {
            oldOrderItem = tempOrderItem;

            if (options.length > 0) {
                //check options
                var currentProductOption = oldOrderItem.options;

                if ((!!oldOrderItem.child_product_id && childProductId == oldOrderItem.child_product_id) || currentProductOption.compare(options)) {
                    console.log('same options');
                    oldOrderItem.qty++;

                    //check out of stock
                    //if (product.qty <= 0 && product.type == "simple"){
                    /*remove elert*/
                    if (oldOrderItem.qty > product.qty && product.type == "simple" && false) {
                        jQuery('#alert_box_stt').val('1');
                        alert('Current qty greater than stock qty of this product');
                    }
                    return;
                }

                //check child product
                var selected_child_product = options.first().value;

                if (selected_child_product == oldOrderItem.child_product_id) {
                    oldOrderItem.qty++;

                    //check out of stock
                    //if (product.qty <= 0 && product.type == "simple"){
                    /*remove elert*/
                    if (oldOrderItem.qty > product.qty && product.type == "simple" && false) {
                        jQuery('#alert_box_stt').val('1');
                        alert('Current qty greater than stock qty of this product');
                    }

                    return;
                }
            } else {
                oldOrderItem.qty++;
                //check out of stock
                //if (product.qty <= 0 && product.type == "simple"){
                /*remove elert*/
                if (oldOrderItem.qty > product.qty && product.type == "simple" && false) {
                    jQuery('#alert_box_stt').val('1');
                    alert('Current qty greater than stock qty of this product');
                }
                return;
            }
        }
    }

    /**
     * Create order item
     */
    var newOrderItem = {};

    //set
    newOrderItem.pos = Object.keys(currentOrder).length;
    newOrderItem.id = product.id + '-' + newOrderItem.pos;
    newOrderItem.baseId = product.id;
    newOrderItem.productId = product.id;
    newOrderItem.name = product.name;
    newOrderItem.sku = (!!preparedSku) ? preparedSku : product.sku;
    newOrderItem.child_product_id = childProductId;
    newOrderItem.tax = product.tax;
    newOrderItem.is_qty_decimal = product.is_qty_decimal;
    //set tax
    if (isNaN(newOrderItem.tax)) {
        newOrderItem.tax = 0;
    }
    newOrderItem.finalPrice = product.price;

    //!!??
    newOrderItem.price = product.price;
    newOrderItem.priceInclTax = product.priceInclTax;
    newOrderItem.tax_amount = product.tax_amount;

    /**
     *
     * Issues involve: XPOS - 1746, 2094, 2098
     * This bunch of code intends to retrieve price processed-data from HTML element which is
     * just play for display purpose - not to serve data
     *
     * So think of a hack or quick & dirty - NOT a solution
     * TODO: Review, Remove
     *
     * @temporary action: comment that out.
     * @risk It causes a incorrectly local taxing: double taxing
     *
     *
     */
    //check product is a  giftcard

    if (options.length > 0 && product.type != 'giftcard') {

        var price_excluding_tax = jQuery('#price-excluding-tax-' + product.id).text().replace(priceFormat.groupSymbol, '');
        var price_including_tax = jQuery('#price-including-tax-' + product.id).text().replace(priceFormat.groupSymbol, '');

        if (priceFormat.decimalSymbol == ',') {
            price_excluding_tax = price_excluding_tax.replace(",", ".");
            price_including_tax = price_including_tax.replace(",", ".");
        }

        newOrderItem.price = parseFloat(price_excluding_tax);
        if (product.type != 'bundle')
            newOrderItem.priceInclTax = parseFloat(price_including_tax);
        else
            newOrderItem.priceInclTax = newOrderItem.price;
    }

    if (product.type == 'grouped' && newOrderItem.price == 0) {
        alert('You must select at least one product to add to cart');
        addToCart(productId);
        return;
    }

    //newOrderItem.type="";
    newOrderItem.type = product.type;
    newOrderItem.options = options;
    if (product.type == 'bundle') {
        ProductConfigure.bundleControl.getOption(options, null);
    } else if (product.type == 'configurable') {
        getConfigurableOption(options, config.attributes);
    }

    newOrderItem.qty = 1;
    if (product.qty <= 0 && product.type == 'simple') {
        newOrderItem.out_stock = 1;
    }

    currentOrder[newOrderItem.id] = newOrderItem;
}

function removeFromCat(orderId) {
    var orderItem = currentOrder[orderId];
    orderItem.qty = 0;

    if (orderId.indexOf('-') > 0) {
        iLog('remove Item with index -');
        delete currentOrder[orderId];
        delete tempOldOrderItem[orderId];
    } else {
        iLog('remove Item with Key');
        var i = 0;
        var keyOrderItem = Object.keys(currentOrder);
        var vt = keyOrderItem.indexOf(orderId);
        for (var key in tempOldOrderItem) {
            if (i == vt) {
                if (tempOldOrderItem.hasOwnProperty(key)) {
                    delete tempOldOrderItem[key];
                }
            }
            i++;
        }
    }
    displayOrder(currentOrder, true);
}

function changeQty(orderId) {
    var orderItem = currentOrder[orderId];
    var string = jQuery('#item-qty-' + orderId).val();
    var value = string.replace(',', '.');
    var class_name = jQuery('#item-qty-' + orderId).attr('class');
    if (class_name.indexOf('qty_decimal') == -1)
        var newQty = parseInt(value);
    else
        var newQty = parseFloat(value);
    if (newQty <= 0 || isNaN(newQty)) {
        newQty = orderItem.qty;
        jQuery('#item-qty-' + orderId).val(newQty);
    }
    orderItem.qty = newQty;

    var product = productData[orderItem.productId];
    var current = parseInt(orderItem.qty);
    var stock = parseInt(product.qty);
    if (current > stock && product.type == 'simple') {
        alert('Current quantity greater than stock quantity of this product');
    }
    if (product.type == 'configurable') {
        var out_stock = 0;
        var options = currentOrder[orderId].options;
        var optionQty = product.list_bundle;
        if (options.length > 0) {
            jQuery.each(options, function (i, option) {
                if (newQty > parseFloat(optionQty[option['value']])) {
                    out_stock++;
                    //orderItem.out_stock = 1;
                    if (out_stock == 1) {
                        alert('Current quantity greater than stock quantity of this product');
                        return false;
                    }
                } else {
                    //orderItem.out_stock = 0;
                }
            });
        }
    }
    if (product.type == "bundle") {
        var out_stock = 0;
        var options = currentOrder[orderId].options;
        var optionQty = product.list_bundle;
        if (options.length > 0) {
            jQuery.each(options, function (i, option) {
                if (!option['name'].match('qty')) {
                    if (option['value'] != "") {
                        if (typeof optionQty != 'undefined' && newQty > parseFloat(optionQty[option['value']])) {
                            out_stock++;
                            //orderItem.out_stock = 1;
                            if (out_stock == 1) {
                                alert('Current quantity greater than stock quantity of this product');
                                return false;
                            }
                        } else {
                            //orderItem.out_stock = 0;
                        }

                    }
                }
            });
        }
    }
    displayOrder(currentOrder, true);
}

function changeQtyReload(orderId, qty_saved) {
    var orderItem = currentOrder[orderId];
    var string = qty_saved;
    var value = string.replace(',', '');
    var class_name = jQuery('#item-qty-' + orderId).attr('class');
    if (class_name.indexOf('qty_decimal') == -1)
        var newQty = parseInt(value);
    else
        var newQty = parseFloat(value).toFixed(2);
    if (newQty < 1 || isNaN(newQty)) {
        newQty = orderItem.qty;
        jQuery('#item-qty-' + orderId).val(newQty);
    }
    orderItem.qty = newQty;

    var product = productData[orderItem.productId];
    var current = parseInt(orderItem.qty);
    var stock = parseInt(product.qty);
    displayOrder(currentOrder, true);
}

function changePriceReload(orderId, price) {
    var orderItem = currentOrder[orderId];
    var newPrice = price;
    if (newPrice < 0 || isNaN(newPrice) || !onFlyDiscount) {
        newPrice = orderItem.price;
        jQuery('#item-price-' + orderId).val(newPrice);
        return;
    }

    if (jQuery('#price_includes_tax').val() == 0) {
        orderItem.price = newPrice;
        orderItem.priceInclTax = orderItem.price * (1 + orderItem.tax / 100);
    } else {
        orderItem.priceInclTax = newPrice;
        orderItem.price = Math.round(orderItem.priceInclTax / (1 + orderItem.tax / 100) * 100) / 100;
    }

    orderItem.price_name = 'item[' + orderId + '][custom_price]';
    jQuery('#item-price-' + orderId).val(newPrice);
    displayOrder(currentOrder, true);

}

/**
 * SHOULD BE: changePrice(event, elem) {...}
 * data-order-id
 *
 */
function changePrice(orderId) {
    var orderItem = currentOrder[orderId];
    var string = jQuery('#item-display-price-' + orderId).val();
    var string_convert = string.replace(',', '.');
    var newPrice = parseFloat(string_convert).toFixed(2);

    // jQuery('#item-price-' + orderId).val(jQuery('#item-display-price-' + orderId).val());
    jQuery('#item-price-' + orderId).val(newPrice);
    if (newPrice < 0 || isNaN(newPrice)) {
        newPrice = orderItem.price;
        jQuery('#item-display-price-' + orderId).val(newPrice);
        jQuery('#item-price-' + orderId).val(newPrice);
    }
    var string = jQuery('#item-display-price-' + orderId).val();
    //var newPrice = (jQuery('#item-display-price-' + orderId).val());
    newPrice = string.replace(',', '');

    var split = newPrice.split("%");
    if (split.length == 2) {
        var value = parseFloat(split[0]);
        if (value < 0 || isNaN(value) || !onFlyDiscount || value > 100) {
            value = orderItem.price;
            jQuery('#item-price-' + orderId).val(value);
            return;
        }
        //var percent = value%orderItem.price;
        var newValue = (100 - parseFloat(value)) * 0.01 * orderItem.price;
        jQuery('#item-price-' + orderId).val(newValue);
        if (jQuery('#price_includes_tax').val() == 0) {
            orderItem.price = newValue;
            orderItem.priceInclTax = orderItem.price * (1 + orderItem.tax / 100);
        } else {
            orderItem.priceInclTax = newValue;
            orderItem.price = Math.round(orderItem.priceInclTax / (1 + orderItem.tax / 100) * 100) / 100;
        }
        orderItem.custom_price = newValue;
        orderItem.price_name = 'item[' + orderId + '][custom_price]';

        displayOrder(currentOrder, true);
        return;
    } else {
        var newPrice = parseFloat(jQuery('#item-price-' + orderId).val());
        if (newPrice < 0 || isNaN(newPrice) || !onFlyDiscount) {
            newPrice = orderItem.price;
            jQuery('#item-price-' + orderId).val(newPrice);
            return;
        }

        if (jQuery('#price_includes_tax').val() == 0) {
            orderItem.price = newPrice;
            orderItem.priceInclTax = orderItem.price * (1 + orderItem.tax / 100);
        } else {
            orderItem.priceInclTax = newPrice;
            orderItem.price = Math.round(orderItem.priceInclTax / (1 + orderItem.tax / 100) * 100) / 100;
        }
        orderItem.custom_price = newPrice;
        orderItem.price_name = 'item[' + orderId + '][custom_price]';
        displayOrder(currentOrder, true);
    }

//    orderItem.price = newPrice;
//    orderItem.priceInclTax = orderItem.price * (1 + orderItem.tax / 100);
//    orderItem.price_name = 'item['+orderId+'][custom_price]';
//    displayOrder(currentOrder, true);
}

function number_format(number, decimals, dec_point, thousands_sep) {

    // http://kevin.vanzonneveld.net
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://getsprink.com)
    // +     bugfix by: Benjamin Lupton
    // +     bugfix by: Allan Jensen (http://www.winternet.no)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +     bugfix by: Howard Yeend
    // +    revised by: Luke Smith (http://lucassmith.name)
    // +     bugfix by: Diogo Resende
    // +     bugfix by: Rival
    // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
    // +   improved by: davook
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Jay Klehr
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Amir Habibi (http://www.residence-mixte.com/)
    // +     bugfix by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // +   improved by: Drew Noakes
    // *     example 1: number_format(1234.56);
    // *     returns 1: '1,235'
    // *     example 2: number_format(1234.56, 2, ',', ' ');
    // *     returns 2: '1 234,56'
    // *     example 3: number_format(1234.5678, 2, '.', '');
    // *     returns 3: '1234.57'
    // *     example 4: number_format(67, 2, ',', '.');
    // *     returns 4: '67,00'
    // *     example 5: number_format(1000);
    // *     returns 5: '1,000'
    // *     example 6: number_format(67.311, 2);
    // *     returns 6: '67.31'
    // *     example 7: number_format(1000.55, 1);
    // *     returns 7: '1,000.6'
    // *     example 8: number_format(67000, 5, ',', '.');
    // *     returns 8: '67.000,00000'
    // *     example 9: number_format(0.9, 0);
    // *     returns 9: '1'
    // *    example 10: number_format('1.20', 2);
    // *    returns 10: '1.20'
    // *    example 11: number_format('1.20', 4);
    // *    returns 11: '1.2000'
    // *    example 12: number_format('1.2000', 3);
    // *    returns 12: '1.200'
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        toFixedFix = function (n, prec) {
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            var k = Math.pow(10, prec);
            return Math.round(n * k) / k;
        },
        s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

var orderTemplate =
    '<tr class="hover {class_item}-{id}" {style}>'
    + '<td>'
    + '<div class="item-first">'
    + '<a href="#" class="remove" onclick="removeFromCat(\'{id}\')"></a><h5 class="hover1 {config_change}-{id}" data-child-product-id={child_product_id}>{name}</h5>'
    + '<h6 class="sku-text">{sku}</h6>'
    + '{option}'
    + '{out_stock}</div></td><td class="qty align-center"><input id="item-qty-{id}" maxlength="12" value="{qty}" data-item-base-id="{baseId}" data-item-id="{id}" class="checkout-item-qty input-text item-qty {change_qty} {is_qty_decimal}" name="item[{id}][qty]" onclick="check_edit_product(\'{id}\')" onchange="changeQty(\'{id}\')"></td>'
    /*+ '<td class="tax align-center">{tax}%</td>'*/
    + '<td class="statetax align-center">{statetax}</td>'
    + '<td class="countytax align-center">{countytax}</td>'
    + '<td class="citytax align-center">{citytax}</td>'
    + '<td class="price align-right"><input onclick="check_edit_product(\'{id}\')" id="item-display-price-{id}" maxlength="12" value="{price_display}" class="item-price input-text {edit_price} {change_qty} " onkeydown="enterToChange(this,event);" onchange="changePrice(\'{id}\')">  <input id="item-price-{id}" class="item-original-price item-price {edit_price}" name="{price_name}" type="hidden" value="{price}" /> </td><td class="subtotal align-right"><div class="subtotal-list">{subtotal}<span></span>'
    + '</div>'
    + '</td>'
    + '</tr>';
var noDiscountTemplate =
    '<tr id="cartitem_{proid}" class="hover {class_item}" {style}>'
    + '<td><div class="item-first">'
    + '<a href="#" class="remove" onclick="removeFromCat(\'{id}\')"></a><h5 class="hover1 {config_change}-{id}" data-child-product-id={child_product_id}>{name}</h5>'
    + '<h6 class="sku-text">{sku}</h6>'
    + '{option}</div></td><td class="qty align-center"><input id="item-qty-{id}" maxlength="12" value="{qty}" data-item-base-id="{baseId}" data-item-id="{id}" class="checkout-item-qty input-text item-qty {is_qty_decimal}" name="item[{id}][qty]" onchange="changeQty(\'{id}\')"></td>'
    /*+ '<td class="tax align-center">{tax}%</td>'*/
    +'<td class="statetax align-center">{statetax}</td>'
    +'<td class="countytax align-center">{countytax}</td>'
    +'<td class="citytax align-center">{citytax}</td>'
    + '<td class="price align-right"><span class="no-change">{price}</span></td><td class="subtotal align-right"><div class="subtotal-list">{subtotal}<span></span></div></td>'
    + '</tr>';
/**
 * Author here?
 *
 * Add to visual
 *
 * @param order
 * @param updatePrice
 * @return
 */
function displayOrder(order, updatePrice) {
    //versobe console
    //console.log('---- Displaying order...');

    jQuery('#order-items_grid table tbody').empty();
    var itemNumber = parseFloat(0);
    var subtotal = parseFloat(0);
    var subtotalInclTax = parseFloat(0);
    var totalTax = parseFloat(0);

    //retrieve tax
    var tax = parseFloat(0);

    jQuery.each(order, function (i, orderItem) {
        //calc
        orderItem.subtotal = orderItem.price * orderItem.qty;

        ////console.log(orderItem);
        if (orderItem.type == 'giftcard')
            orderItem.subtotalInclTax = orderItem.subtotal;
        else
            orderItem.subtotalInclTax = orderItem.priceInclTax * orderItem.qty;

        var orderOption = '';
        var edit_price = '';
        var change_qty = '';
        var out_stock = '';
        var is_qty_decimal = '';

        var limit = jQuery('#is_user_limited').val();
        if (limit == 0) edit_price = 'edit_price';
        else edit_price = '';
        var class_item = orderItem.class_item;
        var config_change = orderItem.config_change;

        if (orderItem.options.length > 0) {
            jQuery.each(orderItem.options, function (i, option) {
                var optionName = option.name.replace('[', '][');
                optionName = optionName.lastIndexOf(']') == (optionName.length - 1) ? optionName : optionName + ']';
                orderOption += '<input type="hidden" name="item[' + orderItem.id + '][' + optionName + '" value="' + option.value + '">';
                //optionInput+='<span class="option-title">'+option.optionTitle+': </span><span class="option-name">'+option.qty+'x '+option.title+'</span></li>';
                if (!!option.value && option.value in productData) {
                    var product_id = option.value;
                    var product = productData[product_id];
                    if (product.qty <= 0 && product.type == 'simple') {
                        orderItem.out_stock = 1;
                    }
                }
            });
            config_change = "config_change item_config item_config-id";
            class_item = "config item_config item_config-id";
            if (orderItem.type == 'grouped')
                change_qty = 'grouped';
        }

        if (orderItem.type == 'configurable' && orderItem.child_product_id != null) {
            /** XPOS-1888: sometime do NOT display "Out of Stock" properly
             * 07/04/2015
             */
            orderItem.out_stock = 0;
            var product_id = orderItem.child_product_id;
            var product = productData[product_id];
            if (!!product && product.qty <= 0 && product.type == 'simple') {
                orderItem.out_stock = 1;
            }
        }


        if (orderItem.out_stock == 1) {
            var out_stock = '<h6 style="height: 15px; line-height: 15px; padding-top:5px; color: red; font-size:.8em">Out of Stock</h6>';
        }
        if (orderItem.is_qty_decimal == 1) {
            is_qty_decimal = 'qty_decimal';
        }
        //orderOption+='</ul>';
        var tempOrderItem = {};
        if (orderItem.qty == 0) {
            tempOrderItem.style = 'style="display:none"';
        }
        tempOrderItem.id = orderItem.id;
        tempOrderItem.proid = orderItem.productId;
        tempOrderItem.child_product_id = orderItem.child_product_id;
        tempOrderItem.baseId = orderItem.baseId;
        tempOrderItem.name = orderItem.name;
        //issue XPOS-1783
        tempOrderItem.sku = orderItem.sku;
        tempOrderItem.option = orderOption;
        tempOrderItem.qty = orderItem.qty;
        tempOrderItem.tax = orderItem.tax;
        
        //Develop by Metizsoft Solution
        tempOrderItem.statetax = 0;
        tempOrderItem.countytax = 0;
        tempOrderItem.citytax = 0;
        ////////////////////////
        
        tempOrderItem.subtotal = formatCurrency(orderItem.subtotal, priceFormat);
        tempOrderItem.class_item = class_item;
        tempOrderItem.price_name = orderItem.price_name;
        tempOrderItem.config_change = config_change;
        tempOrderItem.edit_price = edit_price;
        tempOrderItem.change_qty = change_qty;
        tempOrderItem.out_stock = out_stock;
        tempOrderItem.is_qty_decimal = is_qty_decimal;
        tempOrderItem.isNotChangePrice = 0;
        if (orderItem.type == 'bundle') {
            tempOrderItem.isNotChangePrice = 1;
        }
        if (displayTaxInSubtotal) {
            tempOrderItem.subtotal = formatCurrency(orderItem.subtotalInclTax, priceFormat);
        } else {
            tempOrderItem.subtotal = formatCurrency(orderItem.subtotal, priceFormat);
        }

        var orderItemOutput = '';
        if (onFlyDiscount) {
            if (jQuery("#price_includes_tax").val() == 1) {
                tempOrderItem.price = orderItem.priceInclTax;
            } else {
                tempOrderItem.price = orderItem.price;
            }
            if (!!orderItem.custom_price) {
                tempOrderItem.price = orderItem.custom_price;
            }
            if (displayTaxInShoppingCart) {
                tempOrderItem.price_display = formatCurrency(orderItem.priceInclTax, priceFormat);
            } else {
                tempOrderItem.price_display = formatCurrency(orderItem.price, priceFormat);
            }
            if (priceFormat.decimalSymbol == ',' && tempOrderItem.price.toString().indexOf(',') !== -1) {
                tempOrderItem.price = formatCurrency(parseFloat(tempOrderItem.price.toString().replace(',', '.')), priceFormat);
            }
            orderItemOutput = nano(orderTemplate, tempOrderItem);
        } else {
            if (displayTaxInShoppingCart) {
                tempOrderItem.price = formatCurrency(orderItem.priceInclTax, priceFormat);
            } else {
                tempOrderItem.price = formatCurrency(orderItem.price, priceFormat);
            }

            orderItemOutput = nano(noDiscountTemplate, tempOrderItem);
        }

        /** XPOS-2006
         *
         */
        /*
         if (orderItem.type == 'bundle' && !!orderItem.bundle_children) {
         if (orderItem.qty > 0) {
         var _html = '';
         orderItem.bundle_children.forEach(function(_value, _index) {
         _value = parseInt(_value);
         _childProduct = productData[_index];

         _html += '<tr>';
         _html += '<td><div class="item-first"><h5>'+_childProduct.name + '</h5><h6 class="sku-text">' +_childProduct.sku+ '</h6></div></td>';
         _html += '<td class="qty align-center">' + _value + '</td>';
         _html += '<td></td>';
         _html += '<td class="price align-right"><input value="'+ _childProduct.price +'" disabled="disabled" /></td>';
         _html += '</tr>';

         });
         orderItemOutput += _html;
         }
         }
         */


        jQuery('#order-items_grid table tbody').append(orderItemOutput);
        if (tempOrderItem.isNotChangePrice == 1) {
            //console.log('bundle id = ' + tempOrderItem.id);
            var PriceIdBundle = 'item-display-price-' + tempOrderItem.id;
            //console.log('id bundle price = "' + PriceIdBundle+'"');
            disableElementById(PriceIdBundle);
        }
        itemNumber += parseFloat(orderItem.qty);
        subtotal += parseFloat(orderItem.subtotal);
        subtotalInclTax += parseFloat(orderItem.subtotalInclTax);
        if (orderItem.qty > 0) {
            totalTax += parseFloat(orderItem.tax * 0.01 * orderItem.price * orderItem.qty);
        }
    });

    //var tax = subtotalInclTax - subtotal;
    var tax = totalTax;
    jQuery('#item_count_value').text(itemNumber);
    if (updatePrice) {
        if (displayTaxInSubtotal) {
            jQuery('#subtotal_value').text(formatCurrency(subtotalInclTax.toFixed(2), priceFormat));
        } else {
            jQuery('#subtotal_value').text(formatCurrency(subtotal.toFixed(2), priceFormat));
        }
        if (isNumber(tax)) {
            jQuery('#tax_value').text(formatCurrency(tax, priceFormat));
        } else {
            totalTax = 0;
            jQuery('#tax_value').text(formatCurrency(tax, priceFormat));
        }

        if (subtotalInclTax == 0) {
            removeDiscount();
        }

        var discount = Math.abs(jQuery('#discount_value').val());
        // if(displayTaxInGrandTotal)
        var grandTotal = subtotalInclTax > discount ? subtotalInclTax - discount : 0;
        //  else grandTotal = subtotal - discount;
        jQuery('#grandtotal').text(formatCurrency(grandTotal.toFixed(2), priceFormat));
        jQuery('#grand_before').val(formatCurrency(grandTotal.toFixed(2), priceFormat));
        jQuery('#cash-in').val(grandTotal.toFixed(2));
    }
    
    /////////////////// Develop by Metizsoft Solution ///////////////////
    setTimeout(function () {
        subtotal = 0;
        jQuery.each(order, function (i, orderItem) {
            jQuery('#order-items_grid table tbody tr#cartitem_'+orderItem.productId).find('.statetax').text(0);
            jQuery('#order-items_grid table tbody tr#cartitem_'+orderItem.productId).find('.countytax').text(0);
            jQuery('#order-items_grid table tbody tr#cartitem_'+orderItem.productId).find('.citytax').text(0);
            
            reloadurl = jQuery('#jemswrite').val();
            var state = jQuery('#order-shipping_address_address_region_id').val();
            var postcode = jQuery('#order-shipping_address_address_postcode').val();
            var city = jQuery('#order-shipping_address_address_city').val();
            var ooca = jQuery('#order-shipping_address_address_outofcityarea').val();
            
            if(state != '' && postcode != '' && city != ''){
                new Ajax.Request(reloadurl, {
                    method: 'get',
                    parameters: {
                        itemid: orderItem.productId,
                        state: state,
                        zipcode: postcode,
                        city: city,
                        ooca:ooca
                    },
                    onComplete: function(stateform) {
                        
                        /************** Set item state tax, county tax and city tax ***********/
                        retjson = stateform.responseText.evalJSON();
                        jQuery('#order-items_grid table tbody tr#cartitem_'+orderItem.productId).find('.statetax').text(retjson.statetax);
                        jQuery('#order-items_grid table tbody tr#cartitem_'+orderItem.productId).find('.countytax').text(retjson.countytax);
                        jQuery('#order-items_grid table tbody tr#cartitem_'+orderItem.productId).find('.citytax').text(retjson.citytax);
                        /*************************/
                        
                        /************** Set row total ***********/
                        var rowtotal = (orderItem.qty*(orderItem.price+retjson.statetax+retjson.countytax+retjson.citytax)).toFixed(2);
                        jQuery('#order-items_grid table tbody tr#cartitem_'+orderItem.productId).find('.subtotal .subtotal-list').text(rowtotal);
                        /*************************/
                        
                        /************** Set Sub Total Value ***********/
                        subtotal = subtotal+(orderItem.qty*(orderItem.price+retjson.statetax+retjson.countytax+retjson.citytax));
                        newpr = subtotal.toFixed(2)
                        jQuery('#summary-items #subtotal_value').text(newpr);
                        /*************************/
                        
                    }
                });
            }
        });
        
    }, 500);
    ////////////////////////
    
    setTimeout(function () {
        jQuery('#items_area').getNiceScroll().doScrollPos(0, 100000);
    }, 500);
    jQuery('#items_area').getNiceScroll().doScrollPos(0, 100000);

    function removeDiscount() {
        jQuery('#discount_value').val(0);
        jQuery('#order_discount').val(0);
        jQuery('#discount_change').val(1);
    }
}

/**
 *
 */

function disableElementById(id) {
    jQuery('#' + id).prop('disabled', true);
}
function check_edit_product(orderId) {
    3
    var class_object = jQuery('#item-qty-' + orderId).attr('class');

    if (class_object.indexOf('grouped') > -1) {
        jQuery('#item-qty-' + orderId).val(1.00);
        jQuery('#item-qty-' + orderId).attr('readonly', true);
        jQuery('#item-display-price-' + orderId).attr('readonly', true);
        return;
    }
}
function change_giftcard_amount() {
    //Giftcard input price detect
    var price_select = jQuery('#giftcard_amount').val();
    if (price_select != 'custom')
        jQuery('.giftcard_input_price').html(price_select);
}

function set_custom_giftcard_amount() {
    var value = parseFloat(jQuery('#giftcard_amount_input').val()).toFixed(2);
    if (isNumber(value))
        jQuery('.giftcard_input_price').html(value);
}

function sortObject(obj) {
    var arr = [];
    var new_object = {};
    for (var prop in obj) {
        if (obj.hasOwnProperty(prop)) {
            arr.push({
                'key': prop,
                'sort': obj[prop].pos,
                'value': obj[prop]
            });
        }
    }
    //arr.sort(function(a, b) { return a.sort - b.sort; });
    arr.sort(function (a, b) {
        return b.sort - a.sort;
    });
    for (var i = 0; i < arr.length; i++) {
        new_object[arr[i].sort] = arr[i].value;
    }
    return new_object; // returns array
}

Array.prototype.compare = function (array) {
    // if the other array is a falsy value, return
    if (!array)
        return false;

    // compare lengths - can save a lot of time
    if (this.length != array.length)
        return false;
    for (var i = 0; i < this.length; i++) {
        var a = this[i];
        var b = array[i];
        if (a.name != b.name || a.value != b.value) {
            return false;
        }
    }
    //return JSON.stringify(this) == JSON.stringify(array);
    return true;
}

Product.Options = Class.create();
Product.Options.prototype = {
    initialize: function (config) {
        this.config = config;
        this.reloadPrice();
        document.observe("dom:loaded", this.reloadPrice.bind(this));
    },
    reloadPrice: function () {
        var config = this.config;
        var skipIds = [];
        $$('body .product-custom-option').each(function (element) {
            var optionId = 0;
            element.name.sub(/[0-9]+/, function (match) {
                optionId = parseInt(match[0], 10);
            });
            if (config[optionId]) {
                var configOptions = config[optionId];
                var curConfig = {price: 0};
                if (element.type == 'checkbox' || element.type == 'radio') {
                    if (element.checked) {
                        if (typeof configOptions[element.getValue()] != 'undefined') {
                            curConfig = configOptions[element.getValue()];
                        }
                    }
                } else if (element.hasClassName('datetime-picker') && !skipIds.include(optionId)) {
                    dateSelected = true;
                    $$('.product-custom-option[id^="options_' + optionId + '"]').each(function (dt) {
                        if (dt.getValue() == '') {
                            dateSelected = false;
                        }
                    });
                    if (dateSelected) {
                        curConfig = configOptions;
                        skipIds[optionId] = optionId;
                    }
                } else if (element.type == 'select-one' || element.type == 'select-multiple') {
                    if ('options' in element) {
                        $A(element.options).each(function (selectOption) {
                            if ('selected' in selectOption && selectOption.selected) {
                                if (typeof(configOptions[selectOption.value]) != 'undefined') {
                                    curConfig = configOptions[selectOption.value];
                                }
                            }
                        });
                    }
                } else {
                    if (element.getValue().strip() != '') {
                        curConfig = configOptions;
                    }
                }
                if (element.type == 'select-multiple' && ('options' in element)) {
                    $A(element.options).each(function (selectOption) {
                        if (('selected' in selectOption) && typeof(configOptions[selectOption.value]) != 'undefined') {
                            if (selectOption.selected) {
                                curConfig = configOptions[selectOption.value];
                            } else {
                                curConfig = {price: 0};
                            }
                            optionsPrice.addCustomPrices(optionId + '-' + selectOption.value, curConfig);
                            optionsPrice.reload();
                        }
                    });
                } else {
                    optionsPrice.addCustomPrices(element.id || optionId, curConfig);
                    optionsPrice.reload();
                }
            }
        });
    }
};

var DateOption = Class.create({

    getDaysInMonth: function (month, year) {
        var curDate = new Date();
        if (!month) {
            month = curDate.getMonth();
        }
        if (2 == month && !year) { // leap year assumption for unknown year
            return 29;
        }
        if (!year) {
            year = curDate.getFullYear();
        }
        return 32 - new Date(year, month - 1, 32).getDate();
    },

    reloadMonth: function (event) {
        var selectEl = event.findElement();
        var idParts = selectEl.id.split("_");
        if (idParts.length != 3) {
            return false;
        }
        var optionIdPrefix = idParts[0] + "_" + idParts[1];
        var month = parseInt($(optionIdPrefix + "_month").value);
        var year = parseInt($(optionIdPrefix + "_year").value);
        var dayEl = $(optionIdPrefix + "_day");

        var days = this.getDaysInMonth(month, year);

        //remove days
        for (var i = dayEl.options.length - 1; i >= 0; i--) {
            if (dayEl.options[i].value > days) {
                dayEl.remove(dayEl.options[i].index);
            }
        }

        // add days
        var lastDay = parseInt(dayEl.options[dayEl.options.length - 1].value);
        for (i = lastDay + 1; i <= days; i++) {
            this.addOption(dayEl, i, i);
        }
    },

    addOption: function (select, text, value) {
        var option = document.createElement('OPTION');
        option.value = value;
        option.text = text;

        if (select.options.add) {
            select.options.add(option);
        } else {
            select.appendChild(option);
        }
    }
});

var validateOptionsCallback = function (elmId, result) {
    var container = $(elmId).up('ul.options-list');
    if (!container) {
        return;
    }
    if (result == 'failed') {
        container.removeClassName('validation-passed');
        container.addClassName('validation-failed');
    } else {
        container.removeClassName('validation-failed');
        container.addClassName('validation-passed');
    }
}
