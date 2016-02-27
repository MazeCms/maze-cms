<?php

namespace maze\helpers;

use DateTime;

class BaseDataTime {

    public static function dataCreate($date) {
        if (!$date)
            return false;
        try {
            if (is_string($date)) {
                $dateObj = new DateTime($date);
            } else {
                $dateObj = (new DateTime())->setTimestamp($date);
            }
        } catch (\Exception $e) {
            return false;
        }
        return $dateObj;
    }

    public static function diffMinutes($start, $end) {
        if (!$start || !$end)
            return false;

        $startObj = static::dataCreate($start);
        $endObj = static::dataCreate($end);
        return round(($endObj->getTimestamp() - $startObj->getTimestamp()) / 60);
    }

    public static function diff($start, $end) {
        if (!$start || !$end)
            return false;

        $startObj = static::dataCreate($start);
        $endObj = static::dataCreate($end);
        $interval = $startObj->diff($endObj);
        $date = [];

        if ($interval->d) {
            $date[] = '%D д';
        }
        if ($interval->m) {
            $date[] = '%M м';
        }
        if ($interval->y) {
            $date[] = '%Y г';
        }

        $time = [];
        if ($interval->h || $interval->i || $interval->s) {

            if ($interval->h) {
                $time[] = '%H ч';
            }

            if ($interval->i) {
                $time[] = '%I м';
            }

            if ($interval->s) {
                $time[] = '%S с';
            }
        }
        $format = '';
        if (!empty($date)) {
            $format .= implode('-', $date) . ' ';
        }

        if (!empty($time)) {
            $format .= implode(' ', $time);
        }
        return $interval->format($format);
    }
    
    public static function format($date, $format = false, $default = true)
    {
        if (!$format) {
            $format = \RC::getConfig()->get("format_date");
        }
        $search_replace = array(
            "am" => "дп",
            "pm" => "пп",
            "AM" => "ДП",
            "PM" => "ПП",
            "Monday" => "Понедельник",
            "Mon" => "Пн",
            "Tuesday" => "Вторник",
            "Tue" => "Вт",
            "Wednesday" => "Среда",
            "Wed" => "Ср",
            "Thursday" => "Четверг",
            "Thu" => "Чт",
            "Friday" => "Пятница",
            "Fri" => "Пт",
            "Saturday" => "Суббота",
            "Sat" => "Сб",
            "Sunday" => "Воскресенье",
            "Sun" => "Вс",
            "January" => "Января",
            "Jan" => "Янв",
            "February" => "Февраля",
            "Feb" => "Фев",
            "March" => "Марта",
            "Mar" => "Мар",
            "April" => "Апреля",
            "Apr" => "Апр",
            "May" => "Мая",
            "May" => "Мая",
            "June" => "Июня",
            "Jun" => "Июн",
            "July" => "Июля",
            "Jul" => "Июл",
            "August" => "Августа",
            "Aug" => "Авг",
            "September" => "Сентября",
            "Sep" => "Сен",
            "October" => "Октября",
            "Oct" => "Окт",
            "November" => "Ноября",
            "Nov" => "Ноя",
            "December" => "Декабря",
            "Dec" => "Дек",
            "st" => "ое",
            "nd" => "ое",
            "rd" => "е",
            "th" => "ое"
        );
        $date = static::dataCreate($date);
        if($date){
            if($date = $date->format($format))
            {
                return strtr($date, $search_replace);
            }
           
        }
        return $default ? $default : false;
    }

}
