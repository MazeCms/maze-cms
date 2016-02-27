function Uninstall(options)
{
    var options = $.extend({
        url: "/admin/installapp/?run=uninstall&clear=ajax",
        task: "sortcolonum",
        form: ".admin-form",
        message: {
            title_uninstall: "Удаление программы",
            title_error: "Ошибка удаления",
            error_server: "Ошибка запроса к серверу",
            error_uninstall: "Ошибка ответа от сервера",
            start_text:'Секундочку.... дай поудмать',
            cancel: "Отмена",
            uninstall_ok: "Приложение успешно удалено, диалог автоматически закроется через",
            close: "Закрыть",
            ok: "Выполнить",
            title: "Удалить",
            text: ""

        }
    }, options || {});

    this.options = options;
    this.startInstall = true;
    this.stepsInstal = null;
    this.dialog;

}

Uninstall.prototype.getMess = function (value)
{
    if (this.options.message.hasOwnProperty(value))
    {
        return this.options.message[value]
    }
    return false;
}

Uninstall.prototype.delete = function (id)
{
    var self = this;

    cms.alertPromt({
        close: self.getMess('close'),
        text: self.getMess('text'),
        title: self.getMess('title'),
        ok: self.getMess('ok'),
        h: 'auto',
        w: 300,
        task: "",
        callback: function () {
           self.startUninstall(id)
        },
    })
    return false;
}
Uninstall.prototype.startUninstall = function (id)
{
    var self = this;
    $(document).one('ajaxError', function (e) {
        self.setError("error-server", false, self.getMess('error_server'));
        self.getError();
    })
    
    self.startDialog();
    self.processBar(100);
    self.printMess(self.getMess('start_text'));
     
    $.getJSON(self.options.url, {cmd: "command", id_app: id}, function(data){
        $(document).unbind('ajaxError');

        if (!data.hasOwnProperty('html') || typeof data.html !== 'object'){
            self.printMess({errors:self.getMess('error_uninstall')});
            return false;
        }
        
        if (data.html.hasOwnProperty('errors')) {
            self.printMess(data.html);
            return false;
        }       
        
        self.startInstall = false;
        self.getStepInstall(data.html);
    });
    return false;
}
Uninstall.prototype.updateTable = function ()
{
    $("#installapp-grid").mazeGrid('update');
}
Uninstall.prototype.startDialog = function ()
{
    var self = this;

    var process = '<blockquote><div class="progress">'
            + '<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">0%</div>'
            + '</div>'
            + '<footer class="message-install"></footer>'
            + '</blockquote>'
        $('<div>', {id: 'result-install'}).append(process).mazeDialog({
        title: self.getMess('title_uninstall'),
        toolbarhead: {minidialog: null},
        height: 'auto',
        modal: true,
        width: 600,
        resize: false,
        close: function () {
            self.cancelInstall();
            this.mazeDialog('destroy');
        },
        buttons: [
            {
                label: self.getMess('cancel'),
                class_btn: "maze-btn-warning",
                class_icon: "",
                action: function (e, obj) {
                    this.mazeDialog('close');
                }
            }
        ]
    });

}
Uninstall.prototype.printMess = function (text)
{
    var self = this;

    if (text !== null && typeof text == 'object') {
        if (text.hasOwnProperty('errors')) {
            var result = '';
            if (typeof text.errors == 'object') {
                $.each(text.errors, function (i, val) {
                    result += '<li>' + val.join('<br>') + '</li>';
                })
            } else {
                result = text.errors;
            }

            $("#result-install").append('<div class="alert alert-danger"><ul>' + result + '</ul></div>')
        }
    } else {
        $("#result-install").find('.message-install').html(text);
    }

}
Uninstall.prototype.processBar = function (value)
{
    $('#result-install .progress-bar').width(value + '%').text(value + "%")
}
Uninstall.prototype.getStepInstall = function (dataInstall)
{
    var self = this;
    
    $(document).one('ajaxError', function (e) {
        self.endInstall("error", self.getMess('error_server'));

    })
    
    if (!dataInstall.hasOwnProperty('step') || typeof dataInstall.step !== 'object')
    {
        this.endInstall("error", {errors: self.getMess('error_uninstall')});
        return false;
    }

    var self = this;
    var count = 0;
    var action = $.map(dataInstall.step, function (val, key) {
        return key;
    });

    self.processBar(0);

    this.stepsInstal = dataInstall.step;

    function recursive()
    {
        var cmd = action[count];
        self.printMess(self.stepsInstal[cmd]);
        $.getJSON(self.options.url, {cmd: cmd, params: dataInstall.params}, function (data) {

            if (data.html.hasOwnProperty('errors'))
            {
                self.endInstall("error", data.html);
                return false;
            }

            if (!data.html.hasOwnProperty('result')) {
                self.endInstall("error", {errors: self.getMess('error_uninstall')});
                return false;
            }

            self.processBar(Math.round((count / action.length) * 100));

            if (count < action.length) {
                recursive();
            } else {
                self.endInstall("success");
            }
        });
        count++;
    }
    
    recursive()
    
}
Uninstall.prototype.endInstall = function (type, text)
{
    var self = this;
    
    
    if (type == "success")
    {
        $('<div>')
                .addClass("alert alert-success")
                .html(self.getMess("uninstall_ok") + ' - <span class="timer-redirect"></span>')
                .insertBefore("#result-install");
        var count = 10;
        var timer = setInterval(function () {
            count--;
            $('.timer-redirect').text(count);
            if (count == 0)
            {
                clearInterval(timer);
                $("#result-install").mazeDialog('close');
                // document.location = document.location;
            }
        }, 500)
    }
    else if (type == "error")
    {
        this.printMess(text);
    }

}
Uninstall.prototype.cancelInstall = function ()
{
    this.startInstall = true;
    this.stepsInstal = null;
    this.updateTable();
}