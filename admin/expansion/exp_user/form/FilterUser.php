<?php

namespace exp\exp_user\form;
use maze\validators\DateValidator;

class FilterUser extends \ui\filter\Model {
    
    public $status;
    
    public $bloc;
  
    public $id_lang;
    
    public $id_role;
    
    public $registerDate;
    
    public $lastvisitDate;
        
  
    public function rules() {
       $rules =  parent::rules();
       $rules[] = [['status', 'bloc', 'id_lang', 'id_role'], 'safe'];
       $rules[] = [['registerDate', 'lastvisitDate'], 'validDate'];
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
       
        $query->andFilterWhere(['u.status'=>$this->status]);
        $query->andFilterWhere(['u.bloc'=>$this->bloc]);
        $query->andFilterWhere(['u.id_lang'=>$this->id_lang]);
        $query->andFilterWhere(['r.id_role'=>$this->id_role]);
        $query->andFilterWhere(['>=', 'u.lastvisitDate', $this->lastvisitDate[0]]);
        $query->andFilterWhere(['<=', 'u.lastvisitDate', $this->lastvisitDate[1]]);
        $query->andFilterWhere(['>=', 'u.registerDate', $this->registerDate[0]]);
        $query->andFilterWhere(['<=', 'u.registerDate', $this->registerDate[1]]);
    }
    public function attributeLabels() {
        return[
            "status" => \Text::_("EXP_USER_FILTER_STATUS_TITLE"),
            "bloc" => \Text::_("EXP_USER_FILTER_ENABLE_TITLE"),
            "id_lang"=>\Text::_("EXP_USER_FILTER_LANG_TITLE"),
            "id_role"=>\Text::_("EXP_USER_FILTER_ROLE_TITLE"),
            "registerDate"=>\Text::_("EXP_USER_FILTER_DATEREG_TITLE"),
            "lastvisitDate"=>\Text::_("EXP_USER_FILTER_DATELAST_TITLE")
        ];
    }

}
