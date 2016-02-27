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

class AssetInputmask extends AssetBundle{
    
    public $basePath = '@lib/jquery/inputmask';
    
    public $baseUrl = '/library/jquery/inputmask';
    
    public $js = ['jquery.maskedinput.min.js'];
  
}
