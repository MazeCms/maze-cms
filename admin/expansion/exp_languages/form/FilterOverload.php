<?php

namespace exp\exp_languages\form;
use maze\validators\DateValidator;

class FilterOverload extends \ui\filter\Model {
    
    
    public $front;

    public $id_lang;
    
    public $constant;
        
    public $constValue;
    
    public function rules() {
       $rules =  parent::rules();
       $rules[] = [['front', 'id_lang', 'constant', 'constValue'], 'safe'];
       return $rules;
    }
    
   

    public function queryBilder($query)
    {       
        $query->andFilterWhere(['lo.front'=>$this->front]);
        $query->andFilterWhere(['lo.id_lang'=>$this->id_lang]);
        $query->andFilterWhere(['like', 'lo.constant', $this->constant]);
        $query->andFilterWhere(['like', 'lo.value', $this->constValue]);
    }
    public function attributeLabels() {
        return[
            "front" => \Text::_("EXP_LANGUAGES_APP_FILTER_TITLE_FRONT"),
            "id_lang"=>\Text::_("EXP_LANGUAGES_APP_FILTER_TITLE_LANG"),
            "constant"=>\Text::_("EXP_LANGUAGES_PACKS_SEARCH_LABEL_CONST"),
            "constValue"=>\Text::_("EXP_LANGUAGES_PACKS_SEARCH_LABEL_VAL")
        ];
    }

}
