<?php

defined('_CHECK_') or die("Access denied");

/**
 * БАЗОВЫЙ АБСТРАКТНЫЙ КЛАСС КОНТРОЛЛЕР
 */
abstract class Controller extends Expansion {

    private $_view;
    
    public $enableCsrfValidation = true;

    public function accessFilter() {
        
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
    final public function loadView($layout = '', $view = '', $class_view = '') {

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
        if (empty($class_view)) {
            $class_view = 'default';
        }

        $this->_rout->setLayout($layout);
        $this->_rout->setClassView($class_view);

        $pach = $this->_rout->exp->getPath('views' . DS . $view . DS . 'view' . DS . 'view.' . $class_view . '.php');

        if (file_exists($pach)) {
            include_once $pach;

            $classname = ucfirst($this->_rout->component) . "_View_" . ucfirst($view);
            if (class_exists($classname)) {
                $refClass = new ReflectionClass($classname);

                if ($refClass->isSubclassOf('View')) {
                    return $this->_view = $refClass->newInstance(['layout' => $layout]);
                } else {
                    throw new Exception(Text::_("LIB_FRAMEWORK_CONTROLLER_NOT_PARENT_CLASS") . $classname);
                }
            } else {
                throw new Exception(Text::_("LIB_FRAMEWORK_CONTROLLER_NOT_CLASS") . $classname);
            }
        } else {
            throw new Exception(Text::_("LIB_FRAMEWORK_CONTROLLER_NOT_FILE") . $pach);
        }
    }

    public function renderPart($layout = null, $view = null, $class_view = null, $params = []) {
        if ($layout !== null) {
            
            $objView = $this->loadView($layout, $view, $class_view);
            
            if (is_array($params) && !empty($params)) {
                $objView->set($params);
            }
        }
       

        return self::display();
    }

    /**
     * ВЫВОД ВИДА
     */
    protected function display($params = []) {
        if ($this->_view == null) {
            $this->loadView();
        }

        if (is_array($params) && !empty($params)) {
            $this->_view->set($params);
        }
        $refObject = new ReflectionObject($this->_view);

        if ($refObject->hasMethod("registry") && $refObject->getMethod("registry")->isPublic()) {
            $this->_view->registry();
        } else {
            throw new Exception(Text::_("LIB_FRAMEWORK_CONTROLLER_NOT_REGISTRY") . $refObject->getName());
        }
        
        return $this->_view->render();
    }

}

?>