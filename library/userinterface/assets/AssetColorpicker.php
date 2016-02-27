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

class AssetColorpicker extends AssetBundle{
    
    public $basePath = '@lib/jquery/colorpicker';
    
    public $baseUrl = '/library/jquery/colorpicker';
    
    public $js = [        
        'js/layout.js',
        'js/utils.js',        
        'js/eye.js',
        'js/colorpicker.js'
    ];
    
    public $css = ['css/colorpicker.css'];
}
