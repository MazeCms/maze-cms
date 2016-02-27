<?php

namespace lib\fields\relations\view\table;

use RC;
use maze\base\Model;
use maze\helpers\ArrayHelper;
use root\expansion\exp_contents\model\ModelContent;

class Params extends Model {

    /**
     * @var int длина поля
     */
    public $modeview;
    
    public $cssclass;

    public function rules() {
        return [
            ['modeview', 'required'],
            ['cssclass', 'string']
        ];
    }

    public function getModelContent($data) {

        if (empty($data))
            return null;
        $id = array_map(function($val) {
            return $val->contents_id;
        }, $data);
        $bundle = $data[0]->field->settings->contentstype;
        
       
        
        $models =  RC::getDb()->cache(function($db) use ($bundle, $id) {
                    return ModelContent::findAll(['c.contents_id' => $id, 'c.bundle' => $bundle])->all();
                }, null, 'fw_fields');
        $result = $models ? ModelContent::createModel($models) : null;
        if(!$result) return null;
        $views = [];
        $modelByid = [];
        if (is_array($result)) {
            foreach ($result as $model) {
                $filedView = $model->getViewField($this->modeview);
                $viewsF = [];
                foreach ($filedView as $view) {
                    $viewsF[$view->view_name] = $view;
                }
                $views[$model->contents->contents_id] = $viewsF;
                $modelByid[$model->contents->contents_id] = $model;
            }
        }
        //  сортируем и выводит таблицу с связанными материалами даже если они повторяются
        $newViews = [];
        
        foreach($id as $i){
            if(isset($views[$i])){
                $newViews[] = ['v'=>$views[$i], 'id'=>$i];
            }
        }
        
        return ['views'=>$newViews, 'modelByid'=>$modelByid];
    }

}
