<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AssetChosen
 *
 * @author Николай Константинович Бугаёв http://maze-studio.ru
 */

namespace ui\assets;

use maze\document\AssetBundle;

class AssetStyleCore extends AssetBundle {

    public $basePath = '@admin/templates/system/css';
    public $baseUrl = '/admin/templates/system/css';
    public $css = ['system.css'];

}
