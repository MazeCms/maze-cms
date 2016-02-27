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

class AssetHighcharts extends AssetBundle{
    
    public $basePath = '@lib/javascript/highcharts';
    
    public $baseUrl = '/library/javascript/highcharts';
    
    
    public $js = [
        'js/highcharts.js',
        'js/modules/data.js',
        'js/highcharts-3d.js'
    ];
    
    public $jsOptions = ['sort'=>96];
    
    public $depends = ['ui\assets\AssetJquery'];
    
    public function init() {
        parent::init();
        if(!empty($this->publishOptions)){
            foreach($this->publishOptions as $get){
                
                if(isset($this->$get)){
                    $this->$get;
                }
            }
        }
    }
    /**
     * Диаграмма ввиде столбцов
     */
    public function getDrilldown() {
        $this->js[] = 'js/modules/drilldown.js';
    }
}
