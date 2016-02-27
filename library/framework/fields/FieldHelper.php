<?php

namespace maze\fields;

use RC;
use maze\db\Query;
use maze\fields\BaseWidget;
use maze\helpers\FileHelper;

class FieldHelper {

    /**
     * @var array - коллекция полей
     */
    protected static $fields = [];

    /**
     * @var array - коллекция формы полей 
     */
    protected static $form = [];

    /**
     * @var array - коллекция   виджетов полей 
     */
    protected static $widget = [];

    /**
     * @var array - коллекция   видов полей
     */
    protected static $view = [];

    /**
     * Поулчить поля
     * @param array $condition параметры отбора списка полей
     * @return array  - список достпуных полей вида [['name'=>'title'], ['name'=>'title']...]
     */
    public static function listAllFieds($condition = []) {
        $path = scandir(RC::getAlias('@lib/fields'));
        $result = [];
        foreach ($path as $field) {
            if ($field !== '..' || $field !== '.') {
                if (file_exists(RC::getAlias('@lib/fields/' . $field . '/Field.php'))) {
                    $info = static::getInfoField($field, $condition);
                    if($info){
                        $result[$field] = $info->get('name');
                    }
                    
                }
            }
        }
        return $result;
    }

    /**
     * Получить все виджеты поля
     * 
     * @param  array - список виджетов поля
     */
    public static function listFieldWidget($name) {
        $path = scandir(RC::getAlias('@lib/fields/' . $name . '/widget'));
        $result = [];
        foreach ($path as $widget) {
            if ($widget !== '..' && $widget !== '.') {
                if (file_exists(RC::getAlias('@lib/fields/' . $name . '/widget/' . $widget . '/Widget.php'))) {
                    $result[$widget] = static::getInfoWidget($name, $widget)->get('name');
                }
            }
        }
        return $result;
    }

    /**
     * Получить все виды поля
     * 
     * @param  array - список видов поля
     */
    public static function listFieldView($name) {
        $path = scandir(RC::getAlias('@lib/fields/' . $name . '/view'));
        $result = [];


        foreach ($path as $view) {
            if ($view !== '..' && $view !== '.') {

                $viewObj = static::getInfoView($name, $view);
                $result[$viewObj->get('view')] = $viewObj->get('name');
            }
        }
        return $result;
    }

    /**
     * МЕта данные поля
     * 
     * @param string $name - имя поля
     * @param array $condition параметры отбора списка полей
     * 
     * @return XMLConfig
     */
    public static function getInfoField($name, $condition = []) {
        if (!isset(static::$form[$name])) {
            $info = new \XMLConfig(RC::getAlias('@lib/fields/' . $name . '/meta.options.xml'));
            $resultCond = [];
            foreach ($condition as $name => $value) {

                if($name == 'group'){
                    $listVal = preg_split("/,[\s]+|,/s", $value);
                    $listGroup = preg_split("/,[\s]+|,/s",$info->get($name));
                    foreach($listVal as $v){
                        if(in_array($v, $listGroup)){
                          $resultCond[$name] = $value;
                          break;
                        }
                    }
                    
                }
                elseif ($info->get($name) !== null && $info->get($name) == $value) {
                    $resultCond[$name] = $value;
                }
            }
            if ($resultCond == $condition) {
                static::$form[$name] = $info;
            }else{
               static::$form[$name] = null; 
            }
        }
        return static::$form[$name];
    }

    /**
     * МЕта данные    виджета
     * 
     * @param string $type - имя поля
     * @param string $name - имя виджета
     * @return XMLConfig
     */
    public static function getInfoWidget($type, $name) {
        if (!isset(static::$view[$type][$name])) {
            static::$view[$type][$name] = new \XMLConfig(RC::getAlias('@lib/fields/' . $type . '/widget/' . $name . '/meta.options.xml'));
        }
        return static::$view[$type][$name];
    }

    /**
     * МЕта данные    вида поля
     * 
     * @param string $type - имя поля
     * @param string $name - имя виджета
     * @return XMLConfig
     */
    public static function getInfoView($type, $name) {
        if (!isset(static::$widget[$type][$name])) {
            static::$widget[$type][$name] = new \XMLConfig(RC::getAlias('@lib/fields/' . $type . '/view/' . $name . '/meta.options.xml'));
        }
        return static::$widget[$type][$name];
    }

    /**
     * Создание поля
     * 
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     * $filed = maze\fields\FieldHelper::createField('title',[
     *      'expansion'=>'user', 
     *      'bundle'=>'reguser',
     *      'many_value'=>1,
     *      'widget_name'=>'input', 
     *      'field_name'=>'username', 
     *      'title'=>'Заголовок'
     *   ]);
     *  $filed->save();
     * 
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     * 
     * @param string $name - имя поля
     * @param array  $arg - сойства поля
     * @return maze\fields\BaseField - экземпляр класса Поля
     * @throws \Exception
     */
    public static function createField($name, $arg = []) {
        $path = RC::getAlias('@lib/fields/' . $name);
        if (file_exists($path . DS . 'Field.php')) {

            unset($arg['sort']);
            return RC::createObject(array_merge(['class' => 'lib\fields\\' . $name . '\Field'], $arg));
        } else {
            throw new \Exception("Данного  поля " . $name . " не сущесвует");
        }
    }

    /**
     * поиск поля по ID
     * 
     * @param int $id - ID поля
     * @return maze\fields\BaseField
     */
    public static function findByID($id) {
        if (!isset(static::$fields[$id])) {
            $field = RC::getDb()->cache(function($db) use ($id){ 
                return (new Query())
                            ->select(['{{%field_exp}}.*', '{{%fields}}.type'])
                            ->from('{{%field_exp}}')
                            ->innerJoin('{{%fields}}', '{{%field_exp}}.field_id = {{%fields}}.field_id')
                            ->where(['field_exp_id' => $id])->one();
              }, null, 'fw_fields');
              
            if ($field) {
                if (!empty($field['param'])) {
                    $field['param'] = unserialize($field['param']);
                }
                if (!empty($field['widget_param'])) {
                    $field['widget_param'] = unserialize($field['widget_param']);
                }
                static::$fields[$id] = static::createField($field['type'], $field);
            } else {
                static::$fields[$id] = null;
            }
        }
        return static::$fields[$id];
    }

    /**
     * Поиск поля по условию
     * 
     * @param array $condition - условие поиска заголовка
     */
    public static function find(array $condition) {

        $field =  RC::getDb()->cache(function($db) use ($condition){ 
            return (new Query())
                        ->select(['{{%field_exp}}.*', '{{%fields}}.type'])
                        ->from('{{%field_exp}}')
                        ->innerJoin('{{%fields}}', '{{%field_exp}}.field_id = {{%fields}}.field_id')
                        ->where($condition)->one();
         }, null, 'fw_fields');
        if ($field) {
            if (!empty($field['param'])) {
                $field['param'] = unserialize($field['param']);
            }
            if (!empty($field['widget_param'])) {
                $field['widget_param'] = unserialize($field['widget_param']);
            }
            return static::$fields[$field['field_exp_id']] = static::createField($field['type'], $field);
        }

        return null;
    }

    public static function findAll(array $condition) {
        $fields = RC::getDb()->cache(function($db) use ($condition){ 
         return (new Query())
                        ->select(['{{%field_exp}}.*', '{{%fields}}.type'])
                        ->from('{{%field_exp}}')
                        ->innerJoin('{{%fields}}', '{{%field_exp}}.field_id = {{%fields}}.field_id')
                        ->where($condition)->orderBy('{{%field_exp}}.sort')->all();
         }, null, 'fw_fields');
        if ($fields) {
            $result = [];

            foreach ($fields as $field) {
                if (!empty($field['param'])) {
                    $field['param'] = unserialize($field['param']);
                }
                if (!empty($field['widget_param'])) {
                    $field['widget_param'] = unserialize($field['widget_param']);
                }
                $result[] = static::$fields[$field['field_exp_id']] = static::createField($field['type'], $field);
            }

            return $result;
        }


        return null;
    }

    /**
     * Поле виджета 
     * 
     * @param tymaze\fields\BaseFieldpe $field Description
     */
    public static function rendertWidget($conf = []) {

        if (isset($conf['field'])) {
            $field = $conf['field'];
        } else {
            
        }
        if (!isset($conf['class'])) {
            $conf['class'] = 'lib\fields\\' . $field->type . '\widget\\' . $field->widget_name . '\Widget';
        }


        $widget = $field->getWidget();
        $widget = $widget ? ($widget->attributes ? $widget->attributes : []) : [];
        $conf['field'] = $field;
        $conf += $widget;

        return BaseWidget::element($conf);
    }

}
