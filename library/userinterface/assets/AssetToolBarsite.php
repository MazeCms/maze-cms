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

class AssetToolBarsite extends AssetBundle{
    
    public $basePath = '@lib/jquery/toolbarsite';
    
    public $baseUrl = '/library/jquery/toolbarsite';
    
    public $js = ['tool-bar-site.js', 'tool-bar-elem.js'];
    
    public $css = ['css/tool-bar-site.css', 'css/tool-bar-elem.css'];
    
    public $jsOptions = ['sort'=>2];
    
    public $cssOptions = ['sort'=>2];
    
    public $depends = [
        'ui\assets\AssetJqueryUI', 
        'ui\assets\AssetDialog', 
        'ui\assets\AssetSwitchBtn', 
        'ui\assets\AssetMenu', 
        'ui\assets\AssetCookie', 
        'ui\assets\AssetLoad'];
}
