(function($, window, document) {

    /**
     * @param { object } opts
     */
    UniversalAnalytics = function(opts) {
        var me = this;

        me.opts = opts;

        me.analytics = me.createUniversalAnalytics();
        me.startUniversalAnalytics();
        me.sendUniversalECommerce();

        me.analytics('send', 'pageview');
    };

    UniversalAnalytics.prototype = {
        createUniversalAnalytics: function() {
            (function(i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function() {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'universalAnalytics');

            return universalAnalytics;
        },

        startUniversalAnalytics: function() {
            var me = this;

            me.analytics('create', me.opts.googleTrackingID, 'auto');
            if (me.opts.googleAnonymizeIp) {
                me.analytics('set', 'anonymizeIp', true);
            }

            if (me.opts.doNotTrack) {
                me.analytics('require', 'dnt');

                $('<script type="text/javascript" async src="//storage.googleapis.com/outfox/dnt_min.js"></script>')
                    .append('body');
            }
        },

        sendUniversalECommerce: function() {
            var me = this;

            if (!me.opts.createEcommerceTransaction) {
                return;
            }

            me.analytics('require', 'ecommerce', 'ecommerce.js');
            me.addUniversalTransaction();
        },

        addUniversalTransaction: function() {
            var me = this;

            if (!me.opts.basket.hasData) {
                return;
            }

            me.analytics('ecommerce:addTransaction', {
                id: me.opts.orderNumber,
                affiliation: me.opts.affiliation,
                revenue: me.opts.revenue,
                tax: me.opts.tax,
                shipping: me.opts.shipping,
                currency: me.opts.currency
            });

            me.addUniversalECommerceItems();

            me.analytics('ecommerce:send');

            new GoogleAdds(me.opts);
        },

        addUniversalECommerceItems: function() {
            var me = this;

            $.each(me.opts.basket.data, function(index, item) {
                me.analytics('ecommerce:addItem', item);
            });
        },
    };

})(jQuery, window, document);


