<script>
    var gaProperty = '{$GoogleTrackingID|escape:'javascript'}';
    // Disable tracking if the opt-out cookie exists.
    var disableStr = 'ga-disable-' + gaProperty;
    if (document.cookie.indexOf(disableStr + '=true') > -1) {
        window[disableStr] = true;
    }
    // Opt-out function
    function gaOptout() {
        document.cookie = disableStr + '=true; expires=Thu, 31 Dec 2199 23:59:59 UTC; path=/';
        window[disableStr] = true;
    }
</script>
