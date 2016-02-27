<?php
namespace maze\expansion;
defined('_CHECK_') or die("Access denied");

use maze\base\Object;
use RC;
/**
 * БАЗОВЫЙ АБСТРАКТНЫЙ КЛАСС КОНТРОЛЛЕР
 */
abstract class InstallController extends Object {

    
    public $enableCsrfValidation = true;

    public $request;

    protected $_layout;
    
    protected $_vars;
    
    private $_rout;

    public function init() {
      $this->_rout = RC::app()->router;
      $this->request = RC::app()->request;
    }
    
    /**
     *  ЗАГРУЗКА ВИДА И ШАБЛОНА
     * @$view string  - название шаблона  Вида  и вызываемого класса
     * @$layout string  - шаблона вида
     * @$class_view string  - файл класса вида $view (Компонент_View_Название шаблона  Вида)
     * имя класса НазваниеРасширения_View_НазваниеВида
     * По умолчанию НазваниеВида =  НазваниеРасширения
     * или  НазваниеВида = НазваниеКонтроллера
     * @return View  - экземпляр класса View
     */
    final public function loadView($layout = '', $view = '') {

        // Подклбчаем необлодимый класс Вида		
        if (!empty($view)) {
            $view = $view;
        } else {
            $view = $this->_rout->view;
        }
        // назначаем класс Вида по умочанию
        if (empty($view)) {
            if ($this->_rout->controller == null || $this->_rout->controller === "controller") {
                $view = $this->_rout->component;
            } else {
                $view = $this->_rout->controller;
            }
        }

        $this->_rout->setView($view);

        // Подключаем необходимый шаблон Вида		
        if (!empty($layout)) {
            $layout = $layout;
        } else {
            $layout = $this->_rout->layout;
        }
        // назначаем шаблон по умочанию
        if (empty($layout)) {
            $layout = 'default';
        }
     
        $this->_rout->setLayout($layout);
        
        $this->_layout = $layout;

    }

    public function renderPart($layout = null, $view = null, $params = []) {
        if ($layout !== null) {
            $this->loadView($layout, $view);
        }
  
        return $this->display($params);
    }

    /**
     * ВЫВОД ВИДА
     */
    protected function display($params = []) {
        if ($this->_layout == null) {
            $this->loadView();
        }
        if(empty($this->_vars)){
            $this->_vars = [];
        }
        
       $this->_vars = array_merge($this->_vars, $params);

       return RC::app()->view->render('/'.$this->_layout, $this->_vars);  

    }

}

?>