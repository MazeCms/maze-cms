<?php

namespace maze\application;

use RC;
use maze\base\Object;
use maze\helpers\ArrayHelper;
use maze\table\Expansion;
use maze\exception\UserException;
use ui\assets\AssetExp;

class Component extends Object {

    protected $_name;
    protected $_config;
    protected $_front;
    protected $_exp;
    protected $_render;
    protected $_assets;

    public function init() {

        if ($this->_front === null) {
            $this->_front = defined('SITE') ? 1 : 0;
        }
        $this->_exp = RC::getDb()->cache(function($db) {
            $result = Expansion::find()->from(["exp" => Expansion::tableName()])
                    ->innerJoinWith('installApp')
                    ->andOnCondition(['ia.name' => $this->_name, 'ia.front_back' => $this->_front]);
            if ($this->_front) {
                $result->andOnCondition(['exp.enabled' => 1]);
            }
            return $result->one();
        }, null, 'fw_system');



        $this->_assets = new AssetExp([
            'basePath' => $this->getPath('assets'),
            'baseUrl' => ($this->_front ? '' : 'admin/') . 'expansion/exp_' . $this->_name . '/assets'
        ]);
    }

    public function setName($name) {
        $this->_name = $name;
    }

    public function getName() {
        return $this->_exp->name;
    }

    public function getId_tmp() {
        return $this->_exp->id_tmp;
    }

    public function getId_exp() {
        return $this->_exp->id_exp;
    }

    public function getTime_cache() {
        return $this->_exp->time_cache;
    }

    public function getEnable_cache() {
        return $this->_exp->enable_cache;
    }

    public function getEnabled() {
        return $this->_exp->enabled;
    }

    public function getPathIndex() {
        return RC::getAlias('@root/' . ( $this->_front ? '' : 'admin/') . 'expansion/exp_' . $this->_name . '/' . $this->_name . ".php");
    }

    public function getPath($path = null) {
        $path = $path ? DS . $path : '';
        return dirname($this->getPathIndex()) . $path;
    }

    public function getUrl($url = null) {
        $url = $url ? '/' . ltrim($url, '/') : '';
        return $this->_assets->getAssetBaseUrl() . $url;
    }

    public function getIs() {
        if (!$this->_exp)
            return false;

        if (!file_exists($this->getPathIndex()))
            return false;

        return true;
    }

    public function run() {
        if ($this->_render === null) {
            ob_start();
            $out = include_once $this->getPathIndex();
            if (!is_string($out))
                $out = '';

            $this->_render = ob_get_clean() . $out;
        }
        return $this->_render;
    }

    public function getConfig() {
        if ($this->_config !== null)
            return $this->_config;
        $this->_config = \RC::getConf(["type" => "expansion", "name" => $this->_name]);
        $this->_config->setValue($this->_exp->param);
        return $this->_config;
    }

}

?>