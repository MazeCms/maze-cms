<?php

namespace exp\exp_menu\form;

use maze\base\Model;
use RC;
use URI;

class Item extends Model {

    /**
     * @var string - заголовок пункта меню
     */
    public $name;

    /**
     * @var int - id пункта меню
     */
    public $id_menu;

    /**
     * @var int - id компаненты
     */
    public $id_exp;

    /**
     * @var string - целевой компаненты
     */
    public $component;

    /**
     * @var string - целевой контролер
     */
    public $controller;

    /**
     * @var string - целевой вид
     */
    public $view;

    /**
     * @var string - целевой шаблон
     */
    public $layout;
    
    /**
     * @var string - тип пункта меню
     */
    public $typeLink;
    
    /**
     * @var int - id меню 
     */
    public $id_group;
    
    /**
     * @var string - url адрес иконки пукта
     */
    public $image;
    
    /**
     * @var string - url для типов ссылки url|alias
     */
    public $paramLink;
    
    /**
     * @var array - дополнительные параметры пукта меню
     */
    public $param;
    
    /**
     * @var array - дополнительные get параметры пукта меню для типа expansion
     */
    public $url_param;
    
    public $id_lang;
    
    public $id_tmp;
    
    public $meta_robots;
    
    public $meta_title;
    
    public $meta_key;
    
    public $meta_des;
    
    public $meta_author;
    
    public $enabled;
    
    public $home;
    
    public $time_active;
    
    public $time_inactive;
    
    public $alias;
    
    public $id_role;
    
    public $parent;
    
    public $routes_id;


    public function rules() {
        return [
            [['name', 'typeLink', 'id_group', 'alias'], "required"],
            [['component', 'controller', 'view', 'layout'], "required", 'on'=>'expansion'],
            [['paramLink'], "required", 'on'=>['url', 'alias']],
            [['name','alias','meta_title'], 'string', 'max' => 255],
            ['alias', 'match', 'pattern'=>'/^[a-z0-9-_]{4,255}$/i'],
            [['enabled','home'],'boolean'],
            ['parent','validParents'],
            ['alias', 'unique', 'targetClass'=>'maze\table\Routes', 'targetAttribute'=>'alias', 'filter'=>function($query){
                if($this->id_menu) $query->andFilterWhere(['not', ['routes_id'=>$this->routes_id]]);
            }],
            [['parent'],'default', 'value'=>0],
            [['id_group', 'id_exp', 'parent', 'id_lang', 'id_tmp', 'id_menu', 'id_menu'], 'number'],
            [['time_active', 'time_inactive'], 'date', 'format'=>'Y-m-d H:i:s'],
            [['image', 'meta_robots', 'url_param', 'id_role', 'meta_key', 'meta_des', 'image', 'param'], 'safe']
        ];
    }
    
    public function afterValidate() {
        if($this->typeLink == 'alias' && !$this->hasErrors()){
           $router = RC::getRouter(RC::ROUTERSITE);
           if($path = $router->parseRouteApp($this->paramLink)){
               $uri = new URI($path);
               $this->paramLink = $uri->toString(array('path', 'query', 'fragment'));
           }
        }
    }
    
    
    public function validParents()
    {
        if($this->id_menu && $this->parent)
        {
            $menu = new \exp\exp_menu\model\Menu();
            $parents = $menu->getParnetsItem($this->id_group);
            
            $id = $menu->getChildId($parents, $this->id_menu);
            $id[] = $this->id_menu;
            if(in_array($this->parent, $id))
            {
               $this->addError('parent', 'Данный пункт не может быть родитеским');
            }
        }
    }
    
    
    public function attributeLabels() {
        return[
            "name" => \Text::_("EXP_MENU_ADD_ITEM_FORM_TITLE"),
            "typeLink"=>"Тип пункта меню",
            "paramLink"=>\Text::_("EXP_MENU_ADD_ITEM_FORM_URL"),
            "id_lang"=>\Text::_("EXP_MENU_ADD_ITEM_FORM_LANG"),
            "id_tmp"=>\Text::_("EXP_MENU_ADD_ITEM_FORM_TMP"),
            "meta_robots"=>\Text::_("EXP_MENU_ITEMS_PARAMS_META_LABEL_ROBOTS"),
            "meta_title"=>\Text::_("EXP_MENU_ITEMS_PARAMS_META_LABEL_TITLE"),
            "meta_key"=> \Text::_("EXP_MENU_ITEMS_PARAMS_META_LABEL_KEYWORDS"),
            "meta_des"=>\Text::_("EXP_MENU_ITEMS_PARAMS_META_LABEL_DESCRIPTION"),
            "meta_author"=>\Text::_("EXP_MENU_ITEMS_PARAMS_META_LABEL_AUTHOR"),
            "enabled"=>\Text::_("EXP_MENU_ADD_ITEM_FORM_ACTIVE"),
            "home"=>\Text::_("EXP_MENU_ADD_ITEM_FORM_HOME"),
            "time_active"=>\Text::_("EXP_MENU_ADD_ITEM_FORM_TIMEACTIVE"),
            "time_inactive"=>\Text::_("EXP_MENU_ADD_ITEM_FORM_TIMEINACTIVE"),
            "id_group"=>\Text::_("EXP_MENU_TITLE_MENU"),
            "alias"=>\Text::_("EXP_MENU_ADD_ITEM_FORM_ALIAS"),
            "id_role"=>\Text::_("EXP_MENU_ADD_ITEM_FORM_ACCESS"),
            "parent"=>\Text::_("EXP_MENU_ADD_ITEM_FORM_MENU"),
            "image"=>\Text::_("EXP_MENU_ADD_ITEM_FORM_IMAGE")
        ];
    }

}
