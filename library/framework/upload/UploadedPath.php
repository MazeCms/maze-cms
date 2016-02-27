<?php

namespace maze\upload;

use RC;
use maze\base\Object;
use maze\helpers\Html;
use maze\helpers\FileHelper;

class UploadedPath extends Object {
    /*
     * Значение: 0; Ошибок не возникло, файл был успешно загружен на сервер.
     */

    const UPLOAD_ERR_OK = 0;

    /*
     * Значение: 3; Файл не был загружен.
     */
    const UPLOAD_ERR_NO_FILE = 1;
 

    /**
     * @var string - путь к загруженному файлу
     */
    public $name;

    /**
     * @var string - асоциированное название загружамего файла 
     */
    public $attribute;

    /**
     * @var string  MIME- тип загруженного файла (например "image/gif").
     */
    public $type;

    /**
     * @var integer размер файла в байтах
     */
    public $size;

    /**
     * @var integer - код ошибки ззагрузки файла
     */
    public $error;

    
    private static $_files;

    /**
     * String output.
     * This is PHP magic method that returns string representation of an object.
     * The implementation here returns the uploaded file's name.
     * @return string the string representation of the object
     */
    public function __toString() {
        return $this->name;
    }

    /**
     * Returns an uploaded file for the given model attribute.
     * The file should be uploaded using [[\yii\widgets\ActiveField::fileInput()]].
     * @param \yii\base\Model $model the data model
     * @param string $attribute the attribute name. The attribute name may contain array indexes.
     * For example, '[1]file' for tabular file uploading; and 'file[1]' for an element in a file array.
     * @return UploadedFile the instance of the uploaded file.
     * Null is returned if no file is uploaded for the specified model attribute.
     * @see getInstanceByName()
     */
    public static function getInstance($model, $attribute) {
        $name = Html::getInputName($model, $attribute);
        $value = Html::getAttributeValue($model, $attribute);
        static::loadFile($name, $value);
        return static::getInstanceByName($name);
    }


    public static function getInstancePath($attr, $url){
        static::loadFile($attr, $url);
        return static::getInstanceByName($attr);
    } 
    /**
     * Returns an uploaded file according to the given file input name.
     * The name can be a plain string or a string like an array element (e.g. 'Post[imageFile]', or 'Post[0][imageFile]').
     * @param string $name the name of the file input field.
     * @return UploadedFile the instance of the uploaded file.
     * Null is returned if no file is uploaded for the specified name.
     */
    public static function getInstanceByName($attr) {
        return isset(static::$_files[$attr]) ? static::$_files[$attr] : null;
    }

 

    /**
     * Cleans up the loaded UploadedFile instances.
     * This method is mainly used by test scripts to set up a fixture.
     */
    public static function reset() {
        self::$_files = null;
    }

    /**
     * Saves the uploaded file.
     * Note that this method uses php's move_uploaded_file() method. If the target file `$file`
     * already exists, it will be overwritten.
     * @param string $file the file path used to save the uploaded file
     * @param boolean $deleteTempFile whether to delete the temporary file after saving.
     * If true, you will not be able to save the uploaded file again in the current request.
     * @return boolean true whether the file is saved successfully
     * @see error
     */
    public function saveAs($file, $deleteTempFile = true) {
        $res = false;
        if ($this->error == static::UPLOAD_ERR_OK && file_exists($this->name)) {
            if ($deleteTempFile) {
                $res = copy($this->name, $file);
                if($res){
                    unlink($this->name);
                    $this->name = $file;
                }
            } else {
                $res = copy($this->name, $file);
            }
        }
       return $res;
    }

    /**
     * @return string original file base name
     */
    public function getBaseName() {
        return pathinfo($this->name, PATHINFO_FILENAME);
    }

    /**
     * @return string file extension
     */
    public function getExtension() {
        return strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
    }
    
     /**
     * @return string file name
     */
    public function getFileName()
    {
        return pathinfo($this->name, PATHINFO_BASENAME);
    }

    /**
     * @return boolean whether there is an error with the uploaded file.
     * Check [[error]] for detailed error code information.
     */
    public function getHasError() {
        return $this->error != static::UPLOAD_ERR_OK;
    }

    /**
     * Creates UploadedFile instances from $_FILE.
     * @return array the UploadedFile instances
     */
    private static function loadFiles($paths) {
        foreach($paths as $attr=>$path){
            static::loadFile($attr, $path);
        }
        return self::$_files;
    }

    /**
     * Создание экземпляра загружаемого файла
     * 
     * @param string $attr - название атрибута
     * @param string $url - URL
     */
    private static function loadFile($attr, $path) {
        $path = RC::getAlias($path);
        if(!file_exists($path)){
             return self::$_files[$attr] = new static([
                'attribute' => $attr,
                'name'=>$path,
                'error' =>  static::UPLOAD_ERR_NO_FILE,
            ]);
        }

        self::$_files[$attr] = new static([
                'attribute' => $attr,
                'size' => filesize($path),
                'name'=>$path,
                'type'=>FileHelper::getMimeType($path),
                'error'=>static::UPLOAD_ERR_OK
        ]);
        
        return self::$_files[$attr];
    }

}
