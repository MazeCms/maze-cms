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

class AssetBackbone extends AssetBundle{
    
    public $basePath = '@lib/javascript/backbone';
    
    public $baseUrl = '/library/javascript/backbone';
    
    public $js = [
        'underscore.js',
        'backbone.js',       
        ];
    public $jsOptions = ['sort' => 92];
    
}
