    <?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use maze\table\Widgets;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;

class Widget_Controller extends Controller {

    public function accessFilter() {
        return [
            'add edit copy widgets position saveposition widgetPosition publish unpublish sort moving pack' => function($cotroller){
                if($this->request->get('front_back') === '0'){
                  return $this->_access->roles("widget", "EDIT_WIDGET_ADMIN");
                }                
                return $this->_access->roles("widget", "EDIT_WIDGET");
            },
            'display'=>function(){
                if($this->request->get('front_back') === '0'){
                  return $this->_access->roles("widget", "EDIT_WIDGET_ADMIN");
                }
                return true;
            },        
            'delete' => ["widget", "DELET_WIDGET"]
        ];
    }
    
    public function actionDisplay() {
        
        $front = $this->request->get('front_back') === false ? 1 : $this->request->get('front_back');
        
        $modelFilter = $this->form('FilterWidget');
        $model = $this->model('Widget');
        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }
        
        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
       
            $model = maze\table\Widgets::find()->joinWith([
                        'role',
                        'lang',
                        'tmp',
                        'app'=>function($query)use($front){$query->andWhere(['app.front_back'=>$front]);}
                    ])->from(['w' => maze\table\Widgets::tableName()]);
            
            $modelFilter->queryBilder($model);
            
            return (new GridFormat([
                'id' => 'widgets-grid',
                'model' => $model,
                'colonum' => 'w.id_tmp, w.position, w.ordering',
                'colonumData' => [
                    'id' => '$data->id_wid',
                    'menu' => function() {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'name',
                    'enabled',        
                    'title',
                    'tmp_name'=>function($data){
                        $tmp = $data->tmp;
                        return $tmp ? $tmp->title : Text::_('EXP_WIDGET_WIDGETS_FILTER_TMP_CODE');
                    },        
                    'role' => function($data) {
                        if (!($roles = $data->role)) {
                            return Text::_("EXP_WIDGET_WIDGETS_TABLE_BODY_ALL");
                        }
                        return implode(', ', array_map(function($role) {return $role->name;}, $roles));
                    },
                    'lang_title'=>function($data){
                        $lang = $data->lang;
                        return $lang ? $lang->title : Text::_("EXP_WIDGET_WIDGETS_TABLE_BODY_ALL");
                    },
                    'position',
                    'id_tmp',        
                    'time_active' => function($data) {
                        return DataTime::format($data->time_active, false, '-');
                    },
                    'time_inactive' => function($data) {
                        return DataTime::format($data->time_inactive, false, '-');
                    },
                    'id_wid'
                ]
            ]))->renderJson();
        }

        return parent::display(['front'=>$front, 'modelFilter'=>$modelFilter, 'model'=>$model]);
    }
    
    public function actionWidgets($front){
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        $widgets = $this->model('Widget')->getWidget($front);
 
        return $this->renderPart("modal", false, "modal", [
            'widgets' => $widgets
        ]);
    }
   
    public function actionPosition($id_tmp, $type='html'){
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        $position = $this->model('Widget')->getPosition($id_tmp);
 
        if($type == 'html'){
            return Html::renderSelectOptions(null, $position);
        }
        elseif($type == 'json'){
            return json_encode(['html'=>$position]);
        }
        
    }
    
    public function actionSaveposition($id_wid, $position){
       if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        if($widget = Widgets::findOne($id_wid)){
            $widget->position = $position;
            $widget->save();
        }
        RC::getCache("fw_widgets")->clearTypeFull();
    }
    
    public function actionWidgetPosition($id_tmp, $position){
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        $position = $this->model('Widget')->getWidgetPosition($id_tmp, $position);
 
        return json_encode(['html'=>$position]);
    }
    
    public function actionAdd($name, $front) {
        
        $modelForm = $this->form('FormWidget');
        $model = $this->model('Widget');

        if(!$model->hasWidget($name, $front)){
             throw new maze\exception\NotFoundHttpException(Text::_("EXP_WIDGET_FORM_ALERT_TITLE"));
        }
        $modelForm->name = $name;
        if ($this->request->isPost()) {

            $modelForm->load($this->request->post(null, 'none'));

            if ($this->request->isAjax() && $this->request->get('checkform') == 'widget-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                
                if ($model->saveWidget($modelForm)) {
                    $this->setMessage(Text::_("EXP_WIDGET_CONTROLLER_MESS_SAVE_CLOSE"), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect([['front_back'=>$front]]);
                    }
                    return $this->setRedirect([['run' => 'edit', 'id_wid' => $modelForm->id_wid, 'front'=>$front]]);
                } else {
                    $this->setMessage(Text::_('EXP_WIDGET_CONTROLLER_MESS_SAVE_ERROR'), 'error');
                }
            }
        } 
               
        return $this->renderPart("form", false, "form", [
            'modelForm' => $modelForm,
            'name'=>$name,
            'front'=>$front,
            'model'=>$model
        ]);
    }

    public function actionEdit($id_wid, $front, $return = null) {
        $modelForm = $this->form('FormWidget');
        $model = $this->model('Widget');

        $widget =  Widgets::find()->joinWith(['menu', 'exp', 'url', 'accessRole'])
                ->from(['w'=>Widgets::tableName()])->where(['w.id_wid'=>$id_wid])->one();
        if(!$widget){
             throw new maze\exception\NotFoundHttpException(Text::_("EXP_WIDGET_FORM_ALERT_TITLE"));
        }
        
        $modelForm->name = $widget->name;
        $modelForm->id_wid = $widget->id_wid;
        
        if ($this->request->isPost()) {

            $modelForm->load($this->request->post(null, 'none'));

            if ($this->request->get('action') == 'saveCopy' || $this->request->get('action') == 'copy') {
                $modelForm->title = $modelForm->title . " - ( " . Text::_("EXP_WIDGET_TITLE_COPY") . " )";
                $modelForm->id_wid = null;
            }
            
            if ($this->request->isAjax() && $this->request->get('checkform') == 'widget-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
           
                if ($model->saveWidget($modelForm)) {
                    $this->setMessage(Text::_("EXP_WIDGET_CONTROLLER_MESS_SAVE_CLOSE"), 'success');
                    if($return){                        
                       return $this->setRedirect($return); 
                    }
                    if ($this->request->get('action') == 'saveClose' || $this->request->get('action') == 'saveCopy') {
                        return $this->setRedirect([['front_back'=>$front]]);
                    }
                    return $this->setRedirect([['run' => 'edit', 'id_wid' => $modelForm->id_wid, 'front'=>$front]]);
                } else {
                    $this->setMessage(Text::_('EXP_WIDGET_CONTROLLER_MESS_SAVE_ERROR'), 'error');
                }
            }
        }
        else{
            $modelForm->attributes = $widget->attributes;
            $modelForm->id_exp = array_map(function($arr){return $arr->id_exp; }, $widget->exp); 
            $modelForm->id_menu = array_map(function($arr){return $arr->id_menu; }, $widget->menu);
            $modelForm->url = array_map(function($arr){return $arr->attributes; }, $widget->url); 
            $modelForm->id_role = array_map(function($arr){return $arr->id_role; }, $widget->accessRole);
        }
        return $this->renderPart("form", false, "form", [
            'modelForm' => $modelForm,
            'name'=>$widget->name,
            'front'=>$front,
            'model'=>$model
        ]);
    }
    
    public function actionDelete(array $id_wid, $front) {
      
        if($this->model('Widget')->delete($id_wid)) {
            $this->setMessage(Text::_("EXP_WIDGET_CONTROLLER_MESS_DELET"), "success");
        }else{
            $this->setMessage(Text::_("EXP_WIDGET_CONTROLLER_MESS_DELET_ERROR"), "error");
        }
        $this->setRedirect([['front_back'=>$front]]);
    }

    public function actionPublish(array $id_wid, $front) {
        
        $this->model('Widget')->enable($id_wid, 1);
        if(!$this->request->isAjax()){
            $this->setMessage("EXP_WIDGET_CONTROLLER_MESS_PUBLISH", 'success');
            $this->setRedirect([['front_back'=>$front]]);
        }
            
    }

    public function actionUnpublish(array $id_wid, $front) {
        
        $this->model('Widget')->enable($id_wid, 0);
        if(!$this->request->isAjax()){
            $this->setMessage("EXP_WIDGET_CONTROLLER_MESS_UNPUBLISH", 'success');
            $this->setRedirect([['front_back'=>$front]]);
        }
   
    }
    
    public function actionCopy(array $id_wid, $front){
        
        $widgets = Widgets::findAll(['id_wid'=>$id_wid]);
        $model = $this->model('Widget');
        if(!empty($widgets)){
            foreach($widgets as $wid){
                $model->copy($wid);
            }
            $this->setMessage("EXP_WIDGET_CONTROLLER_MESS_SAVECOPY_CLOSE", 'success');
        }else{
            $this->setMessage(Text::_("EXP_WIDGET_CONTROLLER_MESS_COPY_ERROR"), "error");
        }
        $this->setRedirect([['front_back'=>$front]]);
    }
    
    public function actionSort(array $sort){
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        foreach($sort as $wid){
            $widget = Widgets::findOne($wid['id_wid']);
            if($widget){
                $widget->ordering = $wid['ordering'];
                $widget->save();
            }
        }
        
    }

    public function actionMoving(array $id_wid, $front) {
        
        $model = $this->model('Widget');
        $modelForm = $this->form('FormMoving');
        if ($this->request->isPost()) {
             $modelForm->load($this->request->post());

            if ($this->request->isAjax() && $this->request->get('checkform') == 'widget-form-moving') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                $widgets = Widgets::findAll($id_wid);
                if($widgets){
                    foreach($widgets as $wid){
                        $wid->attributes = $modelForm->attributes;
                        $wid->save();
                    }
                }
            }
            
            $this->setMessage("EXP_WIDGET_CONTROLLER_MESS_MOVING", 'success');
            $this->setRedirect([['front_back'=>$front]]);
        }
        
        return $this->renderPart("moving", false, "modal", [
            'modelForm' => $modelForm,
            'front'=>$front,
            'model'=>$model
        ]);
    }
    
    public function actionPack(array $id_wid, $front) {
        
        $model = $this->model('Widget');
        $modelForm = $this->form('FormPack');
        if ($this->request->isPost()) {
             $modelForm->load($this->request->post());

            if ($this->request->isAjax() && $this->request->get('checkform') == 'widget-form-pack') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                $model->pack($id_wid, $modelForm);
            }
            
            $this->setMessage("EXP_WIDGET_CONTROLLER_MESS_PACK", 'success');
            $this->setRedirect([['front_back'=>$front]]);
        }
        
        return $this->renderPart("pack", false, "modal", [
            'modelForm' => $modelForm,
            'front'=>$front,
            'model'=>$model
        ]);
    }

    public function actionClose($front) {
        $this->setMessage("EXP_WIDGET_CONTROLLER_MESS_CLOSE", 'info');
        $this->setRedirect([['front_back'=>$front]]);
    }

}

?>