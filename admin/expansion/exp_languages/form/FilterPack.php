<?php

namespace exp\exp_languages\form;
use maze\validators\DateValidator;

class FilterPack extends \ui\filter\Model {
    
    
    public $front_back;

    public $id_lang;
    
    public $type;
    
    public $id_app;
    
    public $constant;
        
    public $constValue;
    
    public function rules() {
       $rules =  parent::rules();
       $rules[] = [['front_back', 'id_lang', 'type', 'id_app', 'constant', 'constValue'], 'safe'];
       return $rules;
    }
    
   

    public function queryBilder($query)
    {
       
        $query->andFilterWhere(['app.front_back'=>$this->front_back]);
        $query->andFilterWhere(['lca.id_lang'=>$this->id_lang]);
        $query->andFilterWhere(['lca.id_lang'=>$this->id_lang]);
        $query->andFilterWhere(['app.type'=>$this->type]);
        $query->andFilterWhere(['lca.id_app'=>$this->id_app]);
        $query->andFilterWhere(['like', 'lca.constant', $this->constant]);
        $query->andFilterWhere(['like', 'lca.value', $this->constValue]);
    }
    public function attributeLabels() {
        return[
            "front_back" => \Text::_("EXP_LANGUAGES_APP_FILTER_TITLE_FRONT"),
            "id_lang"=>\Text::_("EXP_LANGUAGES_APP_FILTER_TITLE_LANG"),
            "type"=>\Text::_("EXP_LANGUAGES_APP_FILTER_TITLE_TYPE"),
            "id_app"=>\Text::_("EXP_LANGUAGES_APP_FILTER_TITLE_NAME"),
            "constant"=>\Text::_("EXP_LANGUAGES_PACKS_SEARCH_LABEL_CONST"),
            "constValue"=>\Text::_("EXP_LANGUAGES_PACKS_SEARCH_LABEL_VAL")
        ];
    }

}
