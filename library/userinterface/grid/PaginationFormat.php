<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pagination
 *
 * @author nick
 */

namespace ui\grid;

use maze\base\Object;
use RC;

class PaginationFormat extends Object {

    /**
     * @var int - текушая страница по умолчанию
     */
    public $page = 1;

    /**
     * @var int - число записей на странице по умолчанию
     */
    public $rowNum;

    /**
     * @var string - название переменной текущая страница
     */
    public $pageName = "page";

    /**
     * @var string - название переменной число записей на странице 
     */
    public $rowName = "pnumber";

    /**
     * @var \maze\db\ActiveRecord - Объект модели 
     */
    public $model;
    
    /**
     * @var array - массив объектов модели AR
     */
    protected $data;
    
    /**
     * @var int число записей в БД 
     */
    protected $totalPage;

    public function init() {

        if (empty($this->model) || !is_object($this->model)) {
            return false;
        }

        $model = $this->model;     
        $refClass = new \ReflectionClass($model);
        
        if ($refClass->isSubclassOf('\maze\db\ActiveRecord')) {
            $object = $model::find();
        } elseif ($refClass->getName() == 'maze\db\ActiveQuery' || $refClass->isSubclassOf('\maze\db\ActiveQuery')) {
            $object = $model;
        } else {
            throw new \Exception("Неявляется объектом \maze\db\ActiveQuery или массивом");
        }
        
    
        $this->totalPage = $object->count();
        
        $pnumber = $this->getRowNum();
        $this->page = $this->getPageNum();
 
        if ($pnumber > 0 && $this->page > ceil($this->totalPage / $pnumber)) {
            $this->page = 1;
        }
        $first = ($this->page - 1) * $pnumber;
        
        if ($pnumber && ($this->totalPage - $pnumber) > 0) {
                $object->offset($first)->limit($pnumber);
        }
       
        
        $this->data = $object->all();
        
    }
    public function getCountPage(){
        return ceil($this->totalPage / $this->getRowNum());
    }

    public function getRowNum() {
        $conf = \RC::getConfig();
        $pnumber = RC::app()->request->get($this->rowName);
        if (!$pnumber) {
            $pnumber = $this->rowNum ? $this->rowNum : $conf->page_number;
        }
        return $pnumber;
    }
    
    public function getPageNum() {
        $number = RC::app()->request->get($this->pageName);
        return !$number ? $this->page : $number;
    }
    
    public function getTotalPage() {
        return $this->totalPage;
    }
    
    public function getData(){
        return $this->data;
    }

}
