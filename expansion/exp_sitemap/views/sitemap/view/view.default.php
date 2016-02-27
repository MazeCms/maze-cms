<?php

defined('_CHECK_') or die("Access denied");

class Sitemap_View_Sitemap extends View {

    public function registry() {
        
        $model = $this->get('model');

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
          
          
        $routes = $model->getRoute();

        if (empty($meta["title"])) {
            if ($routes->meta_title) {
                $meta["title"] = $routes->meta_title;
            }else{
                $meta["title"] = $model->map->title;
            }
        }

        if (empty($meta["description"]) && !empty($routes->meta_description)) {
            $meta["description"] = $routes->meta_description;
        }

        if (empty($meta["keywords"]) && !empty($routes->meta_keywords)) {
            $meta["keywords"] = $routes->meta_keywords;
        }

        if (empty($meta["robots"]) && !empty($routes->meta_robots)) {
            $meta["robots"] = $routes->meta_robots;
        }
      
        if($meta["robots"]) $this->document->set("robots", $meta["robots"]);        
        if($meta["title"])  $this->document->set("title", $meta["title"]);
        if($meta["description"]) $this->document->set("description", $meta["description"]);
        if($meta["keywords"]) $this->document->set("keywords", $meta["keywords"]);
  
        
    }

}

?>