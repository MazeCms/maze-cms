<?php

defined('_CHECK_') or die("Access denied");
use maze\exception\UserException;

class FrontController extends Expansion {

    private static $_instance;
    
    private $_cotroller;

    public static function instance() {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
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
                throw new maze\exception\NotFoundHttpException(Text::_("LIB_FRAMEWORK_FRONTCONTROLLER_NOT_CONTROLLER") . $pach);
            }
        } else {
            $controller = '';
            $path = $this->_rout->exp->getPath('controller.php');
           
            $this->_rout->setController('controller');
            if (file_exists($path)) {
                include_once ($path);
            } else {
                throw new maze\exception\NotFoundHttpException(Text::_("LIB_FRAMEWORK_FRONTCONTROLLER_NOT_CONTROLLER") . $path);
            }
        }

        $classname = ucfirst($this->_rout->component) . "_Controller" . $controller;

        if (class_exists($classname)) {
            $refClass = new ReflectionClass($classname);

            if ($refClass->isSubclassOf('Controller')) {
                $this->_cotroller = $refClass->newInstance();

                return $this->_cotroller;
            } else {
                throw new Exception(Text::_("LIB_FRAMEWORK_FRONTCONTROLLER_NOT_PARENT_CLASS") . $classname);
            }
        } else {
            throw new Exception(Text::_("LIB_FRAMEWORK_FRONTCONTROLLER_NOT_CLASS") . $classname);
        }
    }
    protected function getAccess()
    {
       $access = $this->_cotroller->accessFilter();
      
       if(empty($access) || !is_array($access)) return false;
       $task = [];
       foreach($access as $key=>$val)
       {
           if(is_array($val) && count($val) == 2)
           {
              $val = $this->_access->roles($val[0], $val[1]);
           }
           elseif(is_callable($val))
           {
              $val = call_user_func($val, $this->_cotroller);
           }
           if(empty($key)) continue;
            
           $keys = preg_split("/[\s]+/s", $key);
            
           if(!is_array($keys))
           {
               $keys = [$keys];
           }
           foreach($keys as $k)
           {
               if(empty($k)) continue;
               $task[trim($k)] = $val;
           }
       }
       
       return $task;
    }
    public function run($task = '') {
        if ($this->_cotroller == null) {
            $this->loadController();
        }

        $controller = $this->_cotroller;
        
        $access = $this->getAccess();
       
        $refObject = new ReflectionObject($controller);

        $task = empty($task) ? $this->_rout->run : $task;
       
        if($task == null )
        {
            $task = "display"; 
        }
        if(!empty($access) && isset($access[$task]) && !$access[$task])
        {
            throw new maze\exception\UnauthorizedHttpException("Досутп запрещен");
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
                    $varp = $this->request->get($refp->getName(), ['rawdecod', 'decode']);
                     
                    if($varp !== false)
                    {
                      
                        if(is_array($varp) && !$refp->isArray()){                            
                           throw new maze\exception\NotFoundHttpException("Аргумент (".$refp->getName().")  метода (".$action.") является массивом");
                           break; 
                        }
                        elseif($refp->isArray() && !is_array($varp)){
                            throw new maze\exception\NotFoundHttpException("Аргумент (".$refp->getName().")  метода (".$action.") не является массивом");
                            break; 
                        }
                        
                        $params[$refp->getName()]  = $varp;
                        
                    }else
                    {
                        if(!$refp->isOptional())
                        {
                            throw new maze\exception\NotFoundHttpException("Отсутсвует обязательный аргумент (".$refp->getName().") у метода (".$action.") ");
                            break;
                        }else{
                            $params[$refp->getName()]  =  $refp->getDefaultValue();
                        }
                    }
                }
                
            }
            
            if($controller->enableCsrfValidation && $this->request->enableCsrfValidation){
                if(!$this->request->validateCsrfToken()){
                    throw new maze\exception\NotFoundHttpException("Ошибка токена CSRF (".$refObject->getName().") у метода (".$action.")");
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
            throw new maze\exception\NotFoundHttpException(Text::_("LIB_FRAMEWORK_FRONTCONTROLLER_NOT_TASK") . $refObject->getName() . "->" . $task);
        }
    }

}

?>