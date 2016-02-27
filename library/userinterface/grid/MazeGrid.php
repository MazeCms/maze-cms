<?php

namespace ui\grid;

use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;
use ui\grid\GridFormat;
use maze\table\Grids;
use ui\assets\AssetGrid;

class MazeGrid extends Elements {

    public $model;
    public $settings;

    /**
     *
     * @var array - подключамые плагины
     */
    public $plugins = ["checkbox", "core", "tree", "movesort", "contextmenu", "tooltip", "buttonfild", "edits", "tooltip_content"];
    public $optionsplg = [];

    /**
     *
     * @var type url запросов json
     */
    public $url = "";
    public $mode = "default";
    public $datatype = "server";
    public $pagename = "number";
    public $ordername = "order";
    public $fildname = "colonum";
    public $rowname = "pnumber";
    public $page = 1;
    public $sortfild = "";
    public $sortorder = "asc";
    public $params;
    /**
     * @var bollean - показывать скрывать подвал 
     */
    public $showBottomPanel = true;

    /**
     *
     * @var string - callback фильтрация json объекта function(data){return data.html}
     */
    public $filterData;
    public $colHide = [];
    public $grouping = true;
    public $opengroup = true;
    public $labelmenu = ["textasc" => "По возрастанию", "textdesc" => "По убыванию", "textgroup" => "Группировать"];
    public $textpreloader = "Загрузка...";
    public $data = [];
    /**
     *
     * @var string - параметры групировки
     * groupName: function(arr, fild, titleFild){var title = 'Группировка ';  $.each(titleFild, function(i, val){ title += ' '+val+' - '+arr[i] }); return title}
     */
    public $groupingView = [];
    public $sortCol = [];
    public $colModel = [];
    public $rowNum;
    public $minWidth = 600;
    public $height = "auto";
    public $overlay = true;
    public $rowList = [3, 5, 10, 20, 30, 40, 50, 60, 80, 70];
    public $labelinfo = "Показано {FIRST} - {LAST} из {SIZE}";
    public $labelrow = "Записей";
    public $labelpage = "из {SIZE}";
    public $buttons;

    public function init() {
        if (!isset($this->settings['id'])) {
            $this->settings['id'] = $this->getId();
        }
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function setModel($model) {
        $this->colModel = $model;
    }

    public function setPlugin($name, $opt) {
        $this->optionsplg[$name] = $opt;
    }

    public function setGroup($prop, $value) {
        $res[$prop] = $value;
        $this->groupingView = array_merge($this->groupingView, $res);
        return $this->groupingView;
    }
    
    public function setParams($param)
    {
        $this->params = $param;
    }

    public function getOptions() {
        $result = array();

        $model = $this->model;
        if ($model) {
            $refClass = new \ReflectionClass($model);
            if (!$refClass->isSubclassOf('\maze\db\ActiveRecord')) {
                $model = null;
            } else {
                $model = new $model();
            }
        }

        if ($this->colModel) {
            $colModel = $this->colModel;
            $this->colModel = [];
         
            foreach ($colModel as $key => $col) {
                if (isset($col['visible']) && !$col['visible']) {
                        continue;
                }
                if ($model && !isset($col['title']) && isset($col['index'])) {
                   
                    if (preg_match('/([a-z_-]+\.)?([a-z_-]+)/i', $col['index'], $matches)) {
                        $col['title'] = $model->getAttributeLabel($matches[2]);
                    }
                }
                $this->colModel[] = $col;
            }
        } elseif (is_object($model)) {
            $field = array_keys($model->getAttributes());
            foreach ($field as $fl) {
                $this->colModel[] = [
                    'name' => $fl,
                    'index' => $fl,
                    'title' => $model->getAttributeLabel($fl),
                    "hidefild" => true,
                    "align" => "left",
                    "sorttable" => true,
                    "grouping" => true
                ];
            }
        }
        $grid = Grids::findOne($this->settings['id']);
        
        $result["colModel"] = $this->colModel;

        $result["plugins"] = implode(",", $this->plugins);

        $url = new \URI($this->url ? ($this->url) : \URI::current());
        $url->setVar('clear', 'ajax');

        $result["url"] = $this->url = $url->toString(['path', 'query', 'fragment']);

        if ($grid && $grid->page) {
            $result["page"] = $grid->page;
        } else {
            $result["page"] = $this->page ? $this->page : 1;
        }
        if($this->datatype == 'local')
        {
           $result["data"] = $this->data;
        }
        if($this->buttons !== null){
            
            $result["buttons"] = $this->buttons;
        }
        $result["datatype"] = $this->datatype;
        $result["pagename"] = $this->pagename;
        $result["ordername"] = $this->ordername;
        $result["fildname"] = $this->fildname;
        $result["rowname"] = $this->rowname;
        $result["showBottomPanel"] = $this->showBottomPanel;
        if($grid && $grid->sortfild)
        {
            $this->sortfild = $grid->sortfild;
        }
        if($grid && $grid->sortorder)
        {
            $this->sortorder = $grid->sortorder;
        }
        $result["sortfild"] = $this->sortfild;
        $result["sortorder"] = $this->sortorder;
        if ($this->filterData instanceof JsExpression) {
            $result["filterData"] = $this->filterData;
        } else {
            $result["filterData"] = new JsExpression('function(data){return data.html}');
        }
        if($grid && $grid->colHide)
        {
             $result["colHide"] = $grid->colHide;
        }
        else
        {
             $result["colHide"] = $this->colHide;
        }
        if($this->params)
        {
            $result["params"] = $this->params;
        }
        $request = \RC::app()->request;
        $result["params"][$request->csrfParam] = $request->getCsrfToken();
        $result["grouping"] = $this->grouping;
        $result["opengroup"] = $this->opengroup;
        $result["textpreloader"] = $this->textpreloader;
        $result["labelmenu"] = $this->labelmenu;
        // имя группы
        if (!isset($this->groupingView['groupName']) || !($this->groupingView['groupName'] instanceof JsExpression)) {
            $this->groupingView["groupName"] = new JsExpression("function(arr, fild, titleFild){var title = 'Группировка ';  "
                    . "$.each(titleFild, function(i, val){ title += ' '+val+' - '+arr[i] }); return title}");
        }
        if($grid && $grid->groupField)
        {
            $this->groupingView['groupField'] = $grid->groupField;
        }
        if($grid && $grid->rowNum){
           $this->rowNum = $grid->rowNum; 
        }
        $result["groupingView"] = $this->groupingView;
        $result["sortCol"] = $grid && $grid->sortCol ? $grid->sortCol :$this->sortCol;
        $result["rowNum"] = $this->rowNum;
        $result["rowList"] = $this->rowList;
        $result["minWidth"] = $this->minWidth;
        $result["height"] = $this->height;
        $result["overlay"] = $this->overlay;
        $result["labelinfo"] = $this->labelinfo;
        $result["labelrow"] = $this->labelrow;
        $result["labelpage"] = $this->labelpage;
        
        $result["rowNum"] = $this->rowNum ? $this->rowNum : GridFormat::getRowNum($this->rowname);

        $result["mode"] = $this->mode;
        foreach ($this->optionsplg as $name => $obj) {
            $result[$name] = $obj;
        }

        return $result;
    }

    public function run() {
        $teg = '<div id="' . $this->settings['id'] . '"></div>';
        $text = '$("#' . $this->settings['id'] . '").mazeGrid(' . Json::encode($this->getOptions(), JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES) . ')';

        $text .= '.bind("hideFild.mazegrid showFild.mazegrid", function(e, data){ '
                . 'var param = {"gridParams":{}}; param.gridParams[e.type] = $(data).attr("data-grid-fild"); $.get("' . $this->url . '", param)'
                . '})';

        $text .= '.bind("afterSortFild.mazegrid", function(e, data){ '
                . 'var param = {"gridParams":{}}; param.gridParams[e.type] = data.fild; $.get("' . $this->url . '", param)'
                . '})';

        $text .= '.bind("groupFild.mazegrid", function(e, data){ '
                . 'var param = {"gridParams":{}}; param.gridParams[e.type] = data.field.length > 0 ? data.field : null; $.get("' . $this->url . '", param)'
                . '})';

        \Document::instance()->setTextScritp( $text,['wrap'=>\Document::DOCREADY]);
        return $teg;
    }
    
    public function registerClientScript() {
        AssetGrid::register();
    }
}
