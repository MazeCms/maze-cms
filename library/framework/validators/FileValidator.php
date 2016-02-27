<?php


namespace maze\validators;

use maze;
use maze\upload\UploadedFile;
use maze\upload\UploadedUrl;
use maze\upload\UploadedPath;

/**
 * FileValidator verifies if an attribute is receiving a valid uploaded file.
 *
 */
class FileValidator extends Validator {

    /**
     * @var array|string a list of file name extensions that are allowed to be uploaded.
     * This can be either an array or a string consisting of file extension names
     * separated by space or comma (e.g. "gif, jpg").
     * Extension names are case-insensitive. Defaults to null, meaning all file name
     * extensions are allowed.
     * @see wrongType
     */
    public $types;

    /**
     * @var integer the minimum number of bytes required for the uploaded file.
     * Defaults to null, meaning no limit.
     * @see tooSmall
     */
    public $minSize;

    /**
     * @var integer the maximum number of bytes required for the uploaded file.
     * Defaults to null, meaning no limit.
     * Note, the size limit is also affected by 'upload_max_filesize' INI setting
     * and the 'MAX_FILE_SIZE' hidden field value.
     * @see tooBig
     */
    public $maxSize;

    /**
     * @var integer the maximum file count the given attribute can hold.
     * It defaults to 1, meaning single file upload. By defining a higher number,
     * multiple uploads become possible.
     * @see tooMany
     */
    public $maxFiles = 1;

    /**
     * @var string the error message used when a file is not uploaded correctly.
     */
    public $message;

    /**
     * @var string the error message used when no file is uploaded.
     */
    public $uploadRequired;

    /**
     * @var string the error message used when the uploaded file is too large.
     * You may use the following tokens in the message:
     *
     * - {attribute}: the attribute name
     * - {file}: the uploaded file name
     * - {limit}: the maximum size allowed (see [[getSizeLimit()]])
     */
    public $tooBig;

    /**
     * @var string the error message used when the uploaded file is too small.
     * You may use the following tokens in the message:
     *
     * - {attribute}: the attribute name
     * - {file}: the uploaded file name
     * - {limit}: the value of [[minSize]]
     */
    public $tooSmall;

    /**
     * @var string the error message used when the uploaded file has an extension name
     * that is not listed in [[types]]. You may use the following tokens in the message:
     *
     * - {attribute}: the attribute name
     * - {file}: the uploaded file name
     * - {extensions}: the list of the allowed extensions.
     */
    public $wrongType;

    /**
     * @var string the error message used if the count of multiple uploads exceeds limit.
     * You may use the following tokens in the message:
     *
     * - {attribute}: the attribute name
     * - {limit}: the value of [[maxFiles]]
     */
    public $tooMany;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        if ($this->message === null) {
            $this->message = 'LIB_FRAMEWORK_VALIDATOR_FILE_UPLOAD_NO';
        }
        if ($this->uploadRequired === null) {
            $this->uploadRequired = 'LIB_FRAMEWORK_VALIDATOR_FILE_UPLOAD_LOAD';
        }
        if ($this->tooMany === null) {
            $this->tooMany = 'LIB_FRAMEWORK_VALIDATOR_FILE_UPLOAD_MANY';
        }
        if ($this->wrongType === null) {
            $this->wrongType = 'LIB_FRAMEWORK_VALIDATOR_FILE_UPLOAD_EXT';
        }
        if ($this->tooBig === null) {
            $this->tooBig = 'LIB_FRAMEWORK_VALIDATOR_FILE_UPLOAD_BIG';
        }
        if ($this->tooSmall === null) {
            $this->tooSmall = 'LIB_FRAMEWORK_VALIDATOR_FILE_UPLOAD_SMALL';
        }
        if (!is_array($this->types)) {
            $this->types = preg_split('/[\s,]+/', strtolower($this->types), -1, PREG_SPLIT_NO_EMPTY);
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($object, $attribute) {
        if ($this->maxFiles > 1) {
            $files = $object->$attribute;
            if (!is_array($files)) {
                $this->addError($object, $attribute, $this->uploadRequired);

                return;
            }
            foreach ($files as $i => $file) {
                if (!$file instanceof UploadedFile || $file->error == UPLOAD_ERR_NO_FILE) {
                    unset($files[$i]);
                }
            }
            $object->$attribute = array_values($files);
            if (empty($files)) {
                $this->addError($object, $attribute, $this->uploadRequired);
            }
            if (count($files) > $this->maxFiles) {
                $this->addError($object, $attribute, $this->tooMany, ['limit' => $this->maxFiles]);
            } else {
                foreach ($files as $file) {
                    $result = $this->validateValue($file);
                    if (!empty($result)) {
                        $this->addError($object, $attribute, $result[0], $result[1]);
                    }
                }
            }
        } else {
            $result = $this->validateValue($object->$attribute);
            if (!empty($result)) {
                $this->addError($object, $attribute, $result[0], $result[1]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($file) {
        if ($file instanceof UploadedUrl) {
            switch ($file->error) {
                case UploadedUrl::UPLOAD_ERR_OK:
                    if ($this->maxSize !== null && $file->size > $this->maxSize) {
                        return [$this->tooBig, ['file' => $file->name, 'limit' => $this->getSizeLimit()]];
                    } elseif ($this->minSize !== null && $file->size < $this->minSize) {
                        return [$this->tooSmall, ['file' => $file->name, 'limit' => $this->minSize]];
                    } elseif (!empty($this->types) && !in_array(strtolower(pathinfo($file->name, PATHINFO_EXTENSION)), $this->types, true)) {
                        return [$this->wrongType, ['file' => $file->name, 'extensions' => implode(', ', $this->types)]];
                    } else {
                        return null;
                    }
                default:
                    return [$this->message, []];
                    break;
            }
        } elseif ($file instanceof UploadedPath) {
            switch ($file->error) {
                case UploadedPath::UPLOAD_ERR_OK:
                    if ($this->maxSize !== null && $file->size > $this->maxSize) {
                        return [$this->tooBig, ['file' => $file->name, 'limit' => $this->getSizeLimit()]];
                    } elseif ($this->minSize !== null && $file->size < $this->minSize) {
                        return [$this->tooSmall, ['file' => $file->name, 'limit' => $this->minSize]];
                    } elseif (!empty($this->types) && !in_array(strtolower(pathinfo($file->name, PATHINFO_EXTENSION)), $this->types, true)) {
                        return [$this->wrongType, ['file' => $file->name, 'extensions' => implode(', ', $this->types)]];
                    } else {
                        return null;
                    }
                default:
                    return [$this->message, []];
                    break;
            }
        }


        if (!$file instanceof UploadedFile || $file->error == UPLOAD_ERR_NO_FILE) {
            return [$this->uploadRequired, []];
        }
        switch ($file->error) {
            case UPLOAD_ERR_OK:
                if ($this->maxSize !== null && $file->size > $this->maxSize) {
                    return [$this->tooBig, ['file' => $file->name, 'limit' => $this->getSizeLimit()]];
                } elseif ($this->minSize !== null && $file->size < $this->minSize) {
                    return [$this->tooSmall, ['file' => $file->name, 'limit' => $this->minSize]];
                } elseif (!empty($this->types) && !in_array(strtolower(pathinfo($file->name, PATHINFO_EXTENSION)), $this->types, true)) {
                    return [$this->wrongType, ['file' => $file->name, 'extensions' => implode(', ', $this->types)]];
                } else {
                    return null;
                }
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return [$this->tooBig, ['file' => $file->name, 'limit' => $this->getSizeLimit()]];
            case UPLOAD_ERR_PARTIAL:
                \Log::_('system', 'other', 'Файл был загружен лишь частично: ' . $file->name, 404);
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                \Log::_('system', 'other', 'Отсутствует временную папку для сохранения загруженного файла: ' . $file->name, 404);
                break;
            case UPLOAD_ERR_CANT_WRITE:
                \Log::_('system', 'other', 'Не удалось записать загруженный файл на диск: ' . $file->name, 404);
                break;
            case UPLOAD_ERR_EXTENSION:
                \Log::_('system', 'other', 'PHP не предоставляет способа определить какое расширение остановило загрузку файла: ' . $file->name, 404);
                break;
            default:
                break;
        }

        return [$this->message, []];
    }

    /**
     * Returns the maximum size allowed for uploaded files.
     * This is determined based on three factors:
     *
     * - 'upload_max_filesize' in php.ini
     * - 'MAX_FILE_SIZE' hidden field
     * - [[maxSize]]
     *
     * @return integer the size limit for uploaded files.
     */
    public function getSizeLimit() {
        $limit = ini_get('upload_max_filesize');
        $limit = $this->sizeToBytes($limit);
        if ($this->maxSize !== null && $limit > 0 && $this->maxSize < $limit) {
            $limit = $this->maxSize;
        }
        if (isset($_POST['MAX_FILE_SIZE']) && $_POST['MAX_FILE_SIZE'] > 0 && $_POST['MAX_FILE_SIZE'] < $limit) {
            $limit = (int) $_POST['MAX_FILE_SIZE'];
        }

        return $limit;
    }

    /**
     * @inheritdoc
     */
    public function isEmpty($value, $trim = false) {
        $value = is_array($value) && !empty($value) ? $value[0] : $value;
       
        if($value instanceof UploadedUrl){
            return $value->name == null; 
        }elseif($value instanceof UploadedPath){
           return $value->type == null; 
        }
        return !($value instanceof UploadedFile) || $value->error == UPLOAD_ERR_NO_FILE;
    }

    /**
     * Converts php.ini style size to bytes
     *
     * @param string $sizeStr $sizeStr
     * @return int
     */
    private function sizeToBytes($sizeStr) {
        switch (substr($sizeStr, -1)) {
            case 'M':
            case 'm':
                return (int) $sizeStr * 1048576;
            case 'K':
            case 'k':
                return (int) $sizeStr * 1024;
            case 'G':
            case 'g':
                return (int) $sizeStr * 1073741824;
            default:
                return (int) $sizeStr;
        }
    }

}
