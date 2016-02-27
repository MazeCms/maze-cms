<?php

class Sitemapbtn_Plugin_System extends Plugin {

    public function beforeDispatcher() {


        if ($id_role = $this->_access->getIdRole()) {
            if (!RC::app()->getComponent('sitemap')->is)
                return false;
            
            $maps = admin\expansion\exp_sitemap\table\Sitemap::find()->all();
            foreach ($maps as  $m) {
                $items[] = [
                    "class" => 'ContextMenu',
                    "HREF" => ["/admin/sitemap", ["run" => "update", 'sitemap_id' => $m->sitemap_id, "clear" => "ajax"]],
                    "ACTION" => "$.get(this.href, $.noop, 'json'); return false;",
                    "TITLE" =>$m->title,
                ];
            }
            if(empty($items)) return false;
            
            RC::app()->toolbar->addGroup("system", [
                'class' => 'Buttonset',
                "TITLE" => "PLG_SYSTEM_SITEMAPBTN_BTN_NAME",
                "TYPE" => Buttonset::BTNMIN,
                "VISIBLE" => $this->_access->roles("sitemap", "VIEW_ADMIN"),
                "SORT" => 2,
                "SORTGROUP" => 1,
                "SRC" => "/library/image/icon/16/location/white/icon-map.png",
                "HINT" => ["TITLE" => "PLG_SYSTEM_SITEMAPBTN_NAME", "TEXT" => "PLG_SYSTEM_SITEMAPBTN_DESCRIPTION"],
                "MENU" => $items
            ]
        );
        }
    }

}

?>