<?php

namespace admin\expansion\exp_sitemap\model;

use maze\base\Model;
use Text;
use RC;
use maze\helpers\ArrayHelper;
use maze\table\Routes;
use maze\table\AccessRole;
use maze\db\Query;
use admin\expansion\exp_sitemap\table\Sitemap;
use admin\expansion\exp_sitemap\table\SitemapLink;

class ModelMap extends Model {

    public $id;
    protected $routes;
    protected $map;
    protected $link = [];

    public function getMap() {
        if ($this->map == null) {
            if ($this->id) {
                $this->map = Sitemap::find()
                        ->from(['m' => Sitemap::tableName()])
                        ->joinWith(['route', 'link'])
                        ->where(['m.sitemap_id' => $this->id])
                        ->one();
            } else {
                $this->map = new Sitemap();
            }
        }
        return $this->map;
    }

    public function getRoutes() {
        if ($this->routes == null) {
            if ($this->id) {
                $this->routes = $this->map->route;
            } else {
                $this->routes = new Routes;
                $this->routes->expansion = 'sitemaps';
            }
        }

        return $this->routes;
    }

    public function getLink() {
        if ($this->link == null) {
            if ($this->id) {
                $this->link = $this->map->link;
            } else {
                $this->link = [];
            }
        }
        return $this->link;
    }

    public function loadAll($data) {

        $this->getMap()->load($data);
        $this->getRoutes()->load($data);
        if (isset($data['SitemapLink'])) {
            foreach ($data['SitemapLink'] as $linkData) {
                $link = new SitemapLink();
                $link->setAttributes($linkData);
                $this->link[] = $link;
            }
        }
    }

    public function getAllModel() {
        $models = [];

        $models[] = $this->getMap();
        $models[] = $this->getRoutes();

        return $models;
    }

    public function getUrl(){
        if($this->getMap()->isNewRecord) return false;

        return RC::getRouter(RC::ROUTERSITE)->createRoute(['/sitemap/controller/sitemap/default', ['sitemap_id' =>$this->getMap()->sitemap_id]]);
    }
    public function saveSite() {
        $transaction = RC::getDb()->beginTransaction();
        try {
            if (!$this->getRoutes()->validate() || !$this->getRoutes()->save()) {
                throw new \Exception("Ошибка сохранения маршрута карты сайта");
            }
            
            $this->getMap()->routes_id = $this->getRoutes()->routes_id;
            
            if (!$this->getMap()->validate() || !$this->getMap()->save()) {
                throw new \Exception("Ошибка сохранения модели карты сайта");
            }

            

            SitemapLink::deleteAll(['sitemap_id' => $this->getMap()->sitemap_id]);

            if ($this->getLink()) {
                foreach ($this->getLink() as $link) {
                    $link->sitemap_id = $this->getMap()->sitemap_id;
                    if ($link->validate()) {
                        if (!$link->save()) {
                           
                        }
                    }
                }
            }
            RC::getCache("exp_sitemap")->clearTypeFull();
            $transaction->commit();
        } catch (\Exception $ex) {

            $this->getMap()->addError('title', $ex->getMessage() . $ex->getFile() . $ex->getLine());
            $transaction->rollBack();
            return false;
        }
        return true;
    }
    
    public function deleteMap($id) {
        $transaction = RC::getDb()->beginTransaction();
        try {
            
            $maps = Sitemap::find()
                        ->from(['m' => Sitemap::tableName()])
                        ->joinWith(['route'])
                        ->where(['m.sitemap_id' => $id])
                        ->all();
            
            foreach($maps as $map){
                $map->route->delete();
                $map->delete();
            }
            RC::getCache("exp_sitemap")->clearTypeFull();
            $transaction->commit();
        } catch (\Exception $ex) {
            
            $this->getMap()->addError('title', $ex->getMessage() . $ex->getFile() . $ex->getLine());
            $transaction->rollBack();
            return false;
        }
        return true;
    }

}
