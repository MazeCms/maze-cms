<?php

namespace root\profiles\project\defaults;

use RC;
use maze\db\Connection;
use maze\helpers\FileHelper;
use Text;
use maze\database\SQLDumpFileSeparator;

class ThemeProfile extends \maze\base\Model {

    public $footer = "light";
    public $layoutstyle = "layoutstyle";
    public $stylecolor = "green";
    public $bg;
    public $logo;
    public $favicon;
    public $copyright = "© Copyright 2013 все права защищены.";
    public $portfoliocol = "four columns";
    public $portfoliopage = "half";
    public $bloglayout = "two";
    public $showdate = 1;
    public $showreadmore = 1;
    public $textreadmore = "Подробнее...";

    public function rules() {
        return [
            [['footer', 'layoutstyle', 'stylecolor', 'copyright'], 'required', "message" => "Поле ({attribute}) является обязательным"],
            [['showdate', 'showreadmore'], 'boolean'],
            [['portfoliocol', 'portfoliopage', 'bloglayout', 'textreadmore'], 'safe'],
        ];
    }

    public function action($db) {

        FileHelper::copy(RC::getAlias("@root/profiles/project/defaults/contents/images"), RC::getAlias("@root/images"), ['fileMode' => 0777, 'dirMode' => 0777]);

      
        $patrhFile = RC::getAlias("@root/profiles/project/defaults/sql/install.sql");

        if (file_exists($patrhFile)) {
            $query = (new SQLDumpFileSeparator($patrhFile))->getQuery();
            $transaction = $db->beginTransaction();
            if ($query && is_array($query)) {
                try {
                    foreach ($query as $i => $qu) {
                        try {
                            $db->createCommand($qu)->execute();
                        } catch (\Exception $e) {

                            throw new \Exception(Text::_("LIB_FRAMEWORK_INSTALL_BD_QUERY", ['query' => $e->getMessage()]));
                        }
                    }
                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    return ['message' => $e->getMessage().'('.$qu.')', 'resultCode' => 0];
                }
            }
        }

        $params = $this->attributes;

        $db->createCommand()->update('{{%template}}', ['param' => serialize($params)], ['id_tmp' => 2])->execute();

        return ['message' => 'Профиль успешно установлен', 'resultCode' => 1];
    }

    public function attributeLabels() {
        return[
            "footer" => "Тема оформления",
            "layoutstyle" => "Стиль центральной колонки",
            "stylecolor" => "Цветовая схема",
            "bg" => "Фоновое изображение",
            "logo" => "Логотип",
            "favicon" => "Фавикон",
            "copyright" => "Копирайт",
            "portfoliocol" => "Колонок в категории порфолио",
            "portfoliopage" => "Макет страницы работы",
            "bloglayout" => "Макет категории блога",
            "showdate" => "Показывать дату создания",
            "showreadmore" => "Показывать ссылку (читать подроднее)",
            "textreadmore" => "Текст ссылки (читать подроднее)"
        ];
    }

}
