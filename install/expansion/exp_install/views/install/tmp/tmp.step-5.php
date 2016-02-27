<?php

use maze\helpers\Html;
?>
<div id="progress-install">
    <h4>Процесс установка</h4>
    <div class="progress">
        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 1%">
            0%
        </div>
    </div>
    <div class="message-install">

    </div>
    <div class="row">
        <div class="col-xs-6">
            <button id="miss-btn" style="display: none;"  type="button" class="btn btn-warning btn-block">Пропустить</button>
        </div>
        <div class="col-xs-6">
            <button id="repeat-btn" style="display: none;"  type="button" class="btn btn-primary btn-block">Повторить</button>
        </div>
    </div>
    <div style="display: none;"  id="endinstall-btn" class="jumbotron">
        <h1>Ура!! примите мои поздравления</h1>
        <p>Система успешно установлена. После установки удалите директорию "install"</p>
        <p><a class="btn btn-primary btn-lg" href="/admin/admin" role="button">Перейти в админку</a></p>
    </div>
   
</div>
<script>
    jQuery().ready(function () {
        var installObj = new mazeInsatll({
            totalStep:<?= $totalStep ?>
        })
        installObj.init();
        $('#prev-install, #next-install').hide()
        installObj.startInstall({nextStep: 'createTable'});
    })
</script>

