(function($, window, document) {

    /**
     * @param { object } opts
     */
    GoogleAnalytics = function(opts) {
        var me = this;

        me.opts = opts;

        me.addHeadScript();
        me.initScript();
    };

    GoogleAnalytics.prototype = {
        initScript: function() {
            var ga = document.createElement('script');
            ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
        },

        addHeadScript: function() {
            var me = this,
                script = [
                    "var _gaq = _gaq || [];",
                    [
                        "_gaq.push(['_setAccount',",
                        '"',
                        me.opts.googleTrackingID,
                        '"',
                        "]);"
                    ].join(''),
                ],
                entity;

            if (me.opts.googleAnonymizeIp) {
                script.push("_gaq.push(['_gat._anonymizeIp']);");
            }

            if (me.opts.basket.hasData) {
                me.addTransaction(script);
                me.addTransactionData(script);
                new GoogleAdds(me.opts);
                script.push("_gaq.push(['_trackTrans']);");
            }

            script.push("_gaq.push(['_trackPageview']);");

            entity = [
                '<script>',
                script.join(' '),
                '</script>'
            ];

            $(entity.join('')).appendTo('head');
        },

        addTransaction: function(script) {
            var me = this,
                data = [
                    '"' + me.opts.orderNumber + '"',
                    '"' + escape(me.opts.affiliation) + '"',
                    '"' + me.opts.revenue + '"',
                    '"' + me.opts.tax + '"',
                    '"' + me.opts.shipping + '"',
                    '"' + me.opts.city + '"',
                    "",
                    '"' + me.opts.country + '"',
                ].join(',');

            script.push([
                "_gaq.push(['_addTrans',",
                data,
                "]);"].join('')
            );
        },

        addTransactionData: function(script) {
            var me = this, itemString;

            $.each(me.opts.basket.data, function(index, item) {
                itemString = [
                    '"' + me.opts.orderNumber + '"',
                    '"' + item.sku + '"',
                    '"' + escape(item.name) + '"',
                    "",
                    '"' + item.price + '"',
                    '"' + item.quantity + '"',
                ].join(',');

                script.push([
                    "_gaq.push(['_addItem',",
                    itemString,
                    "]);"
                ].join(''));
            });
        },
    };

})(jQuery, window, document);
