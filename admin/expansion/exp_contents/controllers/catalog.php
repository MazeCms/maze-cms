<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use maze\table\ContentType;
use maze\table\FieldExp;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\fields\FieldHelper;
use maze\table\DictionaryTerm;
use maze\db\Query;
use maze\table\Contents;
use maze\table\ContentTermSort;

class Contents_Controller_Catalog extends Controller {
    
    public function accessFilter() {
        return [     
            'dispaly dict term sort' =>["contents", "EDIT_CONTENTS"]
        ];
    }

    public function actionDisplay() {

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $catalog = FieldExp::find()
                    ->from(['fe' => FieldExp::tableName()])
                    ->joinWith(['typeFields'])
                    ->where(['fe.expansion' => 'contents', 'f.type' => 'term'])
                    ->all();
            $idCatalog = [];

            foreach ($catalog as $cat) {
                if (isset($cat->param['dictionary'])) {
                    if (in_array($cat->param['dictionary'], $idCatalog)) {
                        continue;
                    }

                    $idCatalog[] = $cat->param['dictionary'];
                }
            }

            $model = ContentType::find()
                    ->from(['ct' => ContentType::tableName()])
                    ->joinWith(['term' => function($q) {
                            $q->orderBy('dt.parent, dt.sort')->groupBy('dt.term_id');
                        }, 'term.fields'])
                    ->where(['ct.expansion' => 'dictionary', 'ct.bundle' => $idCatalog]);


            return (new GridFormat([
                'id' => 'contents-catalog-grid',
                'model' => $model,
                'colonum' => 'ct.bundle',
                'colonumData' => [
                    'bundle',
                    'title' => function($data) {
                        return Html::a($data->title, ['/admin/contents/catalog', ['run' => 'dict', 'bundle' => $data->bundle]]);
                    },
                            'description'
                        ]
                            ]))->renderJson();
        }

        return parent::display();
    }

    public function actionDict($bundle) {
        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

             $model = DictionaryTerm::find()
                    ->joinWith(['fields', 'type', 'route', 'lang', 'accessRole.role'])
                    ->from(['dt' => DictionaryTerm::tableName()])
                    ->where(['dt.expansion'=>'dictionary', 'dt.bundle'=>$bundle])
                    ->groupBy('dt.term_id');
            
            return (new GridFormat([
                'id' => 'contents-catalog-dict-grid',
                'model' => $model,
                'mode' => 'tree',
                'link' => 'parent',               
                'colonum' => 'dt.sort',
                'colonumData' => [
                    'id' => '$data->term_id',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'typename'=>function($data){
                        return $data->type->title;
                    },
                    'enabled',
                    'bundle',
                    'parent',        
                    'typetree' => '"term"',
                    'alias'=>function($data){
                        return $data->route->alias;
                    },
                    'roletitle'=>function($data){
                        $result =  array_map(function($val){
                            return $val->role->name;
                        }, $data->accessRole);
                        return empty($result) ? Text::_('LIB_USERINTERFACE_FIELD_ALL') : implode(', ',$result);
                    },  
                    'langtitle'=>function($data){
                        $lang = $data->lang ;
                        return $lang ? $lang->title : Text::_('LIB_USERINTERFACE_FIELD_ALL');
                    },
                    'title'=>function($data) use ($bundle){
                        $fields = $data->fields;
                        $result = null;
                        foreach($fields as $fl){
                            if($fl->field_name == 'title'){
                               $field = (new Query())->from("{{%field_title_title}}")
                                       ->where(['entry_id'=>$data->term_id, 'field_exp_id'=>$fl->field_exp_id])
                                       ->one();
                                $result = $field['title_value'];
                                break;
                            }
                        }
                        
                        return Html::a($result, ['/admin/contents/catalog', ['run' => 'term', 'bundle'=>$bundle, 'term_id' => $data->term_id]]);
                    },
                    'term_id'
                ]
            ]))->renderJson();
        }
        
        return $this->renderPart('dict', null, null, ['bundle'=>$bundle]);
    }

    public function actionTerm($bundle, $term_id) {
        
        $modelFilter = $this->form('FilterContent');

        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }
        
        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
           
            
            $catalog = FieldExp::find()
                    ->from(['fe' => FieldExp::tableName()])
                    ->joinWith(['typeFields'])
                    ->where(['fe.expansion' => 'contents', 'f.type' => 'term'])
                    ->all();
       
            $table = [];
            foreach ($catalog as $cat) {
                if (isset($cat->param['dictionary'])) {
                    if ($cat->param['dictionary'] !== $bundle || in_array($cat->param['dictionary'], $table)) {
                        continue;
                    }
                    $table[] = $cat->field_name;
                }
            }


            $model = Contents::find()->joinWith(['fields', 'type', 'route', 'lang', 'accessRole.role', 'termSort'=>function($qu) use ($term_id){
                $qu->andOnCondition(['ts.term_id'=>$term_id]);
            }])
                ->from(['c'=>Contents::tableName()])                    
                ->where(['c.expansion'=>'contents'])->groupBy('c.contents_id');
            
            $modelFilter->queryBilder($model);
            
            foreach($table as $t){
                $tableName = '{{%field_term_'.$t.'}}';
                $model->innerJoin($tableName, $tableName.'.entry_id = c.contents_id')
                        ->andWhere([$tableName.'.term_id'=>$term_id]);
            }
            
            if(!RC::app()->access->roles("contents", "EDIT_CONTENTS") && RC::app()->access->roles('contents', 'EDIT_SELF_CONTENTS')){
                $model->andWhere(['c.id_user'=>RC::app()->access->getUid()]);
            }
            
            return (new GridFormat([
                'id' => 'contents-catalog-term-grid',
                'model' => $model,
                'colonum' => 'ts.sort',
                'colonumData' => [
                    'id' => '$data->contents_id',
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
                        return empty($result) ? Text::_('LIB_USERINTERFACE_FIELD_ALL') : implode(', ',$result);
                    },
                    'isDelete'=>function($data){
                        if(!RC::app()->access->roles("contents", "DELETE_CONTENTS")){                           
                           return RC::app()->access->roles('contents', 'DELETE_SELF_CONTENTS', null, ['id_user'=>$data->id_user]);
                        }
                        return true;
                    },        
                    'langtitle'=>function($data){
                        $lang = $data->lang ;
                        return $lang ? $lang->title : Text::_('LIB_USERINTERFACE_FIELD_ALL');
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
        
        return $this->renderPart('content', null, null, ['bundle'=>$bundle, 'term_id'=>$term_id, 'modelFilter'=>$modelFilter]);
    }
    
    public function actionSort(array $sort) {
        
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
  
        foreach ($sort as $cont) {
            if(isset($cont['contents_id']) && isset($cont['sort']) && isset($cont['term_id'])){
                ContentTermSort::deleteAll(['contents_id'=>$cont['contents_id'], 'term_id'=>$cont['term_id']]);
                $obj = new ContentTermSort();
                $obj->attributes = $cont;
                $obj->save();
            }

        }  
    }

}

?>