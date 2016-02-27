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

class AssetBootflat extends AssetBundle{
    
    public $basePath = '@lib/jquery/bootflat';
    
    public $baseUrl = '/library/jquery/bootflat';
    
    public $js = ['js/site.min.js'];
    
    public $css = ['css/site.min.css'];
    
    public $jsOptions = ['sort'=>95];
}
