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

namespace wid\wid_navbar;

use maze\document\AssetBundle;

class AssetWidget extends AssetBundle {

    public $basePath = '@wid/wid_navbar/assets';
    
    public $baseUrl = 'admin/widgets/wid_navbar/assets';
    
    public $css = ['css/tool-bar-admin.css'];


}
