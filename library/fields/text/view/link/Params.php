<?php

namespace lib\fields\text\view\link;

use RC;
use maze\base\Model;
use maze\table\DictionaryTerm;
use maze\table\Contents;
use maze\helpers\ArrayHelper;

class Params extends Model {

    /**
     * @var int длина поля
     */
    public $url;
    public static $cacheTerm = [];

    public function rules() {
        return [
            ['url', 'required']
        ];
    }

    public function getRealUrl($data) {
        $params = [];
      
        foreach ($data->attributes as $name => $val) {
            $params['{' . $name . '}'] = $val;
        }
        
        if (mb_strpos($this->url, '{term.alias}') !== false && $data->field->expansion == 'dictionary') {
            if (!isset(static::$cacheTerm[$data->entry_id])) {
                static::$cacheTerm[$data->entry_id] = RC::getDb()->cache(function($db) use ($data) {
                    return DictionaryTerm::find()
                                    ->from(['dt' => DictionaryTerm::tableName()])
                                    ->joinWith(['route'])->where(['dt.term_id' => $data->entry_id])->one();
                }, null, 'fw_fields');
            }

            if (static::$cacheTerm[$data->entry_id]) {
                $params['{term.alias}'] = static::$cacheTerm[$data->entry_id]->route->alias;
            }
        
        }
        
        return strtr($this->url, $params);
    }

}
