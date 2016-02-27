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
use admin\expansion\exp_sitemap\table\Sitemap;
use admin\expansion\exp_sitemap\model\ModelMap;
use admin\expansion\exp_sitemap\model\ModelImport;

class Sitemap_Controller extends Controller {

    public function actionDisplay() {

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $model = Sitemap::find()->from(['m' => Sitemap::tableName()])->with(['route', 'link'])->groupBy('m.sitemap_id');


            return (new GridFormat([
                'id' => 'sitemap-grid',
                'model' => $model,
                'colonum' => 'm.sitemap_id',
                'colonumData' => [
                    'id' => '$data->sitemap_id',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'enable_xml',
                    'enable_html',
                    'title',                   
                    'countlink'=>function($data){
                        return count($data->link);
                    },
                    'visits_xml'=>function($data){
                        return $data->visitsXml ? $data->visitsXml->date_visits : '-';
                    },
                    'visits_html'=>function($data){
                        return $data->visitsHtml ? $data->visitsHtml->date_visits : '-';
                    },        
                    'sitemap_id'
                ]
            ]))->renderJson();
        }
        return parent::display();
    }

   
    public function actionImport() {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        $model = new ModelImport;
        $type = [];
        if($enable = $this->request->post('enable')){            
            foreach($enable as $name=>$val){
                if($val){
                    $type[] = $name;
                }
                
            }
        }
        $link = [];
       
        if(!empty($type)){
            $link = $model->getImport($type, $this->request->post());
        }
        return json_encode(['html' => $link]);
    }

    

    public function actionAdd() {
  
        $model = new ModelMap;
        $modelImport = new ModelImport;
        
        if ($this->request->isPost()) {
            $model->loadAll($this->request->post(null, 'none'));
            if ($this->request->isAjax() && $this->request->get('checkform') == 'sitemap-form') {
                 return json_encode(['errors' => call_user_func_array('ui\form\FormBuilder::validate', $model->getAllModel())]);
            }
            if (maze\base\Model::validateMultiple($model->getAllModel())) {
                if ($model->saveSite()) {
                    $this->setMessage(Text::_("Карта сайта {name} успешно создана", ['name' => $model->map->title]), 'success');
                     RC::getLog()->add('exp',['component'=>'sitemap',
                        'category'=>__METHOD__,
                        'action'=>'add',
                        'status'=>'success',
                        'message'=>Text::_("Карта сайта {name} успешно создана", ['name' => $model->map->title])
                    ]);
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect(['/admin/sitemap']);
                    }
                    return $this->setRedirect([['run' => 'edit', 'sitemap_id' => $model->map->sitemap_id]]);
                } else {
                    $this->setMessage($model->map->getErrors(), "error");
                    return $this->setRedirect(['/admin/sitemap']);
                }
            }
        }
        return $this->renderPart("form", false, "form", ['model' => $model, 'modelImport'=>$modelImport]);
    }

    public function actionEdit($sitemap_id) {

        $model = new ModelMap(['id'=>$sitemap_id]);
        $modelImport = new ModelImport;
        
        if (!$model->getMap()) {
            throw new maze\exception\NotFoundHttpException(Text::_("По текущему ID({id}) карты сайта несуществует", ['id' => $sitemap_id]));
        }
        
        $modelImport->params = $model->getMap()->params;

        if ($this->request->isPost()) {
            $model->loadAll($this->request->post(null, 'none'));
            if ($this->request->isAjax() && $this->request->get('checkform') == 'sitemap-form') {
                 return json_encode(['errors' => call_user_func_array('ui\form\FormBuilder::validate', $model->getAllModel())]);
            }
            if (maze\base\Model::validateMultiple($model->getAllModel())) {
                if ($model->saveSite()) {
                    $this->setMessage(Text::_("Карта сайта {name} успешно обновлена", ['name' => $model->map->title]), 'success');
                    
                    RC::getLog()->add('exp',['component'=>'sitemap',
                        'category'=>__METHOD__,
                        'action'=>'edit',
                        'status'=>'success',
                        'message'=>Text::_("Карта сайта {name} успешно обновлена", ['name' => $model->map->title])
                    ]);
                    
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect(['/admin/sitemap']);
                    }
                    return $this->setRedirect([['run' => 'edit', 'sitemap_id' => $model->map->sitemap_id]]);
                } else {
                    $this->setMessage($model->map->getErrors(), "error");
                    return $this->setRedirect(['/admin/sitemap']);
                }
            }
        }
        return $this->renderPart("form", false, "form", ['model' => $model, 'modelImport'=>$modelImport]);
    }

    public function actionDelete(array $sitemap_id) {
        $model = new ModelMap;
        if ($model->deleteMap($sitemap_id)) {
            $this->setMessage(Text::_("Карта сайтов успешно удалена"), 'success');
            RC::getLog()->add('exp',['component'=>'sitemap',
                        'category'=>__METHOD__,
                        'action'=>'delete', 
                        'status'=>'success',
                        'message'=>Text::_("Карта сайта c id({id}) успешно удалена", ['id' =>implode(',',$sitemap_id)])
                    ]);
        }else{
            $this->setMessage(Text::_("Ошибка удаления карты сайта"), "error");
        }
        $this->setRedirect(['/admin/sitemap']);
    }
    
    public function actionUnpublish(array $sitemap_id, $type) {
        $update[$type] = 0;        
        Sitemap::updateAll($update,['sitemap_id'=>$sitemap_id]);   
        RC::getCache("exp_sitemap")->clearTypeFull();
        $this->setMessage(Text::_("Карта сайтов успешно снята с публикации"), 'success');
        $this->setRedirect(['/admin/sitemap']);
    }
    
    public function actionPublish(array $sitemap_id, $type) {
        $update[$type] = 1;
        Sitemap::updateAll($update,['sitemap_id'=>$sitemap_id]);
        RC::getCache("exp_sitemap")->clearTypeFull();
        $this->setMessage(Text::_("Карта сайтов успешно опубликована"), 'success');
        $this->setRedirect(['/admin/sitemap']);
    }
    
    public function actionUpdate($sitemap_id) {
        
        $model = new ModelMap(['id'=>$sitemap_id]);
        $modelImport = new ModelImport;
        
        if (!$model->getMap()) {
            throw new maze\exception\NotFoundHttpException(Text::_("По текущему ID({id}) карты сайта несуществует", ['id' => $sitemap_id]));
        }
        
        $type = [];
        if(isset($model->getMap()->params['enable'])){ 
            $enable = $model->getMap()->params['enable'];
            foreach($enable as $name=>$val){
                if($val){
                    $type[] = $name;
                }
                
            }
        }
        $link = [];
       
        if(!empty($type)){
            $link = $modelImport->getImport($type, $model->getMap()->params);
        }
        if($model->map->link){
            $oldLink = [];
            foreach($model->map->link as $linkItems){
                $oldLink[$linkItems->loc] = $linkItems->attributes;
            }
        }
        
        $result = [];
        foreach($link as $k=>$l){
            if(isset($oldLink[$k])){               
                $result[] = $oldLink[$k];
            }else{
                $result[]=$l;
            }
        }
        
        $model->loadAll(['SitemapLink'=>$result]);
         if (maze\base\Model::validateMultiple($model->getAllModel())) {
                if ($model->saveSite()) {
                    $this->setMessage(Text::_("Карта сайтов ({name}) успешно переиндексирована", ['name'=>$model->getMap()->title]), 'success');
                }
         }else{
             $this->setMessage(Text::_("Ошибка индексации ({name}) карты сайта", ['name'=>$model->getMap()->title]), "error");
         }
         
            
        $this->setRedirect(['/admin/sitemap']);
    }

    public function actionClose() {
        $this->setMessage(Text::_("Предыдущая операция успешно отменена"), 'info');
        $this->setRedirect(['/admin/sitemap']);
    }



}

?>