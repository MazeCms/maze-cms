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

class AssetGrid extends AssetBundle{
    
    public $basePath = '@lib/jquery/grid';
    
    public $baseUrl = '/library/jquery/grid';
    
    public $js = ['maze-grid-1.0.js'];
    
    public $css = ['maze-grid-1.0.css'];
    
    public $depends = ['ui\assets\AssetMenu', 'ui\assets\AssetScrollbar'];
  
}
