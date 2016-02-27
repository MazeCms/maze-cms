<?php
namespace lib\fields\images;

use RC;
use Text;
use maze\base\Model;

class FieldForm extends Model{
   
    /**
     * @var int - поле является обязательным
     */
    public $required = 1;
    
    /**
     * @var string - изображение по умолчанию
     */
    public $pathDefault;

    /**
     * @var integer the minimum width in pixels.
     * Defaults to null, meaning no limit.
     * @see underWidth
     */
    public $minWidth = 800;
    /**
     * @var integer the maximum width in pixels.
     * Defaults to null, meaning no limit.
     * @see overWidth
     */
    public $maxWidth = 3000;
    /**
     * @var integer the minimum height in pixels.
     * Defaults to null, meaning no limit.
     * @see underHeight
     */
    public $minHeight = 400;
    /**
     * @var integer the maximum width in pixels.
     * Defaults to null, meaning no limit.
     * @see overWidth
     */
    public $maxHeight = 2000;
    
     /**
     * @var array|string a list of file name extensions that are allowed to be uploaded.
     * This can be either an array or a string consisting of file extension names
     * separated by space or comma (e.g. "gif, jpg").
     * Extension names are case-insensitive. Defaults to null, meaning all file name
     * extensions are allowed.
     * @see wrongType
     */
    public $types = "jpg, png, jpeg";

    public function rules() {
      return [
           [['required', 'types', 'minWidth', 'maxWidth', 'minHeight', 'maxHeight'], 'required'],
           [['required'], 'boolean'],
           [['types', 'pathDefault'], 'string'],
           [['minWidth', 'maxWidth', 'minHeight', 'maxHeight'], 'number', 'min'=>1]
        ];
    }
    
    public function attributeLabels() {
        return[
            "required"=>Text::_("LIB_FIELDS_IMAGES_REQUIRED"),
            "pathDefault"=>Text::_("LIB_FIELDS_IMAGES_PATHDEFAULT"),
            "types"=>Text::_("LIB_FIELDS_IMAGES_TYPES"),
            "minWidth"=>Text::_("LIB_FIELDS_IMAGES_MINWIDTH"),
            "maxWidth"=>Text::_("LIB_FIELDS_IMAGES_MAXWIDTH"),
            "minHeight"=>Text::_("LIB_FIELDS_IMAGES_MINHEIGTH"),
            "maxHeight"=>Text::_("LIB_FIELDS_IMAGES_MAXHEIGTH")
        ];
    }
    
}
