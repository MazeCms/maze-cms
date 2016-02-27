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

class AssetBootstrap extends AssetBundle{
    
    public $basePath = '@lib/jquery/bootstrap-3.2.0';
    
    public $baseUrl = '/library/jquery/bootstrap-3.2.0';
    
    public $js = ['js/bootstrap.js'];
    
    public $css = ['css/bootstrap.css'];
    
    public $jsOptions = ['sort'=>95];
}
