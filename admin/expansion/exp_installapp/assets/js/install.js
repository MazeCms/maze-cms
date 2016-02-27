function Install(options)
{
    var options = $.extend({
        dropzone: "#load-bloc-drop",
        inputfile: "#hide-upload",
        url: "/admin/installapp/install/?run=install&clear=ajax",
        btnInstall: "#upload-install",
        form: "#form-install",
        file: "#load-file",
        file_path: "#load-path",
        file_url: "#load-url",
        toggle_form: "#select-form"
    }, options || {});

    var self = this;
    this.file = null;
    this.options = options;
    this.startInstall = true;
    this.stepsInstal = null;

    this.error = {
        flag: {},
        mess: {}
    };
}
Install.prototype.init = function ()
{
    var self = this;

    self.getForm();
    self.getDropFile();
    self.getBtnFile();
    self.loadInstall();

}
Install.prototype.getForm = function ()
{
    var self = this;

    $(self.options.toggle_form).bind("change", function () {
        var type = $(this).find('input:checked').val();

        $(self.options.form).find('.type-load').hide()
        $('#load-' + type).show();
    })


}


Install.prototype.getError = function ()
{
    for (proper in this.error.flag)
    {
        if (!this.error.flag[proper])
        {
            var text = '';
            for (proper in this.error.mess)
                text += '<div class="alert-menu-item"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span>  ' + this.error.mess[proper] + '</div>';
            cms.alertBtn(cms.getLang('EXP_INSTALLAPP_INSTALL_ALERT_TITLE'), text, 'auto', 500);
            return false;
        }
    }
}

Install.prototype.setError = function (name, flag, mess)
{
    this.error.flag[name] = flag;
    this.error.mess[name] = mess;
}

Install.prototype.checkType = function (value)
{
    if (value.name.indexOf('.zip') == -1)
    {
        this.setError("type", false, cms.getLang('EXP_INSTALLAPP_INSTALL_ALERT_ERROR_TYPE'));
        this.getError();
        return false;
    }
    else
    {
        this.setError("type", true, cms.getLang('EXP_INSTALLAPP_INSTALL_ALERT_ERROR_TYPE'));
        return true;
    }
}

Install.prototype.getDropFile = function ()
{
    var self = this;
    var $elem = $(self.options.dropzone);

    $elem.bind("dragenter", function (e) {
        e.stopPropagation();
        e.preventDefault();
        $elem.addClass("load-bloc-drop-active")
    })
    $elem.bind("dragleave", function (e) {
        e.stopPropagation();
        e.preventDefault();
        $elem.removeClass("load-bloc-drop-active")
    })
    $elem.bind("dragover", function (e) {
        e.stopPropagation();
        e.preventDefault();
        $elem.addClass("load-bloc-drop-active")
    })
    $elem.bind("drop", function (e) {
        e.stopPropagation();
        e.preventDefault();
        $elem.removeClass("load-bloc-drop-active");
        var file = e.originalEvent.dataTransfer.files[0];
        if (!self.checkType(file))
            return false;
        self.file = e.originalEvent.dataTransfer.files;


        if ($(".path-file").hasClass("path-file"))
        {
            $(".path-file").html(file.name)
        }
        else
        {
            $(".load-bloc-drop").append($("<div>").addClass("path-file").html(file.name))
        }

    })
}

Install.prototype.getBtnFile = function ()
{
    var self = this;
    $(self.options.inputfile).bind("change", function (e) {
        var file = $(this).val();
        if (!self.checkType(e.target.files[0]))
            return false;
        if (file !== "")
        {
            if ($(".path-file").hasClass("path-file"))
            {
                $(".path-file").html(file)
            }
            else
            {
                $(self.options.dropzone).append($("<div>").addClass("path-file").html(file))
            }
            self.file = e.target.files;
        }

    })
}

Install.prototype.unzip = function (data)
{
    var self = this;


    if (data == null) {
        self.endInstall("error", {errors: cms.getLang('EXP_INSTALLAPP_INSTALL_ALERT_SERVERDATA')});
        return false;
    }

    if (data.hasOwnProperty('errors')) {
        self.endInstall("error", data);
        return false;
    }

    if (!data.hasOwnProperty('name')) {
        self.endInstall("error", {errors: cms.getLang('EXP_INSTALLAPP_INSTALL_ALERT_SERVERDATA')});
        return false;
    }


    self.printMess(cms.getLang('EXP_INSTALLAPP_INSTALL_UNZIP'));

    $.getJSON(self.options.url, {cmd: "unzip", name: data.name}, function (data) {
        self.processBar(0);
        if (data.html == null) {
            self.endInstall("error", {errors: cms.getLang('EXP_INSTALLAPP_INSTALL_ALERT_SERVERDATA')});
            return false;
        }

        if (data.html.hasOwnProperty('error')) {
            self.endInstall("error", data);
            return false;
        }

        self.getStepInstall(data.html);
    });
}


Install.prototype.loadInstall = function ()
{
    var self = this;
    var $form = $(self.options.form);
    $(self.options.btnInstall).bind("click", function (e) {

        var type = $(self.options.toggle_form).find('input:checked').val();

        if (type == 'file') {
            if (self.file == null)
                return false;
            self.startDialog();
            self.printMess(cms.getLang('EXP_INSTALLAPP_INSTALL_STEPLOAD'))
            var jqXHR = $(self.options.form).fileupload({
                dataType: 'json',
                url: self.options.url,
                singleFileUploads: false,
                multipart: true,
                change: function (e, data) {
                    return false
                },
                done: function (e, data) {
                    self.unzip(data.result.html);
                },
                progressall: function (e, data) {
                    self.processBar(parseInt(data.loaded / data.total * 100, 10))
                }
            }).fileupload('add', {files: self.file})
        }
        else if (type == 'path' || type == 'url') {

            if ((type == 'path' && $(self.options.file_path).find('input').val() == '') ||
                    (type == 'url' && $(self.options.file_url).find('input').val() == '')) {
                return false;
            }
            self.startDialog();
            self.printMess(cms.getLang('EXP_INSTALLAPP_INSTALL_STEPLOAD'));
            self.processBar(100);
            $.post(self.options.url, $form.serialize(), function (data) {
                self.unzip(data.html);
            }, 'json');

        }
    })

}
Install.prototype.startDialog = function ()
{
    var self = this;

    var process = '<blockquote><div class="progress">'
            + '<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">0%</div>'
            + '</div>'
            + '<footer class="message-install"></footer>'
            + '</blockquote>'
    $('<div>', {id: 'result-install'}).append(process).mazeDialog({
        title: cms.getLang('EXP_INSTALLAPP_INSTALL_FORM_ALERT_TITLE'),
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
                label: cms.getLang('EXP_INSTALLAPP_CANCEL'),
                class_btn: "maze-btn-warning",
                class_icon: "",
                action: function (e, obj) {
                    this.mazeDialog('close');
                }
            }
        ]
    });

}
Install.prototype.printMess = function (text)
{
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

Install.prototype.processBar = function (value)
{
    $('#result-install .progress-bar').width(value + '%').text(value + "%")
}

Install.prototype.getStepInstall = function (dataInstall)
{
    if (!dataInstall.hasOwnProperty('step') || typeof dataInstall.step !== 'object')
    {
        this.endInstall("error", {errors: cms.getLang('EXP_INSTALLAPP_INSTALL_ALERT_SERVERDATA')});
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
                self.endInstall("error", {errors: cms.getLang('EXP_INSTALLAPP_INSTALL_ALERT_SERVERDATA')});
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
Install.prototype.endInstall = function (type, text)
{
    if (type == "success")
    {
        $('<div>')
                .addClass("alert alert-success")
                .html(cms.getLang('EXP_INSTALLAPP_INSTALL_OK') + ' - <span class="timer-redirect"></span>')
                .insertBefore("#result-install");
        var count = 10;
        var timer = setInterval(function () {
            count--;
            $('.timer-redirect').text(count);
            if (count == 0)
            {
                clearInterval(timer);
                $("#result-install").mazeDialog('close');
                document.location = document.location;
            }
        }, 500)
    }
    else if (type == "error")
    {
        this.printMess(text);
    }

}
Install.prototype.cancelInstall = function ()
{
    var self = this;
    this.file = null;
    this.startInstall = true;
    this.stepsInstal = null;
    $(".path-file").remove();

}