<?php
namespace maze\table\query;
use maze\db\ActiveQuery;

class MenuQuery extends ActiveQuery{
    
    public function enable($val=1)
    {
        $this->andWhere(['enabled'=>$val]);
        return $this;
    }
    
    public function home()
    {
        $this->andWhere(['home'=>1]);
        return $this;
    }
    
    public function langItems($id_lang)
    {
        $this->andWhere(['or', 'id_lang=:id_lang', 'id_lang is NULL'], [':id_lang'=>$id_lang]);
        return $this;
    }
    
    public function activeDate()
    {
        $this->andWhere(['or', 'time_active<=NOW()', 'time_active is NULL'])
             ->andWhere(['or', 'time_inactive>=NOW()', 'time_inactive is NULL']);
        return $this;
    }
    
}
