<?php

namespace lib\imagine;

use RC;
use maze\helpers\Html;

/**
 * Image implements most commonly used image manipulation functions using the [Imagine library](http://imagine.readthedocs.org/).
 *
 * Example of use:
 *
 * ~~~php
 * // generate a thumbnail image
 * Image::thumbnail('@webroot/img/test-image.jpg', 120, 120)
 *     ->save(Yii::getAlias('@runtime/thumb-test-image.jpg'), ['quality' => 50]);
 * ~~~
 *
 */
RC::$aliases['@Imagine'] = PATH_LIBRARIES . DS . 'imagine';

class Image extends BaseImage {

    

}
