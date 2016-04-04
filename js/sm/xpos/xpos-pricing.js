/**
 * Created by Huy on 6/22/2015.
 */
;function isSelectedPaymentMethod() {
    return jQuery('.payment-method-item.active').length > 0;
}
function setDefaultPaymentMethod() {
    var defaultMethod = 'sm_store_credit';
    /*setSelectedPayment(defaultMethod);
    select_payment_method(defaultMethod);*/
    payment.switchMethod(defaultMethod);
}
(function (window, undefined, $) {
    function XPOSPrice(product) {
        this.product = product;
        this._init();
    }

    XPOSPrice.prototype = {
        /*
         * Initialization
         */
        _init: function () {
            this._prepareIncludeTax();
            this._prepareTaxPercent();
            this._preparePrice();
        },

        /*
         * Prepare if whether the current price is included tax
         */
        _prepareIncludeTax: function () {
          return this.includeTax = !!window.INCLUDE_TAX;
        },

        /*
         * Prepare tax percentage
         */
        _prepareTaxPercent: function () {
            this.taxPercent = window.parseFloat(this.product.tax);
            return this.taxPercent = window.isNaN(this.taxPercent) ? 0 : this.taxPercent;
        },

        /*
         * Prepare prices
         */
        _preparePrice: function () {
            this.price          = window.parseFloat(this.product.price);
            this.finalPrice     = window.parseFloat(this.product.finalPrice);
            this.productPrice   = (this.includeTax) ? (this.finalPrice) / (1 + this.taxPercent / 100) : this.finalPrice;
            this.oldPrice       = (this.includeTax) ? (this.price) / (1 + this.taxPercent / 100) : this.price;
            //this.regularPrice   = (this.includeTax) ? (this.finalPrice) / (1 + this.taxPercent / 100) : this.finalPrice;
            this.priceInclTax   = (this.includeTax) ? this.finalPrice : this.finalPrice * (1 + this.taxPercent / 100);
            this.priceExclTax   = (this.includeTax) ? (this.finalPrice) / (1 + this.taxPercent / 100) : this.finalPrice;
        },

        /*
         * Tax amount calculation
         * @input price, includeTax, taxPercent
         * @param
         * @return float: tax amount
         */
        calcTaxAmount: function (priceToCalculate, includeTax, taxPercent) {
            var _p = (typeof(priceToCalculate) !== 'undefined') ? priceToCalculate : this.finalPrice;
            var _i = (typeof(includeTax) !== 'undefined') ? includeTax : this.includeTax;
            var _t = (typeof(taxPercent) !== 'undefined') ? taxPercent : this.taxPercent / 100;

            return this.taxAmount = (_i) ? ((_p * _t) / (1 + _t)) : (_p * _t);
        },

        /*
         * Populate price from product being added to order item
         * @param Object orderItem
         * @param Object productBeingAdded
         */
        populatePrice: function (orderItem, productBeingAdded) {
            orderItem.taxPercent    = this.taxPercent;
            orderItem.taxAmount     = this.calcTaxAmount();
            orderItem.price         = this.price;
            //orderItem.regularPrice  = this.regularPrice;
            orderItem.priceInclTax = this.priceInclTax;
            orderItem.priceExclTax = this.priceExclTax;
            orderItem.finalPrice    = this.finalPrice;
            orderItem.includeTax  = this.includeTax;

            if (!!productBeingAdded && productBeingAdded.selectedOptions.length > 0) {
                /**
                 *
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
                if (!!window.XPOSCartObject && productBeingAdded.type != window.XPOSCartObject.PRODUCT_ITEM_TYPE_GIFTCARD) {
                    var price_excluding_tax = jQuery('#price-excluding-tax-' + productBeingAdded.id).text().replace(window.PRICE_FORMAT.groupSymbol, '');
                    var price_including_tax = jQuery('#price-including-tax-' + productBeingAdded.id).text().replace(window.PRICE_FORMAT.groupSymbol, '');

                    if(window.PRICE_FORMAT.decimalSymbol == ','){
                        price_excluding_tax = price_excluding_tax.replace(",", ".");
                        price_including_tax = price_including_tax.replace(",", ".");
                    }

                    if(productBeingAdded.type != 'bundle') {
                        orderItem.priceExclTax = parseFloat(price_excluding_tax);
                        orderItem.priceInclTax = parseFloat(price_including_tax);
                        orderItem.taxAmount     = this.calcTaxAmount(orderItem.priceExclTax, includeTax = false);
                    }
                }
            }
        },

        /*
         * Get product with all price setting
         */
        getProductItem: function() {
            this.product.tax_amount = this.calcTaxAmount();
            this.product.productPrice = this.productPrice;
            this.product.oldPrice = this.oldPrice;
            this.product.priceInclTax = this.priceInclTax;
            this.product.priceExclTax = this.priceExclTax;
            this.product.includeTax = window.INCLUDE_TAX;

            return this.product;
        }
    };

    /*
     * Subtotal
     */
    function XPOSTotal (orderItem) {
        this.orderItem = orderItem;
        this._init();
    }

    XPOSTotal.prototype = {
        /*
         * Initialization
         */
        _init: function () {
            this.PRICE_FORMAT = window.PRICE_FORMAT;
        },
        /*
         * Subtotal calculating
         */
        calcSubtotal: function () {
            var _q = window.parseFloat(this.orderItem.qty);
            var _p = window.parseFloat(this.orderItem.priceExclTax);
            var _t = window.parseFloat(this.orderItem.taxAmount);

            var _subtotal;

            if (window.SHOPPING_CART_SUBTOTAL_DISPLAY_TYPE != 1) {
                _subtotal = _q * (_p + _t);
            } else {
                _subtotal = _p * _q;
            }

            this.orderItem.subtotal = _subtotal;

            return this.orderItem;
        }

    };

    var SMStoreCredit = {
        code: 'sm_store_credit'
    };
    //function _getCurrentDue() {
    //    return parseFloat($('#hidden_sm_store_credit_pay_previous_value').val() || 0);
    //}
    //function _getCurrentPUse() {
    //    return parseFloat($('#sm_store_credit_input_price').val() || 0);
    //}
    function _getPaidPrevious() {
        if ($('#sm_store_credit_pay_previous_checkbox').prop('checked')) {
            return parseFloat($('#sm_store_credit_pay_previous_value').val() || 0);
        }
        return 0;
    }

    function _getCurrentPay() {
        var cash_in = 0;
        var payment_method = jQuery("#payment_method_hidden").val();
        if (!payment_method) {
            return 0;
        }
        if (!window.useSplit) {
            if (isSelectedPaymentMethod())
                cash_in = parseFloat(jQuery("#cash-in").val());
                if ($('.payment-method-item.active').length === 1 && $('.payment-method-item.active').attr('id') === 'p_method_sm_store_credit') {
                    var currentCredit = SMStoreCredit.getAvailableStoreCredit();
                    if (cash_in > currentCredit) {
                        cash_in = currentCredit;
                    }
                }
        } else {
            cash_in = parseFloat(getSplitAmount());
        }
        return cash_in;
    }

    function _getGrandTotal() {
        return parseFloat(jQuery("#order_grandtotal").val());
    }

    function _getGrandTotalWithoutDue() {
        return parseFloat(jQuery("#grand_total_without_due").val());
    }
    SMStoreCredit.getAvailableStoreCredit = function () {
        return parseFloat(jQuery('.currentStoreCredit').html()).toFixed(2);
    };

    SMStoreCredit.updateDue = function () {
        var grandTotal = _getGrandTotal();
        var currentPay = _getCurrentPay();
        currentPay = grandTotal > currentPay ? currentPay : grandTotal;
        var customerDue = grandTotal - currentPay;
        if (customerDue < 0) customerDue = 0;
        $('#next_customer_due_balance').html(customerDue.toFixed(2));
    };

    SMStoreCredit.updateGrandTotalWithoutDue = function() {
        var grandTotal = _getGrandTotal();
        $('#grand_total_without_due').val(grandTotal);
    };

    SMStoreCredit.updateGrandTotalWithDue = function() {
        var payDuePrevious = _getPaidPrevious();
        var grandTotal = _getGrandTotalWithoutDue();
        if (payDuePrevious > 0) {
            grandTotal = (grandTotal + payDuePrevious).toFixed(2);
            jQuery("#order_grandtotal").val(grandTotal);
            jQuery("#grandtotal").text(grandTotal, priceFormat);
        } else {
            jQuery("#order_grandtotal").val(grandTotal);
            jQuery("#grandtotal").text(grandTotal, priceFormat);
        }
        updateBalance();
    };

    SMStoreCredit.bindRealTimeChange = function() {
        $('input.amountSplit').bind('keyup mouseup', function() {
            SMStoreCredit.updateDue();
        });
    };

    //add to global
    //window.TAX_CALCULATING_LOGIC = '0';
    window.XPOSPrice = XPOSPrice;
    window.XPOSTotal = XPOSTotal;
    window.SMStoreCredit = SMStoreCredit;
})(window, undefined, jQuery);


