<?php
/**
 * Системные таблицы
 */
return [
    'roles'=>[
        'columns'=>[
            'id_role'=>'int(11) AUTO_INCREMENT NOT NULL',
            'name'=>'varchar(255) NOT NULL',
            'description'=>'text DEFAULT NULL',
            'PRIMARY KEY(id_role)'
        ],
    ],
    'access_role'=>[
        'columns'=>[
            'id_acr'=>'int(11) AUTO_INCREMENT NOT NULL',
            'exp_name'=>'varchar(255) NOT NULL',
            'key_role'=>'varchar(255) NOT NULL',
            'key_id'=>'int(11) DEFAULT NULL',
            'id_role'=>'int(11) DEFAULT "0"',
            'PRIMARY KEY(`id_acr`)',
            'FOREIGN KEY (`id_role`) REFERENCES  {{%roles}} (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE'
        ],
    ],
    'private_rule'=>[
        'columns'=>[
            'name'=>'varchar(64) NOT NULL',
            'exp_name'=>'varchar(255) NOT NULL',
            'name'=>'varchar(255) NOT NULL',
            'data'=>'text',
            'created_at'=>'int(11) NOT NULL',
            'updated_at'=>'int(11) NOT NULL',
            'PRIMARY KEY(`name`)'
        ]
    ],
    'private'=>[
        'columns'=>[
            'id_priv'=>'int(11) AUTO_INCREMENT NOT NULL',
            'exp_name'=>'varchar(255) NOT NULL',
            'name'=>'varchar(255) NOT NULL',
            'rule_name'=>'varchar(64) DEFAULT NULL',
            'title'=>'text NOT NULL',
            'description'=>'tinytext DEFAULT NULL',
            'PRIMARY KEY(`id_priv`)',
            'FOREIGN KEY (`rule_name`) REFERENCES  {{%private_rule}} (`name`) ON DELETE RESTRICT ON UPDATE RESTRICT'
        ]
    ],
    'role_private'=>[
        'columns'=>[
            'id_role'=>'int(11) NOT NULL',
            'id_priv'=>'int(11) NOT NULL',
            'PRIMARY KEY (`id_role`,`id_priv`)',
            'FOREIGN KEY (`id_role`) REFERENCES  {{%roles}} (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (`id_priv`) REFERENCES  {{%private}} (`id_priv`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'languages'=>[
        'columns'=>[
            'id_lang'=>'int(11) AUTO_INCREMENT NOT NULL',
            'lang_code'=>'varchar(255) NOT NULL',
            'title'=>'varchar(255) NOT NULL',
            'reduce'=>'varchar(255) NOT NULL',
            'img'=>'varchar(255) NOT NULL',
            'ordering'=>"int(11) DEFAULT '0'",
            'enabled'=>"int(1) DEFAULT '0'",
            'PRIMARY KEY (`id_lang`)'
        ]
    ],
    'users'=>[
       'columns'=>[
           'id_user'=>'int(11) AUTO_INCREMENT NOT NULL',
           'username'=>'varchar(255) NOT NULL',
           'name'=>'varchar(255) NOT NULL',
           'avatar'=>'text',
           'email'=>'varchar(255) NOT NULL',
           'password'=>'varchar(255) NOT NULL',
           'registerDate'=>'datetime DEFAULT NULL',
           'lastvisitDate'=>'datetime DEFAULT NULL',
           'timeactiv'=>'datetime DEFAULT NULL',
           'keyactiv'=>'varchar(150) DEFAULT NULL',
           'id_lang'=>"int(11) DEFAULT '0'",
           'timezone'=>'varchar(255) DEFAULT NULL',
           'editor_admin'=>'varchar(255) DEFAULT NULL',
           'editor_site'=>'varchar(255) DEFAULT NULL',
           'status'=>"int(1) DEFAULT '0'",
           'bloc'=>"int(11) DEFAULT '0'",
           'PRIMARY KEY (`id_user`)'
       ] 
    ],
    'user_roles'=>[
        'columns'=>[
            'id_role'=>'int(11) NOT NULL',
            'id_user'=>'int(11) NOT NULL',
            'PRIMARY KEY (`id_role`,`id_user`)',
            'FOREIGN KEY (`id_role`) REFERENCES  {{%roles}} (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (`id_user`) REFERENCES  {{%users}} (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'install_app'=>[
        'columns'=>[
            'id_app'=>'int(11) AUTO_INCREMENT NOT NULL',
            'type'=>'varchar(255) NOT NULL',
            'name'=>'varchar(255) NOT NULL',
            'group_name'=>'varchar(255) DEFAULT NULL',
            'front_back'=>"int(1) DEFAULT '0'",
            'ordering'=>"int(11) DEFAULT '0'",
            'install_data'=>'datetime NOT NULL',
            'PRIMARY KEY (`id_app`)'
        ]
    ],
    'template'=>[
        'columns'=>[
            'id_tmp'=>'int(11) AUTO_INCREMENT NOT NULL',
            'name'=>'varchar(255) NOT NULL',
            'title'=>'varchar(255) NOT NULL',
            'home'=>"int(1) DEFAULT '0'",
            'front'=>"int(1) DEFAULT '0'",
            'time_active'=>'datetime DEFAULT NULL',
            'time_inactive'=>'datetime DEFAULT NULL',
            'param'=>'text',
            'PRIMARY KEY (`id_tmp`)'
        ]
    ],
    'lang_app'=>[
        'columns'=>[
            'id_lang_app'=>'int(11) AUTO_INCREMENT NOT NULL',
            'id_lang'=>'int(11) NOT NULL',
            'id_app'=>'int(11) NOT NULL',
            'defaults'=>"int(1) DEFAULT '0'",
            'ordering'=>"int(1) DEFAULT '0'",
            'enabled'=>"int(1) DEFAULT '0'",
            'PRIMARY KEY (`id_lang_app`)',
            'FOREIGN KEY (`id_lang`) REFERENCES  {{%languages}} (`id_lang`) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (`id_app`) REFERENCES  {{%install_app}} (`id_app`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'expansion'=>[
        'columns'=>[
            'id_exp'=>'int(11) AUTO_INCREMENT NOT NULL',
            'name'=>'varchar(255) NOT NULL',
            'id_tmp'=>'int(11) DEFAULT NULL',
            'time_cache'=>"int(11) DEFAULT '0'",
            'enable_cache'=>"int(11) DEFAULT '0'",
            'param'=>'text',
            'enabled'=>"int(1) DEFAULT '0'",
            'PRIMARY KEY (`id_exp`)',
            'FOREIGN KEY (`id_tmp`) REFERENCES  {{%template}} (`id_tmp`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'widgets'=>[
        'columns'=>[
            'id_wid'=>'int(11) AUTO_INCREMENT NOT NULL',
            'name'=>'varchar(255) NOT NULL',
            'title'=>'varchar(255) NOT NULL',
            'position'=>'varchar(255) NOT NULL',
            'ordering'=>"int(11) DEFAULT '0'",
            'time_cache'=>"int(11) DEFAULT '0'",
            'enable_cache'=>"int(1) DEFAULT '0'",
            'time_active'=>'datetime DEFAULT NULL',
            'time_inactive'=>'datetime DEFAULT NULL',
            'enabled'=>"int(1) DEFAULT '0'",
            'enable_php'=>"tinyint(1) NOT NULL DEFAULT '0'",
            'php_code'=>"text DEFAULT NULL",
            'title_show'=>"int(1) DEFAULT '0'",
            'id_tmp'=>"int(11) NOT NULL DEFAULT '0'",
            'id_lang'=>"int(11) DEFAULT '0'",
            'param'=>'text',
            'PRIMARY KEY (`id_wid`)'
        ]
    ],
    'widgets_exp'=>[
        'columns'=>[
            'id_wid'=>'int(11) NOT NULL',
            'id_exp'=>'int(11) NOT NULL',
            'PRIMARY KEY (`id_wid`,`id_exp`)',
            'FOREIGN KEY (`id_wid`) REFERENCES  {{%widgets}} (`id_wid`) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (`id_exp`) REFERENCES  {{%expansion}} (`id_exp`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'widgets_url'=>[
        'columns'=>[
            'url_id'=>'int(11) AUTO_INCREMENT NOT NULL',
            'id_wid'=>'int(11) NOT NULL',
            'method'=>'varchar(40) NOT NULL',
            'name'=>'varchar(100) DEFAULT NULL',
            'value'=>'varchar(1000) NOT NULL',
            'sort'=>"int(11) DEFAULT '0'",
            'visible'=>"tinyint(1) DEFAULT '0'",
            'PRIMARY KEY (`url_id`)',
            'FOREIGN KEY (`id_wid`) REFERENCES  {{%widgets}} (`id_wid`) ON DELETE CASCADE ON UPDATE CASCADE',
        ]
    ],
    'routes'=>[
        'columns'=>[
            'routes_id'=>'int(11) AUTO_INCREMENT NOT NULL',
            'expansion'=>'varchar(255) NOT NULL',
            'alias'=>'varchar(255) NOT NULL',
            'meta_title'=>'varchar(255) DEFAULT NULL',
            'meta_keywords'=>'varchar(500) DEFAULT NULL',
            'meta_description'=>'text DEFAULT NULL',
            'meta_robots'=>'varchar(45) DEFAULT NULL',
            'date_create'=>'datetime DEFAULT NULL',
            'PRIMARY KEY (`routes_id`)'
        ]
    ],
    'menu_group'=>[
        'columns'=>[
            'id_group'=>'int(11) AUTO_INCREMENT NOT NULL',
            'code'=>'varchar(64) NOT NULL',
            'name'=>'varchar(255) NOT NULL',
            'description'=>'text NOT NULL',
            'ordering'=>"int(11) DEFAULT '0'",
            'PRIMARY KEY (`id_group`)'
        ]
    ],
    'menu'=>[
       'columns'=>[
           'id_menu'=>'int(11) AUTO_INCREMENT NOT NULL',
           'id_group'=>"int(11) DEFAULT '0'",
           'routes_id'=>'int(11) NOT NULL',
           'typeLink'=>'varchar(255) NOT NULL',
           'name'=>'varchar(255) NOT NULL',
           'image'=>"varchar(500) DEFAULT NULL",
           'paramLink'=>'text NOT NULL',
           'time_active'=>'datetime DEFAULT NULL',
           'time_inactive'=>'datetime DEFAULT NULL',
           'ordering'=>"int(11) DEFAULT '0'",
           'enabled'=>"int(1) DEFAULT '0'",
           'id_tmp'=>'int(11) DEFAULT NULL',
           'id_lang'=>'int(11) DEFAULT NULL',
           'id_exp'=>'int(11) DEFAULT NULL',
           'parent'=>"int(11) NOT NULL DEFAULT '0'",
           'home'=>"int(1) DEFAULT '0'",
           'param'=>'text NOT NULL',
           'PRIMARY KEY (`id_menu`)',
           'FOREIGN KEY (`id_group`) REFERENCES  {{%menu_group}} (`id_group`) ON DELETE CASCADE ON UPDATE CASCADE',
           'FOREIGN KEY (`id_tmp`) REFERENCES  {{%template}} (`id_tmp`) ON DELETE CASCADE ON UPDATE CASCADE',
           'FOREIGN KEY (`id_lang`) REFERENCES  {{%languages}} (`id_lang`) ON DELETE CASCADE ON UPDATE CASCADE',
           'FOREIGN KEY (`id_exp`) REFERENCES  {{%expansion}} (`id_exp`) ON DELETE CASCADE ON UPDATE CASCADE',
           'FOREIGN KEY (`routes_id`) REFERENCES  {{%routes}} (`routes_id`) ON DELETE CASCADE ON UPDATE CASCADE'
       ] 
    ],
    'widgets_menu'=>[
        'columns'=>[
            'id_wid'=>'int(11) NOT NULL',
            'id_menu'=>'int(11) NOT NULL',
            'PRIMARY KEY (`id_wid`,`id_menu`)',
            'FOREIGN KEY (`id_wid`) REFERENCES  {{%widgets}} (`id_wid`) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (`id_menu`) REFERENCES  {{%menu}} (`id_menu`) ON DELETE CASCADE ON UPDATE CASCADE',
        ]
    ],
    'sessions'=>[
        'columns'=>[
            'id_sess'=>'int(11) AUTO_INCREMENT NOT NULL',
            'id_user'=>'int(11) NOT NULL',
            'sid'=>'varchar(255) NOT NULL',
            'ip'=>'varchar(200) NOT NULL',
            'agent'=>'varchar(600) NOT NULL',
            'time_start'=>'datetime NOT NULL',
            'time_last'=>'datetime NOT NULL',
            'PRIMARY KEY (`id_sess`)'
        ]
    ],
    'plugin'=>[
        'columns'=>[
            'id_plg'=>'int(11) AUTO_INCREMENT NOT NULL',
            'group_name'=>'varchar(255) NOT NULL',
            'name'=>'varchar(255) NOT NULL',
            'ordering'=>"int(11) DEFAULT '0'",
            'enabled'=>"int(1) DEFAULT '0'",
            'param'=>"text",
            'PRIMARY KEY (`id_plg`)'
        ]
    ],
    'lang_overload'=>[
        'columns'=>[
            'id'=>'int(11) AUTO_INCREMENT NOT NULL',
            'id_lang'=>'int(11) NOT NULL',
            'front'=>"tinyint(1) DEFAULT '0'",
            'constant'=>'varchar(255) DEFAULT NULL',
            'value'=>'text',
            'PRIMARY KEY (`id`)',
            'FOREIGN KEY (`id_lang`) REFERENCES  {{%languages}} (`id_lang`) ON DELETE CASCADE ON UPDATE CASCADE',
        ]
    ],
    'lang_cache'=>[
        'columns'=>[
            'id'=>'int(11) AUTO_INCREMENT NOT NULL',
            'id_app'=>'int(11) NOT NULL',
            'id_lang'=>'int(11) NOT NULL',
            'path'=>'varchar(2000) NOT NULL',
            'constant'=>'varchar(255) DEFAULT NULL',
            'value'=>'text NOT NULL',
            'PRIMARY KEY (`id`)',
            'FOREIGN KEY (`id_app`) REFERENCES  {{%lang_app}} (`id_app`) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (`id_lang`) REFERENCES  {{%languages}} (`id_lang`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'group_exp'=>[
        'columns'=>[
            'id_group'=>'int(11) AUTO_INCREMENT NOT NULL',
            'name'=>'varchar(255) NOT NULL',
            'title'=>'varchar(255) NOT NULL',
            'icon'=>'varchar(255) NOT NULL',
            'ordering'=>"int(11) DEFAULT '0'",
            'description'=>'text DEFAULT NULL',
            'PRIMARY KEY (`id_group`)'
        ]
    ],
    'desktop'=>[
        'columns'=>[
            'id_des'=>'int(11) AUTO_INCREMENT NOT NULL',
            'title'=>'varchar(255) NOT NULL',
            'description'=>'text NOT NULL',
            'defaults'=>"int(1) DEFAULT '0'",
            'ordering'=>"int(11) DEFAULT '0'",
            'param'=>"text NOT NULL",
            'PRIMARY KEY (`id_des`)'
        ]
    ],
    'gadgets'=>[
        'columns'=>[
            'id_gad'=>'int(11) AUTO_INCREMENT NOT NULL',
            'id_des'=>'int(11) NOT NULL',
            'name'=>'varchar(255) NOT NULL',
            'title'=>'varchar(255) DEFAULT NULL',
            'colonum'=>"int(11) DEFAULT '0'",
            'ordering'=>"int(11) DEFAULT '0'",
            'param'=>'text NOT NULL',
            'PRIMARY KEY (`id_gad`)',
            'FOREIGN KEY (`id_des`) REFERENCES  {{%desktop}} (`id_des`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'grids'=>[
        'columns'=>[
            'grid_id'=>'varchar(200) NOT NULL',
            'id_user'=>'int(11) NOT NULL',
            'sortfild'=>'varchar(100) NOT NULL',
            'sortorder'=>'varchar(50) NOT NULL',
            'page'=>'int(11) NOT NULL',
            'rowNum'=>'int(11) NOT NULL',
            'colHide'=>'text NOT NULL',
            'sortCol'=>'text NOT NULL',
            'groupField'=>'text NOT NULL',
            'date_created'=>"datetime NOT NULL",
            'PRIMARY KEY (`grid_id`)',
            'FOREIGN KEY (`id_user`) REFERENCES  {{%users}} (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'filters'=>[
        'columns'=>[
            'filter_id'=>'int(11) AUTO_INCREMENT NOT NULL',
            'id_user'=>'int(11) NOT NULL',
            'code'=>'varchar(100) NOT NULL',
            'component'=>'varchar(255) NOT NULL',
            'title'=>'varchar(255) NOT NULL',
            'enabled'=>"tinyint(1) DEFAULT '0'",
            'defaults'=>"tinyint(1) DEFAULT '0'",
            'sort'=>'int(11) DEFAULT NULL',
            'create_date'=>'datetime NOT NULL',
            'PRIMARY KEY (`filter_id`)',
            'FOREIGN KEY (`id_user`) REFERENCES  {{%users}} (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'filters_fields'=>[
        'columns'=>[
            'filter_id'=>'int(11) AUTO_INCREMENT NOT NULL',
            'field'=>'varchar(100) NOT NULL',
            'visible'=>"tinyint(1) DEFAULT '0'",
            'datavalue'=>'text',
            'PRIMARY KEY (`filter_id`,`field`)',
            'FOREIGN KEY (`filter_id`) REFERENCES  {{%filters}} (`filter_id`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'elfinder_profile'=>[
        'columns'=>[
            'profile_id'=>'int(11) AUTO_INCREMENT NOT NULL',
            'title'=>'varchar(255) NOT NULL',
            'sort'=>'int(11) NOT NULL',
            'enabled'=>"tinyint(1) NOT NULL DEFAULT '0'",
            'cssClass'=>'varchar(100) DEFAULT NULL',
            'rememberLastDir'=>"tinyint(1) DEFAULT '0'",
            'useBrowserHistory'=>"tinyint(1) DEFAULT '0'",
            'resizable'=>"tinyint(1) DEFAULT '0'",
            'notifyDelay'=>'int(11) NOT NULL',
            'loadTmbs'=>'int(11) NOT NULL',
            'showFiles'=>'int(11) NOT NULL',
            'validName'=>'varchar(255) NOT NULL',
            'requestType'=>'varchar(20) NOT NULL',
            'createDate'=>'datetime NOT NULL',
            'commands'=>'text',
            'ui'=>'text',
            'toolbar'=>'text',
            'navbar'=>'text',
            'cwd'=>"text",
            'files'=>'text',
            'PRIMARY KEY (`profile_id`)'
        ]
    ],
    'elfinder_role'=>[
        'columns'=>[
            'id_role'=>'int(11) NOT NULL',
            'profile_id'=>'int(11) NOT NULL',
            'PRIMARY KEY (`profile_id`,`id_role`)',
            'FOREIGN KEY (`id_role`) REFERENCES  {{%roles}} (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (`profile_id`) REFERENCES  {{%elfinder_profile}} (`profile_id`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'elfinder_dir'=>[
        'columns'=>[
            'path_id'=>'int(11) AUTO_INCREMENT NOT NULL',
            'profile_id'=>'int(11) NOT NULL',
            'sort'=>'int(11) NOT NULL',
            'path'=>'text',
            'alias'=>'varchar(255) NOT NULL',
            'uploadMaxSize'=>'varchar(255) NOT NULL',
            'acceptedName'=>'varchar(255) NOT NULL',
            'PRIMARY KEY (`path_id`)',
            'FOREIGN KEY (`profile_id`) REFERENCES  {{%elfinder_profile}} (`profile_id`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'elfinder_attributes'=>[
        'columns'=>[
            'id'=>'int(11) AUTO_INCREMENT NOT NULL',
            'path_id'=>'int(11) NOT NULL',
            'pattern'=>'varchar(255) NOT NULL',
            'read'=>"tinyint(1) DEFAULT '0'",
            'write'=>"tinyint(1) DEFAULT '0'",
            'hidden'=>"tinyint(1) DEFAULT '0'",
            'locked'=>"tinyint(1) DEFAULT '0'",
            'PRIMARY KEY (`id`)',
            'FOREIGN KEY (`path_id`) REFERENCES  {{%elfinder_dir}} (`path_id`) ON DELETE CASCADE ON UPDATE CASCADE'
            
        ]
    ],
    'elfinder_uploadallow'=>[
        'columns'=>[
            'id'=>'int(11) AUTO_INCREMENT NOT NULL',
            'path_id'=>'int(11) NOT NULL',
            'mimetypes'=>'varchar(255) NOT NULL',
            'PRIMARY KEY (`id`)',
            'FOREIGN KEY (`path_id`) REFERENCES  {{%elfinder_dir}} (`path_id`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'content_type'=>[
        'columns'=>[
            'bundle'=>'varchar(128) NOT NULL',
            'expansion'=>'varchar(255) NOT NULL',
            'title'=>'varchar(255) NOT NULL',
            'description'=>'text',
            'param'=>'text',
            'date_create'=>'datetime DEFAULT NULL',
            'PRIMARY KEY (`bundle`,`expansion`)'            
        ]
    ],
    'fields'=>[
        'columns'=>[
            'field_id'=>'int(11) AUTO_INCREMENT NOT NULL',
            'field_name'=>'varchar(53) NOT NULL',
            'type'=>'varchar(128) NOT NULL',
            'date_create'=>'datetime DEFAULT NULL',
            'PRIMARY KEY (`field_id`)'
        ]
    ],
    'field_exp'=>[
        'columns'=>[
            'field_exp_id'=>'int(11) AUTO_INCREMENT NOT NULL',
            'field_id'=>'int(11) NOT NULL',
            'field_name'=>'varchar(53) NOT NULL',
            'title'=>'varchar(255) NOT NULL',
            'prompt'=>'text',
            'expansion'=>'varchar(255) NOT NULL',
            'bundle'=>'varchar(128) NOT NULL',
            'locked'=>"tinyint(1) DEFAULT '0'",
            'active'=>"tinyint(1) DEFAULT '0'",
            'sort'=>'int(11) DEFAULT NULL',
            'many_value'=>"tinyint(10) DEFAULT '0'",
            'widget_name'=>'varchar(53) NOT NULL',
            'param'=>'text',
            'widget_param'=>'text',
            'PRIMARY KEY (`field_exp_id`)',
            'FOREIGN KEY (`field_id`) REFERENCES  {{%fields}} (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (`bundle`, `expansion`) REFERENCES  {{%content_type}} (`bundle`, `expansion`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'contents'=>[
       'columns'=>[
           'contents_id'=>'int(11) AUTO_INCREMENT NOT NULL',
           'expansion'=>'varchar(255) NOT NULL',
           'bundle'=>'varchar(128) NOT NULL',
           'routes_id'=>'int(11) NOT NULL',
           'enabled'=>'tinyint(1) NOT NULL',
           'home'=>'tinyint(1) NOT NULL',
           'sort'=>'int(11) DEFAULT NULL',
           'id_lang'=>'int(11) DEFAULT NULL',
           'id_user'=>'int(11) DEFAULT NULL',
           'time_active'=>'datetime DEFAULT NULL',
           'time_inactive'=>'datetime DEFAULT NULL',
           'date_create'=>'datetime DEFAULT NULL',
           'PRIMARY KEY (`contents_id`)',
           'FOREIGN KEY (`routes_id`) REFERENCES  {{%routes}} (`routes_id`) ON DELETE CASCADE ON UPDATE CASCADE',
           'FOREIGN KEY (`bundle`, `expansion`) REFERENCES  {{%content_type}} (`bundle`, `expansion`) ON DELETE CASCADE ON UPDATE CASCADE',
           'FOREIGN KEY (`id_user`) REFERENCES  {{%users}} (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE'
       ] 
    ],
    'contents_home'=>[
        'columns'=>[
            'contents_id'=>"int(11) NOT NULL DEFAULT '0'",
            'expansion'=>'varchar(255) NOT NULL',
            'sort'=>'int(11) DEFAULT NULL',
            'PRIMARY KEY (`contents_id`)',
            'FOREIGN KEY (`contents_id`) REFERENCES  {{%contents}} (`contents_id`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'content_type_view'=>[
        'columns'=>[
            'view_name'=>"varchar(64) NOT NULL DEFAULT ''",
            'expansion'=>'varchar(255) NOT NULL',
            'bundle'=>'varchar(128) NOT NULL',
            'entry_type'=>'varchar(255) NOT NULL',
            'enabled'=>"tinyint(1) DEFAULT '0'",
            'group_name'=>'varchar(64) DEFAULT NULL',
            'sort'=>'int(11) DEFAULT NULL',
            'field_exp_id'=>'int(11) DEFAULT NULL',
            'mode'=>"tinyint(1) DEFAULT '0'",
            'show_label'=>"tinyint(1) DEFAULT '0'",
            'class_wrapper'=>'varchar(255) DEFAULT NULL',
            'tag_wrapper'=>'varchar(10) NOT NULL',
            'multiple_size'=>'int(11) DEFAULT NULL',
            'multiple_start'=>'int(11) DEFAULT NULL',
            'field_view'=>'varchar(53) DEFAULT NULL',
            'field_view_param'=>'longtext',
            'PRIMARY KEY (`view_name`)',
            'FOREIGN KEY (`field_exp_id`) REFERENCES  {{%field_exp}} (`field_exp_id`) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (`bundle`, `expansion`) REFERENCES  {{%content_type}} (`bundle`, `expansion`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ],
    'dictionary_term'=>[
       'columns'=>[
           'term_id'=>'int(11) AUTO_INCREMENT NOT NULL',
           'parent'=>'int(11) DEFAULT NULL',
           'routes_id'=>'int(11) NOT NULL',
           'expansion'=>'varchar(255) NOT NULL',
           'bundle'=>'varchar(128) NOT NULL',
           'sort'=>'int(11) DEFAULT NULL',
           'enabled'=>"tinyint(1) NOT NULL DEFAULT '0'",
           'id_lang'=>'int(11) DEFAULT NULL',
           'time_active'=>'datetime DEFAULT NULL',
           'time_inactive'=>'datetime DEFAULT NULL',
           'date_create'=>'datetime DEFAULT NULL',
           'PRIMARY KEY (`term_id`)',
           'FOREIGN KEY (`bundle`, `expansion`) REFERENCES  {{%content_type}} (`bundle`, `expansion`) ON DELETE CASCADE ON UPDATE CASCADE',
           'FOREIGN KEY (`routes_id`) REFERENCES  {{%routes}} (`routes_id`) ON DELETE CASCADE ON UPDATE CASCADE'
       ]
    ],
    'content_term_sort'=>[
        'columns'=>[
            'contents_id'=>'int(11) NOT NULL',
            'term_id'=>'int(11) NOT NULL',
            'sort'=>'int(11) NOT NULL',
            'PRIMARY KEY (`contents_id`,`term_id`)',
            'FOREIGN KEY (`contents_id`) REFERENCES  {{%contents}} (`contents_id`) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (`term_id`) REFERENCES  {{%dictionary_term}} (`term_id`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]
    ]
];