<?php

namespace ui\grid;

use maze\base\Object;
use maze\table\Grids;
use maze\helpers\DataTime;

class GridFormatArray extends Object {

    /**
     * @var string индитификатор текущей таблицы
     */
    public $id;

    /**
     * @var string колонка по умолчанию по которой сортируют
     */
    public $colonum;

    /**
     * @var string порядок сортировки по умолчанию;
     */
    public $order = 'asc';

    /**
     * @var int - текушая страница по умолчанию
     */
    public $page = 1;

    /**
     * @var int - число записей на странице по умолчанию
     */
    public $rowNum = 10;

    /**
     * @var string - название переменной текущая страница
     */
    public $pagename = "number";

    /**
     * @var string - Метод полчения переменных
     */
    public $method = 'POST';

    /**
     * @var string - название переменной порядка сортировки
     */
    public $ordername = "order";

    /**
     * @var string - название переменной текущей колонки по которой сортируют
     */
    public $fildname = "colonum";

    /**
     * @var string - название переменной число записей на странице 
     */
    public $rowname = "pnumber";

    /**
     * @var \maze\db\ActiveRecord - Объект модели 
     */
    public $model;

    /**
     * @var array - Поля для вывода
     */
    public $colonumData;

    /**
     * @var array - результат
     */
    protected $data = [];

    /**
     * @var string - режим отоббражения таблицы
     */
    protected $mode = 'default';

    /**
     * @var string - имя поля для поиска родительской ссылки 
     */
    protected $link;

    /**
     * @var int - число записей на странице
     */
    protected $total_page;

    /**
     * @var int - счетчик для ID
     */
    public static $count = 0;

    public function init() {

        if (!is_array($this->model) || empty($this->model)) {
            return false;
        }

        if (empty($this->colonumData) || !is_array($this->colonumData)) {
            throw new \Exception("Неявляется колонкой ");
        }

        if (!$this->id) {
            $this->id = static::getID();
        }

        static::$count++;
        $model = $this->model;
        $object = $model;

        $colonum = \Request::getVar($this->fildname, $this->method);   // имя столбца, по которому сортируем
        $order = \Request::getVar($this->ordername, $this->method);     // порядок сортировки
        $number = \Request::getVar($this->pagename, $this->method);    // текущая страница
        $pnumber = \Request::getVar($this->rowname, $this->method);    // число записей на странице

        if (!$colonum) {
            $colonum = $this->colonum;
        }
        if (!$order) {
            $order = $this->order;
        }
        if (!$number) {
            $number = $this->page;
        }
        if (!$pnumber) {
            $pnumber = $this->rowNum ? $this->rowNum : static::getRowNum();
        }


        $grid = Grids::findOne($this->id);

        if (!$grid) {
            $grid = new Grids();
            $grid->grid_id = $this->id;
        }

        $grid->page = $number;
        $grid->sortfild = $colonum;
        $grid->sortorder = $order;
        $grid->rowNum = $pnumber;


        if (($params = \Request::getVar('gridParams', 'GET'))) {
            if (isset($params['hideFild'])) {
                if (!is_array($grid->colHide))
                    $grid->colHide = [];
                if (!in_array($params['hideFild'], $grid->colHide)) {
                    $grid->colHide = array_merge($grid->colHide, [$params['hideFild']]);
                }
            } elseif (isset($params['showFild'])) {
                if (!empty($grid->colHide)) {
                    if (in_array($params['showFild'], $grid->colHide)) {
                        $key = array_search($params['showFild'], $grid->colHide);
                        $field = $grid->colHide;
                        unset($field[$key]);
                        $grid->colHide = $field;
                    }
                }
            }

            if (isset($params['afterSortFild'])) {
                $grid->sortCol = $params['afterSortFild'];
            }

            if (isset($params['groupFild'])) {
                $grid->groupField = empty($params['groupFild']) ? null : $params['groupFild'];
            }
        }

        $grid->save();

        $total_page = count($object);


        // если текущая запрашиваема странца больше доступных, то сбрасываем на "1"
        if ($pnumber > 0 && $number > ceil($total_page / $pnumber)) {
            $number = 1;
        }
        $data = $object;
        $this->page = $number;
        $this->total_page = $total_page;
        $first = ($number - 1) * $pnumber;

        if (is_array($data) && !empty($data) && $colonum) {

            $colonum = explode(',', $colonum);
            foreach ($colonum as $col) {
                $col = trim($col);
                $end = end($data);
                if (isset($end[$col])) {
                    try {
                        usort($data, function($a, $b) use($col, $order) {

                            if (DataTime::dataCreate($a[$col])) {
                                $start = DataTime::dataCreate($a[$col])->getTimestamp();
                                $end = DataTime::dataCreate($b[$col])->getTimestamp();
                            } else {
                                $start = $a[$col];
                                $end = $b[$col];
                            }
                         
                            if (!is_numeric($start)) {
                                $res = strcmp($start, $end);
                                if ($res == 0)
                                    return 0;
                                return $order == 'asc' ? $res : $res * -1;
                            }

                            if ($start == $end) {
                                return 0;
                            }

                            if ($order == 'asc') {
                                return $start < $end ? -1 : 1;
                            } else {
                                return $start < $end ? 1 : -1;
                            }
                        });
                    } catch (\Exception $ex) {
                        
                    }
                }
            }
        }
        $pnumber = $total_page < $pnumber ? $total_page : $pnumber;

        $data = array_slice($data, $first, $pnumber);


        $custom = $this->getCustomColonum($data);

        foreach ($data as $i => $cl) {
            foreach ($cl as $field => $val) {
                $res = $this->getColonumData($field, $cl);
                if (!is_null($res)) {
                    $this->data[$i][$field] = $res;
                    if (!empty($custom)) {
                        foreach ($custom as $attr) {
                            $this->data[$i][$attr] = $this->getColonumData($attr, $cl);
                        }
                    }
                }
            }
        }
    }

    protected function getCustomColonum($data) {
        $result = [];
        $sl = array_slice($data, 0, 1);
        $oattr = [];
        if (isset($sl[0])) {
            foreach ($sl[0] as $field => $val) {
                $oattr[] = $field;
            }
        }

        foreach ($this->colonumData as $key => $valu) {
            if (is_string($key) && !in_array($key, $oattr)) {
                $result[] = $key;
            }
        }

        return $result;
    }

    protected function getColonumData($name, $data) {

        $result = null;

        foreach ($this->colonumData as $key => $val) {
            if (is_string($key) && $key == $name) {
                if (is_callable($val)) {
                    $result = call_user_func($val, $data);
                } elseif (is_string($val)) {

                    $func = create_function('$data', 'return ' . $val . ';');
                    $result = $func($data);
                }
                break;
            } elseif (is_string($val) && $val == $name && isset($data->$name)) {
                $result = $data->$name;
                break;
            }
        }

        return $result;
    }

    public function getData() {
        return ["page" => $this->page, "total" => $this->total_page, "data" => $this->data];
    }

    public static function getRowNum() {
        $conf = \RC::getConfig();

        return $conf->page_number;
    }

    public function renderJson() {
        return json_encode(['html' => $this->getData()]);
    }

    public static function getID() {
        return 'ui-grid-' . static::$count;
    }

}
