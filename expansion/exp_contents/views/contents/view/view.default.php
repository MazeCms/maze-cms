<?php

defined('_CHECK_') or die("Access denied");
use root\expansion\exp_contents\model\ModelContent;

class Contents_View_Contents extends View {

    public function registry() {
        
        $model = $this->get('model');
        
        $this->document->setBobyClass('type-content-' . $model->contents->bundle);
        $this->document->setBobyClass('content-alias-' . $model->routes->alias);
        $this->document->setBobyClass('content-id-' . $model->id);
        
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
            if (!empty($this->router->menu->meta_title)) {
                $meta["title"] = $this->router->menu->meta_title;
            }

            if (!empty($this->router->menu->meta_des)) {
                $meta["description"] = $this->router->menu->meta_des;
            }

            if (!empty($this->router->menu->meta_key)) {
                $meta["keywords"] = $this->router->menu->meta_key;
            }

            if (!empty($this->router->menu->meta_robots)) {
                $meta["robots"] = $this->router->menu->meta_robots;
            }
        }
      
        if($meta["robots"]) $this->document->set("robots", $meta["robots"]);        
        if($meta["title"])  $this->document->set("title", $meta["title"]);
        if($meta["description"]) $this->document->set("description", $meta["description"]);
        if($meta["keywords"]) $this->document->set("keywords", $meta["keywords"]);
        
        $filedView = $model->getViewField(ModelContent::FULLCONTENT);
        
        $views = [];
        $field = [];
        foreach($filedView as $view){            
            $views[$view->group_name][$view->view_name] = $view;
            $field[$view->view_name] = $view;
        }
        
        $this->set($field);
        $this->set($views);
        $this->set('views', $views);
        $this->set('filedView', $filedView);
        
    }

}

?>