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

class AssetScrollbar extends AssetBundle{
    
    public $basePath = '@lib/jquery/scroll/';
    
    public $baseUrl = '/library/jquery/scroll/';
    
    public $js = ['jquery.mCustomScrollbar.concat.min.js'];
    
    public $css = ['jquery.mCustomScrollbar.css'];
  
}
