<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use maze\helpers\FileHelper;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;
use maze\table\InstallApp;
use ui\assets\AssetTheme;

class Templating_Controller_Template extends Controller {

    public function accessFilter() {
        return [
            'edit editfile tree rename create move' => ["templating", "EDIT_TMP"],
            'delete' => ["templating", "EDIT_TMP"]
        ];
    }
    
    public function actionDisplay() {
 
        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
            $model = InstallApp::find()->where(['type'=>'template']);
            return (new GridFormat([
                'id' => 'template-grid',
                'model' => $model,
                'colonum' => 'front_back',
                'colonumData' => [
                    'id' => '$data->id_app',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'id_app',
                    'front_back',
                    'name',
                    'title'=>function($data){
                        $conf = RC::getConf(array("name"=>$data->name, "type"=>"template", "front"=>$data->front_back));
                        $path = ($data->front_back == 1 ? '' : '/admin'). "/templates/" . $data->name . "/assets";
                        $asset = new AssetTheme(['basePath'=>'@root'.$path, 'baseUrl'=>$path]);
                        
                        return Html::tag('a', Html::imgThumb($asset->getAssetBasePath().'/preview.png', 120, 80), ['href'=>$asset->getAssetBaseUrl().'/preview.png', 'class'=>'preview-tmp']).'<br>'.$conf->get("name") . ' ('.$data->name.')';
                    },
                    'description'=>function($data){
                        $conf = RC::getConf(array("name"=>$data->name, "type"=>"template", "front"=>$data->front_back));
                        return $conf->get("description");
                    },
                    'front_name'=>function($data){
                        return $data->front_back ? Text::_("EXP_TEMPLATING_STYLE_TABLE_FRONT") : Text::_("EXP_TEMPLATING_STYLE_TABLE_ADMIN");
                    },
                    'version' => function($data) {
                        $conf = RC::getConf(array("name"=>$data->name, "type"=>"template", "front"=>$data->front_back));
                        return $conf->get("version");
                    },
                    'created' => function($data) {
                        $conf = RC::getConf(array("name"=>$data->name, "type"=>"template", "front"=>$data->front_back));
                        return $conf->get("created");
                    },
                    'author' => function($data) {
                        $conf = RC::getConf(array("name"=>$data->name, "type"=>"template", "front"=>$data->front_back));
                        return $conf->get("author");
                    },
                    'license' => function($data) {
                        $conf = RC::getConf(array("name"=>$data->name, "type"=>"template", "front"=>$data->front_back));
                        return $conf->get("license");
                    },
                    'email' => function($data) {
                        $conf = RC::getConf(array("name"=>$data->name, "type"=>"template", "front"=>$data->front_back));
                        return $conf->get("email");
                    },
                    'siteauthor' => function($data) {
                        $conf = RC::getConf(array("name"=>$data->name, "type"=>"template", "front"=>$data->front_back));
                        return $conf->get("siteauthor");
                    },
                    'copyright' => function($data) {
                        $conf = RC::getConf(array("name"=>$data->name, "type"=>"template", "front"=>$data->front_back));
                        return $conf->get("copyright");
                    }        
                ]
                    ]))->renderJson();
        }
        return parent::display();
    }

    public function actionEdit($id_app) {
        
        $model = $this->model('Templating')->getTmp($id_app);
        
        if(!$model) throw new maze\exception\NotFoundHttpException(Text::_("EXP_TEMPLATING_TMP_FORM_MESSAGE_NOTMP"));
        
        $url = new URI([($model->front_back  ? "/" : "/admin"), ['tmp_name'=>$model->name, 'wid_view'=>1]]);
        
        $conf = RC::getConf(array("name"=>$model->name, "type"=>"template", "front"=>$model->front_back));

       $src = $model->front_back ? '/' : '/admin/';
       $src .= 'templates/'.$model->name;
       
        return $this->renderPart("form", false, "form", [
            'id_app' =>$model->id_app,
            'name'=>$model->name,
            'url'=>$url,
            'src'=>$src,
            'title'=>$conf->get('name')            
        ]);
    }
    
    public function actionTree($id_app) {
        if (!$this->request->isAjax() || !$this->request->get('clear') == 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        $model = $this->model('Templating')->getDataTree($id_app);
        return json_encode(['html'=>$model], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Редактировать - Сохранить текст  файла
     * 
     * @param int $id_app - id шаблона
     * @param string $path - относитеьный путь к файлу (без учета к директории самого шаблон)
     * @return type
     */
    public function actionEditfile($id_app, $path) {
        if(!$this->request->isAjax()) throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        
        $root = $this->model('Templating')->getRootPath($id_app);
        $path = trim($path, '/\\');
        if($root && file_exists($root.DS.$path)){
            if ($this->request->isPost()) {
                $text = $this->request->post('text', 'none');
                file_put_contents($root.DS.$path, $text);
                $this->setMessage(Text::_("EXP_TEMPLATING_TMP_FORM_MESS_SUCCESS"), "success");
                return json_encode(['ok'=>true]);
            }
            $buffer = file_get_contents($root.DS.$path);
             
        }
        else{
            $this->setMessage(Text::_("EXP_TEMPLATING_TMP_FORM_MESS_NOFILE"), "error");
            return;
        }
       
        return $buffer;
    }
    
    /**
     * Переименовать файл
     * 
     * @param int $id_app - id шаблона
     * @param string $path - относитеьный путь к файлу (без учета к директории самого шаблон)
     * @param string $name - новое имя файла или директории 
     * @return type
     */
    public function actionRename($id_app, $path, $name){
        if(!$this->request->isAjax()) throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        
        $model = $this->model('Templating');
        $root = $model->getRootPath($id_app);
        $path = trim($path, '/\\');
        $path = $root.DS.$path;
       
        if($root && is_file($path)){
            $parentPath = dirname($path);
            $newPath = FileHelper::getName($parentPath.DS.$name, ['rename'=>true, 'space'=>true]);
            $name = pathinfo($newPath, PATHINFO_BASENAME);
            $type = $model->getTypeFile($name);
            rename($path, $newPath);
        }
        elseif($root && is_dir($path)){
            $type = 'folder';
            $parentPath = explode(DS, $path);
            array_pop($parentPath);
            $parentPath = implode(DS, $parentPath);
            $newPath = FileHelper::getName($parentPath.DS.$name, ['rename'=>true, 'space'=>true]);
            $name = explode(DS, $newPath);
            $name = array_pop($name);
            rename($path, $newPath);
        }
        else{
            $this->setMessage(Text::_("EXP_TEMPLATING_TMP_FORM_MESS_NOFILE"), "error");
            return;
        }
        
        return json_encode(['html'=>['name'=>$name, 'type'=>$type]]);
        
    }
    /**
     * Создать файл | папку
     * 
     * @param int $id_app - id шаблона
     * @param string $path - относитеьный путь к файлу (без учета к директории самого шаблон)
     * @param string $name - новое имя файла или директории 
     * @param string $type - тип объекта folder | file
     * @return type
     */
    public function actionCreate($id_app, $path, $name, $type){
        if(!$this->request->isAjax()) throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        
        $model = $this->model('Templating');
        $root = $model->getRootPath($id_app);
        $path = trim($path, '/\\');
        $path = $root.DS.$path;
        $newPath = FileHelper::getName($path.DS.$name, ['rename'=>true, 'space'=>true]);
        $name = explode(DS, $newPath);
        $name =  array_pop($name);
        
        if(is_dir($path)){
            if($type == 'folder'){
                if(!FileHelper::createDirectory($newPath)){
                    $this->setMessage(Text::_("EXP_TEMPLATING_TMP_FORM_MESS_NODIR"), "error");
                }
            }else{
                if(!touch($newPath)){
                    $this->setMessage(Text::_("EXP_TEMPLATING_TMP_FORM_MESS_NOFILE"), "error");                    
                }
                $type = $model->getTypeFile($name);
            }
            
            return json_encode(['html'=>['name'=>$name, 'type'=>$type]]);
        }
        else{
             $this->setMessage(Text::_("EXP_TEMPLATING_TMP_FORM_MESS_NODIR"), "error");
        }
    }
  
    public function actionMove($id_app, $path, $target) {        

        if(!$this->request->isAjax()) throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        
        $model = $this->model('Templating');
        $root = $model->getRootPath($id_app);
        $path = trim($path, '/\\');
        $path = $root.DS.$path;
        $target = $root.DS.$target;
        $newName = null;
        $type = 'folder';
        if(is_dir($path)){
            $pathDir = explode(DS, $path);
            $targetDir = array_pop($pathDir);
            $newName = $target = FileHelper::getName($target.DS.$targetDir, ['rename'=>true]);
        }
        else
        {
            $newName = FileHelper::getName($target.DS.pathinfo($path, PATHINFO_BASENAME), ['rename'=>true]);
            $type = $model->getTypeFile($newName);
        }
        if($this->request->get('copy')){
            FileHelper::copy($path, $target, ['rename'=>true]);
        }
        else{
           FileHelper::move($path, $target, ['rename'=>true]); 
        }
        
        if($newName){
            $newName= explode(DS, $newName);
            $newName = array_pop($newName);
        }
        
        
        echo json_encode(array("html" =>['name'=>$newName, 'type'=>$type]));
    }
    
    

   public function actionDelete($id_app, $path) {
        if(!$this->request->isAjax()) throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));

        $model = $this->model('Templating');
        $root = $model->getRootPath($id_app);
        $path = trim($path, '/\\');
        $path = $root.DS.$path;
        FileHelper::remove($path);
        echo json_encode(array("flag" => true));
    }
 

    public function actionClose() {
        $this->setMessage(Text::_("EXP_TEMPLATING_STYLE_FORM_CLOSE_MESS_YES"), 'info');
        $this->setRedirect('/admin/templating/template');
    }
 

}

?>