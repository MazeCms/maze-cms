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

class AssetTree extends AssetBundle{
    
    public $basePath = '@lib/jquery/menu/jstreenew';
    
    public $baseUrl = '/library/jquery/menu/jstreenew';
    
    public $js = ['jstree.min.js'];
    
    public $css = ['themes/default/style.css'];

}
