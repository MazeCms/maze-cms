<?php

namespace lib\fields\body\widget\body;

use RC;
use Text;
use maze\helpers\Html;
use maze\base\JsExpression;

class Widget extends \maze\fields\BaseWidget {

    public $enableprev;
    public $enablefull;

    public function run() {
       
        if(!$this->data[0]->text_format){
            $this->data[0]->text_format = $this->field->settings->filterDefault;
        }
        
        
        return $this->render('index', [
                    'widget' => $this,
                    'form' => $this->form
        ]);
    }

    public function getListFilter() {

        $filter = $this->field->settings->filter;

        $result = [];
        if (in_array('stripHtml', $filter)) {
            $result['stripHtml'] = Text::_("LIB_FIELDS_BODY_FILTER_STRIPTAG");
        }
        if (in_array('filterHtml', $filter)) {
            $result['filterHtml'] = Text::_("LIB_FIELDS_BODY_FILTER_FILTER");
        }
        if (in_array('fullHtml', $filter)) {
            $result['fullHtml'] = Text::_("LIB_FIELDS_BODY_FILTER_FULLHTML");
        }
        if (in_array('php', $filter)) {
            $result['php'] = Text::_("LIB_FIELDS_BODY_FILTER_PHPCODE");
        }
        return $result;
    }

}
