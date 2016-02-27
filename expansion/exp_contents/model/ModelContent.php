<?php

namespace root\expansion\exp_contents\model;

use maze\base\Model;
use Text;
use RC;
use maze\table\ContentType;
use maze\fields\FieldHelper;
use exp\exp_contents\form\FormType;
use exp\exp_contents\model\ModelType;
use maze\table\FieldExp;
use maze\helpers\ArrayHelper;
use maze\table\Contents;
use maze\table\Routes;
use maze\table\ContentsHome;
use maze\table\AccessRole;
use maze\table\ContentTypeView;
use ToolBarelem;
use maze\table\InstallApp;
use URI;

class ModelContent extends Model {
    
    const FULLCONTENT = 1;
    
    const SHORTCONTENT = 0;

    protected $id;

    protected $fields;
    
    protected $routes;
    
    protected $contents;
    
    protected $id_role;
    
    protected $viewfield;
    
    protected $toolbar;

    public static function find($id){
        $contents = RC::getDb()->cache(function($db) use ($id){
                return Contents::find()->from(['c'=>Contents::tableName()])
                        ->joinWith(['route', 'accessRole'=>function($qu){
                           $qu->andOnCondition(['ar.exp_name'=>'contents'])
                                ->andOnCondition(['ar.key_role'=>'content']);
                        }])
                        ->where(['c.expansion' => 'contents', 'c.enabled'=>1, 'c.contents_id' => $id])
                        ->andWhere(['or', 'c.time_active<=NOW()', 'c.time_active is null'])
                        ->andWhere(['or', 'c.time_inactive>=NOW()', 'c.time_inactive is null'])           
                        ->one();
                }, null, 'exp_contents');
        return static::createModel($contents);
    }
    
    public static function findAll($condition = []) {
        
        $idLang = RC::app()->lang->getLang(['type'=>'exp', 'name'=>'contents'])->getIdLang();
        $idRole = RC::app()->access->getIdRole();
       
        $contents =  Contents::find()->from(['c'=>Contents::tableName()])
                        ->joinWith(['route', 'accessRole'=>function($qu){
                           $qu->andOnCondition(['ar.exp_name'=>'contents'])
                                ->andOnCondition(['ar.key_role'=>'content']);
                        }])
                        ->where(['c.expansion' => 'contents', 'c.enabled'=>1])
                        ->andWhere(['or', ['c.id_lang'=>$idLang], ['c.id_lang'=>0]])
                        ->andWhere(['or', 'c.time_active<=NOW()', 'c.time_active is null'])
                        ->andWhere(['or', 'c.time_inactive>=NOW()', 'c.time_inactive is null'])       
                        ->andWhere(['or', ['ar.id_role'=>$idRole], ['ar.id_role'=>null]])        
                        ->andWhere($condition)
                        ->groupBy('c.contents_id')        
                        ->orderBy('c.sort');
         RC::getPlugin('contents')->triggerHandler("afterFindContent", [&$contents]);
        return $contents;
    }
    
    public static function findHome($condition = []){
        $idLang = RC::app()->lang->getLang(['type'=>'exp', 'name'=>'contents'])->getIdLang();
        $idRole = RC::app()->access->getIdRole();
       
        $contents =  Contents::find()->from(['c'=>Contents::tableName()])
                        ->joinWith(['route', 'frontPage', 'accessRole'=>function($qu){
                           $qu->andOnCondition(['ar.exp_name'=>'contents'])
                                ->andOnCondition(['ar.key_role'=>'content']);
                        }])
                        ->where(['c.expansion' => 'contents', 'c.enabled'=>1,  'c.home'=>1])
                        ->andWhere(['or', ['c.id_lang'=>$idLang], ['c.id_lang'=>0]])
                        ->andWhere(['or', 'c.time_active<=NOW()', 'c.time_active is null'])
                        ->andWhere(['or', 'c.time_inactive>=NOW()', 'c.time_inactive is null'])       
                        ->andWhere(['or', ['ar.id_role'=>$idRole], ['ar.id_role'=>null]])        
                        ->andWhere($condition)
                        ->groupBy('c.contents_id')
                        ->orderBy('h.sort');
         RC::getPlugin('contents')->triggerHandler("afterFindContent", [&$contents]);
        return $contents;
    }
    
    public static function createModel($contents) {
        $result = [];
        if(!$contents) return false;
        
        if(is_array($contents)){
            foreach($contents as $cont){
                $result[] = static::createModel($cont);
            }
        }else{
            $model = new self;
            $model->contents = $contents;
            $fields = $model->getFields();
        
            foreach($fields as $field){
               $field->findData(['entry_id'=>$model->contents->contents_id]);
            }
            
            if($role = $model->contents->accessRole){
                $model->id_role = array_map(function($val){return $val->id_role;}, $role);
            }
            
            $result = $model;
        }
        
        return $result;
    }
    
    public function getContents() {
        return $this->contents;
    }
    
    public function getIDRole(){
        return $this->id_role;
    }
    
    public function getId() {
        return $this->contents->contents_id;
    }

    public function getRoutes() {
        if ($this->routes == null) {
            if ($this->getContents()) {
                if ($cont = $this->getContents()) {
                    $this->routes = $cont->route;
                }
            } 
        }
        return $this->routes;
    }

    public function getFields() {
        if ($this->getContents() && $this->fields == null) {
            $this->fields = FieldHelper::findAll(['expansion' => 'contents', 'bundle' => $this->getContents()->bundle, 'active'=>1]);
        }
        return $this->fields;
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
    
    public function getViewField($mode = self::FULLCONTENT) {
        if(!isset($this->viewfield[$mode])){
            $contents = $this->getContents();
            $views = RC::getDb()->cache(function($db) use ($contents, $mode){
                return  ContentTypeView::find()->where([
                    'bundle'=>$contents->bundle, 
                    'entry_type'=>'contents',
                    'expansion'=>'contents',
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
            $id = 'admin-edits-contents-panel-'.$this->getId();
            $menu = [
                        [
                            'class'=>'ContextMenu',
                            "TITLE" => "EXP_CONTENTS_VIEWS",
                            "SORT" => 1,
                            "VISIBLE" =>$access->roles("contents", "EDIT_VIEW_CONTENTS"),
                            "HREF" => ['admin/contents/view', ["run"=>"edit", "bundle"=>$this->contents->bundle, "expansion"=>"contents", "mode"=>0]],
                            "ACTION" => "window.open(this.href); return false;"
                        ],
                        [
                            'class'=>'ContextMenu',
                            "TITLE" => "EXP_CONTENTS_FIELD",
                            "SORT" => 1,
                            "VISIBLE" =>$access->roles("contents", "EDIT_FIELD_CONTENTS"),
                            "HREF" => ['admin/contents/field', ["run"=>"field", "bundle"=>$this->contents->bundle]],
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
                'type-' . $this->contents->bundle . '-' . $this->getId(),
                'type-' . $this->contents->bundle
            ];

            foreach ($layouts as $layout) {

                if ($view->hasView('@tmp/' . $theme->getName() . '/views/expansion/exp_contents/views/contents/tmp/' . $layout)) {
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
                "ACTION" => "file_edit_template('php', '" . str_replace([$theme->basePath, "\\"], ["", "/"], RC::getAlias('@tmp/' . $theme->getName() . '/views/expansion/exp_contents/views/contents/tmp/')) . "tmp.$result.php', '" . " (".$result. ")', " . $template->id_app . "); return false;"
                    ]);
            }
            $this->toolbar = \maze\toolbarsite\ToolbarBuilder::begin([
            'options'=>['id'=>$id],
            'private'=>['contents'=>'EDIT_CONTENTS'],
            'buttons'=>[
                    [
                        'class'=>'Buttonset',
                        "TITLE" => "LIB_USERINTERFACE_TOOLBAR_EDIT_ONER",
                        "SORT" => 4,       
                        "SRC" => "/library/jquery/toolbarsite/images/icon-edit.png",
                        "HREF" =>['/admin/contents', ['run'=>'edit', 'contents_id'=>$this->getId(), 'clear'=>'ajax']],
                        "ACTION" => "cms.formDialogSave(this,{title:'".Text::_("LIB_USERINTERFACE_TOOLBAR_EDIT_NAME",['name'=>$this->getTitle()])."'}); return false;",
                        "MENU" =>[
                            [
                                'class'=>'ContextMenu',
                                "TITLE" => "LIB_USERINTERFACE_TOOLBAR_WINDOW_NEW",
                                "SORT" => 1,
                                "HREF" =>['/admin/contents', ['run'=>'edit', 'contents_id'=>$this->getId(), "return"=>URI::current()]],
                                "ACTION" => "window.open(this.href); return false;"
                            ],
                            [
                                'class'=>'ContextMenu',
                                "TITLE" => "LIB_USERINTERFACE_TOOLBAR_WINDOW_SELF",
                                "SORT" => 1,
                                "HREF" =>['/admin/contents', ['run'=>'edit', 'contents_id'=>$this->getId(), "return"=>URI::current()]],
                            ]
                            
                        ]
                    ],
                    [
                        'class'=>'Buttonset',
                        "TITLE" => "LIB_USERINTERFACE_TOOLBAR_DELET_BUTTON",
                        "SORT" => 1,
                        "VISIBLE" =>  ($access->roles("contents", "DELETE_CONTENTS") || $access->roles("contents", "DELETE_SELF_CONTENTS", null, ['contents_id'=>$this->getId()])),
                        "SRC" => "/library/jquery/toolbarsite/images/icon-trash.png",
                        "HREF" =>['/admin/contents', ['run'=>'delete', 'contents_id'=>[$this->getId()]]],
                        "ACTION" => "cms.deleteBlock(this, '#".$id."'); return false;"
                    ],
                    [
                        'class'=>'Buttonset',
                        "TITLE" => "LIB_USERINTERFACE_TOOLBAR_UNPUBLISH_BUTTON",
                        "SORT" => 2,
                        "SRC" => "/library/jquery/toolbarsite/images/icon-close.png",
                        "HREF" =>['/admin/contents', ['run'=>'unpublish', 'contents_id'=>[$this->getId()]]],
                        "ACTION" => "cms.deleteBlock(this, '#".$id."'); return false;"
                    ],
                    [
                        'class'=>'Buttonset',
                        "TITLE" => "Настроить",
                        "SORT" => -1,
                        "VISIBLE" =>($access->roles("contents", "EDIT_VIEW_CONTENTS") || $access->roles("contents", "EDIT_FIELD_CONTENTS")),
                        "SRC" => "/library/jquery/toolbarsite/images/icon-settings.png",
                        "MENU"=>$menu
                    ],
                    
                ]
            ]);
        }

        return $this->toolbar;
    }

}
