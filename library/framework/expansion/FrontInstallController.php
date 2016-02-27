<?php
namespace maze\expansion;

defined('_CHECK_') or die("Access denied");
use maze\exception\UserException;
use RC;
use ReflectionClass;
use ReflectionObject;

class FrontInstallController extends \maze\base\Object {

    
    private $_cotroller;
    
    private $_rout;


    public function init() {
      $this->_rout = RC::app()->router;
      
    }


    public function loadController($task = '') {

        $ControllerName = empty($task) ? $this->_rout->controller : $task;

        
        if ($ControllerName !== null && $ControllerName !== "controller") {
            $controller = '_' . ucfirst($ControllerName);

            $pach = $this->_rout->exp->getPath('controllers' . DS . $ControllerName . '.php');
          
            $this->_rout->setController($ControllerName);
            if (file_exists($pach)) {
                include_once ($pach);
            } else {
                throw new \maze\exception\NotFoundHttpException(RC::app()->getText("LIB_FRAMEWORK_FRONTCONTROLLER_NOT_CONTROLLER") . $pach);
            }
        } else {
            $controller = '';
            $path = $this->_rout->exp->getPath('controller.php');
           
            $this->_rout->setController('controller');
            if (file_exists($path)) {
                include_once ($path);
            } else {
                throw new \maze\exception\NotFoundHttpException(RC::app()->getText("LIB_FRAMEWORK_FRONTCONTROLLER_NOT_CONTROLLER") . $path);
            }
        }

        $classname = ucfirst($this->_rout->component) . "_Controller" . $controller;

        if (class_exists($classname)) {
            $refClass = new ReflectionClass($classname);

            if ($refClass->isSubclassOf('\maze\expansion\InstallController')) {
                $this->_cotroller = $refClass->newInstance();

                return $this->_cotroller;
            } else {
                throw new Exception(RC::app()->getText("LIB_FRAMEWORK_FRONTCONTROLLER_NOT_PARENT_CLASS") . $classname);
            }
        } else {
            throw new Exception(RC::app()->getText("LIB_FRAMEWORK_FRONTCONTROLLER_NOT_CLASS") . $classname);
        }
    }
   
    public function run($task = '') {
        if ($this->_cotroller == null) {
            $this->loadController();
        }
         
        $controller = $this->_cotroller;
       
         
        $refObject = new ReflectionObject($controller);

        $task = empty($task) ? $this->_rout->run : $task;
       
        if($task == null ){
            $task = "display"; 
        }
      
        $action = 'action'.ucfirst($task);

        if ($refObject->hasMethod($action) && $refObject->getMethod($action)->isPublic()) {
            $method = $refObject->getMethod($action);
            $params = [];
            if($method->getNumberOfParameters() > 0)
            {
                $refParams = $method->getParameters();
               
               
                
                foreach($refParams as $refp)
                {
                    $varp = RC::app()->request->get($refp->getName(), ['rawdecod', 'decode']);
                     
                    if($varp !== false)
                    {
                        if(is_array($varp) && !$refp->isArray())
                        {                            
                           throw new \maze\exception\NotFoundHttpException("Аргумент (".$refp->getName().")  метода (".$action.") является массивом");
                           break; 
                        }
                        elseif($refp->isArray() && !is_array($varp))
                        {
                            throw new \maze\exception\NotFoundHttpException("Аргумент (".$refp->getName().")  метода (".$action.") не является массивом");
                            break; 
                        }
                       
                        $params[$refp->getName()]  = $varp;
                        
                    }else
                    {
                        if(!$refp->isOptional())
                        {
                            throw new \maze\exception\NotFoundHttpException("Отсутсвует обязательный аргумент (".$refp->getName().") у метода (".$action.") ");
                            break;
                        }
                    }
                }
                
            }
             
            if($controller->enableCsrfValidation && RC::app()->request->enableCsrfValidation){
                if(!RC::app()->request->validateCsrfToken()){
                    throw new \maze\exception\NotFoundHttpException("Ошибка токена CSRF (".$refObject->getName().") у метода (".$action.")");
                }
            }
            
            $this->_rout->setRun($task);
            
            ob_start();
            if(!empty($params))
            {
               $out = $method->invokeArgs($controller, $params); 
            }
            else
            {
                $out = $method->invoke($controller);
            }
            
            if(!is_string($out)) $out = '';
             
            return  ob_get_clean(). $out;
       
        } else {
            throw new \maze\exception\NotFoundHttpException(RC::app()->getText("LIB_FRAMEWORK_FRONTCONTROLLER_NOT_TASK") . $refObject->getName() . "->" . $task);
        }
    }

}

?>