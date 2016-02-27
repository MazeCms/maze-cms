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

class AssetDropdown extends AssetBundle{
    
    public $basePath = '@lib/jquery/select/dropdown';
    
    public $baseUrl = '/library/jquery/select/dropdown';
    
    public $js = ['jquery.dd.js'];
    
    public $css = ['css/dd.css', 'css/flags.css'];
}
