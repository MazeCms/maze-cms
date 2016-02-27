<?php

use maze\table\ContentType;
use root\expansion\exp_contents\model\ModelContent;
use exp\exp_contents\helpers\ContentsHelper;

class Contents_Controller extends Controller {
    
    public function actionDisplay($contents_id) {

       
        $model = ModelContent::find($contents_id);
        
        if(!$model){
             throw new maze\exception\NotFoundHttpException(Text::_("EXP_CONTENTS_CONTENTS_NOT_ID", ['id'=>$contents_id]));
        }
        
        if(($id_role = $model->getIDRole())){
            if(!RC::app()->access->getAccessIDRole($id_role)){
                throw new maze\exception\NotFoundHttpException(Text::_("EXP_CONTENTS_CONTENTS_NOT_USERROLE", ['name'=>$model->getTitle()]));
            }
        }

        $filedView = $model->getViewField(ModelContent::FULLCONTENT);
        
        if(!$filedView){
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_CONTENTS_CONTENTS_NOT_VIEW", ['name'=>$model->contents->bundle]));
        }

        return $this->renderPart(ContentsHelper::getLayoutViewContent($model->contents, 'default'), null, null, ['model'=>$model]); 
    }
    
    
}

?>