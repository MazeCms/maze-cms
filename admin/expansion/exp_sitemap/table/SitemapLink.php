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
use maze\table\InstallApp;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class SitemapLink extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%sitemap_link}}';
    }
    public function rules() {
        return [
            [['title', 'expansion', 'sitemap_id', 'title', 'enabled', 'id', 'loc', 'lastmod', 'changefreq', 'priority'], 'required'],
            ['loc', 'string', 'min'=>1, 'max'=>2000],
            ['enabled', 'boolean'],
        ];
    }
    
    public function getInstallApp() {
        return $this->hasOne(InstallApp::className(), ['expansion' => 'expansion'])
                        ->from(["ia" => InstallApp::tableName()]);
    }

    public function attributeLabels() {
        return[
            'title'=>Text::_('EXP_SITEMAP_LINK_LABEL_TITLE'),
            'enabled'=>Text::_("EXP_SITEMAP_LINK_LABEL_ENABLE"),
            'expansion'=>Text::_("EXP_SITEMAP_LINK_LABEL_EXPANSION"),
            'loc'=>Text::_("EXP_SITEMAP_LINK_LABEL_LOC"),
            'lastmod'=>Text::_("EXP_SITEMAP_LINK_LABEL_LASTMOD"),
            'changefreq'=>Text::_("EXP_SITEMAP_LINK_LABEL_CHANGEFREQ"),
            'priority'=>Text::_("EXP_SITEMAP_LABEL_DESCRIPTION")
        ];
    }

}
