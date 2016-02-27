<?php

defined('_CHECK_') or die("Access denied");
use maze\exception\UserException;

class Event {

    public static $_object = array();
    public $type_event; // тип события (загрузка плагинов по типу)

    public function setObject($key, $event) {

        if (!array_key_exists($key, self::$_object)) {
            self::$_object[$key] = array();
        }

        array_push(self::$_object[$key], $event);
    }

    public static function triggerHandlerList($type, $metod, $arguments) {

        if (array_key_exists($type, self::$_object)) {

            foreach (self::$_object[$type] as $event) {

                if (is_object($event)) {
                    $classname = get_class($event);

                    if (!class_exists($classname)) {
                        throw new UserException(Text::_("LIB_FRAMEWORK_APPLICATION_LOADERPLUGIN_NOCLASS", array($classname)), 404);
                        continue;
                    }
                    $refEvent = new ReflectionClass($classname);

                    if (!$refEvent->hasMethod($metod))
                        continue;

                    $metodEvent = $refEvent->getMethod($metod);

                    if ($metodEvent->getNumberOfRequiredParameters() > count($arguments)) {
                        throw new UserException(Text::_("LIB_FRAMEWORK_APPLICATION_EVENT_PARAMETERS", array($metod, $classname)), 500);
                        continue;
                    }
                    if ($arguments) {
                        $metodEvent->invokeArgs($event, $arguments);
                    } else {
                        $metodEvent->invoke($event);
                    }
                } elseif (is_array($event)) {
                    if ($metod !== $event["event"])
                        continue;

                    if (!function_exists($event["name"]))
                        continue;

                    $refEvent = new ReflectionFunction($event["name"]);

                    if ($refEvent->getNumberOfRequiredParameters() > count($arguments)) {
                        throw new UserException(Text::_("LIB_FRAMEWORK_APPLICATION_EVENT_PARAMETERS", array($metod, $classname)), 500);
                        continue;
                    }

                    if ($arguments) {
                        $refEvent->invokeArgs($arguments);
                    } else {
                        $refEvent->invoke();
                    }
                }
            }
        }
    }

    public static function addEventHandler($type, $event, $function_name) {
        self::setObject($type, array("event" => $event, "name" => $function_name));
    }

}

?>