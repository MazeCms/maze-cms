<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileCache
 *
 * @author nick
 */

namespace maze\cache;

use maze\helpers\FileHelper;
use RC;

class FileCache extends \maze\cache\Cache {

    public $path = '@root/temp/cache';
    protected $fullPath;

    public function init() {


        $this->fullPath = RC::getAlias($this->path . '/' . $this->type);

        if (!is_dir($this->fullPath)) {
            FileHelper::createDirectory($this->fullPath);
        }
    }

    protected function getCacheFileName($key) {

        return $fileName = "cache-" . $this->type . "-" . $key . ".bin";
    }

    protected function getValue($key) {

        $file = $this->fullPath . DS . $this->getCacheFileName($key);

        if (file_exists($file)) {
            if (filemtime($file) < time()) {
                $this->deleteValue($key);
                return false;
            }
            return file_get_contents($file);
        }

        return false;
    }

    protected function setValue($key, $value) {
        $timeLast = time() + (intval($this->time));

        if (time() > $timeLast)
            return false;

        $this->deleteValue($key);
        $file = $this->fullPath . DS . $this->getCacheFileName($key);
        if (is_dir($this->fullPath)) {
            file_put_contents($file, $value);
            touch($file, $timeLast);
        }
    }

    protected function deleteValue($key) {
        $file = $this->fullPath . DS . $this->getCacheFileName($key);
        if (file_exists($file)) {
            return @unlink($file);
        }
        return false;
    }

    protected function deleteType() {
        if (is_dir($this->fullPath)) {
            return FileHelper::remove($this->fullPath);
        }
        return false;
    }

    public function fullClear() {
        if (is_dir(RC::getAlias($this->path))) {
            return FileHelper::remove(RC::getAlias($this->path));
        }
        return false;
    }

}
