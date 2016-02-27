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
use admin\expansion\exp_sitemap\table\SitemapRobots;

class Sitemap_Controller_Robots extends Controller {

    public function actionDisplay() {

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $model = SitemapRobots::find();

            return (new GridFormat([
                'id' => 'sitemap-robots-grid',
                'model' => $model,
                'colonum' => 'robots_id',
                'colonumData' => [
                    'id' => '$data->robots_id',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'images'=>function($data){
                        return $data->images ? Html::imgThumb('@root' . $data->images, 50, 50) : '';
                    },
                    'search',
                    'title',    
                    'robots_id'
                ]
            ]))->renderJson();
        }
        return parent::display();
    }

    public function actionAdd() {
  
        $model = new SitemapRobots;
        
        if ($this->request->isPost()) {
            $model->load($this->request->post(null, 'none'));
            if ($this->request->isAjax() && $this->request->get('checkform') == 'sitemap-robots-form') {
                 return json_encode(['errors' =>FormBuilder::validate($model)]);
            }
            if ($model->validate()) {
                if ($model->save()) {
                    $this->setMessage(Text::_("Робот карты сайта ({name}) успешно создана", ['name' => $model->title]), 'success');
                     RC::getLog()->add('exp',['component'=>'sitemap',
                        'category'=>__METHOD__,
                        'action'=>'add',
                        'status'=>'success',
                        'message'=>Text::_("Робот карты сайта {name} успешно создана", ['name' => $model->title])
                    ]);
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect(['/admin/sitemap/robots']);
                    }
                    return $this->setRedirect([['run' => 'edit', 'robots_id' =>$model->robots_id]]);
                } else {
                    $this->setMessage($model->getErrors(), "error");
                    return $this->setRedirect(['/admin/sitemap/robots']);
                }
            }
        }
        return $this->renderPart("form", false, "form", ['model' => $model]);
    }

    public function actionEdit($robots_id) {

        $model = SitemapRobots::findOne(['robots_id'=>$robots_id]);
        
        if (!$model) {
            throw new maze\exception\NotFoundHttpException(Text::_("По текущему ID({id}) робота карты сайта несуществует", ['id' => $robots_id]));
        }


        if ($this->request->isPost()) {
            $model->load($this->request->post(null, 'none'));
            if ($this->request->isAjax() && $this->request->get('checkform') == 'sitemap-robots-form') {
                 return json_encode(['errors' =>FormBuilder::validate($model)]);
            }
            if ($model->validate()) {
                if ($model->save()) {
                    $this->setMessage(Text::_("Робот карты сайта ({name}) успешно обновлен", ['name' => $model->title]), 'success');
                     RC::getLog()->add('exp',['component'=>'sitemap',
                        'category'=>__METHOD__,
                        'action'=>'add',
                        'status'=>'success',
                        'message'=>Text::_("Робот карты сайта ({name}) успешно обновлен", ['name' => $model->title])
                    ]);
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect(['/admin/sitemap/robots']);
                    }
                    return $this->setRedirect([['run' => 'edit', 'robots_id' =>$model->robots_id]]);
                } else {
                    $this->setMessage($model->getErrors(), "error");
                    return $this->setRedirect(['/admin/sitemap/robots']);
                }
            }
        }
        return $this->renderPart("form", false, "form", ['model' => $model]);
    }

    public function actionDelete(array $robots_id) {

        if (SitemapRobots::deleteAll(['robots_id'=>$robots_id])) {
            $this->setMessage(Text::_("Робот карты сайта успешно удален"), 'success');
            RC::getLog()->add('exp',['component'=>'sitemap',
                        'category'=>__METHOD__,
                        'action'=>'delete', 
                        'status'=>'success',
                        'message'=>Text::_("Робот карты сайта c id({id}) успешно удален", ['id' =>implode(',',$robots_id)])
                    ]);
        }else{
            $this->setMessage(Text::_("Ошибка удаления робот карты сайта"), "error");
        }
        $this->setRedirect(['/admin/sitemap/robots']);
    }
    

    public function actionClose() {
        $this->setMessage(Text::_("Предыдущая операция успешно отменена"), 'info');
        $this->setRedirect(['/admin/sitemap/robots']);
    }



}

?>