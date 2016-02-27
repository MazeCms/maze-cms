<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace maze\helpers;

use RC;
use lib\imagine\Image;

/**
 * Html provides a set of static methods for generating commonly used HTML tags.
 *
 * Nearly all of the methods in this class allow setting additional html attributes for the html
 * tags they generate. You can specify for example. 'class', 'style'  or 'id' for an html element
 * using the `$options` parameter. See the documentation of the [[tag()]] method for more details.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Html extends BaseHtml {

    public static function imgThumb($path, $width, $height, $options = []) {
        if (empty($path))
            return '';
        $filePath = RC::getAlias($path);
        if (file_exists($filePath)) {
            if (!is_dir(RC::getAlias('@root/images/thumb'))) {
                mkdir(RC::getAlias('@root/images/thumb'), 0777, true);
            }
            $pathinfo = pathinfo($filePath);
            $basename = sprintf('%x', crc32($filePath)) . '_' . $width . 'x' . $height . '.' . $pathinfo["extension"];
            $pathThumb = RC::getAlias('@root/images/thumb/' . $basename);
            if (!file_exists($pathThumb)) {
                Image::thumbnail($filePath, $width, $height)->save($pathThumb);
            }

            if (file_exists($pathThumb)) {
                return static::img('/images/thumb/' . $basename, $options);
            }
        }

        return '';
    }

    public static function pathThumb($path, $width, $height) {
        if (empty($path))
            return '';
        $filePath = RC::getAlias($path);
        if (file_exists($filePath)) {
            if (!is_dir(RC::getAlias('@root/images/thumb'))) {
                mkdir(RC::getAlias('@root/images/thumb'), 0777, true);
            }
            $pathinfo = pathinfo($filePath);
            $basename = sprintf('%x', crc32($filePath)) . '_' . $width . 'x' . $height . '.' . $pathinfo["extension"];
            $pathThumb = RC::getAlias('@root/images/thumb/' . $basename);
            if (!file_exists($pathThumb)) {
                Image::thumbnail($filePath, $width, $height)->save($pathThumb);
            }

            if (file_exists($pathThumb)) {
                return '/images/thumb/' . $basename;
            }
        }

        return '';
    }

}
