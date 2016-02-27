<?php

namespace maze\base;

use RC;
use maze\helpers\FileHelper;

/**
 * View представляет собой объект вида в структуре MVC.
 *
 * @author Nikolay Bugaev <info.maze-studio.ru>
 * @since 2.0
 */
class View extends Object
{

    /**
     * @var ViewContextInterface the context under which the [[renderFile()]] method is being invoked.
     */
    public $context;
    /**
     * @var mixed custom parameters that are shared among view templates.
     */
    public $params = [];
    /**
     * @var array a list of available renderers indexed by their corresponding supported file extensions.
     * Each renderer may be a view renderer object or the configuration for creating the renderer object.
     * For example, the following configuration enables both Smarty and Twig view renderers:
     *
     * ~~~
     * [
     *     'tpl' => ['class' => 'maze\smarty\ViewRenderer'],
     *     'twig' => ['class' => 'maze\twig\ViewRenderer'],
     * ]
     * ~~~
     *
     * If no renderer is available for the given view file, the view file will be treated as a normal PHP
     * and rendered via [[renderPhpFile()]].
     */
    public $renderers;
    /**
     * @var string the default view file extension. This will be appended to view file names if they don't have file extensions.
     */
    public $defaultExtension = 'php';
    
    /**
     * @var string - префикс файла шаблона
     */
    public $defaultPrefix = 'tmp';
    /**
     * @var string - имя темы
     */
    public $theme;
  
 
    /**
     * @var array a list of placeholders for embedding dynamic contents. This property
     * is used internally to implement the content caching feature. Do not modify it directly.
     * @internal
     */
    public $dynamicPlaceholders = [];

    /**
     * @var array the view files currently being rendered. There may be multiple view files being
     * rendered at a moment because one view may be rendered within another.
     */
    private $_viewFiles = [];


    /**
     * Initializes the view component.
     */
    public function init()
    {
        parent::init();
        $this->theme = RC::app()->getTheme();
        
    }

    /**
     * Renders a view.
     *
     * The view to be rendered can be specified in one of the following formats:
     *
     * - path alias (e.g. "@app/views/site/index");
     * - absolute path within application (e.g. "//site/index"): the view name starts with double slashes.
     *   The actual view file will be looked for under the [[Application::viewPath|view path]] of the application.
     * - absolute path within current module (e.g. "/site/index"): the view name starts with a single slash.
     *   The actual view file will be looked for under the [[Module::viewPath|view path]] of the [[Controller::module|current module]].
     * - relative view (e.g. "index"): the view name does not start with `@` or `/`. The corresponding view file will be
     *   looked for under the [[ViewContextInterface::getViewPath()|view path]] of the view `$context`.
     *   If `$context` is not given, it will be looked for under the directory containing the view currently
     *   being rendered (i.e., this happens when rendering a view within another view).
     *
     * @param string $view the view name.
     * @param array $params the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @param object $context the context to be assigned to the view and can later be accessed via [[context]]
     * in the view. If the context implements [[ViewContextInterface]], it may also be used to locate
     * the view file corresponding to a relative view name.
     * @return string the rendering result
     * @throws InvalidParamException if the view cannot be resolved or the view file does not exist.
     * @see renderFile()
     */
    public function render($view, $params = [], $context = null)
    {
        $viewFile = $this->findViewFile($view, $context);
        return $this->renderFile($viewFile, $params, $context);
    }

    /**
     * Finds the view file based on the given view name.
     * @param string $view the view name or the path alias of the view file. Please refer to [[render()]]
     * on how to specify this parameter.
     * @param object $context the context to be assigned to the view and can later be accessed via [[context]]
     * in the view. If the context implements [[ViewContextInterface]], it may also be used to locate
     * the view file corresponding to a relative view name.
     * @return string the view file path. Note that the file may not exist.
     * @throws InvalidCallException if a relative view name is given while there is no active context to
     * determine the corresponding view file.
     */
    protected function findViewFile($view, $context = null)
    {
        if (strncmp($view, '@', 1) === 0) {
            // e.g. "@app/views/main"
            $file = RC::getAlias($view);
        } elseif (strncmp($view, '//', 2) === 0) {
            // e.g. "//layouts - назначение шаблона темы"
            $file = $this->theme->getBasePath() . DS . ltrim($view, '/');
           
        } elseif (strncmp($view, '/', 1) === 0) {
            // e.g. "/site/index"
            if (RC::app()->router->component !== null) {
                $file = RC::getAlias('@exp/exp_' . RC::app()->router->component . '/views/' . RC::app()->router->view . '/tmp/'.$view);
            } else {
                throw new \Exception("Unable to locate view file for view '$view': no active controller.");
            }
        } elseif ($context instanceof ViewContextInterface) {
            
            $file = $context->getViewPath() . DS . $view;
            
        } elseif (($currentViewFile = $this->getViewFile()) !== false) {
            $file = dirname($currentViewFile) . DS . $view;
        } else {
            throw new \Exception("Unable to resolve view file for view '$view': no active view context.");
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }

        $prefix = $this->defaultPrefix ? $this->defaultPrefix.'.'  : '';
        
        
        $path = pathinfo($file, PATHINFO_DIRNAME) . DS .$prefix.pathinfo($file, PATHINFO_BASENAME) . '.' . $this->defaultExtension;
        
        if ($this->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }

       
        return $path;
    }
    
    public function hasView($view){
      $file =  $this->findViewFile($view);
      return file_exists($file);
    }

    /**
     * Renders a view file.
     *
     * If [[theme]] is enabled (not null), it will try to render the themed version of the view file as long
     * as it is available.
     *
     * The method will call [[FileHelper::localize()]] to localize the view file.
     *
     * If [[renderers|renderer]] is enabled (not null), the method will use it to render the view file.
     * Otherwise, it will simply include the view file as a normal PHP file, capture its output and
     * return it as a string.
     *
     * @param string $viewFile the view file. This can be either an absolute file path or an alias of it.
     * @param array $params the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @param object $context the context that the view should use for rendering the view. If null,
     * existing [[context]] will be used.
     * @return string the rendering result
     * @throws InvalidParamException if the view file does not exist
     */
    public function renderFile($viewFile, $params = [], $context = null)
    {
        $viewFile = RC::getAlias($viewFile);

       
        if ($this->theme !== null) {
         
            $themeViewFile = $this->theme->applyTo($viewFile);
           
            if($themeViewFile && is_file($themeViewFile)){
                $viewFile = $themeViewFile;
            }
        }
    
        if (!is_file($viewFile)) {
              throw new \Exception("The view file does not exist: $viewFile");
        } 
        $oldContext = $this->context;
        if ($context !== null) {
            $this->context = $context;
        }
        $output = '';
        $this->_viewFiles[] = $viewFile;

        if ($this->beforeRender($viewFile, $params)) {
          
            $ext = pathinfo($viewFile, PATHINFO_EXTENSION);
            if (isset($this->renderers[$ext])) {
                if (is_array($this->renderers[$ext]) || is_string($this->renderers[$ext])) {
                    $this->renderers[$ext] = RC::createObject($this->renderers[$ext]);
                }
                /* @var $renderer ViewRenderer */
                $renderer = $this->renderers[$ext];
                $output = $renderer->render($this, $viewFile, $params);
            } else {
                $output = $this->renderPhpFile($viewFile, $params);
            }
            $this->afterRender($viewFile, $params, $output);
        }

        array_pop($this->_viewFiles);
        $this->context = $oldContext;

        return $output;
    }

    /**
     * @return string|boolean the view file currently being rendered. False if no view file is being rendered.
     */
    public function getViewFile()
    {
        return end($this->_viewFiles);
    }

    /**
     * This method is invoked right before [[renderFile()]] renders a view file.
     * The default implementation will trigger the [[EVENT_BEFORE_RENDER]] event.
     * If you override this method, make sure you call the parent implementation first.
     * @param string $viewFile the view file to be rendered.
     * @param array $params the parameter array passed to the [[render()]] method.
     * @return boolean whether to continue rendering the view file.
     */
    public function beforeRender($viewFile, $params)
    {
        
        return true;
    }

    /**
     * This method is invoked right after [[renderFile()]] renders a view file.
     * The default implementation will trigger the [[EVENT_AFTER_RENDER]] event.
     * If you override this method, make sure you call the parent implementation first.
     * @param string $viewFile the view file being rendered.
     * @param array $params the parameter array passed to the [[render()]] method.
     * @param string $output the rendering result of the view file. Updates to this parameter
     * will be passed back and returned by [[renderFile()]].
     */
    public function afterRender($viewFile, $params, &$output)
    {
      
    }

    /**
     * Renders a view file as a PHP script.
     *
     * This method treats the view file as a PHP script and includes the file.
     * It extracts the given parameters and makes them available in the view file.
     * The method captures the output of the included view file and returns it as a string.
     *
     * This method should mainly be called by view renderer or [[renderFile()]].
     *
     * @param string $_file_ the view file.
     * @param array $_params_ the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @return string the rendering result
     */
    public function renderPhpFile($_file_, $_params_ = [])
    {
        ob_start();
        ob_implicit_flush(false);
        if (is_object($_params_)) {
            $var = get_object_vars($_params_);
            if (!empty($var)){                
                $_params_ = array_combine(array_keys($var), array_values($var));
            }          
        }
       
        if(is_array($_params_)){
            extract($_params_, EXTR_OVERWRITE);
        }
        
      
        require($_file_);

        return ob_get_clean();
    }

   


    /**
     * Evaluates the given PHP statements.
     * This method is mainly used internally to implement dynamic content feature.
     * @param string $statements the PHP statements to be evaluated.
     * @return mixed the return value of the PHP statements.
     */
    public function evaluateDynamicContent($statements)
    {
        return eval($statements);
    }

   

}
