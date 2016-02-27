<?php

namespace maze\upload;

use RC;
use maze\base\Object;
use maze\helpers\Html;
use maze\helpers\FileHelper;

class UploadedUrl extends Object {
    /*
     * Значение: 0; Ошибок не возникло, файл был успешно загружен на сервер.
     */

    const UPLOAD_ERR_OK = 0;

    /*
     * Значение: 1; Ошибок неверный формат URL отсутствует хост
     */
    const UPLOAD_ERR_NO_HOST = 1;

    /*
     * Значение: 2; Ошибок ответа от сервера
     */
    const UPLOAD_ERR_NO_PAGE = 2;

    /*
     * Значение: 3; Файл не был загружен.
     */
    const UPLOAD_ERR_NO_FILE = 3;
 

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

    /**
     * @var string - url загрузки файла
     */
    public $url;
    
    
    public static $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
     /**
     * @var string -  путь куда будут загружаться файлы
     */
    public static $tempName = '@root/temp/upload';
    
    private static $_files;

    /**
     * String output.
     * This is PHP magic method that returns string representation of an object.
     * The implementation here returns the uploaded file's name.
     * @return string the string representation of the object
     */
    public function __toString() {
        return $this->name == null ? 'UploadedUrl' : $this->name;
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


    public static function getInstanceLoad($attr, $url){
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
    private static function loadFiles($urls) {
        foreach($urls as $attr=>$url){
            static::loadFile($attr, $url);
        }
        return self::$_files;
    }

    /**
     * Создание экземпляра загружаемого файла
     * 
     * @param string $attr - название атрибута
     * @param string $url - URL
     */
    private static function loadFile($attr, $url) {
        $urlObj = new \URI($url);

        $error = null;

        if (!$urlObj->getHost()) {
            $error = static::UPLOAD_ERR_NO_HOST;
        } else {
            $url = $urlObj->toString(['scheme', 'user', 'pass', 'host', 'port', 'path', 'query']);
            $curl = curl_init($url);

            curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_NOBODY, 1);
            curl_setopt($curl, CURLOPT_USERAGENT, static::$agent);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($curl, CURLOPT_FAILONERROR, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $header = curl_exec($curl);

            if (curl_errno($curl)) {
                $error = static::UPLOAD_ERR_NO_PAGE;
            }
            curl_close($curl);
        }

        if ($error !== null) {
            return self::$_files[$attr] = new static([
                'attribute' => $attr,
                'error' => $error,
            ]);
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_NOBODY, 0);
        curl_setopt($curl, CURLOPT_USERAGENT, static::$agent);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_FAILONERROR, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $content = curl_exec($curl);

        if (curl_errno($curl)) {
            self::$_files[$attr] = new static([
                'attribute' => $attr,
                'error' => static::UPLOAD_ERR_NO_FILE,
            ]);
        } else {
            $info = curl_getinfo($curl);
            
            $error = static::UPLOAD_ERR_OK;
            
            if (preg_match("#Content-Disposition:.+filename=(.+)#i", $header, $fileName)) {
                $file = trim($fileName[1]);
            } elseif(preg_match("#.+/([^.]+\.[a-z]{2,5})$#i", $url, $fileName)){
                $file = trim(urldecode($fileName[1]));
            }else{
                $ext = FileHelper::getExtensionByMimeType($info['content_type']);
                $file = md5($url).($ext ? '.'.$ext : '.txt');
            } 
           
            $path = RC::getAlias(static::$tempName). DS . $file;
           
            file_put_contents($path, $content);
           
            
            self::$_files[$attr] = new static([
                'attribute' => $attr,
                'size' => filesize($path),
                'url'=>$url,
                'name'=>$path,
                'type'=>FileHelper::getMimeType($path),
                'error'=>$error
            ]);
        }

        curl_close($curl);
        
        return self::$_files[$attr];
    }

}
