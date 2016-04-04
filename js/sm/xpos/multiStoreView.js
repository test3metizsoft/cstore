/**
 * Created by vjcspy on 6/26/15.
 */
var MultiStoreView = function () {
};
MultiStoreView.prototype = {
    currentStoreId: false,
    currentStoreName: '',
    priceFormat: null,
    storeDataProOp: null,
    initialize: function (data) {
        if (!data) {
            data = {};
        } else {
            this.currentStoreId = data.id;
            this.currentStoreName = data.store_name
        }
    },

    setData: function (storeId, currentStoreName) {
        this.currentStoreId = storeId;
        this.currentStoreName = currentStoreName;
    },
    setPriceFormat: function (priceFormat) {
        "use strict";
        this.priceFormat = priceFormat;
    },
    getCurrentStoreId: function () {
        return this.currentStoreId;
    },
    setStoreViewToServer: function (storeId) {
        this.currentStoreId = storeId;
        var url1 = changeStoreViewUrl + "?storeId=" + storeId;
        var url = url1.replace("?___SID=U", "");
        jQuery.getJSON(url, function (json) {
            var dataFromServer = json;
            if (dataFromServer.status != 'true') {
                iLog('set Store Session Failed', null, 5);
            } else {
                iLog('set Store view OK');
            }
        })
    },
    getStoreViewFromServer: function () {
        var url1 = getStoreViewUrl + "?storeId=" + storeId;
        var url = url1.replace("?___SID=U", "");
        jQuery.getJSON(url, function (json) {
            var dataFromServer = json;
            this.currentStoreId = dataFromServer.storeId;
            return this.currentStoreId;
        });
        return this.currentStoreId;
    },
    changeStoreViewXpos: function (store) {
        /*
         * Khi thay doi StoreView in XPos can phai work:
         * setStoreViewToServer
         * goi ham Clear Cache thi se tu dong:
         *                          - load lai product(neu su dung search offline(2**********cai))
         *                          - load lai customer(neu su dung offline)
         * */
        if (true || store.id != this.currentStoreId) { // Ly do de true la co tinh luon reload lai khi thay doi store.
            var url1 = changeStoreViewUrl + "?storeId=" + store.id;
            var url = url1.replace("?___SID=U", "");
            jQuery.getJSON(url, function (json) {
                var dataFromServer = json;
                if (dataFromServer.status != 'true') {
                    iLog('set Store Session Failed', null, 5);
                } else {
                    hideLoadingMask();
                    iLog('set Store view OK');
                    var isLog = $.jStorage.get('enable_status', 'false');
                    var login = $.jStorage.get('xpos_user', false);
                    $.jStorage.flush();
                    $.jStorage.set('enable_status', isLog);
                    $.jStorage.set('xpos_store', store);
                    $.jStorage.set('xpos_user', login);
                    this.priceFormat = json.priceFormat;
                    if (typeof(Storage) !== "undefined") {
                        //localStorage.setItem('dataProOp', null);
                    }
                    varienGlobalEvents.fireEvent('XPOS_SET_STORE_SUCCESS');
                }
            });

            //varienGlobalEvents.fireEvent('XPOS_SET_STORE');
        }
        //jQuery('#loading-mask').hide();
    },
    clearCacheBrower: function (store) {
        $.jStorage.flush();
        $.jStorage.set('xpos_store', store);
        if (typeof(Storage) !== "undefined") {
            localStorage.setItem('dataProOp', null);
        }
        location.reload(true);
    },

    getDataProOp: function () {
        "use strict";
        window.isBrSpSt = false;
        window.dataProOp = {};
        var storeDataProOp = 'dataProOp' + window.multiStoreView.currentStoreId;
        this.storeDataProOp = storeDataProOp;
        window.isBrSpSt = checkBrower(storeDataProOp);
        function checkBrower(storeDataProOp) {
            if (typeof(Storage) !== "undefined") {
                iLog('Br Support and set to Storage');
                if (localStorage.getItem(storeDataProOp) != 'null' && localStorage.getItem(storeDataProOp) != null) {
                    iLog('dataProOp existed. Will set to var');
                    window.dataProOp = JSON.parse(localStorage.getItem(storeDataProOp));
                }
                return true;
            } else {
                iLog('Br Not Support', null, 5);
                return false;
            }
        }
    }
};



