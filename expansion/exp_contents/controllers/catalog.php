<?php defined('_CHECK_') or die("Access denied");

use maze\helpers\ArrayHelper;
use maze\table\ContentType;
use maze\table\FieldExp;
use maze\helpers\Html;
use maze\fields\FieldHelper;
use maze\table\DictionaryTerm;
use maze\table\Contents;
use maze\table\ContentTermSort;
use root\expansion\exp_contents\model\ModelTerm;
use exp\exp_contents\helpers\ContentsHelper;

class Contents_Controller_Catalog extends Controller {
    

    public function actionDisplay($bundle) {

        $queryCategory = ModelTerm::findAll(['dt.bundle'=>$bundle, 'dt.parent'=>0]);
        $modelType = ContentType::findOne(['expansion'=>'dictionary', 'bundle'=>$bundle]);
        
        $category = RC::getDb()->cache(function($db) use ($queryCategory){
            return  $queryCategory->all();
        }, null, 'exp_contents');
        
        if(!$category){
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_CONTENTS_CONTENTS_NOT_ID", ['id'=>$bundle]));   
        }
        
        $model = ModelTerm::createModel($category);
        
        return $this->renderPart(ContentsHelper::getLayoutViewCatalog($bundle, 'catalog'), null, 'catalog', ['model'=>$model, 'bundle'=>$bundle, 'modelType'=>$modelType]); 
    }
    
}

?>