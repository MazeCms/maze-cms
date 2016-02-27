<?php

namespace maze\fields;

use RC;
use Text;
use maze\base\Model;
use maze\db\Expression;
use maze\db\Query;

class BaseField extends Model {

    /**
     * @var int id  - поля
     */
    public $field_id;

    /**
     * @var string - группа материалов
     */
    public $bundle;

    /**
     * @var string - имя расширения
     */
    public $expansion;

    /**
     * @var int - id поля материала
     */
    public $field_exp_id;

    /**
     * @var string - заголовок метки поля
     */
    public $title;

    /**
     * @var string - подсказка поля
     */
    public $prompt;

    /**
     * @var int количество значений поля
     */
    public $many_value;

    /**
     * @var string - виджет поля
     */
    public $widget_name;

    /**
     * @var int активность поля
     */
    public $active;

    /**
     * @var string  - имя поля 
     */
    public $field_name;

    /**
     * @var array - параметры насроек 
     */
    public $param = [];

    /**
     * @var array - параметры насроек  виджета
     */
    public $widget_param = [];

    /**
     * @var int - блокировать удаление поля
     */
    protected $locked;

    /**
     * @var string - имя типа поля
     */
    protected $type;

    /**
     * @var Model - настройки поля
     */
    protected $settings;

    /**
     * @var Model - настройки поля
     */
    protected $widget;

    /**
     * @var XmlObject - мета параметры поля 
     */
    protected $config;

    /**
     * @var XmlObject - мета параметры виджеты поля 
     */
    protected $configWidget;

    /**
     * @var XmlObject - параметры вида
     */
    protected $configView;

    /**
     * @var Model - настройки  вида
     */
    protected $view;

    /**
     * @var array - колекция данных поля
     */
    protected $data = [];

    /**
     * Валидация поля
     * 
     * @return array
     */
    public function rules() {
        return [
            [['bundle', 'expansion', 'field_name', 'widget_name', 'title', 'type', 'many_value', 'locked'], 'required'],
            [['locked', 'active'], 'boolean'],
            ['field_name', 'match', 'pattern' => '/^[a-z_]{3,53}$/'],
            [['active'], 'default', 'value' => 0],
            ['many_value', 'number'],
            ['field_name', 'validFieldName'],
            ['field_exp_id', 'validScheme'],
            ['prompt', 'safe']
        ];
    }

    public function validFieldName($attr, $params) {
        if ($this->field_exp_id == null) {
            $result = (new Query())
                            ->from('{{%field_exp}}')
                            ->where([
                                'field_name' => $this->field_name,
                                'expansion' => $this->expansion,
                                'bundle' => $this->bundle
                            ])->exists();

            if ($result) {
                $this->addError($attr, 'Имя поля не является уникальным');
            }
        }
    }

    public function validScheme($attr, $params) {
        $scheme = $this->getScheme();
        if (!is_array($scheme) || empty($scheme)) {
            $this->addError($attr, 'Поле должно иметь схему базы данных');
        }
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data[] = $data;
    }

    public function resetData() {
        $this->data = [];
        return $this;
    }

    public function addData($argu = []) {
        $argu['class'] = 'lib\fields\\' . $this->type . '\Data';
        $argu['field_exp_id'] = $this->field_exp_id;
        $data = RC::createObject($argu);
        $this->setData($data);
        return $data;
    }

    public function findData(array $condition = []) {
        $fieldData = null;
        if ($this->field_exp_id) {
            $where = [
                'field_exp_id' => $this->field_exp_id
            ];
            $where = array_merge($where, $condition);
            $fieldData = RC::getDb()->cache(function($db) use ($where){ 
                return (new Query())
                            ->from($this->getTableName())
                            ->where($where)->orderBy('id')->all();
              }, null, 'fw_fields');   
            if ($fieldData) {
                foreach ($fieldData as $fl) {
                    $this->addData($fl);
                }
            }
        }
        return $fieldData;
    }

    /**
     * Схема базы данных данных поля
     * 
     * @return array Description
     */
    public function getScheme() {
        return [];
    }

    /**
     * @return boolean - проверка на новое поле
     */
    public function getIsNew() {
        return $this->field_exp_id == null;
    }

    /**
     * Возвращает связанную модель форма настроек
     */
    public function getSettings() {
        if ($this->settings == null) {
            $this->param = empty($this->param) ? [] : $this->param;
            $className = 'lib\fields\\' . $this->type . '\FieldForm';
            $this->settings = RC::createObject(array_merge(['class' => $className], $this->param));
        }
        return $this->settings;
    }

    /**
     * Возвращает связанную модель  настроек виджета
     */
    public function getWidget() {
        if ($this->widget == null && $this->widget_name) {
            $this->widget_param = empty($this->widget_param) ? [] : $this->widget_param;
            $className = 'lib\fields\\' . $this->type . '\widget\\' . $this->widget_name . '\WidgetForm';
            if (class_exists($className)) {
                $this->widget = RC::createObject(array_merge(['class' => $className], $this->widget_param));
            }
        }
        return $this->widget;
    }

    public function getIsView($name) {
        $className = 'lib\fields\\' . $this->type . '\view\\' . $name . '\Params';
        return class_exists($className);
    }

    public function getView($name) {
        if (!isset($this->view[$name])) {
            $className = 'lib\fields\\' . $this->type . '\view\\' . $name . '\Params';
            $this->view[$name] = RC::createObject(array_merge(['class' => $className]));
        }
        return $this->view[$name];
    }

    public function loadAll($data) {
        $this->load($data);
        if ($this->getSettings()) {
            $this->settings->load($data);
        }
        if ($this->getWidget()) {
            $this->widget->load($data);
        }
    }

    public function getType() {
        return $this->type;
    }

    public function getLocked() {
        return $this->locked;
    }

    /**
     * Удалить поле
     */
    public function delete() {
        $db = RC::getDb();
        $transaction = $db->beginTransaction();
        $result = false;
        if (!$this->getIsNew()) {
            try {

                $result = $db->createCommand()->delete('{{%field_exp}}', ['field_exp_id' => $this->field_exp_id])->execute();

                $isType = (new Query())
                                ->from('{{%fields}}')
                                ->innerJoin('{{%field_exp}}', '{{%field_exp}}.field_id = {{%fields}}.field_id')
                                ->where([
                                    '{{%field_exp}}.field_name' => $this->field_name,
                                    '{{%fields}}.type' => $this->type
                                ])->exists();
                if (!$isType) {
                    $db->createCommand()->delete('{{%fields}}', ['type' => $this->type, 'field_name' => $this->field_name])->execute();
                    $db->createCommand()->dropTable($this->getTableName())->execute();
                    $this->field_id = null;
                }

                $this->field_exp_id = null;


                $transaction->commit();
                RC::getCache("fw_fields")->clearTypeFull();
            } catch (\Exception $e) {
                $this->addError('field_name', $e->getMessage());
                $result = false;
                $transaction->rollBack();
            }
        }

        return $result;
    }

    /**
     * Сохранить поле
     */
    public function save() {
        $db = RC::getDb();

        try {

            try {
                $table = $db->getTableSchema($this->getTableName());
            } catch (\Exception $ex) {
                $this->createTable();                
            }
            
            if (!$this->validate()) {
                throw new \Exception();
            }
            $isType = (new Query())
                            ->from('{{%fields}}')
                            ->where([
                                'field_name' => $this->field_name,
                                'type' => $this->type
                            ])->exists();

            if (!$isType) {
                $insert = $db->createCommand()->insert('{{%fields}}', [
                            'field_name' => $this->field_name,
                            'type' => $this->type,
                            'date_create' => new Expression('NOW()')
                        ])->execute();

                if (!$insert) {
                    $this->addError('field_name', 'Ошибка создания поля в  таблицы fields');
                    throw new \Exception('Ошибка создания поля в  таблицы fields');
                }
                $table = $db->getTableSchema('{{%fields}}');
                $this->field_id = $db->getLastInsertID($table->sequenceName);
            } else {
                $field = (new Query())
                                ->from('{{%fields}}')
                                ->where([
                                    'field_name' => $this->field_name,
                                    'type' => $this->type
                                ])->one();
                $this->field_id = $field['field_id'];
            }
            $settings = $this->getSettings();

            if (is_object($settings)) {
                if (!$settings->validate()) {
                    throw new \Exception('Ошибка параметров настроек поля');
                }
                $this->param = $settings->attributes;
            }

            $widget = $this->getWidget();
            if ($widget && is_object($widget)) {
                if (!$widget->validate()) {
                    throw new \Exception('Ошибка параметров  виджета поля');
                }
                $this->widget_param = $widget->attributes;
            }


            $data = [
                'field_id' => $this->field_id,
                'field_name' => $this->field_name,
                'title' => $this->title,
                'expansion' => $this->expansion,
                'bundle' => $this->bundle,
                'many_value' => $this->many_value,
                'widget_name' => $this->widget_name,
                'active' => $this->active,
                'locked' => $this->locked,
                'prompt' => $this->prompt,
                'param' => (empty($this->param) ? null : serialize($this->param) ),
                'widget_param' => (empty($this->widget_param) ? null : serialize($this->widget_param) )
            ];

            if ($this->getIsNew()) {

                $data['sort'] = ((new Query())->from('{{%field_exp}}')->where(['expansion' => $this->expansion, 'bundle' => $this->bundle])->count() + 1);
                $field = $db->createCommand()->insert('{{%field_exp}}', $data)->execute();
                $table = $db->getTableSchema('{{%field_exp}}');
                $this->field_exp_id = $db->getLastInsertID($table->sequenceName);



                if (!$field) {
                    $this->addError('field_name', 'Ошибка создания поля в  таблицы field_exp');
                    throw new \Exception();
                }
            } else {
                $field = $db->createCommand()->update('{{%field_exp}}', $data, ['field_exp_id' => $this->field_exp_id])->execute();
            }

            RC::getCache("fw_fields")->clearTypeFull();
        } catch (\Exception $e) {
            if (!empty($e->getMessage())) {
                $this->addError('field_name', $e->getMessage());
            }
            return false;
        }
        return true;
    }

    public function getConfig() {
        if ($this->config == null) {
            $this->config = new \XMLConfig(RC::getAlias('@lib/fields/' . $this->type . '/meta.options.xml'), $this->getSettings()->attributes);
        }
        return $this->config;
    }

    public function getConfigWidget() {
        if ($this->configWidget == null && !empty($this->widget_name)) {
            $params = $this->getWidget() ? $this->getWidget()->attributes : null;
            $this->configWidget = new \XMLConfig(RC::getAlias('@lib/fields/' . $this->type . '/widget/' . $this->widget_name . '/meta.options.xml'), $params);
        }

        return $this->configWidget;
    }

    public function getConfigView($view) {
        if (!isset($this->configView[$view])) {
            $this->configView[$view] = new \XMLConfig(RC::getAlias('@lib/fields/' . $this->type . '/view/' . $view . '/meta.options.xml'), $this->getView($view)->attributes);
        }

        return $this->configView[$view];
    }

    public function render($view, $params = [], $data = null) {
        if ($this->getIsView($view)) {
            $param = $this->getView($view);
            $param->attributes = $params;
        } else {
            $param = null;
        }

        if ($data === null) {
            $data = $this->data;
        }

        if ($this->many_value != 0 && $this->many_value == 1) {
            if (is_array($data)) {
                $data = end($data);
            }
        }

        $argu = [
            'class' => '\maze\fields\ViewField',
            'field' => $this,
            'param' => $param,
            'viewName' => $view,
            'data' => $data
        ];

        if (file_exists(RC::getAlias('@lib/fields/' . $this->type . '/ViewField.php'))) {
            $argu['class'] = 'lib\fields\\' . $this->type . '\ViewField';
        }

        $viewObj = RC::createObject($argu);

        return $viewObj->render();
    }

    protected function createTable() {
             
        $db = RC::getDb();
        $colonum = $this->getScheme();
        $colonum = array_merge([
            'id' => 'pk',
            'field_exp_id' => 'integer',
            'entry_id' => 'integer',
            'id_lang' => 'integer'
                ], $colonum);
        $db->createCommand()->createTable($this->getTableName(), $colonum)->execute();
        $db->createCommand()
                ->addForeignKey($this->type . '_' . $this->field_name, $this->getTableName(), 'field_exp_id', '{{%field_exp}}', 'field_exp_id', 'CASCADE', 'CASCADE')->execute();
        
    }

    public function deleteData($condition = []) {
        $condition['field_exp_id'] = $this->field_exp_id;
        return RC::getDb()->createCommand()->delete($this->getTableName(), $condition)->execute();
    }

    public function getTableName() {
        return '{{%field_' . $this->type . '_' . $this->field_name . '}}';
    }

    public function attributeLabels() {
        return[
            "title" => Text::_("Заголовок метки поля"),
            "field_name" => Text::_("Код поля"),
            "active" => Text::_("Активность"),
            "widget_name" => Text::_("Виджет поля"),
            "many_value" => Text::_("Количество значений поля"),
            "prompt" => Text::_("Подсказка поля"),
            "id_lang" => Text::_("Язык")
        ];
    }

}
