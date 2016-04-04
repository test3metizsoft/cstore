/**
 * Created by vjcspy on 7/28/15.
 */
var syncOfflineData = function () {
    "use strict";

};

syncOfflineData.prototype = {
    synchingData: false,
    netWorkOnline: false,
    timeRecheckNetWork: 30000,
    currentStoreName: '',
    orderComplete: [],
    holdWhenCheckOutOnline: true,
    initialize: function (data) {
        if (!data) {
            data = {};
        } else {
            this.timeRecheckNetWork = data.timeRecheckNetWork;
            this.holdWhenCheckOutOnline = data.holdWhenCheckOutOnline;
        }
    },
    Ping: function Ping(url, timeout) {
        timeout = timeout || 5000;
        url = url || window.BASE_URL;
        var timer = null;

        return jQuery.Deferred(function deferred(defer) {

            var img = new Image();
            img.onload = function () {
                success("onload");
            };
            img.onerror = function () {
                success("onerror");
            };

            var start = new Date();
            img.src = url += ("?cache=" + +start);
            timer = window.setTimeout(function timer() {
                fail();
            }, timeout);

            function cleanup() {
                window.clearTimeout(timer);
                timer = img = null;
            }

            function success(on) {
                cleanup();
                this.netWorkOnline = true;
                defer.resolve(true, url, new Date() - start, on);
            }

            function fail() {
                cleanup();
                this.netWorkOnline = false;
                defer.reject(false, url, new Date() - start, "timeout");
            }

        }).promise();
    },
    setStatus: function (isSynching) {
        "use strict";
        if (isSynching) {
            this.synchingData = true;
            jQuery('#count_pending_orders').html('Synching');
            $jQuery('#link_sync_order').removeAttr('onclick');
        } else {
            this.synchingData = false;
            var orders = $.jStorage.get("orders");
            jQuery('#count_pending_orders').html(orders.length);
            //jQuery('#link_sync_order').attr('onclick', 'window.XPosSyncData.syncData()');
        }
    },
    syncData: function () {
        "use strict";
        if (isOnline() && this.holdWhenCheckOutOnline == true) {
            iLog('Send Offline Order. Hold because you are checking online!');
            setTimeout(function () {
                window.XPosSyncData.syncData();
            }, window.XPosSyncData.timeRecheckNetWork);
            return;
        }
        if ($.jStorage.get("orders") == null) {
            iLog('Send Offline Order. Nothing to send!');
            setTimeout(function () {
                window.XPosSyncData.syncData();
            }, window.XPosSyncData.timeRecheckNetWork);
            return;
        }
        this.Ping(window.BASE_URL, 5000).done(function (success, url, time, on) {
            var orders = $.jStorage.get("orders");
            numOfOrder = orders.length;
            if (numOfOrder > 0 && orders[0].length) {
                window.XPosSyncData.setStatus(true);
                jQuery.ajax({
                    url: window.url_offline_order,
                    data: orders[0],
                    dataType: 'json',
                    type: 'POST',
                    success: function (data) {
                        if (data['status'] == 'success') {
                            iLog('Send Offline Order. Success!');
                            window.XPosSyncData.saveOrderComplete(orders[0]);
                        }
                        window.XPosSyncData.loadAjax(orders, 1);
                    }
                });
            }
            else {
                window.XPosSyncData.setStatus(false);
                iLog('Send Offline Order. Nothing to send!');
                setTimeout(function () {
                    window.XPosSyncData.syncData();
                }, window.XPosSyncData.timeRecheckNetWork);
            }
            transactionMoneyLoaded = false;
        }).fail(function (failure, url, time, on) {
            console.log("ping fail", arguments);
            iLog('Send Offline Order. Not online!');
            setTimeout(function () {
                window.XPosSyncData.syncData();
            }, window.XPosSyncData.timeRecheckNetWork);
        });
    },
    loadAjax: function (orders, vt) {
        if (isOnline() && this.holdWhenCheckOutOnline == true) {
            iLog('Send Offline Order. Hold because checking online!');
            return;
        }
        "use strict";
        iLog('Send next order: ' + vt);
        if (numOfOrder > vt) {
            jQuery.ajax({
                url: window.url_offline_order,
                data: orders[vt],
                dataType: 'json',
                type: 'POST',
                success: function (data) {
                    if (data['status'] == 'success') {
                        console.log('data success!');
                        window.XPosSyncData.saveOrderComplete(orders[vt]);
                    }
                    window.XPosSyncData.loadAjax(orders, vt + 1);
                }
            });
        } else {
            window.XPosSyncData.removeOrderComplete();
        }
    },
    saveOrderComplete: function (order) {
        this.orderComplete.push(order);
    },

    removeOrderComplete: function () {
        var orders = $.jStorage.get("orders");
        var numOfSuccsessOrder = this.orderComplete.length;
        for (i = 0; i < this.orderComplete.length; i++) {
            var vt = orders.indexOf(this.orderComplete[i]);
            if (vt != -1) {
                orders.splice(vt, 1);
            }
        }
        $.jStorage.set("orders", orders);
        jQuery('#count_pending_orders').html(orders.length);
        this.removeAllElementOrderComplete();
        iLog('Send ' + numOfSuccsessOrder + ' Order Complete!');
        setTimeout(function () {
            window.XPosSyncData.syncData();
        }, window.XPosSyncData.timeRecheckNetWork);
    },
    removeAllElementOrderComplete: function () {
        this.orderComplete.splice(0, this.orderComplete.length);
        console.log('current Order Complete: ' + this.orderComplete);
    },

    sendDataOld: function () {
        "use strict";
        submitOfflineNewT();
    },

    hideLoadingMask: function () {
        Element.hide('loading-mask');
    },
    displayLoadingMask: function () {
        var loaderArea = $$('#html-body .wrapper')[0]; // Blocks all page
        Position.clone($(loaderArea), $('loading-mask'), {offsetLeft: -2});
        toggleSelectsUnderBlock($('loading-mask'), false);
        Element.show('loading-mask');
    }

}
;


function ping() {
    window.XPosSyncData.Ping(window.BASE_URL, 5000).done(function (success, url, time, on) {
        console.log("ping done", arguments);
        ping();
    }).fail(function (failure, url, time, on) {
        console.log("ping fail", arguments);
        ping();
    });
}

jQuery(document).ready(function () {
    "use strict";
    //window.setTimeout(ping, 1000);
    if (false) {
        var data = {};
        data.timeRecheckNetWork = 30000;
        data.holdWhenCheckOutOnline = true;
        window.XPosSyncData = new syncOfflineData(data);
        window.XPosSyncData.syncData();
        if (true) {
            $jQuery('#link_sync_order').removeAttr('onclick');
        }
    }
});


