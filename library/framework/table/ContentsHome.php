<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;

use maze\table\Expansion;
use maze\db\Expression;
use Text;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class ContentsHome extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%contents_home}}';
    }
    
    public function rules() {
        return [
            [['contents_id', 'sort'], 'number'],
            [['expansion'], 'string', 'min'=>3, 'max'=>255]
        ];
    }

}
