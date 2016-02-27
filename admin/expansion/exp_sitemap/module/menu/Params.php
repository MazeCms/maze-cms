<?php
namespace admin\expansion\exp_sitemap\module\menu;

use RC;
use maze\base\Model;
use maze\table\Menu;

class Params extends Model{
   

    public $home;
    
    public $url;
    
    public $menu;

    public function formName(){
        return "Menu";
    }
    
    public function rules() {
      return [
          [['home', 'url', 'menu'],'required'],
          [['home', 'url'],'boolean']
      ];
    }

    public function getImport() {
        $result = [];
        $components = RC::app()->getComponent('sitemap');
         
        $menu = Menu::find()->from(['m' => Menu::tableName()])
                    ->joinWith(['expansion', 'route', 'accessRole' => function($query){
                            $query->andWhere('ar.id_role is null');
                    }])
                    ->where(['m.enabled' => '1'])
                    ->andWhere(['m.time_active' => null])
                    ->andWhere(['m.time_inactive' =>null])
                    ->andWhere(['m.id_group'=>$this->menu])        
                    ->orderBy('m.id_group, m.parent, m.ordering, m.id_exp')
                   ->all();
        $sortByID = [];            
        foreach($menu as $item){
            $sortByID[$item->id_menu] = $item->attributes;
            $sortByID[$item->id_menu]['alias'] = $item->route->alias;
        }
        
        foreach($sortByID as $item){
            $path = $this->getPathMenu($item, $sortByID);
            if(!$path) continue;
            $result[$path] = [
                'link_id'=>null,
                'sitemap_id'=>null,
                'enabled'=>1,
                'id'=>$item['id_menu'],
                'title'=>$item['name'],
                "expansion"=>"menu",
                "loc"=>$path,
                "lastmod"=>date('Y-m-d H:i:s'),
                "changefreq"=>$components->config->getVar('changefreq'),
                "priority"=>$components->config->getVar('priority'),
            ];
        }
        
        return $result;           
    }
    
     protected function getPathMenu($item, $menu){
        $pathResult = null;
        $router = RC::getRouter(RC::ROUTERSITE);
        if($item['home'] && !$this->home){
            return false;
        }
        if ($item['home']) {
                $pathResult = '/';
            } else {
                if ($item['typeLink'] == 'expansion' ) {
                    $path = [$item['alias']];
                    $parent = $item['parent'];
                    
                    while (isset($menu[$parent])) {
                        $item = $menu[$parent];
                        $path[] = $item['alias'];
                        $parent = $item['parent'];
                    }
                    $pathResult = implode('/', array_reverse($path));
                } elseif ($item['typeLink'] == 'alias' || $item['typeLink'] == 'url' ) {
                    if ($item['typeLink'] == 'alias') {
                        $pathResult = $router->createRoute($item['paramLink']);
                    } else {
                        if(!$this->url) return false;
                        $pathResult = $item['paramLink'];
                    }
                } else {
                    $pathResult = "#";
                }
            }
        return $pathResult;
    }
    
}
