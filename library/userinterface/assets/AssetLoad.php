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

class AssetLoad extends AssetBundle{
    
    public $basePath = '@lib/jquery/overlay/preloader';
    
    public $baseUrl = '/library/jquery/overlay/preloader';
    
    public $js = ['jquery-overlay-preloader.js'];
    
    public $css = ['jquery-overlay-preloader.css'];
  
}
