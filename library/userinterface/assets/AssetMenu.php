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

class AssetMenu extends AssetBundle{
    
    public $basePath = '@lib/jquery/contextmenu/';
    
    public $baseUrl = '/library/jquery/contextmenu/';
    
    public $js = ['maze-context-menu-1.0.js'];
    
    public $css = ['maze-context-menu-1.0.css'];
  
}
