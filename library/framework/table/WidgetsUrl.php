<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace maze\table;

use maze\db\ActiveRecord;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class WidgetsUrl extends ActiveRecord {
   
    public static function tableName()
    {       
        return '{{%widgets_url}}';
    }
    
    public function rules() {
        return [
            [['id_wid', 'method'], "required"],
            ['name',"required", 'on'=>['get', 'post']],
            ['value',"required", 'on'=>'url'],
            ['name', 'string', 'max' => 100, 'on'=>['get', 'post']],
            ['value', 'string', 'max' => 1000],
            ['visible', 'boolean'],
            ['visible','default', 'value'=>1],
            [['sort', 'id_wid'], 'number'],
            ['method', 'in', 'range'=>['get', 'post', 'url']]
        ];
    }
    

    
}
