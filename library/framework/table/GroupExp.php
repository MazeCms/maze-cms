<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace maze\table;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class GroupExp extends \maze\db\ActiveRecord {
   
    public static function tableName()
    {       
        return '{{%group_exp}}';
    }    
    
    
}
