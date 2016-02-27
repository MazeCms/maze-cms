<?php defined('_CHECK_') or die("Access denied");

use maze\helpers\ArrayHelper;
use maze\table\ContentType;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use root\expansion\exp_contents\model\ModelContent;
use exp\exp_contents\helpers\ContentsHelper;
use ui\grid\PaginationFormat;

class Contents_Controller_Type extends Controller {

    public function actionDisplay($bundle) {

        $modelType = RC::getDb()->cache(function($db) use ($bundle){ 
            return ContentType::findOne(['expansion'=>'contents', 'bundle'=>$bundle]);
        }, null, 'exp_contents');  
        
        if(!$modelType){
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_CONTENTS_CONTENTS_NOT_ID", ['id'=>$bundle]));   
        }
        

        $paginationModel = RC::getDb()->cache(function($db) use ($bundle){ 
            return new PaginationFormat(['model'=>ModelContent::findAll(['c.bundle'=>$bundle])]);
        }, null, 'exp_contents');    

        $models = ModelContent::createModel($paginationModel->data);
     
        
        return $this->renderPart(ContentsHelper::getLayoutViewType($bundle, 'default'), null, null, [
            'models'=>$models, 
            'modelType'=>$modelType,
            'bundle'=>$bundle,
            'paginationModel'=>$paginationModel
        ]);
    }
}

?>