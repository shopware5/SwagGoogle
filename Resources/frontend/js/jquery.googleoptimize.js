$.subscribe("plugin/swEmotionLoader/onLoadEmotionFinished", function(me) {
    dataLayer.push({'event': 'optimize.activate'});
});