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
use admin\expansion\exp_sitemap\table\SitemapLink;
use admin\expansion\exp_sitemap\table\SitemapVisits;
use maze\table\Routes;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Sitemap extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%sitemap}}';
    }
    public function rules() {
        return [
            [['title'], 'required'],
            ['title', 'string', 'min'=>3, 'max'=>255],
            [['enable_xml', 'enable_html'], 'boolean'],
            [['description', 'params'], 'safe']
        ];
    }
   
    
    public function getLink() {
        return $this->hasMany(SitemapLink::className(), ['sitemap_id' => 'sitemap_id'])->from(['sl'=>SitemapLink::tableName()]);
    }

    public function getVisits(){
        return $this->hasMany(SitemapVisits::className(), ['sitemap_id' => 'sitemap_id'])->from(['sv'=>SitemapVisits::tableName()]);
    }
    
    public function getVisitsXml(){
        return $this->hasOne(SitemapVisits::className(), ['sitemap_id' => 'sitemap_id'])
                ->from(['svxml'=>SitemapVisits::tableName()])
                ->andOnCondition(['svxml.type'=>'xml'])
                ->orderBy('svxml.date_visits DESC');
    }
    
    public function getVisitsHtml(){
        return $this->hasOne(SitemapVisits::className(), ['sitemap_id' => 'sitemap_id'])
                ->from(['svhtml'=>SitemapVisits::tableName()])
                ->andOnCondition(['svhtml.type'=>'html'])
                ->orderBy('svhtml.date_visits DESC');
    }
    
    
    public function getRoute(){
        return $this->hasOne(Routes::className(), [
            'routes_id' => 'routes_id', 
            ])->from(["route"=>Routes::tableName()]);
    }
    
    public function beforeSave($insert) {
        if($this->params){
            $this->params = serialize($this->params);
        }
        if($this->isNewRecord){             
            $this->date_create = new Expression('NOW()');
        }
        $this->date_update = new Expression('NOW()');
        
        return true;
    }
    
    public function afterFind() {
         if (!empty($this->params)) {
            $this->params = unserialize($this->params);
        }
    }
    
     public static function getList()
    {
       return ArrayHelper::map(static::find()->asArray()->all(), 'sitemap_id', 'title'); 
    }

    
    

    public function attributeLabels() {
        return[
            'title'=>Text::_('EXP_SITEMAP_LABEL_TITLE'),
            'enable_xml'=>Text::_("EXP_SITEMAP_LABEL_ENABLE_XML"),
            'enable_html'=>Text::_("EXP_SITEMAP_LABEL_ENABLE_HTML"),
            'description'=>Text::_("EXP_SITEMAP_LABEL_DESCRIPTION")
        ];
    }

}
