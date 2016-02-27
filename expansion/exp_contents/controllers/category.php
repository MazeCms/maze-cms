<?php defined('_CHECK_') or die("Access denied");

use maze\helpers\ArrayHelper;
use maze\table\ContentType;
use maze\table\FieldExp;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\fields\FieldHelper;
use maze\table\DictionaryTerm;
use maze\db\Query;
use maze\table\Contents;
use maze\table\ContentTermSort;
use root\expansion\exp_contents\model\ModelTerm;
use exp\exp_contents\helpers\ContentsHelper;
use root\expansion\exp_contents\model\ModelContent;

class Contents_Controller_Category extends Controller {
    

    public function actionDisplay($term_id) {

        $model = ModelTerm::find($term_id);
        
        if(!$model){
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_CONTENTS_CONTENTS_NOT_ID", ['id'=>$term_id]));
        }
        
        if(($id_role = $model->getIDRole())){
            if(!RC::app()->access->getAccessIDRole($id_role)){
                throw new maze\exception\NotFoundHttpException(Text::_("EXP_CONTENTS_CONTENTS_NOT_USERROLE", ['name'=>$model->getTitle()]));
            }
        }
                
        $filedView = $model->getViewField(ModelTerm::FULLCONTENT);
        
        if(!$filedView){
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_CONTENTS_CONTENTS_NOT_VIEW", ['name'=>$model->term->bundle]));
        }

        return $this->renderPart(ContentsHelper::getLayoutViewTerm($model->term, 'default'), null, null, ['model'=>$model]); 
    }
    
}

?>