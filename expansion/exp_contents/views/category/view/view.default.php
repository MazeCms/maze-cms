<?php

defined('_CHECK_') or die("Access denied");

use root\expansion\exp_contents\model\ModelTerm;
use root\expansion\exp_contents\model\ModelContent;
use ui\grid\PaginationFormat;

class Contents_View_Category extends View {

    public function registry() {

        $model = $this->get('model');
        
        $this->document->setBobyClass('category-alias-' . $model->routes->alias);
        $this->document->setBobyClass('category-id-' . $model->term->term_id);
        $this->document->setBobyClass('category-type-' . $model->term->bundle); 
         
        $meta = ['title' => null, 'description' => null, 'keywords' => null, 'robots' => null];

        $routes = $model->getRoutes();

        if (empty($meta["title"])) {
            if ($routes->meta_title) {
                $meta["title"] = $routes->meta_title;
            }else{
                $meta["title"] = $model->getTitle();
            }
        }

        if (!empty($routes->meta_description)) {
            $meta["description"] = $routes->meta_description;
        }

        if (!empty($routes->meta_keywords)) {
            $meta["keywords"] = $routes->meta_keywords;
        }

        if (!empty($routes->meta_robots)) {
            $meta["robots"] = $routes->meta_robots;
        }
        
        if ($this->router->menu) {
            
            if (empty($meta["title"]) && !empty($this->router->menu->meta_title)) {
                $meta["title"] = $this->router->menu->meta_title;
            } 

            if (empty($meta["description"]) && !empty($this->router->menu->meta_des)) {
                $meta["description"] = $this->router->menu->meta_des;
            }

            if (empty($meta["keywords"]) && !empty($this->router->menu->meta_key)) {
                $meta["keywords"] = $this->router->menu->meta_key;
            }

            if (empty($meta["keywords"]) && !empty($this->router->menu->meta_robots)) {
                $meta["robots"] = $this->router->menu->meta_robots;
            }
        }

        if($meta["robots"]) $this->document->set("robots", $meta["robots"]);        
        if($meta["title"])  $this->document->set("title", $meta["title"]);
        if($meta["description"]) $this->document->set("description", $meta["description"]);
        if($meta["keywords"]) $this->document->set("keywords", $meta["keywords"]);

        $filedView = $model->getViewField(ModelTerm::FULLCONTENT);

        $views = [];
        $field = [];
        foreach ($filedView as $view) {
            $views[$view->group_name][$view->view_name] = $view;
            $field[$view->view_name] = $view;
        }

        $paginationModel = RC::getDb()->cache(function($db) use ($model){
                return new PaginationFormat(['model' => $model->getFindContents()]);
        }, null, 'exp_contents');
        
        $models = ModelContent::createModel($paginationModel->data);
        
        $contentsView = [];
        $contentsModel = [];
        if (is_array($models)) {
            foreach ($models as $cont) {
                $contentsView[$cont->contents->contents_id] = $cont->getViewField(ModelContent::SHORTCONTENT);
                $contentsModel[$cont->contents->contents_id] = $cont;
            }
        }

        $childrenCategoryModel = $model->getChildCategory();
        $categoryView = [];
        $categoryModel = [];
        if (is_array($childrenCategoryModel)) {
            foreach ($childrenCategoryModel as $term) {
                $categoryView[$term->term->term_id] = $term->getViewField(ModelContent::SHORTCONTENT);
                $categoryModel[$term->term->term_id] = $term;
            }
        }
        
        $this->set($field);
        $this->set($views);
        $this->set('views', $views);
        //поля категории
        $this->set('filedView', $filedView);
        //материалы категории
        $this->set('contentsView', $contentsView);
        //дочерние категории
        $this->set('categoryView', $categoryView);
        $this->set('categoryModel', $categoryModel);
        //модель пагинатора
        $this->set('paginationModel', $paginationModel);
        $this->set('contentsModel', $contentsModel);
    }

}

?>