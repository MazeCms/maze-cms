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

namespace wid\wid_slidercontents\assets;

use maze\document\AssetBundle;

class AssetCarusel extends AssetBundle {

    
    public $basePath = '@wid/wid_slidercontents/js/owl-carousel';    
    public $baseUrl = '/widgets/wid_slidercontents/js/owl-carousel';
    
    public $js = [        
        'owl.carousel.min.js',
    ];
    
    public $css = ['owl.carousel.css', 'owl.theme.css'];

}
