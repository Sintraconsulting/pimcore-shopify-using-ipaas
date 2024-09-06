pimcore.registerNS("pimcore.plugin.SyncShopifyBundle");

pimcore.plugin.SyncShopifyBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // alert("SyncShopifyBundle ready!");
    }
});

var SyncShopifyBundlePlugin = new pimcore.plugin.SyncShopifyBundle();
