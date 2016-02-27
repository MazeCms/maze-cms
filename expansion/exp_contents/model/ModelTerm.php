<?php

namespace root\expansion\exp_contents\model;

use Text;
use RC;
use maze\base\Model;
use maze\table\ContentType;
use maze\fields\FieldHelper;
use maze\table\FieldExp;
use maze\helpers\ArrayHelper;
use maze\table\DictionaryTerm;
use maze\table\Routes;
use maze\table\AccessRole;
use maze\table\ContentTypeView;
use maze\table\Contents;
use URI;
use maze\table\InstallApp;

class ModelTerm extends Model {
    
    const FULLCONTENT = 1;
    
    const SHORTCONTENT = 0;
    
    protected $fields;
    
    protected $routes;
    
    protected $term;
    
    protected $id_role;
    
    protected $viewfield;
    
    protected $toolbar;

    public static $_parents;

    public static function find($id){
        $term = RC::getDb()->cache(function($db) use ($id){ 
            return DictionaryTerm::find()                        
                        ->from(['dt'=>DictionaryTerm::tableName()])
                        ->joinWith(['route', 'accessRole'=>function($qu){
                           $qu->andOnCondition(['ar.exp_name'=>'dictionary'])
                                ->andOnCondition(['ar.key_role'=>'term']);
                        }])
                        ->where(['dt.expansion' => 'dictionary', 'dt.term_id' => $id, 'dt.enabled'=>1])
                        ->andWhere(['or', 'dt.time_active<=NOW()', 'dt.time_active is null'])
                        ->andWhere(['or', 'dt.time_inactive>=NOW()', 'dt.time_inactive is null'])   
                        ->one();
        }, null, 'exp_contents');
        
        if(!$term) return false;    
        
        return static::createModel($term);
    }
    
    public static function findAll($condition = []){
        $idLang = RC::app()->lang->getLang(['type'=>'exp', 'name'=>'contents'])->getIdLang();
        $idRole = RC::app()->access->getIdRole();
        $queryTerm = DictionaryTerm::find()                        
                        ->from(['dt'=>DictionaryTerm::tableName()])
                        ->joinWith(['route', 'accessRole'=>function($qu){
                           $qu->andOnCondition(['ar.exp_name'=>'dictionary'])
                                ->andOnCondition(['ar.key_role'=>'term']);
                        }])
                        ->where(['dt.expansion' => 'dictionary',  'dt.enabled'=>1])
                        ->andWhere(['or', 'dt.time_active<=NOW()', 'dt.time_active is null'])
                        ->andWhere(['or', 'dt.time_inactive>=NOW()', 'dt.time_inactive is null'])   
                        ->andWhere(['or', ['dt.id_lang'=>$idLang], ['dt.id_lang'=>0]])
                        ->andWhere(['or', ['ar.id_role'=>$idRole], ['ar.id_role'=>null]])        
                        ->andWhere($condition)
                        ->groupBy('dt.term_id')
                        ->orderBy('dt.sort');
                        
        return  $queryTerm;             

    }

    public static function createModel($term) {
        $result = [];
        if(!$term) return false;
        
        if(is_array($term)){
            foreach($term as $cont){
                $result[] = static::createModel($cont);
            }
        }else{
            $model = new self;
            $model->term = $term;
            $fields = $model->getFields();
        
            foreach($fields as $field){
               $field->findData(['entry_id'=>$model->term->term_id]);
            }
            
            if($role = $model->term->accessRole){
                $model->id_role = array_map(function($val){return $val->id_role;}, $role);
            }
            
            $result = $model;
        }
        
        return $result;
    }

    public function getTerm() {
        return $this->term;
    }
    
    public function getRoutes() {
        if ($this->routes == null) {
           if ($cont = $this->getTerm()) {
                $this->routes = $cont->route;
           }
        }
        return $this->routes;
    }

    public function getFields() {
        if ($this->getTerm() && $this->fields == null) {
            $this->fields = FieldHelper::findAll(['expansion' => 'dictionary', 'bundle' => $this->term->bundle, 'active'=>1]);
        }
        return $this->fields;
    }
    
    
    public function getFindContents($condition = []){
        $catalog = RC::getDb()->cache(function($db){
            return FieldExp::find()        
                    ->from(['fe' => FieldExp::tableName()])
                    ->joinWith(['typeFields'])
                    ->where(['fe.expansion' => 'contents', 'f.type' => 'term'])                    
                    ->all();
        }, null, 'exp_contents');
        
        $table = [];
        foreach ($catalog as $cat) {
            if (isset($cat->param['dictionary'])) {
                if ($cat->param['dictionary'] !== $this->term->bundle || in_array($cat->param['dictionary'], $table)) {
                    continue;
                }
                $table[] = $cat->field_name;
            }
        }
        
        $idLang = RC::app()->lang->getLang(['type'=>'exp', 'name'=>'contents'])->getIdLang();
        $idRole = RC::app()->access->getIdRole();
        $term_id = $this->term->term_id;
        
        $contents =  Contents::find()->from(['c'=>Contents::tableName()])
                        ->joinWith(['route', 'accessRole'=>function($qu){
                           $qu->andOnCondition(['ar.exp_name'=>'contents'])
                                ->andOnCondition(['ar.key_role'=>'content']);
                        }, 'termSort'=>function($qu) use ($term_id){
                                $qu->andOnCondition(['ts.term_id'=>$term_id]);
                        }])
                        ->where(['c.expansion' => 'contents', 'c.enabled'=>1])
                        ->andWhere(['or', ['c.id_lang'=>$idLang], ['c.id_lang'=>0]])
                        ->andWhere(['or', 'c.time_active<=NOW()', 'c.time_active is null'])
                        ->andWhere(['or', 'c.time_inactive>=NOW()', 'c.time_inactive is null'])       
                        ->andWhere(['or', ['ar.id_role'=>$idRole], ['ar.id_role'=>null]])        
                        ->andWhere($condition)
                        ->orderBy('ts.sort')        
                        ->groupBy('c.contents_id'); 
                        
        foreach($table as $t){
                $tableName = '{{%field_term_'.$t.'}}';
                $contents->innerJoin($tableName, $tableName.'.entry_id = c.contents_id')
                        ->andWhere([$tableName.'.term_id'=>$this->term->term_id]);
        }
         RC::getPlugin('contents')->triggerHandler("afterFindContent", [&$contents]);
        return $contents;
    }
    
    /**
     * Все дочерние категории
     * 
     * @return ModelTerm
     */
    public function getChildrenCategory(){
        $children = $this->getChildrenTerm($this->term->term_id, $this->term->bundle);
        $models = [];
        foreach($children as $child){
            $models[] = static::createModel($child);
        }
        return $models;
    }
    /**
     * Дочерние категории первого уровня
     * 
     * @return ModelTerm
     */
    public function getChildCategory(){
        $result = [];        
        $parent = $this->getParents($this->term->bundle);
        
        if(isset($parent[$this->term->term_id])){
            $target = $parent[$this->term->term_id];
            foreach($target as $p){
               $result[] = static::createModel($p);
            }
        }
        return $result;
        
    }
    
    public function getChildrenTerm($id, $bundle){
        $result = [];        
        $parent = $this->getParents($bundle);
        
        if(isset($parent[$id])){
            $target = $parent[$id];
            foreach($target as $p){
               $result[] = $p;
               $result = array_merge($result, $this->getChildrenTerm($p->term_id, $bundle));
            }
        }
        return $result;
    }
    
    public function getParents($bundle){
        if(!isset(static::$_parents[$bundle])){
            $terms = RC::getDb()->cache(function($db) use ($bundle){
                return DictionaryTerm::find()
                        ->where(['expansion' => 'dictionary', 'bundle' => $bundle])
                        ->all();
            }, null, 'exp_contents');
            
            static::$_parents[$bundle] = [];
        
            foreach($terms as $t){
                static::$_parents[$bundle][$t->parent][] = $t;
            }
        }
        
        return static::$_parents[$bundle];
    }


    public function getField($name) {
        $result = null;
        foreach ($this->getFields() as $field) {
            if ($field->field_name == $name) {
                $result = $field;
                break;
            }
        }
        return $result;
    }
    
    public function getTitle(){
      $field =  $this->getField('title');
      if($field){
        if($field->data){
            return $field->data[0]->title_value;
        }
      }
    }
    
    public function getFieldDyID($id) {
        $fields = $this->getFields();
        $result = null;
        foreach($fields as $field){
            if($field->field_exp_id == $id){
                $result = $field;
                break;
            }
        }
        return $result;
    }
    
    public function getIDRole() {
        return $this->id_role;
    }
    
    public function getId() {
        return $this->term->term_id;
    }
    
    public function getViewField($mode = self::FULLCONTENT) {
        if(!isset($this->viewfield[$mode])){
            $term = $this->getTerm();
        
            $views =  RC::getDb()->cache(function($db) use ($term, $mode){
                return ContentTypeView::find()->where([
                'bundle'=>$term->bundle, 
                'entry_type'=>'contents',
                'expansion'=>'dictionary',
                'mode'=>$mode,
                'enabled'=>1
            ])->orderBy('group_name, sort')->all();
           }, null, 'exp_contents');

            foreach($views as $key=>$view){
                if(($field = $this->getFieldDyID($view->field_exp_id))){                    
                    $view->attachBehavior($key, ['class'=>'exp\exp_contents\model\BehaviorField','view'=>$view, 'field'=>clone $field]);                    
                }else{
                     unset($views[$key]);
                }            	

            }
            
            $this->viewfield[$mode] = $views;
        }
        
        
        
        return $this->viewfield[$mode];
    }
    
    public function getToolbar(){
        if(!$this->toolbar){
            $access = RC::app()->access;
            $menu = [
                        [
                            'class'=>'ContextMenu',
                            "TITLE" => "EXP_CONTENTS_VIEWS",
                            "SORT" => 1,
                            "VISIBLE" =>$access->roles("dictionary", "EDIT_FIELD"),
                            "HREF" => ['admin/contents/view', ["run"=>"edit", "bundle"=>$this->term->bundle, "expansion"=>"dictionary", "mode"=>0]],
                            "ACTION" => "window.open(this.href); return false;"
                        ],
                        [
                            'class'=>'ContextMenu',
                            "TITLE" => "EXP_CONTENTS_FIELD",
                            "SORT" => 1,
                            "VISIBLE" =>$access->roles("contents", "EDIT_FIELD_CONTENTS"),
                            "HREF" => ['admin/dictionary/field', ["run"=>"field", "bundle"=>$this->term->bundle]],
                            "ACTION" => "window.open(this.href); return false;"
                        ]
                    ];
            $theme = RC::app()->theme;
            $template = RC::getDb()->cache(function($db) use ($theme) {
                return InstallApp::find()->where(['type' => 'template', 'name' => $theme->name])->one();
            }, null, 'exp_templating');
            $view = RC::app()->view;
            $result = null;
            $layouts = [
                'type-' . $this->term->bundle . '-' . $this->getId(),
                'type-' . $this->term->bundle
            ];

            foreach ($layouts as $layout) {

                if ($view->hasView('@tmp/' . $theme->getName() . '/views/expansion/exp_contents/views/category/tmp/' . $layout)) {
                    $result = $layout;
                    break;
                } elseif ($view->hasView('/' . $layout)) {
                    $result = $layout;
                    break;
                }
            }
            if($result){
               $menu[] = new \ContextMenu([
                "TITLE" => "WID_BLOCK_PARAMS_TEMPLATE",
                "SORT" => 1,
                "ACTION" => "file_edit_template('php', '" . str_replace([$theme->basePath, "\\"], ["", "/"], RC::getAlias('@tmp/' . $theme->getName() . '/views/expansion/exp_contents/views/category/tmp/')) . "tmp.$result.php', '" . " (".$result. ")', " . $template->id_app . "); return false;"
                    ]);
            }
            $id = 'admin-edits-contents-term-panel-'.$this->getId();
            $this->toolbar = \maze\toolbarsite\ToolbarBuilder::begin([
            'options'=>['id'=>$id],
            'private'=>["dictionary"=>"EDIT_TERM"],
            'buttons'=>[
                    [
                        'class'=>'Buttonset',
                        "TITLE" => "LIB_USERINTERFACE_TOOLBAR_EDIT_ONER",
                        "SORT" => 4,       
                        "SRC" => "/library/jquery/toolbarsite/images/icon-edit.png",
                        "HREF" =>['/admin/dictionary/term', ['run'=>'edit', 'term_id'=>$this->getId(), 'clear'=>'ajax']],
                        "ACTION" => "cms.formDialogSave(this,{title:'".Text::_("LIB_USERINTERFACE_TOOLBAR_EDIT_NAME",['name'=>$this->getTitle()])."'}); return false;",
                        "MENU" =>[
                            [
                                'class'=>'ContextMenu',
                                "TITLE" => "LIB_USERINTERFACE_TOOLBAR_WINDOW_NEW",
                                "SORT" => 1,
                                "HREF" =>['/admin/dictionary/term', ['run'=>'edit', 'term_id'=>$this->getId(), "return"=>URI::current()]],
                                "ACTION" => "window.open(this.href); return false;"
                            ],
                            [
                                'class'=>'ContextMenu',
                                "TITLE" => "LIB_USERINTERFACE_TOOLBAR_WINDOW_SELF",
                                "SORT" => 1,
                                "HREF" =>['/admin/dictionary/term', ['run'=>'edit', 'term_id'=>$this->getId(), "return"=>URI::current()]]
                            ]
                            
                        ]
                    ],
                    [
                        'class'=>'Buttonset',
                        "TITLE" => "LIB_USERINTERFACE_TOOLBAR_DELET_BUTTON",
                        "SORT" => 1,
                        "VISIBLE" =>  $access->roles('dictionary','DELETE_TERM'),
                        "SRC" => "/library/jquery/toolbarsite/images/icon-trash.png",
                        "HREF" =>['/admin/dictionary/term', ['run'=>'delete', 'term_id'=>[$this->getId()]]],
                        "ACTION" => "cms.deleteBlock(this, '#".$id."'); return false;"
                    ],
                    [
                        'class'=>'Buttonset',
                        "TITLE" => "LIB_USERINTERFACE_TOOLBAR_UNPUBLISH_BUTTON",
                        "SORT" => 2,
                        "SRC" => "/library/jquery/toolbarsite/images/icon-close.png",
                        "HREF" =>['/admin/dictionary/term', ['run'=>'unpublish', 'term_id'=>[$this->getId()]]],
                        "ACTION" => "cms.deleteBlock(this, '#".$id."'); return false;"
                    ],
                    [
                        'class'=>'Buttonset',
                        "TITLE" => "Настроить",
                        "SORT" => -1,
                        "VISIBLE" =>($access->roles("dictionary", "EDIT_FIELD") || $access->roles("contents", "EDIT_FIELD_CONTENTS")),
                        "SRC" => "/library/jquery/toolbarsite/images/icon-settings.png",
                        "MENU"=>$menu
                    ],
                    
                ]
            ]);
        }

        return $this->toolbar;
    }

}
