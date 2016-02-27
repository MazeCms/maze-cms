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
use admin\expansion\exp_sitemap\table\SitemapVisits;
use maze\base\DynamicModel;

class Sitemap_Controller_Visits extends Controller {

    public function actionDisplay() {        
        return parent::display(['listMap'=>Sitemap::getList()]);
    }
    /**
     * Колличество посещений карты сайта 
     * 
     * @throws maze\exception\NotAcceptableHttpException
     */
    public function actionRobots($type) {
        
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        
        $visits =  SitemapVisits::find()->joinWith(['robot', 'map'])
                ->from(['sv'=>SitemapVisits::tableName()])
                ->where(['sv.type'=>$type])->orderBy('sv.date_visits')->all();
        $data = [];
       
        foreach($visits as $vis){
            if(!isset($data[$vis->map->sitemap_id])){
                $data[$vis->map->sitemap_id] = [
                    'name'=>$vis->map->title,
                    'data'=>[]
                ];
            }
            $keyDate = DataTime::format($vis->date_visits, 'Y-m-d');
            
            if(isset($data[$vis->map->sitemap_id]['data'][$keyDate])){
                $data[$vis->map->sitemap_id]['data'][$keyDate]['y'] +=1;
            }else{
                $data[$vis->map->sitemap_id]['data'][$keyDate] = ['x'=>DataTime::format($keyDate, 'c'), 'y'=>1];
            } 
        }
        
        $resData = [];
        foreach($data as $d){
            $result = [];
            foreach($d['data'] as $v){
                $result[] = $v;
            }
            $d['data']=$result;
            $resData[] =  $d; 
        }
        
        return json_encode(['html'=>$resData]);
        
    }
    
    public function actionMapRobots($type) {
        
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        
        $model = DynamicModel::validateData([
            'in_date_visits'=>$this->request->get('in_date_visits'),
            'out_date_visits'=>$this->request->get('out_date_visits'),
            'sitemap_id'=>$this->request->get('sitemap_id')
        ],[
            [['in_date_visits', 'out_date_visits'], 'date', 'format' => 'Y-m-d'],
            ['sitemap_id', 'number']
        ]);
       
        $visits =  SitemapVisits::find()->joinWith(['robot', 'map'])
                ->from(['sv'=>SitemapVisits::tableName()])
                ->where(['sv.type'=>$type])
                ->andFilterWhere(['>=', 'sv.date_visits',$model->in_date_visits ? $model->in_date_visits : null ])
                ->andFilterWhere(['<=', 'sv.date_visits',$model->out_date_visits ? $model->out_date_visits : null])
                ->andFilterWhere(['sv.sitemap_id'=>$model->sitemap_id ? $model->sitemap_id : null])
                ->orderBy('sv.date_visits')
                ->all();
        $data = [];
        
        foreach($visits as $vis){
            if(!$vis->robot) continue;
            
            if(!isset($data[$vis->robot->robots_id])){
                $data[$vis->robot->robots_id] = [
                    'name'=>$vis->robot->title,
                    'y'=>1
                ];
            }else{
                $data[$vis->robot->robots_id]['y'] +=1; 
            }
           
        }
        
        return json_encode(['html'=>array_values($data)]);
    }
    
    public function actionDelete($sitemap_id, $type) {

        if (SitemapVisits::deleteAll(['sitemap_id'=>$sitemap_id, 'type'=>$type])) {
            $modelMap = Sitemap::findOne(['sitemap_id'=>$sitemap_id]);
            $this->setMessage(Text::_("Список посещений карты сайта ({name}) успешно удален", ['name'=>$modelMap->title]), 'success');
            RC::getLog()->add('exp',['component'=>'sitemap',
                        'category'=>__METHOD__,
                        'action'=>'delete', 
                        'status'=>'success',
                        'message'=>Text::_("Список посещений карты сайта ({name}) успешно удален", ['name'=>$modelMap->title])
                    ]);
        }else{
            $this->setMessage(Text::_("Ошибка удаления списка посещения карты сайта"), "error");
        }
        $this->setRedirect(['/admin/sitemap/visits']);
    }

}

?>