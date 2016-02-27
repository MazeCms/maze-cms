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

namespace wid\wid_toolbar;

use maze\document\AssetBundle;

class AssetWidget extends AssetBundle {

    public $basePath = '@wid/wid_toolbar/assets';
    
    public $baseUrl = 'admin/widgets/wid_toolbar/assets';
    
    public $css = ['css/toolbar.css'];
    
    public $js = ['js/tool-bar-admin.js'];


}
