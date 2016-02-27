<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace maze\table;

use maze\db\Expression;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Grids extends \maze\db\ActiveRecord {
   
    public static function tableName()
    {       
        return '{{%grids}}';
    }    
    
    public function rules() {
        return [
            [['sortfild','sortorder','page', 'colHide', 'sortCol', 'groupField'], 'safe']

        ];
    }
    public function beforeSave($insert)
    {
       $this->colHide = json_encode($this->colHide); 
       $this->sortCol = json_encode($this->sortCol); 
       $this->groupField = json_encode($this->groupField); 
       
       if ($this->isNewRecord) {              
                $this->id_user = \Access::instance()->getUid();
                $this->date_created = new Expression("NOW()");
        }
       
       
       return true;
    }
    
    public function afterFind()
    {
       $this->colHide = json_decode($this->colHide, true); 
       $this->sortCol = json_decode($this->sortCol, true); 
       $this->groupField = json_decode($this->groupField, true); 
    }  
    
}
