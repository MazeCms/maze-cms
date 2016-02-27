<?php

namespace tmp\defaults\helpers;

use Text;
use RC;
use ToolBarelem;

class ToolBare {

    public static function getToolbarContents($contents_id, $title, $bundle) {
        $access = RC::app()->access;
        $id = 'admin-edits-contents-panel-' . $contents_id;
        $toolbar = \maze\toolbarsite\ToolbarBuilder::begin([
                    'options' => ['id' => $id],
                    'private' => ['contents' => 'EDIT_CONTENTS'],
                    'buttons' => [
                        [
                            'class' => 'Buttonset',
                            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_EDIT_ONER",
                            "SORT" => 4,
                            "SRC" => "/library/jquery/toolbarsite/images/icon-edit.png",
                            "HREF" => ['/admin/contents', ['run' => 'edit', 'contents_id' => $contents_id, 'clear' => 'ajax']],
                            "ACTION" => "cms.formDialogSave(this,{title:'" . Text::_("LIB_USERINTERFACE_TOOLBAR_EDIT_NAME", ['name' => $title]) . "'}); return false;",
                            "MENU" => [
                                [
                                    'class' => 'ContextMenu',
                                    "TITLE" => "LIB_USERINTERFACE_TOOLBAR_WINDOW_NEW",
                                    "SORT" => 1,
                                    "HREF" => ['/admin/contents', ['run' => 'edit', 'contents_id' => $contents_id]],
                                    "ACTION" => "window.open(this.href); return false;"
                                ],
                                [
                                    'class' => 'ContextMenu',
                                    "TITLE" => "LIB_USERINTERFACE_TOOLBAR_WINDOW_SELF",
                                    "SORT" => 1,
                                    "HREF" => ['/admin/contents', ['run' => 'edit', 'contents_id' => $contents_id]],
                                ]
                            ]
                        ],
                        [
                            'class' => 'Buttonset',
                            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_DELET_BUTTON",
                            "SORT" => 1,
                            "VISIBLE" => ($access->roles("contents", "DELETE_CONTENTS") || $access->roles("contents", "DELETE_SELF_CONTENTS", null, ['contents_id' => $contents_id])),
                            "SRC" => "/library/jquery/toolbarsite/images/icon-trash.png",
                            "HREF" => ['/admin/contents', ['run' => 'delete', 'contents_id' => [$contents_id]]],
                            "ACTION" => "cms.deleteBlock(this, '#" . $id . "'); return false;"
                        ],
                        [
                            'class' => 'Buttonset',
                            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_UNPUBLISH_BUTTON",
                            "SORT" => 2,
                            "SRC" => "/library/jquery/toolbarsite/images/icon-close.png",
                            "HREF" => ['/admin/contents', ['run' => 'unpublish', 'contents_id' => [$contents_id]]],
                            "ACTION" => "cms.deleteBlock(this, '#" . $id . "'); return false;"
                        ],
                        [
                            'class' => 'Buttonset',
                            "TITLE" => "Настроить",
                            "SORT" => -1,
                            "VISIBLE" => ($access->roles("contents", "EDIT_VIEW_CONTENTS") || $access->roles("contents", "EDIT_FIELD_CONTENTS")),
                            "SRC" => "/library/jquery/toolbarsite/images/icon-settings.png",
                            "MENU" => [
                                [
                                    'class' => 'ContextMenu',
                                    "TITLE" => "EXP_CONTENTS_FIELD",
                                    "SORT" => 1,
                                    "VISIBLE" => $access->roles("contents", "EDIT_FIELD_CONTENTS"),
                                    "HREF" => ["/admin/contents/field", ["run" => "field", "bundle" => trim($bundle)]],
                                    "ACTION" => "window.open(this.href); return false;"
                                ]
                            ]
                        ],
                    ]
        ]);


        return $toolbar;
    }

}
