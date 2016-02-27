<?php

namespace lib\fields\term;
use RC;
use Text;
use maze\db\Expression;
use maze\db\Query;

class Field extends \maze\fields\BaseField{
    
    public function init(){
        $this->type = 'term';
        $this->locked = 0;
    }
    
    public function rules() {
        $rules = parent::rules();
        $rules[] = ['bundle', 'validateUniqueTerm'];
        return $rules;
    }
    
   
    public function validateUniqueTerm($attr, $param) {
        if ($this->field_exp_id == null) {
            $result = (new Query())
                            ->from('{{%field_exp}}')
                           ->innerJoin('{{%fields}}', '{{%fields}}.field_name = {{%field_exp}}.field_name')
                            ->where([
                                '{{%field_exp}}.expansion' => $this->expansion,
                                '{{%field_exp}}.bundle' => $this->bundle,
                                '{{%fields}}.type'=>'term'
                            ])->all();

            foreach($result as $d){
                if($d['param']){
                    $param = unserialize($d['param']);
                    if($param['dictionary'] == $this->getSettings()->dictionary){
                        $this->addError($attr, Text::_('Уданного типа материалов может быть только одна сылка на термин словаря {dict}', ['dict'=>$this->getSettings()->dictionary]));
                        break;
                    }
                }
                
            }
          
        }
    }
    
    public function getScheme(){
        return [
            'term_id'=>"INT(11) DEFAULT NULL COMMENT 'id термина словаря'"
        ];
    }
    
    public function createTable() {

        $db = RC::getDb();
        $colonum = $this->getScheme();
        $colonum = array_merge([
            'id' => 'pk',
            'field_exp_id' => 'integer',
            'entry_id' => 'integer',
            'id_lang' => 'integer'
                ], $colonum);
        $db->createCommand()->createTable($this->getTableName(), $colonum, 'CHARACTER SET utf8 COLLATE utf8_general_ci')->execute();
        $db->createCommand()
                ->addForeignKey($this->type . '_' . $this->field_name, $this->getTableName(), 'field_exp_id', '{{%field_exp}}', 'field_exp_id', 'CASCADE', 'CASCADE')->execute();
    
        $db->createCommand()
                ->addForeignKey($this->type . '_' . $this->field_name.'_term', $this->getTableName(), 'term_id', '{{%dictionary_term}}', 'term_id', 'CASCADE', 'CASCADE')->execute();
    }
}
