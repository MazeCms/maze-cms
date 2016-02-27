<?php

namespace exp\exp_templating\model;

use maze\helpers\ArrayHelper;
use maze\table\Template;
use maze\table\Menu;
use maze\table\Expansion;
use maze\table\InstallApp;
use maze\table\Widgets;

class Templating extends \maze\base\Model {

    public function home($id_tmp) {
        $transaction = \RC::getDb()->beginTransaction();
        try {
            $tmp = Template::findOne($id_tmp);
            if ($tmp) {
                Template::updateAll(['home' => 0], ['front' => $tmp->front]);
                $tmp->home = 1;
                if (!$tmp->save()) {
                    throw new \Exception();
                }
            } else {
                throw new \Exception();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    public function saveStyle($form) {
        $transaction = \RC::getDb()->beginTransaction();
        try {
            if (!$form->validate()) {
                throw new \Exception();
            }

            if ($form->id_tmp) {
                $tmp = Template::findOne($form->id_tmp);
            } else {
                $tmp = new Template();
            }
            if ($form->home) {
                Template::updateAll(['home' => 0], ['front' => $form->front]);
            }
            $tmp->attributes = $form->attributes;
            if (!$tmp->save()) {
                $form->addError('id_tmp', \Text::_('EXP_TEMPLATING_CONT_ERRSAVE_MESS_YES'));
                throw new \Exception();
            }
            Menu::updateAll(['id_tmp' => null], ['id_tmp' => $tmp->id_tmp]);
            Expansion::updateAll(['id_tmp' => null], ['id_tmp' => $tmp->id_tmp]);
            if ($form->id_menu) {
                $menu = Menu::findAll(['id_menu' => $form->id_menu]);
                foreach ($menu as $m) {
                    $m->id_tmp = $tmp->id_tmp;
                    $m->save();
                }
            }
            if ($form->id_exp) {
                $exp = Expansion::findAll(['id_exp' => $form->id_exp]);
                foreach ($exp as $e) {
                    $e->id_tmp = $tmp->id_tmp;
                    $e->save();
                }
            }
            $form->id_tmp = $tmp->id_tmp;
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    public function getTmp($id) {
        return InstallApp::find()->where(['type' => 'template', 'id_app' => $id])->one();
    }

    public function getRootPath($id) {
        $model = $this->getTmp($id);
        if (!$model)
            return;
        $root = $model->front_back ? PATH_ROOT : PATH_SITE;

        $path = $root . DS . "templates" . DS . $model->name;

        if (!is_dir($path))
            return null;
        return $path;
    }

    public function getDataTree($id) {
        $model = $this->getTmp($id);
        if (!$model)
            return;
        $root = $model->front_back ? PATH_ROOT : PATH_SITE;

        $path = $root . DS . "templates" . DS . $model->name;

        if (!is_dir($path))
            return null;

        return ['text' => $model->name, 'type' => 'root', 'children' => $this->getReadDir($path, $path), 'a_attr' => ['href' => '/']];
    }

    public function getReadDir($path, $root) {
        $dir = opendir($path);

        $result = [];

        while (($file = readdir($dir)) !== false) {

            $href = str_replace($root, '', $path . DS . $file);
            if (is_file($path . DS . $file)) {
                $help = '';
                if ($file == "tmp.index.php") {
                    $help = \Text::_("EXP_TEMPLATING_TMP_FORM_SUBMENU_ONETMP");
                } else if ($file == "tmp.error.php") {
                    $help = \Text::_("EXP_TEMPLATING_TMP_FORM_SUBMENU_ERRTMP");
                } else if ($file == "meta.options.php") {
                    $help = \Text::_("EXP_TEMPLATING_TMP_FORM_SUBMENU_SETTINGSTMP");
                }
                //
                $result[] = ['text' => $file, 'type' => $this->getTypeFile($file), 'a_attr' => ['title' => $help, 'href' => $file]];
            } else if (is_dir($path . DS . $file) && $file !== "." && $file !== "..") {
                $help = '';
                if ($file == "views") {
                    $help = \Text::_("EXP_TEMPLATING_TMP_FORM_SUBMENU_RELOADTMP");
                } else if ($file == "language") {
                    $help = \Text::_("EXP_TEMPLATING_TMP_FORM_SUBMENU_LANGFILE");
                }
                //
                $folder = ['text' => $file, 'type' => 'folder', 'a_attr' => ['href' => $file, 'title' => $help]];

                $files = $this->getReadDir($path . DS . $file, $root);
                if (!empty($files)) {
                    $folder['children'] = $files;
                }
                $result[] = $folder;
            }
        }
        usort($result, function($a, $b) {
            return $a['type'] == 'folder' ? -1 : 1;
        });
        return $result;
    }

    public function getTypeFile($filename) {
        $ext = preg_replace("#.+(\.[\w]{1,9})#is", "$1", $filename); // текущее расширение файла			
        $type = "";

        // проверка является ли текущий файл удолетворяет разрешенным расширения		 
        $extentions["img"] = array(
            "#\.jpg#is",
            "#\.jpeg#is",
            "#\.png#is",
            "#\.gif#is",
            "#\.bpm#is",
            "#\.svg#is",
            "#\.ico#is",
            "#\.tga#is",
            "#\.tif#is"
        );
        $extentions["php"] = array(
            "#\.php#is",
            "#\.inc#is",
            "#\.pl#is",
            "#\.html#is"
        );
        $extentions["js"] = array(
            "#\.js#is"
        );
        $extentions["pdf"] = array(
            "#\.pdf#is"
        );
        $extentions["zip"] = array(
            "#\.zip#is",
            "#\.rar#is",
            "#\.7z#is",
            "#\.arj#is",
            "#\.cab#is",
            "#\.exe#is",
            "#\.gz#is",
            "#\.gzip#is",
            "#\.jar#is",
            "#\.pak#is",
            "#\.one#is",
            "#\.ppt#is",
            "#\.spl#is",
            "#\.tar#is",
            "#\.tar-gz#is",
            "#\.tgz#is",
            "#\.zipx#is"
        );
        $extentions["media"] = array(
            "#\.mp3#is",
            "#\.wma#is",
            "#\.wav#is",
            "#\.aa#is",
            "#\.amr#is",
            "#\.ape#is",
            "#\.flac#is",
            "#\.swf#is",
            "#\.mp4#is",
            "#\.mov#is",
            "#\.wmv#is",
            "#\.flv#is",
            "#\.vob#is",
            "#\.3gp#is",
            "#\.avi#is"
        );
        $extentions["office"] = array(
            "#\.doc#is",
            "#\.docx#is",
            "#\.txt#is",
            "#\.xls#is",
            "#\.xlsm#is",
            "#\.odt#is",
            "#\.ppt#is",
            "#\.sxw#is",
            "#\.sxg#is",
            "#\.sxm#is",
            "#\.sxi#is",
            "#\.sxc#is",
            "#\.vob#is",
            "#\.3gp#is",
            "#\.avi#is"
        );
        $extentions["css"] = array(
            "#\.css#is"
        );
        $extentions["lang"] = array(
            "#\.ini#is"
        );

        foreach ($extentions as $key => $exten) {
            foreach ($exten as $ext_preg) {
                if (preg_match($ext_preg, "." . $ext)) {
                    return $key;
                    break;
                }
            }
        }


        return $type;
    }

    public function delete($id_tmp) {
        $transaction = \RC::getDb()->beginTransaction();
        try {
            $tmp = Template::findAll(['id_tmp' => $id_tmp]);
            foreach ($tmp as $t) {
                if ($t->home != 1) {
                    Widgets::deleteAll(['id_tmp'=>$t->id_tmp]);
                    $t->delete();
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            \RC::app()->setError($e);
            return false;
        }
        return true;
    }

}
