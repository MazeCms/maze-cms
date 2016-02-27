<?php

namespace exp\exp_widget\form;
use maze\validators\DateValidator;

class FilterWidget extends \ui\filter\Model {
    
    public $name;
    
    public $title;
    
    public $position;
    
    public $title_show;
    
    public $id_tmp;
    
    public $id_lang;
    
    public $id_role;
    
    public $enabled;
    
    public $time_active;
    
    public $time_inactive;
        
  
    public function rules() {
       $rules =  parent::rules();
       $rules[] = [['name', 'title', 'position', 'title_show', 'id_tmp', 'id_lang', 'id_role', 'enabled'], 'safe'];
       $rules[] = [['time_active', 'time_inactive'], 'validDate'];
       return $rules;
    }
    public function validDate($attribute, $params)
    {
        if(is_array($this->$attribute))
        {           
            $date = new DateValidator();
            $attr = $this->$attribute;
            
             
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
        
        $query->andFilterWhere(['w.id_tmp'=>$this->id_tmp]);
        $query->andFilterWhere(['w.name'=>$this->name]);
        $query->andFilterWhere(['w.position'=>$this->position]);
        $query->andFilterWhere(['w.title_show'=>$this->title_show]);
        $query->andFilterWhere(['w.id_lang'=>$this->id_lang]);
        $query->andFilterWhere(['w.enabled'=>$this->enabled]);
        $query->andFilterWhere(['like', 'w.title', $this->title]);
        $query->andFilterWhere(['r.id_role'=>$this->id_role]);
        $query->andFilterWhere(['>=', 'time_active', $this->time_active[0]]);
        $query->andFilterWhere(['<=', 'time_active', $this->time_active[1]]);
        $query->andFilterWhere(['>=', 'time_inactive', $this->time_inactive[0]]);
        $query->andFilterWhere(['<=', 'time_inactive', $this->time_inactive[1]]);
        
    }
    
    public function attributeLabels() {
        return[
            "name"=>\Text::_("EXP_WIDGET_WIDGETS_FILTER_TYPE_TITLE"),
            "title"=>\Text::_("EXP_WIDGET_WIDGETS_TABLE_HEAD_TITLE"),
            "position"=>\Text::_("EXP_WIDGET_WIDGETS_FILTER_POSITION_TITLE"),            
            "title_show"=>\Text::_("EXP_WIDGET_FORM_LABEL_SHOWTITLE"),
            "id_tmp"=>\Text::_("EXP_WIDGET_WIDGETS_FILTER_TMP_TITLE"),
            "id_lang"=>\Text::_("EXP_WIDGET_WIDGETS_FILTER_LANG_TITLE"),
            "id_role"=>\Text::_("EXP_WIDGET_WIDGETS_FILTER_ROLE_TITLE"),
            "enabled"=>\Text::_("EXP_WIDGET_WIDGETS_FILTER_PUBLISH_TITLE"),
            "time_active"=>\Text::_("EXP_WIDGET_WIDGETS_FILTER_DATA_START_TITLE"),
            "time_inactive"=>\Text::_("EXP_WIDGET_WIDGETS_FILTER_DATA_END_TITLE")
        ];
    }

}
