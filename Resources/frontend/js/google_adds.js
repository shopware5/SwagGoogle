(function($) {

    GoogleAdds = function(opts) {
        var me = this,
            url, img;

        me.opts = opts;

        url = me.createUrl();
        img = me.createImage(url);

        me.addVariables();

        $('<script src="https://www.googleadservices.com/pagead/conversion_async.js"></script>')
            .appendTo('body');

        $('<noscript></noscript>').append($(img)).appendTo('body');
    };

    GoogleAdds.prototype = {
        addVariables: function() {
            var me = this,
                data = [
                    'var google_conversion_id="' + me.opts.googleConversionID + '"',
                    'google_conversion_language="' + me.opts.googleConversionLanguage + '"',
                    'google_conversion_value="' + me.opts.realAmount + '"',
                    'google_conversion_label="' + me.opts.googleConversionLabel + '"',
                    'google_conversion_currency="' + me.opts.currency + '"',
                    'google_conversion_format="1"',
                    'google_conversion_color = "FFFFFF"',
                    'google_remarketing_only=false;'
                ].join(',');

            $('<script></script>').append(data).appendTo('body');
        },

        createImage: function(url) {
            return [
                '<img height="1" width="1" border="0" src="',
                url,
                '">'
            ].join('');
        },

        createUrl: function() {
            var me = this;

            return [
                'https://www.googleadservices.com/pagead/conversion/',
                me.opts.googleConversionID,
                '/?value=',
                me.opts.realAmount,
                '&currency_code=',
                me.opts.currency,
                '&label=',
                me.opts.googleConversionLabel,
                '&guid=ON&script=0'
            ].join('');
        },
    };

})(jQuery);
