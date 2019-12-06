(function($) {

    $.plugin('SwagGoogleAnalytics', {

        defaults: {
            realAmount: 0.0,

            googleTrackingID: '',

            googleConversionID: '',

            googleConversionLabel: '',

            googleConversionLanguage: '',

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

            doNotTrack: false
        },

        init: function() {
            var me = this;

            me.applyDataAttributes();

            me.checkGetCookiePreference();
            me.opts.doNotTrack = me.checkDoNotTrack();

            if (me.isGoogleAllowed() || me.evaluateCookieHint()) {
                me.createLibrary();
                return;
            }

            me.createCheckTimer();
        },

        isGoogleAllowed: function() {
            var me = this;

            me.cookieValue = me.getCookie();

            return me.cookieValue || $.getCookiePreference(me.getCookieKey());
        },

        checkDoNotTrack: function() {
            if (window.doNotTrack || navigator.doNotTrack || navigator.msDoNotTrack) {
                if (window.doNotTrack == "1" || navigator.doNotTrack === "yes" || navigator.doNotTrack == "1" || navigator.msDoNotTrack == "1") {
                    return true;
                }
            }

            return false;
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

            if (me.isGoogleAllowed()) {
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
        },

        /**
         * Polyfill for older shopware versions
         */
        checkGetCookiePreference: function() {
            if ($.isFunction($.getCookiePreference)) {
                return;
            }

            $.getCookiePreference = function() {
                return false;
            };
        },

        getCookieKey: function() {
            if (this.opts.googleTrackingLibrary === 'ga') {
                return '__utm'
            }

            return '_ga';
        },
    });

    $(document).ready(function() {
        $('div[data-googleAnalytics="true"]').SwagGoogleAnalytics();
    });

})(jQuery, window, document);
