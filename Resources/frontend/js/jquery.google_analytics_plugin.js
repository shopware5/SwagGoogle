(function($) {

    $.plugin('SwagGoogleAnalytics', {

        defaults: {
            realAmount: 0.0,

            googleConversionID: '',

            googleConversionLable: '',

            googleConversionLanguage: '',

            googleTrackingID: '',

            googleAnonymizeIp: '',

            googleOptOutCookie: '',

            googleTrackingLibrary: '',

            googleOptOutCookie: '',

            createEcommerceTransaction: false,

            cookieNoteMode: null,

            showCookieNote: null,

            orderNumber: null,

            affiliation: null,

            revenue: null,

            tax: null,

            shipping: null,

            currency: null,

            city: null,

            country: null,

            basket: window.basketData
        },

        init: function() {
            var me = this;

            me.applyDataAttributes();

            me.cookieValue = me.getCookie();
            if (me.cookieValue || me.evaluateCookieHint()) {
                me.createLibrary();
                new GoogleAdds(me.opts);
                return;
            }

            me.createCheckTimer();
        },

        evaluateCookieHint: function() {
            var me = this;

            if (!me.opts.showCookieNote) {
                return true;
            }

            return me.opts.showCookieNote === 1 && me.opts.cookieNoteMode === 0;
        },

        createCheckTimer: function() {
            var me = this;

            me.interval = window.setInterval($.proxy(me.onCheckCookie, me), 1000);
        },

        onCheckCookie: function() {
            var me = this;

            me.cookieValue = me.getCookie();
            if (me.cookieValue) {
                window.clearInterval(me.interval);
                me.createLibrary();
            }
        },

        createLibrary: function() {
            var me = this;

            if (me.opts.googleTrackingLibrary === 'ga') {
                new GoogleAnalytics(me.opts);
                return;
            }

            new UniversalAnalytics(me.opts);
        },

        getCookie: function() {
            var name = "allowCookie=",
                decodedCookie = decodeURIComponent(document.cookie),
                cookieArray = decodedCookie.split(';');

            for (var i = 0; i < cookieArray.length; i++) {
                var cookie = cookieArray[i];
                while (cookie.charAt(0) == ' ') {
                    cookie = cookie.substring(1);
                }
                if (cookie.indexOf(name) == 0) {
                    return cookie.substring(name.length, cookie.length);
                }
            }

            return null;
        }
    });

    $(document).ready(function() {
        $('div[data-googleAnalytics="true"]').SwagGoogleAnalytics();
    });

})(jQuery, window, document);
