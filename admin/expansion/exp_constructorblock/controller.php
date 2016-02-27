<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\helpers\DataTime;
use maze\helpers\FileHelper;
use maze\fields\FieldHelper;
use maze\base\JsExpression;
use maze\helpers\Json;
use admin\expansion\exp_constructorblock\table\Block;
use admin\expansion\exp_constructorblock\model\ModelBlock;
use admin\expansion\exp_constructorblock\table\ViewBlock;

class Constructorblock_Controller extends Controller {

    public function actionDisplay() {

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $model = Block::find()->from(['b' => Block::tableName()])
                    ->joinWith(['type', 'installApp'])->groupBy('b.code');

          

            return (new GridFormat([
                'id' => 'constructorblock-grid',
                'model' => $model,
                'colonum' => 'b.date_create',
                'colonumData' => [
                    'id' => '$data->code',
                    'menu' => function($data) {
                        return "<span class=\"menu-icon-handle\"></span>";
                    },
                    'bundle',
                    'title',
                    'description',
                    'expansion',
                    'typename' => function($data) {
                        if(is_object($data) && isset($data->type->title)){
                             return $data->type->title;
                        }
                       return '-';
                    },
                    'code'
                ]
            ]))->renderJson();
        }
        return parent::display();
    }

    public function actionBundle($expansion, $type = 'html') {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        $position = (new ModelBlock())->getListType($expansion);

        if ($type == 'html') {
            return Html::renderSelectOptions(null, $position);
        } elseif ($type == 'json') {
            return json_encode(['html' => $position]);
        }
    }

    public function actionFilters($expansion, $bundle, array $not = []) {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        $modelHelp = new ModelBlock;
        $filters = $modelHelp->getFilters($expansion, $bundle, $not);
        return json_encode(['html' => $filters]);
    }

    public function actionFilter($filter, array $params = []) {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        $modelHelp = new ModelBlock;
        $modelForm = $modelHelp->getModelFilter($filter);

        if ($this->request->isPost()) {

            $modelForm->load($this->request->post(null, 'none'));

            if ($this->request->isAjax() && $this->request->get('checkform') == 'constructorblock-filter-form') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if ($modelForm->validate()) {
                $modelForm->field = $params['field'];
                return json_encode(['html' => $modelForm->attributes]);
            }
        }
        $modelForm->attributes = $params;

        $formXml = $modelHelp->getFormFilter($filter);
        return $this->renderPart("filter", null, null, ['modelForm' => $modelForm, 'modelHelp' => $modelHelp, 'formXml' => $formXml]);
    }

    public function actionSorts($expansion, $bundle, array $not = []) {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        $modelHelp = new ModelBlock;
        $sorts = $modelHelp->getSorts($expansion, $bundle, $not);
        return json_encode(['html' => $sorts]);
    }

    public function actionFields($expansion, $bundle) {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        $modelHelp = new ModelBlock;
        $fields = $modelHelp->getFields($expansion, $bundle);
        return json_encode(['html' => $fields]);
    }

    public function actionField($field_exp_id, $expansion, array $params = []) {
        if (!$this->request->isAjax() || $this->request->get('clear') !== 'ajax') {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }

        $field = FieldHelper::find(['{{%field_exp}}.expansion' => $expansion, '{{%field_exp}}.field_exp_id' => $field_exp_id, '{{%field_exp}}.active' => 1]);

        $modelView = new ViewBlock();

        $modelView->expansion = $expansion;
        $modelView->bundle = $field->bundle;
        $modelView->field_exp_id = $field_exp_id;
        $field_view = isset($params['field_view']) ? $params['field_view'] : null;
        $metaView = null;
        $fieldViewModel = null;
        $modelView->field_view = $field_view;
        if ($field_view && $field->getIsView($field_view)) {
            $metaView = $field->getConfigView($field_view);
            $fieldViewModel = $field->getView($field_view);
        }

        if ($this->request->isPost()) {

            $modelView->load($this->request->post());

            if ($fieldViewModel) {
                $fieldViewModel->load($this->request->post(null, 'none'));
            }

            if ($this->request->isAjax() && $this->request->get('checkform') == 'constructorblock-field-form') {

                return json_encode(['errors' => FormBuilder::validate($modelView, $fieldViewModel)]);
            }


            if ($modelView->validate()) {
                if ($fieldViewModel) {
                    if (!$fieldViewModel->validate()) {
                        $error = true;
                    } else {
                        $modelView->field_view_param = $fieldViewModel->attributes;
                    }
                }

                return json_encode(['html' => $modelView->attributes]);
            }
        }
        $modelView->attributes = $params;
        if ($fieldViewModel && $modelView->field_view_param) {
            $fieldViewModel->attributes = $modelView->field_view_param;
        }

        return $this->renderPart("field", null, null, [
                    'modelView' => $modelView,
                    'metaView' => $metaView,
                    'fieldViewModel' => $fieldViewModel,
                    'field' => $field
        ]);
    }

    public function actionAdd() {
        $model = new Block(['scenario' => 'create']);
        $modelHelp = new ModelBlock;

        if ($this->request->isPost()) {
            $model->load($this->request->post(null, 'none'));
            if ($this->request->isAjax() && $this->request->get('checkform') == 'constructorblock-form') {
                return json_encode(['errors' => FormBuilder::validate($model)]);
            }
            if ($model->validate()) {
                if ($modelHelp->saveBloc($model, $this->request->post(null, 'none'))) {
                    $this->setMessage(Text::_("EXP_CONSTRUCTORBLOCK_BLOCK_CREATE", ['name' => $model->title]), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect(['/admin/constructorblock']);
                    }
                    return $this->setRedirect([['run' => 'edit', 'code' => $model->code]]);
                } else {
                    $this->setMessage($model->getErrors(), "error");
                    return $this->setRedirect(['/admin/constructorblock']);
                }
            }
        }
        return $this->renderPart("form", false, "form", ['model' => $model, 'modelHelp' => $modelHelp]);
    }

    public function actionEdit($code, $return = null) {

        $model = Block::find()->from(['b' => Block::tableName()])->joinWith(['filter', 'sort', 'view'=>function($query){
            $query->orderBy('v.sort');
        }])->where(['b.code' => $code])->one();

        if (!$model) {
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_CONSTRUCTORBLOCK_BLOCK_NOT_ID", ['code' => $code]));
        }


        $modelHelp = new ModelBlock;

        if ($this->request->isPost()) {
            $model->load($this->request->post(null, 'none'));
            if ($this->request->isAjax() && $this->request->get('checkform') == 'constructorblock-form') {
                return json_encode(['errors' => FormBuilder::validate($model)]);
            }
            if ($model->validate()) {
                if ($modelHelp->saveBloc($model, $this->request->post(null, 'none'))) {
                    $this->setMessage(Text::_("EXP_CONSTRUCTORBLOCK_BLOCK_UPDATE", ['name' => $model->title]), 'success');
                    if($return){
                        return $this->setRedirect($return);
                    }
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect(['/admin/constructorblock']);
                    }
                    return $this->setRedirect([['run' => 'edit', 'code' => $model->code]]);
                } else {
                    $this->setMessage($model->getErrors(), "error");
                    return $this->setRedirect(['/admin/constructorblock']);
                }
            }
        }
        return $this->renderPart("form", false, "form", ['model' => $model, 'modelHelp' => $modelHelp]);
    }

    public function actionDelete(array $code) {
        $modelHelp = new ModelBlock;
        if ($modelHelp->deleteBlock($code)) {
            $this->setMessage(Text::_("EXP_CONSTRUCTORBLOCK_BLOCK_DELETE"), 'success');
        } else {
            $this->setMessage(Text::_("EXP_CONSTRUCTORBLOCK_BLOCK_DELETE_ERR"), "error");
        }

        $this->setRedirect(['/admin/constructorblock']);
    }

    public function actionClose() {
        $this->setMessage(Text::_("EXP_CONSTRUCTORBLOCK_BLOCK_CANCEL"), 'info');
        $this->setRedirect(['/admin/constructorblock']);
    }



}

?>