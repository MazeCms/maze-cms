<?php

defined('_CHECK_') or die("Access denied");

class Widgets {

    private $_front;
    private $_cache;

    public function __construct() {
        $this->_front = defined("SITE") ? 1 : 0;
        $this->_cache = RC::getCache("fw_widgets");
    }

    /**
     * ПОЛУЧИТЬ НАБОР АКТИВНЫХ ВИДЖЕТОВ  ПРИВЯЗАННЫХ К МЕНЮ ИЛИ ПРИЛОЖЕНИЮ И НЕ ПРИВЯЗАННЫХ
     * 
     * @param (int) $id_menu - id текущего меню если есть, иначе null 
     * @param (int) $id_tmp - id текущего шалона, обязательный параметр
     * @param (int) $id_exp - id текушего приложения 
     * return (array) - возвращает массив виджетов, вида : массив[index] = виджет 
     * 
     */
    protected function getBindMenuExp($id_menu, $id_tmp, $id_exp) {
        $front = $this->_front;

        $id_role = Access::instance()->getIdRole();


        $id_lang = RC::app()->lang->getIdLang();

        $date = date("Y-m-d H:i:s");
        $result = RC::getDb()->cache(function($db) use ($front, $id_tmp, $id_role, $id_lang, $id_menu, $id_exp) {
            return (new maze\table\Widgets())->find()
                            ->addSelect(['wid.*', 'app.front_back'])
                            ->from(['wid' => maze\table\Widgets::tableName()])
                            ->joinWith([
                                'menu',
                                'exp',
                                'url',
                                'accessRole',
                                'app' => function($query)use($front) {
                                    $query->andWhere(['app.front_back' => $front]);
                                }
                                    ])
                            ->where(['wid.id_tmp' => $id_tmp])
                            ->andWhere(['wid.enabled' => 1])
                            ->andWhere(['or', 'wid.id_lang=:id_lang', 'wid.id_lang=0'])
                            ->andWhere(['or', 'wid.time_active<=NOW()', 'wid.time_active is null'])
                            ->andWhere(['or', 'wid.time_inactive>=NOW()', 'wid.time_inactive is null'])
                            ->andWhere(['or', ['in', 'ar.id_role', (!empty($id_role) ? $id_role : 0)], 'ar.id_role is null'])
                            ->andWhere(['or', 'menu.id_menu=:id_menu', 'menu.id_menu is null'])
                            ->andWhere(['or', 'exp.id_exp=:id_exp', 'exp.id_exp is null'])
                            ->groupBy('wid.id_wid')
                            ->orderBy('wid.position, wid.ordering')
                            ->params([':id_lang' => $id_lang, ':id_menu' => $id_menu, ':id_exp' => $id_exp])
                            ->asArray()
                            ->all();
            }, null, "fw_widgets");

        return $result;
    }

    protected function getShortMenuExp($id_menu, $id_exp) {
        $front = $this->_front;

        $id_role = Access::instance()->getIdRole();

        $id_lang = RC::app()->lang->getIdLang();

        $date = date("Y-m-d H:i:s");

        $result = RC::getDb()->cache(function($db) use ($front, $id_role, $id_lang, $id_menu, $id_exp) {
            return (new maze\table\Widgets())->find()
                            ->addSelect(['wid.*', 'app.front_back'])
                            ->from(['wid' => maze\table\Widgets::tableName()])
                            ->joinWith([
                                'menu',
                                'exp',
                                'url',
                                'accessRole',
                                'app' => function($query)use($front) {
                                    $query->andWhere(['app.front_back' => $front]);
                                }
                                    ])
                                    ->where(['wid.id_tmp' => 0])
                                    ->andWhere(['wid.enabled' => 1])
                                    ->andWhere(['or', 'wid.id_lang=:id_lang', 'wid.id_lang=0'])
                                    ->andWhere(['or', 'wid.time_active<=NOW()', 'wid.time_active is null'])
                                    ->andWhere(['or', 'wid.time_inactive>=NOW()', 'wid.time_inactive is null'])
                                    ->andWhere(['or', ['in', 'ar.id_role', (!empty($id_role) ? $id_role : 0)], 'ar.id_role is null'])
                                    ->andWhere(['or', 'menu.id_menu=:id_menu', 'menu.id_menu is null'])
                                    ->andWhere(['or', 'exp.id_exp=:id_exp', 'exp.id_exp is null'])
                                    ->groupBy('wid.id_wid')
                                    ->orderBy('wid.position, wid.ordering')
                                    ->params([':id_lang' => $id_lang, ':id_menu' => $id_menu, ':id_exp' => $id_exp])
                                    ->asArray()
                                    ->all();
            }, null, "fw_widgets");

        return $result;
    }

    /**
     * СОРТИРОВКА ВИДЖЕТОВ ПО ПОЗИЦИМЯ
     * 
     * @param (array) $widget - результирующий массив виджетов привязанных к меню и привязанных к приложению
     * return (array)  - возвращает массив разбитый на позици, вида : массив[название позиции][index] = виджет
     */

    protected function getPosition($widget) {
        $result = array();

        foreach ($widget as $wid) {
            $key = $wid['position'];
            unset($wid['menu'], $wid['exp'], $wid['accessRole'], $wid['app']);
            if (array_key_exists($key, $result)) {
                array_push($result[$key], $wid);
            } else {
                $result[$key] = array();
                array_push($result[$key], $wid);
            }
        }
        return $result;
    }
    
    protected function filter($widgets){
        $request = RC::app()->request;
    
        foreach($widgets as $key=>$wid){
            if($wid['enable_php'] && !empty($wid['php_code'])){
                try {
                    $result = create_function('$wid', $wid['php_code']);
                    if(!$result($wid)){
                        unset($widgets[$key]);
                        continue;
                    }
                } catch (Exception $exc) {}
            }
            if(!empty($wid['url'])){
                $visible = false;
                foreach($wid['url'] as $url){
                    if($url['method'] == 'get' || $url['method'] == 'post'){                       
                        
                        if($url['method'] == 'get'){                       
                            $name = $request->get($url['name']);
                        }
                        elseif($url['method'] == 'post'){
                            $name = $request->post($url['name']);
                        }
                        
                        if($name !== false){
                            if(!empty($url['value'])){
                                if($url['value'] == $name){
                                    $visible = $url['visible'] ? true : false;
                                    break;
                                }
                            }
                            else{
                                $visible = $url['visible'] ? true : false;
                                break;
                            }
                        }
                        
                    }                    
                    elseif($url['method'] == 'url'){
                        $current = URI::instance()->toString(['path']);
                        $parsePath = explode('/',  trim($url['value'], '/'));
                        $parseLength = count($parsePath);
                        if(end($parsePath) == "*"){
                            $i=count($parsePath)-1;                            
                            while (isset($parsePath[$i]) && end($parsePath) == "*"){
                                array_pop($parsePath);
                                $i--;
                            }
                            $cutentPath = URI::instance()->toString(['path']);
                            $parsePath = '/'.implode('/',$parsePath);
                            $parseCurent = explode('/',  trim($cutentPath, '/'));
                            if($parseLength == count($parseCurent) && mb_stripos($cutentPath, $parsePath) !== false){
                                $visible = true;
                                break;
                            }
                        }
                        if($current == $url['value']){
                            $visible = $url['visible'] ? true : false;
                            break;
                        }
                    }
                }
                
                if(!$visible){
                    unset($widgets[$key]);
                }
            }
        }
        return $widgets;
    }

    /**
     * ПОЛУЧИТЬ НАБОР АКТИВНЫХ  ВИДЖИТОВ
     * 
     * @param (int) $id_menu - id текущего меню если есть, иначе null
     * @param (int) $id_tmp - id текущего шалона, обязательный параметр
     * @param (int) $id_exp - id текушего приложения
     * return (array) - возвращает отсотрированый массив виджетов, вида : массив[название позиции][index] = виджет
     */

    public function getWidgets($id_menu, $id_tmp, $id_exp) {

        $id_menu = (!empty($id_menu) && $id_menu !== null) ? $id_menu : 0;

        $id_role = (!empty($id_role) && $id_role !== null) ? $id_role : 0;

        $widgetMenu = $this->getBindMenuExp($id_menu, $id_tmp, $id_exp); // Получить набор активных виджетов  привязанных к меню и не привязанных
        $widgetMenu = $this->filter($widgetMenu);
       
        
        return $this->getPosition($widgetMenu); // Сортировка виджетов по позицимя	
    }

    public function getShortWidgets($id_menu, $id_exp) {
        $id_menu = (!empty($id_menu) && $id_menu !== null) ? $id_menu : 0;

        $widgetMenu = $this->getShortMenuExp($id_menu, $id_exp); // Получить набор активных виджетов  привязанных к меню и не привязанных
        $widgetMenu = $this->filter($widgetMenu);
        
        return $this->getPosition($widgetMenu); // Сортировка виджетов по позицимя	
    }

}

?>