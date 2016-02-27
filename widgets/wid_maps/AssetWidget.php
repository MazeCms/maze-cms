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

namespace wid\wid_maps;

use maze\document\AssetBundle;

class AssetWidget extends AssetBundle {

    public $basePath = '@wid/wid_maps/assets';
    
    public $baseUrl = '/widgets/wid_maps/assets';
    
    public $js = ['js/jquery.gmaps.min.js'];
    
    public $depends = ['ui\assets\AssetJquery'];


}
