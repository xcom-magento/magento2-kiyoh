define([
    'jquery',
    'mage/storage',
    'mage/url',
    'domReady!'
], function ($, storage, url) {

    function apiCall(apiUrl, callback) {
        storage.get(
            getUrl(apiUrl)
        ).done(function (result) {
            callback(result);
        }).fail(function () {
            callback(false);
        }).always(function () {
        });
    }

    function getUrl(urlParam) {
        var apiCall = url.build(urlParam);
        if (apiCall.indexOf("http") == -1) {
            apiCall = window.location.origin + "/" + apiCall;
        }
        return apiCall;
    }

    return function (config, dom) {
        var collectSpan = $('#collect_span');
        $(dom).find("#productsync_submit").on('click', function () {
            collectSpan.find('.collected').hide();
            collectSpan.find('.processing').show();
            apiCall(config.url, function (response) {
                console.log(response);
                collectSpan.find('.collected').show();
                collectSpan.find('.processing').hide();
            })
        })
    }

});
