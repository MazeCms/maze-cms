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

class AssetTreeTable extends AssetBundle{
    
    public $basePath = '@lib/jquery/treetable/';
    
    public $baseUrl = '/library/jquery/treetable/';
    
    public $js = ['jquery.treetable.js'];
    
    public $css = ['jquery.treetable.css', 'jquery.treetable.theme.default.css'];

}
