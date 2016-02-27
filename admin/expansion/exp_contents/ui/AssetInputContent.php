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

namespace exp\exp_contents\ui;

use maze\document\AssetBundle;

class AssetInputContent extends AssetBundle {

    public $basePath = '@exp/exp_contents/assets';
    
    public $baseUrl = 'admin/expansion/exp_contents/ui/assets';
    
    public $js = ['js/inputcontent.js'];


}
