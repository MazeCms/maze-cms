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

class AssetPopover extends AssetBundle{
    
    public $basePath = '@lib/jquery/popover';
    
    public $baseUrl = '/library/jquery/popover';
    
    public $js = ['maze-popover-1.0.js'];
    
    public $css = ['css/maze-popover-1.0.css'];
  
}
