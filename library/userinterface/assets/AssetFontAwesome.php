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

class AssetFontAwesome extends AssetBundle{
    
    public $basePath = '@lib/css/font-awesome';
    
    public $baseUrl = '/library/css/font-awesome';
  
    public $css = ['css/font-awesome.css'];
    
    public $jsOptions = ['sort'=>94];
}
