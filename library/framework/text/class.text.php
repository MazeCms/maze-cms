<?php

defined('_CHECK_') or die("Access denied");

use maze\helpers\ArrayHelper;

class Text {

    public static function _($text, $args = null) {

        $text = RC::app()->getText((string)$text);    
      
        if ($args !== null && is_array($args)) {
            
            if (ArrayHelper::isAssociative($args)) {
                $p = [];
                foreach ($args as $name => $val) {
                    $p['{' . $name . '}'] = $val;
                }
                return strtr($text, $p);
            }
            return vsprintf($text, $args);
        }
        return $text;
    }
}

?>