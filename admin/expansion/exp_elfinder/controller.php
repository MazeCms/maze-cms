<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;
use maze\helpers\FileHelper;
use maze\base\JsExpression;
use maze\helpers\Json;
use exp\exp_elfinder\table\Profile;
use admin\expansion\exp_elfinder\classes\elFinderConnector;
use admin\expansion\exp_elfinder\classes\elFinder;

class Elfinder_Controller extends Controller {
    
    public function init(){
        if($this->_rout->run == 'files'){
            $this->enableCsrfValidation = false;
        }
       
    }

    public function accessFilter() {
        return [     
            'add edit sort publish unpublish display' => ["elfinder", "EDIT_PROFILE"], 
            'delete'=>['elfinder','DELETE_PROFILE']
        ];
    }
    
    public function actionDisplay() {

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $model = Profile::find()->joinWith(['roles'])->from(['ef' => Profile::tableName()]);

            return (new GridFormat([
                'id' => 'elfinder-grid',
                'model' => $model,
                'colonum' => 'ef.sort',
                'colonumData' => [
                    'id' => '$data->profile_id',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'title',
                    'enabled',
                    'roles' => function($data) {
                        if (!$data->roles) {
                            return '-';
                        }
                        return implode(', ', array_map(function($role) {
                            return $role->name;
                        }, $data->roles));
                    },
                    'profile_id'
                ]
                    ]))->renderJson();
        }
        return parent::display();
    }

    public function actionAdd() {

        $modelForm = $this->form('FormProfile');
        $model = $this->model('Profile');

        if ($this->request->isPost()) {

            $modelForm->load($this->request->post(null, 'none'));

            if ($this->request->isAjax() && $this->request->get('checkform') == 'elfinder-profile-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {

                if ($model->save($modelForm)) {
                    $this->setMessage(Text::_("EXP_ELFINDER_PROFILE_SAVEOK"), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect(['/admin/elfinder']);
                    }
                    return $this->setRedirect([['run' => 'edit', 'profile_id' => $modelForm->profile_id]]);
                } else {
                    $this->setMessage(Text::_('EXP_ELFINDER_PROFILE_SAVEERROR'), 'error');
                }
            }
        }

        return $this->renderPart("form", false, "form", [
                    'modelForm' => $modelForm,
                    'model' => $model
        ]);
    }

    public function actionEdit($profile_id) {

        $modelForm = $this->form('FormProfile');
        $model = $this->model('Profile');

        if (!($profile = Profile::findOne($profile_id))) {
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_ELFINDER_PROFILE_NOTID", ['id' => $profile_id]));
        }
        $modelForm->profile_id = $profile_id;
        if ($this->request->isPost()) {

            $modelForm->load($this->request->post(null, 'none'));

            if ($this->request->isAjax() && $this->request->get('checkform') == 'elfinder-profile-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {

                if ($model->save($modelForm)) {
                    $this->setMessage(Text::_("EXP_ELFINDER_PROFILE_SAVEOK"), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect(['/admin/elfinder']);
                    }
                    return $this->setRedirect([['run' => 'edit', 'profile_id' => $modelForm->profile_id]]);
                } else {
                    $this->setMessage(Text::_('EXP_ELFINDER_PROFILE_SAVEERROR'), 'error');
                }
            }
        } else {
            $modelForm->attributes = $profile->attributes;
            $modelForm->id_role = array_map(function($data) {
                return $data->id_role;
            }, $profile->role);
        }
        
       
        return $this->renderPart("form", false, "form", [
                'modelForm' => $modelForm,
                'model' => $model
        ]);
    }
    
    public function actionSort(array $sort){
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
             throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        
        foreach($sort as $s){
            if(isset($s['profile_id']) && isset($s['sort'])){
                $dir = Profile::findOne($s['profile_id']);
                $dir->sort = $s['sort'];
                $dir->save();
            }
           
        }
    }
    
    public function actionPublish(array $profile_id) {
        
        $this->model('Profile')->enable($profile_id, 1);
        if(!$this->request->isAjax()){
            $this->setMessage("EXP_ELFINDER_PROFILE_PUBLISH", 'success');
            $this->setRedirect(['/admin/elfinder']);
        }
            
    }

    public function actionUnpublish(array $profile_id) {
        
        $this->model('Profile')->enable($profile_id, 0);
        if(!$this->request->isAjax()){
            $this->setMessage("EXP_ELFINDER_PROFILE_UNPUBLISH", 'success');
            $this->setRedirect(['/admin/elfinder']);
        }
   
    }
    
    public function actionDelete(array $profile_id){
        
        $this->model('Profile')->delete($profile_id);
        $this->setMessage(Text::_("EXP_ELFINDER_PROFILE_DELETEOK"), 'success');
        return $this->setRedirect([['profile_id'=>$profile_id]]);
    }

    public function actionLoadDialog($clear) {

        if ($clear !== 'iframe') {
             throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }

        $lang = $this->_lang->reduce ? $this->_lang->reduce : "en";

        $this->_doc->addScript(RC::app()->getExpUrl("js/jquery.1.7.2.min.js"), ["sort" => 100]);
        $this->_doc->addScript(RC::app()->getExpUrl("js/jquery-ui.min.js"), ["sort" => 99]);

        $this->_doc->addStylesheet(RC::app()->getExpUrl("/css/theme.css"));
        $this->_doc->addStylesheet(RC::app()->getExpUrl("css/elfinder.min.css"));
        $this->_doc->addStylesheet(RC::app()->getExpUrl("css/smoothness/jquery-ui-smoothness.css"));
        $this->_doc->addScript(RC::app()->getExpUrl("js/language/elfinder." . $lang . ".js"));
        $this->_doc->addScript(RC::app()->getExpUrl("js/elfinder.min.js"));

        if ($id_role = $this->_access->getIdRole()) {
            $profile = Profile::find()
                            ->from(['ef' => Profile::tableName()])
                            ->joinWith('role')
                            ->where(['er.id_role' => $id_role, 'ef.enabled' => 1])
                            ->orderBy('ef.sort')->one();

            if ($profile) {
                $toolbar = [];
                foreach($profile->toolbar as $tb){
                    $toolbar[]= Json::encode($tb);
                }
                $toolbar = '['.implode(',',$toolbar).']';
                $clientOptions = [
                    'url' => Route::_([['run' => 'files', 'clear' => 'ajax']]),
                    'lang' => $lang,
                    'requestType' => $profile->requestType,
                    'commands' => $profile->commands,
                    'cssClass' => $profile->cssClass,
                    'getFileCallback' => new JsExpression("function(files, fm) {if(typeof handler == 'function' ) handler(files, fm);}"),
                    'notifyDelay' => $profile->notifyDelay,
                    'uiOptions' => ['toolbar' =>new JsExpression($toolbar)],
                    'ui' => $profile->ui,
                    'rememberLastDir' => $profile->rememberLastDir,
                    'useBrowserHistory' => $profile->useBrowserHistory,
                    'contextmenu' => ['navbar' => $profile->navbar, 'cwd' => $profile->cwd, 'files' => $profile->files],
                    'resizable' => $profile->resizable,
                    'loadTmbs' => $profile->loadTmbs,
                    'showFiles' => $profile->showFiles,
                    'allowShortcuts' => true,
                    'height' => 400,
                    'validName' => new JsExpression('/^' . $profile->validName . '$/'),
                    'commandsOptions' => [
                        'getfile' => ['folders' => false, 'onlyURL' => new JsExpression('onurl'), 'multiple' => new JsExpression('multi')]
                    ]
                ];
            } else {
                $clientOptions = [];
            }
        }
        
     
        $this->_doc->setTextScritp("function dialogFile(handler, multi, onurl){"
                . "var elf = $('#elfinder-elem').elfinder(" . Json::encode($clientOptions) . ").elfinder('instance'); return elf;}");

        return '<div id="elfinder-elem"></div>';
    }

    public function actionFiles() {

        if ($this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }

        

        if ($id_role = $this->_access->getIdRole()) {
            $user = $this->_access->get();

            $profile = Profile::find()->joinWith(['role', 'dir'])
                            ->from(['ef' => Profile::tableName()])
                            ->where(['er.id_role' => $id_role, 'ef.enabled' => 1])
                            ->orderBy('ef.sort')->one();

            $tmb = RC::getAlias('@root/images/.tmb/');
            if ($profile->dir) {
                $opts['roots'] = [];

                foreach ($profile->dir as $dir) {
                    $path = RC::getAlias($dir->path);
                    $path = str_replace('{{user}}', $user["name"], $path);
                    if(!is_dir($path)){
                        FileHelper::createDirectory($path);
                    }
                    $url = str_replace(RC::getAlias('@root'), '', $path);
                    $uploadAllow = array_map(function($data) {
                        return $data->mimetypes;
                    }, $dir->uploadallow);
                    $attr = [];
                    if($dir->attr){
                        foreach($dir->attr as $at){
                           $dattr = $at->attributes;
                           $dattr['pattern'] = '/'.$dattr['pattern'].'/';
                           unset($dattr['id'], $dattr['path_id']);
                           $attr[] = $dattr; 
                        }
                    }
                   
                    $opts['roots'][] = [
                        'driver' => 'LocalFileSystem',
                        'path' => $path,
                        'tmbPath' => $tmb,
                        'tmbURL' => '/images/.tmb/',
                        'URL' => $url,
                        'uploadAllow' => $uploadAllow,
                        'uploadOrder' => ['allow'],
                        'uploadMaxSize' =>$dir->uploadMaxSize,
                        'alias' => $dir->alias,
                        'mimeDetect' => 'internal',
                        'imgLib' => 'gd',
                        'tmbCrop' => false,
                        'acceptedName' => '/^'.$dir->acceptedName.'$/u',
                        'attributes' => $attr
                    ];
                }
                $connector = new elFinderConnector(new elFinder($opts));
                $connector->run();
            }
        }
        
    }

}

?>