<?php

namespace exp\exp_languages\form;
use maze\validators\DateValidator;

class FilterApp extends \ui\filter\Model {
    
    public $enabled;
    
    public $front_back;

    public $id_lang;
    
    public $type;
    
    public $name;
        
  
    public function rules() {
       $rules =  parent::rules();
       $rules[] = [['enabled', 'front_back', 'id_lang', 'type', 'name'], 'safe'];
       return $rules;
    }
    
   

    public function queryBilder($query)
    {
       
        $query->andFilterWhere(['lapp.enabled'=>$this->enabled]);
        $query->andFilterWhere(['app.front_back'=>$this->front_back]);
        $query->andFilterWhere(['lapp.id_lang'=>$this->id_lang]);
        $query->andFilterWhere(['lang.id_lang'=>$this->id_lang]);
        $query->andFilterWhere(['app.type'=>$this->type]);
        $query->andFilterWhere(['like', 'app.name', $this->name]);

    }
    public function attributeLabels() {
        return[
            "enabled" => \Text::_("EXP_LANGUAGES_APP_FILTER_TITLE_ACTIVE"),
            "front_back" => \Text::_("EXP_LANGUAGES_APP_FILTER_TITLE_FRONT"),
            "id_lang"=>\Text::_("EXP_LANGUAGES_APP_FILTER_TITLE_LANG"),
            "type"=>\Text::_("EXP_LANGUAGES_APP_FILTER_TITLE_TYPE"),
            "name"=>\Text::_("EXP_LANGUAGES_APP_FILTER_TITLE_NAME")
        ];
    }

}
