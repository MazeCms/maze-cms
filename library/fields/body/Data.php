<?php

namespace lib\fields\body;

use Text;

class Data extends \maze\fields\BaseDataField {

    /**
     * @var string - Текст полностью
     */
    public $text_full;

    /**
     * @var string - анонс
     */
    public $text_prev;

    /**
     * @var string -  формат текста
     */
    public $text_format;

    public function init() {
        $settings = $this->getField()->settings;
        
        if($this->text_format == 'stripHtml'){
            $this->text_full = strip_tags($this->text_full);
            $this->text_prev = strip_tags($this->text_prev);
        }
        if($this->text_format == 'filterHtml'){
           
            $listtag = preg_split("/,[\s]+|,/s", $settings->listtag);
            $tag = [];
            foreach($listtag as $tagr){
               $tag[] =  "#<".$tagr."[^>]*>#";
               $tag[] =  "</$tagr>";
            }
            $this->text_full = preg_replace($tag, '', $this->text_full);
            $this->text_prev = preg_replace($tag, '', $this->text_prev);
         
        }
        if($this->text_format == 'fullHtml'){
            $this->text_full = str_replace(['<?php', '?>'], '', $this->text_full);
            $this->text_prev = str_replace(['<?php', '?>'], '', $this->text_prev);
        }
    }

    public function beforeSave() {
        if(empty($this->text_full) && empty($this->text_prev)){
           $this->text_full = null;
           $this->text_prev = null;
           return false;
        }
       
        
        return true;
    }
    
    public function fieldRule() {

        $settings = $this->getField()->settings;
        $rules = [];

        if ($settings->reqfull) {
            $rules[] = ['text_full', 'required'];
        }
        if ($settings->reqprev) {
            $rules[] = ['text_prev', 'required'];
        }

        $rules[] = ['text_format', 'required'];

        return $rules;
    }

    public function attributeLabels() {
        return[
            "text_full" => Text::_('LIB_FIELDS_BODY_FULL_LABEL'),
            "text_prev" => Text::_('LIB_FIELDS_BODY_PREVTEXT_LABEL'),
            "text_format" => Text::_('LIB_FIELDS_BODY_FILTER_FORMAT')
        ];
    }

}
