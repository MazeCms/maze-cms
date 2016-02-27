<?php

namespace exp\exp_languages\form;

use maze\base\Model;
use maze\table\InstallApp;
use maze\table\Languages;
use maze\table\Plugin;
use maze\helpers\ArrayHelper;

class FormPack extends Model {

    public $id;
    
    public $id_lang;
    
    public $id_app;
    
    public $front;
    
    public $type;
    
    public $constant;
    
    public $value;

    public function rules() {
        return [
            [['id_lang', 'id_app', 'front', 'type', 'constant', 'value'], "required", "on"=>"create"],
            [['front'], 'boolean', "on"=>"create"],
            [['value', 'id'], 'required', "on"=>"edit"],
            ['id', 'exist', 'targetClass'=>'maze\table\LangCache', 'targetAttribute'=>'id', "on"=>"edit"],
            ['constant', 'match', 'pattern'=>'/^[A-Z]{3}_[A-Z_]+$/', "on"=>"create"],
            ['constant', 'unique', 'targetClass'=>'maze\table\LangCache', 'targetAttribute'=>'constant', "filter"=>function($query){
                $query->andWhere(['id_app' => $this->id_app, 'id_lang' => $this->id_lang]);
            },   "on"=>"create"],
            [['id_lang', 'id_app', 'id'], 'number', "on"=>"create"],
            ['id_lang', 'exist', 'targetClass'=>'maze\table\Languages', 'targetAttribute'=>'id_lang', "on"=>"create"],
            ['id_app', 'exist', 'targetClass'=>'maze\table\InstallApp', 'targetAttribute'=>'id_app', "filter"=>function($query){
                $query->andWhere(['id_app' => $this->id_app, 'type' => $this->type, 'front_back' => $this->front]);
            }, "on"=>"create"]
        ];
    }

    public function attributeLabels() {
        return[
            "id_lang" => \Text::_("EXP_LANGUAGES_APP"),
            "type" => \Text::_("EXP_LANGUAGES_APP_TABLE_TYPE"),
            "constant" => \Text::_("EXP_LANGUAGES_PACKS_SEARCH_LABEL_CONST"),
            "id_app" => \Text::_("EXP_LANGUAGES_APP_FILTER_TYPE_EXP"),
            "front" => \Text::_("EXP_LANGUAGES_APP_TABLE_FRONT"),
            "value" => \Text::_("EXP_LANGUAGES_PACKS_SEARCH_LABEL_VAL")
        ];
    }

}
        