<?php

defined('_CHECK_') or die("Access denied");

use root\expansion\exp_contents\model\ModelContent;

class Contents_View_Type extends View {

    public function registry() {

        $models = $this->get('models');

        $views = [];
        $modelByid = [];
        if (is_array($models)) {
            foreach ($models as $model) {
                $filedView = $model->getViewField(ModelContent::SHORTCONTENT);
                $viewsF = [];
                foreach ($filedView as $view) {
                    $viewsF[$view->view_name] = $view;
                }
                $views[$model->contents->contents_id] = $viewsF;
                $modelByid[$model->contents->contents_id] = $model;
            }
        }


        $this->set('views', $views);
        $this->set('modelByid', $modelByid);
    }

}

?>