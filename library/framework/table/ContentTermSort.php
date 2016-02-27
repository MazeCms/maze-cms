<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;

use maze\db\Expression;
use Text;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class ContentTermSort extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%content_term_sort}}';
    }
    public function rules() {
        return [
            [['contents_id', 'term_id'], 'required'],            
            [['contents_id', 'term_id','sort'], 'number']
        ];
    }


}
