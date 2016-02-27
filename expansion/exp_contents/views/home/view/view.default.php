<?php

defined('_CHECK_') or die("Access denied");
use root\expansion\exp_contents\model\ModelContent;

class Contents_View_Home extends View {

    public function registry() {
        
        $models = $this->get('models');

        $meta = ['title' => null, 'description' => null, 'keywords' => null, 'robots' => null];

        if ($this->router->menu) {
            if (!empty($this->router->menu->meta_title)) {
                $meta["title"] = $this->router->menu->meta_title;
            } elseif ($this->router->menu->name) {
                $meta["title"] = $this->router->menu->name;
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
        
        $views = [];
        $modelByid = [];
        if(!empty($models)){
            foreach($models as $model){
                $views[$model->contents->contents_id] = $model->getViewField(ModelContent::SHORTCONTENT);
                $modelByid[$model->contents->contents_id] = $model;
            }
        }
        
        
        $this->set('views', $views);
        $this->set('modelByid', $modelByid);
        
    }

}

?>