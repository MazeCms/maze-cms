<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace admin\expansion\exp_sitemap\table;

use Text;
use maze\table\Expansion;
use maze\db\Expression;
use maze\helpers\ArrayHelper;
use admin\expansion\exp_sitemap\table\SitemapRobots;
use admin\expansion\exp_sitemap\table\Sitemap;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class SitemapVisits extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%sitemap_visits}}';
    }
    public function rules() {
        return [
            [['type', 'sitemap_id', 'ip', 'agent'], 'required'],
            ['robots_id', 'number']
        ];
    }
    
    public function beforeSave($insert) {
        
        if($this->isNewRecord){             
            $this->date_visits = new Expression('NOW()');
        }
        
        return true;
    }
    
    public function getRobot()
    {
        return $this->hasOne(SitemapRobots::className(), ['robots_id' => 'robots_id'])->from(['sr'=>SitemapRobots::tableName()]);
    }
    
    public function getMap()
    {
        return $this->hasOne(Sitemap::className(), ['sitemap_id' => 'sitemap_id'])->from(['m'=>Sitemap::tableName()]);
    }
 
    
    public function attributeLabels() {
        return[
            'type'=>Text::_('EXP_SITEMAP_VISITS_LABEL_TYPE'),
            'ip'=>Text::_('EXP_SITEMAP_VISITS_LABEL_IP'),
            'agent'=>Text::_('EXP_SITEMAP_VISITS_LABEL_AGENT')
        ];
    }

}
