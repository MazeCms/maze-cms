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

class AssetAddImages extends AssetBundle{
    
    public $basePath = '@lib/jquery/images/addimages';
    
    public $baseUrl = '/library/jquery/images/addimages';
    
    public $js = ['jquery-add-images-1.1.js'];
    
    public $css = ['jquery-add-images-1.1.css'];

  
}
