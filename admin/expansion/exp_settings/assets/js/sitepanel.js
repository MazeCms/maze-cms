sttingsClearChache = function ()
{
    cms.ajaxSend({
        url: '/admin/settings?run=clearchache&clear=ajax&fullclear=1',
        serialize: false,
        overlay: false,
        preloadblock: 'body',
        handler: function (data) {
            $('.tool-bar-site').toolBarSite('setMessage', data.html, data.type)
        }
    });
}