<?php

namespace root\profiles\project\defaults;

use RC;
use maze\db\Connection;
use Installer;
use maze\helpers\FileHelper;
use Text;

class LoadExt extends \maze\base\Model {

    public $constructorblock;
    public $block;
    public $diskspace;
    public $onlineuser;
    public $debugadmin;
    public $replaceterm;
    public $seoredirect;
    public $compressor;
    public $maps;

    public function rules() {
        return [
            [['constructorblock', 'block', 'diskspace', 'onlineuser', 'debugadmin', 'compressor', 'seoredirect', 'replaceterm'], 'boolean', "message" => "Поле ({attribute}) является обязательным"],
            [['constructorblock', 'block', 'maps'], 'validateRequired']
        ];
    }
    
    public function validateRequired($attribute, $param) {
        if ($this->$attribute != 1) {
                $this->addError($attribute, Text::_("Модуль ({attribute}) является обязательным", ['attribute'=>  $this->getAttributeLabel($attribute)]));
        }
    }

    public function action($db) {
        $response = ['message' => 'Установка  расширений успешно завершена', 'resultCode' => 1];

        try {
            $ext = scandir(RC::getAlias('@root/profiles/modules'));
            foreach ($ext as $name) {
                if (mb_strpos($name, '.zip') !== false) {
                    $zip = new \ZipArchive;
                    if ($zip->open(RC::getAlias('@root/profiles/modules/' . $name)) === TRUE) {
                        $zip->extractTo(RC::getAlias('@root/profiles/modules'));
                        $zip->close();
                    }
                }
            }

            if ($this->constructorblock) {
                FileHelper::remove(RC::getAlias("@root/admin/expansion/exp_constructorblock"));
                $installer = Installer::instance([
                            'dir' => '@root/profiles/modules',
                            'mode' => Installer::INSTALL,
                            'type' => 'expansion',
                            'name' => 'constructorblock'
                ]);

                $steps = $installer->getCommands();

                foreach ($steps as $name => $title) {
                    $app = $installer->exec($name);
                    if ($app->hasErrors()) {
                        $errors = $app->getErrors();
                        $errors = array_map(function($val) {
                            return implode('<br>', $val);
                        }, $errors);
                        $response = ['message' => implode('<br>', $errors), 'resultCode' => 0];
                        break;
                    }
                }
            }

            if ($this->block) {
                FileHelper::remove(RC::getAlias("@root/widgets/wid_block"));
                $installer = Installer::instance([
                            'dir' => '@root/profiles/modules',
                            'mode' => Installer::INSTALL,
                            'type' => 'widget',
                            'name' => 'block'
                ]);

                $steps = $installer->getCommands();

                foreach ($steps as $name => $title) {
                    $app = $installer->exec($name);
                    if ($app->hasErrors()) {
                        $errors = $app->getErrors();
                        $errors = array_map(function($val) {
                            return implode('<br>', $val);
                        }, $errors);
                        $response = ['message' => implode('<br>', $errors), 'resultCode' => 0];
                        break;
                    }
                }
            }
            
            if ($this->maps) {
                FileHelper::remove(RC::getAlias("@root/widgets/wid_maps"));
                $installer = Installer::instance([
                            'dir' => '@root/profiles/modules',
                            'mode' => Installer::INSTALL,
                            'type' => 'widget',
                            'name' => 'maps'
                ]);

                $steps = $installer->getCommands();

                foreach ($steps as $name => $title) {
                    $app = $installer->exec($name);
                    if ($app->hasErrors()) {
                        $errors = $app->getErrors();
                        $errors = array_map(function($val) {
                            return implode('<br>', $val);
                        }, $errors);
                        $response = ['message' => implode('<br>', $errors), 'resultCode' => 0];
                        break;
                    }
                }
            }


            if ($this->diskspace) {
                FileHelper::remove(RC::getAlias("@root/admin/gadgets/gad_diskspace"));
                $installer = Installer::instance([
                            'dir' => '@root/profiles/modules',
                            'mode' => Installer::INSTALL,
                            'type' => 'gadget',
                            'name' => 'diskspace'
                ]);

                $steps = $installer->getCommands();

                foreach ($steps as $name => $title) {
                    $app = $installer->exec($name);
                    if ($app->hasErrors()) {
                        $errors = $app->getErrors();
                        $errors = array_map(function($val) {
                            return implode('<br>', $val);
                        }, $errors);
                        $response = ['message' => implode('<br>', $errors), 'resultCode' => 0];
                        break;
                    }
                }
            }

            if ($this->onlineuser) {
                FileHelper::remove(RC::getAlias("@root/admin/gadgets/gad_onlineuser"));
                $installer = Installer::instance([
                            'dir' => '@root/profiles/modules',
                            'mode' => Installer::INSTALL,
                            'type' => 'gadget',
                            'name' => 'onlineuser'
                ]);

                $steps = $installer->getCommands();

                foreach ($steps as $name => $title) {
                    $app = $installer->exec($name);
                    if ($app->hasErrors()) {
                        $errors = $app->getErrors();
                        $errors = array_map(function($val) {
                            return implode('<br>', $val);
                        }, $errors);
                        $response = ['message' => implode('<br>', $errors), 'resultCode' => 0];
                        break;
                    }
                }
            }

            if ($this->debugadmin) {
                FileHelper::remove(RC::getAlias("@root/plugins/system/debugadmin"));
                $installer = Installer::instance([
                            'dir' => '@root/profiles/modules',
                            'mode' => Installer::INSTALL,
                            'type' => 'plugin',
                            'group' => 'system',
                            'name' => 'debugadmin'
                ]);

                $steps = $installer->getCommands();

                foreach ($steps as $name => $title) {
                    $app = $installer->exec($name);
                    if ($app->hasErrors()) {
                        $errors = $app->getErrors();
                        $errors = array_map(function($val) {
                            return implode('<br>', $val);
                        }, $errors);
                        $response = ['message' => implode('<br>', $errors), 'resultCode' => 0];
                        break;
                    }
                }
            }

            if ($this->replaceterm) {
                FileHelper::remove(RC::getAlias("@root/plugins/system/replaceterm"));
                $installer = Installer::instance([
                            'dir' => '@root/profiles/modules',
                            'mode' => Installer::INSTALL,
                            'type' => 'plugin',
                            'group' => 'system',
                            'name' => 'replaceterm'
                ]);

                $steps = $installer->getCommands();

                foreach ($steps as $name => $title) {
                    $app = $installer->exec($name);
                    if ($app->hasErrors()) {
                        $errors = $app->getErrors();
                        $errors = array_map(function($val) {
                            return implode('<br>', $val);
                        }, $errors);
                        $response = ['message' => implode('<br>', $errors), 'resultCode' => 0];
                        break;
                    }
                }
            }

            if ($this->seoredirect) {
                FileHelper::remove(RC::getAlias("@root/plugins/system/seoredirect"));
                $installer = Installer::instance([
                            'dir' => '@root/profiles/modules',
                            'mode' => Installer::INSTALL,
                            'type' => 'plugin',
                            'group' => 'system',
                            'name' => 'seoredirect'
                ]);

                $steps = $installer->getCommands();

                foreach ($steps as $name => $title) {
                    $app = $installer->exec($name);
                    if ($app->hasErrors()) {
                        $errors = $app->getErrors();
                        $errors = array_map(function($val) {
                            return implode('<br>', $val);
                        }, $errors);
                        $response = ['message' => implode('<br>', $errors), 'resultCode' => 0];
                        break;
                    }
                }
            }
            
            if ($this->compressor) {
                FileHelper::remove(RC::getAlias("@root/plugins/system/compressor"));
                $installer = Installer::instance([
                            'dir' => '@root/profiles/modules',
                            'mode' => Installer::INSTALL,
                            'type' => 'plugin',
                            'group' => 'system',
                            'name' => 'compressor'
                ]);

                $steps = $installer->getCommands();

                foreach ($steps as $name => $title) {
                    $app = $installer->exec($name);
                    if ($app->hasErrors()) {
                        $errors = $app->getErrors();
                        $errors = array_map(function($val) {
                            return implode('<br>', $val);
                        }, $errors);
                        $response = ['message' => implode('<br>', $errors), 'resultCode' => 0];
                        break;
                    }
                }
            }
        } catch (\Exception $ex) {
            $response = ['message' => $ex->getMessage(), 'resultCode' => 0];
        }

        return $response;
    }

    public function attributeLabels() {
        return[
            "constructorblock" => "Компанента - Конструктор блоков",
            "block" => "Виджет - Блоки контента (Этот виджет отображает содержимое)",
            "diskspace" => "Гаджет - Пространство на диске",
            "onlineuser" => "Гаджет - Кто на сайте",
            "replaceterm" => "Плагин - подставнока термина словаря поддомена",
            "seoredirect" => "Плагин - SEO переадресация",
            "compressor" => "Плагин - Компрессор сайта",
            "maps" => "Карта - схема проезда"
        ];
    }

}
