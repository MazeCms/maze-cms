<?php

namespace ui\date;

use ui\select\Chosen;
use maze\table\Languages;
use maze\helpers\DataTime;

class FormatDate extends Chosen {

    public function init() {
        parent::init();

        $date = time();
      
        $this->items = [
            'd F Y h:i:s'=>DataTime::format($date, 'd F Y h:i:s'),
            'd-m-Y h:i:s'=>DataTime::format($date, 'd-m-Y h:i:s'),
            'Y-m-d h:i:s'=>DataTime::format($date, 'Y-m-d h:i:s'),
            'd.m.Y h:i:s'=>DataTime::format($date, 'd.m.Y h:i:s'),
            'Y.m.d h:i:s'=>DataTime::format($date, 'Y.m.d h:i:s'),
            'd F Y'=>DataTime::format($date, 'd F Y'),
            'd-m-Y'=>DataTime::format($date, 'd-m-Y'),
            'Y-m-d'=>DataTime::format($date, 'Y-m-d'),
            'd.m.Y'=>DataTime::format($date, 'd.m.Y'),
            'Y.m.d'=>DataTime::format($date, 'Y.m.d')
        ];
    }

}
