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

class AssetDialog extends AssetBundle{
    
    public $basePath = '@lib/jquery/dialog/maze';
    
    public $baseUrl = '/library/jquery/dialog/maze';
    
    public $js = ['jquery-maze-dialog-1.0.js'];
    
    public $css = ['jquery-maze-dialog-1.0.css'];
  
}
