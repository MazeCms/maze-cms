function actionIndexLang() {
    if ($('#applications-pack-indexlang').is(':visible')) {
        return false;
    }
    var url = cms.getURL([{run: 'index', clear: 'ajax'}]);
    var csrfName = $('meta[name=csrf-param]').attr('content');
    var csrfToken = $('meta[name=csrf-token]').attr('content');
    var count = 0;
    function indexApp(data, keys, step) {
        var id_app = keys[count];
        if (data.hasOwnProperty(id_app)) {
            $('#applications-pack-indexlang')
                    .find('.progress-bar')
                    .width((count * step) + '%')
                    .text((Math.round(count * step)) + '%');
            $('#applications-pack-indexlang').find('footer').text(data[id_app]);
            var paramUrl = {id_app: id_app};
            if(csrfName && csrfToken){
                paramUrl[csrfName] = csrfToken;
            }
                
            $.post(url, paramUrl, function () {
                indexApp(data, keys, step);
            }, 'json')
        }
        count++;
        if (count > keys.length) {
            $('#applications-pack-indexlang').fadeOut(500);
            $('#applications-pack-grid').mazeGrid('update');
        }
    }
    $('#applications-pack-indexlang')
            .fadeIn(500)
            .find('.progress-bar')
            .width(0)
            .text('0%');
    $.get(url, function (data) {
        var keys = Object.keys(data.html);
        var step = 100 / keys.length;
        indexApp(data.html, keys, step);
    }, 'json')
}

