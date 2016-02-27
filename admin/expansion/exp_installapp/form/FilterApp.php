<?php

namespace exp\exp_installapp\form;
use maze\validators\DateValidator;

class FilterApp extends \ui\filter\Model {
    
    public $type;
    
    public $front_back;
    
    public $name;
    
    public $install_data;
        
  
    public function rules() {
       $rules =  parent::rules();
       $rules[] = [['type', 'front_back', 'name'], 'safe'];
       $rules[] = [['install_data'], 'validDate'];
       return $rules;
    }
    
    public function validDate($attribute, $params)
    {
        if(is_array($this->$attribute))
        {
           
            $date = new DateValidator();
            $attr = $this->$attribute ;
            foreach($attr as $key=>$val)
            {
                if(!$date->validate($val))
                {
                    $attr[$key] = null;
                }
            }
            $this->offsetSet($attribute, $attr);
        }
    }
   

    public function queryBilder($query)
    {
        $query->andFilterWhere(['ia.front_back'=>$this->front_back]);
        $query->andFilterWhere(['ia.type'=>$this->type]);
        $query->andFilterWhere(['like', 'ia.name', $this->name]);
        $query->andFilterWhere(['>=', 'ia.install_data', $this->install_data[0]]);
        $query->andFilterWhere(['<=', 'ia.install_data', $this->install_data[1]]);

    }
    public function attributeLabels() {
        return[
            "front_back" => \Text::_("EXP_INSTALLAPP_FILTER_LABEL_FRONT"),
            "install_data"=>\Text::_("EXP_INSTALLAPP_FILTER_LABEL_DATE"),
            "type"=>\Text::_("EXP_INSTALLAPP_FILTER_LABEL_TYPE"),
            "name"=>\Text::_("EXP_INSTALLAPP_TABLE_HEAD_NAME")
        ];
    }

}
