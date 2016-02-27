<?php

namespace exp\exp_sitemap\model;

use maze\base\Model;
use Text;
use RC;
use maze\helpers\ArrayHelper;
use maze\table\Routes;
use ToolBarelem;
use admin\expansion\exp_sitemap\table\Sitemap;
use admin\expansion\exp_sitemap\table\SitemapVisits;
use admin\expansion\exp_sitemap\table\SitemapRobots;
use maze\helpers\DataTime;

class SitemapModel extends Model {

    protected $toolbar;
    protected $map;

    public function find($condition) {
        $this->map = RC::getDb()->cache(function($db) use ($condition) {
            return Sitemap::find()
                            ->from(['m' => Sitemap::tableName()])
                            ->joinWith(['route', 'link'])->groupBy('m.sitemap_id')
                            ->where($condition)
                            ->one();
        }, null, 'exp_sitemap');
        return $this->map;
    }

    public function getMap() {
        return $this->map;
    }

    public function getRoute() {
        return $this->map ? $this->map->route : null;
    }

    public function getLink() {
        return $this->map ? $this->map->link : null;
    }
    
    public function getSortParentLink() {
        $links = $this->getLink();
        if(!$links) return false;
        $result = [];
        
        foreach($links as $link){
            $linkPase = explode('/', trim($link->loc, '/'));
            if(count($linkPase) > 1){                
               $parent = implode('/', array_slice($linkPase, 0, -1));
               if(!isset($result[$parent])) $result[$parent] = [];
               $result[$parent][] = $link;
            }else{
                if(!isset($result['root'])) $result['root'] = [];
                $result['root'][] = $link;
            }
        }
        
        return $result;
    }
    
    public function getXMLContents(){
        $request = RC::app()->request;
        
        $html = '<?xml version="1.0" encoding="UTF-8"?>';
        
        $html .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" '.
      'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '.
      'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
        
        $link = $this->getLink();
        
        foreach($link as $l){
           if(!$l->enabled) continue;
           $html .= '<url>';
            $html .= '<loc>'.$request->getBaseUrl().'/'. str_replace(["&", "'", '"', '>', '<'], ["&amp;", "&apos;", "&quot;", "&gt;", "&lt;"], trim($l->loc, '/')).'</loc>' ;
            $html .= '<lastmod>'.DataTime::format($l->lastmod, 'c').'</lastmod>';
            $html .= '<changefreq>'.$l->changefreq.'</changefreq>';
            $html .= '<priority>'.$l->priority.'</priority>';
           $html .= '</url>';
        }
        $html .= '</urlset>';
        return $html;
    }

    public function saveSitemapVisits($type) {
        $visits = new SitemapVisits();
        $request = RC::app()->request;
        $robots = SitemapRobots::find()->all();
        $visits->sitemap_id = $this->map->sitemap_id;
        $visits->type = $type;
        $visits->ip = $request->getUserIP();
        $visits->agent = $request->getUserAgent();
        if ($robots) {
            foreach ($robots as $r) {
                if (mb_stripos($visits->agent, $r->search) !== false) {
                    $visits->robots_id = $r->robots_id;
                }
            }
        }



        return $visits->save();
    }

    

}
