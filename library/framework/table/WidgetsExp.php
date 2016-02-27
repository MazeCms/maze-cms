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
class WidgetsExp extends ActiveRecord {
   
    public static function tableName()
    {       
        return '{{%widgets_exp}}';
    }    
    

    
}
