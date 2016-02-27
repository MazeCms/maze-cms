var mazeInsatll = (function () {
    function MazeInstall(options)
    {

        this.options = $.extend({
            btnPrev: '#prev-install',
            btnNext: '#next-install',
            form: '#form-install',
            repeatBtn:'#repeat-btn',
            missBtn:"#miss-btn",
            endinstallBtn:"#endinstall-btn",
            totalStep:0,
        }, options || {});
        var selfInstance = this;
        this.form = $(selfInstance.options.form)
    }

    MazeInstall.prototype = {
        constructor: MazeInstall,
        init: function () {
            var options = this.options;
            var selfObj = this;
            $(options.btnNext).click(function () {
                selfObj.loadStep($(this).attr('data-step'), $(this).attr('data-wizard'));
                return false;
            })

            $(options.btnPrev).click(function () {
                selfObj.loadStep($(this).attr('data-step'), $(this).attr('data-wizard'));
                return false;
            })
            $(options.repeatBtn+', '+options.missBtn).click(function(){
                var $self = $(this)
                selfObj.startInstall({nextStep:$self.attr('data-step')})
            })
        },
        loadStep: function (step, wizard) {
            var form = $('.right-col form');
            var url = form.attr('action');
            url = cms.URI(url);
            url.setVar('step', step);
            if (wizard && wizard !== '') {
                url.setVar('wizardStep', wizard);
            } else {
                url.delVar('wizardStep')
            }
            form.attr('action', url.toString());
            form.submit();

        },
        startInstall: function (params) {
            var selfInstance = this;
            var url = cms.URI(selfInstance.form.attr('action'));
            url.setVar('clear', 'ajax');
            var postData = selfInstance.form.serializeObject();
            postData = $.extend(postData,params || {})
            var btn = $(selfInstance.options.repeatBtn);
            var btnMiss = $(selfInstance.options.missBtn);
            btn.hide();
            btnMiss.hide();
            $.ajax({
                url: url.toString(),
                type: 'POST',
                dataType: 'json',
                data:postData,
                cache: false,
                timeout: 1000000,
                success: function (data) {
                    var data = data.html
                    var procente = Number(data.step) * 100 / selfInstance.options.totalStep;
                    var message = data.resultCode ? '<div class="alert alert-success" role="alert">' : '<div class="alert alert-danger" role="alert">'
                    message += '<button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span></button>'
                    message += data.message;
                    message += '</div>'
                    $('.message-install').append(message);
                    $('.progress-bar').css('width', Math.round(procente) + '%').text(Math.round(procente) + '%')
                    if (!data.resultCode) {
                        $('#prev-install').show();
                    }
                    if (procente == 100 && data.resultCode) {
                        $('#progress-install .progress').hide();
                        $('.alert').remove();
                        $(selfInstance.options.endinstallBtn).show()
                    }
                    if(!data.resultCode){                        
                        btn.show();
                        btn.attr('data-step', data.curentStep)
                        if(data.step > 3){
                            btnMiss.show();
                            btnMiss.attr('data-step', data.nextStep)
                        }
                        
                    }
                    if(data.resultCode && data.nextStep){
                        selfInstance.startInstall(data);
                    }
                    
                }
            })
        }


    }

    return MazeInstall;
})();