<?php

defined('_CHECK_') or die("Access denied");

use maze\table\Privates;
use ui\grid\GridFormat;

class User_View_Role extends View {

    public function registry() {
        $model = $this->get('modelForm');
        $title = $model->id_role ? Text::_("EXP_USER_TITLEMENU_EDIT") : Text::_("EXP_USER_TITLEMENU_CREATE");
        RC::app()->breadcrumbs = ['label' => 'EXP_USER_ROLE_MENU_TITLE', 'url' => ['/admin/user/role']];
        RC::app()->breadcrumbs = ['label' => $title];

        $private = Privates::find()->orderBy('exp_name')->all();
        $result = (new GridFormat([
            'id' => 'role-form-grid',
            'model' => $private,
            'colonum' => 'exp_name',
            'rowNum' => count($private),
            'colonumData' => [
                'id' => '$data->id_priv',
                'name',
                'title' => function($data) {
                    return Text::_($data->title);
                },
                'description' => function($data) {
                    return Text::_($data->description);
                },
                'exp_name' => function($data) {
                    if ($data->exp_name == 'system') {
                        return 'Системные';
                    }
                    $info = \RC::getConf(["type" => "expansion", "name" => $data->exp_name]);
                    return $info->get("name") ? $info->get("name") : $data->exp_name;
                }
                    ]
                        ]))->data['data'];

                $this->set('private', $result);
                $toolbar = RC::app()->toolbar;

                $toolbar->addGroup('user', [
                    'class' => 'Buttonset',
                    "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
                    "TYPE" => Buttonset::BTNBIG,
                    "SORT" => 10,
                    "VISIBLE" => $this->_access->roles("menu", "EDIT_ITEM"),
                    "SORTGROUP" => 10,
                    "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
                    "ACTION" => "return cms.btnFormAction('#role-form')",
                    "MENU" => [
                        [
                            "class" => 'ContextMenu',
                            "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                            "SORT" => 2,
                            "ACTION" => "return cms.btnFormAction('#role-form')"
                        ],
                        [
                            "class" => 'ContextMenu',
                            "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                            "SORT" => 1,
                            "ACTION" => "return cms.btnFormAction('#role-form', {action:'saveClose'})"
                        ]
                    ]
                ]);

                $toolbar->addGroup('user', [
                    'class' => 'Buttonset',
                    "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
                    "TYPE" => Buttonset::BTNBIG,
                    "SORT" => 8,
                    "SORTGROUP" => 10,
                    "HREF" => [['run' => 'close']],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
                    "ACTION" => "return cms.redirect(this.href)"
                ]);
            }

        }

?>