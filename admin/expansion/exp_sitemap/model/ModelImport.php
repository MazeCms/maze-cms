<?php

namespace admin\expansion\exp_sitemap\model;

use maze\base\Model;
use Text;
use RC;
use maze\helpers\ArrayHelper;
use maze\db\Query;
use maze\table\InstallApp;

class ModelImport extends Model {

    protected $types;
    
    public $params;

    public function getListExp() {
        $result = [];
        $types = $this->getTypes();
        foreach ($types as $type) {
            $result[$type] = RC::getConf(["type" => "expansion", "name" => $type])->get('name');
        }
        
        if($this->params && isset($this->params['sort'])){
            $resultOld = $result;
            $result = [];
            foreach($this->params['sort'] as $type){
                $result[$type] = $resultOld[$type];
            }
        }

        return $result;
    }
    
    public function getEnableType($type) {

        if($this->params && isset($this->params['enable'][$type])){
            return true;
        }
        return false;
    }

    public function getTypes() {
        if ($this->types == null) {
            $dir = scandir(RC::getAlias('@admin/expansion/exp_sitemap/module'));
            $this->types = array_filter($dir, function($val) {
                if ($val == '..' || $val == '.' || is_file(RC::getAlias('@admin/expansion/exp_sitemap/module/' . $val)))
                    return false;
                if (!InstallApp::find()->where(["type" => "expansion", 'name' => $val])->exists())
                    return false;
                return $val;
            });
        }

        return $this->types;
    }

    public function getModelModule($name) {
        try {
            $model = RC::createObject('admin\expansion\exp_sitemap\module\\' . $name . '\Params');
            if($this->params){
               $model->load($this->params); 
            }
            return $model;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getFormModule($name) {
        try {
            $xml = new \XMLConfig(RC::getAlias('@admin/expansion/exp_sitemap/module/' . $name . '/meta.options.xml'));
        } catch (\Exception $ex) {
            return false;
        }

        return $xml;
    }

    public function getImport($types, $data) {

        $result = [];
        if (!is_array($types)) {
            $types = [$types];
        }

        foreach ($types as $type) {
     
            $model = $this->getModelModule($type);
            if ($model) {
                $model->load($data);
                if ($model->validate()) {
                    $importResult =  $model->import;
                    if($importResult){
                        $result = array_merge($result, $importResult);
                    }                    
                }
            }
        }

        return $result;
    }

}
