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

class AssetCodeMirror extends AssetBundle{
    
    public $basePath = '@lib/javascript/CodeMirror';
    
    public $baseUrl = '/library/javascript/CodeMirror';
    
    public $js = [       
        'addon/fold/xml-fold.js',
        'addon/fold/comment-fold.js',        
        'addon/fold/brace-fold.js',
        'addon/fold/foldgutter.js',
        'addon/fold/foldcode.js',
        'mode/php/php.js',
        'mode/clike/clike.js',
        'mode/css/css.js',
        'mode/javascript/javascript.js',
        'mode/xml/xml.js',
        'mode/htmlmixed/htmlmixed.js',
        'addon/edit/matchbrackets.js',
        'lib/codemirror.js', 
        ];
    
    public $css = [        
        'addon/fold/foldgutter.css',
        'lib/codemirror.css'
        ];
  
}
