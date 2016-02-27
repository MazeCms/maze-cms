<?php

namespace exp\exp_languages\form;

use maze\base\Model;
use maze\table\InstallApp;
use maze\table\Languages;
use maze\table\Plugin;
use maze\helpers\ArrayHelper;

class FormOverload extends Model {

    public $id;
    
    public $id_lang;
    
    public $front;
    
    public $constant;
    
    public $value;

    public function rules() {
        return [
            [['id_lang', 'front', 'constant', 'value'], "required", "on"=>"create"],
            [['front'], 'boolean', "on"=>"create"],
            [['value', 'id'], 'required', "on"=>"edit"],
            ['id', 'exist', 'targetClass'=>'maze\table\LangOverload', 'targetAttribute'=>'id', "on"=>"edit"],
            ['constant', 'match', 'pattern'=>'/^[A-Z]{3}_[A-Z_]+$/', "on"=>"create"],
            ['constant', 'unique', 'targetClass'=>'maze\table\LangOverload', 'targetAttribute'=>'constant', "filter"=>function($query){
                $query->andWhere(['front' => $this->front, 'id_lang' => $this->id_lang]);
            },   "on"=>"create"],
            [['id_lang'], 'number', "on"=>"create"],
            ['id_lang', 'exist', 'targetClass'=>'maze\table\Languages', 'targetAttribute'=>'id_lang', "on"=>"create"]
        ];
    }

    public function attributeLabels() {
        return[
            "id_lang" => \Text::_("EXP_LANGUAGES_APP"),
            "constant" => \Text::_("EXP_LANGUAGES_PACKS_SEARCH_LABEL_CONST"),
            "front" => \Text::_("EXP_LANGUAGES_APP_TABLE_FRONT"),
            "value" => \Text::_("EXP_LANGUAGES_PACKS_SEARCH_LABEL_VAL")
        ];
    }

}
        