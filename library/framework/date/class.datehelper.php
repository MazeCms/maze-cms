<?php

defined('_CHECK_') or die("Access denied");

class Datehelper {

    private static $_instance;

    public static function instance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        
    }

    public function diffCurrent($date, $length = false) {
        $arr = $this->timespan(strtotime($date), time());
        if ($length) {
            $arr = array_slice($arr, 0, $length);
        }
        return join(" ", $arr);
    }

    public function get_date($date, $first, $second, $third) {
        if ((($date % 10) > 4 && ($date % 10) < 10) || ($date > 10 && $date < 20)) {
            return $second;
        }
        if (($date % 10) > 1 && ($date % 10) < 5) {
            return $third;
        }
        if (($date % 10) == 1) {
            return $first;
        } else {
            return $second;
        }
    }

    function timespan($seconds = 1, $time = '') {
        if (!is_numeric($seconds)) {
            $seconds = 1;
        }
        if (!is_numeric($time)) {
            $time = time();
        }
        if ($time <= $seconds) {
            $seconds = 1;
        } else {
            $seconds = $time - $seconds;
        }

        $str = array();
        $years = floor($seconds / 31536000);

        if ($years > 0) {
            $str[] = $years . ' ' . $this->get_date($years, 'год', 'лет', 'года');
        }

        $seconds -= $years * 31536000;
        $months = floor($seconds / 2628000);

        if ($years > 0 OR $months > 0) {
            if ($months > 0) {
                $str[] = $months . ' ' . $this->get_date($months, 'месяц', 'месяцев', 'месяца');
            }

            $seconds -= $months * 2628000;
        }

        $weeks = floor($seconds / 604800);

        if ($years > 0 OR $months > 0 OR $weeks > 0) {
            if ($weeks > 0) {
                $str[] = $weeks . ' ' . $this->get_date($weeks, 'неделю', 'недель', 'недели');
            }

            $seconds -= $weeks * 604800;
        }

        $days = floor($seconds / 86400);

        if ($months > 0 OR $weeks > 0 OR $days > 0) {
            if ($days > 0) {
                $str[] = $days . ' ' . $this->get_date($days, 'день', 'дней', 'дня');
            }

            $seconds -= $days * 86400;
        }

        $hours = floor($seconds / 3600);

        if ($days > 0 OR $hours > 0) {
            if ($hours > 0) {
                $str[] = $hours . ' ' . $this->get_date($hours, 'час', 'часов', 'часа');
            }

            $seconds -= $hours * 3600;
        }

        $minutes = floor($seconds / 60);

        if ($days > 0 OR $hours > 0 OR $minutes > 0) {
            if ($minutes > 0) {
                $str[] = $minutes . ' ' . $this->get_date($minutes, 'минута', 'минут', 'минуты');
            }

            $seconds -= $minutes * 60;
        }

        if ($str == '') {
            $str[] = $seconds . ' ' . $this->get_date($seconds, 'секунда', 'секунд', 'секунды');
        }

        return $str;
    }

}
