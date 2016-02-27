<?php

namespace ui\date;

use ui\select\Chosen;
use maze\table\Languages;

class TimeZone extends Chosen {

    public function init() {
        parent::init();
        $time_zone = timezone_identifiers_list();
        $option = array();
        foreach ($time_zone as $zone) {
            $parse = explode("/", $zone);
            if (count($parse) >= 2) {
                $label = $parse;
                unset($label[0]);
                $label = implode("/", $label);
                if (array_key_exists($parse[0], $option)) {
                    $option[$parse[0]][$zone] = $label;
                } else {
                    $option[$parse[0]] = [];
                    $option[$parse[0]][$zone] = $label;
                }
            } else {
                if($zone == 'UTC') continue;
                $option[$zone] = $zone;
            }
        }
      
        $this->items = $option;
    }

}
