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

class AssetSwitchBtn extends AssetBundle{
    
    public $basePath = '@lib/jquery/checkbox/maze-switch';
    
    public $baseUrl = '/library/jquery/checkbox/maze-switch';
    
    public $js = ['maze-switch.js'];
    
    public $css = ['maze-switch.css'];

}
