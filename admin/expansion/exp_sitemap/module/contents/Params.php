<?php

namespace admin\expansion\exp_sitemap\module\contents;

use RC;
use maze\base\Model;
use maze\table\Contents;
use maze\table\DictionaryTerm;
use maze\fields\FieldHelper;

class Params extends Model {

    public $type;
    
    public $category;
    
    public $home;

    public function formName() {
        return "Contents";
    }

    public function rules() {
        return [
            [['type'], 'required'],
            [['category', 'home'], 'safe']
        ];
    }

    public function getImport() {
        $result = [];
        $components = RC::app()->getComponent('sitemap');
        $router = RC::getRouter(RC::ROUTERSITE);
        if (is_array($this->type) && !empty($this->type)) {
            $contents = Contents::find()->from(['c' => Contents::tableName()])
                    ->joinWith(['route', 'accessRole' => function($qu) {
                            $qu->andOnCondition(['ar.exp_name' => 'contents'])
                            ->andOnCondition(['ar.key_role' => 'content']);
                        }])
                    ->where(['c.expansion' => 'contents', 'c.enabled' => 1])
                    ->andWhere(['or', 'c.time_active<=NOW()', 'c.time_active is null'])
                    ->andWhere(['or', 'c.time_inactive>=NOW()', 'c.time_inactive is null'])
                    ->andWhere(['ar.id_role' => null])
                    ->andWhere(['c.bundle' => $this->type])
                    ->groupBy('c.contents_id')
                    ->orderBy('c.sort')
                    ->all();

            foreach ($contents as $item) {
                $title = null;
                $fields = FieldHelper::findAll(['expansion' => 'contents', 'bundle' => $item->bundle, 'active'=>1]);
                foreach($fields as $field){                    
                    if ($field->field_name == 'title') {
                        $field->findData(['entry_id'=>$item->contents_id]);
                        if($field->data){
                            $title = $field->data[0]->title_value;
                        }
                        break;
                    }
                }
                
                $path = $router->createRoute(['/contents/controller/contents/default', ['contents_id' => $item->contents_id]]);
                
                $result[$path] = [
                    'link_id'=>null,
                    'sitemap_id'=>null,
                    'enabled'=>1,
                    'id'=>$item->contents_id,
                    'title'=>$title,
                    "expansion" => "contents",
                    "loc" => $path,
                    "lastmod" => $item->date_create,
                    "changefreq" =>$components->config->getVar('changefreq'),
                    "priority" =>$components->config->getVar('priority'),
                ];
            }
        }
      
        if (is_array($this->category) && !empty($this->category)) {
           
            $terms = DictionaryTerm::find()                        
                        ->from(['dt'=>DictionaryTerm::tableName()])
                        ->joinWith(['route', 'accessRole'=>function($qu){
                           $qu->andOnCondition(['ar.exp_name'=>'dictionary'])
                                ->andOnCondition(['ar.key_role'=>'term']);
                        }])
                        ->where(['dt.expansion' => 'dictionary',  'dt.enabled'=>1])
                        ->andWhere(['or', 'dt.time_active<=NOW()', 'dt.time_active is null'])
                        ->andWhere(['or', 'dt.time_inactive>=NOW()', 'dt.time_inactive is null'])   
                        ->andWhere(['ar.id_role'=>null])        
                        ->andWhere(['dt.bundle' => $this->category])
                        ->groupBy('dt.term_id')
                        ->orderBy('dt.sort')
                        ->all();
     
            foreach ($terms as $item) {
                $title = null;
                $fields = FieldHelper::findAll(['expansion' => 'dictionary', 'bundle' => $item->bundle, 'active'=>1]);
                foreach($fields as $field){                    
                    if ($field->field_name == 'title') {
                        $field->findData(['entry_id'=>$item->term_id]);
                        if($field->data){
                            $title = $field->data[0]->title_value;
                        }
                        break;
                    }
                }
                
                $path = $router->createRoute(['/contents/category/category/default', ['term_id' => $item->term_id]]);
                
                $result[$path] = [
                    'link_id'=>null,
                    'sitemap_id'=>null,
                    'enabled'=>1,
                    'id'=>$item->term_id,
                    'title'=>$title,
                    "expansion" => "dictionary",
                    "loc" => $path,
                    "lastmod" => $item->date_create,
                    "changefreq" => $components->config->getVar('changefreq'),
                    "priority" => $components->config->getVar('priority'),
                ];
            }            
        }
        
        if(!$this->home){
            if(isset($result['/'])){
                unset($result['/']);
            }
        }
        
        return $result;
    }

}
        