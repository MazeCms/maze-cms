<?php defined('_CHECK_') or die("Access denied");

use maze\helpers\ArrayHelper;
use maze\table\ContentType;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use root\expansion\exp_contents\model\ModelContent;
use exp\exp_contents\helpers\ContentsHelper;
use ui\grid\PaginationFormat;

class Contents_Controller_Home extends Controller {

    public function actionDisplay() {
   
        $paginationModel = RC::getDb()->cache(function($db){ 
            return new PaginationFormat(['model'=>ModelContent::findHome()]);
        }, null, 'exp_contents');
        
        $models = ModelContent::createModel($paginationModel->data);
       
        return $this->renderPart('default', null, null, ['models'=>$models, 'paginationModel'=>$paginationModel]);
    }
}

?>