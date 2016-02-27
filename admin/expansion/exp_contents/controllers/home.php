<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;
use maze\helpers\FileHelper;
use maze\base\JsExpression;
use maze\helpers\Json;
use maze\table\Contents;
use maze\table\ContentsHome;
use maze\db\Query;

class Contents_Controller_Home extends Controller {

    public function accessFilter() {
        return [     
            'sort display' => ['contents', 'EDIT_HOME_CONTENTS']
        ];
    }

    public function actionDisplay() {

        $modelFilter = $this->form('FilterContent');

        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }
        
        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $model = Contents::find()->joinWith(['fields', 'type', 'route', 'lang', 'frontPage', 'accessRole.role'])
                    ->from(['c' => Contents::tableName()])
                    ->where(['c.expansion'=>'contents', 'c.home'=>1])->groupBy('c.contents_id');
            
            $modelFilter->queryBilder($model);
            
            return (new GridFormat([
                'id' => 'contents-home-grid',
                'model' => $model,
                'colonum' => 'h.sort',
                'colonumData' => [
                    'id' => '$data->contents_id',
                    'ordering' => function() {
                        return "<span class=\"sort-icon-handle\"></span>";
                    },
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'typename'=>function($data){
                        return $data->type->title;
                    },
                    'enabled',
                    'home',
                    'bundle',
                    'alias'=>function($data){
                        return $data->route->alias;
                    },
                    'roletitle'=>function($data){
                        $result =  array_map(function($val){
                            return $val->role->name;
                        }, $data->accessRole);
                        return empty($result) ? 'ВСЕ' : implode(', ',$result);
                    },
                    'isDelete'=>function($data){
                        if(!RC::app()->access->roles("contents", "DELETE_CONTENTS")){                           
                           return RC::app()->access->roles('contents', 'DELETE_SELF_CONTENTS', null, ['id_user'=>$data->id_user]);
                        }
                        return true;
                    },
                    'langtitle'=>function($data){
                        $lang = $data->lang ;
                        return $lang ? $lang->title : 'Все';
                    },
                    'title'=>function($data){
                        $fields = $data->fields;
                        $result = null;
                        foreach($fields as $fl){
                            if($fl->field_name == 'title'){
                               $field = (new Query())->from("{{%field_title_title}}")
                                       ->where(['entry_id'=>$data->contents_id, 'field_exp_id'=>$fl->field_exp_id])
                                       ->one();
                                $result = $field['title_value'];
                                break;
                            }
                        }
                        
                        return $result;
                    },
                    'contents_id'
                ]
            ]))->renderJson();
        }
        return parent::display(['modelFilter'=>$modelFilter]);
    }

    

    public function actionSort(array $sort) {
        
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        
        foreach ($sort as $cont) {
            if(isset($cont['contents_id']) && isset($cont['sort'])){
                if($obj = ContentsHome::findOne($cont['contents_id'])){
                    $obj->sort = $cont['sort'];
                    $obj->save();
                }
            }

        }  
    }

}

?>