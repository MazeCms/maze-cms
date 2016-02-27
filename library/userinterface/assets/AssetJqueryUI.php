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

class AssetJqueryUI extends AssetBundle{
    
    public $basePath = '@lib/jquery/jqueryui';
    
    public $baseUrl = '/library/jquery/jqueryui';
    
    public $js = ['1.9.2/jquery-ui-1.9.2.min.js',
        'all/jquery-ui-timepicker-addon.js',
        'all/datepicker-lang/jquery.ui.datepicker-ru-RU.js', 
        'all/timepicker-lang/jquery-ui-timepicker-ru-RU.js'
        
    ];
    
    public $css = ['theme/smoothness/jquery-ui-smoothness.css', 'all/jquery-ui-timepicker-addon.css'];
    
    public $jsOptions = ['sort'=>98];
    
    public $cssOptions = ['sort'=>96];
    
    public $depends = ['ui\assets\AssetJquery'];
}
