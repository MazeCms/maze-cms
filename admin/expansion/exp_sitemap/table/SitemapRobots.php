<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace admin\expansion\exp_sitemap\table;

use Text;
use maze\db\Expression;

/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class SitemapRobots extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%sitemap_robots}}';
    }
    
    public function rules() {
        return [
            [['title', 'search'], 'required'],          
            [['title', 'search'], 'string', 'max'=>255],
            ['images',  'string', 'max'=>1000]
        ];
    }
    

    public function attributeLabels() {
        return[
            'title'=>Text::_('EXP_SITEMAP_ROBOTS_LABEL_TITLE'),
            'images'=>Text::_('EXP_SITEMAP_ROBOTS_LABEL_IMAGES'),
            'search'=>Text::_('EXP_SITEMAP_ROBOTS_LABEL_SEARCH')
        ];
    }

}
