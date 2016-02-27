<?php

use maze\table\Menu;

class Sitemapupdate_Plugin_Contents extends Plugin {

    public function afterSaveContent($id, $contents) {

        if (!RC::app()->getComponent('sitemap')->is)
            return false;
        
        $path = RC::getRouter(RC::ROUTERSITE)->createRoute(['/contents/controller/contents/default', ['contents_id' => $id]]);
        if ($path && is_string($path)) {
            $items = admin\expansion\exp_sitemap\table\SitemapLink::find()
                    ->where(['loc' =>$path])
                    ->all();

            if ($items) {
                foreach ($items as $item) {
                    $item->lastmod = date('Y-m-d H:i:s');
                    $item->save();
                }
            }
        }
    }

}

?>