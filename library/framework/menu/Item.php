<?php

/**
 * Description of MenuView
 *
 * @author Николай Константинович Бугаёв http://maze-studio.ru
 */

namespace maze\menu;

use Text;
use RC;
use maze\base\Object;

class Item extends Object {

    public $id_menu;
    public $typeLink;
    public $name;
    public $alias;
    public $meta_title;
    public $meta_robots;
    public $meta_key;
    public $meta_des;
    public $image;
    public $paramLink;
    public $id_tmp;
    public $id_exp;
    public $parent;
    public $param;
    public $home;

    /**
     * @var string - отнотилеьный путь
     */
    protected $_path;

    /**
     * @var array - параметры url 
     */
    protected $_params;

    /**
     * @var array - массив навигационной цепочки текущего пунтка пеню 
     */
    protected $_breadcrumbs;

    /**
     * @var Menu - экземпляр класса 
     */
    public $_menu;

    public function setMenu($menu) {
        return $this->_menu = $menu;
    }

    public function getPath() {

        if ($this->_path === null) {
            if ($this->home) {
                $this->_path = '/';
            } else {
                if ($this->getIsExp()) {
                    $path = [$this->alias];
                    $parent = $this->parent;
                    if($item = $this->_menu->getItemByID($parent)){
                        $path[] = $item->getPath();
                    }
                    
//                    while ($item = $this->_menu->getItemByID($parent)) {
//                        
//                        $path[] = $item->alias;
//                        $parent = $item->parent;
//                    }
                    $this->_path = implode('/', array_reverse($path));
                } elseif ($this->getIsAlias() || $this->getIsUrl()) {
                    if ($this->getIsAlias()) {
                        $this->_path = RC::getRouter(RC::ROUTERSITE)->createRoute($this->paramLink);
                    } else {
                        $this->_path = $this->paramLink;
                    }
                } else {
                    $this->_path = "#";
                }
            }
        }

        return $this->_path;
    }

    public function getBreadcrumbs() {
        if ($this->_breadcrumbs === null) {
            $this->_breadcrumbs[] = ['label' => $this->name, 'url' => [$this->getPath()]];
            $parent = $this->parent;

            while ($item = $this->_menu->getItemByID($parent)) {
                $this->_breadcrumbs[] = ['label' => $item->name, 'url' => [$item->getPath()]];
                $parent = $item->parent;
            }
            $this->_breadcrumbs = array_reverse($this->_breadcrumbs);
        }

        return $this->_breadcrumbs;
    }

    public function getIsExp() {
        return $this->typeLink == 'expansion';
    }

    public function getIsAlias() {
        return $this->typeLink == 'alias';
    }

    public function getIsUrl() {
        return $this->typeLink == 'url';
    }

    public function getComponent() {
        if ($this->getIsExp()) {
            return isset($this->paramLink['component']) ? $this->paramLink['component'] : null;
        }
        return false;
    }

    public function getController() {
        if ($this->getIsExp()) {
            return isset($this->paramLink['controller']) ? $this->paramLink['controller'] : null;
        }
        return false;
    }

    public function getView() {
        if ($this->getIsExp()) {
            return isset($this->paramLink['view']) ? $this->paramLink['view'] : null;
        }
        return false;
    }

    public function getLayout() {
        if ($this->getIsExp()) {
            return isset($this->paramLink['layout']) ? $this->paramLink['layout'] : null;
        }
        return false;
    }

    public function getParams() {
        if ($this->getIsExp()) {
            return isset($this->paramLink['url_param']) ? $this->paramLink['url_param'] : null;
        }
        return false;
    }

    public function get($name = null) {
        if ($name) {
            if (is_array($this->param)) {
                return isset($this->param[$name]) ? $this->param[$name] : null;
            }
        } else {
            return $this->param;
        }
        return null;
    }

}
